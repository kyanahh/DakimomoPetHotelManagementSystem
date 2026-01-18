<?php
include '../includes/auth.php';
include '../includes/db.php';

$sender_id = $_SESSION['user_id'];

if (isset($_POST['message'])) {

    $receiver_id = $_POST['receiver_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $query = "INSERT INTO messages (sender_id, receiver_id, message)
              VALUES ('$sender_id','$receiver_id','$message')";

    mysqli_query($conn, $query);
}

header("Location: chat.php");
exit();
