<?php
// Start session if not already started
session_start();

// Function to get user-specific data based on role
function getUserData()
{
    if (isset($_SESSION['user_id']) && isset($_SESSION['category'])) {
        $user_id = $_SESSION['user_id'];
        $category = $_SESSION['category'];

        // Fetch user data based on category or user ID
        // Example:
        // $sql = "SELECT * FROM $category WHERE user_id = $user_id";
        // Execute query and return user data
        return array('user_id' => $user_id, 'category' => $category);
    }
    return null;
}
$sql_unread_count = "SELECT DISTINCT COUNT(*) AS unread_count FROM notifications WHERE recipient_id = :user_id AND status = false";
$stmt_unread_count = $db->prepare($sql_unread_count);
$stmt_unread_count->execute(['user_id' => $user_id]);
$unread_count = $stmt_unread_count->fetch(PDO::FETCH_ASSOC)['unread_count'];
global $unread_count;
var_dump($unread_count);
// Get user data
$user_data = getUserData();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Shared Components/header.css">
    <link rel="stylesheet" href="/Buyer/buyer.css">

    <!-- Additional stylesheets if needed -->
    <title>Document</title>
</head>

<body>
    <header>
        <div class="logo">
            <img src="/Shared Components/smartcbc.svg" style="width:150px !important" alt="LOGO">
        </div>
        <input type="checkbox" id="nav_check" hidden>
        <nav>
            <ul>
                <li><a href="/Home/homepage.html" class="link light-text active-link">Home</a></li>
                <?php if ($user_data): ?>
                <?php if ($user_data['category'] == 'Admin'): ?>
                <li><a href="admindashboard.php" class="link light-text active-link">Dashboard</a></li>
                <li><a href="products.php" class="link light-text">Products</a></li>
                <li><a href="users.php" class="link light-text">Users</a></li>
                <li><a href="orders.php" class="link light-text">Orders</a></li>
                <li><a href="transactions.php" class="link light-text">Transactions</a></li>
                <?php elseif ($user_data['category'] == 'Buyer'): ?>
                <li><a href="buyerdashboard.php" class="link light-text active-link">Dashboard</a></li>
                <li><a href="products.php" class="link light-text">Products</a></li>
                <li><a href="myorders.php" class="link light-text">My orders</a></li>
                <li><a href="feedback.php" class="link light-text">Feedback</a>
                </li>
                <li><a href="bookselect.php" class="link light-text">Review a book</a></li>
                <?php elseif ($user_data['category'] == 'Seller'): ?>
                <li><a href="sellerdashboard.php" class="link light-text active-link">Dashboard</a></li>
                <li><a href="ViewProducts.php" class=" link light-text">Products</a></li>
                <li><a href="orders.php" class="link light-text">Orders</a></li>
                <li><a href="transactions.php" class="link light-text">Transactions</a></li>
                <li><a href="feedback.php" class="link light-text">Feedback</a>
                    <?php endif; ?>
                <li><a href="/Home/products.php" class="link light-text">Products</a></li>
                <li><a href="/Home/aboutus.html" class="link light-text">About Us</a></li>
                <?php else: ?>
                <li><a href="/Home/homepage.html" class="link light-text active-link">Home</a></li>
                <li><a href="/Home/products.php" class="link light-text">Products</a></li>
                <li><a href="/Home/Aboutus.html" class="link light-text">About us</a></li>
                <li><a href="/Home/contactus.html" class="link-active">Contact us</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php if ($user_data): ?>
        <div class="user-panel">
            <div class="profile">
                <div class="user-name">
                    <h4>User Name</h4> <!-- Replace with actual user name -->
                    <a href="#"><i class="fa-solid fa-angle-down"></i></a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <label for="nav_check" class="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </label>
    </header>
</body>

</html>