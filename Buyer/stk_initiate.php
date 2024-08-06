<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contactNumber = $_POST['paymentNumber'];
    $amount = $_POST['amount'];

    // writeLog($db, "User has initiated payment via mpesa and is paying with the mpesa number " . $contactNumber . " for the amount " . $amount, "INFO", $user_id);

    var_dump($contactNumber);

    // $contactNumber = (int) $contactNumber;
    $formattedNumber = (int) 254 * pow(10, strlen($contactNumber) - 1) + (int) $contactNumber;
    var_dump($formattedNumber);

    //echo $formattedNumber; // Output: 254798989898

    // var_dump($formattedNumber);

    $_SESSION['post_data'] = $_POST;


    // Include gen_token.php to fetch the access token
    ob_start();
    include 'gen_token.php';
    $accessToken = ob_get_clean();
    var_dump($accessToken);

    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $shortCode = '174379'; // Sandbox Short Code
    $timestamp = date('YmdHis');
    $passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'; // Sandbox Pass Key
    $password = base64_encode($shortCode . $passKey . $timestamp);

    $curl_post_data = [
        'BusinessShortCode' => $shortCode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => 1, // Use the amount from $_POST
        'PartyA' => $formattedNumber, // Customer phone number (Ensure it starts with country code 254)
        'PartyB' => $shortCode,
        'PhoneNumber' => $formattedNumber, // Customer phone number (Ensure it starts with country code 254)
        'CallBackURL' => 'https://yourdomain.com/callback.php', // Your callback URL
        'AccountReference' => 'SCBC',
        'TransactionDesc' => 'Payment for X'
    ];

    $data_string = json_encode($curl_post_data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accessToken)); // Access token passed here
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    $curl_response = curl_exec($curl);

    // Check if the request was successful
    if ($curl_response === false) {
        $error = curl_error($curl);
        echo "Curl Error: " . $error;
    } else {
        $response = json_decode($curl_response, true);
        if (isset($response['CheckoutRequestID'])) {
            $checkoutRequestID = $response['CheckoutRequestID'];
            // Redirect to query.php passing CheckoutRequestID as a query parameter
            header("Location: query.php?checkoutRequestID=$checkoutRequestID");
            exit;
        } else {
            echo "Failed to retrieve CheckoutRequestID from response";
        }
    }

    curl_close($curl);
}