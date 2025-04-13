<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\GeolocationService;

class DetectGeolocation
{
    private GeolocationService $geolocationService;

    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $clientIp = $this->geolocationService->getClientIp();

        // For authenticated users, update location if needed
        if ($user) {
            // Check if we need to update the location
            $shouldUpdate = !$user->country_code || 
                          !$user->location_detected_at || 
                          $user->detected_ip !== $clientIp ||
                          $user->location_detected_at?->diffInDays(now()) >= 7;

            if ($shouldUpdate) {
                $countryCode = $this->geolocationService->detectCountry($clientIp);

                $user->update([
                    'country_code' => $countryCode,
                    'location_detected_at' => now(),
                    'detected_ip' => $clientIp,
                ]);
            }
        }
        
        // Store country in session for all users (authenticated or not)
        if (!session()->has('country_code')) {
            $countryCode = $user?->country_code ?? 
                          $this->geolocationService->detectCountry($clientIp);
            
            session(['country_code' => $countryCode]);
        }

        return $next($request);
    }
}
