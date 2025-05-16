<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index()
    {
        $cartItems = session()->get('cart', []);
        return view('cart.index', compact('cartItems'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::findOrFail($productId);

        // Check if product is in stock
        if ($product->stock < $quantity) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available!'
                ]);
            }
            return back()->with('error', 'Not enough stock available!');
        }

        $cart = session()->get('cart', []);

        // If product already in cart, update quantity
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->image
            ];
        }

        session()->put('cart', $cart);

        // Calculate cart count for the response
        $cartCount = 0;
        foreach ($cart as $item) {
            $cartCount += $item['quantity'];
        }

        // Always return JSON if it's an AJAX request or if JSON is requested
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'cartCount' => $cartCount,
                'cartItems' => $cart
            ]);
        }

        // Only redirect for non-AJAX requests
        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    /**
     * Update product quantity in cart.
     */
    public function update(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = (int)$request->input('quantity');

        if ($quantity <= 0) {
            return $this->remove($request);
        }

        $product = Product::findOrFail($productId);

        // Check if product is in stock
        if ($product->stock < $quantity) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available!'
                ]);
            }
            return back()->with('error', 'Not enough stock available!');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);

            // Calculate cart total and count for the response
            $cartTotal = 0;
            $cartCount = 0;
            foreach ($cart as $item) {
                $cartTotal += $item['price'] * $item['quantity'];
                $cartCount += $item['quantity'];
            }

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated successfully!',
                    'cartCount' => $cartCount,
                    'cartTotal' => number_format($cartTotal, 2),
                    'itemSubtotal' => number_format($cart[$productId]['price'] * $quantity, 2)
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Cart updated!');
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart!'
            ]);
        }

        return redirect()->route('cart.index')->with('error', 'Product not found in cart!');
    }

    /**
     * Remove product from cart.
     */
    public function remove(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);

            // Calculate cart total and count for the response
            $cartTotal = 0;
            $cartCount = 0;
            foreach ($cart as $item) {
                $cartTotal += $item['price'] * $item['quantity'];
                $cartCount += $item['quantity'];
            }

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from cart!',
                    'cartCount' => $cartCount,
                    'cartTotal' => number_format($cartTotal, 2),
                    'isEmpty' => count($cart) === 0
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart!'
            ]);
        }

        return redirect()->route('cart.index')->with('error', 'Product not found in cart!');
    }

    /**
     * Clear the entire cart.
     */
    public function clear(Request $request)
    {
        session()->forget('cart');

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully!',
                'cartCount' => 0,
                'cartTotal' => '0.00',
                'isEmpty' => true
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }

    /**
     * Proceed to checkout page.
     */
    public function checkout()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            // Store intended URL in session
            session()->put('url.intended', route('cart.checkout'));
            return redirect()->route('login')->with('error', 'Please login to proceed with checkout.');
        }

        $cartItems = session()->get('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        return view('cart.checkout', compact('cartItems'));
    }

    /**
     * Buy now functionality - add product to cart and redirect to checkout.
     * This method is auth protected - user must be logged in to use it.
     */
    public function buyNow(Request $request)
    {
        // Check if user is authenticated first
        if (!auth()->check()) {
            // Store product info in session for potential use after login
            session()->put('buy_now_product', [
                'product_id' => $request->input('product_id'),
                'quantity' => $request->input('quantity', 1)
            ]);

            // Store intended URL in session for redirect after login
            session()->put('url.intended', route('cart.checkout'));

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to use Buy Now.',
                    'redirect' => route('login')
                ]);
            }

            return redirect()->route('login')->with('error', 'Please login to use Buy Now.');
        }

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::findOrFail($productId);

        // Check if product is in stock
        if ($product->stock < $quantity) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available!'
                ]);
            }
            return back()->with('error', 'Not enough stock available!');
        }

        // Clear the current cart and add only this product
        $cart = [];
        $cart[$productId] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $quantity,
            'image' => $product->image
        ];

        session()->put('cart', $cart);

        // User is authenticated, proceed to checkout
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Proceeding to checkout...',
                'redirect' => route('cart.checkout')
            ]);
        }

        return redirect()->route('cart.checkout');
    }
}
