<?php

namespace Webbhuset\CollectorCheckoutSDK\Checkout;

use Webbhuset\CollectorCheckoutSDK\Checkout\Cart\Item;

class Cart
{
    protected $items = [];
    protected $totalAmount;

    public function __construct(
        array $items,
        int $totalAmount = null
    ) {
        // Type check items
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    protected function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    public function getItems() : array
    {
        return $this->items;
    }

    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function toArray() : array
    {
        $items = array_map(function($item) {
            return $item->toArray();
        }, $this->getItems());

        return [
            'items' => $items,
        ];
    }
}
