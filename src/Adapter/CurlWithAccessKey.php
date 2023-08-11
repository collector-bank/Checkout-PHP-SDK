<?php

namespace Webbhuset\CollectorCheckoutSDK\Adapter;

use Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface;
use Webbhuset\CollectorCheckoutSDK\Adapter\Request;
use Webbhuset\CollectorCheckoutSDK\Errors\RequestError;
use Webbhuset\CollectorCheckoutSDK\Errors\ResponseError;

class CurlWithAccessKey
    extends CurlAdapter
    implements \Webbhuset\CollectorCheckoutSDK\Adapter\AdapterInterface
{
    protected $baseUrl = 'https://api.walleypay.com';
    protected $baseTestUrl = 'https://api.uat.walleydev.com';
    protected $initializePath = '/checkouts';
    protected $updateCartPath = '/checkouts/{privateId}/cart';
    protected $updateFeesPath = '/checkouts/{privateId}/fees';
    protected $referencePath  = '/checkouts/{privateId}/reference';
    protected $acquireInfoPath = '/checkouts/{privateId}';
    protected $config;

    public function __construct(
        ConfigInterface $config
    ) {
        parent::__construct($config);

        $this->config = $config;
    }

    public function getHeaders(string $body, string $path, string $method) : array
    {
        $accessKey = $this->config->getAccessKey();

        if ($method === 'GET') {
            return [
                'Authorization: Bearer ' . $accessKey
            ];
        }
        return [
            'Content-Type: application/json',
            'charset=utf-8',
            'Content-Length: ' . strlen($body),
            'Authorization:Bearer ' . $accessKey
        ];
    }
}
