<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';
include '../Shared Components/logger.php';

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
try {
    // Determine which table to query based on user category
    $table_name = '';
    switch ($category) {
        case 'Author':
            $table_name = 'authors';
            break;
        case 'Publisher':
            $table_name = 'publishers';
            break;
        case 'Manufacturer':
            $table_name = 'manufacturers';
            break;
        // Add more cases as needed
    }


    // Query the appropriate table to fetch data
    $sql = "SELECT * FROM $table_name WHERE user_id = $user_id";

    // Execute the query and fetch the results
    $stmt = $db->query($sql);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);



    switch ($category) {
        case 'Author':
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $full_name = $first_name . ' ' . $last_name;
            global $first_name, $full_name;

            break;
        case 'Publisher':
            $full_name = $first_name = $data['publisher_name'];
            $last_name = "";
            global $first_name, $full_name;
            break;
        case 'Manufacturer':
            $full_name = $first_name = $data['manufacturer_name'];
            $last_name = "";

            break;
        // Add more cases as needed
    }

    // Query the appropriate table to fetch data
    $emailsql = "SELECT * FROM users WHERE user_id = $user_id";
    // Execute the query and fetch the results
    $userstmt = $db->query($emailsql);
    $userdata = $userstmt->fetch(PDO::FETCH_ASSOC);
    $email = $userdata['email'];
    global $email;
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agreed'])) {
        writeLog($db, "User has signed the dealer aggreement and is now a legal dealer in the system", "INFO", $user_id);

        // Prepare and execute SQL statement to update user agreement status
        $stmt = $db->prepare("UPDATE users SET \"agreementStatus\" = 'Accepted', \"agreementTime\" = NOW() WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Redirect to seller dashboard after updating agreement
        header("Location: sellerdashboard.php");
        exit(); // Ensure script stops executing after redirection
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
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .agreement-content p {
            margin-bottom: 10px;
        }

        .agreement-content strong {
            font-weight: bold;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 15px;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        button {
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: var(--primary-color);
            height: 35px;
            width: 25%;
            margin-left: 35%;
        }

        button:hover {
            background-color: var(--accent-color2);
            color: var(--primary-color);
            font-weight: bolder;


        }

        .decline-button {
            margin-top: 20px;
            color: var(--primary-color);
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: var(--background-color);
            border: 1px solid var(--primary-color);
            ;
            height: 35px;
            width: 25%;
            margin-left: 35%;
        }

        .parties {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            margin: 20px 10px;
        }
    </style>
</head>

<body>
    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components/headerdispatcher.php"
        ?>
    <div class="container">
        <h2>Dealer Agreement</h2>
        <div class="agreement-content">
            <p><strong>1. Purpose</strong></p>
            <p>The purpose of this Agreement is to set forth the terms and conditions under which the Company will
                compensate dealers for the sale of books through the Company's website.</p>

            <p><strong>2. Appointment</strong></p>
            <p>The Company hereby appoints dealers as non-exclusive sales agents for the sale of books through the
                Company's website, and dealers accept such appointment.</p>

            <p><strong>3. Duties of the Dealer</strong></p>
            <p>Dealers agree to use reasonable efforts to promote and sell the books listed on the Company's website.
                Dealers will not engage in any activities that are detrimental to the interests of the Company.</p>

            <p><strong>4. Commission</strong></p>
            <p>a. The Company agrees to pay dealers a commission of 2% of the total sales price of each book sold
                through the Company's website as a result of the dealer's efforts.</p>
            <p>b. Total sales price is defined as the gross amount paid by the customer, excluding shipping, handling,
                taxes, and any discounts applied.</p>
            <p>c. Payment will be made to dealers upon purchase. The commission will be deducted from the total sales
                price, and the remainder will be deposited into the dealer's bank account.</p>

            <p><strong>5. Payment Process</strong></p>
            <p>a. The Company will provide a sales report to dealers on a monthly basis detailing the total sales made
                in the previous month.</p>
            <p>b. Commission payments will be calculated based on the sales report provided.</p>
            <p>c. The Company will deposit the commission amount into the dealer's bank account within [number of days]
                of the end of each month.</p>

            <p><strong>6. Delivery</strong></p>
            <p>a. The Company agrees that deliveries of orders made through the Company's website shall not exceed 5
                days after the order date.</p>

            <p><strong>7. Term and Termination</strong></p>
            <p>a. This Agreement shall commence on the date first written above and shall continue until terminated by
                either party with thirty (30) days written notice.</p>
            <p>b. Either party may terminate this Agreement immediately in the event of a material breach by the other
                party that is not cured within fifteen (15) days of written notice of such breach.</p>

            <p><strong>8. Confidentiality</strong></p>
            <p>Dealers agree to keep confidential all information relating to the business of the Company and will not
                disclose any such information to any third party without the prior written consent of the Company.</p>

            <p><strong>9. Governing Law</strong></p>
            <p>This Agreement shall be governed by and construed in accordance with the laws of the [State/Country],
                without regard to its conflict of law principles.</p>

            <p><strong>10. Entire Agreement</strong></p>
            <p>This Agreement constitutes the entire agreement between the parties with respect to the subject matter
                hereof and supersedes all prior agreements and understandings, whether written or oral, relating to such
                subject matter.</p>

            <p><strong>11. Amendments</strong></p>
            <p>No amendment or modification of this Agreement shall be valid or binding unless made in writing and
                signed by both parties.</p>

            <p><strong>12. Notices</strong></p>
            <p>All notices required or permitted under this Agreement shall be in writing and shall be deemed delivered
                when delivered in person, by electronic mail, or deposited in the United States mail, postage prepaid,
                addressed as follows:</p>
            <div class="parties">
                <p>Promisee:<br>
                    SmartCBC<br>
                    smartcbc@gmail.com</p>
                <p>Promisor:<br>
                    <?php echo $full_name; ?>
                    <br>
                    <?php echo $email; ?>
                </p>
            </div>
        </div>
        <form id="agreementForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="agreed" value="1">
            <label for="agreeCheckbox">
                <input type="checkbox" id="agreeCheckbox" required>
                I agree to the terms and conditions stated above.
            </label>
            <button type="submit" id="acceptButton">Accept</button>
            <button type="button" class="decline-button" onclick="cancel();" id="declineButton">Decline</button>

        </form>

    </div>

    <script>

        document.addEventListener("DOMContentLoaded", function () {

            const agreeCheckbox = document.getElementById("agreeCheckbox");
            const acceptButton = document.getElementById("acceptButton");

            acceptButton.addEventListener("click", function () {
                if (agreeCheckbox.checked) {
                    alert("Agreement accepted!");
                    // You can add further actions here, like submitting the form
                } else {
                    alert("Please agree to the terms and conditions before accepting.");
                }
            });
        });

        function cancel() {
            window.location.href = "/Home/homepage.html";
        }
    </script>
</body>

</html>