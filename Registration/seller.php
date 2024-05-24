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
        $role = "Dealer";

        //Author
        if ($user_type === "Author") {
            $fname = $_POST["fname"];
            $lname = $_POST["lname"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $username = $_POST["username"];
            $gender = $_POST["gender"];
            $nationality = $_POST["nationality"];
            $author_address = $_POST["address"]; // Renamed to avoid conflict
            $biography = $_POST["biography"];
            $website = $_POST["website"];
            $password = hash('sha256', $_POST["password"]); // Hashing the password
            $instagram = $_POST["instagram"];
            $facebook = $_POST["facebook"];

            // Combinining Instagram and Facebook values into a single string 
            $social_media_handles = implode(', ', array_filter([$instagram, $facebook]));

            // Inserting data into users table
            $sql = "INSERT INTO users (email, password ,role,category) VALUES ('$email', '$password','$role','$user_type')";
            $db_connection->query($sql);
            $user_id = $db_connection->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['category'] = "Author";



            // Inserting data into the authors table
            $sql = "INSERT INTO authors (first_name, last_name, email, phone, username, gender, nationality, address, biography, website, socialmedia_handles, user_id) VALUES ('$fname', '$lname', '$email', '$phone', '$username', '$gender', '$nationality', '$author_address', '$biography', '$website', '$social_media_handles', '$user_id')";
            $db_connection->query($sql);
        }
        //PUBLISHER
        elseif ($user_type === "Publisher") {
            $publisher_name = $_POST["OrgName"];
            $contact_first_name = $_POST["cfname"];
            $contact_last_name = $_POST["clname"];
            $contact_email = $_POST["cemail"];
            $contact_phone = $_POST["cphone"];
            $publisher_email = $_POST["OrgEmail"];
            $publisher_phone = $_POST["OrgPhone"];
            $publisher_address = $_POST["address1"]; // Make sure the address input has a unique name
            $publisher_website = $_POST["website1"];
            $publisher_password = hash('sha256', $_POST["org-password"]);

            // Inserting data into users table
            $sql = "INSERT INTO users (email, password ,role,category) VALUES ('$publisher_email', '$publisher_password','$role','$user_type')";
            $db_connection->query($sql);
            $user_id = $db_connection->lastInsertId();

            $_SESSION['user_id'] = $user_id;
            $_SESSION['category'] = "Publisher";



            // Inserting data into publishers table
            $sql = "INSERT INTO publishers (publisher_name, contact_first_name, contact_last_name, contact_email, contact_phone, publisher_email, publisher_phone, address, website, user_id) VALUES ('$publisher_name', '$contact_first_name', '$contact_last_name', '$contact_email', '$contact_phone', '$publisher_email', '$publisher_phone', '$publisher_address', '$publisher_website', '$user_id')";
            $db_connection->query($sql);
        }
        //Manufacturer
        elseif ($user_type === "Manufacturer") {
            $manufacturer_name = $_POST['OrgName'];
            $contact_first_name = $_POST['cfname'];
            $contact_last_name = $_POST['clname'];
            $contact_email = $_POST['cemail'];
            $contact_phone = $_POST['cphone'];
            $manufacturer_email = $_POST['OrgEmail'];
            $manufacturer_phone = $_POST['OrgPhone'];
            $address = $_POST['address1'];
            $website = $_POST['website1'];
            $manufacturer_password = hash('sha256', $_POST["org-password"]);
            $products_offered = isset($_POST['products']) ? $_POST['products'] : array();
            $products_offered_string = '{' . implode(",", $products_offered) . '}';

            // Inserting data into users table
            $sql = "INSERT INTO users (email, password,role,category) VALUES ('$manufacturer_email', '$manufacturer_password','$role','$user_type')";
            $db_connection->query($sql);
            $user_id = $db_connection->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['category'] = "Manufacturer";



            // Inserting data into manufacturers table
            $sql = "INSERT INTO manufacturers (manufacturer_name, contact_first_name, contact_last_name, contact_email, contact_phone, manufacturer_email, manufacturer_phone, address, website, products_offered, user_id) VALUES ('$manufacturer_name', '$contact_first_name', '$contact_last_name', '$contact_email', '$contact_phone', '$manufacturer_email', '$manufacturer_phone', '$address', '$website', '$products_offered_string', '$user_id')";
            $db_connection->query($sql);
        }
        $_SESSION['role'] = "Dealer";
        echo "New record created successfully";
        $session_id = session_id();

        // Redirect to buyer dashboard
        header("Location: ../Seller/dealeragreement.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}