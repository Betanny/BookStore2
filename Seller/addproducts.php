<?php
session_start(); // Start the session
include '../Shared Components/logger.php';
require_once '../Shared Components/dbconnection.php';


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

    if (isset($_POST['check_isbn'])) {
        $isbn = $_POST['isbn'];
        $stmt = $db_connection->prepare("SELECT COUNT(*) FROM books WHERE isbn = :isbn");
        $stmt->execute(['isbn' => $isbn]);
        $isUnique = $stmt->fetchColumn() == 0;
        echo json_encode(['isUnique' => $isUnique]);
        exit;
    }


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
            // Only add to errors if it's a mandatory image and not uploaded
            if ($key == 'Front-cover' || $key == 'Back-cover') {
                $errors[] = "Error occurred while uploading image: $imageName";
            }
        }
    }

    // Check if front and back cover images are uploaded successfully
    if (empty($frontPageImagePath)) {
        $errors[] = "Front cover image is required.";
    }
    if (empty($backPageImagePath)) {
        $errors[] = "Back cover image is required.";
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

        $query = "INSERT INTO books (title, author, publisher, price, genre, language, grade, details, pages, covertype, damaged, isbn, series, subject, edition, bookrating, seller_id, priceinbulk, mininbulk, front_page_image, back_page_image, other_images, sellercategory) VALUES (:booktitle, :author, :publisher, :price, :genre, :languages, :grade, :details, :pages, :covertype, :damaged, :isbn, :series, :subject, :edition, 3.5, :user_id, :priceinbulk, :mininbulk, :frontPageImagePath, :backPageImagePath, :otherImages, :sellercategory)";

        $stmt = $db_connection->prepare($query);
        $stmt->execute([
            ':booktitle' => $booktitle,
            ':author' => $author,
            ':publisher' => $publisher,
            ':price' => $price,
            ':genre' => $genre,
            ':languages' => $languages,
            ':grade' => $grade,
            ':details' => $details,
            ':pages' => $pages,
            ':covertype' => $covertype,
            ':damaged' => $damaged,
            ':isbn' => $ISBN,
            ':series' => $series,
            ':subject' => $subject,
            ':edition' => $edition,
            ':user_id' => $user_id,
            ':priceinbulk' => $priceinbulk,
            ':mininbulk' => $mininbulk,
            ':frontPageImagePath' => $frontPageImagePath,
            ':backPageImagePath' => $backPageImagePath,
            ':otherImages' => $otherImages,
            ':sellercategory' => $sellercategory
        ]);
        writeLog($db, "The user has added a product to the system", "INFO", $user_id);

        // After successfully inserting the data, you can redirect the user or show a success message
        header("Location: /Seller/ViewProducts.php");
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}