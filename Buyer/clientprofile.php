<?php
include '../Shared Components\logger.php';

// Include database connection file
require_once '../Shared Components/dbconnection.php';

// Start session
session_start();
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

writeLog($db, "User is accessing their profile", "INFO", $user_id);

try {


    $profile_sql = "SELECT * FROM clients WHERE user_id = :user_id";
    $profile_stmt = $db->prepare($profile_sql);
    $profile_stmt->bindParam(':user_id', $user_id);
    $profile_stmt->execute();
    $profile = $profile_stmt->fetch(PDO::FETCH_ASSOC);

    switch ($category) {
        case 'Individual':
            $first_name = $profile['first_name'];
            $last_name = $profile['last_name'];
            $full_name = $first_name . ' ' . $last_name;
            global $first_name, $full_name;

            break;
        case 'Organization':
            $full_name = $first_name = $profile['organization_name'];
            $last_name = "";
            $contact_first_name = $profile['contact_first_name'];
            $contact_last_name = $profile['contact_last_name'];
            $contact_full_name = $contact_first_name . ' ' . $contact_last_name;
            global $first_name, $full_name, $contact_full_name;
            break;

    }

    global $profile;


} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="/Registration/Stylesheet.css">
    <link rel="stylesheet" href="seller.css">
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">


</head>

<body>
    <div id="header-container"></div>
    <div class="modal" id="editprofile-modal">
        <form action="#" method="post">

            <input type="hidden" name="user_id" value="<?php echo $profile['user_id']; ?>">

            <div class="modal-header">
                <h2 class="modal-title">Edit Profile</h2>
                <div class="close">
                    <i class="fa-solid fa-xmark" onclick="goBack();"></i>
                </div>
            </div>
            <div class="modal-content">
                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="BookTitle">Full Name</label class="no-asterisk">
                        <input type="text" class="inputfield" name="booktitle" value="<?php echo $full_name; ?>" />
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="Author">Email</label class="no-asterisk">
                        <input type="text" class="inputfield" name="author" value="<?php echo $profile['email']; ?>" />
                    </div>

                </div>

                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="Language">address</label class="no-asterisk">
                        <input type="text" class="inputfield" name="address"
                            value="<?php echo $profile['address']; ?>" />
                    </div>
                </div>

                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="county">county</label class="no-asterisk">
                        <input type="text" class="inputfield" name="county" value="<?php echo $profile['county']; ?>" />
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="points">Points</label class="no-asterisk">
                        <input type="number" class="inputfield" name="points" value="<?php echo $profile['points']; ?>"
                            readonly />
                    </div>
                </div>
                <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="phone">phone</label class="no-asterisk">
                        <input type="text" class="inputfield" name="phone" value="<?php echo $profile['phone']; ?>" />
                    </div>
                </div>
                <!-- <div class="input-box">
                    <div class="inputcontrol">
                        <label class="no-asterisk" for="biography"> Book Description</label class="no-asterisk">
                        <textarea class="inputfield" name="biography"
                            style="height: 150px;"><php echo $profile['biography']; ?></textarea>

                    </div>
                </div> -->
                <!-- ?php endif; ?> -->
                <?php if ($category == 'Organization'): ?>
                    <h4><br><br>Contact Person details</h4><br>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="full_name">Full Name</label class="no-asterisk">
                            <input type="text" class="inputfield" name="full_name"
                                value="<?php echo $contact_full_name; ?>" />
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label class="no-asterisk" for="Email">Email</label class="no-asterisk">
                            <input type="text" class="inputfield" name="Email"
                                value="<?php echo $profile['contact_email']; ?>" />
                        </div>
                        <div class="input-box">
                            <div class="inputcontrol">
                                <label class="no-asterisk" for="phone">phone</label class="no-asterisk">
                                <input type="text" class="inputfield" name="phone"
                                    value="<?php echo $profile['contact_phone']; ?>" />
                            </div>
                        </div>

                    </div>
                <?php endif; ?>


                <div class="modal-buttons">
                    <button class="button" type="button" onclick="goBack();">Cancel</button>
                    <button class="button" type="submit">Save Changes</button>

                </div>
            </div>



    </div>




</body>
<script>
    var modal = document.getElementById("editprofile-modal");

    function editProfile() {
        // Get the modal
        modal.style.display = "block";
    }

    function goBack() {
        modal.style.display = "none";
        window.history.back();

    }

    <?php if ($role == 'Client'): ?>
        document.addEventListener("DOMContentLoaded", function () {
            fetch('../Buyer/header.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('header-container').innerHTML = data;
                });
        });
    <?php elseif ($role == 'Dealer'): ?>
        document.addEventListener("DOMContentLoaded", function () {
            fetch('../Seller/header.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('header-container').innerHTML = data;
                });
        });
    <?php else: ?>
        document.addEventListener("DOMContentLoaded", function () {
            fetch('/Buyer/header.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('header-container').innerHTML = data;
                });
        });
    <?php endif; ?>
</script>


</html>