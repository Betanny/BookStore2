<?php
require_once '../Shared Components/dbconnection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Registration/login.html");
    exit(); // Exit to prevent further execution
}
$clientId = $_SESSION['user_id'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $paymentMethod = $_POST['paymentMethod']; // Retrieve payment method from the form
    $shippingAddress = $_POST['shippingAddress']; // Retrieve shipping address from the form

    // Fetch cart data with product details from the database
    $cartdatastmt = $db->prepare("
        SELECT c.*, b.product_type, b.unit_price, b.seller_id
        FROM cart c
        JOIN books b ON c.product_id = b.bookid
        WHERE c.client_id = :client_id
    ");
    $cartdatastmt->bindParam(':client_id', $clientId);
    $cartdatastmt->execute();
    $cartItems = $cartdatastmt->fetchAll(PDO::FETCH_ASSOC);

    // Start transaction
    $db->beginTransaction();
    $status = "Pending";
    try {
        foreach ($cartItems as $item) {
            // Calculate total amount for each item
            $totalAmount = $item['quantity'] * $item['unit_price'];

            // Insert order details into the orders table
            $stmt = $db->prepare("
                INSERT INTO orders (client_id, total_amount, status, payment_method, shipping_address, product_type, product_id, unit_price, quantity, seller_id)
                VALUES (:client_id, :total_amount, :status, :payment_method, :shipping_address, :product_type, :product_id, :unit_price, :quantity, :seller_id)
            ");
            $stmt->bindParam(':client_id', $clientId);
            $stmt->bindParam(':total_amount', $totalAmount);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':shipping_address', $shippingAddress);
            $stmt->bindParam(':product_type', $item['product_type']);
            $stmt->bindParam(':product_id', $item['product_id']);
            $stmt->bindParam(':unit_price', $item['unit_price']);
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->bindParam(':seller_id', $item['seller_id']);
            $stmt->execute();

            // Retrieve the order ID of the newly inserted order
            $orderId = $db->lastInsertId();

            // Insert transaction details into the transactions table
            $stmt = $db->prepare("
                INSERT INTO transactions (client_id, amount, transaction_type, payment_method, order_id)
                VALUES (:client_id, :amount, 'Order', :payment_method, :order_id)
            ");
            $stmt->bindParam(':client_id', $clientId);
            $stmt->bindParam(':amount', $totalAmount);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
        }

        // Remove the order from the cart
        $stmt = $db->prepare("DELETE FROM cart WHERE client_id = :client_id");
        $stmt->bindParam(':client_id', $clientId);
        $stmt->execute();

        // Commit transaction
        $db->commit();

        // Redirect to a success page or perform further actions after successful order submission
        // Example: header("Location: order_success.php");
    } catch (PDOException $e) {
        // Rollback transaction if an error occurs
        $db->rollBack();
        // Handle errors if order submission fails
        error_log("Error submitting order: " . $e->getMessage());
        // Redirect to an error page or display an error message
        // Example: header("Location: order_error.php");
    }
} else {
    // If the request method is not POST, redirect to an error page or display an error message
    // Example: header("Location: order_error.php");
}