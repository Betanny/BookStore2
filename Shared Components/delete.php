<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Check if table name, primary key name, and primary key value are provided
if (isset($_GET['table']) && isset($_GET['pk_name']) && isset($_GET['pk'])) {
    // Sanitize the table name, primary key name, and primary key value
    $table = htmlspecialchars($_GET['table']);
    $pkName = htmlspecialchars($_GET['pk_name']);
    $pk = htmlspecialchars($_GET['pk']);

    // Construct the SQL DELETE statement
    $sql = "DELETE FROM $table WHERE $pkName = :pk";

    try {
        // Prepare the SQL statement
        $stmt = $db->prepare($sql);

        // Bind the primary key value
        $stmt->bindParam(':pk', $pk);

        // Execute the SQL statement
        $stmt->execute();

        // Check if any rows were affected
        $rowCount = $stmt->rowCount();

        // Send response based on deletion result
        if ($rowCount > 0) {
            // Deletion successful
            echo json_encode(['success' => true, 'message' => 'Record deleted successfully.']);
        } else {
            // No rows were affected (record not found)
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
        }
    } catch (PDOException $e) {
        // Handle PDO exception (e.g., database error)
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    // Table name, primary key name, or primary key value not provided
    echo json_encode(['success' => false, 'message' => 'Table name, primary key name, or primary key value not provided.']);
}