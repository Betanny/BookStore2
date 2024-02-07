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
        $user_type = $_POST["user_type"];

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
            $password = $_POST["password"];
            $instagram = $_POST["instagram"];
            $facebook = $_POST["facebook"];

            // Combine Instagram and Facebook values into a single string separated by comma
            $social_media_handles = implode(', ', array_filter([$instagram, $facebook]));
            // Inserting data into users table using prepared statements
            $stmt_users = $db_connection->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt_users->execute([$email, $password]);
            $user_id = $db_connection->lastInsertId();

            // Insert data into the authors table using prepared statements
            $stmt = $db_connection->prepare("INSERT INTO authors (first_name, last_name, email, phone, username, gender, nationality, address, biography, website, socialmedia_handles, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fname, $lname, $email, $phone, $username, $gender, $nationality, $author_address, $biography, $website, $social_media_handles, $user_id]);
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
            $publisher_password = $_POST["password"];




            $stmt_users = $db_connection->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt_users->execute([$publisher_email, $publisher_password]);
            $user_id = $db_connection->lastInsertId();


            // Insert data into publishers table using prepared statements
            $stmt = $db_connection->prepare("INSERT INTO publishers (publisher_name, contact_first_name, contact_last_name, contact_email, contact_phone, publisher_email, publisher_phone, address, website, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$publisher_name, $contact_first_name, $contact_last_name, $contact_email, $contact_phone, $publisher_email, $publisher_phone, $publisher_address, $publisher_website, $user_id]);
        } elseif ($user_type === "Manufacturer") {
            $manufacturer_name = $_POST['OrgName'];
            $contact_first_name = $_POST['cfname'];
            $contact_last_name = $_POST['clname'];
            $contact_email = $_POST['cemail'];
            $contact_phone = $_POST['cphone'];
            $manufacturer_email = $_POST['OrgEmail'];
            $manufacturer_phone = $_POST['OrgPhone'];
            $address = $_POST['address1'];
            $website = $_POST['website1'];
            $manufacturer_password = $_POST["password"];
            $products_offered = isset($_POST['products']) ? $_POST['products'] : array();
            $products_offered_string = '{' . implode(",", $products_offered) . '}';




            $stmt_users = $db_connection->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt_users->execute([$manufacturer_email, $manufacturer_password]);
            $user_id = $db_connection->lastInsertId();


            // Assuming $db_connection is your database connection object

            // Prepare the SQL statement
            $stmt = $db_connection->prepare("INSERT INTO manufacturers (manufacturer_name, contact_first_name, contact_last_name, contact_email, contact_phone, manufacturer_email, manufacturer_phone, address, website, products_offered,user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Execute the SQL statement with the provided values
            $stmt->execute([$manufacturer_name, $contact_first_name, $contact_last_name, $contact_email, $contact_phone, $manufacturer_email, $manufacturer_phone, $address, $website, $products_offered_string, $user_id]);






        }

        echo "New record created successfully";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}