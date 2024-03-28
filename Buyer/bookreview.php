<?php
require_once '../Shared Components/dbconnection.php';

session_start();

if (!isset ($_SESSION['user_id'])) {
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

        if (isset ($_GET['product_id'])) {
            $selected_product_id = $_GET['product_id'];
            echo "console.log('{$selected_product_id}');";

            $book_query = "SELECT * FROM books WHERE bookid = ?";
            $stmt = $db->prepare($book_query);
            $stmt->execute([$selected_product_id]);
            $selected_book = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($selected_book) {
                $selected_title = $selected_book['title'];
                $selected_author = $selected_book['author'];
                echo "console.log('{$selected_title}');";
                echo "console.log('{$selected_author}');";
                global $selected_title;
                global $selected_author;

            } else {
                echo "No book found with ID: $selected_product_id";
            }
        } else {
            echo "Product ID is not set.";
        }
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

    <title>Document</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="file-path">

    </div>
    <!-- <form action="#" id="getpid" method="get"> -->
    <!-- <input type="hidden" id="product_id" onchange="submitFormAndLog()">
    <button type="submit" id="hiddenSubmit" style="display:none">Post</button> -->

    <!-- </form> -->

    <div class="book-selection">

        <div class="allProducts-container">


            <div class="table">
                <h4>
                    Which book would you like to review?<br>
                    Please select one of the following</h4>
                <div class="row-header1">
                    <div class="ordername-cell">Title</div>
                </div>
                <div class="order-rows">
                    <!-- Adding the order items -->
                    <?php foreach ($orders as $order): ?>
                    <div class="row" onclick="setpid()" id="get_product_id" value="<?php echo $order['product_id']; ?>">
                        <div class=" ordername-cell">
                            <?php echo $order['title']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>


            </div>
        </div>
    </div>

    </div>

    <div class="product-container" style="display:none;">
        <div class="product-image">

            <div class="img-section">
                <img src="/Images/Book categories/mathematics.png" alt="">

            </div>
            <div class="img-nav">
                <p> 1 > </p>
            </div>



        </div>

        <div class="product-details">
            <form action="productreview.php" method="post" id="Add-products">

                <h4>
                    <?php echo $selected_title; ?>
                </h4>
                <h5> By
                    <?php echo $selected_author; ?>
                </h5>
                <div class="input-box">
                    <div class="input-control">
                        <label for="Rating">What would you rate the book?</label><br>
                        <div class="stars">
                            <div class="star-widget">
                                <input type="radio" name="rate" id="rate-1" value="1">
                                <label for="rate-1" class="fas fa-star"></label>
                                <input type="radio" name="rate" id="rate-2" value="2">
                                <label for="rate-2" class="fas fa-star"></label>
                                <input type="radio" name="rate" id="rate-3" value="3">
                                <label for="rate-3" class="fas fa-star"></label>
                                <input type="radio" name="rate" id="rate-4" value="4">
                                <label for="rate-4" class="fas fa-star"></label>
                                <input type="radio" name="rate" id="rate-5" value="5">
                                <label for="rate-5" class="fas fa-star"></label>
                            </div><br />
                        </div>
                        <div class="error"></div>
                    </div>
                </div>

                <div class="input-box">
                    <div class="inputcontrol">
                        <label for="review">Please provide a detailed review of the books</label>
                        <textarea class="inputfield" style="height: 100px;" class="inputfield" name="review"></textarea>
                        <div class="error"></div>
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label for="likes" class="no-asterisk">What did you like most about this book?</label>
                        <textarea class="inputfield" style="height: 70px;" class="inputfield" name="likes"></textarea>
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label for="dislikes" class="no-asterisk">What did you dislike most about this book?</label>
                        <textarea class="inputfield" style="height: 70px;" class="inputfield"
                            name="dislikes"></textarea>
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label for="comments" class="no-asterisk">Any additional comments?</label>
                        <textarea class="inputfield" style="height: 70px;" class="inputfield"
                            name="comments"></textarea>
                    </div>


                </div>
            </form>
        </div>
        <div class="other-products">
            <h5>Review other Books</h5>


        </div>


    </div>
    </div>






</body>



<script>
function submitFormAndLog() {
    document.getElementById("getpid").submit();
    console.log('Working');
}
document.addEventListener("DOMContentLoaded", function() {
    fetch('header.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });



});

const categoryToggles = document.querySelectorAll('.category-toggle');

categoryToggles.forEach(toggle => {
    toggle.addEventListener('click', () => {
        const subCategories = toggle.nextElementSibling;
        subCategories.style.display = subCategories.style.display === 'block' ? 'none' : 'block';
    });
});

function setpid() {
    const rows = document.querySelectorAll('.row');
    rows.forEach(row => {
        row.addEventListener('click', () => {
            const productId = row.getAttribute('value'); // Get the value attribute of the clicked row
            // Hide book selection and show product container
            document.querySelector('.book-selection').style.display = 'none';
            document.querySelector('.product-container').style.display = 'flex';
            // Set the selected product ID
            document.getElementById('product_id').value = productId;
            console.log(productId);
            var submitButton = document.getElementById('hiddenSubmit');
            submitButton.click();
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Response from PHP script
                    console.log(this.responseText);
                }
            };
            const url = "<?php echo $_SERVER['PHP_SELF']; ?>?product_id=" + productId;
            console.log("Request URL:", url); // Debug statement
            xhttp.open("GET", url, true);
            xhttp.send();
            console.log("Clicked");

        });
    });
}
</script>

</html>