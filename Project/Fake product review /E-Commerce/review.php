<?php
$productId = $_GET['product_id'] ?? '';
$productName = $_GET['product_name'] ?? '';
?>

<h2>Write a Review for <?php echo htmlspecialchars($productName); ?></h2>
<form method="post" action="submit_review.php">
    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
    <input type="hidden" name="product_name" value="<?php echo $productName; ?>">
    <textarea name="review" rows="5" cols="50" placeholder="Write your review here..."></textarea><br>
    <button type="submit">Submit Review</button>
</form>
