<?php
$productId = $_GET['product_id'] ?? '';
$productName = $_GET['product_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Write a Review</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .review-container {
            background-color: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 1rem;
            resize: vertical;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="review-container">
    <h2>Write a Review for <?php echo htmlspecialchars($productName); ?></h2>
    <form method="post" action="submit_review.php">
        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
        <input type="hidden" name="product_name" value="<?php echo $productName; ?>">
        <textarea name="review" rows="5" placeholder="Write your review here..." required></textarea><br>
        <button type="submit">Submit Review</button>
    </form>
</div>

</body>
</html>
