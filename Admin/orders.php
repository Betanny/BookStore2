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
    // Define SQL query to fetch orders data
    $statusFilter = 'All';
    $query = ''; // Initialize the query variable
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
                   o.order_id, o.order_date, o.shipping_address,o.delivery_option, o.dealer_delivery_date, o.quantity, o.status, o.dealer_status, o.delivery_date
            FROM books b
            INNER JOIN orders o ON b.bookid = o.product_id";

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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="/Registration/Stylesheet.css">
    <link rel="stylesheet" href="/Seller/seller.css">

    <title>My Orders</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="viewproducts-container">
        <div class="viewproducts-header">
            <h4>Orders</h4>

            <div class="left-filter">
                <button type="submit" class="add-button">Export <div class="icon-cell">
                        <i class="fa-solid fa-file-arrow-down"></i>
                    </div></button>
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
                    <div class="cell">Status</div>
                    <div class="cell" style="text-align: center;">Client Delivery Date</div>
                    <div class="cell" style="text-align: center;">Dealer Delivery Date</div>
                    <div class="cell" style="text-align: center;">Delivery Type</div>


                </div>
                <div class="order-rows">
                    <!-- Adding the order items -->
                    <?php foreach ($orders as $order): ?>
                    <div class="row">
                        <div class="ordername-cell">
                            <?php echo $order['title']; ?>
                        </div>
                        <div class="cell">
                            <?php echo $order['order_date']; ?>
                        </div>
                        <div class="bigger-cell2">
                            <?php echo $order['shipping_address']; ?>
                        </div>
                        <div class="small-cell">
                            <?php echo $order['quantity']; ?>
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
; border-radius: 15px; padding: 5px;
                            ">
                            <?php echo $order['status']; ?>
                        </div>


                        <div class="bigger-cell" style="text-align: center;">
                            <?php if (strtolower($order['dealer_status']) === 'pending' || strtolower($order['dealer_status']) === 'declined'): ?>
                            ---
                            <?php else: ?>
                            <?php echo $order['delivery_date']; ?>

                            <!-- <button class="update-button">Update</button> -->
                            <?php endif; ?>
                        </div>
                        <div class="cell">

                            <?php if (strtolower($order['dealer_status']) === 'pending' || strtolower($order['dealer_status']) === 'declined'): ?>
                            ---
                            <?php else: ?>
                            <?php echo $order['dealer_delivery_date']; ?>

                            <!-- <button class="update-button">Update</button> -->
                            <?php endif; ?>
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

                        <!-- <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash"></i>
                        </div>
                        <div class="icon-cell">
                            <a href="#" class="delete-link" data-table="orders"
                                data-pk="<php echo $order['order_id']; ?>" data-pk-name="order_id">
                            <i class="fa-solid fa-trash"></i>
                            </a>
                        </div> -->






                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>


</html>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Fetch and insert header
    fetch('header.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });

    // Get all elements with the class "delete-link"
    var deleteLinks = document.querySelectorAll('.delete-link');

    // Loop through each delete link
    deleteLinks.forEach(function(link) {
        // Add click event listener to each delete link
        link.addEventListener('click', function(event) {
            // Prevent the default behavior (i.e., following the href)
            event.preventDefault();

            // Get the table name, primary key column name, and primary key value from the data attributes
            var tableName = link.getAttribute('data-table');
            var primaryKey = link.getAttribute('data-pk');
            var pkName = link.getAttribute('data-pk-name');

            // Perform AJAX request to the delete script
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/Shared Components/delete.php?table=' + tableName + '&pk=' +
                primaryKey + '&pk_name=' + pkName, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Handle successful deletion (if needed)
                    // For example, you can remove the deleted row from the DOM
                    link.parentElement.parentElement.remove();
                } else {
                    // Handle error (if needed)
                    console.error('Error:', xhr.statusText);
                }
            };
            xhr.onerror = function() {
                // Handle network errors (if needed)
                console.error('Request failed');
            };
            xhr.send();
        });
    });
});
</script>