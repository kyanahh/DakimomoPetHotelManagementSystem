<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    exit();
}

$sender   = $_SESSION['user_id'];
$clientId = (int) ($_POST['receiver_id'] ?? 0);
$message  = trim($_POST['message'] ?? '');

if ($clientId === 0 || $message === '') {
    exit();
}

$message = mysqli_real_escape_string($conn, $message);

/*
IMPORTANT:
Match the CLIENT insert exactly
conversation_id = client_id
NO receiver_id column
*/
mysqli_query($conn, "
    INSERT INTO messages (conversation_id, sender_id, message)
    VALUES ($clientId, $sender, '$message')
");