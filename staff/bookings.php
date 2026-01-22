<?php
include '../includes/auth.php';
include '../includes/db.php';

/* STAFF ONLY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}

/* FETCH BOOKINGS ASSIGNED / ACTIVE */
$bookings = mysqli_query($conn, "
    SELECT 
        b.booking_id,
        b.service_type,
        b.start_date,
        b.end_date,
        b.status,
        p.pet_name,
        u.full_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    JOIN users u ON b.user_id = u.user_id
    WHERE b.status IN ('Confirmed','Completed')
    ORDER BY b.start_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bookings | Staff</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.badge-status {
    font-size: 12px;
    padding: 6px 10px;
}
</style>
</head>

<body>

<div class="admin-wrapper">
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<h3 class="section-title mb-4">📋 Assigned Bookings</h3>

<div class="card shadow-sm">
<div class="card-body p-0">

<table class="table table-hover align-middle mb-0">
<thead class="table-light">
<tr>
    <th>Pet</th>
    <th>Owner</th>
    <th>Service</th>
    <th>Date Range</th>
    <th>Status</th>
    <th class="text-center">Action</th>
</tr>
</thead>
<tbody>

<?php
if (mysqli_num_rows($bookings) == 0) {
    echo "<tr><td colspan='6' class='text-center text-muted p-4'>
            No active bookings assigned.
          </td></tr>";
}

while ($b = mysqli_fetch_assoc($bookings)) {

$statusBadge = $b['status'] === 'Confirmed'
    ? 'success'
    : 'dark';
?>

<tr>
<td class="fw-semibold"><?= htmlspecialchars($b['pet_name']) ?></td>
<td><?= htmlspecialchars($b['full_name']) ?></td>
<td><?= htmlspecialchars($b['service_type']) ?></td>
<td>
<?= date("M d, Y", strtotime($b['start_date'])) ?>
–
<?= date("M d, Y", strtotime($b['end_date'])) ?>
</td>
<td>
<span class="badge bg-<?= $statusBadge ?> badge-status">
<?= $b['status'] ?>
</span>
</td>
<td class="text-center">
<?php if ($b['status'] == 'Confirmed') { ?>
    <a href="pet-status.php?booking_id=<?= $b['booking_id'] ?>"
       class="btn btn-sm btn-brown">
       Update Status
    </a>
<?php } else { ?>
    <span class="text-muted small">—</span>
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

</body>
</html>