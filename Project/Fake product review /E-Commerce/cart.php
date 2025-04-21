<?php
// cart.php - Display Cart Items & Proceed to Checkout

// Load cart items from XML file
function loadCartItems() {
    if (file_exists("data/cart.xml")) {
        $cartXml = simplexml_load_file("data/cart.xml");
        return $cartXml;
    }
    return null;
}

$cartItems = loadCartItems();
$totalAmount = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: auto; }
        .cart-item { border: 1px solid #ddd; padding: 10px; margin: 10px; display: flex; justify-content: space-between; align-items: center; }
        .cart-item img { max-width: 50px; height: auto; }
        .checkout {
            background: #28a745;
            color: white;
            padding: 10px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .total {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Shopping Cart</h1>
        
        <?php if ($cartItems && count($cartItems->item) > 0): ?>
            <?php foreach ($cartItems->item as $item): ?>
                <div class="cart-item">
                    <span><?php echo $item->name; ?> (x<?php echo $item->quantity; ?>)</span>
                    <span>$<?php echo $item->price * $item->quantity; ?></span>
                </div>
                <?php $totalAmount += $item->price * $item->quantity; ?>
            <?php endforeach; ?>
            <div class="total">Total: $<?php echo $totalAmount; ?></div>
            <form action="checkout.php" method="post">
                <input type="hidden" name="totalAmount" value="<?php echo $totalAmount; ?>">
                <button type="submit" class="checkout">Proceed to Checkout</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
