<?php
// payment.php - Payment Processing with Card Validation

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $totalAmount = isset($_POST['totalAmount']) ? $_POST['totalAmount'] : 0;
} else {
    header("Location: ../E-commerce/checkout.php");
    exit();
}

// Load card details from XML file
function loadCardDetails() {
    if (file_exists("data/cards.xml")) {
        return simplexml_load_file("data/cards.xml");
    }
    return null;
}
$cards = loadCardDetails();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 50%; margin: auto; padding: 20px; border: 1px solid #ddd; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        .pay-button {
            background: #28a745;
            color: white;
            padding: 10px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment</h1>
        <form action="process_payment.php" method="post">
            <label for="card_number">Credit Card Number:</label>
            <input type="text" id="card_number" name="card_number" required>

            <label for="expiry">Expiry Date (MM/YY):</label>
            <input type="text" id="expiry" name="expiry" required>

            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" required>

            <input type="hidden" name="totalAmount" value="<?php echo $totalAmount; ?>">
            <button type="submit" class="pay-button">Pay $<?php echo $totalAmount; ?></button>
        </form>
    </div>
</body>
</html>
