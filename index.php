<?php

define("VALID_COUNTRY", "/[a-zA-Z]{2,}/", true);
define("VALID_EMAIL", "", true);

require __DIR__ . '/vendor/autoload.php';

Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=stock_api','root','isx'));

Flight::route('POST /', function() {
    $data = array(
        'products' => json_encode(Flight::request()->data->products, true),
        'country' => Flight::request()->data->country,
        'format' => Flight::request()->data->format,
        'to_email' => Flight::request()->data->to_email,
        'email_address' => Flight::request()->data->email_address);

    validate_data($data);

    process_order(
        new \OrderAPI\OrderData($data['products'], strtoupper($data['country']), $data['format'], $data['to_email'], $data['email_address']));
});

function process_order($order_data)
{
    validate_products($order_data->products);
    $tax_base = new \OrderAPI\Taxes($order_data->country, Flight::db());
    $invoice = new \OrderAPI\Invoice($order_data->products, $tax_base, Flight::db());

    $invoice_formatted = format_invoice($invoice, $order_data->format);

    echo $invoice_formatted;
}

function format_invoice($invoice, $format) {
    return $format === "json" ? json_encode($invoice) : invoice_as_html($invoice);
}

function invoice_as_html($invoice) {
    $as_html = '<html><body><h1>Invoice</h1>';
    $as_html .= '<table style="width:100%"><tr><th>Item name</th><th>Item description</th> <th>Price per unit</th><th>Quantity</th><th>Item subtotal</th></tr>';
    foreach($invoice->inventory as $item) {
        $as_html .= '<tr><td>' . $item->name . '</td>';
        $as_html .= '<td>' . $item->description . '</td>';
        $as_html .= '<td>' . $item->price . '$</td>';
        $as_html .= '<td>' . $item->quantity . '</td>';
        $as_html .= '<td>' . $item->quantity * $item->price . '$</td>';
        $as_html .= '</tr>';
    }
    $as_html .= '</table>';
    $as_html .= '<p>Total price (' . $invoice->tax_base->tax_percentage * 100 .'% tax added): ' . number_format($invoice->total_price, 2, ',', ' ') . '$</p>';
    $as_html .= '<p>Shipping items to ' . $invoice->tax_base->country . ', thank you for ordering!</p>';
    $as_html .= '</body></html>';
    return $as_html;
}

function validate_products($order_data)
{
    $stmt = Flight::db()->prepare('SELECT * FROM stock WHERE product_id = ? HAVING min(product_quantity) > ?');
    foreach($order_data as $key => $value)
    {
        $stmt->execute([$key, $value]);
        if(!$stmt->fetch())
        {
            Flight::halt(400, "out of stock or invalid id for item (id = " . $key . ").");
        }
    }
}

function validate_data($data)
{
    $halt_data = array();
    if(empty($data['products']) || !is_array(json_decode($data['products'], true)))
    {
        array_push($halt_data, "parameter 'products' does not look like an array.");
    }
    if(!preg_match(VALID_COUNTRY, $data['country'])) {
        array_push($halt_data,  "suspicious or missing value for parameter 'country'.");
    }
    if($data['format'] !== "json" && $data['format'] !== "html")
    {
        array_push($halt_data,  "value of parameter 'format' is not equal to 'json' nor 'html'.");
    }
    if(boolval(json_decode($data['to_email'])) == true)
    {
        if(empty($data['email_address'])) {
            array_push($halt_data, "parameter 'email_address' cannot be empty if 'to_email' is 'true'.");
        }
    }
    if(!empty($halt_data))
    {
        Flight::halt(400, implode("<br>", $halt_data));
    }
}

Flight::start();