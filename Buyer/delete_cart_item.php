<?php
include '../Shared Components\logger.php';

require_once '../Shared Components/dbconnection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cartId'])) {
    $cartId = $_POST['cartId'];

    try {
        // Prepare and execute the SQL query to delete the item from the cart table
        $stmt = $db->prepare("DELETE FROM cart WHERE cart_id = :cart_id AND client_id = :client_id");
        $stmt->bindParam(':cart_id', $cartId);
        $stmt->bindParam(':client_id', $_SESSION['user_id']);
        $stmt->execute();

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            writeLog($db, "User deleted item from the cart ", "INFO", $user_id);

            // If deletion is successful, send a success response
            http_response_code(200); // OK status code
        } else {
            // If no rows were affected, send a not found response
            http_response_code(404); // Not found status code
        }
    } catch (PDOException $e) {
        // If an error occurs, send an internal server error response
        http_response_code(500); // Internal server error status code
        error_log("Error deleting item from cart: " . $e->getMessage());
    }
} else {
    // If the request method is not POST or the cartId parameter is not set, send a bad request response
    http_response_code(400); // Bad request status code
}