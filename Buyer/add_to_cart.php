<?php
session_start(); // Start the session

// Include database connection file
require_once '../Shared Components/dbconnection.php';
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['bookid'];
$price = (int) $_POST['price'];

$quantity = 1;
$productType = "retail";
$productCategory = "book";

try {
    if (isset($_POST['quantity'])) {

        $cart_id = $_POST['cart_id'];
        $newQuantity = $_POST['quantity'];

        // Check if the item already exists in the cart
        $stmt = $db->prepare("SELECT * FROM cart WHERE cart_id = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            $bookstmt = $db->prepare("SELECT * FROM books WHERE bookid = :bookid");
            // Bind the book ID parameter
            $bookstmt->bindParam(':bookid', $book_id);

            // Execute the query
            $bookstmt->execute();
            var_dump($book_data['mininbulk']);

            // Fetch the book data
            $book_data = $bookstmt->fetch(PDO::FETCH_ASSOC);
            if ($newQuantity > $book_data['mininbulk']) {
                $discount = (($book_data['price'] * ($newQuantity)) - ($newQuantity * $book_data['priceinbulk']));
            }
            // Item already exists in the cart, update the quantity
            $stmt = $db->prepare("UPDATE cart SET quantity = :quantity, discount = :discount WHERE cart_id = :cart_id");
            $stmt->bindParam(':quantity', $newQuantity);
            $stmt->bindParam(':discount', $discount);
            var_dump($discount);
            $stmt->bindParam(':cart_id', $cart_id);
            $stmt->execute();
        }
    } else {
        $discount = 0;
        // Item doesn't exist, insert new item into the cart
        $stmt = $db->prepare("INSERT INTO cart (client_id, product_id, quantity, product_type, product_category, unit_price, discount)
VALUES (:client_id, :product_id, :quantity, :product_type, :product_category, :unit_price, :discount)");
        $stmt->bindParam(':client_id', $user_id);
        $stmt->bindParam(':product_id', $book_id);
        $stmt->bindParam(':quantity', $newQuantity);
        $stmt->bindParam(':product_type', $productType);
        $stmt->bindParam(':product_category', $productCategory);
        $stmt->bindParam(':unit_price', $price);
        $stmt->bindParam(':discount', $discount);

        $stmt->execute();
    }


    // Check if the insertion or update was successful
    if ($stmt->rowCount() > 0) {
        header('Location:/Home/products.php');
        // Book added to cart successfully or quantity updated
        echo "Operation successful!";
    } else {
        // Error adding book to cart or updating quantity
        echo "Operation failed!";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}