<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';


if (session_status() !== PHP_SESSION_ACTIVE) {
    error_reporting(E_ALL & ~E_NOTICE);

    session_start();
}


$query = isset($_GET['query']) ? $_GET['query'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';
$education_level = isset($_GET['education_level']) ? $_GET['education_level'] : '';


if ($query) {
    // Search query to fetch matching books
    $sql = "SELECT * FROM (
                SELECT DISTINCT ON (bookid) bookid, front_page_image, grade, title, price, bookrating, RANDOM() as rand 
                FROM books 
                WHERE LOWER(title) LIKE LOWER(:query) OR grade LIKE :query OR LOWER(author) LIKE LOWER(:query) OR LOWER(publisher) LIKE LOWER(:query) 
            ) AS distinct_books";
    $params = ['query' => '%' . $query . '%'];
} else {
    // Getting books from the books table
    $sql = "SELECT * FROM (
                SELECT DISTINCT ON (bookid) bookid, front_page_image, grade, title, price, bookrating, RANDOM() as rand 
                FROM books
            ) AS distinct_books";
    $params = [];
}


// Filter by education level
if ($education_level) {
    $sql .= " WHERE grade = :education_level";
    $params['education_level'] = $education_level;
}
$stmt = $db->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
global $books;



//Getting approved books from the approved books table
$appbookrecsql = "SELECT * FROM kicdapprovedbooks";
$appbookrecomendationstmt = $db->query($appbookrecsql);
$appbooks = $appbookrecomendationstmt->fetchAll(PDO::FETCH_ASSOC);
global $appbooks;
// Function to check if a book is KICD approved
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


// Filter books based on the selected filter
if ($filter == 'KICD') {
    $books = array_filter($books, function ($book) use ($appbooks) {
        return isKICDApproved($book['title'], $book['grade'], $appbooks);
    });
} elseif ($filter == 'Non-KICD') {
    $books = array_filter($books, function ($book) use ($appbooks) {
        return !isKICDApproved($book['title'], $book['grade'], $appbooks);
    });
}

//best_rated
$sql_best_rated = "SELECT * FROM books ORDER BY bookrating DESC LIMIT 1";
$stmt_best_rated = $db->query($sql_best_rated);
$best_rated_book = $stmt_best_rated->fetch(PDO::FETCH_ASSOC);
global $best_rated_book;


//most_popular
$sql_most_popular = "SELECT b.*, COUNT(o.product_id ) AS order_count 
FROM books b 
LEFT JOIN orders o ON b.bookid = o.product_id 
GROUP BY b.bookid 
ORDER BY order_count DESC 
LIMIT 1";
$stmt_most_popular = $db->query($sql_most_popular);
$most_popular = $stmt_most_popular->fetch(PDO::FETCH_ASSOC);
global $most_popular;


//best_selling
$sql_best_selling = "SELECT b.*, SUM(o.total_amount) AS total_sales 
FROM books b 
LEFT JOIN orders o ON b.bookid = o.product_id 
GROUP BY b.bookid 
ORDER BY total_sales DESC 
LIMIT 1";
$stmt_best_selling = $db->query($sql_best_selling);
$best_selling = $stmt_best_selling->fetch(PDO::FETCH_ASSOC);
global $best_selling;




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
    <!-- <div id="header-container"></div> -->

    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components\headerdispatcher.php"
        ?>

    <div class="products-container">
        <div class="products-container">
            <div class="filters-container">
                <h3>What specifically would you like?</h3>

                <form id="filter-form" action="" method="GET">
                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
                    <div class="main-category">
                        <p>Level of Education</p>
                        <div class="radio-buttons">
                            <ul>
                                <li><input type="radio" id="all-levels" name="education_level" value="All" <?php if ($education_level == 'All')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="all-levels">All</label>
                                </li>
                            </ul>
                            <ul>

                                <li> <input type="radio" id="pp1" name="education_level" value="PP1" <?php if ($education_level == 'PP1')
                                    echo 'checked'; ?> onchange="applyFilter()">

                                    <label for="pp1">Pre-primary 1 (PP1)</label>
                                </li>
                                <li><input type="radio" id="pp2" name="education_level" value="PP2" <?php if ($education_level == 'PP2')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="pp2">Pre-primary 2 (PP2)</label>
                                </li>
                            </ul>
                            <ul>
                                <li><input type="radio" id="grade-1" name="education_level" value="Grade 1" <?php if ($education_level == 'Grade 1')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-1">Grade 1</label>
                                </li>
                                <li><input type="radio" id="grade-2" name="education_level" value="Grade 2" <?php if ($education_level == 'Grade 2')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-2">Grade 2</label>
                                </li>
                                <li><input type="radio" id="grade-3" name="education_level" value="Grade 3" <?php if ($education_level == 'Grade 3')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-3">Grade 3</label>
                                </li>
                                <li><input type="radio" id="grade-4" name="education_level" value="Grade 4" <?php if ($education_level == 'Grade 4')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-4">Grade 4</label>
                                </li>
                                <li><input type="radio" id="grade-5" name="education_level" value="Grade 5" <?php if ($education_level == 'Grade 5')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-5">Grade 5</label>
                                </li>
                                <li><input type="radio" id="grade-6" name="education_level" value="Grade 6" <?php if ($education_level == 'Grade 6')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-6">Grade 6</label>
                                </li>
                            </ul>
                            <ul>
                                <li> <input type="radio" id="grade-7" name="education_level" value="Grade 7" <?php if ($education_level == 'Grade 7')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-7">Grade 7</label>
                                </li>

                                <li> <input type="radio" id="grade-8" name="education_level" value="Grade 8" <?php if ($education_level == 'Grade 8')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-8">Grade 8</label>
                                </li>
                                <li> <input type="radio" id="grade-9" name="education_level" value="Grade 9" <?php if ($education_level == 'Grade 9')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-9">Grade 9</label>
                                </li>
                                <li> <input type="radio" id="grade-10" name="education_level" value="Grade 10" <?php if ($education_level == 'Grade 10')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-10">Grade 10</label>
                                </li>
                                <li> <input type="radio" id="grade-11" name="education_level" value="Grade 11" <?php if ($education_level == 'Grade 11')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-11">Grade 11</label>
                                </li>
                                <li> <input type="radio" id="grade-12" name="education_level" value="Grade 12" <?php if ($education_level == 'Grade 12')
                                    echo 'checked'; ?> onchange="applyFilter()">
                                    <label for="grade-12">Grade 12</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>




            </div>
            <div class="maincontent">
                <!-- <div class="slideshow-container">
                <div class="slideshow-book">
                    <h5>Best Selling Book</h5>
                    <img src="<php echo str_replace('D:\xammp2\htdocs\BookStore2', '', $best_selling['front_page_image']); ?>"
                        alt="Best Selling Book">
                    <p>
                        <php echo $best_selling['title']; ?>
                    </p>
                </div>
                <div class="slideshow-book">
                    <img src="<php echo str_replace('D:\xammp2\htdocs\BookStore2', '', $most_popular['front_page_image']); ?>"
                        alt="Most Popular Book">
                    <p>
                        <php echo $most_popular['title']; ?>
                    </p>
                </div>
                <div class="slideshow-book">
                    <img src="<php echo str_replace('D:\xammp2\htdocs\BookStore2', '', $best_rated_book['front_page_image']); ?>"
                        alt="
                        Highest Rated Book">
                    <p>
                        <php echo $best_rated_book['title']; ?>
                    </p>
                </div>
            </div> -->



                <div class=" bookshow-container">
                    <div class="booksheader">
                        <div class="filter-dropdown">
                            <form action="" method="GET">
                                <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
                                <select id="genre-filter" class="filter-bar" name="filter"
                                    onchange="this.form.submit()">
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
                                <input type="text" name="query" id="search-input" class="search-bar"
                                    placeholder="Search..." value="<?php echo htmlspecialchars($query); ?>">
                                <button class="search-button" type="submit"><i
                                        class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>





                    </div>
                    <div class="books-container">


                        <div class="all-books">
                            <?php if (!empty($books)): ?>
                            <?php
                                foreach ($books as $book) {
                                    $front_image = str_replace('D:\xammp2\htdocs\BookStore2', '', $book['front_page_image']);


                                    echo '<div class="book">';
                                    echo '<div class="book-img">';
                                    echo '<a href="product.php?bookid=' . $book['bookid'] . '"><img src="' . $front_image . '"></a>';
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
                                    // Check if the book is KICD approved and display accordingly
                                    $titleToCheck = $book['title'];
                                    $gradeToCheck = $book['grade']; // Assuming grade 6 for demonstration
                                    if (isKICDApproved($titleToCheck, $gradeToCheck, $appbooks)) {
                                        echo '<p style="color: green;">KICD APPROVED</p>';
                                    }

                                    echo '</div>';
                                }
                                ?>
                            <?php else: ?>
                            <!-- <div class="row"> -->
                            <h2>No Products with that keyword.</h2>
                        </div>
                        <?php endif; ?>


                    </div>
                </div>


            </div>
        </div>








    </div>













</body>



<script>
// Function to rotate the slides
function rotateSlides() {
    const slideshow = document.querySelector('.slideshow-container');
    const slides = slideshow.querySelectorAll('.slideshow-book');

    // Find the active slide
    const activeSlide = slideshow.querySelector('.active');

    // Get the index of the active slide
    const activeIndex = Array.from(slides).indexOf(activeSlide);

    // Calculate the index of the next slide
    const nextIndex = (activeIndex + 1) % slides.length;

    // Remove the active class from the current slide
    activeSlide.classList.remove('active');

    // Add the active class to the next slide
    slides[nextIndex].classList.add('active');
}

// Rotate the slides every 3 seconds
setInterval(rotateSlides, 3000);
</script>

</html>