<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.html");
    exit();
}

// Get user ID and category from session
$user_id = $_SESSION['user_id'];
$category = $_SESSION['category'];

try {
    // Initialize $name variable
    $name = '';

    // SQL command to select all columns from the users table
    $sql = "
        SELECT 
            ROW_NUMBER() OVER() AS serialno,
            email,
            role,
            category,
            DATE(createdat) AS date_joined
        FROM 
            users
    ";

    // Prepare the SQL statement
    $stmt = $db->prepare($sql);

    // Execute the SQL statement
    $stmt->execute();

    // Fetch the results into an associative array
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check category and retrieve name accordingly
    switch ($category) {
        case 'individual':
            // SQL command to concatenate first name and last name from clients table
            $namesql = "
                SELECT 
                    CONCAT(first_name, ' ', last_name) AS name
                FROM
                    clients
                WHERE
                    client_id = :user_id
            ";
            break;
        case 'organization':
            // SQL command to select organization name from clients table
            $namesql = "
                SELECT 
                    organization_name AS name
                FROM
                    clients
                WHERE
                    client_id = :user_id
            ";
            var_dump($user_id);
            break;
        case 'Author':
            // SQL command to concatenate first name and last name from authors table
            $namesql = "
                SELECT 
                    CONCAT(first_name, ' ', last_name) AS name
                FROM
                    authors
                WHERE
                    author_id = :user_id
            ";
            break;
        case 'publisher':
            // SQL command to select publisher name from publishers table
            $namesql = "
                SELECT 
                    publisher_name AS name
                FROM
                    publishers
                WHERE
                    publisher_id = :user_id
            ";
            break;
        case 'manufacturer':
            // SQL command to select manufacturer name from manufacturers table
            $namesql = "
                SELECT 
                    manufacturer_name AS name
                FROM
                    manufacturers
                WHERE
                    manufacturer_id = :user_id
            ";
            break;
        default:
            // Handle unknown category
            $name = 'Unknown Category';
            break;
    }

    // Prepare and execute the SQL statement to fetch the name
    $namestmt = $db->prepare($namesql);
    $namestmt->bindParam(':user_id', $user_id);
    $namestmt->execute();

    // Fetch name from the result
    $nameData = $namestmt->fetch(PDO::FETCH_ASSOC);

    // Check if $nameData is empty
    if ($nameData) {
        // Assign the retrieved name to $name variable
        $name = $nameData['name'];
    } else {
        // Handle case when no matching record is found
        $name = 'No Name Found';
    }
} catch (PDOException $e) {
    // Handle PDO exception
    echo "Error: " . $e->getMessage();
}

// Display the fetched data
// var_dump($users);
// echo "Name: $name <br>";
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="/Registration/Stylesheet.css">
    <link rel="stylesheet" href="/Seller/seller.css">

    <title>Document</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="viewproducts-container">
        <div class="viewproducts-header">
            <h4>All Users</h4>

            <div class="left-filter">

                <button type="submit" class="add-button">Export <div class="icon-cell">
                        <i class="fa-solid fa-file-arrow-down"></i>
                    </div></button>


            </div>
            <div class="right-filter">
                <div class="filter-dropdown">
                    <select id="genre-filter" class="filter-bar" placeholder="sort">
                        <option value="All">All</option>
                        <option value="Latest">Latest</option>
                        <option value="Popularity">Popularity</option>
                        <option value="Rating">Rating</option>
                    </select>
                </div>

                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input type="text" id="search-input" class="search-bar" placeholder="Search...">
                </div>
                <div class="addproductsbutton">
                    <button type="submit" class="add-button">Add <div class="icon-cell">
                            <i class="fa-solid fa-plus"></i>
                        </div></button>

                </div>

            </div>
        </div>

        <div class="allProducts-container">
            <div class="table">
                <div class="row-header">
                    <div class="cell">No.</div>
                    <div class="bigger-cell2">User Name</div>
                    <div class="bigger-cell2">Email</div>
                    <div class="bigger-cell">Role</div>
                    <div class="bigger-cell">Category</div>
                    <div class="bigger-cell">Date joined</div>


                </div>
                <div class="rows">
                    <!-- Adding the user items -->
                    <?php foreach ($users as $user): ?>
                    <div class="row">
                        <div class="cell">
                            <?php echo $user['serialno']; ?>
                        </div>
                        <div class="bigger-cell2">
                            <?php echo $name; ?>
                        </div>
                        <div class="bigger-cell2">
                            <?php echo $user['email']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $user['role']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $user['category']; ?>
                        </div>

                        <div class="bigger-cell">
                            <?php echo $user['date_joined']; ?>
                        </div>
                        <!-- <div class="cell">
                            </div> -->

                        <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash"></i>
                        </div>
                        <div class="icon-cell">
                            <i class="fa-solid fa-pen"></i>
                        </div>
                        <div class="icon-cell">
                            <i class="fa-solid fa-trash"></i>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>



</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('header.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;

        });
});
</script>

</html>