<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:cod,stripe',
            'notes' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Calculate subtotal amount
        $subtotalAmount = 0;
        foreach ($cart as $item) {
            $subtotalAmount += $item['price'] * $item['quantity'];
        }

        // Get coupon from session if exists
        $coupon = session()->get('coupon');
        $discountAmount = 0;
        $couponId = null;
        $couponCode = null;

        if ($coupon) {
            $discountAmount = $coupon['discount'];
            $couponId = $coupon['coupon_id'];
            $couponCode = $coupon['code'];
        }

        // Calculate total amount after discount
        $totalAmount = $subtotalAmount - $discountAmount;

        DB::beginTransaction();

        try {
            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(), // Will be null for guest checkout
                'coupon_id' => $couponId,
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
                'subtotal_amount' => $subtotalAmount,
                'customer_name' => $validatedData['customer_name'],
                'customer_email' => $validatedData['customer_email'],
                'customer_phone' => $validatedData['customer_phone'] ?? null,
                'shipping_address' => $validatedData['shipping_address'],
                'total_amount' => $totalAmount,
                'notes' => $validatedData['notes'] ?? null,
                'status' => 'order_received',
                'payment_method' => $validatedData['payment_method'],
            ]);

            // Create order items
            foreach ($cart as $id => $item) {
                $product = Product::findOrFail($id);

                // Check if product is still in stock
                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return redirect()->route('cart.checkout')
                        ->with('error', "Not enough stock for {$product->name}!");
                }

                // Reduce product stock
                $product->stock -= $item['quantity'];
                $product->save();

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            // Increment coupon usage if used
            if ($couponId) {
                $couponModel = \App\Models\Coupon::find($couponId);
                if ($couponModel) {
                    $couponModel->incrementUsedCount();
                }
            }

            DB::commit();

            // Clear the cart and coupon after successful order
            session()->forget(['cart', 'coupon']);

            // If payment method is Stripe, redirect to payment page
            if ($validatedData['payment_method'] === 'stripe') {
                $order->update([
                    'payment_status' => 'pending'
                ]);
                return redirect()->route('payments.process', $order->id);
            }

            // For COD, just show the confirmation
            return redirect()->route('orders.confirmation', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.checkout')
                ->with('error', 'There was an error processing your order. Please try again.');
        }
    }

    /**
     * Display the order confirmation page.
     */
    public function confirmation($orderId)
    {
        $order = Order::with('orderItems')->findOrFail($orderId);
        return view('orders.confirmation', compact('order'));
    }

    /**
     * Display a list of the user's orders.
     *
     * Display the user's order history.
     */
    public function history()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('orders.history', compact('orders'));
    }

    /**
     * Display the specified order details.
     */
    public function show($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);

        // Check if the user owns this order or is admin
        if (auth()->id() !== $order->user_id && !auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Admin: List all orders.
     */
    public function adminIndex()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Admin: Display the specified order details.
     */
    public function adminShow($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Admin: Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'required|in:order_received,preparing_for_shipment,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->status = $validatedData['status'];
        $order->save();

        $statusLabels = [
            'order_received' => 'Order Received',
            'preparing_for_shipment' => 'Preparing for Shipment',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        $message = "Order #{$order->id} status updated from {$statusLabels[$oldStatus]} to {$statusLabels[$order->status]}";

        // Check if the request is coming from the orders index page
        $referer = $request->headers->get('referer');
        if (strpos($referer, 'admin/orders/') !== false && strpos($referer, 'admin/orders/' . $id) === false) {
            // If coming from the orders index page, redirect back to the index
            return redirect()->route('admin.orders.index')->with('success', $message);
        }

        // Otherwise, redirect back to the order detail page
        return redirect()->back()->with('success', $message);
    }



    /**
     * Cancel an order.
     * Orders can only be cancelled if they are in 'order_received' or 'preparing_for_shipment' status.
     */
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::with('orderItems')->findOrFail($id);

        // Check if the user owns this order
        if (auth()->id() !== $order->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order can be cancelled
        $cancellableStatuses = ['order_received', 'preparing_for_shipment'];
        if (!in_array($order->status, $cancellableStatuses)) {
            return redirect()->back()->with('error', 'This order cannot be cancelled because it has already been shipped or delivered.');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update order status to cancelled
            $order->status = 'cancelled';
            $order->save();

            // Restore product stock
            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Order has been cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'There was an error cancelling your order. Please try again.');
        }
    }
}
