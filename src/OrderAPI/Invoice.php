<?php

namespace OrderAPI;

class Invoice
{

    public $total_price;
    public $inventory;
    public $tax_base;
    private $database;

    public function __construct($products, $tax_base, $database)
    {
        $this->inventory = $products;
        $this->tax_base = $tax_base;
        $this->database = $database;
        $this->total_price = $this->calculate_total_price();
        $this->process_inventory();
    }

    function calculate_total_price() {
        $result = 0.0;
        $stmt = $this->database->prepare('SELECT product_price FROM stock WHERE product_id = ?');

        foreach($this->inventory as $index => $quantity)
        {
            $stmt->execute([$index]);
            $unit_price = $stmt->fetchColumn();

            $unit_price_taxed = $unit_price + ($unit_price * $this->tax_base->tax_percentage);
            $result += $unit_price_taxed * $quantity;
        }

        return $result;
    }

    /**
     * Processes inventory given in constructor to a more friendly format.
     * Initially, generates Product-instances for every item, then replaces primitive inventory with an array of Product(s).
     */
    function process_inventory()
    {
        $result = array();
        $stmt = $this->database->prepare('SELECT product_title, product_price, product_description FROM stock WHERE product_id = ?');
        foreach($this->inventory as $product_index => $product_quantity) {
            $stmt->execute([$product_index]);
            $data = $stmt->fetch();
            array_push($result,
                new Model\Product($product_index, $data['product_title'], $data['product_price'], $data['product_description'], $product_quantity));
        }
        $this->inventory = $result;
    }

}