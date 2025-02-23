<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view with the user's bands.
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all bands for the authenticated user with their members and subscription status
        $bands = $user->bands()->with('members')->get();

        // Transform the bands to include full URLs for cover images and subscription info
        $bands->map(function ($band) use ($user) {
            $band->cover_image_thumbnail_path = $band->cover_image_thumbnail_path 
                ? Storage::url($band->cover_image_thumbnail_path) 
                : null;
            
            // Add disabled status if not in trial and subscription not active
            $band->is_disabled = $user->is_subscribed && $band->subscription_status !== 'active';
            
            return $band;
        });

        return Inertia::render('Dashboard', [
            'bands' => $bands
        ]);
    }
} 