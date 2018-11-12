<?php

namespace OrderAPI;

define("DEFAULT_TAX", 0.2);

class Taxes
{

    public $country;
    public $tax_percentage;
    private $database;

    public function __construct($country, $database)
    {
        $this->country = $country;
        $this->database = $database;
        $this->tax_percentage = $this->get_tax_base();
    }

    public function get_tax_base()
    {
        $stmt = $this->database->prepare('SELECT tax FROM tax_base WHERE upper(country_name) = ?');
        $stmt->execute([$this->country]);
        $result = $stmt->fetchColumn();
        return $result ? $result : DEFAULT_TAX;
    }

}