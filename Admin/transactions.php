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
    // Define SQL query to fetch transactions data
    $sql = "SELECT 
                transactions.transaction_id,
                ROW_NUMBER() OVER() AS serialno,
                transactions.order_id,
                CASE 
                    WHEN clients.client_type = 'Individual' THEN CONCAT(clients.first_name, ' ', clients.last_name)
                    ELSE clients.organization_name
                END AS client_name,
                transactions.amount,
                transactions.transaction_type,
                transactions.payment_method,
                transactions.payment_number,
                DATE(transactions.transaction_date) AS transaction_date
            FROM 
                transactions
            JOIN 
                clients ON clients.client_id = transactions.client_id";

    // Prepare and execute the query
    $stmt = $db->prepare($sql);
    $stmt->execute();

    // Fetch the results into an associative array
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['export']) && $_GET['export'] === 'true') {

        $filename = 'transactions_report.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write CSV headers
        fputcsv($output, array_keys($transactions[0]));

        // Write transaction data to CSV
        foreach ($transactions as $transaction) {
            fputcsv($output, $transaction);
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
    <link rel="stylesheet" href="/Registration/Stylesheet.css">
    <link rel="stylesheet" href="/Seller/seller.css">

    <title>Transactions</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="viewproducts-container">
        <div class="viewproducts-header">
            <!-- <h4>Orders</h4> -->
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
                <div class="row-header" style="padding: 20px 60px 20px 0;margin-left:25px;margin-right:auto;">
                    <div class="cell">No.</div>
                    <div class="bigger-cell2">Client Name</div>
                    <div class="cell">Order No.</div>
                    <div class="bigger-cell">Transaction Type</div>
                    <div class="bigger-cell">Payment Methods</div>
                    <div class="bigger-cell">Payment Number</div>
                    <div class="cell">Amount</div>
                    <div class="bigger-cell" style="text-align: center;">Transaction Date</div>
                </div>
                <div class="order-rows">
                    <!-- Adding the order items -->
                    <?php foreach ($transactions as $transaction): ?>
                    <div class="row">
                        <div class="cell">
                            <?php echo $transaction['serialno'];
                                ?>
                        </div>
                        <div class="bigger-cell2">
                            <?php echo $transaction['client_name']; ?>
                        </div>
                        <div class="cell">
                            <?php echo $transaction['order_id'];
                                ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $transaction['transaction_type']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $transaction['payment_method']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $transaction['payment_number']; ?>
                        </div>
                        <div class="cell">
                            <?php echo $transaction['amount']; ?>
                        </div>
                        <div class="bigger-cell" style="text-align: center;">
                            <?php echo $transaction['transaction_date']; ?>
                        </div>


                        <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash"></i>
                        </div>

                        <div class="icon-cell">
                            <a href="#" class="delete-link" data-table="transactions"
                                data-pk="<?php echo $transaction['transaction_id']; ?>" data-pk-name=" transaction_id">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>





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

document.addEventListener("DOMContentLoaded", function() {
    var exportButton = document.getElementById('exportButton');
    exportButton.addEventListener('click', function() {
        // Update the href attribute of the export button with the desired URL
        var currentHref = window.location.href;
        var exportUrl = currentHref.includes('?export=true') ? currentHref : currentHref +
            '?export=true';
        exportButton.querySelector('a').setAttribute('href', exportUrl);
    });
});

document.addEventListener("DOMContentLoaded", function() {
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
                primaryKey +
                '&pk_name=' + pkName, true);
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

</html>