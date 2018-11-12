<?php

namespace OrderAPI\Model;

class Product
{
    public $price;
    public $name;
    public $description;
    public $id;
    public $quantity;

    public function __construct($id, $name, $price, $description, $quantity)
    {
        $this->price = $price;
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->quantity = $quantity;
    }
}