# Checkout api SDK
Php package used to communicate with Collector Bank Checkout API. Used when creating orders.

[Checkout API reference](http://web-checkout-documentation.azurewebsites.net/)


Data objects are immutable. Pass all variables in the constructor.

### Example initialization
```
$config = new \Webbhuset\CollectorCheckoutSDK\Config\Config;  // Either use this class or create your own class, implementing \Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface
$config->setUsername('my-username')
    ->setSharedAccessKey('my-shared-access-key')
    ->setCountryCode('SE')
    ->setStoreId('1')
    ->setRedirectPageUri('https://example.com')
    ->setMerchantTermsUri('https://example.com')
    ->setNotificationUri('https://example.com')
    ->setValidationUri('https://example.com');

$adapter = new \Webbhuset\CollectorCheckoutSDK\Adapter\CurlAdapter($config);

$shippingFee = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees\Fee(1, "Shipping fee", 10, 25);
$fees = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees($shippingFee, null);
$item = new \Webbhuset\CollectorCheckoutSDK\Checkout\Cart\Item(
    'my-sku',
    'The product name',
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

$session = new \Webbhuset\CollectorCheckoutSDK\Session($adapter);


try {
    $session->initialize($this->getConfig(), $fees, $cart, $customer, $countryCode, $reference);

} catch (RequestError $e) {
    // do stuff
} catch (ResponseError $e) {
    // do stuff
}


$iframeSnippet = \Webbhuset\CollectorCheckoutSDK\Iframe->getScript($iframeConfig)
```


If an address is updated (not handled in this lib), you might have to update items or fees if address has changed
```
$session = new Session($config)
$checkoutData = $session->load($privateId)
    ->getCheckoutData() //initialized

$deliveryCountry = $checkoutData->getCustomer()->getDeliveryAddress()
->getCountry();

if ($deliveryCountry == 'Sverige') {
    $shippingFee = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees\Fee(
            1,
            "Shipping fee for sweden",
            20,
            25
    );
    $newFees = new \Webbhuset\CollectorCheckoutSDK\Checkout\Fees($shippingFee, null);
    $session->updateFees($newFees)
}
```
To render checkout or success iframe
```

// If the session got initalized in another request, you have to save to public token and use it
$session = new Session($config);
$publicToken = $mySavedToken;

// Or initialize new session
$session->initialize($this->getConfig(), $fees, $cart, $customer, $countryCode, $reference);
$publicToken = $session->getPublicToken();


$iframeConfig = new \Webbhuset\CollectorCheckoutSDK\Config\IframeConfig(
    $publicToken
);

$iframe = $session->getIframe($iframeConfig)

```
