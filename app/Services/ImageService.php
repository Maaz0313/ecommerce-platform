<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected $manager;
    
    public function __construct()
    {
        // Create a new ImageManager instance with the GD driver
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimize and save product image
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $path
     * @param string $filename
     * @param int $width
     * @param int $height
     * @return string
     */
    public function optimizeAndSaveProductImage(UploadedFile $image, string $path, string $filename, int $width = 800, int $height = 800)
    {
        try {
            // Create the directory if it doesn't exist
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Load image with Intervention
            $img = $this->manager->read($image->getRealPath());

            // Resize the image while preserving aspect ratio
            $img = $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Save the image with quality optimization
            $img->save($path . '/' . $filename, 85); // 85% quality
            
            return $filename;
        } catch (\Exception $e) {
            // Log the error but don't crash the application
            Log::error('Image optimization failed: ' . $e->getMessage());
            
            // Fallback to normal upload
            $image->move($path, $filename);
            return $filename;
        }
    }
    
    /**
     * Generate a thumbnail from a product image
     *
     * @param string $imagePath
     * @param string $thumbnailPath
     * @param int $width
     * @param int $height
     * @return string|null
     */    public function generateThumbnail(string $sourcePath, string $targetPath, string $filename, int $width = 200, int $height = 200)
    {
        try {
            // Create thumbnail directory if it doesn't exist
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
            
            // Add thumbnail prefix
            $thumbnailFilename = 'thumb_' . $filename;
            
            // Load source image
            $img = $this->manager->read($sourcePath . '/' . $filename);
            
            // Resize to thumbnail size
            $img = $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Save the thumbnail
            $img->save($targetPath . '/' . $thumbnailFilename, 80);
            
            return $thumbnailFilename;
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
