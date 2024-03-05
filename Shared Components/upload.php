<?php
/*
if (isset($_POST['submit'])) {
    // Front Cover Image
    $frontImage = $_FILES['Front-cover'];
    $frontImageName = $_FILES['Front-cover']['name'];
    $frontImageTmpName = $_FILES['Front-cover']['tmp_name'];
    $frontImageSize = $_FILES['Front-cover']['size'];
    $frontImageError = $_FILES['Front-cover']['error'];
    $frontImageType = $_FILES['Front-cover']['type'];

    $frontImageExt = explode('.', $frontImageName);
    $frontImageActualExt = strtolower(end($frontImageExt));

    // Back Cover Image
    $backImage = $_FILES['Back-cover'];
    $backImageName = $_FILES['Back-cover']['name'];
    $backImageTmpName = $_FILES['Back-cover']['tmp_name'];
    $backImageSize = $_FILES['Back-cover']['size'];
    $backImageError = $_FILES['Back-cover']['error'];
    $backImageType = $_FILES['Back-cover']['type'];
    var_dump($frontImageTmpName, $frontImageDestination);


    $backImageExt = explode('.', $backImageName);
    $backImageActualExt = strtolower(end($backImageExt));

    // Arrays of files we want to allow
    $allowed = array('jpg', 'jpeg', 'png');

    // Handling Front Cover Image
    if (in_array($frontImageActualExt, $allowed) && in_array($backImageActualExt, $allowed)) {
        if ($frontImageError === UPLOAD_ERR_OK && $backImageError === UPLOAD_ERR_OK) {
            if ($frontImageSize < 1000000 && $backImageSize < 1000000) {
                $frontImageNameNew = uniqid('', true) . "." . $frontImageActualExt;
                $frontImageDestination = '/Images/uploads' . $frontImageNameNew;
                move_uploaded_file($frontImageTmpName, $frontImageDestination);

                $backImageNameNew = uniqid('', true) . "." . $backImageActualExt;
                $backImageDestination = 'Images/uploads/' . $backImageNameNew;
                move_uploaded_file($backImageTmpName, $backImageDestination);

                echo "Images uploaded successfully!";
            } else {
                echo "Your image is too big.";
            }
        } else {
            echo "Error uploading images.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
    }
}*/



if (isset($_POST['submit'])) {
    // Front Cover Image
    $frontImage = $_FILES['Front-cover'];
    $frontImageName = $_FILES['Front-cover']['name'];
    $frontImageTmpName = $_FILES['Front-cover']['tmp_name'];
    $frontImageSize = $_FILES['Front-cover']['size'];
    $frontImageError = $_FILES['Front-cover']['error'];
    $frontImageType = $_FILES['Front-cover']['type'];

    $frontImageExt = explode('.', $frontImageName);
    $frontImageActualExt = strtolower(end($frontImageExt));

    // Back Cover Image
    $backImage = $_FILES['Back-cover'];
    $backImageName = $_FILES['Back-cover']['name'];
    $backImageTmpName = $_FILES['Back-cover']['tmp_name'];
    $backImageSize = $_FILES['Back-cover']['size'];
    $backImageError = $_FILES['Back-cover']['error'];
    $backImageType = $_FILES['Back-cover']['type'];

    $backImageExt = explode('.', $backImageName);
    $backImageActualExt = strtolower(end($backImageExt));

    // Arrays of files we want to allow
    $allowed = array('jpg', 'jpeg', 'png');

    // Handling Front Cover Image
    if (in_array($frontImageActualExt, $allowed) && in_array($backImageActualExt, $allowed)) {
        if ($frontImageError === UPLOAD_ERR_OK && $backImageError === UPLOAD_ERR_OK) {
            if ($frontImageSize < 1000000 && $backImageSize < 1000000) {
                $frontImageNameNew = uniqid('', true) . "." . $frontImageActualExt;
                $frontImageDestination = 'D:\xammp2\htdocs\BookStore2\Images\uploads/' . $frontImageNameNew;
                move_uploaded_file($frontImageTmpName, $frontImageDestination);

                $backImageNameNew = uniqid('', true) . "." . $backImageActualExt;
                $backImageDestination = 'D:\xammp2\htdocs\BookStore2\Images\uploads/' . $backImageNameNew;
                move_uploaded_file($backImageTmpName, $backImageDestination);

                echo "Images uploaded successfully!";
            } else {
                echo "Your image is too big.";
            }
        } else {
            echo "Error uploading images.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
    }
}
?>