<?php
include '../includes/db.php';

$keyword = trim($_POST['keyword'] ?? '');

if ($keyword === '') {
    echo "🤖 Please choose a topic or ask a question.";
    exit;
}

$safe = mysqli_real_escape_string($conn, $keyword);

/*
 Match keyword against:
 - question
 - keywords
 Only ACTIVE FAQs
*/
$query = mysqli_query($conn, "
    SELECT answer 
    FROM chatbot_faqs
    WHERE status = 'Active'
    AND (
        question LIKE '%$safe%'
        OR keywords LIKE '%$safe%'
    )
    LIMIT 1
");

if ($query && mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    echo nl2br(htmlspecialchars($row['answer']));
} else {
    echo "🤖 Sorry! I don’t have an answer for that yet.<br>Please chat with our staff for assistance.";
}