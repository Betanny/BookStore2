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
    // Determine which table to query based on user category
    $table_name = 'clients';


    // Query the appropriate table to fetch data
    $sql = "SELECT * FROM $table_name WHERE user_id = $user_id";

    // Execute the query and fetch the results
    $stmt = $db->query($sql);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    //Full Name
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $full_name = $first_name . ' ' . $last_name;
    global $full_name;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="buyer.css">
    <link rel="stylesheet" href="/Shared Components/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>Document</title>
</head>

<body class="">
    <header>
        <div class="logo">
            <img src="/Shared Components\smartcbc.svg" style="width:150px !important" alt="LOGO">

        </div>
        <input type="checkbox" id="nav_check" hidden>
        <nav>

            <ul>
                <li><a href="/Buyer/buyerdashboard.php" class="link light-text active-link">Dashboard</a></li>
                <li><a href="/Home/products.php" class="link light-text">Products</a></li>
                <li><a href="/Buyer/myorders.php" class="link light-text">My orders</a></li>
                <li><a href="/Shared Components\feedback.php" class="link light-text">Feedback</a>
                </li>
                <li><a href="/Buyer/bookselect.php" class="link light-text">Review a book</a></li>
                <!-- <li><a href="/Shared Components\logout.php" class="link-active">logout</a></li> -->

            </ul>

        </nav>
        <div class="user-panel">
            <div class="icon">
                <a href="#"><i class="fa-regular fa-envelope"></i></a>

            </div>
            <div class="icon">
                <a href="#"><i class="fa-regular fa-bell"></i></a>
            </div>
            <div class="icon">
                <a href="../Buyer/CheckOut.php"><i class="fa-solid fa-cart-shopping"></i></a>

            </div>

            <div class="profile">
                <div class="user-image">
                    <img src="/Images/Illustrations/profile.svg" class="img-profile">

                </div>
                <div class="user-name">
                    <h4>
                        <?php echo $full_name; ?>
                    </h4>
                    <a href="" onclick="minivisible()"><i class="fa-solid fa-angle-down"></i></a>

                </div>

            </div>
        </div>
        <!-- style="display:flex;flex-direction:column;" -->



        <label for="nav_check" class="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </label>


    </header>
    <div class="mini-menu" style="display:none">

        <li><a href="/Shared Components\feedback.php" class="link light-text">Profile</a>
        <li><a href="/Shared Components\logout.php" class="link light-text">LogOut</a>

    </div>
</body>
<!-- <div id="feedbackContainer" style="display: none;">
       include 'D:\xammp2\htdocs\BookStore2\Shared Components\feedback.php'; ?>
    </div>
    <script src="/Shared Components/Feedback.php"></script> -->
<script>
    function minivisible(event) {
        event.preventDefault();
        const miniMenu = document.getElementsByClassName('mini-menu');
        miniMenu.style.display = 'block';
    }

    document.addEventListener("DOMContentLoaded", function () {
        const userPanel = document.getElementsByClassName('profile');
        const miniMenu = document.getElementsByClassName('mini-menu');

        userPanel.addEventListener('onmouseover', function () {
            miniMenu.style.display = 'block';
        });

        userPanel.addEventListener('mouseleave', function () {
            miniMenu.style.display = 'none';
        });
    });
</script>

</html>