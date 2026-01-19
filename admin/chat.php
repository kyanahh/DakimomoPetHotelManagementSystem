<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

$clients = mysqli_query($conn, "
    SELECT user_id, full_name 
    FROM users 
    WHERE role='client' AND status='active'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messages | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.chat-box {
    height: 420px;
    overflow-y: auto;
    background: #f9f9f9;
    padding: 15px;
}

.bubble {
    max-width: 70%;
    padding: 10px 14px;
    border-radius: 12px;
    margin-bottom: 8px;
    font-size: 14px;
}

.bubble.admin {
    background: #6f4e37;
    color: #fff;
    margin-left: auto;
    text-align: right;
}

.bubble.client {
    background: #e4e6eb;
    color: #000;
    margin-right: auto;
}

.timestamp {
    font-size: 11px;
    opacity: .7;
    margin-top: 3px;
}
</style>
</head>
<body>

<div class="admin-wrapper">
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<h3 class="section-title mb-3">Messages</h3>

<div class="row">

<!-- CLIENT LIST -->
<div class="col-md-4">
<div class="card shadow-sm">
<ul class="list-group list-group-flush">
<?php while ($c = mysqli_fetch_assoc($clients)) { ?>
<li class="list-group-item client-item"
    style="cursor:pointer"
    data-id="<?= $c['user_id']; ?>">
    <?= htmlspecialchars($c['full_name']); ?>
</li>
<?php } ?>
</ul>
</div>
</div>

<!-- CHAT -->
<div class="col-md-8">
<div class="card shadow-sm h-100">
<div class="card-body d-flex flex-column">

<div id="chatBox" class="chat-box mb-3">
    <p class="text-muted text-center">Select a client to start chatting</p>
</div>

<form id="chatForm" class="d-flex">
<input type="hidden" id="receiver_id">
<input type="text" id="message" class="form-control me-2"
       placeholder="Type a message..." required>
<button class="btn btn-brown">Send</button>
</form>

</div>
</div>
</div>

</div>
</div>

<script>
let selectedClient = null;
const adminId = <?= $admin_id ?>;

/* SELECT CLIENT */
document.querySelectorAll('.client-item').forEach(item => {
    item.addEventListener('click', () => {
        selectedClient = item.dataset.id;
        document.getElementById('receiver_id').value = selectedClient;
        loadMessages();
    });
});

/* LOAD MESSAGES */
function loadMessages() {
    if (!selectedClient) return;

    fetch('fetch-messages.php?user_id=' + selectedClient)
        .then(res => res.text())
        .then(html => {
            document.getElementById('chatBox').innerHTML = html;
            document.getElementById('chatBox').scrollTop =
                document.getElementById('chatBox').scrollHeight;
        });
}

/* SEND MESSAGE */
document.getElementById('chatForm').addEventListener('submit', function (e) {
    e.preventDefault();

    if (!selectedClient) return;

    const msg = document.getElementById('message').value;

    fetch('send-message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'receiver_id=' + selectedClient + '&message=' + encodeURIComponent(msg)
    }).then(() => {
        document.getElementById('message').value = '';
        loadMessages();
    });
});

/* AUTO REFRESH */
setInterval(loadMessages, 3000);
</script>

</body>
</html>