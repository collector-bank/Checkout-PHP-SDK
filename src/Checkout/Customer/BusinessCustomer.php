<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout\Customer;

use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\BusinessAddress;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\AbstractCustomer;

class BusinessCustomer extends AbstractCustomer
{
    protected $companyName;
    protected $organizationNumber;
    protected $invoiceReference;
    protected $invoiceTag;
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $mobilePhoneNumber;
    protected $invoiceAddress;
    protected $deliveryAddress;

    public function __construct(
        string $companyName,
        string $organizationNumber,
        string $invoiceReference,
        string $invoiceTag,
        string $email,
        string $firstName,
        string $lastName,
        string $mobilePhoneNumber,
        BusinessAddress $invoiceAddress,
        BusinessAddress $deliveryAddress
    ) {
        $this->companyName          = $companyName;
        $this->organizationNumber   = $organizationNumber;
        $this->invoiceReference     = $invoiceReference;
        $this->invoiceTag           = $invoiceTag;
        $this->email                = $email;
        $this->firstName            = $firstName;
        $this->lastName             = $lastName;
        $this->mobilePhoneNumber    = $mobilePhoneNumber;
        $this->invoiceAddress       = $invoiceAddress;
        $this->deliveryAddress      = $deliveryAddress;
    }

    public function getCompanyName() : string
    {
        return $this->companyName;
    }

    public function getOrganizationNumber() : string
    {
        return $this->organizationNumber;
    }

    public function getInvoiceReference() : string
    {
        return $this->invoiceReference;
    }

    public function getInvoiceTag() : string
    {
        return $this->invoiceTag;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getFirstName() : string
    {
        return $this->firstName;
    }

    public function getLastName() : string
    {
        return $this->lastName;
    }

    public function getMobilePhoneNumber() : string
    {
        return $this->mobilePhoneNumber;
    }

    public function getInvoiceAddress() : BusinessAddress
    {
        return $this->invoiceAddress;
    }

    public function getDeliveryAddress() : BusinessAddress
    {
        return $this->deliveryAddress;
    }
}
