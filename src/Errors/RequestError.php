<?php

namespace Webbhuset\CollectorCheckoutSDK\Errors;

class RequestError extends \Exception
{
    protected $request;

    public function __construct($request, int $code, string $message)
    {
        $this->request = $request;

        parent::__construct($message, $code);
    }

    public function getRequest()
    {
        return $this->request;
    }
}
