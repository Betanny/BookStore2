<?php
session_start(); // Start the session

// Include database connection file
require_once '../Shared Components/dbconnection.php';
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$book_id = $_POST['bookid'];
$quantity = 1;
$quantity = 1; // Assuming default quantity is 1
$productType = "retail";
$productCategory = "book";


try {


    $stmt = $db->prepare("INSERT INTO cart (client_id, product_id, quantity, product_type, product_category) VALUES (:client_id, :product_id, :quantity, :product_type, :product_category)");
    $stmt->bindParam(':client_id', $user_id);
    $stmt->bindParam(':product_id', $book_id);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':product_type', $productType);
    $stmt->bindParam(':product_category', $productCategory);

    $stmt->execute();

    // Check if the insertion was successful
    if ($stmt->rowCount() > 0) {
        // Book added to cart successfully
        echo "Book added to cart successfully!";
    } else {
        // Error adding book to cart
        echo "Error adding book to cart!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}