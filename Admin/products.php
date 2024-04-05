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
    // Define your SQL query to fetch data from the books and orders table
    $sql = "SELECT b.bookid, b.title, b.isbn, b.subject, b.bookrating, 
               COUNT(o.product_id) AS copies_bought, 
               SUM(o.total_amount) AS total_values_generated
        FROM books b
        LEFT JOIN orders o ON b.bookid = o.product_id
        GROUP BY b.bookid, b.title, b.isbn, b.subject, b.bookrating";

    // Prepare and execute the query
    $stmt = $db->prepare($sql);
    $stmt->execute();

    // Fetch the results into an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);


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

    <title>Document</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="viewproducts-container">
        <div class="viewproducts-header">
            <h4>All products</h4>

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
                <div class="addproductsbutton">
                    <button type="submit" class="add-button">Add <div class="icon-cell">
                            <i class="fa-solid fa-plus"></i>
                        </div></button>

                </div>

            </div>
        </div>

        <div class="allProducts-container">
            <div class="table">
                <div class="row-header">
                    <div class="name-cell">Title</div>
                    <div class="cell">ISBN</div>
                    <div class="bigger-cell">Subject</div>
                    <div class="cell">Rating</div>
                    <div class="cell">Copies Bought</div>
                    <div class="cell">Total Values</div>


                </div>
                <div class="rows">
                    <!-- Adding the product items -->
                    <?php foreach ($products as $product): ?>
                    <div class="row">
                        <input type="checkbox" class="checkbox" name="product_id" value="<?php $product['bookid']; ?>">
                        <div class=" name-cell">
                            <?php echo $product['title']; ?>
                        </div>
                        <div class="cell">
                            <?php echo $product['isbn']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $product['subject']; ?>
                        </div>
                        <div class="cell">
                            <?php echo $product['bookrating']; ?>
                        </div>
                        <div class="cell">
                            <?php echo $product['copies_bought']; ?>
                        </div>
                        <div class="cell">
                            <?php echo $product['total_values_generated']; ?>
                        </div>
                        <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash"></i>
                        </div>
                        <div class="icon-cell">
                            <i class="fa-solid fa-trash"></i>
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
<?php foreach ($products as $product): ?>
console.log(<?php echo json_encode($product['bookid']); ?>);
<?php endforeach; ?>
</script>

</html>