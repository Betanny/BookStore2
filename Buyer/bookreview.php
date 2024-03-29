<?php
require_once '../Shared Components/dbconnection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Registration/login.html");
}

if (!isset($_POST['product_id'])) {
    header("Location: bookselect.php");
}

$user_id = $_SESSION['user_id'];
$selected_product_id = $_POST['product_id'];
$rating = $_POST['rate'];
$review = $_POST['review'];
$likes = $_POST['likes'];
$dislikes = $_POST['dislikes'];
$comments = $_POST['comments'];

try {
    // Insert the review into the database
    $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text, likes, dislikes, additional_comments) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$selected_product_id, $user_id, $rating, $review, $likes, $dislikes, $comments]);

    // Redirect to a success page
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    // Redirect to an error page
    header("Location: review_error.php");
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
            <div class="img-section">
                <img id="bookImage"
                    src="<?php echo str_replace('D:\xammp2\htdocs\BookStore2', '', $selected_book['front_page_image']); ?>"
                    alt="Book Image">
                alt="Book Cover">
            </div>
            <div class="img-nav">
                <button id="prevButton"><i class="fa-solid fa-angles-left"></i></button>
                <span id="imageCounter">1</span>
                <button id="nextButton"><i class="fa-solid fa-angles-right"></i></button>
            </div>
        </div>
        <div class="product-details">
            <h4>
                <?php echo $selected_book['title']; ?>
            </h4>

            <form action="productreview.php" method="post" id="Add-products">
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
                <button type="submit">Submit Review</button>
            </form>
        </div>

        <div class="other-products">
            <h5>Review other Books</h5>
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
    </script>
</body>

</html>