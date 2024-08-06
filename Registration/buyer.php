<?php
include '../Shared Components\logger.php';

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

            // Check if email already exists
            $email_check_query = $db_connection->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $email_check_query->bindParam(':email', $email);
            $email_check_query->execute();
            $email_exists = $email_check_query->fetchColumn();

            if ($email_exists) {
                echo "Email already exists. Please use a different email.";
                exit();
            }

            // Inserting data into users table  
            $query = "INSERT INTO users (email, password, role, category) VALUES (:email, :password, :role, :user_type)";
            $stmt = $db_connection->prepare($query);
            $stmt->execute([':email' => $email, ':password' => $password, ':role' => $role, ':user_type' => $user_type]);

            // Retrieve the last inserted user ID
            $user_id = $db_connection->lastInsertId();

            // Inserting data into clients table 
            $sql = "INSERT INTO clients (client_type, first_name, last_name, email, phone, address, county, user_id) VALUES (:user_type, :first_name, :last_name, :email, :phone, :address, :county, :user_id)";
            $stmt = $db_connection->prepare($sql);
            $stmt->execute([':user_type' => $user_type, ':first_name' => $first_name, ':last_name' => $last_name, ':email' => $email, ':phone' => $phone, ':address' => $address, ':county' => $county, ':user_id' => $user_id]);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['category'] = "Individual";
            $_SESSION['role'] = "Client";
            writeLog($db_connection, "User has registered as a client", "INFO", $user_id);

            header("Location: ../Buyer/buyerdashboard.php");
            exit();

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

            // Check if email already exists
            $email_check_query = $db_connection->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $email_check_query->bindParam(':email', $email);
            $email_check_query->execute();
            $email_exists = $email_check_query->fetchColumn();

            if ($email_exists) {
                echo "Email already exists. Please use a different email.";
                exit();
            }

            // Inserting data into users table  
            $query = "INSERT INTO users (email, password, role, category) VALUES (:email, :password, :role, :user_type)";
            $stmt = $db_connection->prepare($query);
            $stmt->execute([':email' => $email, ':password' => $password_organization, ':role' => $role, ':user_type' => $user_type]);

            // Retrieve the last inserted user ID
            $user_id = $db_connection->lastInsertId();

            // Inserting data into clients table  
            $sql = "INSERT INTO clients (client_type, organization_name, email, phone, address, county, user_id, contact_first_name, contact_last_name, contact_email, contact_phone) VALUES (:user_type, :organization_name, :email, :organization_phone, :address, :county, :user_id, :contact_first_name, :contact_last_name, :contact_email, :contact_phone)";
            $stmt = $db_connection->prepare($sql);
            $stmt->execute([':user_type' => $user_type, ':organization_name' => $organization_name, ':email' => $email, ':organization_phone' => $organization_phone, ':address' => $address, ':county' => $county, ':user_id' => $user_id, ':contact_first_name' => $contact_first_name, ':contact_last_name' => $contact_last_name, ':contact_email' => $contact_email, ':contact_phone' => $contact_phone]);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['category'] = "Organization";
            $_SESSION['role'] = "Client";
            writeLog($db_connection, "User has registered as a client", "INFO", $user_id);

            header("Location: ../Buyer/buyerdashboard.php");
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}