<?php

session_start();

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
            // Fetch the hashed password and user category from the database based on the provided email
            $stmt = $db_connection->prepare("SELECT password, user_id, category FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $dbpass = $user_data['password'];
            $role = $user_data['role'];
            echo "<script>console.log($dbpass);</script>";
            echo "<script>console.log($category);</script>";



            if ($user_data) {
                // Hash the provided password using SHA256 for comparison
                $hashed_input_password = hash('sha256', $password);
                echo "<script>console.log($hashed_input_password);</script>";




                // Compare the hashed input password with the hashed password from the database
                if ($hashed_input_password === $dbpass) {

                    echo "<script>console.log($category);</script>";

                    // Credentials match, login successful

                    // Store user_id in the session
                    $_SESSION['user_id'] = $user_data['user_id'];

                    // Redirect based on user category
                    switch ($role) {
                        case 'Admin':
                            header("Location: admindashboard.php");
                            break;
                        case 'Client':
                            $session_id = session_id();
                            header("Location: ../Buyer/buyerdashboard.html");
                            break;
                        case 'Dealer':
                            // Redirect to the HTML file with session ID
                            $session_id = session_id();
                            header("Location: ../Seller/addproducts.html?session_id=$session_id");
                            break;
                        default:
                            // Handle other user categories or redirect to a generic dashboard
                            header("Location: generic_dashboard.php");
                            break;
                    }
                    echo "<script>alert('Valid login')</script>";


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