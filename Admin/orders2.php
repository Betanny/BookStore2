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
    // Define SQL query to fetch orders data
    $sql = "SELECT o.order_id, b.title, o.order_date, o.shipping_address, o.quantity, o.status, o.delivery_date
    FROM orders o
    INNER JOIN books b ON o.product_id = b.bookid";

    // Prepare and execute the query
    $stmt = $db->prepare($sql);
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
                    <select id="genre-filter" class="filter-bar" placeholder="sort">
                        <option value="All">All</option>
                        <option value="Latest">Latest</option>
                        <option value="Popularity">Popularity</option>
                        <option value="Rating">Rating</option>
                    </select>
                </div>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="search-input" class="search-bar" placeholder="Search...">
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
                    <div class="bigger-cell" style="text-align: center;">Delivery Date</div>
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
                            <?php echo $order['delivery_date']; ?>
                        </div>

                        <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash"></i>
                        </div>
                        <div class="icon-cell">
                            <a href="#" class="delete-link" data-table="orders"
                                data-pk="<?php echo $order['order_id']; ?>" data-pk-name="order_id">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>






                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="deleteModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this order?</p>
            <div class="modal-buttons">
                <button id="confirmDelete" class="delete-button">Delete</button>
                <button class="cancel-button">Cancel</button>
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

deleteLinks.forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.preventDefault();

        // Get the table name, primary key column name, and primary key value from the data attributes
        var tableName = link.getAttribute('data-table');
        var primaryKey = link.getAttribute('data-pk');
        var pkName = link.getAttribute('data-pk-name');

        // Open the delete confirmation modal
        var modal = document.getElementById('deleteModal');
        modal.style.display = 'block';

        // Get the confirm delete button and add a click event listener
        var confirmDeleteButton = document.getElementById('confirmDelete');
        confirmDeleteButton.addEventListener('click', function() {
            // Perform AJAX request to the delete script
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/Shared Components/delete.php?table=' + tableName + '&pk=' +
                primaryKey + '&pk_name=' + pkName, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Handle successful deletion (if needed)
                    // For example, you can remove the deleted row from the DOM
                    link.parentElement.parentElement.remove();
                    modal.style.display = 'none';
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

        // Get the close button and add a click event listener to close the modal
        var closeButton = document.getElementsByClassName('close-button')[0];
        closeButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Close the modal when clicking outside of it
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
});
</script>

</html>