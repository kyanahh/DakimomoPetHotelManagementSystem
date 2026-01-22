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

.bubble {
    max-width: 70%;
    padding: 10px 14px;
    border-radius: 15px;
    margin-bottom: 10px;
    font-size: 14px;
}

.bubble.client {
    background: #c8a97e;
    color: #fff;
    margin-left: auto;
}

.bubble.support {
    background: #fff;
    border: 1px solid #ddd;
    margin-right: auto;
}

.chat-input {
    border-top: 1px solid #ddd;
    padding: 10px;
    background: #fff;
}

.quick-btn {
    margin: 4px;
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
<div class="chat-header d-flex justify-content-between align-items-center">
<span>💬 Dakimomo Support</span>
<button class="btn btn-sm btn-outline-dark" onclick="toggleBot()">
🤖 FAQ Bot
</button>
</div>

<!-- CHATBOT (HIDDEN BY DEFAULT) -->
<div id="chatbotBox" class="chat-box d-none">
<div class="bubble support">
Hi! 👋 I’m Dakimomo’s virtual assistant. How can I help you?
</div>

<div class="mt-2">
<button class="btn btn-sm btn-secondary quick-btn" onclick="askBot('services')">Services</button>
<button class="btn btn-sm btn-secondary quick-btn" onclick="askBot('pricing')">Pricing</button>
<button class="btn btn-sm btn-secondary quick-btn" onclick="askBot('booking')">How to Book</button>
<button class="btn btn-sm btn-secondary quick-btn" onclick="askBot('payment')">Payment</button>
</div>
</div>

<!-- HUMAN CHAT (DEFAULT – UNCHANGED) -->
<div id="humanChatBox" class="chat-box">
<div id="chatBox">
<p class="text-muted text-center">Start chatting with Dakimomo Support</p>
</div>
</div>

<!-- INPUT (UNCHANGED) -->
<form id="chatForm" class="chat-input d-flex">
<input type="text" id="message" class="form-control me-2"
       placeholder="Type a message..." required>
<button class="btn btn-dark">Send</button>
</form>

</div>
</div>
</div>

<script>
const chatBox  = document.getElementById('chatBox');
const humanBox = document.getElementById('humanChatBox');
const botBox   = document.getElementById('chatbotBox');

/* LOAD HUMAN CHAT MESSAGES (UNCHANGED) */
function loadMessages() {
    fetch('fetch-messages.php')
        .then(res => res.text())
        .then(data => {
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

/* SEND HUMAN MESSAGE (UNCHANGED) */
document.getElementById('chatForm').addEventListener('submit', e => {
    e.preventDefault();

    const msgInput = document.getElementById('message');
    const message  = msgInput.value.trim();

    if (message === '') return;

    // BOT MODE
    if (!botBox.classList.contains('d-none')) {

        // show user message
        botBox.innerHTML += `
            <div class="bubble client">${message}</div>
        `;

        fetch('chatbot-send.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'keyword=' + encodeURIComponent(message)
        })
        .then(res => res.text())
        .then(reply => {
            botBox.innerHTML += `
                <div class="bubble support">${reply}</div>
            `;
            botBox.scrollTop = botBox.scrollHeight;
        });

    } 
    // HUMAN MODE
    else {

        fetch('send-message.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'message=' + encodeURIComponent(message)
        }).then(() => {
            loadMessages();
        });

    }

    msgInput.value = '';
});

/* AUTO REFRESH (UNCHANGED) */
setInterval(loadMessages, 3000);
loadMessages();

/* TOGGLE CHATBOT */
function toggleBot() {
    botBox.classList.toggle('d-none');
    humanBox.classList.toggle('d-none');
}

/* ASK CHATBOT (FAQ FROM DATABASE) */
function askBot(keyword) {
    fetch('chatbot-send.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'keyword=' + encodeURIComponent(keyword)
    })
    .then(res => res.text())
    .then(reply => {
        botBox.innerHTML += `<div class="bubble support">${reply}</div>`;
        botBox.scrollTop = botBox.scrollHeight;
    });
}

</script>

</body>
</html>