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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // User details
        $password = $_POST["password"];

        // Check user type
        $user_type = $_POST["user_type"];
        if ($user_type === "Individual") {
            $email = $_POST["email"];
            $first_name = $_POST["fname"];
            $last_name = $_POST["lname"];
            $phone = $_POST["phone"];
            $address = $_POST["address"];
            $county = $_POST["county"];

            // Inserting data into users table using prepared statements
            $stmt_users = $db_connection->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt_users->execute([$email, $password]);

            // Retrieve the last inserted user ID
            $user_id = 'us000' . $db_connection->lastInsertId();

            // Inserting data into clients table using prepared statements
            $stmt_clients = $db_connection->prepare("INSERT INTO clients (client_type, first_name, last_name, email, phone, address, county, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_clients->execute([$user_type, $first_name, $last_name, $email, $phone, $address, $county, $user_id]);
        } elseif ($user_type === "Business") {
            $email = $_POST["OrgEmail"];
            $organization_name = $_POST["OrgName"];
            $contact_first_name = $_POST["cfname"];
            $contact_last_name = $_POST["clname"];
            $contact_email = $_POST["cemail"];
            $contact_phone = $_POST["cphone"];
            $address = $_POST["address"];
            $county = $_POST["county"];

            // Inserting data into users table using prepared statements
            $stmt_users = $db_connection->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt_users->execute([$email, $password]);

            // Retrieve the last inserted user ID
            $user_id = 'us000' . $db_connection->lastInsertId();

            // Inserting data into clients table using prepared statements
            $stmt_clients = $db_connection->prepare("INSERT INTO clients (client_type, organization_name, email, phone, address, county, user_id, contact_first_name, contact_last_name, contact_email, contact_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_clients->execute([$user_type, $organization_name, $email, $contact_phone, $address, $county, $user_id, $contact_first_name, $contact_last_name, $contact_email, $contact_phone]);
        }
        echo "New record created successfully";

    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}