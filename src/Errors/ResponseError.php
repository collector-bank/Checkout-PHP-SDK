<?php

namespace Webbhuset\CollectorCheckoutSDK\Errors;

class ResponseError extends \Exception
{
    protected $request;
    protected $response;

    public function __construct($request, array $response)
    {
        $this->request = $request;
        $this->response = $response;

        $responseData = $this->getResponseBody();

        $message = isset($responseData['error']['message'])
            ? $responseData['error']['message']
            : 'Something went wrong with the request';

        $code = isset($responseData['error']['code'])
            ? $responseData['error']['code']
            : 1;

        parent::__construct($message, $code);
    }

    public function getErrors() : array
    {
        $response = $this->getResponse();
        if (isset($response['error']['errors'])) {
            return $response['error']['errors'];
        }

        return [];
    }

    public function getResponse()
    {
        return $this->response;
    }


    public function getErrorLogMessageFromResponse()
    {
        $errorLogMessage = "";
        $errorLogMessage .= $this->getMessage();
        $responseBody = $this->getResponseBody();

        if (isset($responseBody['error']['errors'][0]['reason'])
            && isset($responseBody['error']['errors'][0]['message'])
        ) {
            $reason = $responseBody['error']['errors'][0]['reason'];
            $message = $responseBody['error']['errors'][0]['message'];

            $errorLogMessage .= " $reason: $message";
        }

        return $errorLogMessage;
    }


    public function getResponseBody()
    {
        $body = $this->response['body'] ?? '';
        $decodedBody = json_decode($body, true);

        return $decodedBody;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
