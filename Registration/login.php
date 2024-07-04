<?php
include '../Shared Components\logger.php';
require_once '../Shared Components/dbconnection.php';


session_start();
try {

    // Defining error messages
    $emailError = $passwordError = '';

    // Checking if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Validating form data (basic validation for demonstration)
        if (empty($email)) {
            $emailError = 'Email is required';
        }
        if (empty($password)) {
            $passwordError = 'Password is required';
        }

        if (empty($emailError) && empty($passwordError)) {
            // Fetch the hashed password and user category from the database based on the provided email
            $stmt = $db->prepare("SELECT password, user_id, category, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_data) {
                $dbpass = $user_data['password'];
                $role = $user_data['role'];
                $category = $user_data['category'];
                $user_id = $user_data['user_id'];

                // Hash the provided password using SHA256 for comparison
                $hashed_input_password = hash('sha256', $password);

                // Compare the hashed input password with the hashed password from the database
                if ($hashed_input_password === $dbpass) {
                    // Credentials match, login successful
// Store user_id in the session
                    $_SESSION['user_id'] = $user_data['user_id'];
                    $_SESSION['category'] = $category;
                    $_SESSION['role'] = $role;
                    writeLog($db, "User logged in", "INFO", $user_id);

                    // Redirect based on user category
                    switch ($role) {
                        case 'Client':
                            header("Location: ../Buyer/buyerdashboard.php");
                            break;
                        case 'Dealer':
                            header("Location: ../Seller/sellerdashboard.php");
                            break;
                        case 'Admin':
                            header("Location: ../Admin/admindashboard.php");
                            break;
                        default:
                            header("Location: generic_dashboard.php");
                            break;
                    }
                    exit();
                } else {
                    writeLog($db, "User failed to log in due to wrong credentials", "ERROR", $user_id);

                    // Display error notification if credentials are incorrect
                    $passwordError = 'Incorrect email or password. Please try again.';

                }
            } else {
                // Display error notification if user with the provided email does not exist
                writeLog($db, "User failed to log in as user does not exist", "ERROR", $user_id);
                $emailError = 'User with the provided email does not exist.';
            }
        }
    }
} catch (PDOException $e) {
    // Display error notification if connection fails
    $connectionError = 'Connection failed: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="Stylesheet.css">
    <title>Registration</title>
</head>

<body>
    <div id="header-container"></div>

    <div class="wrapper" id="main-content">
        <div class="client-form">
            <div class="register-container">
                <div class="top">
                    <h1 class="header-h1">Welcome Back!!</h1>
                    <h5>Login to continue</h5>
                </div>

                <form action="login.php" method="post" id="LoginForm" name="form" autocomplete="true">
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="Email">Email</label>
                            <input type="text" class="inputfield" name="email" />
                            <div class="error"><?php echo $emailError; ?></div>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="Password">Password</label>
                            <input type="password" class="inputfield" id="password" name="password" />
                            <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility(this)"></i>

                            <div class="error"><?php echo $passwordError; ?></div>
                        </div>
                    </div>
                    <div class="UserCredentialsManager">
                        <div class="input-box">
                            <label>
                                <input type="checkbox" name="remember_me"> Remember Me
                            </label>
                        </div>
                        <div class="input-box">
                            <a href="#" onclick="showResetPasswordModal()">Forgot Password?</a>
                        </div>
                    </div>

                    <div class="submit-sect">
                        <button type="submit" class="register-button">Submit</button>
                    </div>
                </form>
                <div class="bottom">
                    <span class="light-text">Don't have an account? <a href="/Home/homepage.html"
                            class="reg-link">Register</a></span>
                </div>
            </div>
        </div>
    </div>
    <div id="resetpasswordmodal" class="modal">
        <form id="ResetPasswordForm" action="reset_password_request.php" method="post">

            <div id="resetpasswordmodal-header" class="modal-header">

                <h2>Reset Password</h2>
                <div class="close">
                    <i class="fa-solid fa-xmark" onclick="cancel();"></i>
                </div>
            </div>

            <div id="resetpasswordmodal-content" class="modal-content">

                <div class="input-box">
                    <div class="inputcontrol">
                        <label for="email2">Email</label>
                        <input type="text" class="inputfield" name="email2" />
                        <div class="error"><?php echo $emailError; ?></div>
                    </div>
                    <button type="submit" class="button">Submit</button>

                </div>
            </div>
        </form>
    </div>
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

        document.getElementById('resetpasswordmodal').style.display = "none";


    });

    document.getElementById("LoginForm").addEventListener('submit', function(e) {
        // Prevent the default form submission
        e.preventDefault();
        submitForm();
    });

    function submitForm() {
        var isValid = false;
        isValid = validateForm();
        if (isValid) {
            document.getElementById("LoginForm").submit();
        }
    }

    function validateForm() {
        var isValid = true;
        isValid = validateEmail('email') && isValid;
        isValid = validateField('password', 'Password is required') && isValid;

        return isValid;
    }


    function validateField(fieldName, errorMessage) {
        var inputField = document.getElementsByName(fieldName)[0];
        var inputControl = inputField.parentElement;
        var errorDisplay = inputControl.querySelector('.error');
        var fieldValue = inputField.value.trim();
        if (fieldValue === '') {
            inputControl.classList.add('error');
            inputControl.classList.remove('success');
            errorDisplay.textContent = errorMessage;
            return false;
        } else {
            errorDisplay.textContent = "";
            return true;
        }
    }

    function validateEmail(fieldName) {
        var inputField = document.getElementsByName(fieldName)[0];
        var inputControl = inputField.parentElement;
        var errorDisplay = inputControl.querySelector('.error');
        var email = inputField.value.trim();
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email === '') {
            var errorMessage = "Email is required";
            inputControl.classList.add('error');
            inputControl.classList.remove('success');
            errorDisplay.textContent = errorMessage;
            return false;
        } else if (!emailPattern.test(email)) {
            var errorMessage = "Invalid email format";
            inputControl.classList.add('error');
            inputControl.classList.remove('success');
            errorDisplay.textContent = errorMessage;
            return false;
        } else {
            errorDisplay.textContent = "";
            return true;
        }
    }

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

    function showResetPasswordModal() {
        document.getElementById('resetpasswordmodal').style.display = 'block';
        document.getElementById('main-content').classList.add('blurred');

    }

    function cancel() {
        document.getElementById('resetpasswordmodal').style.display = 'none';
        document.getElementById('main-content').classList.remove('blurred');

    }

    document.getElementById("ResetPasswordForm").addEventListener('submit', function(e) {
        e.preventDefault();
        var emailFieldName = 'email2';
        if (validateEmail(emailFieldName)) {
            var form = this;
            var formData = new FormData(form);
            fetch('reset_password_request.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    if (data.includes("Password reset email sent.")) {
                        document.getElementById('resetpasswordmodal').style.display = 'none';
                        document.getElementById('main-content').classList.remove('blurred');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
    </script>
    <div id="footer-container"></div>
</body>

</html>