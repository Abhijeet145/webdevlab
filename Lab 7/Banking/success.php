<?php
// success.php - Payment Success Page

$totalAmount = isset($_GET['amount']) ? $_GET['amount'] : '0.00';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .container { width: 50%; margin: auto; padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9; }
        .success-message { color: #28a745; font-size: 20px; font-weight: bold; }
        .home-button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="success-message">Payment Successful!</h1>
        <p>Thank you for your purchase.</p>
        <p>Total Amount Paid: <strong>$<?php echo htmlspecialchars($totalAmount); ?></strong></p>
        <a href="../E-Commerce/index.php" class="home-button">Return to Homepage</a>
    </div>
</body>
</html>
