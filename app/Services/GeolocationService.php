<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    private Reader $reader;
    private array $config;

    public function __construct()
    {
        ray('GeolocationService constructed');
        
        $this->config = config('geolocation');
        
        try {
            $this->reader = new Reader($this->config['database']['path']);
            ray('GeoIP2 reader initialized successfully');
        } catch (\Exception $e) {
            ray()->error('Failed to initialize GeoIP2 reader: ' . $e->getMessage());
            // We'll initialize operations without the reader
            // Detection will fall back to default country
        }
    }

    /**
     * Detect country from IP address
     */
    public function detectCountry(string $ip): string
    {
        ray('Detecting country for IP: ' . $ip);
        
        // Check cache first
        if ($this->config['cache']['enabled']) {
            $cacheKey = $this->config['cache']['key_prefix'] . $ip;
            
            return Cache::remember($cacheKey, $this->config['cache']['duration'], function () use ($ip) {
                return $this->performDetection($ip);
            });
        }

        return $this->performDetection($ip);
    }

    /**
     * Perform the actual country detection
     */
    private function performDetection(string $ip): string
    {
        try {
            if (!isset($this->reader)) {
                ray()->error('GeoIP2 reader not initialized');
                throw new \Exception('GeoIP2 reader not initialized');
            }

            $record = $this->reader->country($ip);
            $countryCode = $record->country->isoCode;
            ray('Country detected')->table([
                'ip' => $ip,
                'country' => $countryCode
            ]);
            return $countryCode;
        } catch (\Exception $e) {
            ray()->error('Failed to detect country')->table([
                'ip' => $ip,
                'error' => $e->getMessage()
            ]);
            return $this->config['default_country'];
        }
    }

    /**
     * Get client IP address
     */
    public function getClientIp(): string
    {
        $request = request();
        
        if ($this->config['use_forwarded_for'] && $request->hasHeader('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            $clientIp = trim($ips[0]);
            
            // Validate IP
            if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
                ray('Using X-Forwarded-For IP: ' . $clientIp);
                return $clientIp;
            }
        }
        
        $ip = $request->ip();
        ray('Using direct IP: ' . $ip);
        return $ip;
    }
} 