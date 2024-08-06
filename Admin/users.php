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
    $roleFilter = 'All';
    $query = ''; // Initialize the query variable
    $queryCondition = '';

    // Check if a filter has been selected
    if (isset($_GET['role']) && ($_GET['role'] == 'All' || $_GET['role'] == 'Client' || $_GET['role'] == 'Dealer')) {
        $roleFilter = $_GET['role'];
    }

    // Check if a search query is provided
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = $_GET['query'];
        $queryCondition = "WHERE 
            (LOWER(users.email) LIKE LOWER(:query)
            OR LOWER(users.role) LIKE LOWER(:query)
            OR LOWER(users.category) LIKE LOWER(:query))";
    }

    // Add role filter to the query condition
    if ($roleFilter != 'All') {
        if (!empty($queryCondition)) {
            $queryCondition .= " AND users.role = :role";
        } else {
            $queryCondition = "WHERE users.role = :role";
        }
    }
    // Ensure hidden users are not displayed
    if (!empty($queryCondition)) {
        $queryCondition .= " AND users.view_status IS NULL";
    } else {
        $queryCondition = "WHERE users.view_status IS NULL";
    }
    // Build the final SQL query
    $sql = "SELECT 
        user_id,
        ROW_NUMBER() OVER() AS serialno,
        email,
        role,
        category,
        view_status,
        DATE(createdat) AS date_joined
    FROM 
        users 
    $queryCondition";

    // Prepare and execute the query
    $stmt = $db->prepare($sql);

    // Bind values if necessary
    if (!empty($query)) {
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    }
    if ($roleFilter != 'All') {
        $stmt->bindValue(':role', $roleFilter, PDO::PARAM_STR);
    }

    $stmt->execute();

    // Fetch the results into an associative array
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['export']) && $_GET['export'] === 'true') {
        writeLog($db, "User has extracted a copy of the products ", "INFO", $user_id);

        $filename = 'users_report.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write CSV headers
        fputcsv($output, array_keys($users[0]));

        // Write transaction data to CSV
        foreach ($users as $user) {
            fputcsv($output, $user);
        }

        // Close output stream
        fclose($output);
        exit();
    }

    if (isset($_GET['action']) && $_GET['action'] === 'hide_user' && isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        $deleteduserid = $_GET['user_id'];

        try {
            // Prepare the SQL statement to update the view_status to "hidden"
            $sql = "UPDATE users SET view_status = 'hidden' WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            writeLog($db, "Manager has deleted user" . $deleteduserid, "INFO", $user_id);

            // Execute the statement
            if ($stmt->execute()) {
                echo "User status updated to hidden successfully.";
            } else {
                echo "Failed to update user status.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        exit(); // End script execution after handling the hide action
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

    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">
</head>

<body>
    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components\headerdispatcher.php"
        ?>
    <div class="viewproducts-container">
        <div class="viewproducts-header">
            <h4>All Users</h4>

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
                    <form action="" method="get">
                        <select id="filterDropdown" class="filter-bar" name="role" onchange="this.form.submit()">
                            <option value="All" <?php
                            if (isset($_GET['role']) && $_GET['role'] === 'All') {
                                echo "selected";
                            }
                            ; ?>>All</option>
                            <option value="Client" <?php
                            if (isset($_GET['role']) && $_GET['role'] === 'Client') {
                                echo "selected";
                            }
                            ; ?>>Client</option>
                            <option value="Dealer" <?php
                            if (isset($_GET['role']) && $_GET['role'] === 'Dealer') {
                                echo "selected";
                            }
                            ; ?>>Dealer</option>
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
                    <div class="cell">No.</div>
                    <div class="bigger-cell2">User Name</div>
                    <div class="bigger-cell2">Email</div>
                    <div class="bigger-cell">Role</div>
                    <div class="bigger-cell">Category</div>
                    <div class="bigger-cell">Date joined</div>


                </div>
                <div class="rows">
                    <!-- Adding the user items -->
                    <?php foreach ($users as $user): ?>
                        <div class="row">
                            <div class="cell">
                                <?php echo $user['serialno'];
                                ?>
                            </div>

                            <div class="bigger-cell2">
                                <?php

                                // Check category and retrieve name accordingly
                                switch ($user['category']) {
                                    case 'Individual':
                                        // SQL command to concatenate first name and last name from clients table
                                        $namesql = "
                                            SELECT CONCAT(first_name, ' ', last_name) AS name
                                            FROM clients
                                            WHERE user_id = :user_id
                                        ";
                                        break;
                                    case 'Organization':
                                        // SQL command to select organization name from clients table
                                        $namesql = "
                                            SELECT organization_name AS name
                                            FROM clients
                                            WHERE user_id = :user_id
                                        ";
                                        break;
                                    case 'Author':
                                        // SQL command to concatenate first name and last name from authors table
                                        $namesql = "
                                            SELECT CONCAT(first_name, ' ', last_name) AS name
                                            FROM authors
                                            WHERE user_id = :user_id
                                        ";
                                        break;
                                    case 'Publisher':
                                        // SQL command to select publisher name from publishers table
                                        $namesql = "
                                            SELECT publisher_name AS name
                                            FROM publishers
                                            WHERE user_id = :user_id
                                        ";
                                        break;
                                    case 'Manufacturer':
                                        // SQL command to select manufacturer name from manufacturers table
                                        $namesql = "
                                            SELECT manufacturer_name AS name
                                            FROM manufacturers
                                            WHERE user_id = :user_id;
                                        ";
                                        break;
                                    default:
                                        // Handle unknown category
                                        $name = 'Unknown Category';
                                        break;
                                }


                                // Only execute if $namesql is not empty
                                $namestmt = $db->prepare($namesql);
                                $namestmt->bindParam(':user_id', $user['user_id']);
                                $namestmt->execute();

                                // Fetch name from the result
                                $nameData = $namestmt->fetch(PDO::FETCH_ASSOC);

                                if ($nameData !== false) {
                                    // Access $nameData['name'] and assign it to $name
                                    $name = $nameData['name'];
                                } else {
                                    // Handle case when no matching record is found
                                    $name = 'No Name Found';
                                }
                                echo $name;
                                ?>
                            </div>

                            <div class="bigger-cell2">
                                <?php echo $user['email']; ?>
                            </div>
                            <div class="bigger-cell">
                                <?php echo $user['role']; ?>
                            </div>
                            <div class="bigger-cell">
                                <?php echo $user['category']; ?>
                            </div>

                            <div class="bigger-cell">
                                <?php echo $user['date_joined']; ?>
                            </div>
                            <!-- <div class="cell">
                            </div> -->

                            <!-- <div class="icon-cell">
                            <i class="fa-solid fa-eye-slash"></i>
                        </div> -->
                            <div class="icon-cell">
                                <i class="fa-solid fa-pen" id="editIcon" style="cursor:pointer;"
                                    data-role="<?php echo $user['role']; ?>"
                                    data-userid="<?php echo $user['user_id']; ?>"></i>
                            </div>
                            <!-- <div class="icon-cell">
                            <a href="#" class="delete-link" data-table="books"
                                data-pk="<php echo $product['bookid']; ?>" data-pk-name="bookid">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div> -->
                            <div class="icon-cell">
                                <a href="#" class="delete-link" data-table="users" data-pk="<?php echo $user['user_id']; ?>"
                                    data-pk-name="user_id">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
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


</body>
<script>
    let tableName, primaryKey, pkName, deleteLink;

    document.addEventListener("DOMContentLoaded", function () {
        // Get all elements with the class "delete-link"
        var deleteLinks = document.querySelectorAll('.delete-link');

        // Loop through each delete link
        deleteLinks.forEach(function (link) {
            // Add click event listener to each delete link
            link.addEventListener('click', function (event) {
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

    document.getElementById('confirm-delete-button').addEventListener('click', function () {
        confirmDelete(tableName, primaryKey, pkName, deleteLink);
    });
    // Perform AJAX request to the delete script
    // function confirmDelete(tableName, primaryKey, pkName, link) {
    //     var xhr = new XMLHttpRequest();
    //     xhr.open('GET', '/Shared Components/delete.php?table=' + tableName + '&pk=' +
    //         primaryKey +
    //         '&pk_name=' + pkName, true);
    //     xhr.onload = function() {
    //         if (xhr.status === 200) {
    //             // Handle successful deletion (if needed)
    //             // For example, you can remove the deleted row from the DOM
    //             link.parentElement.parentElement.remove();
    //             // console.log(pkName + " " + primaryKey);
    //             console.log(pkName + " " + primaryKey + " " + tableName + "   " + link);

    //             console.log("removing");
    //         } else {
    //             // Handle error (if needed)
    //             console.error('Error:', xhr.statusText);
    //         }
    //     };
    //     xhr.onerror = function() {
    //         // Handle network errors (if needed)
    //         console.error('Request failed');
    //     };
    //     console.log("Sending");
    //     xhr.send();

    // }
    function confirmDelete(tableName, primaryKey, pkName, link) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', window.location.pathname + '?action=hide_user&user_id=' + primaryKey, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Handle successful update
                link.parentElement.parentElement.remove();
                console.log("User status updated to hidden");
            } else {
                // Handle error
                console.error('Error:', xhr.statusText);
            }
        };
        xhr.onerror = function () {
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




    document.addEventListener("DOMContentLoaded", function () {
        var exportButton = document.getElementById('exportButton');
        exportButton.addEventListener('click', function () {
            // Update the href attribute of the export button with the desired URL
            var currentHref = window.location.href;
            var exportUrl = currentHref.includes('?export=true') ? currentHref : currentHref +
                '?export=true';
            exportButton.querySelector('a').setAttribute('href', exportUrl);
        });
    });



    document.addEventListener('DOMContentLoaded', function () {
        // Select all edit icons with the class 'edit-icon'
        const editIcons = document.querySelectorAll('.fa-pen');

        // Loop through each edit icon and add the click event listener
        editIcons.forEach(icon => {
            icon.addEventListener('click', function () {
                const role = this.getAttribute('data-role');
                const userId = this.getAttribute('data-userid');
                console.log('User role:', role);
                console.log('User ID:', userId);

                if (role === 'Client') {
                    window.location.href = '../Buyer/clientprofile.php?user_id=' + userId;
                } else if (role === 'Dealer') {
                    window.location.href = '../Shared Components/profile.php?user_id=' + userId;
                } else {
                    alert('Invalid role.');
                }
            });
        });
    });
</script>

</html>