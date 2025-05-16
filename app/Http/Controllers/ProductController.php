<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    protected $imageService;
    
    /**
     * Create a new controller instance.
     *
     * @param ImageService $imageService
     * @return void
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        // Check if we're accessing from admin or public
        $isAdmin = $request->route()->getName() === 'admin.products.index';

        if ($isAdmin) {
            // For admin, show all products including inactive ones
            $query = Product::with('category');
            
            // Handle search in admin
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }
            
            $products = $query->latest()->paginate(15);
            return view('admin.products.index', compact('products'));
        } else {
            // For public, only show active products
            // Handle search
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }

            // Handle category filter (multiple)
            if ($request->has('categories') && is_array($request->categories) && count($request->categories) > 0) {
                $query->whereHas('category', function($q) use ($request) {
                    $q->whereIn('slug', $request->categories);
                });
            }

            $products = $query->paginate(12);
            return view('products.index', compact('products'));
        }
    }

    /**
     * Display the specified product.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with(['category', 'images'])->firstOrFail();
        
        // Get related products (from same category, excluding current product)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validatedData['slug'] = Str::slug($request->name);
        $validatedData['is_active'] = $request->has('is_active');

        // Create the product first
        $product = Product::create($validatedData);

        // Handle primary image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . Str::slug($product->name) . '.' . $image->getClientOriginalExtension();
            
            // Use image service to optimize and save the image
            $this->imageService->optimizeAndSaveProductImage(
                $image, 
                public_path('images/products'), 
                $imageName
            );
            
            // Generate thumbnail for admin panel
            $this->imageService->generateThumbnail(
                public_path('images/products'),
                public_path('images/products/thumbnails'),
                $imageName
            );
            
            // Create primary image record
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imageName,
                'is_primary' => true,
                'sort_order' => 0
            ]);
            
            // Keep backward compatibility
            $product->image = $imageName;
            $product->save();
        }

        // Handle additional images
        if ($request->hasFile('additional_images')) {
            $sortOrder = 1;
            foreach ($request->file('additional_images') as $image) {
                $imageName = time() . '-' . $sortOrder . '-' . Str::slug($product->name) . '.' . $image->getClientOriginalExtension();
                
                // Use image service to optimize and save the image
                $this->imageService->optimizeAndSaveProductImage(
                    $image, 
                    public_path('images/products'), 
                    $imageName
                );
                
                // Generate thumbnail for admin panel
                $this->imageService->generateThumbnail(
                    public_path('images/products'),
                    public_path('images/products/thumbnails'),
                    $imageName
                );
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imageName,
                    'is_primary' => false,
                    'sort_order' => $sortOrder
                ]);
                
                $sortOrder++;
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validatedData['slug'] = Str::slug($request->name);
        $validatedData['is_active'] = $request->has('is_active');

        // Handle primary image
        if ($request->hasFile('image')) {
            // Delete the old primary image if it exists
            if ($product->image) {
                $oldImagePath = public_path('images/products/') . $product->image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Find and update or create primary image
            $image = $request->file('image');
            $imageName = time() . '-' . Str::slug($product->name) . '.' . $image->getClientOriginalExtension();
            
            // Use image service to optimize and save the image
            $this->imageService->optimizeAndSaveProductImage(
                $image, 
                public_path('images/products'), 
                $imageName
            );
            
            // Generate thumbnail
            $this->imageService->generateThumbnail(
                public_path('images/products'),
                public_path('images/products/thumbnails'),
                $imageName
            );
            
            $primaryImage = $product->primaryImage;
            
            if ($primaryImage) {
                // Replace existing primary image
                $primaryImage->image_path = $imageName;
                $primaryImage->save();
            } else {
                // Create new primary image
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imageName,
                    'is_primary' => true,
                    'sort_order' => 0
                ]);
            }
            
            // Keep backward compatibility
            $validatedData['image'] = $imageName;
        }
        
        // Handle additional images
        if ($request->hasFile('additional_images')) {
            $existingImageCount = $product->images()->where('is_primary', false)->count();
            $sortOrder = $existingImageCount + 1;
            
            foreach ($request->file('additional_images') as $image) {
                $imageName = time() . '-' . $sortOrder . '-' . Str::slug($product->name) . '.' . $image->getClientOriginalExtension();
                
                // Use image service to optimize and save the image
                $this->imageService->optimizeAndSaveProductImage(
                    $image, 
                    public_path('images/products'), 
                    $imageName
                );
                
                // Generate thumbnail
                $this->imageService->generateThumbnail(
                    public_path('images/products'),
                    public_path('images/products/thumbnails'),
                    $imageName
                );
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imageName,
                    'is_primary' => false,
                    'sort_order' => $sortOrder
                ]);
                
                $sortOrder++;
            }
        }

        $product->update($validatedData);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete all product images
        $productImages = $product->images;
        foreach ($productImages as $productImage) {
            $imagePath = public_path('images/products/') . $productImage->image_path;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $productImage->delete();
        }
        
        // Also delete the main image for backward compatibility
        if ($product->image) {
            $imagePath = public_path('images/products/') . $product->image;
            if (file_exists($imagePath) && !in_array($product->image, $productImages->pluck('image_path')->toArray())) {
                unlink($imagePath);
            }
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    /**
     * Remove a product image.
     */
    public function removeImage($id)
    {
        $productImage = ProductImage::findOrFail($id);
        
        // Check if image exists and delete it
        $imagePath = public_path('images/products/') . $productImage->image_path;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        // Also delete thumbnail if it exists
        $thumbPath = public_path('images/products/thumbnails/thumb_') . $productImage->image_path;
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }
        
        // Delete the image record
        $productImage->delete();
        
        return redirect()->back()->with('success', 'Product image removed successfully!');
    }

    /**
     * Reorder product images.
     */
    public function reorderImages(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $imageIds = $request->input('image_ids');
        
        if (!is_array($imageIds)) {
            return response()->json(['error' => 'Invalid image order data'], 400);
        }
        
        try {
            // Update the sort order for each image
            foreach ($imageIds as $index => $imageId) {
                ProductImage::where('id', $imageId)
                    ->where('product_id', $product->id)
                    ->update(['sort_order' => $index]);
            }
            
            // Log the reordering operation for debugging
            \Log::info('Product images reordered', [
                'product_id' => $product->id,
                'image_ids' => $imageIds
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Image order updated successfully',
                'data' => [
                    'product_id' => $product->id,
                    'images_count' => count($imageIds)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error reordering product images', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to update image order: ' . $e->getMessage()
            ], 500);
        }
    }
}
