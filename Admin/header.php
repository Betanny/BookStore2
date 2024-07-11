<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
//
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}





// Get user ID and category from session
$user_id = $_SESSION['user_id'];
$category = $_SESSION['category'];

try {
    // Determine which table to query based on user category
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

    $sql_unread_count = "SELECT COUNT(*) AS unread_count FROM notifications WHERE recipient_id = :user_id AND status = false";
    $stmt_unread_count = $db->prepare($sql_unread_count);
    $stmt_unread_count->execute(['user_id' => $user_id]);
    $unread_count = $stmt_unread_count->fetch(PDO::FETCH_ASSOC)['unread_count'];

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="seller.css">
    <link rel="stylesheet" href="/Shared Components/header.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

    <title>smartCBC</title>
</head>

<body>
    <header>
        <div class="logo">
            <img src="/Shared Components\smartcbc.svg" style="width:150px !important" alt="LOGO">
        </div>
        <input type="checkbox" id="nav_check" hidden>
        <nav>

            <ul>
                <li><a href="admindashboard.php" class="link light-text active-link">Dashboard</a></li>
                <li><a href="products.php" class="link light-text">Products</a></li>
                <li><a href="users.php" class="link light-text">Users</a></li>
                <li><a href="orders.php" class="link light-text">Orders</a></li>
                <li><a href="transactions.php" class="link light-text">Transactions</a></li>
                <!-- <li><a href="/Shared Components\logout.php" class="link-active">logout</a></li> -->

            </ul>

        </nav>
        <div class="user-panel">
            <!-- <div class="icon">
                <a href="#"><i class="fa-regular fa-envelope"></i></a>

            </div> -->
            <div class="icon">
                <a href="../Shared Components/notifications.php"><i class="fa-regular fa-bell"></i></a>
                <?php if ($unread_count > 0): ?>
                <span class="notification-count"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </div>
            <div class="profile">
                <div class="user-image">
                    <img src="/Images/Illustrations/profile.svg" class="img-profile">

                </div>
                <div class="user-name">
                    <h4>
                        Manager
                    </h4>
                    <div class="dropdown">
                        <button class="dropbtn"><i class="fa-solid fa-angle-down"></i></button>
                        <div class="dropdown-content">
                            <a href="/Shared Components\profile.php">Edit</a>
                            <a href="/Shared Components\logout.php">Logout</a>
                        </div>
                    </div>
                </div>

            </div>


        </div>

        <label for="nav_check" class="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </label>


    </header>
</body>

</html>