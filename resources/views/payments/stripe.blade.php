@extends('layouts.app')

@section('title', 'Pay with Card')

@section('styles')
<style>
    #payment-form {
        max-width: 600px;
        margin: 0 auto;
    }

    #payment-element {
        margin-bottom: 24px;
    }

    #payment-message {
        color: rgb(105, 115, 134);
        font-size: 16px;
        line-height: 20px;
        padding-top: 12px;
        text-align: center;
    }

    #payment-element {
        margin-bottom: 24px;
    }

    /* Buttons and links */
    #submit-button {
        background: #5469d4;
        color: #ffffff;
        border-radius: 4px;
        border: 0;
        padding: 12px 16px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: block;
        transition: all 0.2s ease;
        box-shadow: 0px 4px 5.5px 0px rgba(0, 0, 0, 0.07);
        width: 100%;
    }

    #submit-button:hover {
        filter: contrast(115%);
    }

    #submit-button:disabled {
        opacity: 0.5;
        cursor: default;
    }

    /* spinner/processing state, errors */
    .spinner,
    .spinner:before,
    .spinner:after {
        border-radius: 50%;
    }

    .spinner {
        color: #ffffff;
        font-size: 22px;
        text-indent: -99999px;
        margin: 0px auto;
        position: relative;
        width: 20px;
        height: 20px;
        box-shadow: inset 0 0 0 2px;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
    }

    .spinner:before,
    .spinner:after {
        position: absolute;
        content: "";
    }

    .spinner:before {
        width: 10.4px;
        height: 20.4px;
        background: #5469d4;
        border-radius: 20.4px 0 0 20.4px;
        top: -0.2px;
        left: -0.2px;
        -webkit-transform-origin: 10.4px 10.2px;
        transform-origin: 10.4px 10.2px;
        -webkit-animation: loading 2s infinite ease 1.5s;
        animation: loading 2s infinite ease 1.5s;
    }

    .spinner:after {
        width: 10.4px;
        height: 10.2px;
        background: #5469d4;
        border-radius: 0 10.2px 10.2px 0;
        top: -0.1px;
        left: 10.2px;
        -webkit-transform-origin: 0px 10.2px;
        transform-origin: 0px 10.2px;
        -webkit-animation: loading 2s infinite ease;
        animation: loading 2s infinite ease;
    }

    @-webkit-keyframes loading {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    @keyframes loading {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    .order-summary {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .order-summary h5 {
        margin-bottom: 15px;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center">Complete Your Payment</h1>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="order-summary mb-4">
                <h5>Order Summary #{{ $order->id }}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p><strong>Total Amount:</strong> ₨{{ number_format($order->total_amount, 2) }}</p>
                        <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pay with Credit/Debit Card</h5>
                </div>
                <div class="card-body">
                    <form id="payment-form">
                        <div id="payment-element">
                            <!-- Stripe Elements will be inserted here -->
                        </div>
                        <button id="submit-button" class="btn btn-primary w-100 py-2">
                            <div class="spinner d-none" id="spinner"></div>
                            <span id="button-text">Pay Now</span>
                        </button>
                        <div id="payment-message" class="hidden mt-3"></div>
                    </form>
                </div>
            </div>

            <div class="mt-3 text-center">
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Order
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Initialize Stripe
    const stripe = Stripe('{{ $stripeKey }}');
    
    // Create payment elements
    const elements = stripe.elements({
        clientSecret: '{{ $clientSecret }}',
        appearance: {
            theme: 'stripe',
            variables: {
                colorPrimary: '#0d6efd',
            },
        },
    });

    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    // Handle form submission
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    const paymentMessage = document.getElementById('payment-message');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Disable the submit button to prevent multiple clicks
        setLoading(true);

        // Confirm the payment
        const {error} = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: '{{ route('payments.callback', $order->id) }}',
            },
        });

        if (error) {
            // Show error message
            showMessage(error.message);
        }
        
        setLoading(false);
    });

    // Show a message to the user
    function showMessage(messageText) {
        paymentMessage.textContent = messageText;
        paymentMessage.classList.remove('d-none');
        
        setTimeout(function() {
            paymentMessage.classList.add('d-none');
            paymentMessage.textContent = "";
        }, 4000);
    }

    // Show a spinner on payment submission
    function setLoading(isLoading) {
        if (isLoading) {
            submitButton.disabled = true;
            spinner.classList.remove('d-none');
            buttonText.classList.add('d-none');
        } else {
            submitButton.disabled = false;
            spinner.classList.add('d-none');
            buttonText.classList.remove('d-none');
        }
    }
</script>
@endsection
