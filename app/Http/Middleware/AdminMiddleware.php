<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated with the admin guard
        if (!Auth::guard('admin')->check()) {
            // Store the intended URL in the session
            session()->put('admin.url.intended', $request->url());

            // Flash a message to the session
            session()->flash('auth_required', 'Please log in to access the admin area.');            // Redirect to the admin login page
            return redirect()->route('admin.login');
        }

        // Check if user is an admin
        if (!Auth::guard('admin')->user()->is_admin) {
            // Log out from admin guard
            Auth::guard('admin')->logout();

            // Flash a message to the session
            session()->flash('error', 'You do not have permission to access the admin area.');

            // Redirect to the home page
            return redirect()->route('home')->with('error', 'You do not have access to the admin area.');
        }

        return $next($request);
    }
}
