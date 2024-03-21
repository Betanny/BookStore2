<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
// Check if user is logged in
if (!isset ($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.html");
    exit();
}

// Get user ID and category from session
$user_id = $_SESSION['user_id'];
$category = $_SESSION['category'];

try {


    $table_name = 'clients';


    // Query the appropriate table to fetch data
    $sql = "SELECT * FROM $table_name WHERE user_id = $user_id";

    // Execute the query and fetch the results
    $stmt = $db->query($sql);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    //Getting the book orders made
    $clientid = $data['client_id'];

    $statusFilter = 'All';

    // Check if a filter has been selected
    if (isset ($_GET['status']) && ($_GET['status'] == 'All' || $_GET['status'] == 'Pending' || $_GET['status'] == 'Delivered')) {
        $statusFilter = $_GET['status'];
    }

    if ($statusFilter == 'All') {
        $ordersql = "SELECT orders.*, books.title AS title
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <title>Document</title>
</head>

<body>
    <div id="header-container"></div>

    <div class="viewproducts-container">
        <div class="viewproducts-header">
            <h4>My Orders</h4>

            <div class="left-filter">
                <button type="submit" class="add-button">Export <div class="icon-cell">
                        <i class="fa-solid fa-file-arrow-down"></i>
                    </div></button>
            </div>
            <div class="right-filter">
                <div class="filter">
                    <!-- <select id="filterDropdown"> -->
                    <form action="" method="get">
                        <select id="filterDropdown" name="status" onchange="this.form.submit()">
                            <option value="All" <?php
                            if ($_GET['status'] === 'All') {
                                echo "selected";
                            }
                            ; ?>>All</option>
                            <option value="Pending" <?php
                            if ($_GET['status'] === 'Pending') {
                                echo "selected";
                            }
                            ; ?>>Pending</option>
                            <option value="Delivered" <?php
                            if ($_GET['status'] === 'Delivered') {
                                echo "selected";
                            }
                            ; ?>>Delivered</option>
                        </select>

                    </form>
                </div>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="search-input" class="search-bar" placeholder="Search...">
                </div>

            </div>
        </div>

        <div class="allProducts-container">
            <div class="table">
                <div class="row-header1">
                    <div class="ordername-cell">Title</div>
                    <div class="cell1">Order Date</div>
                    <div class="bigger-cell2">Shipping Address</div>
                    <div class="cell1">Quantity</div>
                    <div class="cell1">Status</div>
                    <div class="cell1">Delivery Date</div>
                </div>
                <div class="order-rows">
                    <!-- Adding the order items -->
                    <?php foreach ($orders as $order): ?>
                    <div class="row">
                        <div class="ordername-cell">
                            <?php echo $order['title']; ?>
                        </div>
                        <div class="cell1">
                            <?php echo $order['order_date']; ?>
                        </div>
                        <div class="bigger-cell2">
                            <?php echo $order['shipping_address']; ?>
                        </div>
                        <div class="cell1">
                            <?php echo $order['quantity']; ?>
                        </div>
                        <div class="cell1" style="background-color:
    <?php
    // Determine background color based on status
    $status = strtolower($order['status']);
    if ($status === 'delivered') {
        echo '#90ee90';
    } elseif ($status === 'pending') {
        echo '#ffa500';
    } elseif ($status === 'declined') {
        echo 'red';
    } else {
        echo 'transparent'; // Default color or no color
    }
    ?>
; border-radius: 15px; padding: 5px;
                            ">
                            <?php echo $order['status']; ?>
                        </div>


                        <div class="cell1">
                            <?php echo $order['delivery_date']; ?>
                        </div>
                        <?php if ($status === 'pending'): ?>
                        <button type="submit" id="update-btn" class="update-button">Update</button>

                        <!-- <button class="update-button">Update</button> -->
                        <?php endif; ?>






                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>




</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('header.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });
});
</script>

</html>