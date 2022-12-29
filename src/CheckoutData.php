<?php

namespace Webbhuset\CollectorCheckoutSDK;

use Webbhuset\CollectorCheckoutSDK\Errors\ValidationError;
use Webbhuset\CollectorCheckoutSDK\Checkout\Purchase\Result;
use Webbhuset\CollectorCheckoutSDK\Checkout\Status;
use Webbhuset\CollectorCheckoutSDK\Checkout\Purchase;
use Webbhuset\CollectorCheckoutSDK\Checkout\Cart;
use Webbhuset\CollectorCheckoutSDK\Checkout\Cart\Item as CartItem;
use Webbhuset\CollectorCheckoutSDK\Checkout\Order\Item as OrderItem;
use Webbhuset\CollectorCheckoutSDK\Checkout\Fees;
use Webbhuset\CollectorCheckoutSDK\Checkout\Fees\Fee;
use Webbhuset\CollectorCheckoutSDK\Checkout\Order;
use Webbhuset\CollectorCheckoutSDK\Checkout\Shipping;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\AbstractCustomer;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\PrivateCustomer;
use Webbhuset\CollectorCheckoutSDK\Checkout\Customer\BusinessCustomer;

class CheckoutData
{
    protected $customer;
    protected $countryCode = '';
    protected $status;
    protected $paymentName = '';
    protected $reference = '';
    protected $cart;
    protected $fees;
    protected $purchase;
    protected $order;
    protected $shipping;

    public function __construct(
        array $response
    ) {
        $this->fromResponse($response);
    }

    public function getCustomerType() : string
    {
        return $this->customerType;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getCountryCode() : string
    {
        return $this->countryCode;
    }

    public function getStatus() : Status
    {
        return $this->status;
    }

    /**
     * Please note: These values are subject to change and we strongly encourage you to handle them dynamically.
     * New payment names may emerge without any notice and you should design your system to handle this situation.
     *
     * @return string
     */
    public function getPaymentName() : string
    {
        return $this->paymentName;
    }

    public function getReference() : string
    {
        return $this->reference;
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function getFees()
    {
        return $this->fees;
    }

    public function getPurchase()
    {
        return $this->purchase;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getShipping()
    {
        return $this->shipping;
    }

    protected function fromResponse(array $response) : CheckoutData
    {
        $data = isset($response['data']) ? $response['data'] : false;
        if (!$data) {
            throw new ValidationError('Unexpected data in response');
        }

        $this->customer         = $this->customerFromArray($data);
        $this->countryCode      = $data['countryCode'] ?? '';
        $this->status           = $this->statusFromArray($data);
        $this->paymentName      = $data['paymentName'] ?? '';
        $this->reference        = $data['reference'] ?? '';
        $this->cart             = $this->cartFromArray($data);
        $this->fees             = $this->feesFromArray($data);
        $this->purchase         = $this->purchaseFromArray($data);
        $this->order            = $this->orderFromArray($data);
        $this->shipping         = $this->shippingFromArray($data);

        return $this;
    }

    protected function feesFromArray(array $data)
    {
        if (empty($data['fees'])) {
            return null;
        }

        $shippingFeeData    = $data['fees']['shipping'] ?? [];
        $shippingFee        = $this->feeFromArray($shippingFeeData);

        $invoiceFeeData     = $data['fees']['directInvoiceFee'] ?? [];
        $directInvoiceFee   = $this->feeFromArray($invoiceFeeData);

        return new Fees(
            $shippingFee,
            $directInvoiceFee
        );
    }

    protected function feeFromArray(array $data)
    {
        if (empty($data)) {
            return null;
        }

        return new Fee(
            isset($data['id']) ? (string) $data['id'] : '',
            isset($data['description']) ? (string) $data['description'] : '',
            isset($data['unitPrice']) ? (float) $data['unitPrice'] :  0,
            isset($data['vat']) ? (float) $data['vat'] :  0,
            isset($data['sku']) ? (string) $data['sku'] :  ''
        );
    }

    protected function cartFromArray(array $data)
    {
        if (empty($data['cart'])) {
            return null;
        }

        $data           = $data['cart'] ?? [];
        $totalAmount    = isset($data['totalAmount']) ? (int) $data['totalAmount'] : null;
        $itemsData      = $data['items'] ?? [];
        $items          = [];
        foreach ($itemsData as $itemData) {
            $items[] = new CartItem(
                isset($itemData['id'])          ? (string) $itemData['id'] : '',
                isset($itemData['description']) ? (string) $itemData['description'] : '',
                isset($itemData['unitPrice'])   ? (float) $itemData['unitPrice'] : 0,
                isset($itemData['quantity'])    ? (int) $itemData['quantity'] : 0,
                isset($itemData['vat'])         ? (float) $itemData['vat'] : 0
            );
        }

        return new Cart(
            $items,
            $totalAmount
        );
    }

    protected function orderFromArray(array $data)
    {
        if (empty($data['order'])) {
            return null;
        }

        $data           = $data['order'] ?? [];
        $totalAmount    = isset($data['totalAmount']) ? (int) $data['totalAmount'] : 0;
        $itemsData      = $data['items'] ?? [];
        $items          = [];
        foreach ($itemsData as $itemData) {
            $items[] = new OrderItem(
                isset($itemData['id'])          ? (string) $itemData['id'] : '',
                isset($itemData['description']) ? (string) $itemData['description'] : '',
                isset($itemData['unitPrice'])   ? (float) $itemData['unitPrice'] : 0,
                isset($itemData['quantity'])    ? (int) $itemData['quantity'] : 0,
                isset($itemData['vat'])         ? (float) $itemData['vat'] : 0,
                isset($itemData['sku'])         ? (string) $itemData['sku'] : ''
            );
        }

        return new Order(
            $items,
            $totalAmount
        );
    }

    protected function shippingFromArray(array $data) : Shipping
    {
        $shippingData = isset($data['shipping'])
            ? $data['shipping']
            : [];

        return new Shipping($shippingData);
    }

    protected function purchaseFromArray(array $data) : Purchase
    {
        $data = $data['purchase'] ?? [];
        $result = isset($data['result'])
            ? new Result($data['result'])
            : new Result(Result::PRELIMINARY);

        return new Purchase(
            $data['amountToPay'] ?? 0,
            $data['paymentName'] ?? '',
            $data['invoiceDeliveryMethod'] ?? '',
            $data['purchaseIdentifier'] ?? '',
            $result
        );
    }

    protected function statusFromArray(array $data) : Status
    {
        return new Status($data['status'] ?? Status(Status::INITIALIZED));
    }

    protected function customerFromArray(array $data)
    {
        $customerType = $data['customerType'] ?? false;

        if (!$customerType) {
            return null;
        }

        if ($customerType === AbstractCustomer::BUSINESS_CUSTOMER) {
            $customerData       = $data['businessCustomer'] ?? [];

            $companyName        = $customerData['companyName'] ?? '';
            $orgNumber          = $customerData['organizationNumber'] ?? '';
            $invoiceReference   = $customerData['invoiceReference'] ?? '';
            $invoiceTag         = $customerData['invoiceTag'] ?? '';
            $email              = $customerData['email'] ?? '';
            $firstName          = $customerData['firstName'] ?? '';
            $lastName           = $customerData['lastName'] ?? '';
            $mobilePhoneNumber  = $customerData['mobilePhoneNumber'] ?? '';
            $invoiceAddress     = $this->businessAddressFromArray($customerData['invoiceAddress'] ?? []);
            $deliveryAddress    = $this->businessAddressFromArray($customerData['deliveryAddress'] ?? []);

            return new \Webbhuset\CollectorCheckoutSDK\Checkout\Customer\BusinessCustomer(
                $companyName,
                $orgNumber,
                $invoiceReference,
                $invoiceTag,
                $email,
                $firstName,
                $lastName,
                $mobilePhoneNumber,
                $invoiceAddress,
                $deliveryAddress
            );
        }

        $customerData                 = $data['customer'] ?? [];
        $email                        = $customerData['email'] ?? '';
        $mobilePhoneNumber            = $customerData['mobilePhoneNumber'] ?? '';
        $deliveryMobilePhoneNumber    = $customerData['deliveryContactInformation']['mobilePhoneNumber'] ?? '';
        $deliveryAddress              = $this->privateAddressFromArray($customerData['deliveryAddress'] ?? []);
        $billingAddress               = $this->privateAddressFromArray($customerData['billingAddress'] ?? []);
        $nationalIdentificationNumber = $customerData['nationalIdentificationNumber'] ?? '';

        return new PrivateCustomer(
            $email,
            $mobilePhoneNumber,
            $deliveryMobilePhoneNumber,
            $billingAddress,
            $deliveryAddress,
            $nationalIdentificationNumber
        );
    }


    protected function businessAddressFromArray(array $data)
    {
        $companyName    = $data['companyName'] ?? '';
        $address        = $data['address'] ?? '';
        $address2       = $data['address2'] ?? null;
        $coAddress      = $data['coAddress'] ?? null;
        $postalCode     = $data['postalCode'] ?? '';
        $city           = $data['city'] ?? '';
        $country        = $data['country'] ?? '';
        $firstName      = $data['firstName'] ?? '';
        $lastName       = $data['lastName'] ?? '';

        return new \Webbhuset\CollectorCheckoutSDK\Checkout\Customer\BusinessAddress(
            $companyName,
            $address,
            $postalCode,
            $city,
            $country,
            $address2,
            $coAddress,
            $firstName,
            $lastName
        );
    }

    protected function privateAddressFromArray(array $data)
    {
        $firstName      = $data['firstName'] ?? '';
        $lastName       = $data['lastName'] ?? '';
        $address        = $data['address'] ?? '';
        $address2       = $data['address2'] ?? null;
        $coAddress      = $data['coAddress'] ?? null;
        $postalCode     = $data['postalCode'] ?? '';
        $city           = $data['city'] ?? '';
        $country        = $data['country'] ?? '';

        return new \Webbhuset\CollectorCheckoutSDK\Checkout\Customer\PrivateAddress(
            $firstName,
            $lastName,
            $address,
            $postalCode,
            $city,
            $country,
            $address2,
            $coAddress
        );
    }
}
