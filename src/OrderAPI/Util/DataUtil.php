<?php

namespace OrderAPI\Util;

define("VALID_COUNTRY", "/[a-zA-Z]{2,}/", true);

class DataUtil
{

    /**
     * Creates table data for items in an inventory
     *
     * @param $inventory, the inventory in an Invoice
     * @return string, the generated table data
     */
    public static function inventory_to_table_data($inventory): string {
        $result = '';
        foreach($inventory as $item)
        {
            $result .= '<tr><td>' . $item->name . '</td>';
            $result .= '<td>' . $item->description . '</td>';
            $result .= '<td>' . $item->price . '$</td>';
            $result .= '<td>' . $item->quantity . '</td>';
            $result .= '<td>' . $item->quantity * $item->price . '$</td>';
            $result .= '</tr>';
        }
        return $result;
    }

    /**
     * Ensures that raw input is valid
     *
     * @param $data, the raw input from our POST endpoint
     * @return array, the array containing (potential) error output
     */
    public static function check_input($data)
    {
        $halt_data = array();
        if (empty($data['products']) || !is_array(json_decode($data['products'], true)))
        {
            array_push($halt_data, "{parameter 'products' does not look like an array.}");
        } else {
            $products_raw = json_decode($data['products'], true);
            foreach($products_raw as $product) {
                if(empty($product['id']) || empty($product['quantity']))
                {
                    array_push($halt_data, "{parameter 'products' is malformed.}");
                }
            }
        }
        if (!preg_match(VALID_COUNTRY, $data['country']))
        {
            array_push($halt_data, "{suspicious or missing value for parameter 'country'.}");
        }
        if ($data['format'] !== "json" && $data['format'] !== "html")
        {
            array_push($halt_data, "{value of parameter 'format' is not equal to 'json' nor 'html'.}");
        }
        if (boolval(json_decode($data['to_email'])) == true)
        {
            if (empty($data['email_address']))
            {
                array_push($halt_data, "{parameter 'email_address' cannot be empty if 'to_email' is 'true'.}");
            }
        }
        return $halt_data;
    }
}