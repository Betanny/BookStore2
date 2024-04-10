<?php

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];

    if ($role === 'Admin') {
        include '../Admin/header.php';
    } elseif ($role === 'Client') {
        include '../Buyer/header.php';
    } elseif ($role === 'Dealer') {
        include '../Seller/header.php';
    } else {
        include '../Shared Components/header.php'; // Default header for users not categorized
    }
} else {
    include '../Shared Components/header.php'; // Default header for users who are not logged in
}