
<html>
<head>
    <title>Order invoice</title>
</head>
<body>
    <h1>Invoice</h1>
    <table style="width:100%">
        <tr>
            <th>Item name</th>
            <th>Item description</th>
            <th>Price per unit</th>
            <th>Quantity</th>
            <th>Item subtotal</th>
        </tr>
        <?php echo $body_content; ?>
    </table>
    <p>Total price (<?php echo $tax_amount; ?>% tax added): <?php echo $total_price; ?>$</p>
    <p>Shipping items to <?php echo $country; ?>, thank you for ordering!</p>
</body>
</html>