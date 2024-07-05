<?php
include '../Shared Components\logger.php';

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
    //Full Name
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $full_name = $first_name . ' ' . $last_name;
    global $full_name;
    $points = $data['points'];
    global $points;


    //Getting book reccomendations from the books table
    $bookrecsql = "SELECT DISTINCT bookid, front_page_image, title, price, bookrating, RANDOM() as rand FROM books ORDER BY rand LIMIT 4";
    $bookrecomendationstmt = $db->query($bookrecsql);
    $books = $bookrecomendationstmt->fetchAll(PDO::FETCH_ASSOC);
    global $books;


    //Getting the book orders made
    $clientid = $data['client_id'];




    // Set the default status filter to 'all'
    $statusFilter = 'All';

    // Check if a filter has been selected
    if (isset($_GET['status']) && ($_GET['status'] == 'All' || $_GET['status'] == 'Pending' || $_GET['status'] == 'Delivered')) {
        $statusFilter = $_GET['status'];
    }


    // Modify the SQL query based on the selected status filter
    if ($statusFilter == 'All') {
        $ordersql = "SELECT orders.*, books.title AS book_title
                     FROM orders 
                     INNER JOIN books ON orders.product_id = books.bookid 
                     WHERE orders.client_id = $clientid";
    } else {
        $ordersql = "SELECT orders.*, books.title AS book_title 
                      FROM orders 
                     INNER JOIN books ON orders.product_id = books.bookid
                        WHERE orders.client_id = $clientid AND status = '$statusFilter'";
    }

    $ordersstmt = $db->query($ordersql);
    $orders = $ordersstmt->fetchAll(PDO::FETCH_ASSOC);
    global $orders;


    // Get the total number of items bought
    $itemsBoughtSql = "SELECT COUNT(*) AS total_items_bought FROM orders WHERE client_id = $clientid";
    $itemsBoughtStmt = $db->query($itemsBoughtSql);
    $totalItemsBoughtResult = $itemsBoughtStmt->fetch(PDO::FETCH_ASSOC);
    $totalItemsBought = $totalItemsBoughtResult['total_items_bought'];
    global $totalItemsBought;

    // Get the total number of items reviewed
    $reviewsSql = "SELECT COUNT(*) AS total_items_reviewed FROM reviews WHERE user_id = $user_id";
    $reviewsStmt = $db->query($reviewsSql);
    $totalItemsReviewedResult = $reviewsStmt->fetch(PDO::FETCH_ASSOC);
    $totalItemsReviewed = $totalItemsReviewedResult['total_items_reviewed'];
    global $totalItemsReviewed;


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
    <!-- <link rel="stylesheet" href="/Home/home.css"> -->

    <link rel="stylesheet" href="/Shared Components/style.css">
    <!-- <link rel="stylesheet" href="/Shared Components/style.css"> -->
    <!-- <link rel="stylesheet" href="admin.css"> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">



    <style>
    .task {
        height: 100%;
        /* Set the height of the calendar container to 100% */
    }
    </style>
</head>

<body>
    <div id="header-container"></div>
    <div class="dashboard-container">
        <div class="reports-container">
            <div class="welcome-container">
                <div class="welcome-text">
                    <h4>Welcome back
                        <?php echo $full_name; ?>!!!
                    </h4>
                    <p>Your commitment to your child's learning journey is truly inspiring!</p>
                    <p>Let's navigate through this educational journey together, ensuring every click brings us closer
                        to shaping bright futures for our kids. </p>
                    <p>Happy exploring!</p>
                </div>
                <div class="welcome-image">
                    <img src="/Images/Illustrations/client dashboard image.webp" alt="">
                </div>
            </div>
            <div class="order-container">
                <h4> My Orders</h4>
                <div class="filter">
                    <!-- <select id="filterDropdown"> -->
                    <form action="" method="get">
                        <select id="filterDropdown" name="status" onchange="this.form.submit()">
                            <option value="All" <?php
                            if (isset($_GET['status']) && $_GET['status'] === 'All') {
                                echo "selected";
                            }
                            ; ?>>All</option>
                            <option value="Pending" <?php
                            if (isset($_GET['status']) && $_GET['status'] === 'Pending') {
                                echo "selected";
                            }
                            ; ?>>Pending</option>
                            <option value="Delivered" <?php
                            if (isset($_GET['status']) && $_GET['status'] === 'Delivered') {
                                echo "selected";
                            }
                            ; ?>>Delivered</option>
                        </select>

                    </form>
                </div>
                <div class="items-container">
                    <div class="table">
                        <div class="row-header">
                            <div class="name-cell">Item</div>
                            <div class="cell">Price</div>
                            <div class="cell">Status</div>
                            <div class="cell">Delivery Date</div>
                        </div>
                        <div class="rows">
                            <!-- Adding the order items -->
                            <?php foreach ($orders as $order): ?>
                            <div class="row">
                                <div class="name-cell">
                                    <?php echo $order['book_title']; ?>
                                </div>
                                <div class="cell">
                                    <?php echo $order['unit_price']; ?>
                                </div>
                                <div class="cell">
                                    <?php echo $order['status']; ?>
                                </div>
                                <div class="cell">
                                    <?php echo $order['delivery_date']; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>




            <div class="suggestions">
                <h4>Other books you might like</h4>
                <div class="book-suggestions">
                    <?php
                    foreach ($books as $book) {
                        $front_image = str_replace('D:\xammp2\htdocs\BookStore2', '', $book['front_page_image']);


                        echo '<div class="book">';
                        echo '<div class="book-img">';
                        echo '<a href="/Home/product.php?bookid=' . $book['bookid'] . '"><img src="' . $front_image . '"></a>';
                        echo '</div>';

                        echo '<p>' . $book['title'] . '</p>';
                        echo '<p>Price: ksh.' . $book['price'] . '</p>';
                        echo '<p>Rating: ';
                        // Get integer part of the rating
                        $integer_rating = floor($book['bookrating']);
                        // Get decimal part of the rating
                        $decimal_rating = $book['bookrating'] - $integer_rating;
                        // Generate full stars based on the integer part of the rating
                        for ($i = 1; $i <= $integer_rating; $i++) {
                            echo '<span class="star">&#9733;</span>'; // Full star
                        }

                        // // If decimal part is greater than 0, add a half star
                        // if ($decimal_rating > 0) {
                        //     echo '<span class="half-star">&#9733;</span>'; // Half star
                        // }
                    
                        // Generate empty stars for remaining
                        for ($i = $integer_rating + 1; $i <= 5; $i++) {
                            echo '<span class="star">&#9734;</span>'; // Empty star
                        }
                        echo '</p>';
                        echo '</div>';
                    }
                    ?>
                </div>


            </div>
        </div>



        <div class="task-panel">
            <div class="profile-container">
                <div class="user-details">
                    <div class="user">
                        <img src="/Images/Illustrations/profile.svg" alt="">
                        <h4>
                            <?php echo $full_name; ?>
                        </h4>
                        <h4><i class="fa-solid fa-star"></i> Esteemed Client <i class="fa-solid fa-star"></i></h4>
                    </div>
                    <div class="details">
                        <div class="double-details">
                            <div class="detail">
                                <p>
                                    <?php echo $totalItemsBought; ?>
                                </p>
                                <h5>Items bought</h5>
                            </div>
                            <div class="detail">
                                <p>
                                    <?php echo $points; ?>
                                </p>
                                <h5>My points</h5>
                            </div>
                        </div>
                        <div class="double-details">
                            <div class="detail">
                                <p>
                                    <?php echo $totalItemsReviewed; ?>
                                </p>
                                <h5>Products reviewed</h5>
                            </div>
                            <div class="detail">
                                <p> <?php echo $totalItemsReviewed; ?>
                                </p>
                                <h5>Products rated</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="notifications">
                <div class="notifications-container">
                    <h4> Notifications</h4>
                    <?php if (empty($notifications)): ?>
                    <p>You have no notifications</p>
                    <?php else: ?>
                    <div class="rows">
                        <?php foreach ($notifications as $notification): ?>
                        <div class="notification" data-email="<?php echo htmlspecialchars($notification['email']); ?>"
                            data-message="<?php echo htmlspecialchars($notification['notification_message']); ?>"
                            onclick="openNotification(this);">
                            <h5><?php echo htmlspecialchars($notification['email']); ?></h5>
                            <p><?php echo htmlspecialchars($notification['notification_message']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

        <div class="modal" id="notifications-modal">
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


</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('header copy.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });

    document.getElementById('notifications-modal').style.display = 'none';
});

function cancel() {
    window.location.href = 'ViewProducts.php';
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
</script>

</html>