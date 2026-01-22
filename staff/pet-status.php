<?php
include '../includes/auth.php';
include '../includes/db.php';

/* STAFF ONLY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}

/* ADD STATUS */
if (isset($_POST['add_status'])) {

    $booking_id = (int) $_POST['booking_id'];
    $staff_id   = $_SESSION['user_id'];
    $title      = mysqli_real_escape_string($conn, $_POST['status_title']);
    $message    = mysqli_real_escape_string($conn, $_POST['status_message']);

    mysqli_query($conn, "
        INSERT INTO pet_care_updates 
        (booking_id, staff_id, status_title, status_message)
        VALUES ('$booking_id','$staff_id','$title','$message')
    ");

    header("Location: pet-status.php?success=1");
    exit();
}

/* FETCH ACTIVE BOOKINGS */
$bookings = mysqli_query($conn, "
    SELECT 
        b.booking_id,
        p.pet_name,
        u.full_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    JOIN users u ON b.user_id = u.user_id
    WHERE b.status = 'Confirmed'
    ORDER BY b.start_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pet Care Status | Staff</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.status-card {
    border-left: 5px solid #c8a97e;
}
.timeline {
    border-left: 3px solid #ddd;
    padding-left: 15px;
}
.timeline-item {
    margin-bottom: 15px;
}
.timeline-item::before {
    content: "";
    width: 10px;
    height: 10px;
    background: #c8a97e;
    border-radius: 50%;
    display: inline-block;
    margin-left: -19px;
    margin-right: 10px;
}
</style>
</head>

<body>

<div class="admin-wrapper">
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<h3 class="section-title mb-4">🐾 Pet Care Status Updates</h3>

<div class="row g-4">

<!-- ADD STATUS -->
<div class="col-md-6">
<div class="card shadow-sm status-card">
<div class="card-header fw-bold">Add New Status</div>
<div class="card-body">

<form method="POST">

<label class="fw-semibold">Booking</label>
<select name="booking_id" class="form-control mb-3" required>
<option disabled selected>Select booking</option>
<?php while ($b = mysqli_fetch_assoc($bookings)) { ?>
<option value="<?= $b['booking_id'] ?>">
Booking #<?= $b['booking_id'] ?> —
<?= htmlspecialchars($b['pet_name']) ?> (<?= htmlspecialchars($b['full_name']) ?>)
</option>
<?php } ?>
</select>

<label class="fw-semibold">Status</label>
<select name="status_title" class="form-control mb-3" required>
<option disabled selected>Select Status</option>
<option>Fed</option>
<option>Walked</option>
<option>Bathed</option>
<option>Medication Given</option>
<option>Resting</option>
<option>Under Observation</option>
<option>Ready for Pickup</option>
</select>

<label class="fw-semibold">Remarks (optional)</label>
<textarea name="status_message"
class="form-control mb-3"
rows="3"
placeholder="Additional notes about the pet..."></textarea>

<button name="add_status" class="btn btn-brown w-100">
Save Status Update
</button>

</form>

</div>
</div>
</div>

<!-- STATUS HISTORY -->
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-header fw-bold">Recent Pet Care Updates</div>
<div class="card-body timeline">

<?php
$history = mysqli_query($conn, "
    SELECT 
        u.status_title,
        u.status_message,
        u.created_at,
        p.pet_name,
        s.full_name AS staff_name
    FROM pet_care_updates u
    JOIN bookings b ON u.booking_id = b.booking_id
    JOIN pets p ON b.pet_id = p.pet_id
    JOIN users s ON u.staff_id = s.user_id
    ORDER BY u.created_at DESC
    LIMIT 10
");

if (mysqli_num_rows($history) == 0) {
    echo "<p class='text-muted'>No updates yet.</p>";
}

while ($h = mysqli_fetch_assoc($history)) {
?>
<div class="timeline-item">
    <strong><?= htmlspecialchars($h['status_title']) ?></strong>
    <small class="text-muted">
        — <?= htmlspecialchars($h['pet_name']) ?>
    </small>
    <div class="small text-muted">
        <?= date("M d, Y h:i A", strtotime($h['created_at'])) ?>
        • by <?= htmlspecialchars($h['staff_name']) ?>
    </div>
    <?php if (!empty($h['status_message'])) { ?>
        <div><?= htmlspecialchars($h['status_message']) ?></div>
    <?php } ?>
</div>
<?php } ?>

</div>
</div>
</div>

</div>

</div>
</div>

<?php if (isset($_GET['success'])) { ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="statusToast"
         class="toast align-items-center text-white bg-success border-0"
         role="alert"
         aria-live="assertive"
         aria-atomic="true"
         data-bs-delay="3000">

        <div class="d-flex">
            <div class="toast-body">
                Pet care status updated successfully.
            </div>
            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toastEl = document.getElementById('statusToast');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>

</body>
</html>