<?php

namespace Webbhuset\CollectorCheckoutSDK;

use Webbhuset\CollectorCheckoutSDK\Errors\RequestError;
use Webbhuset\CollectorCheckoutSDK\Errors\ResponseError;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\PrivateCustomer;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\PrivateAddress;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\BusinessCustomer;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\BusinessAddress;
use Webbhuset\CollectorCheckoutSDK\CheckoutData;
use Webbhuset\CollectorCheckoutSDK\Session;
use Webbhuset\CollectorCheckoutSDK\Config\Config;
use Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface;

class Test
{
    protected $useRealRequests;

    public function __construct(
        bool $useRealRequests = false,
        ConfigInterface $config = null
    ) {
        $this->useRealRequests = $useRealRequests;
        $this->config = $config;
    }
    public function getMockConfig() : Config
    {
        $config = new Config;
        $config->setUsername('my-username')
            ->setSharedAccessKey('my-shared-access-key')
            ->setCountryCode('SE')
            ->setStoreId('1')
            ->setRedirectPageUri('https://example.com')
            ->setMerchantTermsUri('https://example.com')
            ->setNotificationUri('https://example.com')
            ->setValidationUri('https://example.com');

        return $config;
    }

    public function getAdapter()
    {
        $config = $this->getConfig();
        if ($this->useRealRequests) {
            return new \Webbhuset\CollectorCheckoutSDK\Adapter\CurlAdapter($config);
        }

        return new \Webbhuset\CollectorCheckoutSDK\Adapter\MockAdapter($config);
    }

    public function getConfig()
    {
        if ($this->config) {
            return $this->config;
        }

        return $this->getMockConfig();
    }

    public function mockThrowRequestError()
    {
        $requestData = [
            'items' => []
        ];

        $request = json_encode($requestData, true);

        $responseData = [
            'id' => 'f5f4e86d-95b1-4c1a-8ad0-1d5907236bd7',
            'data' => null,
            'error' => [
                'code' => 423,
                'message' => "The resource requested is currently locked for modification. Try again.",
                'errors' => [
                    'reason' => "Resource_Locked",
                    "message" => "The resource requested is currently locked for modification. Try again.",
                ]
            ]
        ];

        $response = json_encode($responseData, true);

        throw new RequestError($request, $response);
    }

    public function initialize() : Session
    {
        $shippingFee = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees\Fee(1, "Shipping fee", 10, 25);
        $fees = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees($shippingFee, null);
        $item = new \Webbhuset\CollectorCheckoutSDK\Checkout\Cart\Item(
            'my-sku',
            'Kanelbulle',
            59,
            1,
            25,
            false,
            'my-sku'
        );
        $cart = new \Webbhuset\CollectorCheckoutSDK\Checkout\Cart([$item]);
        $customer = new \Webbhuset\CollectorCheckoutSDK\Checkout\Customer\InitializeCustomer(
            'test@example.com',
            '0123456789',
            '89123456',
            '12345'
        );

        $countryCode = 'SE';
        $reference = 'ref-000001';

        $adapter = $this->getAdapter();
        $session = new \Webbhuset\CollectorCheckoutSDK\Session($adapter);
        $session->initialize(
            $this->getConfig(),
            $fees,
            $cart,
            $countryCode,
            $customer,
            $reference
        );

        return $session;
    }

    public function initAndGetIframe() : string
    {
        $session = $this->initialize();

        $iframeConfig = new \Webbhuset\CollectorCheckoutSDK\Config\IframeConfig(
            $session->getPublicToken()
        );

        $iframe = \Webbhuset\CollectorCheckoutSDK\Iframe::getScript($iframeConfig);

        return $iframe;
    }

    public function initAndUpdateFees() : Session
    {
        $session = $this->initialize();
        $this->updateFees($session);

        return $session;
    }

    public function updateFees(Session $session) : Session
    {
        $shippingFee = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees\Fee(
            1,
            "Shipping fee",
            20,
            25
        );

        $directInvoiceFee = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees\Fee(
            2,
            "Direct invoice fee",
            100,
            25
        );

        $fees = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees($shippingFee, $directInvoiceFee);

        $session->updateFees($fees);

        return $session;
    }

    public function initAndSetReference()
    {
        $session = $this->initialize();
        $this->setOrderReference($session);
        return $session;
    }

    public function setOrderReference(Session $session) : Session
    {
        $newRef = "updated-reference-001";
        $session->setOrderReference($newRef);

        return $session;
    }

    public function initAndUpdateCart() : Session
    {
        $session = $this->initialize();
        $item = new \Webbhuset\CollectorCheckoutSDK\Checkout\Cart\Item(
            'my-new-sku',
            'Kanelbulle 2',
            59,
            1,
            25,
            false,
            'my-new-sku'
        );
        $cart = new \Webbhuset\CollectorCheckoutSDK\Checkout\Cart([$item]);
        $session->updateCart($cart);

        return $session;
    }

    public function initAndLoad() : CheckoutData
    {
        $session = $this->initialize();
        $privateId = $session->getPrivateId();

        $session->load($privateId);

        return $session->getCheckoutData;
    }

    public function initUpdateLoad()
    {
        $session = $this->initAndUpdateCart();
        $session = $this->updateFees($session);

        $privateId = $session->getPrivateId();
        $session->load($privateId);

        return $session->getCheckoutData();
    }

    public function mockPrivateCustomer() : PrivateCustomer
    {
        $invoiceAddress = new PrivateAddress(
            'Testname',
            'Testsson',
            'Storgatan 14',
            '12345',
            'Ankeborg',
            'Sverige',
            null,
            null
        );

        $deliveryAddress = new PrivateAddress(
            'Testname',
            'Testsson',
            'Torggatan 94',
            '40000',
            'Göteborg',
            'Sverige',
            null,
            null
        );

        $customer = new PrivateCustomer(
            'test@example.com',
            '123456789',
            '987654321',
            $invoiceAddress,
            $deliveryAddress
        );

        return $customer;
    }

    public function testSharedKeyHeaderGeneration() : bool
    {
        // Example input and result from https://jsfiddle.net/wmLg1s35/12/
        $username = 'myUsername';
        $path = '/checkout';
        $sharedAccessKey = 'mySharedKey';
        $requestBody = '{"storeId":123,"countryCode":"SE","reference":"123456789","notificationUri":"http://backend-api-notification-uri.com","redirectPageUri":"http://purchase-completed-confirmation-page.com","merchantTermsUri":"http://merchant-purchase-terms.com","cart":{"items":[{"id":1,"description":"Someproduct","unitPrice":200,"quantity":1,"vat":20}]}}';

        $expectedResult = "SharedKey bXlVc2VybmFtZTpmNTJiYzE3YmIyNWFmOWYzMzVlY2M2MjhjOWY0N2RiNGMwNTdmY2ZhYmVlYzRjM2Y0ZDRiMjRiMTU2N2QwYWNk";
        $key = \Webbhuset\CollectorCheckoutSDK\Adapter\Request::getSharedKeyHeader($username, $requestBody, $path, $sharedAccessKey);

        if ($key === $expectedResult) {
            return true;
        }

        return false;
    }
}
