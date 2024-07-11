<?php
include '../Shared Components\logger.php';

// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();

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
                    WHERE (LOWER(b.title) LIKE LOWER(:query) 
                       OR LOWER(b.grade) LIKE LOWER(:query) 
                       OR LOWER(b.author) LIKE LOWER(:query) 
                       OR LOWER(b.publisher) LIKE LOWER(:query))
                                              AND b.view_status IS NULL

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
                WHERE b.view_status IS NULL
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


    if (isset($_GET['export']) && $_GET['export'] === 'true') {
        writeLog($db, "User has extracted a copy of the products ", "INFO", $user_id);

        $filename = 'Products_report.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write CSV headers
        fputcsv($output, array_keys($books[0]));

        // Write transaction data to CSV
        foreach ($products as $product) {
            fputcsv($output, $product);
        }

        // Close output stream
        fclose($output);
        exit();
    }


    if (isset($_GET['action']) && $_GET['action'] === 'hide_book' && isset($_GET['bookid'])) {
        $bookid = $_GET['bookid'];

        $deletebookid = $_GET['bookid'];

        try {
            // Prepare the SQL statement to update the view_status to "hidden"
            $sql = "UPDATE books SET view_status = 'hidden' WHERE bookid = :bookid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':bookid', $bookid, PDO::PARAM_INT);
            writeLog($db, "Manager has deleted book ID: " . $bookid, "INFO", $user_id);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Book status updated to hidden successfully.";
            } else {
                echo "Failed to update book status.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        exit(); // End script execution after handling the hide action
    }


    if (isset($_GET['bookid'])) {

        $bookid = $_GET['bookid'];
        $selected_book_sql = "SELECT * FROM books WHERE bookid = :bookid";
        $selected_book_stmt = $db->prepare($selected_book_sql);
        $selected_book_stmt->bindParam(':bookid', $bookid);
        $selected_book_stmt->execute();
        $selected_book = $selected_book_stmt->fetch(PDO::FETCH_ASSOC);
        global $selected_book;

    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $bookid = $_POST['bookid'];
        $booktitle = $_POST['booktitle'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $price = $_POST['Price'];
        $minnum = $_POST['minnum'];
        $priceinbulk = $_POST['Priceinbulk'];
        $genre = $_POST['genre'];
        $language = $_POST['Language'];
        $grade = $_POST['grade'];
        $edition = $_POST['Edition'];
        $subject = $_POST['Subject'];
        $pages = $_POST['Pages'];
        $details = $_POST['details'];

        // Update database
        // Define your SQL query to update the book details
        $sql = "UPDATE books 
                    SET title = :title, 
                        author = :author, 
                        publisher = :publisher, 
                        price = :price, 
                        mininbulk = :minnum, 
                        priceinbulk = :priceinbulk, 
                        genre = :genre, 
                        language = :language, 
                        grade = :grade, 
                        edition = :edition, 
                        subject = :subject, 
                        pages = :pages, 
                        details = :details
                        -- Add more fields here
                    WHERE bookid = :bookid";

        // Prepare and execute the query
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':title' => $booktitle,
            ':author' => $author,
            ':publisher' => $publisher,
            ':price' => $price,
            ':minnum' => $minnum,
            ':priceinbulk' => $priceinbulk,
            ':genre' => $genre,
            ':language' => $language,
            ':grade' => $grade,
            ':edition' => $edition,
            ':subject' => $subject,
            ':pages' => $pages,
            ':details' => $details,
            ':bookid' => $bookid
        ]);
        writeLog($db, "Dealer has edited their product ", "INFO", $user_id);

        // Redirect to a success page or reload the current page
        header("Location: products.php");
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
    <link rel="stylesheet" href="/Seller/seller.css">
    <link rel="stylesheet" href="/Registration/Stylesheet.css">



    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">
</head>

<body>
    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components/headerdispatcher.php"
        ?>
    <div class="viewproducts-container">
        <div class="viewproducts-header">
            <h4>All products</h4>

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
                    <div class="bigger-cell" style="text-align:left;">Rating</div>
                    <div class="bigger-cell">Copies Bought</div>
                    <div class="bigger-cell">Total Income</div>
                    <div class="small-cell"></div>
                    <div class="small-cell"></div>



                </div>
                <div class="rows">
                    <!-- Adding the product items -->
                    <?php if (!empty($products)): ?>

                    <?php foreach ($products as $product): ?>
                    <div class="row">
                        <!-- <input type="checkbox" class="checkbox" name="product_id" value="?php $product['bookid']; ?>">
                    -->
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

                        <div class="icon-cell">
                            <i class="fa-solid fa-pen" onclick="editProduct(<?php echo $product['bookid']; ?>)"></i>
                        </div>
                        <!-- Add the icon with a class to handle click events -->
                        <!-- <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash toggle-icon"></i>
                        </div> -->

                        <div class="icon-cell">
                            <a href="#" class="delete-link" data-table="books"
                                data-pk="<?php echo $product['bookid']; ?>" data-pk-name=" bookid">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
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
    <div class="modal" id="delete-modal" style="display:none;">

        <div class="modal-content">
            <h1>Are you sure you want to delete?</h1>

            <div class="modal-buttons">
                <button class="button" type="button" onclick="cancelDelete();">Cancel</button>
                <button class="button" type="button" id="confirm-delete-button">Delete</button>
            </div>
        </div>
    </div>
    <div class="modal" id="editproducts-modal" style="display:none;">
        <form action="#" method="post">
            <!-- ?php if (isset($selected_book)): ?> -->

            <input type="hidden" name="bookid" value="<?php echo $selected_book['bookid']; ?>">

            <div class="modal-header">
                <h2 class="modal-title">Edit Product</h2>
                <div class="close">
                    <i class="fa-solid fa-xmark" onclick="cancel();"></i>
                </div>
            </div>
            <div class="modal-content">
                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="BookTitle">Book Title</label class="no-asterisk">
                        <input type="text" class="inputfield" name="booktitle"
                            value="<?php echo $selected_book['title']; ?>" readonly />
                    </div>
                </div>
                <div class="two-forms">
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Author">Author</label class="no-asterisk">
                            <input type="text" class="inputfield" name="author"
                                value="<?php echo $selected_book['author']; ?>" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Publisher">Publisher</label class="no-asterisk">
                            <input type="text" class="inputfield" name="publisher"
                                value="<?php echo $selected_book['publisher']; ?>" readonly />
                        </div>
                    </div>
                </div>
                <div class="two-forms">
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="ISBN">ISBN</label class="no-asterisk">
                            <input type="text" class="inputfield" name="isbn"
                                value="<?php echo $selected_book['isbn']; ?>" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Price">Price</label class="no-asterisk">
                            <input type="text" class="inputfield" name="Price"
                                value="<?php echo $selected_book['price']; ?>" readonly />
                        </div>
                    </div>
                </div>
                <div class="two-forms">
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="minnum">Minimum number in bulk</label class="no-asterisk">
                            <input type="text" class="inputfield" name="minnum"
                                value="<?php echo $selected_book['mininbulk']; ?>" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Priceinbulk">Price in bulk</label class="no-asterisk">
                            <input type="text" class="inputfield" name="Priceinbulk"
                                value="<?php echo $selected_book['priceinbulk']; ?>" readonly />
                        </div>
                    </div>
                </div>

                <div class="two-forms">
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="genre">Genre</label class="no-asterisk">
                            <input type="text" class="inputfield" name="genre"
                                value="<?php echo $selected_book['genre']; ?>" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Language">Language</label class="no-asterisk">
                            <input type="text" class="inputfield" name="Language"
                                value="<?php echo $selected_book['language']; ?>" readonly />
                        </div>
                    </div>
                </div>
                <div class="two-forms">
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="grade">Grade</label class="no-asterisk">
                            <input type="text" class="inputfield" name="grade"
                                value="<?php echo str_replace('_', ' ', $selected_book['grade']); ?>" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Edition">Edition</label class="no-asterisk">
                            <input type="text" class="inputfield" name="Edition"
                                value="<?php echo $selected_book['edition']; ?>" readonly />
                        </div>
                    </div>
                </div>
                <div class="two-forms">
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Subject">Subject</label class="no-asterisk">
                            <input type="text" class="inputfield" name="Subject"
                                value="<?php echo $selected_book['subject']; ?>" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Pages">Pages</label class="no-asterisk">
                            <input type="text" class="inputfield" name="Pages"
                                value="<?php echo $selected_book['pages']; ?>" readonly />
                        </div>
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="details"> Book Description</label class="no-asterisk">
                        <textarea class="inputfield" name="details" readonly
                            style="height: 150px;"><?php echo $selected_book['details']; ?></textarea>

                    </div>
                </div>
                <!-- ?php endif; ?> -->

                <div class="modal-buttons">
                    <button class="button" type="button" onclick="cancel();">Cancel</button>
                    <button class="button" type="submit">Save Changes</button>

                </div>
            </div>



        </form>
    </div>


</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var exportButton = document.getElementById('exportButton');
    exportButton.addEventListener('click', function() {
        // Update the href attribute of the export button with the desired URL
        var currentHref = window.location.href;
        var exportUrl = currentHref.includes('?export=true') ? currentHref : currentHref +
            '?export=true';
        exportButton.querySelector('a').setAttribute('href', exportUrl);
    });
    <?php if (isset($_GET['bookid'])): ?>
    // If bookid is set, display the modal
    document.getElementById("editproducts-modal").style.display = "block";
    <?php endif; ?>
});

function editProduct(bookId) {
    // Redirect to the edit page with the book ID as a query parameter
    window.location.href = 'products.php?bookid=' + bookId;
    console.log(bookId);

    // Get the modal
    var modal = document.getElementById("editproducts-modal");
    modal.style.display = "block";

}

function cancel() {
    window.location.href = 'products.php';
}
<?php foreach ($products as $product): ?>
console.log(<?php echo json_encode($product['bookid']); ?>);
<?php endforeach; ?>


// document.addEventListener("DOMContentLoaded", function() {
//     // Get all elements with the class "delete-link"
//     var deleteLinks = document.querySelectorAll('.delete-link');

//     // Loop through each delete link
//     deleteLinks.forEach(function(link) {
//         // Add click event listener to each delete link
//         link.addEventListener('click', function(event) {
//             // Prevent the default behavior (i.e., following the href)
//             event.preventDefault();

//             // Get the table name, primary key column name, and primary key value from the data attributes
//             var tableName = link.getAttribute('data-table');
//             var primaryKey = link.getAttribute('data-pk');
//             var pkName = link.getAttribute('data-pk-name');

//             // Perform AJAX request to the delete script
//             var xhr = new XMLHttpRequest();
//             xhr.open('GET', '/Shared Components/delete.php?table=' + tableName + '&pk=' +
//                 primaryKey +
//                 '&pk_name=' + pkName, true);
//             xhr.onload = function() {
//                 if (xhr.status === 200) {
//                     // Handle successful deletion (if needed)
//                     // For example, you can remove the deleted row from the DOM
//                     link.parentElement.parentElement.remove();
//                 } else {
//                     // Handle error (if needed)
//                     console.error('Error:', xhr.statusText);
//                 }
//             };
//             xhr.onerror = function() {
//                 // Handle network errors (if needed)
//                 console.error('Request failed');
//             };
//             xhr.send();
//         });
//     });
// });
let tableName, primaryKey, pkName, deleteLink;

document.addEventListener("DOMContentLoaded", function() {
    // Get all elements with the class "delete-link"
    var deleteLinks = document.querySelectorAll('.delete-link');

    // Loop through each delete link
    deleteLinks.forEach(function(link) {
        // Add click event listener to each delete link
        link.addEventListener('click', function(event) {
            // Prevent the default behavior (i.e., following the href)

            // Get the table name, primary key column name, and primary key value from the data attributes
            tableName = link.getAttribute('data-table');
            primaryKey = link.getAttribute('data-pk');
            pkName = link.getAttribute('data-pk-name');
            deleteLink = link;
            var row = link.closest('.row'); // Get the closest row element

            event.preventDefault();
            document.getElementById('delete-modal').style.display = 'block';

        });
    });
});

document.getElementById('confirm-delete-button').addEventListener('click', function() {
    confirmDelete(tableName, primaryKey, pkName, deleteLink);
});

function confirmDelete(tableName, primaryKey, pkName, link) {
    console.log(pkName)
    var xhr = new XMLHttpRequest();
    xhr.open('GET', window.location.pathname + '?action=hide_book&bookid=' + primaryKey, true);
    console.log(window.location.pathname)

    xhr.onload = function() {
        if (xhr.status === 200) {
            // Handle successful update
            link.parentElement.parentElement.remove();
            console.log("Book status updated to hidden");
        } else {
            // Handle error
            console.error('Error:', xhr.statusText);
        }
    };
    xhr.onerror = function() {
        // Handle network errors
        console.error('Request failed');
    };
    xhr.send();
    // document.getElementById('delete-modal').style.display = 'none';
    location.reload();

}

function cancelDelete() {
    document.getElementById('delete-modal').style.display = 'none';
}


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