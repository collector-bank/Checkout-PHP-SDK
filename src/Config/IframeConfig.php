<?php

namespace Webbhuset\CollectorCheckoutSDK\Config;

class IframeConfig
    implements \Webbhuset\CollectorCheckoutSDK\Config\IframeConfigInterface
{
    protected $dataToken;
    protected $dataLang;
    protected $dataPadding;
    protected $dataContainerId;
    protected $dataActionColor;
    protected $dataActionTextColor;

    public function __construct(
        string $dataToken,
        string $dataLang = null,
        string $dataPadding = null,
        string $dataContainerId = null,
        string $dataActionColor = null,
        string $dataActionTextColor = null
    ) {
        $this->dataToken            = $dataToken;
        $this->dataLang             = $dataLang;
        $this->dataContainerId      = $dataContainerId;
        $this->dataActionColor      = $dataActionColor;
        $this->dataActionTextColor  = $dataActionTextColor;
        $this->dataPadding          = $dataPadding;
    }

    public function getSrc($mode = "production mode") : string
    {
        if ("production mode" == $mode) {

            return 'https://api.walleypay.com/walley-checkout-loader.js';
        }

        return 'https://api.uat.walleydev.com/walley-checkout-loader.js';
    }

    /**
     * The publicToken acquired when Initializing a Checkout Session.
     *
     * @return string
     */
    public function getDataToken() : string
    {
        return $this->dataToken;
    }

    /**
     * The display language. Currently supported combinations are:
     * sv-SE, en-SE, nb-NO, fi-FI, da-DK and en-DE. Both sv-SE and en-SE
     *
     * @return string
     */
    public function getDataLang()
    {
        return $this->dataLang;
    }

    public function getDataPadding()
    {
        return $this->dataPadding;
    }

    public function getDataContainerId()
    {
        return $this->dataContainerId;
    }

    public function getDataActionColor()
    {
        return $this->dataActionColor;
    }

    public function getDataActionTextColor()
    {
        return $this->dataActionTextColor;
    }
}
