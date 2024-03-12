<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
//
$full_name = '';
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
    $table_name = '';
    switch ($category) {
        case 'Author':
            $table_name = 'authors';
            break;
        case 'Publisher':
            $table_name = 'publishers';
            break;
        case 'Manufacturer':
            $table_name = 'manufacturers';
            break;
        // Add more cases as needed
    }


    // Query the appropriate table to fetch data
    $sql = "SELECT * FROM $table_name WHERE user_id = $user_id";

    // Execute the query and fetch the results
    $stmt = $db->query($sql);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);


    //Full Name
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $full_name = $first_name . ' ' . $last_name;


    // SQL query to get the best-rated book for the current seller
    $sql_best_rated = "SELECT * FROM books WHERE seller_id = $user_id ORDER BY bookrating DESC LIMIT 1";

    // Execute the query to fetch the best-rated book
    $stmt_best_rated = $db->query($sql_best_rated);
    $best_rated_book = $stmt_best_rated->fetch(PDO::FETCH_ASSOC);
    $title = $best_rated_book['title'];
    global $title;




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
    <link rel="stylesheet" href="seller.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Document</title>

</head>

<body>
    <div id="header-container"></div>
    <div class="dashboard-container">
        <div class="reports-container">
            <div class="welcome-container">
                <h4>Welcome back
                    <?php echo $full_name; ?><i class="fa-solid fa-hand"></i>
                </h4>
            </div>
            <div class="two-reports">
                <div class="report1">
                    <h4>Best Products</h4>
                    <div class="products">
                        <div class="product">
                            <h4>
                                <?php echo $title; ?>
                            </h4>
                            <h5>Highest rated</h5>

                        </div>
                        <div class="product">
                            <h4>
                                <?php echo $title; ?>
                            </h4>
                            <h5>Best Selling</h5>

                        </div>
                        <div class="product">
                            <h4>
                                <?php echo $title; ?>
                            </h4>
                            <h5>Most Popular</h5>

                        </div>
                    </div>

                </div>
                <div class="report2">
                    <h4>Customers</h4>

                    <img src="../Images/Dummy/Customer.png" alt="">

                </div>


            </div>
            <div class="sales-report">
                <h4>Sales report</h4>
                <img src="../Images/Dummy/column-chart.webp" alt="">


            </div>
            <!-- 
            <div class="top-reports">
                <div class="report">
                    <h3>Clients</h3>
                    <div class="amount"></div>
                </div>
                <div class="report">
                    <h3>Publishers</h3>
                    <div class="amount"></div>
                </div>
                <div class="report">
                    <h3>Authors</h3>
                    <div class="amount"></div>
                </div>
                <div class="report">
                    <h3>Manufacturers</h3>
                    <div class="amount"></div>
                </div>
            </div> -->
        </div>

        <div class="task-panel">
            <!--Calendar-->
            <div class="calendar-container">

                <h3>Calendar</h3>
                <!-- <div id="calendar-container"></div> -->
            </div>
            <!--Pending tasks-->
            <div class="pending-task">
                <h3>Pending tasks</h3>
            </div>
            <!--Notifications-->
            <div class="notifications-container">
                <h3>Notifications</h3>

            </div>
        </div>
    </div>


</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('header.html').then(response => response.text()).then(data => {
        document.getElementById('header-container').innerHTML = data;
    });
    fetch('/Shared Components/calendar.html').then(response => response.text()).then(data => {
        document.getElementById('calendar-container').innerHTML = data;
    });
});
</script>

</html>