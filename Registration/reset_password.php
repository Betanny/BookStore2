<?php
require_once '../Shared Components/dbconnection.php';
include '../Shared Components/logger.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    try {
        // Check if the token is valid
        $stmt = $db->prepare("SELECT user_id, reset_token_expiry FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && strtotime($user['reset_token_expiry']) > time()) {
            // Hash the new password
            $hashed_password = hash('sha256', $new_password);

            // Update the user's password in the database
            $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
            $stmt->execute([$hashed_password, $token]);

            writeLog($db, "Password reset for user ID " . $user['user_id'], "INFO", $user['user_id']);
            echo "Password has been reset.";
        } else {
            echo "Invalid or expired token.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else if (isset($_GET['token'])) {
    $token = $_GET['token'];
    ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password</title>
        </head>

        <body>
            <form action="reset_password.php" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div>
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div>
                    <button type="submit">Reset Password</button>
                </div>
            </form>
        </body>

        </html>
    <?php
}
?>