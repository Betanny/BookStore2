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
// Fetch all user emails from the 'users' table
$stmt_emails = $db->prepare("SELECT email FROM users");
$stmt_emails->execute();
$emails = $stmt_emails->fetchAll(PDO::FETCH_COLUMN);

// Pass the emails to JavaScript
echo "<script>var emailList = " . json_encode($emails) . ";</script>";
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

            // Check if a similar notification already exists
            $sql_check_duplicate = "SELECT COUNT(*) FROM notifications 
                                    WHERE sender_id = :sender_id 
                                    AND recipient_id = :recipient_id 
                                    AND notification_message = :message";
            $stmt_check_duplicate = $db->prepare($sql_check_duplicate);
            $stmt_check_duplicate->execute([
                'sender_id' => $user_id,
                'recipient_id' => $recipient_id,
                'message' => $message
            ]);

            $count = $stmt_check_duplicate->fetchColumn();

            if ($count == 0) {
                // Insert new notification if not a duplicate
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
            } else {
                error_log("Duplicate notification detected. No new notification inserted.");
            }
        }
    }
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

                <button type="button" class="add-button">New <div class="icon-cell">
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
                    <?php if (!$notification['status']): ?>
                    <div class="unread-dot"></div>
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($notification['email']); ?></h4>
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
                                <input type="text" class="inputfield" name="recipient" list="email-suggestions" />
                                <datalist id="email-suggestions">
                                    <!-- JavaScript will populate this with options -->
                                </datalist>
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

    // Send AJAX request to update notification status
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_notification_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            var status = xhr.status;
            if (status === 0 || (status >= 200 && status < 400)) {
                // The request has been completed successfully
                console.log(xhr.responseText);
            } else {
                // Oh no! There has been an error with the request!
                console.error("Failed to update notification status: " + xhr.responseText);
            }
        }
    };

    xhr.send("notification_id=" + replyId);
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


document.addEventListener("DOMContentLoaded", function() {
    var emailSuggestions = document.getElementById('email-suggestions');

    // Populate datalist with email options
    emailList.forEach(function(email) {
        var option = document.createElement('option');
        option.value = email;
        emailSuggestions.appendChild(option);
    });
});
</script>

</html>