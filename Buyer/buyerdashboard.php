<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
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
    $bookrecsql = "SELECT DISTINCT bookid, front_page_image, title, price, bookrating, RANDOM() as rand FROM books ORDER BY rand LIMIT 6";
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
    <!-- <link rel="stylesheet" href="admin.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Document</title>
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
                            <option value="All" style="display: none;">All</option>
                            <option value="All">All</option>
                            <option value="Pending">Pending</option>
                            <option value="Delivered">Delivered</option>
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
                        echo '<a href=""><img src="' . $front_image . '"></a>';
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
                                <p>15</p>
                                <h5>Products reviewed</h5>
                            </div>
                            <div class="detail">
                                <p>153</p>
                                <h5>Products rated</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="notifications">
                <div class="notifications-container">
                    <h4> Notifications</h4>

                    <div class="notification">
                        <h5>Delivery</h5>
                        <p>On 2nd march your book was delivered</p>

                    </div>
                    <div class="notification">
                        <h5>Delivery</h5>
                        <p>On 2nd march your book was delivered</p>

                    </div>
                    <div class="notification">
                        <h5>Delivery</h5>
                        <p>On 2nd march your book was delivered</p>

                    </div>
                </div>
            </div>

        </div>
    </div>


</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('header.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });

    fetch('/Shared Components/calendar.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('calendar-container').innerHTML = data;
        });
});
</script>

</html>