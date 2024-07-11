<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
// session_start();

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
        default:
            throw new Exception("Invalid category");
    }

    // Query the appropriate table to fetch data
    $sql = "SELECT * FROM $table_name WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        throw new Exception("No data found for the user");
    }

    switch ($category) {
        case 'Author':
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $full_name = $first_name . ' ' . $last_name;
            break;
        case 'Publisher':
            $full_name = $data['publisher_name'];
            break;
        case 'Manufacturer':
            $full_name = $data['manufacturer_name'];
            break;
        // Add more cases as needed
    }

    $sql_unread_count = "SELECT COUNT(*) AS unread_count FROM notifications WHERE recipient_id = :user_id AND status = false";
    $stmt_unread_count = $db->prepare($sql_unread_count);
    $stmt_unread_count->execute(['user_id' => $user_id]);
    $unread_count = $stmt_unread_count->fetch(PDO::FETCH_ASSOC)['unread_count'];

    $sql_notifications = "SELECT notifications.*, users.email 
                          FROM notifications 
                          JOIN users ON notifications.sender_id = users.user_id 
                          WHERE notifications.recipient_id = :user_id";
    $stmt_notifications = $db->prepare($sql_notifications);
    $stmt_notifications->execute(['user_id' => $user_id]);
    $notifications = $stmt_notifications->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $recipient_email = $_POST['recipient'];
        $message = $_POST['message'];

        $stmt = $db->prepare("SELECT user_id FROM users WHERE email = :email");
        $stmt->execute(['email' => $recipient_email]);
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$recipient) {
            throw new Exception("Recipient not found");
        }

        $recipient_id = $recipient['user_id'];

        // Insert new notification
        $sql = "INSERT INTO notifications (sender_id, recipient_id, notification_message) 
                VALUES (:sender_id, :recipient_id, :message)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'sender_id' => $user_id,
            'recipient_id' => $recipient_id,
            'message' => $message
        ]);
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
} catch (Exception $e) {
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
    <link rel="icon" type="image/svg+xml" href="/Shared Components/smartcbc.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">
    <title>SmartCBC</title>
</head>

<body>
    <header>
        <div class="logo">
            <img src="/Shared Components/smartcbc.svg" style="width:150px !important" alt="LOGO">
        </div>
        <input type="checkbox" id="nav_check" hidden>
        <nav>
            <ul id="nav-menu">
                <li><a href="/Seller/sellerdashboard.php" id="dashboard-link"
                        class="link light-text active-link">Dashboard</a></li>
                <li><a href="/Seller/ViewProducts.php" id="ViewProducts-link" class="link light-text">Products</a></li>
                <li><a href="/Seller/orders.php" id="orders-link" class="link light-text">Orders</a></li>
                <li><a href="/Seller/transactions.php" id="transactions-link" class="link light-text">Transactions</a>
                </li>
                <li><a href="/Shared Components/feedback.php" id="feedback-link" class="link light-text">Feedback</a>
                </li>
            </ul>
        </nav>
        <div class="user-panel">
            <div class="icon">
                <a href="../Shared Components/notifications.php"><i class="fa-regular fa-bell"></i></a>
                <?php if ($unread_count > 0): ?>
                <span class="notification-count">
                    <?php echo $unread_count; ?></span>
                <?php endif; ?>
            </div>
            <div class="profile">
                <div class="user-image">
                    <img src="/Images/Illustrations/profile.svg" class="img-profile">
                </div>
                <div class="user-name">
                    <h4><?php echo htmlspecialchars($full_name); ?></h4>
                    <div class="dropdown">
                        <button class="dropbtn"><i class="fa-solid fa-angle-down"></i></button>
                        <div class="dropdown-content">
                            <a href="/Shared Components/profile.php">Edit</a>
                            <a href="/Shared Components/logout.php">Logout</a>
                            <a href="/Shared Components/deleteaccount.php">Delete Account</a>
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    const links = document.querySelectorAll('#nav-menu a');

    // Function to deactivate all links
    function deactivateAllLinks() {
        links.forEach(link => {
            link.classList.remove('active-link');
        });
    }

    // Function to activate the correct link based on the current URL
    function activateLink() {
        const currentPath = window.location.pathname;
        links.forEach(link => {
            if (currentPath.endsWith(link.getAttribute('href'))) {
                link.classList.add('active-link');
            }
        });
    }

    deactivateAllLinks();
    activateLink();
});
</script>

</html>