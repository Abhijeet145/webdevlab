<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $reviewText = $_POST['review'];

    // Step 1: Call API (replace with actual endpoint and method)
    $apiResponse = [
        'sentiment' => 'positive', // Example placeholder
        'is_fake' => 'no'
    ];

    // Step 2: Save in data/reviews.xml
    $reviewFile = 'data/reviews.xml';
    if (file_exists($reviewFile)) {
        $xml = simplexml_load_file($reviewFile);
    } else {
        $xml = new SimpleXMLElement('<reviews></reviews>');
    }

    $review = $xml->addChild('review');
    $review->addChild('product_id', $productId);
    $review->addChild('product_name', htmlspecialchars($productName));
    $review->addChild('text', htmlspecialchars($reviewText));
    $review->addChild('sentiment', $apiResponse['sentiment']);
    $review->addChild('is_fake', $apiResponse['is_fake']);
    $review->addChild('timestamp', date('Y-m-d H:i:s'));

    $xml->asXML($reviewFile);

    echo "<script>alert('Review submitted successfully!'); window.location.href = 'myOrders.php';</script>";
}
?>
