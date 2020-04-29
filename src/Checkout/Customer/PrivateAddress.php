<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout\Customer;

class PrivateAddress
{
    protected $firstName;
    protected $lastName;
    protected $coAddress;
    protected $address;
    protected $address2;
    protected $postalCode;
    protected $city;
    protected $country;

    public function __construct(
        string $firstName,
        string $lastName,
        string $address,
        string $postalCode,
        string $city,
        string $country,
        string $address2 = null,
        string $coAddress = null
    ) {
        $this->firstName    = $firstName;
        $this->lastName     = $lastName;
        $this->coAddress    = $coAddress;
        $this->address      = $address;
        $this->address2     = $address2;
        $this->postalCode   = $postalCode;
        $this->city         = $city;
        $this->country      = $country;
    }

    public function getFirstName() : string
    {
        return $this->firstName;
    }

    public function getLastName() : string
    {
        return $this->lastName;
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
}
