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
    $query = isset($_GET['query']) ? $_GET['query'] : '';
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';

    // Define your SQL query to fetch data from the books and orders table
    if ($query) {
        // Search query to fetch matching books
        $sql = "SELECT * FROM (
                    SELECT DISTINCT ON (b.bookid) b.bookid, b.title, b.isbn, b.subject, b.bookrating, b.grade, 
                    COUNT(o.product_id) AS copies_bought,
                    SUM(o.total_amount) AS total_values_generated
                    FROM books b
                    LEFT JOIN orders o ON b.bookid = o.product_id
                    WHERE LOWER(b.title) LIKE LOWER(:query) 
                       OR LOWER(b.grade) LIKE LOWER(:query) 
                       OR LOWER(b.author) LIKE LOWER(:query) 
                       OR LOWER(b.publisher) LIKE LOWER(:query)
                    GROUP BY b.bookid, b.title, b.isbn, b.subject, b.bookrating, b.grade
                ) AS distinct_books";
        $params = ['query' => '%' . $query . '%'];
    } else {
        // Non-search query
        $sql = "SELECT b.bookid, b.title, b.isbn, b.subject, b.bookrating, b.grade, 
                COUNT(o.product_id) AS copies_bought, 
                SUM(o.total_amount) AS total_values_generated
                FROM books b
                LEFT JOIN orders o ON b.bookid = o.product_id
                GROUP BY b.bookid, b.title, b.isbn, b.subject, b.bookrating, b.grade";
        $params = [];
    }

    // Prepare and execute the query
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    // Fetch the results into an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Define the isKICDApproved function
    function isKICDApproved($title, $grade, $appbooks)
    {
        foreach ($appbooks as $book) {
            preg_match('/\d+/', $book['grade'], $matches);
            $dbGrade = $matches[0];

            if ($book['title'] === $title && $book['grade'] == $dbGrade) {
                return true;
            }
        }
        return false;
    }

    // Getting approved books from the approved books table
    $appbookrecsql = "SELECT * FROM kicdapprovedbooks";
    $appbookrecomendationstmt = $db->query($appbookrecsql);
    $appbooks = $appbookrecomendationstmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter books based on the selected filter
    if ($filter == 'KICD') {
        $products = array_filter($products, function ($product) use ($appbooks) {
            return isset($product['grade']) && isKICDApproved($product['title'], $product['grade'], $appbooks);
        });
    } elseif ($filter == 'Non-KICD') {
        $products = array_filter($products, function ($product) use ($appbooks) {
            return !isset($product['grade']) || !isKICDApproved($product['title'], $product['grade'], $appbooks);
        });
    } else {
        $books = $products; // If no specific filter is applied, show all books
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
                    <form action="" method="GET">
                        <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
                        <select id="genre-filter" class="filter-bar" name="filter" onchange="this.form.submit()">
                            <option value="All" <?php if ($filter == 'All')
                                echo 'selected'; ?>>All</option>
                            <option value="KICD" <?php if ($filter == 'KICD')
                                echo 'selected'; ?>>KICD approved
                            </option>
                            <option value="Non-KICD" <?php if ($filter == 'Non-KICD')
                                echo 'selected'; ?>>Non KICD
                                approved</option>
                        </select>
                    </form>
                </div>

                <div class="search-container">
                    <form action="" method="GET">
                        <input type="text" name="query" id="search-input" class="search-bar" placeholder="Search..."
                            value="<?php echo htmlspecialchars($query); ?>">
                        <button class="search-button" type="submit"><i
                                class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
                <!-- <div class="addproductsbutton">
                    <button type="submit" class="add-button">Add <div class="icon-cell">
                            <i class="fa-solid fa-plus"></i>
                        </div></button>

                </div> -->

            </div>
        </div>

        <div class="allProducts-container">
            <div class="table">
                <div class="row-header">
                    <div class="name-cell">Title</div>
                    <div class="bigger-cell">ISBN</div>
                    <div class="bigger-cell">Subject</div>
                    <div class="bigger-cell">Rating</div>
                    <div class="bigger-cell">Copies Bought</div>
                    <div class="bigger-cell">Total Income</div>


                </div>
                <div class="rows">
                    <!-- Adding the product items -->
                    <?php if (!empty($products)): ?>

                    <?php foreach ($products as $product): ?>
                    <div class="row">
                        <!-- <input type="checkbox" class="checkbox" name="product_id" value="?php $product['bookid']; ?>"> -->
                        <div class=" name-cell">
                            <?php echo $product['title']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $product['isbn']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $product['subject']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $product['bookrating']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $product['copies_bought']; ?>
                        </div>
                        <div class="bigger-cell">
                            <?php echo $product['copies_bought'] == 0 ? '---' : $product['total_values_generated']; ?>
                        </div>

                        <!-- Add the icon with a class to handle click events -->
                        <!-- <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash toggle-icon"></i>
                        </div> -->

                        <!-- <div class="icon-cell">
                            <a href="#" class="delete-link" data-table="books"
                                data-pk="<php echo $product['bookid']; ?>" data-pk-name="bookid">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div> -->
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <h2>No Products with that keyword.</h2>
                </div>
                <?php endif; ?>

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