<?php
require_once '../Shared Components/dbconnection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.html");
    exit; // Stop further execution
}
$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT client_id FROM clients WHERE user_id = $user_id";
    // Execute the query and fetch the results
    $client_id_stmt = $db->query($sql);
    $client_id_result = $client_id_stmt->fetch(PDO::FETCH_ASSOC);
    $client_id = $client_id_result['client_id'];



    // Assign form data to variables
    $status = "Pending"; // status is always pending
    $payment_method = $_POST['paymentMethod'];
    $payment_number = $_POST['paymentNumber'];
    var_dump($payment_number);
    $shipping_address = $_POST['shipping_address'];
    $product_type = "book"; // product_type is always book
    $transactiontype = "Purchase";

    // Prepare SQL statement to insert into orders table using named parameters
    $stmt = $db->prepare("
        INSERT INTO orders (client_id, total_amount, status,dealer_status, payment_method, shipping_address, product_type, product_id, unit_price, quantity, seller_id)
        VALUES (:client_id, :total_amount, :status,:dealer_status, :payment_method, :shipping_address, :product_type, :product_id, :unit_price, :quantity, :seller_id)
    ");
    $cartItems = json_decode($_POST['cart_items'], true);

    // Iterate over each item in the cart
    foreach ($cartItems as $cartItem) {
        $product_id = $cartItem['product_id'];
        $unit_price = $cartItem['unit_price'];
        $quantity = $cartItem['quantity'];
        $total_amount = $unit_price * $quantity;
        $cart_id = $cartItem['cart_id'];
        $seller_id = $cartItem['seller_id'];

        var_dump($cart_id);


        // Assuming you have a way to determine seller_id for each product_id, otherwise adjust this accordingly

        // Bind parameters
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':total_amount', $total_amount);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':dealer_status', $status);

        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':shipping_address', $shipping_address);
        $stmt->bindParam(':product_type', $product_type);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':unit_price', $unit_price);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':seller_id', $seller_id); // Adjust this accordingly


        // Execute the statement
        if ($stmt->execute()) {
            echo "Order for product ID $product_id placed successfully.<br>";
            $order_id = $db->lastInsertId();

            // Prepare SQL statement to insert into transactions table
            $stmt_transaction = $db->prepare("
                INSERT INTO transactions (client_id, amount, transaction_type, payment_method,payment_number,order_id)
                VALUES (:client_id, :amount, :transaction_type, :payment_method, :payment_number,:order_id)
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
                echo "Transaction completed successfully.<br>";
                $stmt_delete_cart = $db->prepare("
            DELETE FROM cart WHERE cart_id = :cart_id 
        ");
                $stmt_delete_cart->bindParam(':cart_id', $cart_id);
                $stmt_delete_cart->execute();
            } else {
                echo "Error processing transaction.<br>";
            }

        } else {
            echo "Error placing order for product ID $product_id.<br>";
        }
    }
    header('Location:/Buyer/CheckOut.php');


} else {
    echo "Invalid request.";
}