<?php
require_once '../Shared Components/dbconnection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Registration/login.html");
    exit(); // Ensure script termination after redirection
}

$user_id = $_SESSION['user_id'];

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        var_dump($_POST);
        // Retrieve form data
        $shippingAddress = $_POST['DeliveryAddress'];
        $paymentMethod = $_POST['paymentMethod'];
        var_dump($paymentMethod);


        // Fetch cart data with product details from the database
        $cartdatastmt = $db->prepare("
        SELECT c.*, b.seller_id
        FROM cart c
        INNER JOIN books b ON c.product_id = b.bookid
        WHERE c.client_id = :client_id
    ");

        $cartdatastmt->bindParam(':client_id', $user_id);
        $cartdatastmt->execute();
        $cartItems = $cartdatastmt->fetchAll(PDO::FETCH_ASSOC);
        var_dump($user_id);



        // var_dump($cartItems);

        // Start transaction
        // $db->beginTransaction();
        $status = "Pending";
        var_dump($status);


        foreach ($cartItems as $item) {
            // Calculate total amount for each item
            $totalAmount = $item['quantity'] * $item['unit_price'];
            echo "Total amout";
            var_dump($totalAmount);


            // Insert order details into the orders table
            //     $stmt = $db->prepare("
            //     INSERT INTO orders (client_id, total_amount, status, payment_method, shipping_address, product_type, product_id, unit_price, quantity, seller_id)
            //     VALUES (:client_id, :total_amount, :status, :payment_method, :shipping_address, :product_type, :product_id, :unit_price, :quantity, :seller_id)
            // ");
            //     $stmt->bindParam(':client_id', $user_id);
            //     $stmt->bindParam(':total_amount', $totalAmount);
            //     $stmt->bindParam(':status', $status);
            //     $stmt->bindParam(':payment_method', $paymentMethod);
            //     $stmt->bindParam(':shipping_address', $shippingAddress);
            //     $stmt->bindValue(':product_type', 'book'); // Assuming product_type is a fixed value
            //     $stmt->bindParam(':product_id', $item['product_id']);
            //     $stmt->bindParam(':unit_price', $item['unit_price']);
            //     $stmt->bindParam(':quantity', $item['quantity']);
            //     $stmt->bindParam(':seller_id', $item['seller_id']);
            //     var_dump($item['seller_id']);

            //     $stmt->execute();
            var_dump($item['seller_id']);
            var_dump($item['quantity']);



            // $query = "INSERT INTO orders (client_id, total_amount, status, payment_method, shipping_address, product_type, product_id, unit_price, quantity, seller_id) 
            //   VALUES ('$user_id', '$totalAmount', '$status', '$paymentMethod', '$shippingAddress', 'book', '{$item['product_id']}', '{$item['unit_price']}', '{$item['quantity']}', '{$item['seller_id']}')";
            // $db->query($query);
            // Insert order details into the orders table
            $query = "INSERT INTO orders (client_id, total_amount, status, payment_method, shipping_address, product_type, product_id, unit_price, quantity, seller_id) 
            VALUES (:user_id, :totalAmount, 'Pending', :paymentMethod, :shippingAddress, 'book', :product_id, :unit_price, :quantity, :seller_id)";
            $orderstmt = $db->prepare($query);

            $params = array(
                ':user_id' => $user_id,
                ':totalAmount' => $totalAmount,
                ':paymentMethod' => $paymentMethod,
                ':shippingAddress' => $shippingAddress,
                ':product_id' => $item['product_id'],
                ':unit_price' => $item['unit_price'],
                ':quantity' => $item['quantity'],
                ':seller_id' => $item['seller_id']
            );

            echo $orderstmt->queryString; // Output the prepared SQL statement
            $db->query($query);
            var_dump($item['unit_price']);

            $result = $orderstmt->execute($params);
            var_dump($item['unit_price']);
            echo $orderstmt->queryString; // Output the prepared SQL statement


            if (!$result) {
                // If the query failed, log the error message
                $error = $db->errorInfo();
                error_log("Error: " . $error[2]);
                // You might want to handle the error or redirect to an error page here
            }



            var_dump($item['seller_id']);


            // Retrieve the order ID of the newly inserted order
            $orderId = $db->lastInsertId();
            var_dump($orderId);


            // Insert transaction details into the transactions table
            $stmt = $db->prepare("
                    INSERT INTO transactions (client_id, amount, transaction_type, payment_method, order_id)
                    VALUES (:client_id, :amount, 'Order', :payment_method, :order_id)
                ");
            $stmt->bindParam(':client_id', $user_id);
            $stmt->bindParam(':amount', $totalAmount);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
        }

        // Remove the order from the cart
        $stmt = $db->prepare("DELETE FROM cart WHERE client_id = :client_id");
        $stmt->bindParam(':client_id', $user_id);
        $stmt->execute();

        // Commit transaction
        $db->commit();

        // Redirect to a success page or perform further actions after successful order submission
        // Example: header("Location: order_success.php");

    } else {
        // If the request method is not POST, redirect to an error page or display an error message
        // Example: header("Location: order_error.php");
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    // Redirect to an error page
    // header("Location: review_error.php");
}