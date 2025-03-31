<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    /**
     * Show the landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Return the landing page view
        return view('landing.index');
    }
} 