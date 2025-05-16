<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Get featured categories (all categories)
        $categories = Category::all();
        
        // Get featured products (e.g., some of the latest products)
        $featuredProducts = Product::where('is_active', true)
            ->latest()
            ->take(8)
            ->get();
        
        return view('home', compact('categories', 'featuredProducts'));
    }
}
