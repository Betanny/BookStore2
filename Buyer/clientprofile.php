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
$role = $_SESSION['role'];

if ($role == "Admin") {
    $user_id = $_GET['user_id'];
    writeLog($db, "Admin is accessing a user's profile. User: $user_id", "INFO", $_SESSION['user_id']);


} else {

    writeLog($db, "User is accessing their profile", "INFO", $user_id);

}
$CurrentpasswordError = $passwordError = '';


try {
    $user_sql = "SELECT * FROM users WHERE user_id = :user_id";
    $user_stmt = $db->prepare($user_sql);
    $user_stmt->bindParam(':user_id', $user_id);
    $user_stmt->execute();
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    $profile_sql = "SELECT * FROM clients WHERE user_id = :user_id";
    $profile_stmt = $db->prepare($profile_sql);
    $profile_stmt->bindParam(':user_id', $user_id);
    $profile_stmt->execute();
    $profile = $profile_stmt->fetch(PDO::FETCH_ASSOC);
    $category = $user['category'];

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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Capture form data
        $email = $_POST['author'];
        $address = $_POST['address'];
        $county = $_POST['county'];
        $phone = $_POST['phone'];
        $contact_full_name = $_POST['full_name'] ?? null;
        $contact_email = $_POST['Email'] ?? null;
        $contact_phone = $_POST['contact_phone'] ?? null;
        $current_password = $_POST['password'];
        $new_password = $_POST['org-password'];
        $confirm_password = $_POST['org-password2'];

        // Update user details
        $update_sql = "UPDATE clients SET 
            email = :email, 
            address = :address, 
            county = :county, 
            phone = :phone";

        if ($category == 'Organization') {
            $update_sql .= ",
                             contact_email = :contact_email, 
                             contact_phone = :contact_phone";
        }

        $update_sql .= " WHERE user_id = :user_id";

        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bindParam(':email', $email);
        $update_stmt->bindParam(':address', $address);
        $update_stmt->bindParam(':county', $county);
        $update_stmt->bindParam(':phone', $phone);
        if ($category == 'Organization') {
            $update_stmt->bindParam(':contact_email', $contact_email);
            $update_stmt->bindParam(':contact_phone', $contact_phone);
        }
        $update_stmt->bindParam(':user_id', $user_id);
        $update_stmt->execute();

        // Handle password change
        if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
            if ($new_password !== $confirm_password) {
                $passwordError = "New passwords do not match.";
            }

            $hashed_current_password = hash('sha256', $current_password);

            if ($hashed_current_password !== $user['password']) {
                $CurrentpasswordError = "Current pasword is not correct, retry again";
            }

            $hashed_new_password = hash('sha256', $new_password);

            $password_update_sql = "UPDATE users SET password = :password WHERE user_id = :user_id";
            $password_update_stmt = $db->prepare($password_update_sql);
            $password_update_stmt->bindParam(':password', $hashed_new_password);
            $password_update_stmt->bindParam(':user_id', $user_id);
            $password_update_stmt->execute();
        }

        echo "Profile updated successfully.";

        if ($_SESSION['role'] == "Admin") {
            writeLog($db, "Admin has edited a user's profile. User: $user_id", "INFO", $_SESSION['user_id']);

            header("Location: ../Admin/users.php");
        } else {
            writeLog($db, "User has edited their profile", "INFO", $user_id);
            header("Location: buyerdashboard.php");

        }
    }
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

    <style>
    .error {
        color: red;
    }
    </style>
</head>

<body>
    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components\headerdispatcher.php"
        ?>
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
                        <input type="text" class="inputfield" name="booktitle" value="<?php echo $full_name; ?>"
                            readonly />
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


                <h4><br><br>Change Password?</h4><br>
                <div class="input-box">
                    <div class="inputcontrol">
                        <div class="error"><?php echo $CurrentpasswordError; ?></div>
                        <label for="Password">Current Password</label>
                        <input type="password" class="inputfield" id="password" name="password" />
                        <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility(this)"></i>
                    </div>
                </div>
                <div class="two-forms">
                    <div class="form-group">
                        <div class="inputcontrol">
                            <div class="error"><?php echo $passwordError; ?></div>
                            <label for="Password">New Password</label>
                            <input type="password" class="inputfield" id="org-password" name="org-password" />
                            <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility(this)"></i>

                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="inputcontrol">
                            <label for="org-password2">Confirm New Password</label>
                            <input type="password" class="inputfield" id="org-password2" name="org-password2" />
                            <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility(this)"></i>

                            <div class="error"></div>
                        </div>
                    </div>
                </div>


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

// <php if ($role == 'Client'): ?>
//     document.addEventListener("DOMContentLoaded", function () {
//         fetch('../Buyer/header.php')
//             .then(response => response.text())
//             .then(data => {
//                 document.getElementById('header-container').innerHTML = data;
//             });
//     });
// <php elseif ($role == 'Dealer'): ?>
//     document.addEventListener("DOMContentLoaded", function () {
//         fetch('../Seller/header.php')
//             .then(response => response.text())
//             .then(data => {
//                 document.getElementById('header-container').innerHTML = data;
//             });
//     });
// <php else: ?>
//     document.addEventListener("DOMContentLoaded", function () {
//         fetch('/Admin/header.php')
//             .then(response => response.text())
//             .then(data => {
//                 document.getElementById('header-container').innerHTML = data;
//             });
//     });
// <php endif; ?>

function togglePasswordVisibility(icon) {
    var passwordField = icon.previousElementSibling; // Get the input field before the icon
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}
</script>


</html>