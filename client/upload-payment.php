<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: my-bookings.php");
    exit();
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$booking = mysqli_query($conn, "
    SELECT * FROM bookings 
    WHERE booking_id='$booking_id' AND user_id='$user_id'
");

if (mysqli_num_rows($booking) == 0) {
    header("Location: my-bookings.php");
    exit();
}

$data = mysqli_fetch_assoc($booking);

if (isset($_POST['submit_payment'])) {

    $reference = mysqli_real_escape_string($conn, $_POST['reference_number']);

    if (empty($_FILES['payment_proof']['name'])) {
        die("Proof of payment is required.");
    }

    $upload_dir = "../assets/uploads/payments/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
    $filename = 'balance_' . time() . '_' . $user_id . '.' . $ext;
    $target = $upload_dir . $filename;

    if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target)) {
        die("Failed to upload payment proof.");
    }

    mysqli_query($conn, "
        UPDATE bookings SET
            payment_method='GCash',
            remaining_reference_number='$reference',
            full_payment_proof='$filename',
            balance_amount=0.00,
            payment_status='Fully Paid'
        WHERE booking_id='$booking_id'
        AND user_id='$user_id'
    ");

    $_SESSION['toast'] = "Remaining balance paid successfully. Booking is now fully paid.";
    $_SESSION['toast_type'] = "success";

    header("Location: my-bookings.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Payment | Dakimomo</title>
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

<?php if (isset($_SESSION['toast'])) { ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
  <div class="toast show text-bg-<?= $_SESSION['toast_type'] ?? 'success' ?>">
    <div class="d-flex">
      <div class="toast-body">
        <?= $_SESSION['toast']; ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto"
              data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php
unset($_SESSION['toast']);
unset($_SESSION['toast_type']);
} ?>

<div class="container mt-5 w-50 d-flex justify-content-center">
<div class="card shadow-sm p-4 col-sm-8">
<h4 class="section-title mb-3">Upload Payment Proof</h4>

<form method="POST" enctype="multipart/form-data">

<div class="alert alert-info mb-3">
    <strong>Remaining Balance:</strong>
    ₱<?= number_format($data['balance_amount'], 2); ?>
</div>

<div class="mb-3">
    <label>Payment Method</label>
    <input type="text"
           class="form-control"
           value="GCash"
           readonly>
</div>

<div class="mb-3">
    <label>GCash Reference Number</label>
    <input type="text"
           name="reference_number"
           class="form-control"
           placeholder="Enter GCash reference number"
           required>
</div>

<div class="mb-3">
    <label>Upload Proof of Payment</label>
    <input type="file"
           name="payment_proof"
           class="form-control"
           accept="image/*,.pdf"
           required>
</div>

<button type="submit" name="submit_payment" class="btn btn-brown w-100">
Pay Remaining Balance
</button>

</form>
</div>
</div>

<script>
setTimeout(() => {
    const toastEl = document.querySelector('.toast');
    if (toastEl) {
        bootstrap.Toast.getOrCreateInstance(toastEl).hide();
    }
}, 3000);
</script>

</body>
</html>