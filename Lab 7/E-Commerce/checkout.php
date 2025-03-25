<?php
// checkout.php - Collect Address & Proceed to Payment

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $totalAmount = isset($_POST['totalAmount']) ? $_POST['totalAmount'] : 0;
} else {
    $totalAmount = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 50%; margin: auto; padding: 20px; border: 1px solid #ddd; }
        label { display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        .payment {
            background: #007bff;
            color: white;
            padding: 10px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        <form action="../Banking/payment.php" method="post">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="address">Delivery Address:</label>
            <textarea id="address" name="address" required></textarea>

            <label for="billing">Billing Address (if different):</label>
            <textarea id="billing" name="billing"></textarea>

            <div class="total">Total Amount: $<?php echo $totalAmount; ?></div>
            <input type="hidden" name="totalAmount" value="<?php echo $totalAmount; ?>">
            <button type="submit" class="payment">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>
