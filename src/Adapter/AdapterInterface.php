<?php

namespace Webbhuset\CollectorCheckoutSDK\Adapter;

use Webbhuset\CollectorCheckoutSDK\Adapter\Request;

interface AdapterInterface
{
    public function __construct(
        \Webbhuset\CollectorCheckoutSDK\Config\ConfigInterface $config
    );

    public function initializeCheckout(array $data) : array;

    public function updateCart(array $data, string $privateId) : array;

    public function updateFees(array $data, string $privateId) : array;

    public function setOrderReference(string $reference, string $privateId) : array;

    public function acquireInformation(string $privateId) : array;
}
