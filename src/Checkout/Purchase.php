<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout;

use Webbhuset\CollectorCheckoutSDK\Checkout\Purchase\Result;


class Purchase
{
    protected $amountToPay;
    protected $paymentName;
    protected $invoiceDeliveryMethod;
    protected $purchaseIdentifier;
    protected $result;

    public function __construct(
        $amountToPay,
        string $paymentName,
        string $invoiceDeliveryMethod,
        string $purchaseIdentifier,
        Result $result
    ) {
        $this->amountToPay              = $amountToPay;
        $this->paymentName              = $paymentName;
        $this->invoiceDeliveryMethod    = $invoiceDeliveryMethod;
        $this->purchaseIdentifier       = $purchaseIdentifier;
        $this->result                   = $result;
    }

    public function getAmountToPay() : int
    {
        return (int) $this->amountToPay;
    }

    public function getPaymentName() : string
    {
        return $this->paymentName;
    }

    public function getInvoiceDeliveryMethod() : string
    {
        return $this->invoiceDeliveryMethod;
    }

    public function getPurchaseIdentifier() : string
    {
        return $this->purchaseIdentifier;
    }

    public function getResult() : Result
    {
        return $this->result;
    }
}
