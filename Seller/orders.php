<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';
include '../Shared Components/logger.php';

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

//try {
// Define SQL query to fetch orders data
// Set the default status filter to 'all'
// $statusFilter = 'All';

// Check if a filter has been selected
// if (isset($_GET['status']) && ($_GET['status'] == 'All' || $_GET['status'] == 'Pending' || $_GET['status'] == 'Delivered')) {
//     $statusFilter = $_GET['status'];
// }
// if ($statusFilter == 'All') {

//     $sql = "SELECT o.order_id, b.title, o.order_date, o.shipping_address,o.dealer_delivery_date, o.quantity, o.status,o.dealer_status, o.delivery_date
//         FROM orders o
//         INNER JOIN books b ON o.product_id = b.bookid
//         WHERE o.seller_id = :seller_id";
// } else {
//     $sql = "SELECT o.order_id, b.title, o.order_date, o.shipping_address,o.dealer_delivery_date, o.quantity, o.status,o.dealer_status, o.delivery_date
//     FROM orders o
//     INNER JOIN books b ON o.product_id = b.bookid
//     WHERE o.seller_id = :seller_id AND status = '$statusFilter'";
// }

// Prepare and execute the query
// $stmt = $db->prepare($sql);
// $stmt->execute([':seller_id' => $user_id]);
// Define your SQL query to fetch data from the books and orders table
$statusFilter = 'All';
$query = '';

// Check if a filter has been selected
if (isset($_GET['status']) && ($_GET['status'] == 'All' || $_GET['status'] == 'Pending' || $_GET['status'] == 'Delivered')) {
    $statusFilter = $_GET['status'];
}

// Check if a search query is provided
if (isset($_GET['query']) && !empty($_GET['query'])) {
    // Set the $query variable
    $query = $_GET['query'];

    // Append a condition to search by title
    $queryCondition = " AND LOWER(b.title) LIKE LOWER(:query)";
} else {
    $queryCondition = "";
}

try {
    $statusFilter = 'All';
    $queryCondition = '';

    // Check if a filter has been selected
    if (isset($_GET['status']) && ($_GET['status'] == 'All' || $_GET['status'] == 'Pending' || $_GET['status'] == 'Delivered')) {
        $statusFilter = $_GET['status'];
    }

    // Check if a search query is provided
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = $_GET['query']; // Add wildcards to search for partial matches
        $queryCondition .= " AND (LOWER(b.title) LIKE LOWER(:query) OR b.grade LIKE :query)";
    }

    $sql = "SELECT b.bookid, b.title, b.isbn, b.subject, b.bookrating, 
                   COUNT(o.product_id) AS copies_bought, 
                   SUM(o.total_amount) AS total_values_generated,
                   o.order_id, o.order_date,o.delivery_option, o.shipping_address, o.dealer_delivery_date, o.quantity, o.status, o.dealer_status, o.delivery_date
            FROM books b
            LEFT JOIN orders o ON b.bookid = o.product_id
            WHERE b.seller_id = :seller_id";

    // Append status filter condition if not 'All'
    if ($statusFilter != 'All') {
        $sql .= " AND o.status = :status";
    }

    // Append search query condition if provided
    $sql .= $queryCondition;

    // Group by relevant columns
    $sql .= " GROUP BY b.bookid, b.title, b.isbn, b.subject, b.bookrating, o.order_id, o.order_date, o.shipping_address, o.dealer_delivery_date, o.quantity, o.status, o.dealer_status, o.delivery_date";

    // Prepare and execute the query
    $stmt = $db->prepare($sql);

    // Bind the seller ID parameter
    $stmt->bindValue(':seller_id', $user_id, PDO::PARAM_INT);

    // Bind the status parameter if applicable
    if ($statusFilter != 'All') {
        $stmt->bindValue(':status', $statusFilter, PDO::PARAM_STR);
    }

    // Bind the search query parameter if provided
    if (!empty($query)) {
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    }

    // Execute the query
    $stmt->execute();
    // Fetch the results into an associative array
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);







    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['confirm_delivery'])) {
            // Confirm Delivery form submission 
            $orderId = $_POST['order_id'];
            $comments = $_POST['comments'];
            $current_date = date("Y-m-d");

            // Update database
            $sql = "UPDATE orders SET dealer_status = 'Delivered', dealer_comment = :comments, dealer_delivery_date = :delivery_date WHERE order_id = :orderId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':comments', $comments);
            $stmt->bindParam(':orderId', $orderId);
            $stmt->bindParam(':delivery_date', $current_date);
            $stmt->execute();
            writeLog($db, "Dealer has confirmed to have delivered the order " . $orderId, "INFO", $user_id);

        } elseif (isset($_POST['decline_order'])) {
            // Decline Order form submission
            $orderId = $_POST['order_id'];
            $reasons = $_POST['reason'];


            // Update database
            $sql = "UPDATE orders SET dealer_status = 'Declined', status = 'Declined', dealer_comment = :reasons,dealer_delivery_date = :delivery_date  WHERE order_id = :orderId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':reasons', $reasons);
            $stmt->bindParam(':orderId', $orderId);
            $stmt->bindParam(':delivery_date', $current_date);

            $stmt->execute();
            writeLog($db, "Dealer has declined the order " . $orderId, "INFO", $user_id);

        }
        header("Location: {$_SERVER['REQUEST_URI']}");

    }

    if (isset($_GET['export']) && $_GET['export'] === 'true') {
        writeLog($db, "User has extracted a copy of the products ", "INFO", $user_id);

        $filename = 'orders_report.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write CSV headers
        fputcsv($output, array_keys($orders[0]));

        // Write transaction data to CSV
        foreach ($orders as $order) {
            fputcsv($output, $order);
        }

        // Close output stream
        fclose($output);
        exit();
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/Shared Components/style.css">
    <!-- <link rel="stylesheet" href="/Registration/Stylesheet.css"> -->
    <link rel="stylesheet" href="seller.css">
    <link rel="stylesheet" href="home.css">

    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

</head>

<body>
    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components/headerdispatcher.php"
        ?>
    <div class="viewproducts-container">
        <div class="viewproducts-header">
            <h4>My Orders</h4>

            <div class="left-filter">
                <button type="button" class="add-button" id="exportButton">Export
                    <a href="#" class="icon-cell" style="color: white;">
                        <div class="icon-cell">
                            <i class="fa-solid fa-file-arrow-down"></i>
                        </div>
                    </a>
                </button>
            </div>
            <div class="right-filter">
                <div class="filter-dropdown">
                    <form action="" method="get">
                        <select id="filterDropdown" class="filter-bar" name="status" onchange="this.form.submit()">
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

                <div class="search-container">
                    <form action="" method="GET">
                        <input type="text" name="query" id="search-input" class="search-bar" placeholder="Search..."
                            value="<?php echo htmlspecialchars($query); ?>">
                        <button class="search-button" type="submit"><i
                                class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="allProducts-container">
            <div class="table">
                <div class="row-header">
                    <div class="ordername-cell">Title</div>
                    <div class="cell">Order Date</div>
                    <div class="bigger-cell2">Shipping Address</div>
                    <div class="small-cell">Quantity</div>
                    <div class="cell">Delivery Option</div>
                    <div class="cell">Client Delivery Status</div>
                    <div class="cell">Dealer Delivery Status</div>
                    <div class="cell">Delivery Date</div>
                </div>
                <div class="order-rows">
                    <!-- Adding the order items -->
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="row">
                                <div class="ordername-cell">
                                    <?php echo $order['title']; ?>
                                </div>
                                <div class="cell">
                                    <?php echo isset($order['order_date']) ? $order['order_date'] : ''; ?>
                                </div>
                                <div class="bigger-cell2">
                                    <?php echo $order['shipping_address']; ?>
                                </div>
                                <div class="small-cell">
                                    <?php echo $order['quantity']; ?>
                                </div>
                                <div class="cell">
                                    <?php
                                    if (isset($order) && isset($order['delivery_option'])) {
                                        echo $order['delivery_option'];
                                    } else {
                                        echo 'Delivery option not available';
                                    }
                                    ?>
                                </div>
                                <div class="cell" style="background-color:
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
; border-radius: 15px;margin:7px; padding: 5px;
                            ">
                                    <?php echo $order['status']; ?>
                                </div>
                                <div class="cell" style="background-color:
    <?php
            // Determine background color based on status
            $status = strtolower($order['dealer_status']);
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
                                    <?php echo $order['dealer_status']; ?>
                                </div>

                                <div class="cell">
                                    <?php if (strtolower($order['dealer_status']) === 'declined'): ?>
                                        ---
                                    <?php else: ?>
                                        <?php echo $order['dealer_delivery_date']; ?>
                                    <?php endif; ?>

                                    <?php if (strtolower($order['dealer_status']) === 'pending'): ?>
                                        <button type="submit" id="update-btn-<?php echo $order['order_id']; ?>"
                                            class="update-button" data-order-id="<?php echo $order['order_id']; ?>">Update</button>
                                    <?php endif; ?>
                                </div>






                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- <div class="row"> -->
                        <h2>No orders to display yet.</h2>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>

    <div class="update-container">
        <div class="update-popup">
            <div class="select-update-action">
                <button type="button" class="action-button" id="Delivered">Confirm Delivery</button>
                <button type="button" class="action-button" id="Decline">Decline Order</button>
            </div>
            <div class="Delivered-container">
                <form action="#" method="post" onsubmit="reloadPage()">
                    <input type="hidden" name="order_id" id="delivery-order-id" value="">





                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="comments" class="no-asterisk">Any message you would like to pass across to your
                                customers</label>
                            <textarea class="inputfield" style="height: 70px;width: 90%;" class="inputfield"
                                name="comments"></textarea>
                        </div>
                    </div>

                    <button type="submit" name="confirm_delivery" class="submit-btn">Confirm Delivery</button>

                </form>

            </div>
            <div class="Decline-container">
                <form action="#" method="post" onsubmit="reloadPage()">
                    <input type="hidden" name="order_id" id="rejected-order-id" value="">

                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="reason" class="no-asterisk">Reason for declining:</label><br>
                            <textarea id="reason" style="height: 60px;width: 90%;" name="reason"></textarea>
                        </div>
                    </div>

                    <button type="submit" name="decline_order" class="submit-btn">Decline Order</button>
                </form>

            </div>
        </div>
    </div>


</body>

<script>
    // var update-btn =document.getElementById('update-btn');
    document.addEventListener("DOMContentLoaded",
        function () { // Get the update button
            var updateButton = document.getElementById('update-btn');
            // Get the Delivered button
            var deliveredButton = document.getElementById('Delivered');
            // Get the Decline button        
            var declineButton = document.getElementById('Decline');
            document.querySelector('.update-container').style.display = 'none';



            // Add click event listener to the update button 
            // updateButton.addEventListener('click', function () {
            //     // Hide the viewproducts-container
            //     document.querySelector('.viewproducts-container').style.display = 'none';
            //     document.querySelector('.Decline-container').style.display = 'none';
            //     document.querySelector('.Delivered-container').style.display = 'none';
            //     document.querySelector('.update-container').style.display = 'block';
            // });
            var updateButtons = document.querySelectorAll('.update-button');

            // Loop through each update button and add event listener
            updateButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Get the parent row of the clicked button
                    var row = button.closest('.row');

                    // Hide the viewproducts-container and show the update-container
                    document.querySelector('.viewproducts-container').style.display = 'none';
                    document.querySelector('.update-container').style.display = 'block';
                    document.querySelector('.Decline-container').style.display = 'none';
                    document.querySelector('.Delivered-container').style.display = 'none';
                    // const orderId = updateButton.getAttribute("data-order-id");
                    // console.log("Order ID:", orderId);
                    // document.getElementById("order-id-input").value = orderId;
                    var orderId = button.getAttribute("data-order-id");
                    console.log("Order ID:", orderId);
                    // Set the order ID in the delivery form
                    document.getElementById("delivery-order-id").value = orderId;

                    // Set the order ID in the rejected form
                    document.getElementById("rejected-order-id").value = orderId;




                    // Add any additional logic as needed
                });
            });


            // Add click event listener to the Delivered button     
            deliveredButton.addEventListener('click', function () {
                // Show the Delivered container and hide the Decline container 
                document.querySelector('.Delivered-container').style.display = 'block';
                document.querySelector('.Decline-container').style.display = 'none';
                deliveredButton.classList.add('active');
                deliveredButton.classList.remove('inactive');
                declineButton.classList.add('inactive');
                declineButton.classList.remove('active');

            });


            // Add click event listener to the Decline button   
            declineButton.addEventListener('click', function () {
                // Show the Decline container and hide the Delivered container
                document.querySelector('.Delivered-container').style.display = 'none';
                document.querySelector('.Decline-container').style.display = 'block';
                declineButton.classList.add('active');
                declineButton.classList.remove('inactive');
                deliveredButton.classList.add('inactive');
                deliveredButton.classList.remove('active');
            });
        });




    document.addEventListener("DOMContentLoaded", function () {
        var exportButton = document.getElementById('exportButton');
        exportButton.addEventListener('click', function () {
            // Update the href attribute of the export button with the desired URL
            var currentHref = window.location.href;
            var exportUrl = currentHref.includes('?export=true') ? currentHref : currentHref +
                '?export=true';
            exportButton.querySelector('a').setAttribute('href', exportUrl);
        });
    });
</script>


</html>