<?php
// Include database connection file
require_once 'dbconnection.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get user ID and category from session
$user_id = $_SESSION['user_id'];
$category = $_SESSION['category'];

try {
    // Determine which table to query based on user category
    $table_name = '';
    switch ($category) {
        case 'Authors':
            $table_name = 'authors';
            break;
        case 'Publishers':
            $table_name = 'publishers';
            break;
        case 'Manufacturers':
            $table_name = 'manufacturers';
            break;
        // Add more cases as needed
    }

    // Query the appropriate table to fetch data
    $stmt = $db->prepare("SELECT * FROM $table_name WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display data on the dashboard
    foreach ($data as $row) {
        // Display data as needed
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}