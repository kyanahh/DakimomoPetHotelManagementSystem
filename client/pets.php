<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM pets WHERE user_id='$user_id'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Pets | Dakimomo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.pet-card img {
    height: 220px;
    object-fit: cover;
}
.pet-meta span {
    display: inline-block;
    margin-right: 6px;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
      <img src="../assets/images/logo.png" height="40" class="me-2">
      <strong>Dakimomo</strong>
    </a>

    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link active" href="pets.php">My Pets</a></li>
      <li class="nav-item"><a class="nav-link" href="my-bookings.php">Bookings</a></li>
      <li class="nav-item"><a class="nav-link" href="chat.php">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<?php if (isset($_SESSION['toast'])) { ?>
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div class="toast show text-bg-success">
    <div class="d-flex">
      <div class="toast-body"><?= $_SESSION['toast']; ?></div>
      <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php unset($_SESSION['toast']); } ?>

<div class="container mt-4 p-5">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title">🐾 My Pets</h2>
    <a href="add-pet.php" class="btn btn-brown">
        + Add Pet
    </a>
</div>

<div class="row g-4">

<?php if (mysqli_num_rows($result) === 0) { ?>
    <div class="col-12">
        <div class="alert alert-info text-center">
            You haven’t added any pets yet. 🐶🐱
        </div>
    </div>
<?php } ?>

<?php while ($pet = mysqli_fetch_assoc($result)) {

    $image = $pet['pet_image']
        ? "../assets/uploads/pets/" . $pet['pet_image']
        : "../assets/images/default-pet.png";

    $months = $pet['age_months'];
    if ($months < 12) {
        $age = "$months month(s)";
    } else {
        $years = floor($months / 12);
        $rem   = $months % 12;
        $age   = $years . " year(s)" . ($rem ? " $rem month(s)" : "");
    }
?>

<div class="col-md-4">
<div class="card pet-card shadow-sm h-100">

    <!-- IMAGE -->
    <img src="<?= $image ?>" class="card-img-top" alt="<?= htmlspecialchars($pet['pet_name']); ?>">

    <div class="card-body">

        <!-- NAME -->
        <h5 class="fw-bold mb-1"><?= htmlspecialchars($pet['pet_name']); ?></h5>

        <!-- BADGES -->
        <div class="pet-meta mb-2">
            <span class="badge bg-secondary"><?= htmlspecialchars($pet['pet_type']); ?></span>
            <span class="badge bg-info text-dark"><?= htmlspecialchars($pet['pet_size']); ?></span>
        </div>

        <div class="row small text-muted mb-2">
            <div class="col-6"><strong>Breed:</strong> <?= htmlspecialchars($pet['breed']); ?></div>
            <div class="col-6"><strong>Age:</strong> <?= $age; ?></div>
        </div>

        <p class="small mb-1">
            <strong>Medications:</strong><br>
            <?= $pet['medications'] ?: 'None'; ?>
        </p>

        <p class="small mb-3">
            <strong>Notes:</strong><br>
            <?= $pet['notes'] ?: '—'; ?>
        </p>

        <!-- ACTIONS -->
        <div class="d-flex justify-content-between">
            <a href="edit-pet.php?id=<?= $pet['pet_id']; ?>"
               class="btn btn-sm btn-outline-primary">
               Edit
            </a>

            <button class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#delete<?= $pet['pet_id']; ?>">
                Delete
            </button>
        </div>

    </div>
</div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="delete<?= $pet['pet_id']; ?>" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Delete Pet</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
Are you sure you want to delete <strong><?= htmlspecialchars($pet['pet_name']); ?></strong>?
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<a href="delete-pet.php?id=<?= $pet['pet_id']; ?>" class="btn btn-danger">
Delete
</a>
</div>

</div>
</div>
</div>

<?php } ?>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
setTimeout(() => {
    const toast = document.querySelector('.toast');
    if (toast) bootstrap.Toast.getOrCreateInstance(toast).hide();
}, 3000);
</script>

</body>
</html>