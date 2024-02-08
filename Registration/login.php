<?php
$host = "localhost";
$port = "5432";
$dbname = "MyBookstore";
$user = "postgres";
$password = "#Wa1r1mu";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
    $db_connection = new PDO($dsn);

    // Set PDO to throw exceptions for errors
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Validate form data (basic validation for demonstration)
        if (empty($email) || empty($password)) {
            // Display error notification if email or password is empty
            echo "<script>alert('Please enter both email and password.')</script>";
        } else {
            // Check if the email and password exist in the users table
            $stmt = $db_connection->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $stmt->execute([$email, $password]);
            $user = $stmt->fetch();

            if ($user) {
                // Credentials found, redirect to login page
                // header("Location: login.php");
                echo "Login Successfull";
                exit();
            } else {
                // Display error notification if credentials are incorrect
                echo "<script>alert('Incorrect email or password. Please try again.')</script>";
            }
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}