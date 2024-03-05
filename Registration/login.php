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
            // Fetch the hashed password from the database based on the provided email
            $stmt = $db_connection->prepare("SELECT password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $hashed_password = $stmt->fetchColumn();

            if ($hashed_password) {
                // Hash the provided password using SHA256 for comparison
                $hashed_input_password = hash('sha256', $password);

                // Compare the hashed input password with the hashed password from the database
                if ($hashed_input_password === $hashed_password) {
                    // Credentials match, login successful
                    echo "Login Successful";
                    exit();
                } else {
                    // Display error notification if credentials are incorrect
                    echo "<script>alert('Incorrect email or password. Please try again.')</script>";
                }
            } else {
                // Display error notification if user with the provided email does not exist
                echo "<script>alert('User with the provided email does not exist.')</script>";
            }
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}