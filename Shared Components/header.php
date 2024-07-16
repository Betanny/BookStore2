<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Home/style.css">
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">
    <title>SmartCBC</title>
</head>

<body>
    <header>
        <div class="logo">
            <img src="/Shared Components/smartcbc.svg" style="width:150px !important" alt="LOGO">
        </div>
        <input type="checkbox" id="nav_check" hidden>
        <nav>
            <ul id="nav-menu">
                <li><a href="/Home/homepage.php" id="home-link" class="link light-text">Home</a></li>
                <li><a href="/Home/products.php" id="products-link" class="link light-text">Products</a></li>
                <li><a href="/Home/Aboutus.php" id="Aboutus-link" class="link light-text">About us</a></li>
                <li><a href="/Home/contactus.php" id="contactus-link" class="link light-text">Contact us</a></li>
                <li><a href="/Registration/login.php" class="link-active">Login</a></li>
            </ul>
        </nav>
        <label for="nav_check" class="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </label>
    </header>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const links = document.querySelectorAll('#nav-menu a');

        // Function to deactivate all links
        function deactivateAllLinks() {
            links.forEach(link => {
                console.log(links);
                link.classList.remove('active-link');
            });
        }

        // Function to activate the correct link based on the current URL
        function activateLink() {
            const currentPath = window.location.pathname;
            console.log(currentPath);
            links.forEach(link => {
                if (currentPath.endsWith(link.getAttribute('href'))) {
                    link.classList.add('active-link');
                }
            });
        }

        deactivateAllLinks();
        activateLink();
    });
    </script>
</body>

</html>