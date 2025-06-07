<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Blog;

class LandingController extends Controller
{
    private PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Show the landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $countryCode = session('country_code');
        $pricing = $this->pricingService->getPricing($countryCode);
        
        // Fetch the latest 2 blogs for the landing page showcase
        $latestBlogs = Blog::latest()->take(2)->get();
        
        return view('landing.index', [
            'pricing' => $pricing,
            'formattedPrice' => $this->pricingService->formatPrice($pricing),
            'latestBlogs' => $latestBlogs, // Pass blogs to the view
        ]);
    }
} 