<?php
// display_books.php - Display Books by Category & Add to Cart
// session_start();
$isOrderPlaced = isset($_POST['orderPlaced']) ? $_POST['orderPlaced'] :'false';
$cart = isset($_POST['cart']) ? $_POST['cart'] : 'false';
if ($isOrderPlaced === 'true') {
    // 1. Check if cart is set and not empty
    if ($cart === 'true') {
        $orderHistoryFile = 'data/orderHistory.xml';

        // 2. Load or create the XML file
        if (file_exists($orderHistoryFile)) {
            $xml = simplexml_load_file($orderHistoryFile);
        } else {
            $xml = new SimpleXMLElement('<orders></orders>');
        }

        // 3. Create a new order entry
        $order = $xml->addChild('order');
        $order->addChild('timestamp', date('Y-m-d H:i:s'));

        if (file_exists("data/cart.xml")) {
            $cartXml = simplexml_load_file("data/cart.xml");
            foreach ($cartXml->item as $item) {
                $product = $order->addChild('product');
                $product->addChild('id', $item->id);
                $product->addChild('name', htmlspecialchars($item->name));
                $product->addChild('quantity', $item->quantity);
                $product->addChild('price', $item->price);
            }
            // 4. Save to XML
            $xml->asXML($orderHistoryFile);
            // 5. Clear the cart
            $xml = new SimpleXMLElement('<products><filler></filler></products>');
            $xml->asXML('data/cart.xml');
        }

        echo "<script>alert('Order placed successfully!');</script>";
    }
}

// Load products from XML file
function loadProductsByCategory($category) {
    $xml = simplexml_load_file("data/products.xml") or die("Error loading XML file");
    $products = [];
    foreach ($xml->product as $product) {
        if ((string) $product->category === $category) {
            $products[] = $product;
        }
    }
    return $products;
}

// Get cart item count
function getCartItemCount() {
    if (file_exists("data/cart.xml")) {
        $cartXml = simplexml_load_file("data/cart.xml");
        $count = 0;
        foreach ($cartXml->item as $item) {
            $count += (int) $item->quantity;
        }
        return $count;
    }
    return 0;
}

// Add product to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $cartXml = simplexml_load_file("data/cart.xml") or die("Error loading XML file");
    $exists = false;
    
    foreach ($cartXml->item as $item) {
        if ((string) $item->id === $_POST["id"]) {
            $item->quantity = (int) $item->quantity + 1;
            $exists = true;
            break;
        }
    }
    
    if (!$exists) {
        $item = $cartXml->addChild("item");
        $item->addChild("id", $_POST["id"]);
        $item->addChild("name", $_POST["name"]);
        $item->addChild("price", $_POST["price"]);
        $item->addChild("quantity", 1);
    }
    
    $cartXml->asXML("data/cart.xml");
    // echo "<script>alert('Item added to cart!'); </script>";
}

$cartItemCount = getCartItemCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 90%; margin: auto; }
        .category { font-size: 24px; font-weight: bold; margin-top: 20px; }
        .product { border: 1px solid #ddd; padding: 10px; margin: 10px; display: inline-block; width: 29%; text-align: center; }
        .product img { max-width: auto; height: 200px; }
        .cart {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .my_order {
            position: absolute;
            top: 10px;
            right: 120px;
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <a href="myOrders.php" class="my_order">My Orders</a>
    <a href="cart.php" class="cart">🛒 Cart (<?php echo $cartItemCount; ?>)</a>
    <div class="container">
        <h1>Welcome to our E-Commerce Website</h1>
        
        <?php
        $categories = ["Electronics","Clothing","Books"];
        foreach ($categories as $category) {
            echo "<div class='category'>$category</div>";
            $products = loadProductsByCategory($category);
            foreach ($products as $product) {
                echo "<div class='product'>
                        <img src='{$product->image}' alt='Product Image'>
                        <h2>{$product->name}</h2>
                        <p>\${$product->price}</p>
                        <form method='post'>
                            <input type='hidden' name='id' value='{$product->id}'>
                            <input type='hidden' name='name' value='{$product->name}'>
                            <input type='hidden' name='price' value='{$product->price}'>
                            <button type='submit' name='add_to_cart'>Add to Cart</button>
                        </form>
                      </div>";
            }
        }
        ?>
    </div>
</body>
</html>
