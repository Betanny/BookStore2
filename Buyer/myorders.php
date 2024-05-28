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
    if (isset($_GET['status']) && ($_GET['status'] == 'All' || $_GET['status'] == 'Pending' || $_GET['status'] == 'Delivered')) {
        $statusFilter = $_GET['status'];
    }

    if ($statusFilter == 'All') {
        $ordersql = "SELECT orders.*, books.title AS title
                     FROM orders 
                     INNER JOIN books ON orders.product_id = books.bookid 
                     WHERE orders.client_id = $clientid";
    } else {
        $ordersql = "SELECT orders.*, books.title AS title 
                      FROM orders 
                     INNER JOIN books ON orders.product_id = books.bookid
                        WHERE orders.client_id = $clientid AND status = '$statusFilter'";
    }


    $ordersstmt = $db->query($ordersql);
    $orders = $ordersstmt->fetchAll(PDO::FETCH_ASSOC);
    global $orders;

    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['confirm_delivery'])) {
            // Confirm Delivery form submission 
            $orderId = $_POST['order_id'];
            var_dump($orderId);
            $comments = $_POST['comments'];
            $current_date = date("Y-m-d");

            // Update database
            $sql = "UPDATE orders SET status = 'Delivered', comments = :comments, delivery_date = :delivery_date WHERE order_id = :orderId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':comments', $comments);
            $stmt->bindParam(':orderId', $orderId);
            $stmt->bindParam(':delivery_date', $current_date);
            $stmt->execute();
        } elseif (isset($_POST['decline_order'])) {
            // Decline Order form submission
            $orderId = $_POST['order_id'];
            $reasons = $_POST['reason'];
            $improvement = $_POST['improvement'];


            // Update database
            $sql = "UPDATE orders SET status = 'Declined', reasons = :reasons, comments = :improvement WHERE order_id = :orderId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':reasons', $reasons);
            $stmt->bindParam(':improvement', $improvement);
            $stmt->bindParam(':orderId', $orderId);
            $stmt->execute();
        }
        header("Location: {$_SERVER['REQUEST_URI']}");

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
                    <div class="cell1" style="margin:20px;">Status</div>
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
; border-radius: 15px;margin:15px; padding: 5px;
                            ">
                            <?php echo $order['status']; ?>
                        </div>


                        <div class="cell1">
                            <?php echo $order['delivery_date']; ?>

                            <?php if ($status === 'pending'): ?>
                            <!-- <button type="submit" id="update-btn-<?php echo $order['order_id']; ?>"
                                    class="update-button">Update</button> -->
                            <button type="submit" id="update-btn-<?php echo $order['order_id']; ?>"
                                class="update-button" data-order-id="<?php echo $order['order_id']; ?>">Update</button>


                            <!-- <button class="update-button">Update</button> -->
                            <?php endif; ?>

                        </div>





                    </div>
                    <?php endforeach; ?>
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
                            <div class="toggle-buttons">
                                <label class="question-label" for="all-items">Did you receive all the items in your
                                    order?</label>
                                <input type="radio" id="all-items-yes" name="all-items" value="yes">
                                <label for="all-items-yes">Yes</label>
                                <input type="radio" id="all-items-no" name="all-items" value="no">
                                <label for="all-items-no">No</label>
                            </div>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="inputcontrol">
                        <div class="toggle-buttons">

                            <label class="question-label" for="delivery-timeliness">Were the items delivered within the
                                expected
                                timeframe?</label>
                            <input type="radio" id="delivery-timeliness-yes" name="delivery-timeliness" value="yes">
                            <label for="delivery-timeliness-yes">Yes</label>
                            <input type="radio" id="delivery-timeliness-no" name="delivery-timeliness" value="no">
                            <label for="delivery-timeliness-no">No</label>
                        </div>
                        <div class="error"></div>
                    </div>
                    <div class="inputcontrol">
                        <div class="toggle-buttons">

                            <label class="question-label" for="item-condition">Did the items arrive in good condition
                                and as described on our
                                website?</label>
                            <input type="radio" id="item-condition-yes" name="item-condition" value="yes">
                            <label for="item-condition-yes">Yes</label>
                            <input type="radio" id="item-condition-no" name="item-condition" value="no">
                            <label for="item-condition-no">No</label>
                        </div>
                        <div class="error"></div>
                    </div>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="comments" class="no-asterisk">Any additional comments?</label>
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
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="improvement" class="no-asterisk">what would you have opted to be done
                                differently?</label>
                            <textarea id="improvement" style="height: 60px;width: 90%;" name="improvement"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="decline_order" class="submit-btn">Decline Order</button>
                </form>

            </div>
        </div>
    </div>



</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('header.php').then(response => response.text()).then(data => {
        document.getElementById('header-container').innerHTML = data;
    });
});
document.addEventListener("DOMContentLoaded",
    function() { // Get the update button
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
        updateButtons.forEach(function(button) {
            button.addEventListener('click', function() {
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
        deliveredButton.addEventListener('click', function() {
            // Show the Delivered container and hide the Decline container 
            document.querySelector('.Delivered-container').style.display = 'block';
            document.querySelector('.Decline-container').style.display = 'none';
            deliveredButton.classList.add('active');
            deliveredButton.classList.remove('inactive');
            declineButton.classList.add('inactive');
            declineButton.classList.remove('active');

        });


        // Add click event listener to the Decline button   
        declineButton.addEventListener('click', function() {
            // Show the Decline container and hide the Delivered container
            document.querySelector('.Delivered-container').style.display = 'none';
            document.querySelector('.Decline-container').style.display = 'block';
            declineButton.classList.add('active');
            declineButton.classList.remove('inactive');
            deliveredButton.classList.add('inactive');
            deliveredButton.classList.remove('active');
        });
    });

function reloadPage() {
    location.reload(); // Reload the current page
}
</script>

</html>