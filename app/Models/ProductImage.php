<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order'
    ];
      public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get image URL with optional thumbnail path
     *
     * @param bool $thumbnail Whether to return thumbnail URL
     * @return string
     */
    public function getImageUrl($thumbnail = false)
    {
        if ($thumbnail) {
            // Check if thumbnail exists
            $thumbPath = public_path('images/products/thumbnails/thumb_' . $this->image_path);
            if (file_exists($thumbPath)) {
                return asset('images/products/thumbnails/thumb_' . $this->image_path);
            }
        }
        
        return asset('images/products/' . $this->image_path);
    }
}
