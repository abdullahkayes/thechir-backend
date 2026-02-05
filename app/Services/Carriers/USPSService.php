<?php

namespace App\Services\Carriers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class USPSService
{
    private string $userId;
    private string $baseUrl = 'https://secure.shippingapis.com/ShippingAPI.dll';

    public function __construct()
    {
        $this->userId = env('USPS_USER_ID', '');
    }

    /**
     * Get USPS shipping rates.
     * Uses USPS RateV4 API.
     * 
     * @param string $originZip Origin ZIP code
     * @param string $destZip Destination ZIP code
     * @param float $weightLbs Weight in pounds
     * @return array|null Rates or null if API fails
     */
    public function getRates(string $originZip, string $destZip, float $weightLbs): ?array
    {
        if (empty($this->userId)) {
            Log::debug('USPSService: USPS_USER_ID not configured');
            return null;
        }

        try {
            // USPS RateV4 API requires XML
            $xml = $this->buildRateRequest($originZip, $destZip, $weightLbs);
            
            $response = Http::timeout(15)
                ->get($this->baseUrl, [
                    'API' => 'RateV4',
                    'XML' => $xml
                ]);

            if ($response->successful()) {
                return $this->parseRateResponse($response->body());
            }

            Log::warning('USPSService: API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('USPSService: Exception calling USPS API', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Build USPS RateV4 XML request.
     */
    private function buildRateRequest(string $originZip, string $destZip, float $weightLbs): string
    {
        // USPS requires weight in pounds and ounces
        $pounds = floor($weightLbs);
        $ounces = round(($weightLbs - $pounds) * 16);
        
        if ($ounces >= 16) {
            $pounds++;
            $ounces = 0;
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<RateV4Request USERID="{$this->userId}">
    <Revision>2</Revision>
    <Package ID="1">
        <Service>ALL</Service>
        <ZipOrigination>{$originZip}</ZipOrigination>
        <ZipDestination>{$destZip}</ZipDestination>
        <Pounds>{$pounds}</Pounds>
        <Ounces>{$ounces}</Ounces>
        <Container></Container>
        <Size>REGULAR</Size>
        <Width></Width>
        <Length></Length>
        <Height></Height>
        <Girth></Girth>
        <Value></Value>
    </Package>
</RateV4Request>
XML;
    }

    /**
     * Parse USPS RateV4 XML response.
     */
    private function parseRateResponse(string $xml): array
    {
        $rates = [];
        
        try {
            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            
            $packages = $doc->getElementsByTagName('Package');
            foreach ($packages as $package) {
                $postages = $package->getElementsByTagName('Postage');
                foreach ($postages as $postage) {
                    $mailService = $postage->getElementsByTagName('MailService')->item(0)?->nodeValue;
                    $rate = $postage->getElementsByTagName('Rate')->item(0)?->nodeValue;
                    
                    if ($mailService && $rate) {
                        $rates[] = [
                            'service' => $this->cleanServiceName($mailService),
                            'rate' => (float) $rate,
                            'carrier' => 'USPS'
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('USPSService: Failed to parse XML response', [
                'error' => $e->getMessage()
            ]);
        }

        return $rates;
    }

    /**
     * Clean up USPS service name.
     */
    private function cleanServiceName(string $name): string
    {
        // Remove trademark symbols and extra spaces
        $name = str_replace(['™', '®'], '', $name);
        return trim($name);
    }

    /**
     * Get USPS Ground Advantage rate (most cost-effective for packages).
     * 
     * @param string $originZip
     * @param string $destZip
     * @param float $weightLbs
     * @return float|null
     */
    public function getGroundAdvantageRate(string $originZip, string $destZip, float $weightLbs): ?float
    {
        $rates = $this->getRates($originZip, $destZip, $weightLbs);
        
        if (!$rates) {
            return null;
        }

        // Find the best rate (usually Ground Advantage)
        $bestRate = null;
        $minRate = PHP_FLOAT_MAX;

        foreach ($rates as $rate) {
            // Prefer Ground Advantage, Priority Mail, or First Class
            $service = strtolower($rate['service']);
            if (str_contains($service, 'ground') || 
                str_contains($service, 'priority') ||
                str_contains($service, 'first class')) {
                if ($rate['rate'] < $minRate) {
                    $minRate = $rate['rate'];
                    $bestRate = $rate;
                }
            }
        }

        // If no preferred service found, use the cheapest
        if (!$bestRate && !empty($rates)) {
            usort($rates, fn($a, $b) => $a['rate'] <=> $b['rate']);
            $bestRate = $rates[0];
        }

        return $bestRate['rate'] ?? null;
    }
}
