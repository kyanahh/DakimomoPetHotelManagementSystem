<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') exit();

$client_id = (int) ($_GET['user_id'] ?? 0);
if (!$client_id) exit();

$messages = mysqli_query($conn, "
    SELECT * FROM messages
    WHERE conversation_id = $client_id
    ORDER BY created_at ASC
");

while ($m = mysqli_fetch_assoc($messages)) {

    $class = ($m['sender_id'] == $_SESSION['user_id']) ? 'admin' : 'client';
    $time  = date("h:i A", strtotime($m['created_at']));

    echo "
    <div class='bubble $class'>
        ".htmlspecialchars($m['message'])."
        <div class='timestamp'>$time</div>
    </div>
    ";
}