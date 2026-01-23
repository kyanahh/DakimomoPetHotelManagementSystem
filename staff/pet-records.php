<?php
include '../includes/auth.php';
include '../includes/db.php';

/* STAFF ONLY */
if ($_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['pet_id'])) {
    header("Location: bookings.php");
    exit();
}

$pet_id = (int) $_GET['pet_id'];

/* PET INFO */
$pet = mysqli_query($conn, "
    SELECT p.*, u.full_name
    FROM pets p
    JOIN users u ON p.user_id = u.user_id
    WHERE p.pet_id = $pet_id
");

if (mysqli_num_rows($pet) == 0) {
    header("Location: bookings.php");
    exit();
}

$pet = mysqli_fetch_assoc($pet);

/* RELATED BOOKINGS */
$bookings = mysqli_query($conn, "
    SELECT booking_id, pet_id, service_type, start_date, end_date, status
    FROM bookings
    WHERE pet_id = $pet_id
    ORDER BY start_date DESC
");

/* CARE UPDATES */
$updates = mysqli_query($conn, "
    SELECT u.*
    FROM pet_care_updates u
    JOIN bookings b ON u.booking_id = b.booking_id
    WHERE b.pet_id = $pet_id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pet Records | Staff</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.pet-img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid #ddd;
}
.info-label {
    font-weight: 600;
    color: #555;
}
</style>
</head>

<body>

<div class="admin-wrapper">
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<h3 class="section-title mb-4">🐾 Pet Records</h3>

<!-- PET PROFILE -->
<div class="card shadow-sm mb-4">
<div class="card-body d-flex gap-4">

<?php
$image = $pet['pet_image']
    ? "../assets/uploads/pets/".$pet['pet_image']
    : "../assets/images/default-pet.png";
?>

<img src="<?= $image ?>" class="pet-img">

<div>
<h5 class="fw-bold mb-1"><?= htmlspecialchars($pet['pet_name']); ?></h5>
<small class="text-muted">Owner: <?= htmlspecialchars($pet['full_name']); ?></small>

<hr>

<p><span class="info-label">Species:</span> <?= htmlspecialchars($pet['pet_type']); ?></p>

<p>
    <span class="info-label">Gender:</span>
    <?= $pet['gender'] === 'Male' ? '♂ Male' : '♀ Female'; ?>
</p>

<p><span class="info-label">Breed:</span> <?= $pet['breed'] ?: '—'; ?></p>

<p><span class="info-label">Size:</span> <?= htmlspecialchars($pet['pet_size']); ?></p>

<p><span class="info-label">Age:</span>
<?php
$months = (int)$pet['age_months'];
if ($months >= 12) {
    echo floor($months / 12) . " year(s)";
} else {
    echo $months . " month(s)";
}
?>
</p>
</div>

</div>
</div>

<!-- HEALTH & NOTES -->
<div class="row g-4 mb-4">

<div class="col-md-6">
<div class="card shadow-sm h-100">
<div class="card-header fw-bold">💊 Medications</div>
<div class="card-body">
<?= nl2br(htmlspecialchars($pet['medications'] ?: 'No medications listed.')); ?>
</div>
</div>
</div>

<div class="col-md-6">
<div class="card shadow-sm h-100">
<div class="card-header fw-bold">📝 Owner Notes</div>
<div class="card-body">
<?= nl2br(htmlspecialchars($pet['notes'] ?: 'No special notes provided.')); ?>
</div>
</div>
</div>

</div>

<!-- BOOKINGS -->
<div class="card shadow-sm mb-4">
<div class="card-header fw-bold">📅 Related Bookings</div>
<div class="card-body p-0">

<table class="table table-sm table-striped mb-0">
<thead class="table-light">
<tr>
<th>Service</th>
<th>Date</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>

<?php if (mysqli_num_rows($bookings) > 0) { ?>
<?php while ($b = mysqli_fetch_assoc($bookings)) { ?>
<tr>
<td><?= $b['service_type']; ?></td>
<td>
<?= date("M d, Y", strtotime($b['start_date'])); ?> –
<?= date("M d, Y", strtotime($b['end_date'])); ?>
</td>
<td>
<span class="badge bg-secondary"><?= $b['status']; ?></span>
</td>
<td>
<a href="petstatus.php?booking_id=<?= $b['booking_id']; ?>"
   class="btn btn-sm btn-brown">
Update Status
</a>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="4" class="text-center text-muted">No bookings found.</td>
</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

<!-- CARE HISTORY -->
<div class="card shadow-sm mb-4">
<div class="card-header fw-bold">📋 Care History</div>
<div class="card-body">

<?php if (mysqli_num_rows($updates) > 0) { ?>
<?php while ($u = mysqli_fetch_assoc($updates)) { ?>
<div class="border-start border-4 ps-3 mb-3">
<span class="badge bg-primary"><?= $u['status_title']; ?></span>
<p class="mb-1"><?= nl2br(htmlspecialchars($u['status_message'])); ?></p>
<small class="text-muted">
<?= date("M d, Y h:i A", strtotime($u['created_at'])); ?>
</small>
</div>
<?php } ?>
<?php } else { ?>
<p class="text-muted">No care updates yet.</p>
<?php } ?>

</div>
</div>

<a href="bookings.php" class="btn btn-secondary">← Back to Bookings</a>

</div>
</div>

</body>
</html>