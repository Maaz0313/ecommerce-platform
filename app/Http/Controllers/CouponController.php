<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of available coupons.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get active coupons that haven't expired
        $coupons = Coupon::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereRaw('used_count < max_uses');
            })
            ->get();

        return view('coupons.index', compact('coupons'));
    }
    /**
     * Apply a coupon to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = $request->input('code');
        $coupon = Coupon::where('code', $code)->first();

        // Get cart items and calculate total
        $cartItems = session()->get('cart', []);
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }

        // Check if cart is empty
        if (empty($cartItems)) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty!'
                ]);
            }
            return back()->with('error', 'Your cart is empty!');
        }

        // Check if coupon exists
        if (!$coupon) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coupon code!'
                ]);
            }
            return back()->with('error', 'Invalid coupon code!');
        }

        // Check if coupon is valid
        if (!$coupon->isValid($cartTotal)) {
            $message = 'This coupon is not valid!';

            if ($coupon->min_order_amount > $cartTotal) {
                $message = 'This coupon requires a minimum order amount of ₨' . number_format($coupon->min_order_amount, 2);
            }

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            return back()->with('error', $message);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($cartTotal);
        $discountedTotal = $cartTotal - $discount;

        // Store coupon in session
        session()->put('coupon', [
            'code' => $coupon->code,
            'discount' => $discount,
            'coupon_id' => $coupon->id
        ]);

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'discount' => number_format($discount, 2),
                'discountedTotal' => number_format($discountedTotal, 2),
                'originalTotal' => number_format($cartTotal, 2)
            ]);
        }

        return back()->with('success', 'Coupon applied successfully!');
    }

    /**
     * Remove the coupon from the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        session()->forget('coupon');

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed successfully!'
            ]);
        }

        return back()->with('success', 'Coupon removed successfully!');
    }
}
