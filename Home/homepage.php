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
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

</head>

<body>

    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components\headerdispatcher.php"
        ?>
    <div id="logout-container" style="display: none;"></div>

    <div class="homepage-container">
        <div class="users">
            <div class="client-container">
                <div class="card">
                    <div class="imgBx">
                        <a href="#">
                            <img src="/Images/Client-06-02-2024.gif">
                        </a>
                        <h3>Are you feeling lost in the maze of CBC? </h3>
                        <p><br>Unlock a treasure chest of learning goodies designed to make studying a breeze!</p>
                        <p><br>Our platform is your secret map to top-quality educational products tailored just for
                            you!</p>
                        <p><br>Grab discounted stationery to gear up for your academic adventures!</p>
                        <p><br>Ready to dive into the world of educational excellence? Let's set sail! 🚀</p>
                    </div>
                    <input type="submit" onclick="window.location.href='/Registration/buyer.html'"
                        class="register-button" value="Register">
                    <div class="login-text">Have an account? <a href="/Registration/login.php">Login</a></div>



                </div>
            </div>

            <div class="dealer-container">
                <div class="card">
                    <div class="imgBx">
                        <a href="#">
                            <img src="/Images/Dealer-06-02-2024.gif">
                        </a>
                        <h3>Join Our Marketplace and Reach Millions!</h3>
                        <p><br>Hey Authors! Tired of writing books only to struggle finding readers?</p><br>
                        <p>Publishers, ever wished for a platform where your books shine brighter?</p><br>
                        <p>Manufacturers, looking for a stage to showcase your innovative stationery?</p><br>
                        <p>Well, look no further! Join our vibrant marketplace today!</p><br>
                    </div>
                    <input type="submit" onclick="window.location.href='/Registration/dealer.html'"
                        class="register-button" value="Register">
                    <div class="login-text">Have an account? <a href="/Registration/login.php">Login</a></div>

                </div>
            </div>


        </div>
        <div class="bookcategories-container">
            <h1>Book Categories</h1>
            <h4>There are variety of books in accordance to the CBC guidelines in our store</h4>
            <div class="all-categories">
                <div class="row-category">
                    <div class="category">
                        <img src="/Images/Book categories/mathematics.png">
                        <p>Mathematics</p>
                    </div>

                    <div class="category">
                        <img src="/Images/Book categories/science.png">
                        <p>Science and Technology</p>
                    </div>

                    <div class="category">
                        <img src="/Images/Book categories/socialstudies.png">
                        <p>Social Studies</p>
                    </div>

                    <div class="category">
                        <img src="/Images/Book categories/languages.jfif">
                        <p>Languages</p>
                    </div>
                </div>
                <div class="row-category">

                    <div class="category">
                        <img src="/Images/Book categories/religion.jfif">
                        <p>Religious Education</p>
                    </div>

                    <div class="category">
                        <img src="/Images/Book categories/practicals.jfif">
                        <p>Practical and Creative Subjects</p>
                    </div>

                    <div class="category">
                        <img src="/Images/Book categories/physical health.jfif">
                        <p>Physical Education and Health</p>
                    </div>

                    <div class="category">
                        <img src="/Images/Book categories/environmental.jfif">
                        <p>Environmental Studies</p>
                    </div>
                </div>
            </div>

        </div>
        <div class="aboutus-container">
            <div class="subcont-img">
                <img src="/Images/Illustrations/bookstore.jpg">
            </div>
            <div class="subcont-text">

                <h1>About our store</h1>
                <p>Welcome to smartCBC where we're dedicated to revolutionizing educational content access. Our
                    platform connects creators, publishers, educators, and learners, offering a comprehensive selection
                    of high-quality materials. Join us in shaping the future of education and unlocking the power of
                    learning together.</p>
                <a href="Aboutus.html" class="button">Read More</a>

            </div>


        </div>
        <div class="reviews-container">
            <h2>What do our customers say about us?</h2>
            <div class="review-subcontainer">
                <div class="wholereview">
                    <div class="review">
                        <p>"I'm incredibly impressed with the diverse range of educational materials available on
                            smartCBC As a teacher, I've found everything I need to enhance my curriculum and
                            engage my students. The platform's user-friendly interface makes browsing and purchasing a
                            breeze!"</p>
                    </div>
                    <div class="profile">
                        <img src="/Images/Illustrations/Male.svg" class="img-profile">
                        <p>Milly Wambui<br>Nairobi Academy</p>

                    </div>
                </div>
                <div class="wholereview">
                    <div class="review">
                        <p>"I've been searching for a reliable source of textbooks and study materials for my students
                            , and I'm thrilled to have discovered smartCBC. Not only is the selection
                            extensive, but the quality of the materials is top-notch. Plus, the customer service team
                            was incredibly helpful when I had questions about my order"</p>
                    </div>
                    <div class="profile">
                        <img src="/Images/Illustrations/Female.svg" class="img-profile">
                        <p>Mrs Kamotho(head teacher)<br>Chrisco Academy</p>

                    </div>
                </div>
            </div>
            <div class="review-subcontainer">
                <div class="wholereview">
                    <div class="review">
                        <p>"As an author, I've struggled to find a platform that values and supports content creators
                            like myself. [Your Company Name] has been a game-changer for me. Not only have they provided
                            a seamless publishing process, but they also prioritize fair compensation and transparent
                            communication. I couldn't be happier with my experience!"</p>
                    </div>
                    <div class="profile">
                        <img src="/Images/Illustrations/Male.svg" class="img-profile">
                        <p>Author<br>Melissa Kenneth</p>

                    </div>
                </div>
            </div>
        </div>

    </div>
    <div id="footer-container"></div>


</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
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
    document.getElementById('home-link').classList.add('active-link');


});
// document.addEventListener("DOMContentLoaded", function() {
//     fetch('/Shared Components/logout.php').then(response => response.text()).then(data => {
//         document.getElementById('logout-container').innerHTML = data;
//     });
// });
</script>

</html>