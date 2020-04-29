<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout;

use Webbhuset\CollectorCheckoutSDK\Checkout\Fees\Fee;

class Fees
{
    protected $shippingFee;
    protected $directInvoiceFee;

    public function __construct(
        Fee $shippingFee = null,
        Fee $directInvoiceFee = null
    ) {
        $this->shippingFee      = $shippingFee;
        $this->directInvoiceFee = $directInvoiceFee;
    }

    public function getShippingFee() : Fee
    {
        return $this->shippingFee;
    }

    public function getDirectInvoiceFee() : Fee
    {
        return $this->directInvoiceFee;
    }

    public function toArray() : array
    {
        $fees = [];

        if ($this->shippingFee) {
            $fees['shipping'] = $this->shippingFee->toArray();
        }

        if ($this->directInvoiceFee) {
            $fees['directInvoiceFee'] = $this->directInvoiceFee->toArray();
        }

        return $fees;
    }
}
