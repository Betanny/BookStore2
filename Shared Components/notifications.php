<?php
include '../Shared Components/logger.php';

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
    $table_name = '';
    switch ($category) {
        case 'Individual':
        case 'Organization':
            $table_name = 'clients';
            break;
        case 'Publisher':
            $table_name = 'publishers';
            break;
        case 'Author':
            $table_name = 'authors';
            break;
        case 'Manufacturer':
            $table_name = 'manufacturers';
            break;
        case 'Admin':
            $table_name = 'users';
            break;
        default:
            throw new Exception("Unknown category: $category");
    }

    // Query the appropriate table to fetch user's name
    $sql_name = "SELECT * FROM $table_name WHERE user_id = :user_id";
    $stmt_name = $db->prepare($sql_name);
    $stmt_name->execute(['user_id' => $user_id]);
    $data = $stmt_name->fetch(PDO::FETCH_ASSOC);

    // Get user's full name based on category
    switch ($category) {
        case 'Author':
            $full_name = $data['first_name'] . ' ' . $data['last_name'];
            break;
        case 'Publisher':
            $full_name = $data['publisher_name'];
            break;
        case 'Manufacturer':
            $full_name = $data['manufacturer_name'];
            break;
        case 'Admin':
            $full_name = "Manager";
            break;
        default:
            $full_name = ""; // Default case if category doesn't match
            break;
    }

    // Query to fetch notifications for the user
    $sql_notifications = "SELECT notifications.*, users.email 
                          FROM public.notifications 
                          JOIN users ON notifications.sender_id = users.user_id 
                          WHERE notifications.recipient_id = :user_id";
    $stmt_notifications = $db->prepare($sql_notifications);
    $stmt_notifications->execute(['user_id' => $user_id]);
    $notifications = $stmt_notifications->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['notification_id'])) {
            // Handle notification status update (if needed)
            $notification_id = intval($_POST['notification_id']);

            // Update notification status to 'read'
            $sql_update_status = "UPDATE notifications SET status = true WHERE notification_id = :notification_id";
            $stmt_update_status = $db->prepare($sql_update_status);
            $stmt_update_status->execute(['notification_id' => $notification_id]);
        } else {
            $recipient_email = $_POST['recipient'];
            $message = $_POST['message'];
            $reply_id = isset($_POST['reply_id']) && is_numeric($_POST['reply_id']) ? intval($_POST['reply_id']) : null;

            // Log received data
            error_log("Recipient Email: $recipient_email");
            error_log("Message: $message");
            error_log("Reply ID: " . ($reply_id !== null ? $reply_id : 'NULL'));

            // Fetch recipient user_id
            $stmt = $db->prepare("SELECT user_id FROM users WHERE email = :email");
            $stmt->execute(['email' => $recipient_email]);
            $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($recipient) {
                $recipient_id = $recipient['user_id'];

                // Update existing notification status to 'read' if reply_id is provided
                if ($reply_id !== null) {
                    $sql_update_status = "UPDATE notifications SET status = true WHERE notification_id = :reply_id";
                    $stmt_update_status = $db->prepare($sql_update_status);
                    $stmt_update_status->execute(['reply_id' => $reply_id]);
                }

                // Insert new notification with proper handling of reply_id
                $sql_insert_notification = "INSERT INTO notifications (sender_id, recipient_id, notification_message, reply_id) 
                    VALUES (:sender_id, :recipient_id, :message, :reply_id)";
                $stmt_insert_notification = $db->prepare($sql_insert_notification);

                // Log the insert query and parameters
                error_log("Executing Insert Query: $sql_insert_notification");
                error_log("Parameters: sender_id=$user_id, recipient_id=$recipient_id, message=$message, reply_id=" . ($reply_id !== null ? $reply_id : 'NULL'));

                $stmt_insert_notification->execute([
                    'sender_id' => $user_id,
                    'recipient_id' => $recipient_id,
                    'message' => $message,
                    'reply_id' => $reply_id
                ]);
            }
        }
    }
} catch (PDOException $e) {
    error_log("PDO Error: " . $e->getMessage());
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
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

    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

</head>

<body>
    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components/headerdispatcher.php"
        ?>
    <div class="modal">
        <div class="modal-header">
            <div class="left-section">

                <button type="submit" class="add-button">New <div class="icon-cell">
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
                    data-message=" <?php echo htmlspecialchars($notification['notification_message']); ?>"
                    data-reply-id="<?php echo htmlspecialchars($notification['notification_id']); ?>"
                    onclick=" openNotification(this);">
                    <h4>
                        <?php echo htmlspecialchars($notification['email']); ?>
                    </h4>
                    <h5><?php echo htmlspecialchars($notification['notification_message']); ?></h5>
                </div>
                <?php endforeach; ?>


            </div>
            <div class="opened-notification" id="opened-notification" style="display:none;">
                <h4 id="sender-email">Sender : </h4>
                <p id="notification-message"> Message : </p>

                <button class="button" onclick="replyToNotification()">Reply</button>
            </div>

            <div class="new-notification" id="new-notification" style="display:none;">
                <form action="#" method="post" id="new-notification-form">
                    <input type="hidden" name="reply_id" id="reply-id" />

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



</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    document.getElementsByClassName('modal').style.display = 'none';


});

function cancel() {
    window.history.back();
}
var allnotification = document.getElementById('all-notifications');
var openednotification = document.getElementById('opened-notification');
var newnotification = document.getElementById('new-notification');
var addButton = document.querySelector('.add-button');


// function openNotification() {
//     allnotification.style.display = 'none';
//     newnotification.style.display = 'none';
//     openednotification.style.display = 'block';

// }
function openNotification(element) {
    const email = element.getAttribute('data-email');
    const message = element.getAttribute('data-message');
    const replyId = element.getAttribute('data-reply-id');

    document.getElementById('sender-email').innerText = 'Sender: ' + email;
    document.getElementById('notification-message').innerText = 'Message: ' + message;
    document.getElementById('reply-id').value = replyId;

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

// function replyToNotification() {
//     const recipient = document.getElementById('sender-email').innerText.replace('Sender: ', '');
//     document.querySelector('input[name="recipient"]').value = recipient;
//     const replyId = document.getElementById('reply-id').value;

//     document.getElementById('all-notifications').style.display = 'none';
//     document.getElementById('new-notification').style.display = 'block';
//     document.getElementById('opened-notification').style.display = 'none';
//     addButton.innerHTML = 'Back <div class="icon-cell"><i class="fa-solid fa-back"></i></div>';
// }
function replyToNotification() {
    const recipient = document.getElementById('sender-email').innerText.replace('Sender: ', '');
    document.querySelector('input[name="recipient"]').value = recipient;
    const replyId = document.getElementById('reply-id').value;

    // Ensure replyId is set correctly
    if (replyId.trim() !== '') {
        document.getElementById('reply-id').value = replyId; // Set the reply_id field
    } else {
        document.getElementById('reply-id').value = ''; // Reset reply_id if necessary
    }

    // Adjust display of sections as needed
    document.getElementById('all-notifications').style.display = 'none';
    document.getElementById('new-notification').style.display = 'block';
    document.getElementById('opened-notification').style.display = 'none';
    addButton.innerHTML = 'Back <div class="icon-cell"><i class="fa-solid fa-back"></i></div>';
}
</script>


</html>