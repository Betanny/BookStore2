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

    // Query the appropriate table to fetch user's name
    $sql_name = "SELECT * FROM $table_name WHERE user_id = :user_id";
    $stmt_name = $db->prepare($sql_name);
    $stmt_name->execute(['user_id' => $user_id]);
    $data = $stmt_name->fetch(PDO::FETCH_ASSOC);

    // Get user's full name based on category
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

    // Query to fetch notifications for the user
    $sql_notifications = "SELECT notifications.*, users.email 
                          FROM public.notifications 
                          JOIN users ON notifications.sender_id = users.user_id 
                          WHERE notifications.recipient_id = :user_id";
    $stmt_notifications = $db->prepare($sql_notifications);
    $stmt_notifications->execute(['user_id' => $user_id]);
    $notifications = $stmt_notifications->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Document</title>

</head>

<body>
    <div id="header-container"></div>
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Notifications</h2>
            <div class="close">
                <i class="fa-solid fa-xmark" onclick="cancel();"></i>
            </div>
        </div>
        <div class="modal-content">
            <div class="all-notifications">
                <?php foreach ($notifications as $notification): ?>

                <div class="notification">
                    <!-- <a href="orders.php?status=Pending"> -->
                    <h4><?php echo $notification['email']; ?></h4>
                    <h5><?php echo $notification['notification_message']; ?></h5>
                    </a>
                </div>
                <?php endforeach; ?>


            </div>
            <div class="opened-notification" style="display:none;">
                <h4>Sender : wairimumishy354@gmail.com</h4>
                <p> Message : This is my messge to you</p>


                <button class="button">Reply</button>

            </div>
            <div class="new-notification" style="display:none;">
                <div class="notification-header">
                    <h4>To: </h4>
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="recipient"></label class="no-asterisk">
                            <input type="text" class="inputfield" name="recipient" />
                        </div>
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="details"></label class="no-asterisk">
                        <textarea class="inputfield" name="details"
                            style="height: 150px; width: 90%; margin-left: 25px;"></textarea>

                    </div>
                </div>
                <button class="button">Send</button>

            </div>
        </div>
    </div>



</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('header.php').then(response => response.text()).then(data => {
        document.getElementById('header-container').innerHTML = data;
    });


});

function cancel() {
    window.location.href = 'ViewProducts.php';
}
</script>