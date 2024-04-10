<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';
session_start();

$bookid = $_GET['bookid'];

//Getting books from the books table
$bookDetailsQuery = "SELECT * FROM books WHERE bookid = :bookid";
$stmt = $db->prepare($bookDetailsQuery);
$stmt->bindParam(':bookid', $bookid);
$stmt->execute();
$book = $stmt->fetch(PDO::FETCH_ASSOC);
global $book;


function processAndAggregateImageURLs($book)
{
    // Extract the base path from the first image URL
    $basePath = 'D:\xammp2\htdocs\BookStore2';

    // Initialize an array to store processed image URLs
    $imageURLs = array();

    // Process and aggregate the front page image URL
    $imageURLs[] = str_replace($basePath, '', $book['front_page_image']);

    // Process and aggregate the back page image URL
    $imageURLs[] = str_replace($basePath, '', $book['back_page_image']);

    // Process and aggregate the other images
    $otherImages = explode(',', $book['other_images']);
    foreach ($otherImages as $otherImage) {
        // Process and aggregate each other image URL
        $imageURLs[] = str_replace($basePath, '', $otherImage);
    }

    return $imageURLs;
}

$imageURLs = processAndAggregateImageURLs($book);


global $imageURLs;



//Getting books from the books table
$bookrecsql = "SELECT DISTINCT bookid, front_page_image, title, price, bookrating, RANDOM() as rand FROM books ORDER BY rand LIMIT 6";
$bookrecomendationstmt = $db->query($bookrecsql);
$books = $bookrecomendationstmt->fetchAll(PDO::FETCH_ASSOC);
global $books;



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Home/home.css">
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Document</title>
</head>

<body>


    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components\headerdispatcher.php"
        ?>


    <div class="product-container">
        <div class="product-image">
            <div class="img-section">
                <img id="bookImage" src="<?php echo $imageURLs[0]; ?>" alt="Book Cover">
            </div>
            <div class="img-nav">
                <button id="prevButton"><i class="fa-solid fa-angles-left"></i></button>
                <span id="imageCounter">1</span>
                <button id="nextButton"><i class="fa-solid fa-angles-right"></i></button>
            </div>
        </div>

        <div class="product-details">
            <h4>
                <?php echo $book['title']; ?>
            </h4>
            <h5>By:Author
                <?php echo $book['author']; ?>
            </h5>
            <h5>Publisher:
                <?php echo $book['publisher']; ?>
            </h5>

            <p>
                <?php echo $book['details']; ?>
            </p>
            <div class="other-details">
                <h5> Author :
                    <?php echo $book['author']; ?>
                </h5>
                <h5> Publisher :
                    <?php echo $book['publisher']; ?>
                </h5>
                <h5>ISBN:
                    <?php echo $book['isbn']; ?>
                </h5>
                <h5>Language:
                    <?php echo $book['language']; ?>
                </h5>
                <h5>Rating:
                    <?php echo $book['bookrating']; ?>
                </h5>
                <h5>Price:
                    <?php echo $book['price']; ?>
                </h5>
                <h5>Price in Bulk:
                    <?php echo $book['priceinbulk']; ?>
                </h5>
                <h5>Minimum pieces in Bulk:
                    <?php echo $book['mininbulk']; ?>
                </h5>
                <form id="addToCartForm" action="/Buyer/add_to_cart.php" method="post">
                    <input type="hidden" name="bookid" id="bookidInput" value="<?php echo $book['bookid']; ?>">
                    <input type="hidden" name="price" id="bookidInput" value="<?php echo $book['price']; ?>">

                    <button type="submit">Add to Cart</button>
                </form>



            </div>
            <div class="product-review">
                What do others say about it:
                <div class="wholereview">

                    <div class="review">
                        <p>"I'm incredibly impressed with the diverse range of educational materials available on [Your
                            Company
                            Name].</p>
                        <div class="profile">
                            <p>By John Doe</p>

                        </div>
                    </div>

                </div>

            </div>


        </div>

        <div class="other-products">
            <h4>Other Books you might like</h4>

            <?php
            foreach ($books as $book) {
                $front_image = str_replace('D:\xammp2\htdocs\BookStore2', '', $book['front_page_image']);


                echo '<div class="book">';
                echo '<div class="book-img">';
                echo '<a href=""><img src="' . $front_image . '"></a>';
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
    </div>






</body>



<script>
document.addEventListener("DOMContentLoaded", function() {
    // fetch('/Shared Components/header.php')
    //     .then(response => response.text())
    //     .then(data => {
    //         document.getElementById('header-container').innerHTML = data;
    //     });
    fetch('/Shared Components/footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-container').innerHTML = data;
        });
    const bookImage = document.getElementById('bookImage');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const imageCounter = document.getElementById('imageCounter');

    const imageURLs = <?php echo json_encode($imageURLs); ?>;
    let currentIndex = 0;

    function showImage(index) {
        if (index >= 0 && index < imageURLs.length) {
            bookImage.src = imageURLs[index];
            imageCounter.innerText = index + 1;
            currentIndex = index;
        }
    }

    prevButton.addEventListener('click', function() {
        showImage(currentIndex - 1);
    });

    nextButton.addEventListener('click', function() {
        showImage(currentIndex + 1);
    });
});
const categoryToggles = document.querySelectorAll('.category-toggle');

categoryToggles.forEach(toggle => {
    toggle.addEventListener('click', () => {
        const subCategories = toggle.nextElementSibling;
        subCategories.style.display = subCategories.style.display === 'block' ? 'none' : 'block';
    });
});

function addToCart(bookId) {
    console.log(bookId);
    console.log("No book id")
    document.getElementById("addToCartForm").submit();

}
</script>

</html>