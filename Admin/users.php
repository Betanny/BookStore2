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
    $sql = "
    SELECT
        ROW_NUMBER() OVER() AS serialno,
        u.email,
        u.role,
        u.category,
        CASE
            WHEN u.role = 'client' THEN COALESCE(o.total_purchases, 0)
            WHEN u.category = 'author' OR u.category = 'publisher' THEN COALESCE(b.books_published, 0)
            WHEN u.category = 'manufacturer' THEN 0
            ELSE NULL
        END AS products,
        u.createdat AS date_joined
    FROM
        public.users u
    LEFT JOIN (
        SELECT
            c.user_id,
            COUNT(*) AS total_purchases
        FROM
            public.orders o
        JOIN
            public.clients c ON o.client_id = c.client_id
        WHERE
            c.user_id = :user_id
        GROUP BY
            c.user_id
    ) o ON u.user_id = o.user_id
    LEFT JOIN (
        SELECT
            seller_id,
            COUNT(*) AS books_published
        FROM
            public.books
        WHERE
            seller_id = :user_id
        GROUP BY
            seller_id
    ) b ON u.user_id = b.seller_id
";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    // Fetch the results into an associative array
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <h4>All Users</h4>

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
                    <div class="cell">No.</div>
                    <div class="bigger-cell2">Email</div>
                    <div class="bigger-cell">Role</div>
                    <div class="bigger-cell">Category</div>
                    <div class="bigger-cell">Products</div>
                    <div class="bigger-cell2">Date joined</div>


                </div>
                <div class="rows">
                    <!-- Adding the user items -->
                    <?php foreach ($users as $user): ?>
                        <div class="row">
                            <div class="cell">
                                <?php echo $user['serialno']; ?>
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
                                <?php echo $user['products']; ?>
                            </div>
                            <div class="bigger-cell2">
                                <?php echo $user['date_joined']; ?>
                            </div>
                            <!-- <div class="cell">
                            </div> -->

                            <div class="icon-cell">
                                <i class="fa-solid fa-eye-slash"></i>
                            </div>
                            <div class="icon-cell">
                                <i class="fa-solid fa-pen"></i>
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
    document.addEventListener("DOMContentLoaded", function () {
        fetch('header.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('header-container').innerHTML = data;

            });
    });
</script>

</html>