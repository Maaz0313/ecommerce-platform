<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'price', 
        'stock', 
        'image', 
        'is_active', 
        'category_id'
    ];
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
    
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }
    
    /**
     * Get image URL with optional thumbnail path
     *
     * @param bool $thumbnail Whether to return thumbnail URL
     * @return string
     */
    public function getImageUrl($thumbnail = false)
    {
        if (!$this->image) {
            return null;
        }
        
        if ($thumbnail) {
            // Check if thumbnail exists
            $thumbPath = public_path('images/products/thumbnails/thumb_' . $this->image);
            if (file_exists($thumbPath)) {
                return asset('images/products/thumbnails/thumb_' . $this->image);
            }
        }
        
        return asset('images/products/' . $this->image);
    }
    
    /**
     * Get the order items for the product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
