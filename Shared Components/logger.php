<?php

// Include the database connection

// Function to log events
function writeLog($db_connection, $message, $log_level, $user_id = null)
{
    try {
        // Insert log entry into database
        $query = "INSERT INTO logs (user_id, log_level, message) VALUES (:user_id, :log_level, :message)";
        $stmt = $db_connection->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':log_level' => $log_level,
            ':message' => $message
        ]);
    } catch (PDOException $e) {
        error_log("Failed to write log to database: " . $e->getMessage());
    }

    // Optionally, log to a file as well
    $log_file = __DIR__ . '/app_logs.log'; // Change the path if necessary
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$log_level] [User ID: " . ($user_id ?? 'N/A') . "] $message" . PHP_EOL;

    try {
        if (!file_put_contents($log_file, $log_entry, FILE_APPEND)) {
            error_log("Failed to write log to file: $log_file");
        }
    } catch (Exception $e) {
        error_log("Exception caught while writing log to file: " . $e->getMessage());
    }
}