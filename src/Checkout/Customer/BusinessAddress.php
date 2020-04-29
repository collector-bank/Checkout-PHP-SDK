<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout\Customer;

class BusinessAddress
{
    protected $companyName;
    protected $coAddress;
    protected $address;
    protected $address2;
    protected $postalCode;
    protected $city;
    protected $country;
    protected $firstName;
    protected $lastName;

    public function __construct(
        string $companyName,
        string $address,
        string $postalCode,
        string $city,
        string $country,
        string $address2 = null,
        string $coAddress = null,
        string $firstName = null,
        string $lastName = null
    ) {
        $this->companyName  = $companyName;
        $this->coAddress    = $coAddress;
        $this->address      = $address;
        $this->address2     = $address2;
        $this->postalCode   = $postalCode;
        $this->city         = $city;
        $this->country      = $country;
        $this->firstName    = $firstName;
        $this->lastName     = $lastName;
    }

    public function getCompanyName() : string
    {
        return $this->companyName;
    }

    public function getCoAddress()
    {
        return $this->coAddress;
    }

    public function getAddress() : string
    {
        return $this->address;
    }

    public function getAddress2()
    {
        return $this->address2;
    }

    public function getPostalCode() : string
    {
        return $this->postalCode;
    }

    public function getCity() : string
    {
        return $this->city;
    }

    public function getCountry() : string
    {
        return $this->country;
    }

    public function getFirstName() : string
    {
        return $this->firstName;
    }

    public function getLastName() : string
    {
        return $this->lastName;
    }
}
