<?php
session_start()
    ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>

<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="LOGO">

        </div>
        <input type="checkbox" id="nav_check" hidden>
        <nav>

            <ul>
                <li><a href="/Home/homepage.html" class="link light-text active-link">Home</a></li>
                <li><a href="#" class="link light-text">Products</a></li>
                <li><a href="#" class="link light-text">About us</a></li>
                <li><a href="#" class="link-active">Contact us</a></li>
            </ul>

        </nav>
        <label for="nav_check" class="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </label>


    </header>
</body>

</html>