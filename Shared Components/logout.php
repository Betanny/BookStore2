<?php
include '../Shared Components\logger.php';

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

// Check if the logout form is submitted
if (isset($_POST['logout'])) {
    // Clear all session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();
    writeLog($db, "User has logged out of the system", "INFO", $user_id);

    // Redirect to the home page
    header("Location: ../Home/homepage.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

</head>

<body>
    <div id="header-container"></div>
    <div class="logout-container">
        <div class="logout-popup">
            <div class="logout-content">
                <img src="/Images/Illustrations/LogoutIcon.svg" alt="">
                <h4>Oh No! You're leaving <br>Are you sure?</h4>
            </div>
            <div class="select-logout-action">
                <button type="button" class="active" onclick="goBack()" id="return-btn">Naaah, Just Kidding</button>
                <!-- Use a form to submit the logout -->
                <form method="post" action="">
                    <button type="submit" class="inactive" name="logout" id="logout-btn">Yes, Log Me Out</button>
                </form>
            </div>
        </div>
    </div>
</body>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('header.php').then(response => response.text()).then(data => {
        document.getElementById('header-container').innerHTML = data;
    });
});

function reloadPage() {
    location.reload(); // Reload the current page
}

function goBack() {
    window.history.back();
}
</script>

</html>