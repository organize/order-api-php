<?php

namespace OrderAPI;

class OrderData
{
    public $products;
    public $country;
    public $format;
    public $to_email;
    public $email_address;

    public $totalPrice;

    public function __construct($products, $country, $format, $to_email, $email_address)
    {
        $this->products = json_decode($products, true);
        $this->country = $country;
        $this->format = $format;
        $this->to_email = $to_email;
        $this->email_address = $email_address;
        $this->process_product_format();
    }

    private function process_product_format()
    {
        $result = array();
        foreach($this->products as $product) {
            $result[$product['id']] = $product['quantity'];
        }
        $this->products = $result;
    }
}