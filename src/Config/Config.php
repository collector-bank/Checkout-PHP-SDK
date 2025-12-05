<?php

namespace Webbhuset\CollectorCheckoutSDK\Config;

class Config implements \Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface
{
    protected $accessKey;
    protected $countryCode;
    protected $storeId;
    protected $isTestMode = false;
    protected $merchantTermsUri;
    protected $redirectPageUri;
    protected $notificationUri;
    protected $validationUri;
    protected $profileName;

    public function getAccessKey() : string
    {
        return $this->accessKey;
    }

    public function setAccessKey(string $accessKey) : Config
    {
        $this->accessKey = $accessKey;

        return $this;
    }

    public function getCountryCode() : string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode) : Config
    {
        $this->countryCode = $countryCode;


        return $this;
    }

    public function getIsTestMode() : bool
    {
        return $this->isTestMode;
    }

    public function setIsTestMode($bool) : Config
    {
        $this->isTestMode = $bool;

        return $this;
    }

    public function getStoreId() : string
    {
        return $this->storeId;
    }

    public function setStoreId(string $storeId) : Config
    {
        $this->storeId = $storeId;

        return $this;
    }

    public function getMerchantTermsUri() : string
    {
        return $this->merchantTermsUri;
    }

    public function setMerchantTermsUri(string $merchantTermsUri) : Config
    {
        $this->merchantTermsUri = $merchantTermsUri;

        return $this;
    }

    public function getRedirectPageUri()
    {
        return $this->redirectPageUri;
    }

    public function setRedirectPageUri(string $redirectPageUri) : Config
    {
        $this->redirectPageUri = $redirectPageUri;

        return $this;
    }

    public function getNotificationUri() : string
    {
        return $this->notificationUri;
    }

    public function setNotificationUri(string $notificationUri) : Config
    {
        $this->notificationUri = $notificationUri;

        return $this;
    }

    public function getValidationUri()
    {
        return $this->validationUri;
    }

    public function setValidationUri(string $validationUri) : Config
    {
        $this->validationUri = $validationUri;

        return $this;
    }

    public function getProfileName() : string
    {
        return $this->profileName;
    }

    public function setProfileName(string $profileName) : Config
    {
        $this->profileName = $profileName;

        return $this;
    }
}
