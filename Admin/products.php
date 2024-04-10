<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
// if (session_status() === PHP_SESSION_NONE) {
session_start();
// }
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
                        <!-- <input type="checkbox" class="checkbox" name="product_id" value="?php $product['bookid']; ?>"> -->
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
                        <!-- Add the icon with a class to handle click events -->
                        <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash toggle-icon"></i>
                        </div>

                        <div class="icon-cell">
                            <a href="#" class="delete-link" data-table="books"
                                data-pk="<?php echo $product['bookid']; ?>" data-pk-name="bookid">
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
<?php foreach ($products as $product): ?>
console.log(<?php echo json_encode($product['bookid']); ?>);
<?php endforeach; ?>


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

//hide view button feature

// Get the icon element
const hideviewicon = document.querySelector('.toggle-icon');

// Add a click event listener
icon.addEventListener('click', () => {
    // Toggle visibility (hide/show)
    hideviewicon.classList.toggle('hidden');
});
</script>

</html>