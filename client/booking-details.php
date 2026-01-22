<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: my-bookings.php");
    exit();
}

$booking_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT b.*, p.pet_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    WHERE b.booking_id='$booking_id'
    AND b.user_id='$user_id'
");

if (mysqli_num_rows($query) == 0) {
    header("Location: my-bookings.php");
    exit();
}

$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Details | Dakimomo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
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
      <li class="nav-item"><a class="nav-link" href="pets.php">My Pets</a></li>
      <li class="nav-item"><a class="nav-link active" href="my-bookings.php">Bookings</a></li>
      <li class="nav-item"><a class="nav-link" href="chat.php">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4">

<!-- HEADER SUMMARY -->
<div class="card shadow-sm mb-4">
<div class="card-body d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-1">Booking #<?= $data['booking_id']; ?></h4>
        <small class="text-muted"><?= $data['service_type']; ?> for <?= $data['pet_name']; ?></small>
    </div>
    <div class="text-end">
        <span class="badge fs-6 bg-<?=
            $data['status'] === 'Pending' ? 'warning' :
            ($data['status'] === 'Reserved' ? 'primary' :
            ($data['status'] === 'Confirmed' ? 'success' :
            ($data['status'] === 'Completed' ? 'dark' :
            ($data['status'] === 'Rejected' ? 'danger' : 'secondary'))))
        ?>">
            <?= $data['status']; ?>
        </span>
        <div class="fw-bold mt-1">
            ₱<?= number_format($data['total_amount'], 2); ?>
        </div>
        <small class="text-muted">
            Created: <?= date("F d, Y h:i A", strtotime($data['date_created'])); ?>
        </small>
    </div>
</div>
</div>

<!-- DETAILS GRID -->
<div class="row g-4">

<!-- BOOKING INFO -->
<div class="col-md-6">
<div class="card shadow-sm h-100">
<div class="card-header fw-bold">📅 Booking Information</div>
<div class="card-body">

<table class="table table-sm table-borderless mb-0">
<tr><th>Pet Name:</th><td><?= $data['pet_name']; ?></td></tr>
<tr><th>Service:</th><td><?= $data['service_type']; ?></td></tr>
<tr><th>Start Date:</th><td><?= date("F d, Y", strtotime($data['start_date'])); ?></td></tr>
<tr><th>End Date:</th><td><?= date("F d, Y", strtotime($data['end_date'])); ?></td></tr>
<tr><th>Total Days:</th><td><?= $data['total_days']; ?> day(s)</td></tr>
<tr><th>Price / Day:</th><td>₱<?= number_format($data['price_per_day'],2); ?></td></tr>
</table>

</div>
</div>
</div>

<!-- PAYMENT INFO -->
<div class="col-md-6">
<div class="card shadow-sm h-100">
<div class="card-header fw-bold">💳 Payment Information</div>
<div class="card-body">

<table class="table table-sm table-borderless mb-0">

<tr>
    <th>Payment Status:</th>
    <td>
        <span class="badge bg-<?=
            $data['payment_status'] === 'Fully Paid' ? 'success' :
            ($data['payment_status'] === 'Partially Paid' ? 'warning' :
            'secondary')
        ?>">
            <?= $data['payment_status']; ?>
        </span>
    </td>
</tr>

<tr><th>Payment Type:</th><td><?= $data['payment_type']; ?></td></tr>

<?php if (!empty($data['payment_method'])) { ?>
<tr><th>Payment Method:</th><td><?= $data['payment_method']; ?></td></tr>
<?php } ?>

<?php if (!empty($data['reference_number'])) { ?>
<tr><th>GCash Reference #:</th><td><?= $data['reference_number']; ?></td></tr>
<?php } ?>

<tr><th>Total Amount:</th>
<td class="fw-bold text-success">
    ₱<?= number_format($data['total_amount'], 2); ?>
</td></tr>

<?php if ($data['payment_type'] === 'Down Payment') { ?>
<tr>
    <th>Down Payment:</th>
    <td class="text-primary">
        ₱<?= number_format($data['down_payment_amount'], 2); ?>
    </td>
</tr>
<tr>
    <th>Remaining Balance:</th>
    <td class="text-danger">
        ₱<?= number_format($data['balance_amount'], 2); ?>
    </td>
</tr>
<?php } ?>

<!-- DOWN PAYMENT PROOF -->
<?php if (!empty($data['down_payment_proof'])) { ?>
<tr>
    <th>Down Payment Proof:</th>
    <td>
        <a href="../assets/uploads/payments/<?= $data['down_payment_proof']; ?>"
           target="_blank"
           class="btn btn-sm btn-outline-primary">
           View Proof
        </a>
    </td>
</tr>
<?php } ?>

<!-- FULL PAYMENT PROOF -->
<?php if (!empty($data['full_payment_proof'])) { ?>
<tr>
    <th>Full Payment Proof:</th>
    <td>
        <a href="../assets/uploads/payments/<?= $data['full_payment_proof']; ?>"
           target="_blank"
           class="btn btn-sm btn-outline-success">
           View Proof
        </a>
    </td>
</tr>
<?php } ?>

<?php if (!empty($data['rejection_reason'])) { ?>
<tr>
    <th>Admin Remarks:</th>
    <td class="text-danger"><?= $data['rejection_reason']; ?></td>
</tr>
<?php } ?>

</table>

</div>
</div>
</div>

</div>

<!-- ACTION BUTTONS -->
<div class="mt-4 d-flex gap-2 mb-5">
    <a href="my-bookings.php" class="btn btn-secondary">← Back</a>

    <?php if ($data['status'] === 'Approved' && $data['payment_status'] === 'Unpaid') { ?>
        <a href="upload-payment.php?id=<?= $data['booking_id']; ?>"
           class="btn btn-brown">
           Upload Payment
        </a>
    <?php } ?>
</div>

</div>

</body>
</html>