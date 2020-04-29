<?php

namespace Webbhuset\CollectorCheckoutSDK\Config;

interface ConfigInterface
{
    public function getUsername() : string;
    public function getSharedAccessKey() : string;
    public function getCountryCode() : string;
    public function getStoreId() : string;

    public function getIsMockMode() : bool;
    public function getIsTestMode() : bool;

    public function getMerchantTermsUri() : string;
    public function getRedirectPageUri();
    public function getNotificationUri() : string;
    public function getValidationUri();
    public function getProfileName();
}
