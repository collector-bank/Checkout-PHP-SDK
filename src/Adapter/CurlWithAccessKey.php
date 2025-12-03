<?php

namespace Webbhuset\CollectorCheckoutSDK\Adapter;

use Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface;
use Webbhuset\CollectorCheckoutSDK\Adapter\Request;
use Webbhuset\CollectorCheckoutSDK\Errors\RequestError;
use Webbhuset\CollectorCheckoutSDK\Errors\ResponseError;
use Webbhuset\CollectorPaymentSDK\Invoice\Article\ArticleList;

class CurlWithAccessKey
    implements \Webbhuset\CollectorCheckoutSDK\Adapter\AdapterInterface
{
    const HEADER_ACCEPTED = 'HTTP/1.1 202 Accepted';

    protected $baseUrl = 'https://api.walleypay.com';
    protected $baseTestUrl = 'https://api.uat.walleydev.com';
    protected $initializePath = '/checkouts';
    protected $updateCartPath = '/checkouts/{privateId}/cart';
    protected $updateFeesPath = '/checkouts/{privateId}/fees';
    protected $referencePath  = '/checkouts/{privateId}/reference';
    protected $acquireInfoPath = '/checkouts/{privateId}';
    protected $getOrderPath = '/manage/orders/{privateId}';
    protected $partActivatePath = '/manage/orders/{privateId}/capture';
    protected $partCreditPath = '/manage/orders/{privateId}/refund';
    protected $cancelInvoicePath = '/manage/orders/{privateId}/cancel';
    protected $reauthorizePath = '/manage/orders/{privateId}/reauthorize';
    protected $reauthorizeStatusPath = '/manage/orders/{privateId}/reauthorize';

    protected $config;

    public function __construct(
        ConfigInterface $config
    ) {

        $this->config = $config;
    }

    public function getHeaders(string $body, string $path, string $method, $accessKey = "") : array
    {
        if ($accessKey === "") {
            $accessKey = $this->config->getAccessKey();
        }

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

    public function getOrder(string $orderReference)
    {
        $path = $this->getOrderPath;
        $path = $this->replacePathPrivate($path, $orderReference);

        $response = $this->sendRequest($path, '', 'GET');
        $responseBody = $this->extractBody($response);

        return $responseBody;
    }

    /**
     * @param string $orderReference
     * @param array $payload
     * @return array response status code
     */
    public function reauthorize(string $orderReference, array $payload):array
    {
        $path = $this->reauthorizePath;
        $path = $this->replacePathPrivate($path, $orderReference);

        $bodyJsonEncoded = json_encode($payload);
        $response = $this->sendRequest($path, $bodyJsonEncoded, 'POST');

        return [
            'status' => (int) $this->getStatusCodeFromResponse($response),
            'reauthorizationId' => $this->getReauthorizationIdFromResponse($response),
        ];

    }

    private function getReauthorizationIdFromResponse(array $response): ?string
    {
        if (empty($response['header'])) {
            return null;
        }

        $rawHeader = $response['header'];

        if (is_string($rawHeader)) {
            $headers = preg_split('/\r\n|\r|\n/', $rawHeader);

            if (count($headers) === 1) {
                $headers = preg_split('/(?=\s[A-Za-z0-9\-]+:)/', $rawHeader);
            }
        } else {
            $headers = (array)$rawHeader;
        }

        $location = null;
        foreach ($headers as $headerLine) {
            $headerLine = trim($headerLine);
            if (stripos($headerLine, 'Location:') === 0) {
                $location = trim(substr($headerLine, strlen('Location:')));
                break;
            }
        }

        if (!$location) {
            return null;
        }

        if (preg_match('#/reauthorize/([^/]+)$#i', $location, $matches)) {
            return $matches[1];
        }

        $parts = explode('/', trim($location, '/'));
        return end($parts) ?: null;
    }

    /**
     * @param int $walleyOrderId
     * @param int $reauthorizeId
     * @return int response status code
     */
    public function reauthorizeStatus($walleyOrderId, $reauthorizeId):int
    {
        $path = $this->reauthorizeStatusPath;
        $path = $this->replacePathPrivate($path, $walleyOrderId) . '/' . $reauthorizeId;
        $response = $this->sendRequest($path);

        return (int) $this->getStatusCodeFromResponse($response);
    }

    private function getStatusCodeFromResponse($response) {
        $headerText = $response['header'];
        $statusLine = explode("\r\n", $headerText)[0]; // get the first line
        $parts = explode(' ', $statusLine); // split parts by space

        if (count($parts) < 2) {
            throw new \Exception('Invalid HTTP response header');
        }

        $statusCode = intval($parts[1]); // get the second part which is the status code

        return $statusCode;
    }

    public function getReauthorizeStatus(string $location)
    {
        $response = $this->sendRequest($location, 'GET');
        $responseBody = $this->extractBody($response);

        return $responseBody;
    }

    public function partActivateInvoice(
        string $orderReference,
        ArticleList $articleList,
        string $correlationId
    ) {
        $path = $this->replacePathPrivate($this->partActivatePath, $orderReference);
        $items = $this->convertArticleListToItems($articleList);

        $body = [
            'amount' => $this->getArticleListAmount($articleList),
            'actionReference' => $correlationId,
            'items' => $items,
        ];
        $bodyJsonEncoded = json_encode($body);
        $response = $this->sendRequest($path, $bodyJsonEncoded, 'POST');

        if (!$this->isResponseHeader202($response['status'])) {
            throw new ResponseError($body, $response);
        }

        return self::HEADER_ACCEPTED;
    }

    public function partCreditInvoice(
        string $orderReference,
        ArticleList $articleList,
        string $correlationId
    ) {
        $path = $this->replacePathPrivate($this->partCreditPath, $orderReference);
        $items = $this->convertArticleListToItems($articleList);

        $body = [
            'amount' => $this->getArticleListAmount($articleList),
            'actionReference' => $correlationId,
            'items' => $items,
        ];
        $bodyJsonEncoded = json_encode($body);
        $response = $this->sendRequest($path, $bodyJsonEncoded, 'POST');

        if (isset($response['header'])
            && !$this->isResponseHeader202($response['status'])) {
            throw new ResponseError($body, $response);
        }

        return self::HEADER_ACCEPTED;
    }

    public function cancelInvoice(
        string $orderReference,
        ArticleList $articleList,
        string $correlationId
    ) {
        $path = $this->replacePathPrivate($this->cancelInvoicePath, $orderReference);
        $items = $this->convertArticleListToItems($articleList);

        $body = [
            'amount' => $this->getArticleListAmount($articleList),
            'actionReference' => $correlationId,
            'items' => $items,
        ];
        $bodyJsonEncoded = json_encode($body);
        $response = $this->sendRequest($path, $bodyJsonEncoded, 'POST');

        if (isset($response['header'])
            && !$this->isResponseHeader202($response['header'])) {
            throw new ResponseError($body, $response);
        }

        return self::HEADER_ACCEPTED;
    }

    private function isResponseHeader202($status):bool
    {
        return (int) $status === 202;
    }

    protected function replacePathPrivate(string $path, string $privateId) : string
    {
        $path = str_replace('{privateId}', $privateId, $path);

        return $path;
    }

    private function getArticleListAmount(ArticleList $articleList):float
    {
        $result = 0;
        $articleListArray = $articleList->getArticleList();
        foreach ($articleListArray as $article) {
            $result += $article['Quantity'] * $article['UnitPrice'];
        }

        return $result;
    }

    private function convertArticleListToItems(ArticleList $articleList):array
    {
        $result = [];
        $articleListArray = $articleList->getArticleList();
        foreach ($articleListArray as $article) {
            $result[] = [
                'id' => $article['ArticleId'],
                'description' => $article['Description'],
                'quantity' => $article['Quantity'],
                'unitPrice' => $article['UnitPrice'],
                'type' => $article['Type'],
                'vat' => $article['VAT'],
            ];
        }

        return $result;
    }

    /**
     * Initialize checkout
     */
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
        $path = $this->updateCartPath;
        $path = $this->replacePathPrivate($path, $privateId);
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
        $path = $this->updateFeesPath;
        $path = $this->replacePathPrivate($path, $privateId);
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
        $path = $this->referencePath;
        $path = $this->replacePathPrivate($path, $privateId);
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
        $path = $this->acquireInfoPath;
        $path = $this->replacePathPrivate($path, $privateId);

        $response = $this->sendRequest($path, '', 'GET');
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
            'status' => $httpCode,
        ];
    }

    protected function extractBody(array $response)
    {
        $body = $response['body'] ?? '';
        $decodedBody = json_decode($body, true);

        return $decodedBody;
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
