<?php
include '../Shared Components\logger.php';

// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session

if (session_status() !== PHP_SESSION_ACTIVE) {
    error_reporting(E_ALL & ~E_NOTICE);

    session_start();
}


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.php");
    exit();
}

// Get user ID and category from session
$user_id = $_SESSION['user_id'];
$category = $_SESSION['category'];
$role = $_SESSION['role'];


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_POST['submit'])) {
    try {
        // Get feedback text from the form
        $feedback_text = $_POST["feedback_text"];
        $rating = $_POST['rating'];
        $rating = intval($rating);



        // Prepare SQL statement with parameterized query
        $stmt = $db->prepare("INSERT INTO feedback (user_id, feedback_text, rating) VALUES (:user_id, :feedback_text, :rating)");

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':feedback_text', $feedback_text);
        $stmt->bindParam(':rating', $rating); // Assuming $rating_int holds the integer rating value
        echo '<script>
        document.querySelector(".post").style.display = "block";
        document.querySelector(".star-widget").style.display = "none";
      </script>';



        // Execute SQL statement
        $stmt->execute();
        writeLog($db, "User has submitted feadback for the system", "INFO", $user_id);

    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
}



?>


<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

</head>

<body>
    <style>
    @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
        justify-content: space-around;

    }

    .feedback-container {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        align-items: center;
        width: 100%;
    }

    .upper-container {
        background-color: var(--accent-color2);
        height: 300px;
        text-align: center;
        padding: 30px;
        width: 100%;

    }

    .feedback-form {
        position: relative;
        margin-top: 40px;
        width: 40%;
        background: var(--primary-color);
        padding: 20px 30px;
        border: 1px solid #444;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: space-around;
        flex-direction: column;
        left: 30%;
    }

    .feedback-form .post {
        display: none;
    }

    .feedback-form .text {
        font-size: 25px;
        color: #666;
        font-weight: 500;
    }

    .feedback-form .edit {
        position: absolute;
        left: 10px;
        top: 5px;
        font-size: 16px;
        color: white;
        font-weight: 500;
        cursor: pointer;
    }

    .feedback-form .exit {
        position: absolute;
        right: 10px;
        top: 5px;
        font-size: 20px;
        color: white;
        font-weight: 100;
        cursor: pointer;
    }


    .feedback-form .edit:hover {
        text-decoration: underline;
    }

    .feedback-form .exit:hover {
        text-decoration: underline;
    }

    .feedback-form .star-widget input {
        display: none;
    }

    .upper-container h4 {
        color: var(--primary-color);
        font-size: 30px;
    }


    .star-widget label {
        font-size: 40px;
        color: #444;
        padding: 10px;
        float: right;
        transition: all 0.2s ease;
    }

    input:not(:checked)~label:hover,
    input:not(:checked)~label:hover~label {
        color: #fd4;
    }

    input:checked~label {
        color: #fd4;
    }

    input#rate-5:checked~label {
        color: #fe7;
        text-shadow: 0 0 20px #952;
    }

    #rate-1:checked~form header:before {
        content: "I just hate it ";
    }

    #rate-2:checked~form header:before {
        content: "I don't like it ";
    }

    #rate-3:checked~form header:before {
        content: "It is awesome ";
    }

    #rate-4:checked~form header:before {
        content: "I just like it ";
    }

    #rate-5:checked~form header:before {
        content: "I just love it ";
    }

    .feedback-form form {
        display: none;
    }

    input:checked~form {
        display: block;
    }

    form header {
        width: 100%;
        font-size: 25px;
        color: #fe7;
        font-weight: 500;
        margin: 5px 0 20px 0;
        text-align: center;
        transition: all 0.2s ease;
    }

    form .textarea {
        height: 100px;
        width: 100%;
        overflow: hidden;
    }

    form .textarea textarea {
        height: 100%;
        width: 100%;
        outline: none;
        color: var(---primary-color);
        border: 1px solid #333;
        background: var(--background-color2);
        padding: 10px;
        font-size: 17px;
        resize: none;
    }

    .textarea textarea:focus {
        border-color: #444;
    }

    form .btn {
        height: 45px;
        width: 100%;
        margin: 15px 0;
    }

    form .btn button {
        height: 100%;
        width: 100%;
        border: 1px solid #444;
        outline: none;
        background: var(--background-color2);
        color: var(--primary-color);
        font-size: 17px;
        font-weight: 500;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    form .btn button:hover {
        background: #1b1b1b;
    }
    </style>

    <!-- <div id="header-container"></div> -->

    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include '../Shared Components/headerdispatcher.php';
    ?>

    <div class="feedback-container">

        <div class="upper-container">
            <h4>Rate our Services</h4>
            <p>Have questions or need assistance with your orders?</p>
            <p>Reach out to us via email, phone, or the contact form below.</p>
            <div class="feedback-form">

                <div class="post">
                    <div class="text">Thanks for rating us!</div>
                    <div class="edit">EDIT</div>
                    <div class="exit">
                        <i class="fas fa-times-circle" onclick="goBack()"></i>
                    </div>

                </div>
                <div class="star-widget">
                    <input type="radio" value="5" name="rate" id="rate-5">
                    <label for="rate-5" class="fas fa-star"></label>
                    <input type="radio" value="4" name="rate" id="rate-4">
                    <label for="rate-4" class="fas fa-star"></label>
                    <input type="radio" value="3" name="rate" id="rate-3">
                    <label for="rate-3" class="fas fa-star"></label>
                    <input type="radio" value="2" name="rate" id="rate-2">
                    <label for="rate-2" class="fas fa-star"></label>
                    <input type="radio" value="1" name="rate" id="rate-1">
                    <label for="rate-1" class="fas fa-star"></label>
                    <form action="" id="feedback-form" method="post">
                        <header></header>
                        <div class="textarea">
                            <textarea cols="30" id="feedback_text" name="feedback_text"
                                placeholder="Describe your experience.."></textarea>
                        </div>
                        <input type="hidden" id="rating" name="rating">

                        <div class="btn">
                            <button id="submit" type="submit">Post</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    </form>
    <script>
    const btn = document.querySelector("button");
    const post = document.querySelector(".post");
    const widget = document.querySelector(".star-widget");
    const editBtn = document.querySelector(".edit");
    btn.onclick = () => {

        const rating = document.querySelector('input[name="rate"]:checked').value;
        console.log(rating);

        // Set the value of the hidden input field
        document.getElementById("rating").value = rating;
        document.getElementById("feedback-form").submit();
        widget.style.display = "none";
        post.style.display = "block";
    }

    editBtn.onclick = () => {
        widget.style.display = "block";
        post.style.display = "none";

        return false;
    }



    <?php if ($role === 'Client'): ?>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('../Buyer/header.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('header-container').innerHTML = data;
            });
    });
    <?php else: ?>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('/Buyer/header.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('header-container').innerHTML = data;
            });
    });
    <?php endif; ?>



    //Function to navigate back to the previous page

    function goBack() {
        window.history.back();

    }
    </script>

</body>

</html>