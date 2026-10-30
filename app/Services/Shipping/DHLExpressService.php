<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException;

class DHLExpressService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $account;

    public function __construct()
    {
        // $this->baseUrl  = config('dhl.base_url');
        // $this->apiKey   = config('dhl.api_key');
        // $this->apiSecret= config('dhl.api_secret');
        // $this->account  = config('dhl.account_number');

        $this->baseUrl  = 'https://express.api.dhl.com/mydhlapi/test';
        $this->apiKey   = 'apE3tJ5vK9nT2w';
        $this->apiSecret= 'K^0mF!8nV$5fL@3j';
        $this->account  = '974709860';
    }

    /**
     * Get DHL Shipping Rates
     */
    public function getRates(array $payload): array
    {
        $query = [
            'accountNumber'           => $this->account,
            'originCountryCode'       => $payload['customerDetails']['shipperDetails']['countryCode'],
            'originCityName'          => $payload['customerDetails']['shipperDetails']['cityName'],
            'destinationCountryCode'  => $payload['customerDetails']['receiverDetails']['countryCode'],
            'destinationCityName'     => $payload['customerDetails']['receiverDetails']['cityName'],
            'weight'                  => $payload['packages']['total']['total_weight'],
            'length'                  => $payload['packages']['total']['lengthTotal'],
            'width'                   => $payload['packages']['total']['widthTotal'],
            'height'                  => $payload['packages']['total']['heightTotal'],
            'plannedShippingDate'     => $payload['packages']['total']['pickupDate'],
            'isCustomsDeclarable'     => 'true',
            'unitOfMeasurement'       => 'metric',
        ];
        
//        print_r($query);exit;

        return $this->request('GET', '/rates', [], $query);
    }

    /**
     * Create Shipment and Generate Label
     */
    public function createShipment(array $payload): array
    {
         $body = [
            'plannedShippingDateAndTime' => $payload['plannedShippingDateAndTime'],
            'pickup' => [
                'isRequested' => false,
                'closeTime' => '18:00',
                'location' => 'reception'
            ],
            'productCode'       => $payload['productCode'] ?? 'D',
            'localProductCode'  => $payload['productCode'] ?? 'D',
            'getRateEstimates'  => $payload['getRateEstimates'] ?? false,
            'accounts' => [
                [
                    'typeCode' => 'shipper',
                    'number'   => $this->account,
                ]
            ],
            'valueAddedServices'   => $payload['valueAddedServices'] ?? [],
            'customerReferences'  => $payload['customerReferences'] ?? [],
            'customerDetails' => $payload['customerDetails'],
            'content' => $payload['content'],
            'requestOndemandDeliveryURL' => false,
            'getTransliteratedResponse' => false,
        ];

        $body = array_filter_recursive($body);
//echo "<pre>";print_r($body);exit;
        $response = $this->request('POST', '/shipments', $body);
//echo "<pre>";print_r($response);exit;
        return $response;
    }

    /**
     * Centralized HTTP Request
     */
    protected function request(string $method, string $endpoint, array $payload = [], array $query = []): array
    {
        try {
            $response = $http = Http::timeout(config('dhl.timeout', 30))
            ->withBasicAuth($this->apiKey, $this->apiSecret)
            ->withHeaders([
                'Message-Reference'                => uniqid(),
                'Message-Reference-Date'           => gmdate('Y-m-d\TH:i:s\Z'),
                'Plugin-Name'                      => 'Laravel-DHL',
                'Plugin-Version'                   => '1.0.0',
                'Shipping-System-Platform-Name'    => 'Laravel',
                'Shipping-System-Platform-Version' => app()->version(),
                'Webstore-Platform-Name'           => 'Sio',
                'Webstore-Platform-Version'        => '1.0',
            ])
            ->acceptJson()
            ->asJson();

            $response = $http->send($method, $this->baseUrl . $endpoint, [
                'query' => $query,     // for GET /rates 
                'json'  => $payload,   // for POST /shipments
            ])->throw();
//print_r($response->json());exit;
            return $response->json();

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // print_r($e->response->json()); exit; // For debugging purposes
            throw new \Exception(
                'DHL API Error: ' . optional($e->response)->body()
            );
            report($e);
        }
    }
}
