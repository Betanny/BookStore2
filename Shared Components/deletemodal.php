<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.php");
    exit();
}

// Get user ID and category from session
$user_id = $_SESSION['user_id'];
$category = $_SESSION['category'];
$role = $_SESSION['role'];


try {
    $table_name = '';
    switch ($category) {
        case 'Author':
            $table_name = 'authors';
            break;
        case 'Publisher':
            $table_name = 'publishers';
            break;
        case 'Manufacturer':
            $table_name = 'manufacturers';
            break;
        // Add more cases as needed
    }
    // Query the appropriate table to fetch data
    $sql = "SELECT * FROM $table_name WHERE user_id = $user_id";

    // Execute the query and fetch the results
    $stmt = $db->query($sql);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    switch ($category) {
        case 'Author':
            $table_name = 'authors';
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $full_name = $first_name . ' ' . $last_name;
            global $first_name, $full_name;

            break;
        case 'Publisher':
            $table_name = 'publishers';
            $full_name = $first_name = $data['publisher_name'];
            $last_name = "";
            global $first_name, $full_name;
            break;
        case 'Manufacturer':
            $table_name = 'manufacturers';
            $full_name = $first_name = $data['manufacturer_name'];
            $last_name = "";

            break;
        // Add more cases as needed
    }
    $profile_sql = "SELECT * FROM $table_name WHERE user_id = :user_id";
    $profile_stmt = $db->prepare($profile_sql);
    $profile_stmt->bindParam(':user_id', $user_id);
    $profile_stmt->execute();
    $profile = $profile_stmt->fetch(PDO::FETCH_ASSOC);
    global $profile;


} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="/Registration/Stylesheet.css">
    <link rel="stylesheet" href="seller.css">

    <title>Document</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="modal" id="editprofile-modal">
        <form action="#" method="post">

            <input type="hidden" name="user_id" value="<?php echo $profile['user_id']; ?>">

            <div class="modal-header">
                <h2 class="modal-title">Edit Profile</h2>
                <div class="close">
                    <i class="fa-solid fa-xmark" onclick="goBack();"></i>
                </div>
            </div>
            <div class="modal-content">
                <h1>Are you sure you want to delete?</h1>

                <div class="modal-buttons">
                    <button class="button" type="button" onclick="goBack();">Cancel</button>
                    <button class="button" type="button">Delete</button>

                </div>
            </div>



    </div>




</body>
<script>
    var modal = document.getElementById("editprofile-modal");

    function editProfile() {
        // Get the modal
        modal.style.display = "block";
    }

    function goBack() {
        modal.style.display = "none";
        window.history.back();

    }

    <?php if ($role == 'Client'): ?>
        document.addEventListener("DOMContentLoaded", function () {
            fetch('../Buyer/header.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('header-container').innerHTML = data;
                });
        });
    <?php elseif ($role == 'Dealer'): ?>
        document.addEventListener("DOMContentLoaded", function () {
            fetch('../Seller/header.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('header-container').innerHTML = data;
                });
        });
    <?php else: ?>
        document.addEventListener("DOMContentLoaded", function () {
            fetch('/Buyer/header.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('header-container').innerHTML = data;
                });
        });
    <?php endif; ?>
</script>


</html>