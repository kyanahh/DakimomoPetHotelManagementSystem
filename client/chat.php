<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'client') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messages | Dakimomo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* CHAT LAYOUT */
.chat-wrapper {
    height: calc(100vh - 120px);
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 12px 16px;
    border-bottom: 1px solid #ddd;
    font-weight: 600;
}

.chat-box {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f5f5f5;
}

/* MESSAGE BUBBLES */
.bubble {
    max-width: 70%;
    padding: 10px 14px;
    border-radius: 15px;
    margin-bottom: 10px;
    font-size: 14px;
    position: relative;
}

.bubble.client {
    background: #c8a97e;
    color: #fff;
    margin-left: auto;
    border-bottom-right-radius: 3px;
}

.bubble.support {
    background: #fff;
    border: 1px solid #ddd;
    margin-right: auto;
    border-bottom-left-radius: 3px;
}

.timestamp {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 4px;
    text-align: right;
}

/* INPUT AREA */
.chat-input {
    border-top: 1px solid #ddd;
    padding: 10px;
    background: #fff;
}
</style>
</head>
<body style="background-color: #c8a97e;">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">Dakimomo</a>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="pets.php">My Pets</a></li>
      <li class="nav-item"><a class="nav-link" href="my-bookings.php">Bookings</a></li>
      <li class="nav-item"><a class="nav-link active" href="chat.php">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4 d-flex justify-content-center">
<div class="card shadow-sm col-sm-8">
<div class="chat-wrapper">

    <!-- HEADER -->
    <div class="chat-header">
        💬 Dakimomo Support
    </div>

    <!-- CHAT MESSAGES -->
    <div id="chatBox" class="chat-box">
        <p class="text-muted text-center">Start chatting with Dakimomo Support</p>
    </div>

    <!-- INPUT -->
    <form id="chatForm" class="chat-input d-flex">
        <input type="text" id="message" class="form-control me-2"
               placeholder="Type a message..." required>
        <button class="btn btn-dark">Send</button>
    </form>

</div>
</div>
</div>

<script>
const chatBox = document.getElementById('chatBox');

/* LOAD MESSAGES */
function loadMessages() {
    fetch('fetch-messages.php')
        .then(res => res.text())
        .then(data => {
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

/* SEND MESSAGE */
document.getElementById('chatForm').addEventListener('submit', e => {
    e.preventDefault();

    const msg = document.getElementById('message');

    fetch('send-message.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'message=' + encodeURIComponent(msg.value)
    }).then(() => {
        msg.value = '';
        loadMessages();
    });
});

/* AUTO REFRESH */
setInterval(loadMessages, 3000);
loadMessages();
</script>

</body>
</html>