<?php

namespace Webbhuset\CollectorCheckoutSDK\Adapter;

use Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface;
use Webbhuset\CollectorCheckoutSDK\Adapter\Request;
use Webbhuset\CollectorCheckoutSDK\Errors\RequestError;
use Webbhuset\CollectorCheckoutSDK\Errors\ResponseError;

class CurlAdapter
    implements \Webbhuset\CollectorCheckoutSDK\Adapter\AdapterInterface
{
    protected $postData = [];
    protected $headers = [];
    protected $config;
    protected $baseUrl = 'https://api.checkout.walleypay.com';
    protected $baseTestUrl = 'https://api.checkout.uat.walleydev.com';
    protected $initializePath = '/checkout';
    protected $updateCartPath = '/merchants/{storeId}/checkouts/{privateId}/cart';
    protected $updateFeesPath = '/merchants/{storeId}/checkouts/{privateId}/fees';
    protected $referencePath  = '/merchants/{storeId}/checkouts/{privateId}/reference';
    protected $acquireInfoPath = '/merchants/{storeId}/checkouts/{privateId}';

    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function initializeCheckout(array $data) : array
    {
        $path = $this->initializePath;
        $body = json_encode($data);


        $response = $this->sendRequest($path, $body, 'POST');
        $responseBody = $this->extractBody($response);

        if (!$this->validateResponse($responseBody)) {
            throw new ResponseError($body, $response);
        }

        return $responseBody;
    }

    public function updateCart(array $data, string $privateId) : array
    {
        $storeId = $this->config->getStoreId();
        $path = $this->updateCartPath;
        $path = $this->replacePath($path, $privateId, $storeId);
        $body = json_encode($data);

        $response = $this->sendRequest($path, $body, 'PUT');
        $responseBody = $this->extractBody($response);

        if (!$this->validateResponse($responseBody)) {
            throw new ResponseError($body, $response);
        }

        return $responseBody;
    }

    public function updateFees(array $data, string $privateId) : array
    {
        $storeId = $this->config->getStoreId();
        $path = $this->updateFeesPath;
        $path = $this->replacePath($path, $privateId, $storeId);
        $body = json_encode($data);

        $response = $this->sendRequest($path, $body, 'PUT');
        $responseBody = $this->extractBody($response);

        if (!$this->validateResponse($responseBody)) {
            throw new ResponseError($body, $response);
        }

        return $responseBody;
    }

    public function setOrderReference(string $reference, string $privateId) : array
    {
        $storeId = $this->config->getStoreId();
        $path = $this->referencePath;
        $path = $this->replacePath($path, $privateId, $storeId);
        $data = [
            'Reference' => $reference,
        ];

        $body = json_encode($data);

        $response = $this->sendRequest($path, $body, 'PUT');
        $responseBody = $this->extractBody($response);

        if (!$this->validateResponse($responseBody)) {
            throw new ResponseError($body, $response);
        }

        return $responseBody;
    }

    public function acquireInformation(string $privateId) : array
    {
        $storeId = $this->config->getStoreId();
        $path = $this->acquireInfoPath;
        $path = $this->replacePath($path, $privateId, $storeId);

        $response = $this->sendRequest($path);
        $responseBody = $this->extractBody($response);

        if (!$this->validateResponse($responseBody)) {
            throw new ResponseError('', $response);
        }

        return $responseBody;
    }

    protected function sendRequest(string $path, string $body = '', $method = 'GET')
    {
        $url = $this->getBaseUrl() . $path;

        $headers = $this->getHeaders($body, $path, $method);

        $ch = curl_init();
        $options = [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_CONNECTTIMEOUT  => 30,
            CURLINFO_HEADER_OUT     => true
        ];

        if ($method === 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = "PUT";
            $options[CURLOPT_POSTFIELDS]    = $body;
        } elseif ($method === 'POST') {
            $options[CURLOPT_POST]          = true;
            $options[CURLOPT_POSTFIELDS]    = $body;
        }

        curl_setopt_array($ch, $options);
        $response   = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error      = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($error) {
            throw new RequestError($body, 0, $error);
        }

        $header = substr($response, 0, $headerSize);
        $responseBody = substr($response, $headerSize);

        return [
            'header' => $header,
            'body' => $responseBody,
        ];
    }

    protected function extractBody(array $response)
    {
        $body = $response['body'] ?? '';
        $decodedBody = json_decode($body, true);

        return $decodedBody;
    }

    public function getHeaders(string $body, string $path, string $method) : array
    {
        $userName = $this->config->getUsername();
        $sharedAccessKey = $this->config->getSharedAccessKey();

        $sharedKeyHeader = Request::getSharedKeyHeader($userName, $body, $path, $sharedAccessKey);
        
        if ($method === 'GET') {
            return [
                'Authorization: ' . $sharedKeyHeader
            ];
        }
        return [
            'Content-Type: application/json',
            'charset=utf-8',
            'Content-Length: ' . strlen($body),
            'Authorization: ' . $sharedKeyHeader
        ];
    }

    protected function replacePath(string $path, string $privateId, string $storeId) : string
    {
        $path = str_replace('{privateId}', $privateId, $path);
        $path = str_replace('{storeId}', $storeId, $path);

        return $path;
    }

    public function getBaseUrl()
    {
        if ($this->config->getIsTestMode()) {
            return $this->baseTestUrl;
        }

        return $this->baseUrl;
    }

    protected function validateResponse($response) : bool
    {
        if (
            is_array($response)
            && array_key_exists('error', $response)
            && empty($response['error'])
        ) {
            // good result should have 'error' => null
            return true;
        }

        return false;
    }
}
