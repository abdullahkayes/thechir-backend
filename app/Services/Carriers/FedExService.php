<?php

namespace App\Services\Carriers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FedExService
{
    private string $key;
    private string $secret;
    private string $accountNumber;
    private string $baseUrl;
    private ?string $accessToken = null;
    private ?int $tokenExpiresAt = null;

    public function __construct()
    {
        $this->key = env('FEDEX_KEY', '');
        $this->secret = env('FEDEX_SECRET', '');
        $this->accountNumber = env('FEDEX_ACCOUNT_NUMBER', '');
        
        // Use test URL for development, live for production
        $this->baseUrl = env('APP_ENV') === 'production'
            ? 'https://apis.fedex.com'
            : 'https://apis-sandbox.fedex.com';
    }

    /**
     * Get FedEx shipping rates.
     * 
     * @param string $originZip Origin ZIP code
     * @param string $destZip Destination ZIP code
     * @param float $weightLbs Weight in pounds
     * @return array|null Rates or null if API fails
     */
    public function getRates(string $originZip, string $destZip, float $weightLbs): ?array
    {
        if (empty($this->key) || empty($this->secret)) {
            Log::debug('FedExService: FedEx credentials not configured');
            return null;
        }

        // Validate inputs
        if (empty($originZip) || empty($destZip) || $weightLbs <= 0) {
            Log::warning('FedExService: Invalid input parameters', [
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
                Log::error('FedExService: Failed to get access token');
                return null;
            }

            // Build rate request with proper package details
            // Add unique transaction ID to prevent caching
            $transactionId = uniqid('fedex_', true);
            
            $requestData = [
                'accountNumber' => [
                    'value' => $this->accountNumber
                ],
                'requestedShipment' => [
                    'shipper' => [
                        'address' => [
                            'postalCode' => $originZip,
                            'countryCode' => 'US'
                        ]
                    ],
                    'recipient' => [
                        'address' => [
                            'postalCode' => $destZip,
                            'countryCode' => 'US'
                        ]
                    ],
                    'pickupType' => 'DROPOFF_AT_FEDEX_LOCATION',
                    'serviceType' => 'FEDEX_GROUND',
                    'packagingType' => 'YOUR_PACKAGING',
                    'rateRequestType' => ['ACCOUNT', 'LIST'],
                    'requestedPackageLineItems' => [
                        [
                            'weight' => [
                                'value' => round($weightLbs, 2),
                                'units' => 'LB'
                            ]
                            // Note: Not sending dimensions to force FedEx to use actual weight
                            // instead of DIM weight
                        ]
                    ]
                ],
                'transactionId' => $transactionId,
                'timestamp' => date('c')
            ];

            Log::info('FedExService: Sending rate request', [
                'originZip' => $originZip,
                'destZip' => $destZip,
                'weightLbs' => $weightLbs,
                'roundedWeight' => round($weightLbs, 2),
                'baseUrl' => $this->baseUrl,
                'accountNumber' => substr($this->accountNumber, -4) . '****' // Masked
            ]);

            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'X-locale' => 'en_US'
                ])
                ->post($this->baseUrl . '/rate/v1/rates/quotes', $requestData);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('FedExService: Rate request successful', [
                    'status' => $response->status(),
                    'hasErrors' => isset($responseData['errors']),
                    'hasRateReply' => isset($responseData['output']['rateReplyDetails']),
                    'rateReplyCount' => count($responseData['output']['rateReplyDetails'] ?? [])
                ]);
                
                // Log the raw response for debugging
                Log::debug('FedExService: Raw rate response', [
                    'response' => json_encode($responseData)
                ]);
                
                return $this->parseRateResponse($responseData);
            }

            Log::warning('FedExService: API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'originZip' => $originZip,
                'destZip' => $destZip
            ]);
        } catch (\Exception $e) {
            Log::error('FedExService: Exception calling FedEx API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return null;
    }

    /**
     * Get OAuth access token from FedEx.
     */
    private function getAccessToken(): ?string
    {
        // Check if we have a valid cached token
        if ($this->accessToken && $this->tokenExpiresAt && time() < $this->tokenExpiresAt) {
            return $this->accessToken;
        }

        try {
            $response = Http::timeout(30)
                ->asForm()
                ->post($this->baseUrl . '/oauth/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->key,
                    'client_secret' => $this->secret
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                // Cache token for 3500 seconds (FedEx tokens expire in 3600s)
                $this->tokenExpiresAt = time() + 3500;
                return $this->accessToken;
            }

            Log::error('FedExService: Failed to get OAuth token', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('FedExService: OAuth exception', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Parse FedEx rate response.
     */
    private function parseRateResponse(array $data): array
    {
        $rates = [];

        try {
            // Check for errors in response
            if (isset($data['errors'])) {
                Log::warning('FedExService: API returned errors', [
                    'errors' => $data['errors']
                ]);
                return $rates;
            }

            if (!isset($data['output']['rateReplyDetails'])) {
                Log::warning('FedExService: No rateReplyDetails in response', [
                    'response_keys' => array_keys($data),
                    'output_keys' => array_keys($data['output'] ?? [])
                ]);
                return $rates;
            }

            Log::info('FedExService: Parsing rate reply details', [
                'count' => count($data['output']['rateReplyDetails'])
            ]);

            foreach ($data['output']['rateReplyDetails'] as $index => $detail) {
                $serviceType = $detail['serviceType'] ?? 'Unknown';
                
                Log::debug('FedExService: Processing rate detail', [
                    'index' => $index,
                    'serviceType' => $serviceType,
                    'hasRatedShipmentDetails' => isset($detail['ratedShipmentDetails'])
                ]);

                if (!isset($detail['ratedShipmentDetails'])) {
                    continue;
                }

                foreach ($detail['ratedShipmentDetails'] as $shipmentIndex => $shipmentDetail) {
                    // Try multiple possible charge fields
                    $totalCharge = $shipmentDetail['totalNetCharge'] ??
                                   $shipmentDetail['totalNetFedExCharge'] ??
                                   ($shipmentDetail['shipmentRateDetail']['totalNetCharge'] ?? null);

                    Log::debug('FedExService: Checking charge fields', [
                        'serviceType' => $serviceType,
                        'shipmentIndex' => $shipmentIndex,
                        'totalNetCharge' => $shipmentDetail['totalNetCharge'] ?? 'not set',
                        'totalNetFedExCharge' => $shipmentDetail['totalNetFedExCharge'] ?? 'not set',
                        'shipmentRateDetail_totalNetCharge' => $shipmentDetail['shipmentRateDetail']['totalNetCharge'] ?? 'not set',
                        'finalTotalCharge' => $totalCharge
                    ]);

                    if ($totalCharge) {
                        $rates[] = [
                            'service' => $this->formatServiceName($serviceType),
                            'rate' => (float) $totalCharge,
                            'carrier' => 'FedEx'
                        ];
                        
                        Log::info('FedExService: Rate extracted', [
                            'service' => $this->formatServiceName($serviceType),
                            'rate' => (float) $totalCharge
                        ]);
                    }
                }
            }

            Log::info('FedExService: Parsed rates summary', [
                'count' => count($rates),
                'rates' => $rates
            ]);
        } catch (\Exception $e) {
            Log::error('FedExService: Failed to parse rate response', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $rates;
    }

    /**
     * Format FedEx service name for display.
     */
    private function formatServiceName(string $serviceType): string
    {
        $serviceNames = [
            'FEDEX_GROUND' => 'FedEx Ground',
            'FEDEX_HOME_DELIVERY' => 'FedEx Home Delivery',
            'FEDEX_EXPRESS_SAVER' => 'FedEx Express Saver',
            'FEDEX_2_DAY' => 'FedEx 2Day',
            'STANDARD_OVERNIGHT' => 'FedEx Standard Overnight',
            'PRIORITY_OVERNIGHT' => 'FedEx Priority Overnight',
            'INTERNATIONAL_ECONOMY' => 'FedEx International Economy',
            'INTERNATIONAL_PRIORITY' => 'FedEx International Priority',
        ];

        return $serviceNames[$serviceType] ?? ucwords(strtolower(str_replace('_', ' ', $serviceType)));
    }

    /**
     * Get FedEx Ground rate (most cost-effective for packages).
     * 
     * @param string $originZip
     * @param string $destZip
     * @param float $weightLbs
     * @return float|null
     */
    public function getGroundRate(string $originZip, string $destZip, float $weightLbs): ?float
    {
        Log::info('FedExService: Getting ground rate', [
            'originZip' => $originZip,
            'destZip' => $destZip,
            'weightLbs' => $weightLbs
        ]);

        $rates = $this->getRates($originZip, $destZip, $weightLbs);

        if (empty($rates)) {
            Log::warning('FedExService: No rates returned from API');
            return null;
        }

        Log::info('FedExService: Rates received', [
            'count' => count($rates),
            'rates' => $rates
        ]);

        // Find the cheapest rate (usually Ground or Home Delivery)
        $groundRate = null;
        $minRate = PHP_FLOAT_MAX;

        foreach ($rates as $rate) {
            $service = strtolower($rate['service']);
            if (str_contains($service, 'ground') || str_contains($service, 'home delivery')) {
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
            Log::info('FedExService: Using cheapest available rate', [
                'service' => $groundRate['service'],
                'rate' => $groundRate['rate']
            ]);
        } else {
            Log::info('FedExService: Using ground rate', [
                'service' => $groundRate['service'],
                'rate' => $groundRate['rate']
            ]);
        }

        return $groundRate['rate'] ?? null;
    }
}
