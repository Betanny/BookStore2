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
    <div class="contactus-container">

        <div class="introduction-container">
            <h4>About us</h4>

            <div class="introduction-txt">
                <p>Welcome to SmartCBC, where education meets innovation.Our platform is dedicated to enriching the
                    learning experience with a curated selection of CBC-compliant educational materials.We bridge the
                    gap between knowledge seekers and providers, offering a marketplace that caters to students,
                    authors, and publishers alike.</p>
                <p>Dive into our world of academic treasures and discover a more efficient, engaging way to learn and
                    grow.</p>
            </div>
            <div class="intro-image">
                <img src="" alt="">
            </div>
        </div>





        <div class="users-container">
            <h4>Our Users</h4>
            <div class="Our-users">
                <div class="author">
                    <h4>Authors</h4>
                    <p>Talented authors find a welcoming home with us. We offer a dynamic marketplace where your
                        creative works can reach millions of eager minds. Our platform ensures your books not only find
                        their audience but also make a lasting impact in the educational sphere.
                    </p>


                </div>
                <div class="client">
                    <h4>Clients</h4>
                    <p>Our clients are the heart of our educational ecosystem. We provide students and parents with a
                        rich selection of CBC-compliant learning materials and resources. Whether you’re looking for the
                        latest textbooks or seeking engaging learning tools, our platform is your one-stop shop for
                        academic success. Choose to order individually for personal use or in bulk for greater
                        savings—either way, you’re investing in quality education.
                    </p>


                </div>

                <div class="publisher">
                    <h4>Publishers</h4>
                    <p>We understand the challenges publishers face in today’s competitive market. Our platform serves
                        as a vibrant stage where your publications can shine. From niche academic works to mainstream
                        educational texts, we help your books get the recognition they deserve.
                    </p>

                </div>
            </div>




        </div>

        <div class="mission-container">
            <h4>Our Mission</h4>
            <p>At SmartCBC, our mission is to revolutionize the educational journey by providing a comprehensive suite
                of CBC-compliant resources that cater to the diverse needs of learners and educators. We are committed
                to fostering an environment where curiosity is ignited, knowledge is shared, and academic achievement is
                celebrated. Through our innovative platform, we aim to offer accessible, high-quality educational tools
                and materials that not only enhance the learning experience but also encourage a lifelong passion for
                discovery. By bridging the gap between students and educational content creators, we aspire to build a
                community that thrives on collaboration, excellence, and the relentless pursuit of learning.</p>
            <h4>Our vision</h4>
            <p>Our vision is to establish SmartCBC as the premier destination for educational engagement, setting the
                standard for excellence in the digital learning space. We envision a future where every learner,
                regardless of background or location, has the opportunity to reach their full potential through our
                platform. We see ourselves as pioneers in the field, leading the charge towards a more informed and
                educated world. Our goal is to create a vibrant ecosystem where authors, publishers, and educators come
                together to share their wisdom, inspire minds, and make a lasting impact on the global educational
                landscape. In this pursuit, we remain steadfast in our dedication to innovation, quality, and the
                transformative power of education.</p>

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

</script>

</html>