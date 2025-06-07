<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     */
    public function index()
    {
        // Get all blogs, newest first
        $blogs = Blog::orderBy('created_at', 'desc')->get();
        return view('blogs.index', compact('blogs'));
    }

    /**
     * Display a single blog post by slug.
     */
    public function show($slug)
    {
        // Find blog by slug or fail with 404
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return view('blogs.show', compact('blog'));
    }
}
