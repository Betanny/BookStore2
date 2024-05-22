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
try {
    // Determine which table to query based on user category
    $table_name = 'clients';

    // Query the appropriate table to fetch data
    $sql = "SELECT * FROM $table_name WHERE user_id = $user_id";

    // Execute the query and fetch the results
    $stmt = $db->query($sql);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    switch ($category) {
        case 'Individual':
            // Full Name
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $full_name = $first_name . ' ' . $last_name;
            break;
        case 'Organization':
            $full_name = $data['organization_name'];
            break;
    }

    // Unread notifications count
    $sql_unread_count = "SELECT DISTINCT COUNT(*) AS unread_count FROM notifications WHERE recipient_id = :user_id AND status = false";
    $stmt_unread_count = $db->prepare($sql_unread_count);
    $stmt_unread_count->execute(['user_id' => $user_id]);
    $unread_count = $stmt_unread_count->fetch(PDO::FETCH_ASSOC)['unread_count'];

    // Cart items count
    $sql_cart_count = "SELECT DISTINCT COUNT(*) AS cart_items FROM cart WHERE client_id = :user_id";
    $stmt_cart_count = $db->prepare($sql_cart_count);
    $stmt_cart_count->execute(['user_id' => $user_id]);
    $cart_count = $stmt_cart_count->fetch(PDO::FETCH_ASSOC)['cart_items'];




    $sql_notifications = "SELECT notifications.*, users.email 
    FROM public.notifications 
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
    <link rel="icon" type="image/svg+xml" href="/Shared Components/smartcbc.svg">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
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
                <li><a href="/Buyer/buyerdashboard.php" class="link light-text active-link">Dashboard</a></li>
                <li><a href="/Home/products.php" class="link light-text">Products</a></li>
                <li><a href="/Buyer/myorders.php" class="link light-text">My orders</a></li>
                <li><a href="/Shared Components/feedback.php" class="link light-text">Feedback</a></li>
                <li><a href="/Buyer/bookselect.php" class="link light-text">Review a book</a></li>
            </ul>
        </nav>
        <div class="user-panel">
            <div class="icon" onclick="showModal();" role="button">
                <i class="fa-regular fa-bell" style="color:blue;cursor:pointer"></i>
                <?php if ($unread_count > 0): ?>
                <span class="notification-count"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </div>
            <div class="icon">
                <a href="../Buyer/CheckOut.php"><i class="fa-solid fa-cart-shopping"></i></a>
                <?php if ($cart_count > 0): ?>
                <span class="notification-count"><?php echo $cart_count; ?></span>
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
                            <a href="clientprofile.php">Edit</a>
                            <a href="/Shared Components/logout.php">Logout</a>
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
    <div class="modal" id="modal" style="display:none;">
        <div class="modal-header">
            <div class="left-section">

                <button type="submit" class="add-button">New<div class="icon-cell">
                        <i class="fa-solid fa-plus"></i>
                    </div></button>
            </div>
            <h2 class="modal-title">Notifications</h2>
            <div class="close">
                <i class="fa-solid fa-xmark" onclick="cancel();"></i>
            </div>
        </div>
        <div class="modal-content">
            <div class="all-notifications" id="all-notifications">
                <?php foreach ($notifications as $notification): ?>

                <div class="notification" data-email="<?php echo htmlspecialchars($notification['email']); ?>"
                    data-message="<?php echo htmlspecialchars($notification['notification_message']); ?>"
                    onclick="openNotification(this);">
                    <h4><?php echo htmlspecialchars($notification['email']); ?></h4>
                    <h5><?php echo htmlspecialchars($notification['notification_message']); ?></h5>
                </div>
                <?php endforeach; ?>


            </div>
            <div class="opened-notification" id="opened-notification" style="display:none;">
                <h4 id="sender-email">Sender : </h4>
                <p id="notification-message"> Message : </p>


                <button class="button">Reply</button>

            </div>
            <div class="new-notification" id="new-notification" style="display:none;">
                <form action="#" method="post" id="new-notification-form">

                    <div class="notification-header">
                        <h4>To: </h4>
                        <div class="form-group">
                            <div class="inputcontrol">
                                <label class="no-asterisk" for="recipient"></label>
                                <input type="text" class="inputfield" name="recipient" />
                            </div>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="message"></label>
                            <textarea class="inputfield" name="message"
                                style="height: 150px; width: 85%; margin-left: 25px;"></textarea>
                            <div class="error"></div>

                        </div>

                    </div>
                    <button type="submit" class="button">Send</button>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById('modal');

        function showModal() {
            if (container.style.display === "none" || container.style.display === "") {
                container.style.display = "block";
                console.log("Showing modal"); // Debug statement
            } else {
                console.log("Hiding modal"); // Debug statement
                container.style.display = "none";
            }
        }

        function cancel() {
            console.log("Hiding modal"); // Debug statement
            container.style.display = "none";
        }

        var allnotification = document.getElementById('all-notifications');
        var openednotification = document.getElementById('opened-notification');
        var newnotification = document.getElementById('new-notification');
        var addButton = document.querySelector('.add-button');

        function openNotification(element) {
            const email = element.getAttribute('data-email');
            const message = element.getAttribute('data-message');

            document.getElementById('sender-email').innerText = 'Sender: ' + email;
            document.getElementById('notification-message').innerText = 'Message: ' + message;

            document.getElementById('all-notifications').style.display = 'none';
            document.getElementById('new-notification').style.display = 'none';
            document.getElementById('opened-notification').style.display = 'block';
        }

        function newNotification() {
            allnotification.style.display = 'none';
            newnotification.style.display = 'block';
            openednotification.style.display = 'none';
            addButton.innerHTML = 'Back <div class="icon-cell"><i class="fa-solid fa-back"></i></div>';
        }

        function allNotification() {
            allnotification.style.display = 'block';
            newnotification.style.display = 'none';
            openednotification.style.display = 'none';
            addButton.innerHTML = 'New <div class="icon-cell"><i class="fa-solid fa-plus"></i></div>';
        }

        addButton.addEventListener('click', function() {
            if (this.innerHTML.includes('Back')) {
                allNotification();
            } else {
                newNotification();
            }
        });

        // Expose functions to global scope
        window.showModal = showModal;
        window.cancel = cancel;
        window.openNotification = openNotification;
    });
    </script>
</body>

</html>