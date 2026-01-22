<?php
include '../includes/auth.php';
include '../includes/db.php';

/* ADMIN ONLY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: bookings.php");
    exit();
}

$booking_id = (int) $_GET['id'];

$query = mysqli_query($conn, "
    SELECT 
        b.*,
        u.full_name,
        p.pet_name
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN pets p ON b.pet_id = p.pet_id
    WHERE b.booking_id = $booking_id
");

if (mysqli_num_rows($query) === 0) {
    header("Location: bookings.php");
    exit();
}

$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Details | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-wrapper">
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<h3 class="section-title mb-4">Booking Details</h3>

<!-- SUMMARY -->
<div class="card shadow-sm mb-4">
<div class="card-body d-flex justify-content-between">
    <div>
        <h5 class="mb-1">Booking #<?= $data['booking_id']; ?></h5>
        <small class="text-muted">
            <?= $data['service_type']; ?> • <?= $data['pet_name']; ?> • <?= $data['full_name']; ?>
        </small>
    </div>
    <div class="text-end">
        <span class="badge bg-primary fs-6"><?= $data['status']; ?></span>
        <div class="fw-bold mt-1">
            ₱<?= number_format($data['total_amount'], 2); ?>
        </div>
    </div>
</div>
</div>

<div class="row g-4">

<!-- BOOKING INFO -->
<div class="col-md-6">
<div class="card shadow-sm h-100">
<div class="card-header fw-bold">📅 Booking Information</div>
<div class="card-body">

<table class="table table-sm table-borderless">
<tr><th>Client:</th><td><?= $data['full_name']; ?></td></tr>
<tr><th>Pet:</th><td><?= $data['pet_name']; ?></td></tr>
<tr><th>Service:</th><td><?= $data['service_type']; ?></td></tr>
<tr><th>Start Date:</th><td><?= date("F d, Y", strtotime($data['start_date'])); ?></td></tr>
<tr><th>End Date:</th><td><?= date("F d, Y", strtotime($data['end_date'])); ?></td></tr>
<tr><th>Total Days:</th><td><?= $data['total_days']; ?></td></tr>
<tr><th>Price / Day:</th><td>₱<?= number_format($data['price_per_day'],2); ?></td></tr>
<tr class="fw-bold"><th>Total Amount:</th><td>₱<?= number_format($data['total_amount'],2); ?></td></tr>
</table>

</div>
</div>
</div>

<!-- PAYMENT INFO -->
<div class="col-md-6">
<div class="card shadow-sm h-100">
<div class="card-header fw-bold">💳 Payment Information</div>
<div class="card-body">

<table class="table table-sm table-borderless">
<tr>
    <th>Status:</th>
    <td>
        <span class="badge bg-<?=
            $data['payment_status'] === 'Fully Paid' ? 'success' :
            ($data['payment_status'] === 'Partially Paid' ? 'warning' : 'secondary')
        ?>">
            <?= $data['payment_status']; ?>
        </span>
    </td>
</tr>

<tr><th>Payment Type:</th><td><?= $data['payment_type']; ?></td></tr>
<tr><th>Method:</th><td><?= $data['payment_method'] ?? '—'; ?></td></tr>

<?php if (!empty($data['reference_number'])) { ?>
<tr><th>Reference #:</th><td><?= $data['reference_number']; ?></td></tr>
<?php } ?>

<tr><th>Down Payment:</th><td>₱<?= number_format($data['down_payment_amount'],2); ?></td></tr>
<tr><th>Balance:</th><td>₱<?= number_format($data['balance_amount'],2); ?></td></tr>

</table>

<hr>

<h6 class="fw-bold">Proof of Payment</h6>

<?php if (!empty($data['down_payment_proof'])) { ?>
<a href="../assets/uploads/payments/<?= $data['down_payment_proof']; ?>"
   target="_blank"
   class="btn btn-sm btn-outline-primary mb-2">
   View Down Payment Proof
</a>
<?php } ?>

<?php if (!empty($data['full_payment_proof'])) { ?>
<a href="../assets/uploads/payments/<?= $data['full_payment_proof']; ?>"
   target="_blank"
   class="btn btn-sm btn-outline-success mb-2">
   View Full / Remaining Payment Proof
</a>
<?php } ?>

<?php if (empty($data['down_payment_proof']) && empty($data['full_payment_proof'])) { ?>
<p class="text-muted">No payment proof uploaded.</p>
<?php } ?>

</div>
</div>
</div>

</div>

<!-- ADMIN ACTIONS -->
<div class="mt-4 d-flex gap-2">

<a href="bookings.php" class="btn btn-secondary">← Back</a>

<?php if ($data['payment_status'] === 'Partially Paid' || $data['payment_status'] === 'Fully Paid') { ?>
<a href="bookings.php?action=verify&id=<?= $data['booking_id']; ?>"
   class="btn btn-success">
   Verify Payment
</a>
<?php } ?>

<?php if ($data['payment_status'] === 'Fully Paid' && $data['status'] !== 'Completed') { ?>
<a href="bookings.php?action=complete&id=<?= $data['booking_id']; ?>"
   class="btn btn-dark">
   Mark as Completed
</a>
<?php } ?>

</div>

</div>
</div>

</body>
</html>