<?php
$orderHistoryFile = 'data/orderHistory.xml';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 90%; margin: auto; padding: 20px; }
        .back {
            display: inline-block;
            margin-bottom: 20px;
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .order {
            margin-bottom: 40px;
        }
        .order h2 {
            margin-top: 0;
            color: #444;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .product-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px;
            display: inline-block;
            width: 30%;
            vertical-align: top;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .product-card h3 { margin: 10px 0 5px; }
        .product-card p { margin: 5px 0; }
        .review-button {
            margin-top: 10px;
            padding: 8px 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="indexx.php" class="back">← Back to Shop</a>
        <h1>My Orders</h1>

        <?php
        if (file_exists($orderHistoryFile)) {
            $xml = simplexml_load_file($orderHistoryFile);

            if (count($xml->order) > 0) {
                foreach ($xml->order as $index => $order) {
                    echo "<div class='order'>";
                    echo "<h2>Order #" . ($order->index + 1) . " — <small>{$order->timestamp}</small></h2>";

                    if (!isset($order->product)) {
                        echo "<p><em>No products in this order.</em></p>";
                    } else {
                        foreach ($order->product as $product) {
                            echo "<div class='product-card'>";
                            echo "<h3>{$product->name}</h3>";
                            echo "<p><strong>ID:</strong> {$product->id}</p>";
                            echo "<p><strong>Quantity:</strong> {$product->quantity}</p>";
                            echo "<p><strong>Price:</strong> \${$product->price}</p>";
                            echo "<a class='review-button' href='review.php?product_id={$product->id}&product_name=" . urlencode($product->name) . "'>Write a Review</a>";
                            echo "</div>";
                        }
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>No orders placed yet.</p>";
            }
        } else {
            echo "<p>No order history found.</p>";
        }
        ?>
    </div>
</body>
</html>
