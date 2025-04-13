<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Geolocation Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the geolocation service.
    |
    */

    // MaxMind GeoLite2 database settings
    'database' => [
        'path' => storage_path('app/geolite2/GeoLite2-Country.mmdb'),
    ],

    // Cache settings
    'cache' => [
        'enabled' => true,
        'duration' => 7 * 24 * 60, // 7 days in minutes
        'key_prefix' => 'geolocation_',
    ],

    // Default country if detection fails
    'default_country' => 'US',

    // Whether to use X-Forwarded-For header for IP detection
    'use_forwarded_for' => true,

    // Trusted proxies for X-Forwarded-For header
    'trusted_proxies' => [
        '127.0.0.1',
        '::1',
    ],
]; 