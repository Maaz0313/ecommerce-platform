<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        // Get product count
        $productCount = Product::count();
        
        // Get category count
        $categoryCount = Category::count();
        
        // Get order count
        $orderCount = Order::count();
        
        // Calculate total revenue
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        
        // Get pending orders count
        $pendingOrderCount = Order::where('status', 'pending')->count();
        
        // Get processing orders count
        $processingOrderCount = Order::where('status', 'processing')->count();
        
        // Get completed orders count
        $completedOrderCount = Order::where('status', 'completed')->count();
        
        // Get most popular products (by order items)
        $popularProducts = Product::withCount(['orderItems'])
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();
            
        // Get today's sales
        $todaySales = Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // Get recent orders
        $recentOrders = Order::with('orderItems.product')
            ->latest()
            ->take(5)
            ->get();
        
        // Get low stock products (less than 10 items)
        $lowStockProducts = Product::where('stock', '<', 10)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();
            
        return view('admin.dashboard', compact(
            'productCount', 
            'categoryCount', 
            'orderCount',
            'totalRevenue',
            'pendingOrderCount',
            'processingOrderCount',
            'completedOrderCount',
            'todaySales',
            'popularProducts',
            'recentOrders', 
            'lowStockProducts'
        ));
    }
}
