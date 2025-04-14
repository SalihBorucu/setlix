<?php

namespace App\Services;

class PricingService
{
    private const COUNTRY_PRICING = [
        'GB' => ['amount' => 10, 'currency' => 'GBP', 'symbol' => '£'],
        'US' => ['amount' => 15, 'currency' => 'USD', 'symbol' => '$'],
        'CA' => ['amount' => 15, 'currency' => 'USD', 'symbol' => '$'],
        'AU' => ['amount' => 15, 'currency' => 'USD', 'symbol' => '$'],
        'NZ' => ['amount' => 15, 'currency' => 'USD', 'symbol' => '$'],
    ];

    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 
        'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 
        'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE'
    ];

    /**
     * Get pricing details for a specific country
     */
    public function getPricing(?string $countryCode = null): array
    {
        // Default to GBP if no country code provided
        if (!$countryCode) {
            return self::COUNTRY_PRICING['GB'];
        }

        // Check for specific country pricing
        if (isset(self::COUNTRY_PRICING[$countryCode])) {
            return self::COUNTRY_PRICING[$countryCode];
        }

        // Check for EU pricing
        if (in_array($countryCode, self::EU_COUNTRIES)) {
            return ['amount' => 12, 'currency' => 'EUR', 'symbol' => '€'];
        }

        // Default to USD for rest of world
        return ['amount' => 15, 'currency' => 'USD', 'symbol' => '$'];
    }

    /**
     * Format price with currency symbol
     */
    public function formatPrice(array $pricing): string
    {
        return $pricing['symbol'] . $pricing['amount'];
    }
} 