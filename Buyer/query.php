<?php
if (isset($_GET['checkoutRequestID'])) {
    $checkoutRequestID = $_GET['checkoutRequestID'];

    // Include gen_token.php to fetch the access token
    ob_start();
    include 'gen_token.php';
    $accessToken = ob_get_clean();

    // Set up your cURL request to query STK Push status
    $shortCode = '174379'; // Sandbox Short Code
    $passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'; // Sandbox Pass Key

    for ($i = 0; $i < 5; $i++) {
        // Generate current timestamp
        $timestamp = date('YmdHis');
        $password = base64_encode($shortCode . $passKey . $timestamp);

        // Set up cURL for each iteration
        $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query');

        // Set headers and other options
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the request body with dynamic timestamp and CheckoutRequestID
        $requestBody = json_encode([
            "BusinessShortCode" => $shortCode,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "CheckoutRequestID" => $checkoutRequestID
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Execute the request and capture the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            // Decode the response
            $responseData = json_decode($response, true);

            // Check the resultCode
            if (isset($responseData['ResultCode']) && $responseData['ResultCode'] == 0) {
                // Payment successful, redirect to place_order
                echo '<script>window.location.href = "place_order.php?transaction=success";</script>';
                exit();
            }

            // Output the response for debugging
            // echo "Attempt " . ($i + 1) . ": " . $response . "<br>";
        }

        // Close curl session
        curl_close($ch);

        // Sleep for 30 seconds before the next attempt
        sleep(5);
        if ($i == 4) {
            header("Location: checkout.php?error=timedout");
            exit();

        }
    }

    // If the loop completes without success, redirect to checkout with error
    header("Location: checkout.php?error=failed");
} else {
    header("Location: checkout.php?error=failed");
}