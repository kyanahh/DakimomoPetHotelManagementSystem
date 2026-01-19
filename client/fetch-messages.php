<?php
include '../includes/auth.php';
include '../includes/db.php';

$client_id = $_SESSION['user_id'];

$messages = mysqli_query($conn, "
    SELECT * FROM messages
    WHERE conversation_id = $client_id
    ORDER BY created_at ASC
");

while ($m = mysqli_fetch_assoc($messages)) {

    $class = ($m['sender_id'] == $client_id) ? 'client' : 'support';
    $time = date("h:i A", strtotime($m['created_at']));

    echo "
    <div class='bubble $class'>
        ".htmlspecialchars($m['message'])."
        <div class='timestamp'>$time</div>
    </div>
    ";
}