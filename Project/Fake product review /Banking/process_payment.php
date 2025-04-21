<?php
// process_payment.php - Validate Payment and Redirect

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardNumber = $_POST["card_number"] ?? "";
    $expiry = $_POST["expiry"] ?? "";
    $cvv = $_POST["cvv"] ?? "";
    $totalAmount = $_POST["totalAmount"] ?? 0;

    // Load card details from XML
    function loadCardDetails() {
        if (file_exists("data/cards.xml")) {
            return simplexml_load_file("data/cards.xml");
        }
        return null;
    }

    $cards = loadCardDetails();
    $isValid = false;

    if ($cards) {
        foreach ($cards->card as $card) {
            if ($card->number == $cardNumber && $card->expiry == $expiry && $card->cvv == $cvv) {
                $isValid = true;
                break;
            }
        }
    }

    if ($isValid) {
        // Redirect to success page
        header("Location: success.php?amount=$totalAmount");
        exit();
    } else {
        // Redirect back to payment page with an error message
        echo "<script>alert('Invalid card details!'); </script>";
        header("Location: ../E-commerce/cart.php?error=Invalid card details");
        exit();
    }
} else {
    header("Location: payment.php");
    exit();
}
?>
