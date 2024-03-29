<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout\Customer;

use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\PrivateAddress;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\AbstractCustomer;

class PrivateCustomer extends AbstractCustomer
{
    protected $email;
    protected $mobilePhoneNumber;
    protected $deliveryMobilePhoneNumber;
    protected $invoiceAddress;
    protected $deliveryAddress;
    protected $nationalIdentificationNumber;

    public function __construct(
        string $email,
        string $mobilePhoneNumber,
        string $deliveryMobilePhoneNumber,
        PrivateAddress $invoiceAddress,
        PrivateAddress $deliveryAddress,
        string $nationalIdentificationNumber = ''
    ) {
        $this->email                        = $email;
        $this->mobilePhoneNumber            = $mobilePhoneNumber;
        $this->deliveryMobilePhoneNumber    = $deliveryMobilePhoneNumber;
        $this->invoiceAddress               = $invoiceAddress;
        $this->deliveryAddress              = $deliveryAddress;
        $this->nationalIdentificationNumber = $nationalIdentificationNumber;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getMobilePhoneNumber() : string
    {
        return $this->mobilePhoneNumber;
    }

    public function getDeliveryMobilePhoneNumber() : string
    {
        return $this->deliveryMobilePhoneNumber;
    }

    public function getInvoiceAddress() : PrivateAddress
    {
        return $this->invoiceAddress;
    }

    public function getDeliveryAddress() : PrivateAddress
    {
        return $this->deliveryAddress;
    }

    public function getNationalIdentificationNumber(): string
    {
        return $this->nationalIdentificationNumber;
    }
}
