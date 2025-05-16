<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if the user is an admin attempting to login through the public form
        $user = User::where('email', $request->email)->first();
        if ($user && $user->is_admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Administrators should use the admin login page')
                ->withInput(['email' => $request->email]);
        }

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

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
                        return redirect()->route('cart.checkout');
                    }
                } catch (\Exception $e) {
                    // If there's an error, just continue with normal login flow
                    // and forget the Buy Now product
                    $request->session()->forget('buy_now_product');
                }
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
