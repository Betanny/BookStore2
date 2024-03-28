<?php
// Start session and include necessary files
session_start();
require_once '../Shared Components/dbconnection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if feedback text is provided
    if (!empty ($_POST["feedback_text"])) {
        // Get user ID from session
        if (isset ($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            // Sanitize input to prevent SQL injection
            $feedback_text = htmlspecialchars($_POST["feedback_text"]);

            try {
                // Prepare SQL statement with parameterized query
                $stmt = $db->prepare("INSERT INTO feedback (user_id, feedback_text) VALUES (:user_id, :feedback_text)");

                // Bind parameters
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':feedback_text', $feedback_text);

                // Execute SQL statement
                $stmt->execute();

                // Redirect to a success page
                header("Location: feedback_success.php");
                exit();
            } catch (PDOException $e) {
                // Handle database errors
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Handle case where user ID is not set in session
            echo "User ID not found in session.";
        }
    } else {
        // Handle case where feedback text is empty
        echo "Feedback text is required.";
    }
} else {
    // Handle case where form is not submitted
    echo "Form not submitted.";
}