<?php

namespace Webbhuset\CollectorCheckoutSDK\Adapter;

use Magento\Framework\App\ObjectManager;
use Webbhuset\CollectorCheckout\Invoice\ArticleListToInvoiceItems;
use Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface;
use Webbhuset\CollectorCheckoutSDK\Adapter\Request;
use Webbhuset\CollectorCheckoutSDK\Errors\RequestError;
use Webbhuset\CollectorCheckoutSDK\Errors\ResponseError;
use Webbhuset\CollectorPaymentSDK\Invoice\Article\ArticleList;

class CurlWithAccessKey
    extends CurlAdapter
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
        parent::__construct($config);

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
     * @return int response status code
     */
    public function reauthorize(string $orderReference, array $payload):int
    {
        $path = $this->reauthorizePath;
        $path = $this->replacePathPrivate($path, $orderReference);

        $bodyJsonEncoded = json_encode($payload);
        $response = $this->sendRequest($path, $bodyJsonEncoded, 'POST');

        return (int) $this->getStatusCodeFromResponse($response);
    }

    private function getStatusCodeFromResponse($response) {
        $headerText = $response['header'];
        $statusLine = explode("\r\n", $headerText)[0]; // get the first line
        $parts = explode(' ', $statusLine); // split parts by space

        if (count($parts) < 2) {
            throw new Exception('Invalid HTTP response header');
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

        if (!$this->isResponseHeader202($response['header'])) {
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
            && !$this->isResponseHeader202($response['header'])) {
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

    private function isResponseHeader202($header):bool
    {
        return strpos($header, self::HEADER_ACCEPTED) !== false;
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
}
