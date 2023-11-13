<?php

namespace Webbhuset\CollectorCheckoutSDK;

use Webbhuset\CollectorCheckout\Config\Source\Customer\DefaultType;
use Webbhuset\CollectorCheckoutSDK\Adapter\AdapterInterface;
use Webbhuset\CollectorCheckoutSDK\Checkout\Cart;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\InitializeCustomer;
use Webbhuset\CollectorCheckoutSDK\Checkout\Fees;
use Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface;

class Session
{
    protected $adapter;
    protected $privateId;
    protected $publicToken;
    protected $expiresAt;
    protected $checkoutData;

    public $validCountryCodes = [
        'SE',
        'NO',
        'FI',
        'DK',
        'DE',
    ];

    public function __construct(
        AdapterInterface $adapter
    ) {
        $this->adapter = $adapter;
    }

    public function initialize(
        ConfigInterface $config,
        Fees $fees,
        Cart $cart,
        string $countryCode,
        InitializeCustomer $customer = null,
        string $reference = null
    ) {
        $fees       = $fees->toArray();
        $cart       = $cart->toArray();

        if (!in_array($countryCode, $this->validCountryCodes)) {
            $codes = implode(', ', $this->validCountryCodes);
            throw new ValidationError("Country code not valid. Must be one of {$codes}");
        }


        $data = [
            "storeId"                   => $config->getStoreId(),
            "countryCode"               => $countryCode,
            "reference"                 => $reference,
            "redirectPageUri"           => $config->getRedirectPageUri(),
            "merchantTermsUri"          => $config->getMerchantTermsUri(),
            "notificationUri"           => $config->getNotificationUri(),
            "validationUri"             => $config->getValidationUri(),
            'fees'                      => $fees,
            'cart'                      => $cart,
        ];


        if ($config->getProfileName()) {
            $data['profileName'] = $config->getProfileName();
        }

        if (empty($data['fees'])) {
            unset($data['fees']);
        }
        if ($customer) {
            $customerData = [
                'email'                         => $customer->getEmail(),
                'mobilePhoneNumber'             => $customer->getMobilePhoneNumber(),
                'nationalIdentificationNumber'  => $customer->getNationalIdentificationNumber(),
                'deliveryAddress'               => $customer->getDeliveryAddress(),
            ];
            if ((int)$customer->getCustomerType() === DefaultType::PRIVATE_CUSTOMERS) {
                $data['privateCustomerPrefill'] = $customerData;
            }
            if ((int)$customer->getCustomerType() === DefaultType::BUSINESS_CUSTOMERS) {
                $data['businessCustomerPrefill'] = $customerData;
            }
        }

        $response = $this->adapter->initializeCheckout($data);

        if (isset($response['data']['privateId'])) {
            $this->privateId = $response['data']['privateId'];
        }

        if (isset($response['data']['publicToken'])) {
            $this->publicToken = $response['data']['publicToken'];
        }

        if (isset($response['data']['expiresAt'])) {
            $this->expiresAt = $response['data']['expiresAt'];
        }

        return $this;
    }

    public function updateCart(Cart $cart)
    {
        $cart       = $cart->toArray();
        $response   = $this->adapter->updateCart($cart, $this->getPrivateId());

        return $this;
    }

    public function updateFees(Fees $fees)
    {
        $fees       = $fees->toArray();
        $response   = $this->adapter->updateFees($fees, $this->getPrivateId());

        return $this;
    }

    public function setOrderReference(string $reference)
    {
        $response = $this->adapter->setOrderReference($reference, $this->getPrivateId());

        return $this;
    }

    public function setPrivateId(string $privateId) : Session
    {
        $this->privateId = $privateId;

        return $this;
    }

    public function load(string $privateId) : Session
    {
        $response = $this->adapter->acquireInformation($privateId);

        $this->privateId = $privateId;
        $this->checkoutData = new CheckoutData($response);

        return $this;
    }

    public function getCheckoutData() : CheckoutData
    {
        return $this->checkoutData;
    }

    public function getPublicToken()
    {
        return $this->publicToken;
    }

    public function getPrivateId()
    {
        return $this->privateId;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
