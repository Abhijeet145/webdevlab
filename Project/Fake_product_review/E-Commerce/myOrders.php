<?php
$orderHistoryFile = 'data/orderHistory.xml';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; }
        .container { width: 90%; margin: auto; padding: 20px; }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .order {
            margin-bottom: 40px;
        }

        .order h2 {
            margin-top: 0;
            color: #444;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        .product {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px;
            display: inline-block;
            width: 29%;
            vertical-align: top;
            text-align: center;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .product h3 { margin: 10px 0; font-size: 18px; color: #222; }
        .product p { margin: 6px 0; font-size: 14px; }

        .review-button {
            margin-top: 10px;
            padding: 8px 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .review-button:hover {
            background: #218838;
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
                $orderNumber = 1;
                foreach ($xml->order as $index => $order) {
                    echo "<div class='order'>";
                    echo "<h2>Order #" . ($orderNumber++) . " — <small>{$order->timestamp}</small></h2>";

                    if (!isset($order->product)) {
                        echo "<p><em>No products in this order.</em></p>";
                    } else {
                        foreach ($order->product as $product) {
                            echo "<div class='product'>";
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
