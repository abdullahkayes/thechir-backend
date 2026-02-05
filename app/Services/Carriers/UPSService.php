<?php

namespace App\Services\Carriers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UPSService
{
    private string $clientId;
    private string $clientSecret;
    private string $accountNumber;
    private string $baseUrl;
    private ?string $accessToken = null;
    private ?int $tokenExpiresAt = null;

    public function __construct()
    {
        $this->clientId = env('UPS_CLIENT_ID', '');
        $this->clientSecret = env('UPS_CLIENT_SECRET', '');
        $this->accountNumber = env('UPS_ACCOUNT_NUMBER', '');
        
        // Use production URL (UPS doesn't have a public sandbox)
        $this->baseUrl = 'https://onlinetools.ups.com/api';
    }

    /**
     * Get UPS shipping rates.
     * Uses UPS Rating API.
     * 
     * @param string $originZip Origin ZIP code
     * @param string $destZip Destination ZIP code
     * @param float $weightLbs Weight in pounds
     * @return array|null Rates or null if API fails
     */
    public function getRates(string $originZip, string $destZip, float $weightLbs): ?array
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            Log::debug('UPSService: UPS credentials not configured');
            return null;
        }

        // Validate inputs
        if (empty($originZip) || empty($destZip) || $weightLbs <= 0) {
            Log::warning('UPSService: Invalid input parameters', [
                'originZip' => $originZip,
                'destZip' => $destZip,
                'weightLbs' => $weightLbs
            ]);
            return null;
        }

        try {
            // Get OAuth token
            $token = $this->getAccessToken();
            if (!$token) {
                Log::error('UPSService: Failed to get access token');
                return null;
            }

            Log::info('UPSService: Sending rate request', [
                'originZip' => $originZip,
                'destZip' => $destZip,
                'weightLbs' => $weightLbs
            ]);

            // Build rate request
            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'transId' => uniqid('ups_'),
                    'transactionSrc' => 'ecommerce'
                ])
                ->post($this->baseUrl . '/rating/v1/Shop', [
                    'RateRequest' => [
                        'Request' => [
                            'SubVersion' => '1801',
                            'TransactionReference' => [
                                'CustomerContext' => 'Rate Request'
                            ]
                        ],
                        'Shipment' => [
                            'Shipper' => [
                                'Address' => [
                                    'PostalCode' => $originZip,
                                    'CountryCode' => 'US'
                                ]
                            ],
                            'ShipTo' => [
                                'Address' => [
                                    'PostalCode' => $destZip,
                                    'CountryCode' => 'US'
                                ]
                            ],
                            'ShipFrom' => [
                                'Address' => [
                                    'PostalCode' => $originZip,
                                    'CountryCode' => 'US'
                                ]
                            ],
                            'Package' => [
                                [
                                    'Packaging' => [
                                        'Code' => '02',
                                        'Description' => 'Package'
                                    ],
                                    'Dimensions' => [
                                        'UnitOfMeasurement' => [
                                            'Code' => 'IN'
                                        ],
                                        'Length' => '12',
                                        'Width' => '9',
                                        'Height' => '6'
                                    ],
                                    'PackageWeight' => [
                                        'UnitOfMeasurement' => [
                                            'Code' => 'LBS'
                                        ],
                                        'Weight' => (string) round($weightLbs, 2)
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $rates = $this->parseRateResponse($response->json());
                Log::info('UPSService: Rates received', [
                    'count' => count($rates)
                ]);
                return $rates;
            }

            Log::warning('UPSService: API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('UPSService: Exception calling UPS API', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Get OAuth access token from UPS.
     */
    private function getAccessToken(): ?string
    {
        // Check if we have a valid cached token
        if ($this->accessToken && $this->tokenExpiresAt && time() < $this->tokenExpiresAt) {
            return $this->accessToken;
        }

        try {
            $response = Http::timeout(30)
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->baseUrl . '/security/v1/oauth/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'] ?? null;
                // UPS tokens typically expire in 14400 seconds (4 hours)
                $this->tokenExpiresAt = time() + ($data['expires_in'] ?? 14000) - 60;
                return $this->accessToken;
            }

            Log::error('UPSService: Failed to get OAuth token', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('UPSService: OAuth exception', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Parse UPS rate response.
     */
    private function parseRateResponse(array $data): array
    {
        $rates = [];

        try {
            if (!isset($data['RateResponse']['RatedShipment'])) {
                return $rates;
            }

            foreach ($data['RateResponse']['RatedShipment'] as $shipment) {
                $serviceCode = $shipment['Service']['Code'] ?? 'Unknown';
                $totalCharge = $shipment['TotalCharges']['MonetaryValue'] ?? null;

                if ($totalCharge) {
                    $rates[] = [
                        'service' => $this->formatServiceName($serviceCode),
                        'rate' => (float) $totalCharge,
                        'carrier' => 'UPS'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('UPSService: Failed to parse rate response', [
                'error' => $e->getMessage()
            ]);
        }

        return $rates;
    }

    /**
     * Format UPS service code to readable name.
     */
    private function formatServiceName(string $serviceCode): string
    {
        $serviceNames = [
            '03' => 'UPS Ground',
            '12' => 'UPS 3 Day Select',
            '02' => 'UPS 2nd Day Air',
            '13' => 'UPS Next Day Air Saver',
            '01' => 'UPS Next Day Air',
            '14' => 'UPS Next Day Air Early',
            '07' => 'UPS Worldwide Express',
            '08' => 'UPS Worldwide Expedited',
            '65' => 'UPS Worldwide Saver',
            '82' => 'UPS Today Standard',
            '83' => 'UPS Today Dedicated Courier',
            '84' => 'UPS Today Intercity',
            '85' => 'UPS Today Express',
        ];

        return $serviceNames[$serviceCode] ?? 'UPS ' . $serviceCode;
    }

    /**
     * Get UPS Ground rate (most cost-effective for packages).
     * 
     * @param string $originZip
     * @param string $destZip
     * @param float $weightLbs
     * @return float|null
     */
    public function getGroundRate(string $originZip, string $destZip, float $weightLbs): ?float
    {
        Log::info('UPSService: Getting ground rate', [
            'originZip' => $originZip,
            'destZip' => $destZip,
            'weightLbs' => $weightLbs
        ]);

        $rates = $this->getRates($originZip, $destZip, $weightLbs);

        if (empty($rates)) {
            Log::warning('UPSService: No rates returned from API');
            return null;
        }

        Log::info('UPSService: Rates received', [
            'count' => count($rates),
            'rates' => $rates
        ]);

        // Find the cheapest rate (usually Ground)
        $groundRate = null;
        $minRate = PHP_FLOAT_MAX;

        foreach ($rates as $rate) {
            $service = strtolower($rate['service']);
            if (str_contains($service, 'ground')) {
                if ($rate['rate'] < $minRate) {
                    $minRate = $rate['rate'];
                    $groundRate = $rate;
                }
            }
        }

        // If no ground rate found, use the cheapest available
        if (!$groundRate && !empty($rates)) {
            usort($rates, fn($a, $b) => $a['rate'] <=> $b['rate']);
            $groundRate = $rates[0];
            Log::info('UPSService: Using cheapest available rate', [
                'service' => $groundRate['service'],
                'rate' => $groundRate['rate']
            ]);
        } else {
            Log::info('UPSService: Using ground rate', [
                'service' => $groundRate['service'],
                'rate' => $groundRate['rate']
            ]);
        }

        return $groundRate['rate'] ?? null;
    }
}
