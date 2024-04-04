<?php
session_start(); // Start the session

// Include database connection file
require_once '../Shared Components/dbconnection.php';
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
// Check if the request method is POST

// Check if the user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Check if the bookid is provided in the POST data
    if (isset($_POST['bookid'])) {
        // Sanitize and validate the bookid
        $bookid = filter_input(INPUT_POST, 'bookid', FILTER_SANITIZE_NUMBER_INT);
        // Validate bookid (you may want to add more validation here)
        if (!empty($bookid) && is_numeric($bookid)) {
            // Prepare and execute the SQL statement to insert the book into the cart
            $userId = $_SESSION['user_id']; // Assuming you have a user_id stored in the session
            $quantity = 1; // Assuming default quantity is 1
            $productType = 'Retail'; // Assuming default product type is 'Retail'

            $stmt = $db->prepare("INSERT INTO public.cart (client_id, product_id, quantity, product_type) VALUES (:client_id, :product_id, :quantity, :product_type)");
            $stmt->bindParam(':client_id', $userId);
            $stmt->bindParam(':product_id', $bookid);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':product_type', $productType);
            $stmt->execute();

            // Check if the insertion was successful
            if ($stmt->rowCount() > 0) {
                // Book added to cart successfully
                echo "Book added to cart successfully!";
            } else {
                // Error adding book to cart
                echo "Error adding book to cart!";
            }
        } else {
            // Invalid bookid
            echo "Invalid book ID!";
        }
    } else {
        // Bookid not provided
        echo "Book ID not provided!";
    }
} else {
    // Invalid request method
    echo "Invalid request method!";
}