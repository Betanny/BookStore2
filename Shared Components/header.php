<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/svg+xml" href="/Shared Components/smartcbc.svg">
    <title>Document</title>
</head>

<body>
    <header>
        <div class="logo">
            <img src="/Shared Components\smartcbc.svg" style="width:150px !important" alt="LOGO">
        </div>
        <input type="checkbox" id="nav_check" hidden>
        <nav>
            <ul id="nav-menu">
                <li><a href="/Home/homepage.html" name="nav-link" class="link light-text active-link">Home</a></li>
                <li><a href="/Home/products.php" name="nav-link" class="link light-text">Products</a></li>
                <li><a href="/Home/Aboutus.html" name="nav-link" class="link light-text">About us</a></li>
                <li><a href="/Home/contactus.html" name="nav-link" class="link light-text">Contact us</a></li>
                <li><a href="/Registration/login.php" class="link-active">Login</a></li>
            </ul>
        </nav>
        <label for="nav_check" class="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </label>
    </header>
</body>

<script>
// Get all the links
const links = document.getElementsByClassName('link');
document.addEventListener("DOMContentLoaded", function() {

    // Add click event listeners to each link
    Array.from(links).forEach(link => {
        link.addEventListener('click', function(event) {
            // Prevent the default action

            // Remove the 'active-link' class from all links
            Array.from(links).forEach(link => {
                link.classList.remove('active-link');
            });

            // Add the 'active-link' class to the clicked link
            this.classList.add('active-link');


        });
    });

    const navMenu = document.getElementById('nav-menu');
    const links = navMenu.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', function(event) {
            // Prevent default behavior if needed
            // event.preventDefault();

            // Remove active-link class from all links
            links.forEach(link => link.classList.remove('active-link'));

            // Add active-link class to the clicked link
            this.classList.add('active-link');
        });
    });
});
</script>

</html>