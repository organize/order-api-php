<?php

require __DIR__ . '/vendor/autoload.php';

$DB_PDO = empty(getenv('CLEARDB_DATABASE_PDO')) ? 'mysql:host=localhost;dbname=stock_api' : getenv('CLEARDB_DATABASE_PDO');
$DB_USERNAME = empty(getenv('CLEARDB_USERNAME')) ? 'root' : getenv('CLEARDB_USERNAME');
$DB_PASSWORD = empty(getenv('CLEARDB_PASSWORD')) ? 'isx' : getenv('CLEARDB_PASSWORD');

Flight::register('db', 'PDO', array($DB_PDO, $DB_USERNAME, $DB_PASSWORD));

Flight::route('POST /', function() {
    $data = array(
        'products' => json_encode(Flight::request()->data->products, true),
        'country' => Flight::request()->data->country,
        'format' => Flight::request()->data->format,
        'to_email' => Flight::request()->data->to_email,
        'email_address' => Flight::request()->data->email_address);

    if(validate_input($data))
    {
        process_order(
            new \OrderAPI\Model\OrderData($data['products'], strtoupper($data['country']), $data['format'], json_decode($data['to_email']), $data['email_address']));

    }
});

/**
 * Places an order, generates an invoice and returns invoice to user
 *
 * @param $order_data, the (validated) raw array of input given through our POST endpoint
 */
function process_order($order_data)
{
    if(validate_products($order_data->products))
    {
        $tax_base = new \OrderAPI\Taxes($order_data->country, Flight::db());
        $invoice = new \OrderAPI\Invoice($order_data->products, $tax_base, Flight::db());

        $invoice_formatted = $order_data->format === "json" ? json_encode($invoice) : invoice_as_html($invoice);

        if ($order_data->to_email) {
            \OrderAPI\Mail\Mailer::send_mail($invoice_formatted, $order_data->email_address, $order_data->format === "json");
        }
        echo $invoice_formatted;
    }
}

/**
 * Generates HTML from an Invoice instance
 *
 * @param $invoice. the Invoice instance
 * @return string, the generated HTML from given $invoice
 */
function invoice_as_html($invoice): string {
    return Flight::view()->fetch('invoice_base.php', array(
        'body_content' => \OrderAPI\Util\DataUtil::inventory_to_table_data($invoice->inventory),
        'tax_amount' => $invoice->tax_base->tax_percentage * 100,
        'total_price' => number_format($invoice->total_price, 2, ',', ' '),
        'country' => $invoice->tax_base->country));
}

/**
 * Ensures that items given in $order_data are available in stock
 *
 * @param $order_data, the OrderData instance.
 * @return bool, whether or not products in order are valid.
 */
function validate_products($order_data): bool
{
    $stmt = Flight::db()->prepare('SELECT * FROM stock WHERE product_id = ? HAVING min(product_quantity) > ?');
    foreach($order_data as $key => $value)
    {
        $stmt->execute([$key, $value]);
        if(!$stmt->fetch())
        {
            Flight::halt(400, "{failure: out of stock or invalid id for item (id = " . $key . ").}");
            return false;
        }
    }
    return true;
}

/**
 * Ensures that given input is valid, halts with a stack of errors if invalid
 *
 * @param $data, the array of input
 * @return bool, whether or not given input is valid
 */
function validate_input($data): bool
{
    $halt_data = \OrderAPI\Util\DataUtil::check_input($data);
    if(!empty($halt_data))
    {
        Flight::halt(400, '{failure: ' . implode(", ", $halt_data) . '}');
    }
    return empty($halt_data);
}

Flight::start();