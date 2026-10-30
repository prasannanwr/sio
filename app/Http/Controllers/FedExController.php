<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FedExController extends Controller
{


    public static function fedExAuth()
    {
        // Check if the access token is already in the cache
        $cachedToken = Cache::get('fedex_access_token');

        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $response = Http::asForm()->post(env('FEDEX_API_URL') . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => env('FEDEX_API_KEY'),
                'client_secret' => env('FEDEX_API_SECRET')
            ]);

            if ($response->successful() && $response->json('access_token')) {
                $accessToken = $response->json('access_token');

                // Cache the access token with a slightly shorter lifetime
                Cache::put('fedex_access_token', $accessToken, $response->json('expires_in') - 60); // 60 seconds less

                return $accessToken;
            } else {
                Log::error("FEDEX AUTH error", [$response->json()]);
                return false;
            }
        } catch (Exception $e) {
            // Handle any exceptions, such as connection errors or timeouts
            Log::error('FedEx authentication error: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * Estimate the cost of a shipping
     * 
     * [
     *'shipper_city' => $o->name,
     *'recipient_city' => $d->name,
     *'shipper_zip' => $o->postcode,
     *'recipient_zip' => $d->postcode,
     *'shipper_country_code' => $o->country_code,
     *'recipient_country_code' => $d->country_code,
     *'commodities' => $commodities,
     *'requestedPackageLineItems' => [
     *[
     *'weight' => [
     *    'value' => $request->weight,
     *    'units' => 'KG'
     *]
     *]
     *]

     *]
     *
     * @param array $data
     * @return array
     */
    public static function estimateCost($data = [])
    {
        //token
        $t = FedExController::fedExAuth();

        if ($t == false) {
            return response()->json(['status' => false, 'msg' => 'an error occurred while authenticating at FedEx'], 500);
        }

        $response = Http::withHeaders([
            'X-locale' => 'en_US',
            'Content-Type' => 'application/json',
            'authorization' => 'Bearer ' . $t,
        ])->post(env('FEDEX_API_URL') . '/rate/v1/rates/quotes', [
            "accountNumber" => [
                "value" => env("FEDEX_API_ACC"),
            ],
            "requestedShipment" => [
                "shipper" => [
                    "address" => [
                        "city" => $data['shipper_city'],
                        "postalCode" => $data['shipper_zip'],
                        "countryCode" => $data['shipper_country_code'],
                    ],
                ],
                "recipient" => [
                    "address" => [
                        "city" => $data['recipient_city'],
                        "postalCode" => $data['recipient_zip'],
                        "countryCode" => $data['recipient_country_code'],
                    ],
                ],
                "serviceType" => "INTERNATIONAL_PRIORITY",
                "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                "customsClearanceDetail" => [
                    "documentContent" => "DOCUMENT",
                    "dutiesPayment" => [
                        "paymentType" => "SENDER",
                    ],
                    "commodities" => $data['commodities'],
                ],
                "rateRequestType" => [
                    "ACCOUNT",
                ],
                "requestedPackageLineItems" => $data['requestedPackageLineItems'],
            ],
        ]);

        if ($response->successful()) {
            $r = ['status' => true, 'data' => json_decode($response->body())->output->rateReplyDetails[0]];
            return response()->json($r);
        } else {
            Log::error($response->json());
            return response()->json(['msg' => 'an error occurred', 'error' => $response->json()], 500);
        }
    }

    public static function validatAddress()
    {
    }

    public static function ship($data)
    {
        //token
        $t = FedExController::fedExAuth();

        if ($t == false) {
            return response()->json(['msg' => 'an error occurred while authenticating at FedEx'], 500);
        }

        $response = Http::withHeaders([
            'X-locale' => 'en_US',
            'Content-Type' => 'application/json',
            'authorization' => 'Bearer ' . $t,
        ])->post(env('FEDEX_API_URL') . '/ship/v1/shipments', [
            "accountNumber" => [
                "value" => env("FEDEX_API_ACC"),
            ],
            "requestedShipment" => [
                "labelSpecification" => [
                    "imageType" => "PDF",
                    "labelStockType" => "STOCK_4X8",
                    "labelFormatType" => "LABEL_DATA_ONLY",
                    "labelOrder" => "SHIPPING_LABEL_FIRST",
                    "printedLabelOrigin" => [
                        "address" => [
                            "city" =>  $data['shipper_city'],
                            "countryCode" => $data['shipper_country_code'],
                            "streetLines" => [
                                $data['shipper_address1'], $data['shipper_address2']
                            ],
                            "postalCode" => $data['shipper_zip']
                        ],
                        "contact" => [
                            "phoneNumber" => $data['shipper_phone'],
                            "personName" => $data['shipper_name'],
                            "emailAddress" => $data['shipper_email']
                        ]
                    ],
                    "labelRotation" => "UPSIDE_DOWN",
                    "labelPrintingOrientation" => "BOTTOM_EDGE_OF_TEXT_FIRST",
                    "returnedDispositionDetail" => false
                ],
                "shipper" => [
                    "address" => [
                        "city" =>  $data['shipper_city'],
                        "countryCode" => $data['shipper_country_code'],
                        "streetLines" => [
                            $data['shipper_address1'], $data['shipper_address2']
                        ],
                        "postalCode" => $data['shipper_zip']
                    ],
                    "contact" => [
                        "phoneNumber" => $data['shipper_phone'],
                        "personName" => $data['shipper_name'],
                        "emailAddress" => $data['shipper_email']
                    ]

                ],
                "recipients" => [
                    "address" => [
                        "city" =>  $data['recipient_city'],
                        "countryCode" => $data['recipient_country_code'],
                        "streetLines" => [
                            $data['recipient_address1'], $data['recipient_address2']
                        ],
                        "postalCode" => $data['recipient_zip']
                    ],
                    "contact" => [
                        "phoneNumber" => $data['recipient_phone'],
                        "personName" => $data['recipient_name'],
                        "emailAddress" => $data['recipient_email']
                    ]

                ],

                "shippingChargesPayment" => [
                    "paymentType" => "THIRD_PARTY",
                    "payor" => [
                        "responsibleParty" => [
                            "accountNumber" => [
                                "value" => env("FEDEX_API_ACC")
                            ]
                        ]
                    ]
                ],
                "shipDatestamp" => $data['shipDatestamp'],
                "emailNotificationDetail" => [
                    "aggregationType" => "PER_PACKAGE",
                    "emailNotificationRecipients" => [
                        [
                            "emailAddress" => $data['recipient_email'],
                            "emailNotificationRecipientType" => "RECIPIENT",
                            "name" => $data['recipient_name'],
                            "notificationFormatType" => "HTML",
                            "notificationType" => "EMAIL",
                            "locale" => "en_US",
                            "notificationEventType" => [
                                "ON_LABEL",
                                "ON_DELIVERY",
                                "ON_SHIPMENT",
                                "ON_ESTIMATED_DELIVERY",
                                "ON_DELIVERY",
                                "ON_EXCEPTION"
                            ]
                        ],
                        [
                            "emailAddress" => $data['shipper_email'],
                            "emailNotificationRecipientType" => "BROKER",
                            "name" => $data['shipper_name'],
                            "notificationFormatType" => "HTML",
                            "notificationType" => "EMAIL",
                            "locale" => "en_US",
                            "notificationEventType" => [
                                "ON_LABEL",
                                "ON_DELIVERY",
                                "ON_SHIPMENT",
                                "ON_ESTIMATED_DELIVERY",
                                "ON_DELIVERY",
                                "ON_EXCEPTION"
                            ]
                        ]
                    ],
                    "personalMessage" => "This Shipment was Processed via a SIOSHIPPING Agent."
                ],
                "serviceType" => "INTERNATIONAL_PRIORITY",
                "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                "customsClearanceDetail" => [
                    "commercialInvoice" => [
                        "commercialInvoice" => [
                            "originatorName" => $data['shipper_name'],
                            "comments" => [
                                $data['customs_note']
                            ],
                            "customerReferences" => [
                                [
                                    "customerReferenceType" => "INVOICE_NUMBER",
                                    "value" => $data['customs_inv_num']
                                ]
                            ],
                            "declarationStatement" => $data['customs_note'],
                            "termsOfSale" => $data['customs_terms_of_sale'],
                            "shipmentPurpose" => "PERSONAL",
                            "emailNotificationDetail" => [
                                "emailAddress" => $data['shipper_email'],
                                "type" => "EMAILED",
                                "recipientType" => "BROKER"
                            ]
                        ]
                    ],
                    "commodities" => $data['commodities'],
                    "freightOnValue" => "OWN_RISK",
                    "dutiesPayment" => [
                        "payor" => [
                            "responsibleParty" => [
                                "address" => [
                                    "city" =>  $data['shipper_city'],
                                    "countryCode" => $data['shipper_country_code'],
                                    "streetLines" => [
                                        $data['shipper_address1'], $data['shipper_address2']
                                    ],
                                    "postalCode" => $data['shipper_zip']
                                ],
                                "contact" => [
                                    "phoneNumber" => $data['shipper_phone'],
                                    "personName" => $data['shipper_name'],
                                    "emailAddress" => $data['shipper_email']
                                ],
                                "accountNumber" => [
                                    "value" => env("FEDEX_API_ACC")
                                ]
                            ]
                        ],
                        "paymentType" => "SENDER"
                    ],
                    "generatedDocumentLocale" => "en_US",
                ],
                "rateRequestType" => [
                    "ACCOUNT",
                ],
                "requestedPackageLineItems" => $data['requestedPackageLineItems'],
            ],
        ]);

        if ($response->successful()) {
            $r = $response->json('output.rateReplyDetails.0');
            return response()->json($r);
        } else {
            throw new Exception('Failed to create fedex shipping error is : ' . json_encode($response->json()));
            Log::error('Failed to create FedEx shipping:', [$response]);
            return response()->json(['msg' => 'an error occurred', 'error' => $response->json()], 500);
        }
    }
}
