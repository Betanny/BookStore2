<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';


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
  <link rel="stylesheet" href="home.css">
  <link rel="stylesheet" href="/Shared Components/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <title>Document</title>
</head>

<body>
  <div id="header-container"></div>



  <div class="products-container">
    <div class="filters-container">
      <h3>What specifically would you like? </h3>

      <div class="main-category">
        <p class="category-toggle">Genres</p>
        <div class="sub-categories">
          <ul>
            <li>Mathematics</li>
            <li>Science and Technology</li>
            <li>Social Studies</li>
            <li>Languages</li>
            <li>Religious Education</li>
            <li>Practical and Creative Subjects</li>
            <li>Physical Education and Health</li>
            <li>Environmental Studies</li>
          </ul>
        </div>
      </div>

      <div class="main-category">
        <p class="category-toggle">Level of Education</p>
        <div class="sub-categories">
          <ul>
            <li>Early Childhood Education (ECE) Level</li>
            <ul>
              <li class="level3">Pre-primary 1 (PP1)</li>
              <li class="level3">Pre-primary 2 (PP2)</li>
            </ul>
            <li>Primary Education Level</li>
            <ul>
              <li class="level3">Grade 1</li>
              <li class="level3">Grade 2</li>
              <li class="level3">Grade 3</li>
              <li class="level3">Grade 4</li>
              <li class="level3">Grade 5</li>
              <li class="level3">Grade 6</li>
            </ul>
            <li>Secondary Education Level</li>
            <ul>
              <li class="level3">Grade 7</li>
              <li class="level3">Grade 8</li>
              <li class="level3">Grade 9</li>
              <li class="level3">Grade 10</li>
              <li class="level3">Grade 11</li>
              <li class="level3">Grade 12</li>
            </ul>
          </ul>
        </div>
      </div>



    </div>
    <div class="maincontent">
      <div class="slideshow-container">



      </div>
      <div class="bookshow-container">
        <div class="booksheader">
          <div class="filter-dropdown">
            <select id="genre-filter" class="filter-bar">
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




        </div>
        <div class="books-container">


          <div class="all-books">
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
    </div>








  </div>


  </div>










</body>



<script>
  document.addEventListener("DOMContentLoaded", function () {
    fetch('/Shared Components/header.php')
      .then(response => response.text())
      .then(data => {
        document.getElementById('header-container').innerHTML = data;
      });
    fetch('/Shared Components/footer.html')
      .then(response => response.text())
      .then(data => {
        document.getElementById('footer-container').innerHTML = data;
      });
  });
  const categoryToggles = document.querySelectorAll('.category-toggle');

  categoryToggles.forEach(toggle => {
    toggle.addEventListener('click', () => {
      const subCategories = toggle.nextElementSibling;
      subCategories.style.display = subCategories.style.display === 'block' ? 'none' : 'block';
    });
  });
</script>

</html>