<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $reviewText = $_POST['review'];

    //Call Python Flask API
    $apiUrl = 'http://127.0.0.1:5000/analyze-review'; // Your local Python server
    $data = ['text' => $reviewText];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $apiResponse = json_decode($response, true);

    $sentiment = $apiResponse['sentiment'] ?? 'unknown';
    $isFake = $apiResponse['confidence'] ?? 'unknown';
    $isFake = $isFake < 0.96 ? 'yes' : 'no';

    // Save in data/reviews.xml
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
    $review->addChild('sentiment', $sentiment);
    $review->addChild('is_fake', $isFake);
    $review->addChild('timestamp', date('Y-m-d H:i:s'));

    $xml->asXML($reviewFile);

    echo "<script>alert('Review submitted successfully!'); window.location.href = 'myOrders.php';</script>";
}
?>
