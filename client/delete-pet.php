<?php
include '../includes/auth.php';
include '../includes/db.php';
session_start();

if (isset($_GET['id'])) {
    $pet_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    mysqli_query($conn, "DELETE FROM pets WHERE pet_id='$pet_id' AND user_id='$user_id'");

    // Set toast message
    $_SESSION['toast'] = "Pet deleted successfully.";
}

header("Location: pets.php");
exit();
?>