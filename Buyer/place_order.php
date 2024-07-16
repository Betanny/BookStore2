<?php
include '../Shared Components/logger.php';
require_once '../Shared Components/dbconnection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.php");
    exit; // Stop further execution
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['paymentMethod'] = $_POST['paymentMethod'];
    $_SESSION['paymentNumber'] = $_POST['paymentNumber'];
    $_SESSION['deliveryType'] = $_POST['deliveryType'];
    $_SESSION['shipping_address'] = $_POST['shipping_address'];
    $_SESSION['cart_items'] = $_POST['cart_items']; // Assuming this is a serialized array or JSON data

    include 'stk_initiate.php'; // Adjust path as needed

}
if (isset($_GET['transaction']) && $_GET['transaction'] == 'success') {
    try {
        // Retrieve client ID
        $sql = "SELECT client_id FROM clients WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $client_id = $stmt->fetchColumn();

        if (!$client_id) {
            throw new Exception("Client ID not found for user ID: $user_id");
        }

        // Assign form data to variables
        $status = "Pending";
        $payment_method = $_SESSION['paymentMethod'];
        $payment_number = $_SESSION['paymentNumber'];
        $delivery_option = $_SESSION['deliveryType'];
        $shipping_address = $_SESSION['shipping_address'];
        $product_type = "book";
        $transactiontype = "Purchase";

        // Prepare SQL statement to insert into orders table using named parameters
        $stmt = $db->prepare("
                INSERT INTO orders (client_id, total_amount, delivery_option, status, dealer_status, payment_method, shipping_address, product_type, product_id, unit_price, quantity, seller_id)
                VALUES (:client_id, :total_amount, :delivery_option, :status, :dealer_status, :payment_method, :shipping_address, :product_type, :product_id, :unit_price, :quantity, :seller_id)
            ");

        $cartItems = json_decode($_SESSION['cart_items'], true);

        // Iterate over each item in the cart
        foreach ($cartItems as $cartItem) {
            $product_id = $cartItem['product_id'];
            $unit_price = $cartItem['unit_price'];
            $quantity = $cartItem['quantity'];
            $total_amount = $unit_price * $quantity;
            $cart_id = $cartItem['cart_id'];
            $seller_id = $cartItem['seller_id'];

            // Bind parameters
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':total_amount', $total_amount);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':dealer_status', $status); // Assuming dealer_status is same as status
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':delivery_option', $delivery_option);
            $stmt->bindParam(':shipping_address', $shipping_address);
            $stmt->bindParam(':product_type', $product_type);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':unit_price', $unit_price);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':seller_id', $seller_id); // Adjust as needed

            // Execute the statement
            if ($stmt->execute()) {
                $order_id = $db->lastInsertId();

                // Prepare SQL statement to insert into transactions table
                $stmt_transaction = $db->prepare("
                        INSERT INTO transactions (client_id, amount, transaction_type, payment_method, payment_number, order_id)
                        VALUES (:client_id, :amount, :transaction_type, :payment_method, :payment_number, :order_id)
                    ");

                // Bind parameters for the transaction insertion
                $stmt_transaction->bindParam(':client_id', $client_id);
                $stmt_transaction->bindParam(':amount', $total_amount);
                $stmt_transaction->bindParam(':transaction_type', $transactiontype);
                $stmt_transaction->bindParam(':payment_method', $payment_method);
                $stmt_transaction->bindParam(':payment_number', $payment_number);
                $stmt_transaction->bindParam(':order_id', $order_id);

                // Execute the transaction insertion
                if ($stmt_transaction->execute()) {
                    // Delete cart item after successful order placement
                    $stmt_delete_cart = $db->prepare("DELETE FROM cart WHERE cart_id = :cart_id");
                    $stmt_delete_cart->bindParam(':cart_id', $cart_id);
                    $stmt_delete_cart->execute();

                    writeLog($db, "User placed an order for product ID $product_id", "INFO", $user_id);
                    echo "Order for product ID $product_id placed successfully.<br>";
                } else {
                    echo "Error processing transaction.<br>";
                    writeLog($db, "Error processing transaction for product ID $product_id", "ERROR", $user_id);
                }
            } else {
                echo "Error placing order for product ID $product_id.<br>";
                writeLog($db, "Error placing order for product ID $product_id", "ERROR", $user_id);
            }
        }

        // Redirect to a success page after processing all orders
        header('Location: /Buyer/CheckOut.php');
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        writeLog($db, "Error: " . $e->getMessage(), "ERROR", $user_id);
    }
}