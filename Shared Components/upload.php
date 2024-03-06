<?php

/*
if (isset($_POST['submit'])) {
    // Array to store error messages
    $errors = [];

    // Function to validate file extension
    function isValidExtension($fileName, $allowedExtensions)
    {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return in_array($fileExt, $allowedExtensions);
    }

    // Array of allowed file extensions
    $allowedExtensions = array('jpg', 'jpeg', 'png');

    // Loop through each image
    foreach ($_FILES as $key => $image) {
        $imageName = $image['name'];
        $imageTmpName = $image['tmp_name'];
        $imageSize = $image['size'];
        $imageError = $image['error'];
        $imageType = $image['type'];

        // Check if the file is uploaded without errors
        if ($imageError === UPLOAD_ERR_OK) {
            // Validate file extension
            if (isValidExtension($imageName, $allowedExtensions)) {
                // Check file size
                if ($imageSize < 1000000000) { // Adjust file size limit as needed
                    // Generate unique file name
                    $imageNewName = uniqid('', true) . "." . strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                    // Set upload destination
                    $imageDestination = 'D:\xammp2\htdocs\BookStore2\Images\uploads/' . $imageNewName;
                    // Move uploaded file to destination
                    if (move_uploaded_file($imageTmpName, $imageDestination)) {
                        echo "Image uploaded successfully: $imageNewName <br>";
                    } else {
                        $errors[] = "Error uploading image: $imageName";
                    }
                } else {
                    $errors[] = "Image size exceeds the limit: $imageName";
                }
            } else {
                $errors[] = "Invalid file type for image: $imageName";
            }
        } else {
            $errors[] = "Error occurred while uploading image: $imageName";
        }
    }

    // Display any errors encountered
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
} 


*/


// if (isset($_POST['submit'])) {
//     // Array to store error messages
//     $errors = [];
//     // Paths for front, back, and other images
//     $frontPageImagePath = '';
//     $backPageImagePath = '';
//     $otherImagePaths = [];

//     // Function to validate file extension
//     function isValidExtension($fileName, $allowedExtensions)
//     {
//         $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
//         return in_array($fileExt, $allowedExtensions);
//     }

//     // Array of allowed file extensions
//     $allowedExtensions = array('jpg', 'jpeg', 'png');

//     // Loop through each image
//     foreach ($_FILES as $key => $image) {
//         $imageName = $image['name'];
//         $imageTmpName = $image['tmp_name'];
//         $imageSize = $image['size'];
//         $imageError = $image['error'];
//         $imageType = $image['type'];

//         // Check if the file is uploaded without errors
//         if ($imageError === UPLOAD_ERR_OK) {
//             // Validate file extension
//             if (isValidExtension($imageName, $allowedExtensions)) {
//                 // Check file size
//                 if ($imageSize < 1000000000) { // Adjust file size limit as needed
//                     // Generate unique file name
//                     $imageNewName = uniqid('', true) . "." . strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
//                     // Set upload destination
//                     $imageDestination = 'D:\xammp2\htdocs\BookStore2\Images\uploads/' . $imageNewName;
//                     // Move uploaded file to destination
//                     if (move_uploaded_file($imageTmpName, $imageDestination)) {
//                         echo "Image uploaded successfully: $imageNewName <br>";
//                         // Determine image type and store path accordingly
//                         switch ($key) {
//                             case 'Front-cover':
//                                 $frontPageImagePath = $imageDestination;
//                                 break;
//                             case 'Back-cover':
//                                 $backPageImagePath = $imageDestination;
//                                 break;
//                             default:
//                                 $otherImagePaths[] = $imageDestination;
//                                 break;
//                         }
//                     } else {
//                         $errors[] = "Error uploading image: $imageName";
//                     }
//                 } else {
//                     $errors[] = "Image size exceeds the limit: $imageName";
//                 }
//             } else {
//                 $errors[] = "Invalid file type for image: $imageName";
//             }
//         } else {
//             $errors[] = "Error occurred while uploading image: $imageName";
//         }
//     }

//     // Display any errors encountered
//     if (!empty($errors)) {
//         foreach ($errors as $error) {
//             echo $error . "<br>";
//         }
//     } else {
//         // Proceed to insert data into the database
//         // Collect form data
//         $booktitle = $_POST["booktitle"];
//         $author = $_POST["author"];
//         $publisher = $_POST["publisher"];
//         $genre = $_POST["genre"];
//         $languages = $_POST["languages"];
//         $ISBN = $_POST["ISBN"];
//         $edition = $_POST["edition"];
//         $subjects = $_POST["subjects"];
//         $grade = $_POST["grade"];
//         $series = $_POST["series"];
//         $covertype = $_POST["covertype"];
//         $damaged = $_POST["damaged"];
//         $pages = $_POST["pages"];
//         $details = $_POST["details"];
//         $retailprice = $_POST["retailprice"];
//         $priceinbulk = $_POST["priceinbulk"];
//         $mininbulk = $_POST["mininbulk"];

//         // Database connection and insert query
//         $db_connection = new mysqli("localhost", "username", "password", "database");
//         if ($db_connection->connect_error) {
//             die("Connection failed: " . $db_connection->connect_error);
//         }

//         // Prepare SQL statement
//         $sql = "INSERT INTO books (title, author, publisher, genre, language, isbn, edition, subject, grade, series, covertype, damaged, pages, details, retailprice, priceinbulk, mininbulk, front_page_image, back_page_image, other_images)
//         VALUES ('$booktitle', '$author', '$publisher', '$genre', '$languages', '$ISBN', '$edition', '$subjects', '$grade', '$series', '$covertype', $damaged, $pages, '$details', $retailprice, $priceinbulk, $mininbulk, '$frontPageImagePath', '$backPageImagePath', '$otherImagePaths')";

//         // Bind parameters and execute query
//         $stmt = $db_connection->prepare($sql);
//         $stmt->bind_param("sssssssssssssssssssss", $booktitle, $author, $publisher, $genre, $languages, $ISBN, $edition, $subjects, $grade, $series, $covertype, $damaged, $pages, $details, $retailprice, $priceinbulk, $mininbulk, $frontPageImagePath, $backPageImagePath, implode(',', $otherImagePaths));
//         $stmt->execute();

//         // Check if insertion was successful
//         if ($stmt->affected_rows > 0) {
//             echo "Data inserted successfully.";
//         } else {
//             echo "Error inserting data: " . $stmt->error;
//         }

//         // Close statement and connection
//         $stmt->close();
//         $db_connection->close();
//     }
// }

//


session_start(); // Start the session

// Store session ID in a session variable
$session_id = session_id();
$_SESSION['session_id'] = $session_id;

echo $session_id;

$host = "localhost";
$port = "5432";
$dbname = "MyBookstore";
$user = "postgres";
$password = "#Wa1r1mu";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
    $db_connection = new PDO($dsn);

    // Set PDO to throw exceptions for errors
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['submit'])) {
        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id']; // Retrieve user ID from session
            $stmt = $db_connection->prepare("SELECT category FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $sellercategory = $stmt->fetchColumn();
        }
    }



    // Array to store error messages
    $errors = [];
    // Paths for front, back, and other images
    $frontPageImagePath = '';
    $backPageImagePath = '';
    $otherImagePaths = [];

    // Function to validate file extension
    function isValidExtension($fileName, $allowedExtensions)
    {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return in_array($fileExt, $allowedExtensions);
    }

    // Array of allowed file extensions
    $allowedExtensions = array('jpg', 'jpeg', 'png');

    // Loop through each image
    foreach ($_FILES as $key => $image) {
        $imageName = $image['name'];
        $imageTmpName = $image['tmp_name'];
        $imageSize = $image['size'];
        $imageError = $image['error'];
        $imageType = $image['type'];

        // Check if the file is uploaded without errors
        if ($imageError === UPLOAD_ERR_OK) {
            // Validate file extension
            if (isValidExtension($imageName, $allowedExtensions)) {
                // Check file size
                if ($imageSize < 1000000000) { // Adjust file size limit as needed
                    // Generate unique file name
                    $imageNewName = uniqid('', true) . "." . strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                    // Set upload destination
                    $imageDestination = 'D:\xammp2\htdocs\BookStore2\Images\uploads/' . $imageNewName;
                    // Move uploaded file to destination
                    if (move_uploaded_file($imageTmpName, $imageDestination)) {
                        echo "Image uploaded successfully: $imageNewName <br>";
                        // Determine image type and store path accordingly
                        switch ($key) {
                            case 'Front-cover':
                                $frontPageImagePath = $imageDestination;
                                break;
                            case 'Back-cover':
                                $backPageImagePath = $imageDestination;
                                break;
                            default:
                                $otherImagePaths[] = $imageDestination;
                                break;
                        }
                    } else {
                        $errors[] = "Error uploading image: $imageName";
                    }
                } else {
                    $errors[] = "Image size exceeds the limit: $imageName";
                }
            } else {
                $errors[] = "Invalid file type for image: $imageName";
            }
        } else {
            $errors[] = "Error occurred while uploading image: $imageName";
        }
    }

    // Display any errors encountered
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    } else {
        // Proceed to insert data into the database
        // Collect form data
        $booktitle = $_POST["booktitle"];
        $author = $_POST["author"];
        $publisher = $_POST["publisher"];
        $genre = $_POST["genre"];
        $languages = $_POST["languages"];
        $ISBN = $_POST["ISBN"];
        $edition = $_POST["edition"];
        $grade = $_POST["grade"];
        $subject = $_POST["subjects"];
        $details = $_POST["details"];
        $pages = $_POST["pages"];
        $covertype = $_POST["covertype"];
        $damaged = $_POST["damaged"];
        $price = $_POST["retailprice"];
        $priceinbulk = $_POST["priceinbulk"];
        $mininbulk = $_POST["mininbulk"];
        $series = isset($_POST["series"]) ? $_POST["series"] : "";
        $otherImages = implode(',', $otherImagePaths);


        $query = "INSERT INTO books (title, author, publisher, price, genre, language, grade, details, pages, covertype, damaged, isbn, series, subject, edition, bookrating, seller_id, priceinbulk, mininbulk, front_page_image, back_page_image, other_images, sellercategory) VALUES ('$booktitle', '$author', '$publisher', '$price', '$genre', '$languages', '$grade', '$details', '$pages', '$covertype', '$damaged', '$ISBN', '$series', '$subject', '$edition', 3.5, '$user_id', '$priceinbulk', '$mininbulk', '$frontPageImagePath', '$backPageImagePath', '$otherImages', '$sellercategory');";
        $db_connection->query($query);

        // Now you can proceed to insert this data into your database table
        // Make sure to sanitize the inputs and use prepared statements to prevent SQL injection
        // Insert query goes here...

        // After successfully inserting the data, you can redirect the user or show a success message
        // For example:
        exit();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}