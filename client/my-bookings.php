<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

/* ==========================
   CLIENT CANCEL BOOKING
========================== */
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'cancel') {

    $booking_id = (int)$_GET['id'];
    $user_id    = $_SESSION['user_id'];

    mysqli_query($conn, "
        UPDATE bookings
        SET 
            status='Rejected',
            payment_status='Not Required',
            rejection_reason='Cancelled by client'
        WHERE booking_id=$booking_id
        AND user_id=$user_id
        AND payment_status='For Verification' OR payment_status='Partially Paid'
    ");

    header("Location: my-bookings.php?toast=cancelled");
    exit();
}

$result = mysqli_query($conn, "
    SELECT b.*, p.pet_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    WHERE b.user_id='$user_id'
    ORDER BY b.start_date DESC
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | Dakimomo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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

<?php if (isset($_GET['toast']) && $_GET['toast']=='cancelled') { ?>
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast show text-white bg-danger">
        <div class="toast-body">
            Booking cancelled successfully.
        </div>
    </div>
</div>
<?php } ?>

<!-- MAIN CONTENT -->
<div class="container mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="section-title mb-1">My Bookings</h3>
            <p class="text-muted mb-0">View the status of your service requests.</p>
        </div>
        <a href="book-service.php" class="btn btn-brown">
            Book New Service
        </a>
    </div>
    <!-- BOOKINGS TABLE -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Booking #</th>
                        <th>Pet</th>
                        <th>Service</th>
                        <th>Date Range</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td>#<?= $row['booking_id']; ?></td>
                            <td><?= htmlspecialchars($row['pet_name']); ?></td>
                            <td><?= htmlspecialchars($row['service_type']); ?></td>
                            <td>
                            <?= date("M d, Y", strtotime($row['start_date'])) ?>
                            –
                            <?= date("M d, Y", strtotime($row['end_date'])) ?>
                            </td>
                            <td>
                            <?php
                            $days = (strtotime($row['end_date']) - strtotime($row['start_date'])) / 86400 + 1;
                            echo $days . " day(s)";
                            ?>
                            </td>
                            <td>
                                <?php
                                $statusBadge = [
                                    'Pending'    => ['warning', 'Awaiting Approval'],
                                    'Reserved'   => ['primary', 'Booked'],
                                    'Confirmed'   => ['success', 'Confirmed'],
                                    'Completed'  => ['dark', 'Completed'],
                                    'Rejected'   => ['danger', 'Cancelled']
                                ];

                                [$color, $label] = $statusBadge[$row['status']] ?? ['secondary','Unknown'];
                                echo "<span class='badge bg-$color'>$label</span>";
                                ?>
                            </td>
                            <td>
                                <?php
                                $payBadge = [
                                    'Pending'         => ['danger','Pending'],
                                    'For Verification'=> ['info','For Verification'],
                                    'Partially Paid'  => ['warning','Partially Paid'],
                                    'Fully Paid'      => ['success','Fully Paid'],
                                    'Not Required'   => ['secondary','Not Required']
                                ];

                                [$color, $label] = $payBadge[$row['payment_status']] ?? ['secondary','—'];
                                echo "<span class='badge bg-$color'>$label</span>";
                                ?>
                            </td>
                            <td>
                                <a title="Details" href="booking-details.php?id=<?= $row['booking_id']; ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php
                                if (
                                    $row['payment_status'] === 'Partially Paid' && $row['status'] === 'Confirmed'
                                ) {
                                ?>
                                    <a href="upload-payment.php?id=<?= $row['booking_id']; ?>"
                                    class="btn btn-sm btn-brown">
                                    Pay
                                    </a>
                                <?php } ?>
                                <?php
                                if (
                                    $row['status'] === 'Reserved' && 
                                    $row['payment_status'] === 'Partially Paid'
                                ) {
                                ?>  
                                    <button class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelModal<?= $row['booking_id']; ?>">
                                        Cancel
                                    </button>
                                <?php } ?>
                                <?php
                                if (
                                    $row['status'] === 'Confirmed' || $row['status'] === 'Completed'
                                ) {
                                ?>  
                                    <a href="view-updates.php?id=<?= $row['booking_id']; ?>" 
                                        class="btn btn-sm btn-info">
                                        Updates
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>

<!-- CANCEL BOOKING MODAL -->
<div class="modal fade" id="cancelModal<?= $row['booking_id']; ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title text-danger">
            Cancel Booking #<?= $row['booking_id']; ?>
        </h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="mb-2">
            Are you sure you want to cancel this booking?
        </p>
        <ul class="small text-muted">
            <li>This action cannot be undone</li>
            <li>No payment will be required</li>
            <li>The booking will be marked as cancelled</li>
            <li>The down payment made is non-refundable</li>
        </ul>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">
            Keep Booking
        </button>

        <a href="?action=cancel&id=<?= $row['booking_id']; ?>"
           class="btn btn-danger">
            Yes, Cancel Booking
        </a>
      </div>

    </div>
  </div>
</div>

                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <h6>No bookings yet</h6>
                            <p class="mb-2">You haven’t booked a service yet.</p>
                            <a href="book-service.php" class="btn btn-brown btn-sm">
                                Book Your First Service
                            </a>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>

        </div>
    </div>
    <p class="text-muted mt-3">
    <small>
    Note: Payments are only required after booking approval.
    </small>
</p>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>