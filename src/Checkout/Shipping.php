<?php


namespace Webbhuset\CollectorCheckoutSDK\Checkout;


class Shipping
{
    protected $shippingData = [];

    public function __construct(
        $shippingData
    ) {
        $this->shippingData = $shippingData;
    }

    public function getData()
    {
        return $this->shippingData;
    }
}