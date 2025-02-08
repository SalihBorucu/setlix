<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private ImageManager $manager;
    
    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }
    
    /**
     * Process and store a band cover image
     *
     * @param UploadedFile $file
     * @return array{
     *     cover_image_path: string,
     *     cover_image_thumbnail_path: string,
     *     cover_image_small_path: string
     * }
     */
    public function processBandCoverImage(UploadedFile $file): array
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Process original image
        $image = $this->manager->read($file);
        
        // Store original (resized to max dimensions if needed)
        $original = $image->scaleDown(2000, 2000);
        $originalPath = 'bands/originals/' . $filename;
        Storage::disk('public')->put($originalPath, $original->toJpeg());
        
        // Create and store thumbnail (16:9 ratio)
        $thumbnail = $image->scaleDown(400, 225);
        $thumbnailPath = 'bands/thumbnails/' . $filename;
        Storage::disk('public')->put($thumbnailPath, $thumbnail->toJpeg());
        
        // Create and store small version
        $small = $image->scaleDown(200, 112);
        $smallPath = 'bands/small/' . $filename;
        Storage::disk('public')->put($smallPath, $small->toJpeg());
        
        return [
            'cover_image_path' => $originalPath,
            'cover_image_thumbnail_path' => $thumbnailPath,
            'cover_image_small_path' => $smallPath,
        ];
    }
    
    /**
     * Delete band cover images
     *
     * @param string|null $originalPath
     * @param string|null $thumbnailPath
     * @param string|null $smallPath
     * @return void
     */
    public function deleteBandCoverImages(?string $originalPath, ?string $thumbnailPath, ?string $smallPath): void
    {
        $disk = Storage::disk('public');
        
        if ($originalPath) {
            $disk->delete($originalPath);
        }
        
        if ($thumbnailPath) {
            $disk->delete($thumbnailPath);
        }
        
        if ($smallPath) {
            $disk->delete($smallPath);
        }
    }
} 