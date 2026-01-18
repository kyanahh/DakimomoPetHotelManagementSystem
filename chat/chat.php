<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

if (isset($_GET['user_id'])) {
    $receiver_id = $_GET['user_id'];
} else {
    echo "No chat selected.";
    exit();
}

// Fetch messages
$messages = mysqli_query($conn, "
    SELECT m.*, u.full_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE (sender_id='$user_id' AND receiver_id='$receiver_id')
       OR (sender_id='$receiver_id' AND receiver_id='$user_id')
    ORDER BY m.date_sent ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container section">
    <h3 class="section-title mb-3">Messages</h3>

    <div class="card shadow-sm">
        <div class="card-body" style="height:400px; overflow-y:auto;">

            <?php while ($msg = mysqli_fetch_assoc($messages)) { ?>

                <?php if ($msg['sender_id'] == $user_id) { ?>
                    <!-- SENT -->
                    <div class="text-end mb-2">
                        <div class="d-inline-block p-2 rounded text-white"
                             style="background:#8B5E3C; max-width:75%;">
                            <?= htmlspecialchars($msg['message']); ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <!-- RECEIVED -->
                    <div class="text-start mb-2">
                        <div class="d-inline-block p-2 rounded bg-light"
                             style="max-width:75%;">
                            <strong><?= $msg['full_name']; ?>:</strong><br>
                            <?= htmlspecialchars($msg['message']); ?>
                        </div>
                    </div>
                <?php } ?>

            <?php } ?>

        </div>

        <div class="card-footer">
            <form method="POST" action="send-message.php" class="d-flex gap-2">
                <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                <input type="text" name="message" class="form-control" placeholder="Type message..." required>
                <button class="btn btn-brown">Send</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>