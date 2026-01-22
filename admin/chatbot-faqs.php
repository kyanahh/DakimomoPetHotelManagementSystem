<?php
include '../includes/auth.php';
include '../includes/db.php';

/* ADMIN ONLY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ADD FAQ */
if (isset($_POST['add_faq'])) {

    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $answer   = mysqli_real_escape_string($conn, $_POST['answer']);
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);

    mysqli_query($conn, "
        INSERT INTO chatbot_faqs (question, answer, keywords, status)
        VALUES ('$question','$answer','$keywords','Active')
    ");

    header("Location: chatbot-faqs.php?toast=added");
    exit();
}

/* UPDATE FAQ */
if (isset($_POST['update_faq'])) {

    $id       = (int)$_POST['faq_id'];
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $answer   = mysqli_real_escape_string($conn, $_POST['answer']);
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);
    $status   = $_POST['status'];

    mysqli_query($conn, "
        UPDATE chatbot_faqs SET
            question='$question',
            answer='$answer',
            keywords='$keywords',
            status='$status'
        WHERE faq_id=$id
    ");

    header("Location: chatbot-faqs.php?toast=updated");
    exit();
}

/* FETCH FAQS */
$faqs = mysqli_query($conn, "
    SELECT * FROM chatbot_faqs
    ORDER BY status='Active' DESC, faq_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chatbot FAQs | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="admin-wrapper">
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title">🤖 Chatbot FAQs</h3>
    <button class="btn btn-brown" data-bs-toggle="modal" data-bs-target="#addFAQ">
        + Add FAQ
    </button>
</div>

<div class="card shadow-sm">
<div class="card-body p-0 overflow-auto" style="height: 450px;">

<table class="table table-hover align-middle mb-0" >
<thead class="table-light">
<tr>
    <th>Question</th>
    <th>Keywords</th>
    <th>Status</th>
    <th class="text-center">Actions</th>
</tr>
</thead>
<tbody>

<?php while ($f = mysqli_fetch_assoc($faqs)) { ?>
<tr>
<td><?= htmlspecialchars($f['question']) ?></td>
<td><small><?= htmlspecialchars($f['keywords']) ?></small></td>
<td>
<span class="badge bg-<?= $f['status'] === 'Active' ? 'success' : 'secondary' ?>">
    <?= $f['status'] ?>
</span>
</td>
<td class="text-center">
<button class="btn btn-sm btn-outline-primary"
        data-bs-toggle="modal"
        data-bs-target="#edit<?= $f['faq_id'] ?>">
    Edit
</button>
</td>
</tr>

<!-- EDIT MODAL -->
<div class="modal fade" id="edit<?= $f['faq_id'] ?>">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content">

<form method="POST">

<div class="modal-header">
<h5>Edit FAQ</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="faq_id" value="<?= $f['faq_id'] ?>">

<label>Question</label>
<input type="text" name="question" class="form-control mb-2"
       value="<?= htmlspecialchars($f['question']) ?>" required>

<label>Answer</label>
<textarea name="answer" class="form-control mb-2" rows="4" required><?= htmlspecialchars($f['answer']) ?></textarea>

<label>Keywords (comma-separated)</label>
<input type="text" name="keywords" class="form-control mb-2"
       value="<?= htmlspecialchars($f['keywords']) ?>" required>

<label>Status</label>
<select name="status" class="form-control">
<option <?= $f['status']=='Active'?'selected':'' ?>>Active</option>
<option <?= $f['status']=='Inactive'?'selected':'' ?>>Inactive</option>
</select>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="update_faq" class="btn btn-primary">Save</button>
</div>

</form>

</div>
</div>
</div>

<?php } ?>

</tbody>
</table>

</div>
</div>

</div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addFAQ">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content">

<form method="POST">

<div class="modal-header">
<h5>Add New FAQ</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<label>Question</label>
<input type="text" name="question" class="form-control mb-2" required>

<label>Answer</label>
<textarea name="answer" class="form-control mb-2" rows="4" required></textarea>

<label>Keywords (comma-separated)</label>
<input type="text" name="keywords" class="form-control" required>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="add_faq" class="btn btn-success">Add FAQ</button>
</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>