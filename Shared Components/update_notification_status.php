<?php
include '../Shared Components/logger.php';
require_once '../Shared Components/dbconnection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Registration/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $notification_id = intval($_POST['notification_id']);
    $user_id = $_SESSION['user_id'];

    try {
        $sql_update_status = "UPDATE notifications SET status = true WHERE notification_id = :notification_id AND recipient_id = :user_id";
        $stmt_update_status = $db->prepare($sql_update_status);
        $stmt_update_status->execute(['notification_id' => $notification_id, 'user_id' => $user_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log("PDO Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}