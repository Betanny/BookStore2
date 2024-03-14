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
    global $first_name, $full_name;


    // SQL query to get the best-rated book for the current seller
    $sql_best_rated = "SELECT * FROM books WHERE seller_id = $user_id ORDER BY bookrating DESC LIMIT 1";

    // SQL query to get the number of books and total income for the current seller
    $sql_books_and_income = "SELECT COUNT(*) AS num_books, SUM(books.price) AS total_income FROM books INNER JOIN orders ON books.bookid = orders.product_id WHERE orders.seller_id = $user_id";



    // Execute the query to fetch the best-rated book
    $stmt_best_rated = $db->query($sql_best_rated);
    $best_rated_book = $stmt_best_rated->fetch(PDO::FETCH_ASSOC);
    $title = $best_rated_book['title'];
    global $title;

    // Execute the query to fetch the number of books AND total income
    $stmt_books_and_income = $db->query($sql_books_and_income);
    $result_books_and_income = $stmt_books_and_income->fetch(PDO::FETCH_ASSOC);
    $num_books = $result_books_and_income['num_books'];
    $total_income = $result_books_and_income['total_income'];
    global $num_books, $total_income;

    // SQL query to get the number of unique clients
    $sql_num_clients = "SELECT COUNT(DISTINCT client_id) AS num_clients FROM orders WHERE seller_id = $user_id";

    // Execute the query to fetch the number of unique clients
    $stmt_num_clients = $db->query($sql_num_clients);
    $num_clients_result = $stmt_num_clients->fetch(PDO::FETCH_ASSOC);
    $num_clients = $num_clients_result['num_clients'];
    global $num_clients;





    // SQL query to count orders with each status
    $sql_order_counts = "SELECT status, COUNT(*) AS count FROM orders WHERE seller_id = $user_id GROUP BY status";
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


    //pending tasks
    $sql_pending_orders = "SELECT books.title AS book_title, orders.delivery_date 
    FROM orders 
    INNER JOIN books ON orders.product_id = books.bookid 
    WHERE orders.seller_id = $user_id AND orders.status = 'Pending'";
    $stmt_pending_orders = $db->query($sql_pending_orders);

    // Check if there are pending orders
    if ($stmt_pending_orders) {
        $pending_orders = $stmt_pending_orders->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $pending_orders = array(); // Set an empty array if there are no pending orders
    }
    global $pending_orders;



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
                    <?php echo $first_name; ?>
                    <i class="fa-solid fa-hand"></i>
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
                <div class="report">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    <div class="content">
                        <h4>
                            Total Income
                        </h4>
                        <p>110</p>
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



        </div>

        <div class="reports-container2">
            <div class="pie-chart-container">
                <h4>Book Sales</h4>
                <canvas id="ordersChart"></canvas>
            </div>
            <div class="pendingtasks-container">
                <h4>Product Report</h4>
                <div class="tasks-container">

                    <?php foreach ($pending_orders as $order): ?>
                        <div class="task">
                            <h4>
                                <?php echo $order['book_title']; ?>
                            </h4>
                            <h5>Delivery Date:
                                <?php echo $order['delivery_date']; ?>
                            </h5>
                        </div>
                    <?php endforeach; ?>

                </div>

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
    </div>


</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetch('header.html').then(response => response.text()).then(data => {
            document.getElementById('header-container').innerHTML = data;
        });

        fetch('/Shared Components/calendar.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById('calendar').innerHTML = data;
                // Optionally, include any additional JavaScript logic for the calendar here
            });
    });
    document.addEventListener('DOMContentLoaded', function () {
        <?php
        // Fetch order data from PHP
        $sql_order_counts = "SELECT status, COUNT(*) AS count FROM orders WHERE seller_id = $user_id GROUP BY status";
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