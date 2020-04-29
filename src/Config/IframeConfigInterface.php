<?php

namespace Webbhuset\CollectorCheckoutSDK\Config;

interface IframeConfigInterface
{
    public function getSrc($mode) : string;
    public function getDataToken();
    public function getDataLang();
    public function getDataPadding();
    public function getDataContainerId();
    public function getDataActionColor();
    public function getDataActionTextColor();
}
