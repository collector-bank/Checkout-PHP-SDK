<?php

namespace Webbhuset\CollectorCheckoutSDK;

use Webbhuset\CollectorCheckoutSDK\Config\IframeConfig;

class Iframe
{
    public static function getScript(IframeConfig $iframeConfig, String $mode = "production mode") : string
    {
        $data = [
            'src'               => $iframeConfig->getSrc($mode),
            'data-token'        => $iframeConfig->getDataToken(),
            'data-lang'         => $iframeConfig->getDataLang(),
            'data-padding'      => $iframeConfig->getDataPadding(),
            'data-container-id' => $iframeConfig->getDataContainerId(),
            'data-action-color' => $iframeConfig->getDataActionColor(),
            'data-action-text-color' => $iframeConfig->getDataActionTextColor(),
        ];

        $properties = [];
        foreach ($data as $k => $v) {
            if (!$v) {
                continue;
            }

            $properties[] = "{$k}=\"{$v}\"";
        }
        $properties = implode(' ', $properties);

        $script = "<script {$properties}></script>";

        return $script;
    }
}
