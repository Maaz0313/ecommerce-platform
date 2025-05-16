<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        // Check if there's a Buy Now product in the session
        if ($request->session()->has('buy_now_product')) {
            $buyNowProduct = $request->session()->get('buy_now_product');

            // Get the product details
            $productId = $buyNowProduct['product_id'];
            $quantity = $buyNowProduct['quantity'];

            try {
                $product = \App\Models\Product::findOrFail($productId);

                // Check if product is in stock
                if ($product->stock >= $quantity) {
                    // Clear the current cart and add only this product
                    $cart = [];
                    $cart[$productId] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $quantity,
                        'image' => $product->image
                    ];

                    $request->session()->put('cart', $cart);

                    // Remove the Buy Now product from the session
                    $request->session()->forget('buy_now_product');

                    // Redirect to checkout
                    return redirect()->route('cart.checkout')
                        ->with('success', 'Account created successfully! You can now complete your purchase.');
                }
            } catch (\Exception $e) {
                // If there's an error, just continue with normal registration flow
                // and forget the Buy Now product
                $request->session()->forget('buy_now_product');
            }
        }

        return redirect(route('home'))->with('success', 'Account created successfully!');
    }
}
