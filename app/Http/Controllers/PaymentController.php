<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Process a payment with Stripe
     */
    public function processPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Check if the user owns this order
        if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order has already been paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'This order has already been paid.');
        }

        // Check if the order has been cancelled
        if ($order->status === 'cancelled') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'This order has been cancelled and cannot be paid for.');
        }

        try {
            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create a payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $order->total_amount * 100, // Amount in cents
                'currency' => config('services.stripe.currency', 'PKR'),
                'description' => 'Order #' . $order->id,
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_email' => $order->customer_email,
                ],
            ]);

            // Update the order with the payment intent ID
            $order->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
                'payment_method' => 'stripe',
            ]);

            // Create a payment transaction record
            PaymentTransaction::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'payment_method' => 'stripe',
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $order->total_amount,
                'currency' => config('services.stripe.currency', 'PKR'),
                'status' => 'pending',
            ]);

            // Return the client secret to the frontend
            return view('payments.stripe', [
                'clientSecret' => $paymentIntent->client_secret,
                'order' => $order,
                'stripeKey' => config('services.stripe.key'),
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe payment error: ' . $e->getMessage());
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'There was an error processing your payment. Please try again.');
        }
    }

    /**
     * Handle the Stripe payment callback
     */
    public function handlePaymentCallback(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $paymentIntentId = $request->input('payment_intent');

        // Check if the order has been cancelled
        if ($order->status === 'cancelled') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'This order has been cancelled and cannot be paid for.');
        }

        try {
            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Retrieve the payment intent
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            // Update the order and payment transaction based on the payment intent status
            if ($paymentIntent->status === 'succeeded') {
                // Update order status
                $order->update([
                    'payment_status' => 'paid',
                ]);

                // Update payment transaction
                $transaction = PaymentTransaction::where('payment_intent_id', $paymentIntentId)->first();
                if ($transaction) {
                    $transaction->update([
                        'status' => 'completed',
                        'transaction_id' => $paymentIntent->id,
                        'metadata' => json_encode($paymentIntent->toArray()),
                    ]);
                }

                return redirect()->route('orders.show', $order->id)
                    ->with('success', 'Payment successful! Your order has been processed.');
            } else {
                // Payment failed or is still pending
                return redirect()->route('orders.show', $order->id)
                    ->with('error', 'Payment not completed. Status: ' . $paymentIntent->status);
            }

        } catch (\Exception $e) {
            Log::error('Stripe callback error: ' . $e->getMessage());
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'There was an error processing your payment callback.');
        }
    }

    /**
     * Handle Stripe webhooks
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $this->handleSuccessfulPayment($paymentIntent);
                    break;
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $this->handleFailedPayment($paymentIntent);
                    break;
                default:
                    Log::info('Unhandled Stripe event: ' . $event->type);
            }

            return response()->json(['status' => 'success']);

        } catch (\UnexpectedValueException $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook signature error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Handle successful payment webhook
     */
    private function handleSuccessfulPayment($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if ($orderId) {
            $order = Order::find($orderId);

            if ($order) {
                // Only update if the order is not cancelled
                if ($order->status !== 'cancelled') {
                    // Update order status
                    $order->update([
                        'payment_status' => 'paid',
                    ]);

                    // Update payment transaction
                    $transaction = PaymentTransaction::where('payment_intent_id', $paymentIntent->id)->first();
                    if ($transaction) {
                        $transaction->update([
                            'status' => 'completed',
                            'transaction_id' => $paymentIntent->id,
                            'metadata' => json_encode($paymentIntent),
                        ]);
                    }

                    Log::info('Payment succeeded for order #' . $orderId);
                } else {
                    Log::info('Payment succeeded for cancelled order #' . $orderId . '. No action taken.');
                }
            }
        }
    }

    /**
     * Handle failed payment webhook
     */
    private function handleFailedPayment($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if ($orderId) {
            $order = Order::find($orderId);

            if ($order) {
                // Only update if the order is not cancelled
                if ($order->status !== 'cancelled') {
                    // Update order status
                    $order->update([
                        'payment_status' => 'failed',
                    ]);

                    // Update payment transaction
                    $transaction = PaymentTransaction::where('payment_intent_id', $paymentIntent->id)->first();
                    if ($transaction) {
                        $transaction->update([
                            'status' => 'failed',
                            'metadata' => json_encode($paymentIntent),
                        ]);
                    }

                    Log::info('Payment failed for order #' . $orderId);
                } else {
                    Log::info('Payment failed for cancelled order #' . $orderId . '. No action taken.');
                }
            }
        }
    }
}
