<?php
include '../includes/auth.php';
include '../includes/db.php';

$sender = $_SESSION['user_id'];
$conversation_id = $sender; // client owns the conversation
$message = mysqli_real_escape_string($conn, $_POST['message']);

mysqli_query($conn, "
    INSERT INTO messages (conversation_id, sender_id, message)
    VALUES ($conversation_id, $sender, '$message')
");