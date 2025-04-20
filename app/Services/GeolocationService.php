<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Cache;

class GeolocationService
{
    private Reader $reader;
    private array $config;

    public function __construct()
    {
        $this->config = config('geolocation');

        try {
            $this->reader = new Reader($this->config['database']['path']);
        } catch (\Exception $e) {
            report($e);
            // We'll initialize operations without the reader
            // Detection will fall back to default country
        }
    }

    /**
     * Detect country from IP address
     */
    public function detectCountry(string $ip): string
    {
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
                throw new \Exception('GeoIP2 reader not initialized');
            }

            $record = $this->reader->country($ip);
            return $record->country->isoCode;
        } catch (\Exception $e) {
            report($e);
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
                return $clientIp;
            }
        }

        return $request->ip();
    }
}
