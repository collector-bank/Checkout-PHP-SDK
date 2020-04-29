<?php

namespace Webbhuset\CollectorCheckoutSDK\Adapter;

use Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface;
use Webbhuset\CollectorCheckoutSDK\Adapter\Request;
use Webbhuset\CollectorCheckoutSDK\Errors\RequestError;
use Webbhuset\CollectorCheckoutSDK\Errors\ResponseError;

class MockAdapter
    implements \Webbhuset\CollectorCheckoutSDK\Adapter\AdapterInterface
{
    protected $config;

    public function __construct(
        \Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function initializeCheckout(array $data) : array
    {
        return [
            'id' => '33c5ddb4-1b5d-47c9-aeee-9ffca0f43a65',
            'data' => [
                'privateId' => '59e156e1-84df-45a4-a0fc-51f07db460e1',
                'publicToken' => 'public-SE-001731cd8b90ed7487dc7495779f9f4fa0a3c6ae4507185b',
                'expiresAt' => '2019-07-29T15=>32=>45.3696454+00=>00',
            ],
            'error' => null,
        ];
    }

    public function updateCart(array $data, string $privateId) : array
    {
        return [
            'id' => '1ccbd0c4-50ef-4508-a68b-609749ed721d',
            'data' => null,
            'error' => null,
        ];
    }

    public function updateFees(array $data, string $privateId) : array
    {
        return [
            'id' => '1ccbd0c4-50ef-4508-a68b-609749ed721d',
            'data' => null,
            'error' => null,
        ];
    }


    public function acquireInformation(string $privateId) : array
    {
        return $this->acquireCompletedPrivateCustomer();
    }

    public function setOrderReference(string $reference, string $privateId) : array
    {
        return [
            'id' => '4fca45a9-0f61-4cc4-9248-6511be14e1fb',
            'data' => null,
            'error' => null,
        ];
    }

    /**
     * Example real world initialized order
     *
     * @param string $privateId
     * @return array
     */
    protected function acquireInitedPrivateCustomer()
    {
        return [
            'id' => 'aed80239-74da-422e-90d5-569e3d4e35f5',
            'data' => [
                'customerType' => 'PrivateCustomer',
                'customer' => null,
                'businessCustomer' => null,
                'countryCode' => 'SE',
                'status' => 'Initialized',
                'paymentName' => null,
                'reference' => 'ref-00001',
                'cart' => [
                    'totalAmount' => 59,
                    'items' => [
                        'id' => 'my-sku',
                        'description' => 'Kanelbulle',
                        'unitPrice' => 59,
                        'quantity' => 1,
                        'vat' => 25,
                        'requiresElectronicId' => false,
                        'sku' => 'my-sku',
                    ],
                ],
                'fees' => [
                    'shipping' => [
                        'id' => '1',
                        'description' => 'Shipping fee',
                        'unitPrice' => 10,
                        'vat' => 25,
                    ]
                ],
                'purchase' => null,
                'order' => null,
                'hasSessionExpired' => false,
            ],
            'error' => null,
        ];
    }


    /**
     * Example business customer response from
     * http://web-checkout-documentation.azurewebsites.net/#4-acquire-information-about-a-checkout-session
     *
     * @return array
     */
    protected function acquireCompletedBusinessCustomer()
    {
        return [
            "id" => "80a0b38b-2f8b-47db-81a5-8b56baa4ad84",
            "data" => [
                "customerType" => "BusinessCustomer",
                "customer" => null,
                "businessCustomer" => [
                    "companyName" => "Bra Byggare AB",
                    "organizationNumber" => "620306-1400",
                    "invoiceReference" => "Reparation dusch Collector",
                    "email" => "bosse@brabyggare.se",
                    "firstName" => "Bosse",
                    "lastName" => "Byggare",
                    "mobilePhoneNumber" => "+46730481277",
                    "invoiceAddress" => [
                        "companyName" => "Bra Byggare AB",
                        "coAddress" => null,
                        "address" => "Storgatan 1",
                        "address2" => null,
                        "postalCode" => "50332",
                        "city" => "Borås",
                        "country" => "Sverige"
                    ],
                    "deliveryAddress" => [
                        "companyName" => "Collector AB",
                        "coAddress" => null,
                        "address" => "Östra Hamngatan 24",
                        "address2" => "att => Bosse Byggare",
                        "postalCode" => "41109",
                        "city" => "Göteborg",
                        "country" => "Sverige"
                    ]
                ],
                "countryCode" => "SE",
                "status" => "PurchaseCompleted",
                "paymentName" => "DirectInvoice",
                "reference" => "1231",
                "cart" => null,
                "fees" => null,
                "purchase" => [
                    "amountToPay" => 377,
                    "paymentName" => "DirectInvoice",
                    "invoiceDeliveryMethod" => "Email",
                    "purchaseIdentifier" => "33074878",
                    "result" => "Preliminary"
                ],
                "order" => [
                    "totalAmount" => 377,
                    "items" => [
                        [
                            "id" => "scap001",
                            "description" => "Shower Cap",
                            "unitPrice" => 10,
                            "quantity" => 1,
                            "vat" => 25
                        ],
                        [
                            "id" => "shipping001",
                            "description" => "Shipping fee (incl. vat)",
                            "unitPrice" => 59,
                            "quantity" => 1,
                            "vat" => 25
                        ],
                        [
                            "id" => "dirfee001",
                            "description" => "Order administration fee (incl. vat)",
                            "unitPrice" => 299,
                            "quantity" => 1,
                            "vat" => 25
                        ]
                    ]
                ]
            ]
        ];
    }


    /**
     * Example private customer response from
     * http://web-checkout-documentation.azurewebsites.net/#4-acquire-information-about-a-checkout-session
     *
     * @return array
     */
    protected function acquireCompletedPrivateCustomer()
    {

        return [
            "id" => "9eec015f-4b97-44be-a711-d22f3af75069",
            "data" => [
                "customerType" => "PrivateCustomer",
                "customer" => [
                    "email" => "test@collectortest.se",
                    "mobilePhoneNumber" => "+4670707071",
                    "deliveryContactInformation" => [
                    "mobilePhoneNumber" => "+46701111111"
                    ],
                    "deliveryAddress" => [
                        "firstName" => "Lars-Erik Rudolf",
                        "lastName" => "Viberg",
                        "coAddress" => null,
                        "address "=> "Sommarstugevägen 2",
                        "address2" => null,
                        "postalCode" => "85353",
                        "city" => "Sundsvall",
                        "country" => "Sverige"
                    ],
                    "billingAddress" => [
                        "firstName" => "Lars-Erik Rudolf",
                        "lastName" => "Viberg",
                        "coAddress" => null,
                        "address" => "Lingonstigen 10",
                        "address2" => null,
                        "postalCode" => "85352",
                        "city" => "Sundsvall",
                        "country" => "Sverige"
                    ],
                ],
                "businessCustomer" => null,
                "countryCode" => "SE",
                "status" => "PurchaseCompleted",
                "paymentName" => "Account",
                "reference" => "123456789",
                "cart" => null,
                "fees" => null,
                "purchase" => [
                    "amountToPay" => 279,
                    "paymentName" => "Account",
                    "invoiceDeliveryMethod" => "Paper",
                    "purchaseIdentifier" => "640173",
                    "result" => "Preliminary"
                ],
                "order" => [
                    "totalAmount" => 200,
                    "items" => [
                        [
                            "id" => "1",
                            "description" => "Some product",
                            "unitPrice" => 200,
                            "quantity" => 1,
                            "vat" => 20
                        ],
                        [
                            "id" => "998",
                            "description" => "Shipping cost (incl. VAT)",
                            "unitPrice" => 59,
                            "quantity" => 1,
                            "vat" => 25.0
                        ]
                    ]
                ]
            ],
            "error" => null
        ];
    }
}
