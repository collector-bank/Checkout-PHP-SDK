<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout\Customer;

class InitializeCustomer
{
    protected $email;
    protected $mobilePhoneNumber;
    protected $nationalIdentificationNumber;
    protected $postalCode;

    public function __construct(
        string $email,
        string $mobilePhoneNumber,
        string $nationalIdentificationNumber = null,
        string $postalCode = null
    ) {
        $this->email                        = $email;
        $this->mobilePhoneNumber            = $mobilePhoneNumber;
        $this->nationalIdentificationNumber = $nationalIdentificationNumber;
        $this->postalCode                   = $postalCode;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getMobilePhoneNumber() : string
    {
        return $this->mobilePhoneNumber;
    }

    public function getNationalIdentificationNumber() : string
    {
        return $this->nationalIdentificationNumber;
    }

    public function getPostalCode() : string
    {
        return $this->postalCode;
    }

    public function toArray() : array
    {
        return [
            'email'                         => $this->getEmail(),
            'mobilePhoneNumber'             => $this->getMobilePhoneNumber(),
            'nationalIdentificationNumber'  => $this->getNationalIdentificationNumber(),
            'postalCode'                    => $this->getPostalCode(),
        ];
    }
}
