<?php
session_start(); // Start the session

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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_type = $_POST["user_type"];
        $role = "Client";




        // Check user type
        if ($user_type === "Individual") {
            $email = $_POST["email"];
            $first_name = $_POST["fname"];
            $last_name = $_POST["lname"];
            $phone = $_POST["phone"];
            $address = $_POST["address"];
            $county = $_POST["county"];
            $password = hash('sha256', $_POST["password"]); // Hashing the password





            // Inserting data into users table  
            $query = "INSERT INTO users (email, password, role, category) VALUES ('$email', '$password','$role', '$user_type')";
            $db_connection->query($query);

            // Retrieve the last inserted user ID
            $user_id = $db_connection->lastInsertId();


            // Inserting data into clients table 
            $sql = "INSERT INTO clients (client_type, first_name, last_name, email, phone, address, county, user_id) VALUES ('$user_type', '$first_name', '$last_name', '$email', '$phone', '$address', '$county', '$user_id')";
            $db_connection->query($sql);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['category'] = "Individual";
            $_SESSION['role'] = "Client";

            $session_id = session_id();
            header("Location: ../Buyer/buyerdashboard.php");


        } elseif ($user_type === "Organization") {
            $email = $_POST["OrgEmail"];
            $organization_name = $_POST["OrgName"];
            $organization_phone = $_POST["OrgPhone"];
            $contact_first_name = $_POST["cfname"];
            $contact_last_name = $_POST["clname"];
            $contact_email = $_POST["cemail"];
            $contact_phone = $_POST["cphone"];
            $address = $_POST["address"];
            $county = $_POST["county"];
            $password_organization = hash('sha256', $_POST["org-password"]); // Hashing the password


            // Inserting data into users table  
            $query = "INSERT INTO users (email, password, role, category) VALUES ('$email', '$password_organization','$role', '$user_type')";
            $db_connection->query($query);

            // Retrieve the last inserted user ID
            $user_id = $db_connection->lastInsertId();

            // Inserting data into clients table  
            $sql = "INSERT INTO clients (client_type, organization_name, email, phone, address, county, user_id, contact_first_name, contact_last_name, contact_email, contact_phone) VALUES ('$user_type', '$organization_name', '$email', '$organization_phone', '$address', '$county', '$user_id', '$contact_first_name', '$contact_last_name', '$contact_email', '$contact_phone')";
            $db_connection->query($sql);
        }
        echo "New record created successfully";
        $_SESSION['user_id'] = $user_id;
        $_SESSION['category'] = "Organization";
        $_SESSION['role'] = "Dealer";
        header("Location: ../Buyer/buyerdashboard.php");
        $session_id = session_id();

        // // Redirect to buyer dashboard
        // header("Location: buyer_dashboard.php?session_id=$session_id");
        // exit();

    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}