<?php
include '../includes/auth.php';
include '../includes/db.php';

// ADMIN ONLY
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ======================
   ADD USER
====================== */
if (isset($_POST['add_user'])) {

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $role      = $_POST['role'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn, "
        INSERT INTO users (full_name, email, password, role, status)
        VALUES ('$full_name','$email','$password','$role','active')
    ");

    header("Location: users.php?toast=added");
    exit();
}

/* ======================
   ACTIVATE / DEACTIVATE
====================== */
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];

    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "
            UPDATE users
            SET status = IF(status='active','inactive','active')
            WHERE user_id=$id
        ");
    }

    header("Location: users.php?toast=updated");
    exit();
}

/* ======================
   SEARCH USERS
====================== */
$search = $_GET['search'] ?? '';

$where = "";
if (!empty($search)) {
    $safe = mysqli_real_escape_string($conn, $search);
    $where = "WHERE full_name LIKE '%$safe%' OR email LIKE '%$safe%'";
}

/* ======================
   FETCH USERS
====================== */
$users = mysqli_query($conn, "
    SELECT user_id, full_name, email, role, status
    FROM users
    $where
    ORDER BY role ASC, full_name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management | Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-wrapper">

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title">User Management</h3>
    <button class="btn btn-brown" data-bs-toggle="modal" data-bs-target="#addUserModal">
        + Add User
    </button>
</div>

<!-- SEARCH -->
<form class="mb-3">
    <input type="text" name="search" class="form-control"
           placeholder="Search by name or email"
           value="<?= htmlspecialchars($search); ?>">
</form>

<!-- TABLE -->
<div class="card shadow-sm">
<div class="card-body p-0">

<table class="table table-hover align-middle mb-0">
<thead class="table-light">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th class="text-center">Action</th>
</tr>
</thead>
<tbody>

<?php if (mysqli_num_rows($users) == 0) { ?>
<tr>
    <td colspan="5" class="text-center text-muted py-4">
        No users found.
    </td>
</tr>
<?php } ?>

<?php while ($u = mysqli_fetch_assoc($users)) { ?>
<tr>
    <td><?= htmlspecialchars($u['full_name']); ?></td>
    <td><?= htmlspecialchars($u['email']); ?></td>
    <td>
        <span class="badge <?= $u['role']=='admin' ? 'bg-dark' : 'bg-primary'; ?>">
            <?= ucfirst($u['role']); ?>
        </span>
    </td>
    <td>
        <span class="badge <?= $u['status']=='active' ? 'bg-success' : 'bg-secondary'; ?>">
            <?= ucfirst($u['status']); ?>
        </span>
    </td>
    <td class="text-center">

        <?php if ($u['user_id'] == $_SESSION['user_id']) { ?>
            <span class="text-muted">—</span>
        <?php } else { ?>
            <a href="?toggle=<?= $u['user_id']; ?>"
               class="btn btn-sm <?= $u['status']=='active' ? 'btn-danger' : 'btn-success'; ?>">
               <?= $u['status']=='active' ? 'Deactivate' : 'Activate'; ?>
            </a>
        <?php } ?>

    </td>
</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

</div>
</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<form method="POST">

<div class="modal-header">
    <h5 class="modal-title">Add New User</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="text" name="full_name" class="form-control mb-3"
       placeholder="Full Name" required>

<input type="email" name="email" class="form-control mb-3"
       placeholder="Email Address" required>

<input type="password" name="password" class="form-control mb-3"
       placeholder="Temporary Password" required>

<select name="role" class="form-control mb-3">
    <option value="client">Client</option>
    <option value="staff">Staff</option>
    <option value="admin">Admin</option>
</select>

</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="submit" name="add_user" class="btn btn-success">
        Add User
    </button>
</div>

</form>

</div>
</div>
</div>

<!-- TOAST -->
<?php if (isset($_GET['toast'])) { ?>
<div class="toast-container position-fixed top-0 end-0 p-3">
<div class="toast show text-white
<?= $_GET['toast']=='added' ? 'bg-primary' : 'bg-success'; ?>">
<div class="d-flex">
<div class="toast-body">
<?= $_GET['toast']=='added'
    ? 'User added successfully.'
    : 'User status updated successfully.'; ?>
</div>
<button class="btn-close btn-close-white me-2 m-auto"
        data-bs-dismiss="toast"></button>
</div>
</div>
</div>

<script>
setTimeout(() => {
    document.querySelector('.toast')?.classList.remove('show');
}, 3000);
</script>
<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>