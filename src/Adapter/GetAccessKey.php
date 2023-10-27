<?php

namespace Webbhuset\CollectorCheckoutSDK\Adapter;

use Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface;
use Webbhuset\CollectorCheckoutSDK\Adapter\Request;
use Webbhuset\CollectorCheckoutSDK\Errors\RequestError;
use Webbhuset\CollectorCheckoutSDK\Errors\ResponseError;

class GetAccessKey
{
    protected $baseUrl = 'https://api.walleypay.com';
    protected $baseTestUrl = 'https://api.uat.walleydev.com';
    protected $accessTokenPath = '/oauth2/v2.0/token/';
    protected $grantType = "client_credentials";
    protected $scopeUat = "705798e0-8cef-427c-ae00-6023deba29af/.default";
    protected $scopeProd = "a3f3019f-2be9-41cc-a254-7bb347238e89/.default";
    protected $config;

    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function getAccessKey(): string
    {
        $clientId = $this->config->getClientId();
        $clientSecret = $this->config->getClientSecret();
        $grantType = $this->grantType;
        $scope = $this->scopeProd;
        if ($this->config->getIsTestModeOath()) {
            $scope = $this->scopeUat;
        }
        $requestBody = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => $grantType,
            'scope' => $scope,
        ];
        $response = $this->sendAccessKeyRequest($this->accessTokenPath, $requestBody);
        $responseBody = $this->extractBody($response);

        if (!isset($responseBody['access_token'])) {
            throw new ResponseError($requestBody, $response);
        }

        return $responseBody['access_token'];
    }

    protected function sendAccessKeyRequest($path, $body)
    {
        $url = $this->getBaseUrl() . $path;

        $ch = curl_init();
        $options = [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_HEADER          => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_CONNECTTIMEOUT  => 30,
            CURLINFO_HEADER_OUT     => true,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => $body
        ];

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

    public function getBaseUrl()
    {
        if ($this->config->getIsTestMode()) {
            return $this->baseTestUrl;
        }

        return $this->baseUrl;
    }

    protected function extractBody(array $response)
    {
        $body = $response['body'] ?? '';
        $decodedBody = json_decode($body, true);

        return $decodedBody;
    }
}
