<?php
require_once '../Shared Components/dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the POST request
    $data = json_decode(file_get_contents('php://input'), true);
    $isbn = $data['isbn'];

    // Check if ISBN already exists in the database
    $stmt = $db->prepare("SELECT COUNT(*) FROM books WHERE isbn = ?");
    $stmt->execute([$isbn]);
    $count = $stmt->fetchColumn();

    // Return the response as JSON
    $response = array('isUnique' => $count == 0);
    header('Content-Type: application/json');
    echo json_encode($response);
}