<?php
require_once '../Shared Components/dbconnection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$selected_product_id = $_GET['product_id'];




try {


    $selected_book_sql = "SELECT * FROM books WHERE bookid = :bookid";
    $selected_book_stmt = $db->prepare($selected_book_sql);
    $selected_book_stmt->bindParam(':bookid', $selected_product_id);
    $selected_book_stmt->execute();
    $selected_book = $selected_book_stmt->fetch(PDO::FETCH_ASSOC);
    global $selected_book;

    // $bookssql = "SELECT * FROM books WHERE bookid IN (
    //     SELECT product_id FROM orders 
    //     WHERE client_id = ? AND status = 'Delivered'
    // )";

    // $bookstmt = $db->prepare($bookssql);
    // $bookstmt->execute([$user_id]);
    // $books = $bookstmt->fetchAll(PDO::FETCH_ASSOC);

    //other book suggestions
    $sql = "SELECT * FROM clients WHERE user_id = $user_id";

    // Execute the query and fetch the results
    $stmt = $db->query($sql);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    //Getting the book orders made
    $clientid = $data['client_id'];

    $bookrecsql = "
    SELECT DISTINCT b.bookid, b.front_page_image, b.title, b.price, b.bookrating, RANDOM() as rand 
    FROM books b 
    JOIN orders o ON b.bookid = o.product_id 
    WHERE o.status = 'Delivered' AND o.client_id = :client_id ";


    $bookrecomendationstmt = $db->prepare($bookrecsql);
    $bookrecomendationstmt->bindParam(':client_id', $clientid);
    $bookrecomendationstmt->execute();
    $bookrec = $bookrecomendationstmt->fetchAll(PDO::FETCH_ASSOC);
    global $bookrec;


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $rating = intval($_POST['rate']);
        $review = $_POST['review'];
        $likes = $_POST['likes'];
        $dislikes = $_POST['dislikes'];
        $comments = $_POST['comments'];


        // Insert the review into the database
        $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, product_type, rating, review_text, likes, dislikes, additional_comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$selected_product_id, $user_id, "Book", $rating, $review, $likes, $dislikes, $comments]);

        header("Location: /Buyer/bookselect.php");



    }
    // Redirect to a success page
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    // Redirect to an error page
    // header("Location: review_error.php");
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
    <title>View Book</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="file-path"></div>

    <div class="product-container">
        <div class="product-image">
            <div class="back-container">

                <button type="submit" onclick="returnToBooks()" class="back-button">
                    <div class="icon-cell">
                        <i class="fa-solid fa-caret-left"></i>
                    </div>Back
                </button>
            </div>
            <div class="img-section">
                <img id="bookImage"
                    src="<?php echo str_replace('D:\xammp2\htdocs\BookStore2', '', $selected_book['front_page_image']); ?>"
                    alt="Book Image">
            </div>

        </div>
        <div class="product-details">
            <h4>
                <?php echo $selected_book['title']; ?>
            </h4>

            <form action="" method="post" id="Add-products">
                <div class="input-box">
                    <div class="input-control">
                        <label for="Rating">What would you rate the book?</label><br>
                        <div class="stars">
                            <div class="star-widget">
                                <input type="radio" name="rate" id="rate-1" value="5">
                                <label for="rate-1" class="fas fa-star"></label>
                                <input type="radio" name="rate" id="rate-2" value="5">
                                <label for="rate-2" class="fas fa-star"></label>
                                <input type="radio" name="rate" id="rate-3" value="3">
                                <label for="rate-3" class="fas fa-star"></label>
                                <input type="radio" name="rate" id="rate-4" value="2">
                                <label for="rate-4" class="fas fa-star"></label>
                                <input type="radio" name="rate" id="rate-5" value="1">
                                <label for="rate-5" class="fas fa-star"></label>
                            </div><br />
                        </div>
                        <div class="error"></div>
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label for="review">Please provide a detailed review of the book</label>
                        <textarea class="inputfield" style="height: 100px;" class="inputfield" name="review"></textarea>
                        <div class="error"></div>
                    </div>
                </div>
                <!-- Add other review form fields here -->
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
                <input type="hidden" name="product_id" value="<?php echo $selected_product_id; ?>">
                <button class="button" type="submit">Submit Review</button>
            </form>
        </div>


        <div class="other-products">
            <h4>Other Books you can review</h4>

            <?php
            foreach ($bookrec as $book) {
                $front_image = str_replace('D:\xammp2\htdocs\BookStore2', '', $book['front_page_image']);


                echo '<div class="book">';
                echo '<div class="book-img">';
                echo '<a href="/Buyer/bookreview.php?product_id=' . $book['bookid'] . '"><img src="' . $front_image . '"></a>';

                echo '</div>';

                echo '<p>' . $book['title'] . '</p>';
                echo '<p>Price: ksh.' . $book['price'] . '</p>';
                echo '<p>Rating: ';
                // Get integer part of the rating
                $integer_rating = floor($book['bookrating']);
                // Get decimal part of the rating
                $decimal_rating = $book['bookrating'] - $integer_rating;
                // Generate full stars based on the integer part of the rating
                for ($i = 1; $i <= $integer_rating; $i++) {
                    echo '<span class="star">&#9733;</span>'; // Full star
                }

                // // If decimal part is greater than 0, add a half star
                // if ($decimal_rating > 0) {
                //     echo '<span class="half-star">&#9733;</span>'; // Half star
                // }
            
                // Generate empty stars for remaining
                for ($i = $integer_rating + 1; $i <= 5; $i++) {
                    echo '<span class="star">&#9734;</span>'; // Empty star
                }
                echo '</p>';
                echo '</div>';
            }
            ?>


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

    function returnToBooks() {
        window.location.href = "/Buyer/bookselect.php";
    }
    </script>
</body>

</html>