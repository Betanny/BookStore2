<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
//
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
                            Total Income
                        </h4>
                        <p>
                            <?php echo (int) $total_income; ?>
                        </p>
                    </div>
                </div>
            </div>







            <div class="sales-report">
                <h4>Sales report</h4>
                <img src="../Images/Dummy/column-chart.webp" alt="">

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
                <canvas id="ordersChart"></canvas>
            </div>
            <div class="pendingtasks-container">
                <h4>Notifications</h4>
                <div class="tasks-container">

                    <div class="task">
                        <h4>Notification</h4>
                        <h5>This is a notifiction</h5>

                    </div>

                </div>

            </div>

        </div>
    </div>


</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetch('header.php')
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
    document.addEventListener('DOMContentLoaded', function () {
        <?php
        // Fetch order data from PHP
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
        ?>

        // Calculate total ord ers
        const totalOrders = <?php echo array_sum($order_data); ?>;

        // Pie chart data
        const data = {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($order_data); ?>,
                backgroundColor: ['#44b89d', '#800020',
                    '#FFA500'
                ],
                hoverBackgroundColor: ['#44b89d', '#800020',
                    '#FFA500'
                ],
            }]
        };

        // Chart options
        const options = {
            responsive: true,
            cutoutPercentage: 60, // Determines the size of the hole in the middle
            legend: {
                position: 'bottom'
            },
            title: {
                display: true,
                text: `Total Orders: ${totalOrders}`
            }
        };

        // Get the canvas element
        const ctx = document.getElementById('ordersChart').getContext('2d');

        // Create the pie chart
        const ordersChart = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: options
        });
    });
</script>

</html>