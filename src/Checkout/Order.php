<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout;

use Webbhuset\CollectorCheckoutSDK\Checkout\Order\Item;

class Order
{
    protected $items = [];
    protected $totalAmount;

    public function __construct(
        array $items,
        int $totalAmount
    ) {
        // Type check items
        foreach ($items as $item) {
            $this->addItem($item);
        }

        $this->totalAmount = $totalAmount;
    }

    protected function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    public function getItems() : array
    {
        return $this->items;
    }

    public function getTotalAmount() : int
    {
        return $this->totalAmount;
    }
}
