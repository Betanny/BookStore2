<?php
include '../Shared Components\logger.php';

// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
//
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





    // Query to get the total_income
    $sql_total_income = "SELECT SUM(total_amount) AS total_income FROM orders";
    $stmt_total_income = $db->query($sql_total_income);
    $total_income_result = $stmt_total_income->fetch(PDO::FETCH_ASSOC);
    $total_income = $total_income_result['total_income'];

    // Query to get the number of books
    $sql_num_books = "SELECT COUNT(*) AS num_books FROM books";
    $stmt_num_books = $db->query($sql_num_books);
    $num_books_result = $stmt_num_books->fetch(PDO::FETCH_ASSOC);
    $num_books = $num_books_result['num_books'];
    global $num_books, $total_income;

    // SQL query to get the number of unique clients
    $sql_num_clients = "SELECT COUNT(DISTINCT client_id) AS num_clients FROM orders";

    // Execute the query to fetch the number of unique clients
    $stmt_num_clients = $db->query($sql_num_clients);
    $num_clients_result = $stmt_num_clients->fetch(PDO::FETCH_ASSOC);
    $num_clients = $num_clients_result['num_clients'];
    global $num_clients;

    // Execute the query to fetch the number of dealers
    $sql_num_dealers = "SELECT COUNT(DISTINCT user_id) as num_dealers FROM users WHERE role='Dealer'";
    $sql_num_dealers = $db->query($sql_num_dealers);
    $num_dealers_result = $sql_num_dealers->fetch(PDO::FETCH_ASSOC);
    $num_dealers = $num_dealers_result['num_dealers'];
    global $num_dealers;





    // SQL query to count orders with each status
    $sql_order_counts = "SELECT status, COUNT(*) AS count FROM orders GROUP BY status";
    $stmt_order_counts = $db->query($sql_order_counts);
    $order_counts = $stmt_order_counts->fetchAll(PDO::FETCH_ASSOC);


    // Initialize arrays to store data for the pie chart
    $status_labels = [];
    $order_data = [];

    // Loop through the fetched order counts and populate the arrays
    foreach ($order_counts as $row) {
        $status_labels[] = $row['status'];
        $order_data[] = $row['count'];
    }

    //sql to get best rated
    $sql_best_rated = "SELECT * FROM books ORDER BY bookrating DESC LIMIT 1";
    // Execute the query to fetch the best-rated book
    $stmt_best_rated = $db->query($sql_best_rated);
    $best_rated_book = $stmt_best_rated->fetch(PDO::FETCH_ASSOC);
    $best_rated_title = $best_rated_book['title'];
    global $best_rated_title;

    $sql_best_selling = "
    SELECT b.title, SUM(o.total_amount) AS total_sales
    FROM public.orders o
    JOIN public.books b ON o.product_id = b.bookid
    GROUP BY b.title
    ORDER BY total_sales DESC
    LIMIT 1
";

    // Execute the query to fetch the best-selling book
    $stmt_best_selling = $db->query($sql_best_selling);
    $best_selling_book = $stmt_best_selling->fetch(PDO::FETCH_ASSOC);
    $best_selling_title = $best_selling_book['title'];
    global $best_selling_title;

    // SQL to get the most popular book
    $sql_most_popular = "
    SELECT b.title, COUNT(*) AS total_orders
    FROM public.orders o
    JOIN public.books b ON o.product_id = b.bookid
    GROUP BY b.title
    ORDER BY total_orders DESC
    LIMIT 1
";

    // Execute the query to fetch the most popular book
    $stmt_most_popular = $db->query($sql_most_popular);
    $most_popular_book = $stmt_most_popular->fetch(PDO::FETCH_ASSOC);
    $most_popular_title = $most_popular_book['title'];
    global $most_popular_title;


    $sqlMonthly = "SELECT EXTRACT(MONTH FROM order_date) AS month, SUM(total_amount) AS total_sales 
    FROM orders 
    WHERE EXTRACT(YEAR FROM order_date) = EXTRACT(YEAR FROM CURRENT_DATE)
    GROUP BY EXTRACT(MONTH FROM order_date)";

    // Prepare and execute the monthly query
    $stmtMonthly = $db->prepare($sqlMonthly);
    $stmtMonthly->execute();

    // Fetch the monthly results into an associative array
    while ($row = $stmtMonthly->fetch(PDO::FETCH_ASSOC)) {
        $month = (int) $row['month'];
        $totalSales = (float) $row['total_sales'];
        // Update salesDataMonthly array
        $salesDataMonthly[$month] = $totalSales;
    }

    // Define your SQL query to fetch yearly sales data from the database
    $sqlYearly = "SELECT EXTRACT(YEAR FROM order_date) AS year, SUM(total_amount) AS total_sales 
   FROM orders 
      GROUP BY EXTRACT(YEAR FROM order_date)";

    // Prepare and execute the yearly query
    $stmtYearly = $db->prepare($sqlYearly);
    $stmtYearly->execute();

    // Fetch the yearly results into an associative array
    while ($row = $stmtYearly->fetch(PDO::FETCH_ASSOC)) {
        $year = (int) $row['year'];
        $totalSales = (float) $row['total_sales'];
        // Update salesDataYearly array
        $salesDataYearly[$year] = $totalSales;
    }



    // Fetch order data from PHP
// Query to get the count of each status
    $sql_order_counts = "SELECT status, COUNT(*) AS count FROM orders GROUP BY status";
    $stmt_order_counts = $db->prepare($sql_order_counts);
    $stmt_order_counts->execute();
    $order_counts = $stmt_order_counts->fetchAll(PDO::FETCH_ASSOC);

    // Initialize counts
    $pending_count = 0;
    $delivered_count = 0;
    $declined_count = 0;

    // Store the counts in variables
    foreach ($order_counts as $order_count) {
        switch ($order_count['status']) {
            case 'Pending':
                $pending_count = $order_count['count'];
                break;
            case 'Delivered':
                $delivered_count = $order_count['count'];
                break;
            case 'Declined':
                $declined_count = $order_count['count'];
                break;
        }
    }
    $total_status_count = $pending_count + $delivered_count + $declined_count;
    $pending_percentage = round(($pending_count / $total_status_count) * 100);
    $delivered_percentage = round(($delivered_count / $total_status_count) * 100);
    $declined_percentage = round(($declined_count / $total_status_count) * 100);


    // Query to fetch notifications for the user
    $sql_notifications = "SELECT notifications.*, users.email
 FROM public.notifications 
 JOIN users ON notifications.sender_id = users.user_id 
 WHERE notifications.recipient_id = :user_id";
    $stmt_notifications = $db->prepare($sql_notifications);
    $stmt_notifications->execute(['user_id' => $user_id]);
    $notifications = $stmt_notifications->fetchAll(PDO::FETCH_ASSOC);



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
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">
    <style>
    .chart {
        width: 200px;
        height: 200px;
        margin: 10px auto;
        border-radius: 50%;
        background: conic-gradient(green 0% <?php echo $delivered_percentage . "%"; ?>,
                orange <?php echo $delivered_percentage . "%"; ?> <?php echo $pending_percentage + $delivered_percentage . "%"; ?>,
                red <?php echo $pending_percentage + $delivered_percentage . "%"?> 100%);
    }
    </style>
</head>

<body>
    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components\headerdispatcher.php"
        ?>
    <div class="dashboard-container">
        <div class="reports-container">
            <div class="welcome-container">
                <h4>Welcome back Manager <i class="fa-solid fa-hand"></i>
                </h4>
            </div>
            <div class="general-report">
                <div class="report">
                    <i class="fa-solid fa-book"></i>
                    <div class="content">
                        <p>
                            <?php echo $num_books; ?>
                        </p>

                        <h4>Books</h4>
                    </div>
                </div>

                <div class="report">
                    <i class="fa-solid fa-users"></i>
                    <div class="content">

                        <h4>clients </h4>
                        <p>
                            <?php echo $num_clients; ?>
                        </p>
                    </div>
                </div>
                <div class="report">
                    <i class="fa-solid fa-users"></i>
                    <div class="content">
                        <h4>
                            Dealers
                        </h4>
                        <p>
                            <?php echo (int) $num_dealers; ?>
                        </p>
                    </div>
                </div>
                <div class="report">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    <div class="content">
                        <h4>
                            Total Income<br>
                            <h5>ksh</h5>
                        </h4>

                        <p>
                            <?php echo (int) $total_income; ?>
                        </p>
                    </div>
                </div>
            </div>







            <div class="sales-report">
                <div class="salesreport-header">
                    <h4>Sales report</h4>
                    <div class="filter-dropdown">
                        <select id="filter" class="filter-bar" onchange="updateGraph();" placeholder="sort">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>
                <div class="bar-graph">


                    <div id="monthly-graph" style="display: block;">
                        <?php if (empty($salesDataMonthly || $salesDataYearly)): ?>
                        <h2>Nothing to display yet</h2>
                        <?php else: ?>
                        <?php
                            $months = [
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December'
                            ];
                            foreach ($salesDataMonthly as $month => $totalSales): ?>
                        <div class="bar-container">
                            <div class="bar-label"><?php echo $months[$month]; ?>:</div>
                            <div class="bar-wrapper">
                                <div class="bar" style="width: <?php echo $totalSales / 800; ?>px;"></div>
                                <div class="bar-value">ksh <?php echo $totalSales; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="yearly-graph" style="display: none;">
                        <?php foreach ($salesDataYearly as $year => $totalSales): ?>
                        <div class="bar-container">
                            <div class="bar-label"><?php echo $year; ?>:</div>
                            <div class="bar-wrapper">
                                <div class="bar" style="width: <?php echo $totalSales / 2000; ?>px;">
                                </div>
                                <div class="bar-value">ksh
                                    <?php echo $totalSales; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                </div>
                <div class="totalcomm">
                    Total Commission earned: <?php echo $totalSales * 0.02; ?>
                </div>

            </div>

            <div class="products-report-container">
                <h4>Product Report</h4>
                <div class="products">
                    <div class="product">
                        <h4>
                            <?php echo $best_rated_title; ?>
                        </h4>
                        <h5>Highest rated</h5>

                    </div>
                    <div class="product">
                        <h4>
                            <?php echo $best_selling_title; ?>
                        </h4>
                        <h5>Best Selling</h5>

                    </div>
                    <div class="product">
                        <h4>
                            <?php echo $most_popular_title; ?>
                        </h4>
                        <h5>Most Popular</h5>

                    </div>
                </div>



            </div>



        </div>

        <div class="reports-container2">
            <div class="pie-chart-container">
                <h4>Book Sales</h4>
                <div class="chart"></div>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color color-delivered"></div>
                        <span>Delivered: <?php echo $delivered_count; ?>
                            (<?php echo round($delivered_percentage, 2); ?>%)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color color-pending"></div>
                        <span>Pending: <?php echo $pending_count; ?>
                            (<?php echo round($pending_percentage, 2); ?>%)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color color-declined"></div>
                        <span>Declined: <?php echo $declined_count; ?>
                            (<?php echo round($declined_percentage, 2); ?>%)</span>
                    </div>
                </div>
            </div>
            <div class="pendingtasks-container">
                <h4>Notifications</h4>
                <div class="tasks-container">
                    <?php if (empty($notifications)): ?>
                    <p>You have no notifications</p>
                    <?php else: ?>
                    <div class="task">

                        <?php foreach ($notifications as $notification): ?>
                        <div class="admin-notification"
                            data-email="<?php echo htmlspecialchars($notification['email']); ?>"
                            data-message="<?php echo htmlspecialchars($notification['notification_message']); ?>"
                            onclick="openNotification(this);">
                            <h5><?php echo htmlspecialchars($notification['email']); ?></h5>
                            <p><?php echo htmlspecialchars($notification['notification_message']); ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>


                </div>

            </div>

        </div>
    </div>


</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function updateGraph() {
    const filter = document.getElementById('filter').value;
    const monthlyGraph = document.getElementById('monthly-graph');
    const yearlyGraph = document.getElementById('yearly-graph');

    if (filter === 'monthly') {
        monthlyGraph.style.display = 'block';
        yearlyGraph.style.display = 'none';
    } else if (filter === 'yearly') {
        monthlyGraph.style.display = 'none';
        yearlyGraph.style.display = 'block';
    }
}

document.addEventListener("DOMContentLoaded", function() {
    // Data for pie chart
    const data = {
        pending: <?php echo $pending_count; ?>,
        delivered: <?php echo $delivered_count; ?>,
        declined: <?php echo $declined_count; ?>
    };

    // Calculate total
    const total = data.pending + data.delivered + data.declined;

    // Calculate percentages
    const pendingPercentage = (data.pending / total) * 100;
    const deliveredPercentage = (data.delivered / total) * 100;
    const declinedPercentage = (data.declined / total) * 100;

    console.log(
        `Pending: ${pendingPercentage}%, Delivered: ${deliveredPercentage}%, Declined: ${declinedPercentage}%`
    );

    // Apply conic gradient dynamically
    document.querySelector('.chart').style.background = `conic-gradient(
        green 0% pendingPercentage%,
        orange pendingPercentage% calc(pendingPercentage + deliveredPercentage)%,
        red calc(pendingPercentage + deliveredPercentage)% 100%
    )`;
});
</script>

</html>