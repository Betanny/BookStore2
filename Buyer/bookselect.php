<?php
require_once '../Shared Components/dbconnection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Registration/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $table_name = 'clients';

    $sql = "SELECT * FROM $table_name WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $clientid = $data['client_id'];

        $ordersql = "SELECT orders.*, books.title AS title 
                        FROM orders 
                        INNER JOIN books ON orders.product_id = books.bookid
                        WHERE orders.client_id = ? AND status = 'Delivered'";

        $ordersstmt = $db->prepare($ordersql);
        $ordersstmt->execute([$clientid]);
        $orders = $ordersstmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="buyer.css">
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="/Registration/Stylesheet.css">
    <link rel="stylesheet" href="/Home/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Select Book</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="file-path"></div>

    <div class="book-selection">
        <div class="allProducts-container">
            <div class="table">
                <h4>Which book would you like to review?<br>Please select one of the following</h4>
                <div class="row-header1">
                    <div class="ordername-cell">Title</div>
                </div>
                <div class="order-rows">
                    <?php foreach ($orders as $order): ?>
                    <div class="row" onclick="setpid(<?php echo $order['product_id']; ?>)">
                        <div class=" ordername-cell">
                            <?php echo $order['title']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('header.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('header-container').innerHTML = data;
            });
    });

    function setpid(productId) {
        window.location.href = "bookreview.php?product_id=" + productId;
    }
    </script>
</body>

</html>