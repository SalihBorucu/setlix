<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // NOTE: Place your blog images in public/blog_images/ (JPEG/PNG, max 2MB)
        // Example blog entries for development/testing
        Blog::create([
            'title' => 'How This Band Uses Setlix for Every Gig',
            'slug' => 'how-this-band-uses-setlix',
            'image_path' => 'blog_images/band-uses-setlix.jpg',
            'content' => 'Discover how our band streamlined setlist management, notes, and gig prep with Setlix. No more lost papers or last-minute chaos! Read our story and tips for getting the most out of Setlix.',
        ]);
        Blog::create([
            'title' => '5 Setlist Tips for Wedding Bands',
            'slug' => '5-setlist-tips-for-wedding-bands',
            'image_path' => 'blog_images/wedding-band-tips.png',
            'content' => 'Playing weddings? Here are 5 essential setlist tips to keep your band organized, impress clients, and handle last-minute changes with ease.',
        ]);
        // Add more blog entries as needed. Ensure images exist at the specified paths.
    }
}
