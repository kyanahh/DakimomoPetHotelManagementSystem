<?php
include '../includes/auth.php';
include '../includes/db.php';

/* ==========================
   ADMIN ACCESS ONLY
========================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ==========================
   APPROVE BOOKING
========================== */
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'approve') {
    $id = (int) $_GET['id'];
    mysqli_query($conn, "
        UPDATE bookings 
        SET status='Approved', rejection_reason=NULL 
        WHERE booking_id=$id
    ");
    header("Location: bookings.php?toast=approved");
    exit();
}

/* ==========================
   REJECT BOOKING
========================== */
if (isset($_POST['reject_booking'])) {
    $booking_id = (int) $_POST['booking_id'];
    $reason = mysqli_real_escape_string($conn, $_POST['rejection_reason']);

    mysqli_query($conn, "
        UPDATE bookings 
        SET status='Rejected', rejection_reason='$reason'
        WHERE booking_id=$booking_id
    ");

    header("Location: bookings.php?toast=rejected");
    exit();
}

/* ==========================
   ADMIN ADD BOOKING (DATE RANGE)
========================== */
if (isset($_POST['add_booking'])) {

    $user_id  = (int) $_POST['user_id'];
    $pet_id   = (int) $_POST['pet_id'];
    $service  = $_POST['service_type'];
    $start    = $_POST['start_date'];
    $end      = $_POST['end_date'] ?: $start;

    $current = $start;

    while ($current <= $end) {
        mysqli_query($conn, "
            INSERT INTO bookings (user_id, pet_id, service_type, booking_date, status)
            VALUES ('$user_id','$pet_id','$service','$current','Approved')
        ");
        $current = date('Y-m-d', strtotime($current . ' +1 day'));
    }

    header("Location: bookings.php?toast=added");
    exit();
}

/* ==========================
   SEARCH BOOKINGS
========================== */
$search = $_GET['search'] ?? '';
$where = "";

if (!empty($search)) {
    $safe = mysqli_real_escape_string($conn, $search);
    $where = "
        WHERE 
            u.full_name LIKE '%$safe%' OR
            p.pet_name LIKE '%$safe%' OR
            b.service_type LIKE '%$safe%' OR
            b.booking_date LIKE '%$safe%'
    ";
}

/* ==========================
   FETCH BOOKINGS (SORTED)
========================== */
$bookings = mysqli_query($conn, "
    SELECT 
        b.booking_id,
        b.service_type,
        b.booking_date,
        b.status,
        b.rejection_reason,
        p.pet_name,
        u.full_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    JOIN users u ON b.user_id = u.user_id
    $where
    ORDER BY 
        CASE 
            WHEN b.status = 'Pending' THEN 1
            WHEN b.status = 'Approved' THEN 2
            WHEN b.status = 'Rejected' THEN 3
        END,
        b.booking_date DESC
");

/* ==========================
   FETCH CLIENTS & PETS
========================== */
$clients = mysqli_query($conn, "
    SELECT user_id, full_name FROM users WHERE role='client'
");

$pets = mysqli_query($conn, "
    SELECT pet_id, pet_name, user_id FROM pets
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Bookings | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-wrapper">
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title">Manage Bookings</h3>
    <button class="btn btn-brown" data-bs-toggle="modal" data-bs-target="#addBookingModal">
        + Add Booking
    </button>
</div>

<form class="mb-3">
    <input type="text"
           name="search"
           class="form-control"
           placeholder="Search by client, pet, service, or date"
           value="<?= htmlspecialchars($search); ?>">
</form>

<div class="card shadow-sm">
<div class="card-body p-0">

<table class="table table-hover align-middle mb-0">
<thead class="table-light">
<tr>
    <th>Client</th>
    <th>Pet</th>
    <th>Service</th>
    <th>Date</th>
    <th>Status</th>
    <th>Remarks</th>
    <th class="text-center">Action</th>
</tr>
</thead>
<tbody>

<?php while ($b = mysqli_fetch_assoc($bookings)) { ?>
<tr>
<td><?= htmlspecialchars($b['full_name']); ?></td>
<td><?= htmlspecialchars($b['pet_name']); ?></td>
<td><?= htmlspecialchars($b['service_type']); ?></td>
<td><?= date("M d, Y", strtotime($b['booking_date'])); ?></td>
<td>
<?php
if ($b['status']=='Pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
elseif ($b['status']=='Approved') echo '<span class="badge bg-success">Approved</span>';
else echo '<span class="badge bg-danger">Rejected</span>';
?>
</td>
<td><?= htmlspecialchars($b['rejection_reason']); ?></td>
<td class="text-center">
<?php if ($b['status']=='Pending') { ?>
<a href="?action=approve&id=<?= $b['booking_id']; ?>" class="btn btn-sm btn-success">Approve</a>
<button class="btn btn-sm btn-danger" data-bs-toggle="modal"
data-bs-target="#reject<?= $b['booking_id']; ?>">Reject</button>
<?php } else { echo '—'; } ?>
</td>
</tr>

<!-- REJECT MODAL -->
<div class="modal fade" id="reject<?= $b['booking_id']; ?>" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5 class="modal-title">Reject Booking</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="hidden" name="booking_id" value="<?= $b['booking_id']; ?>">
<textarea name="rejection_reason" class="form-control" rows="4" required></textarea>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="reject_booking" class="btn btn-danger">Reject</button>
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

<!-- ADD BOOKING MODAL -->
<div class="modal fade" id="addBookingModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<form method="POST">

<div class="modal-header">
<h5 class="modal-title">Add Booking (Admin)</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<!-- CLIENT SEARCH -->
<input list="clients" id="clientInput" class="form-control mb-3"
placeholder="Select Client" required>
<datalist id="clients">
<?php while ($c = mysqli_fetch_assoc($clients)) { ?>
<option value="<?= $c['full_name']; ?>" data-id="<?= $c['user_id']; ?>">
<?php } ?>
</datalist>
<input type="hidden" name="user_id" id="clientId">

<!-- PET -->
<select name="pet_id" id="petSelect" class="form-control mb-3" disabled required>
<option value="">Select Pet</option>
<?php while ($p = mysqli_fetch_assoc($pets)) { ?>
<option value="<?= $p['pet_id']; ?>" data-owner="<?= $p['user_id']; ?>">
<?= $p['pet_name']; ?>
</option>
<?php } ?>
</select>

<select name="service_type" class="form-control mb-3">
<option>Pet Sitting</option>
<option>Pet Hotel</option>
</select>

<label>Start Date</label>
<input type="date" name="start_date" class="form-control mb-2"
min="<?= date('Y-m-d', strtotime('+1 day')); ?>" required>

<label>End Date (optional)</label>
<input type="date" name="end_date" class="form-control">

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="add_booking" class="btn btn-success">Add Booking</button>
</div>

</form>
</div>
</div>
</div>

<!-- TOAST -->
<?php if (isset($_GET['toast'])) { ?>
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="actionToast"
       class="toast text-white
       <?= $_GET['toast']=='approved' ? 'bg-success' :
          ($_GET['toast']=='added' ? 'bg-primary' : 'bg-danger'); ?>"
       role="alert"
       aria-live="assertive"
       aria-atomic="true"
       data-bs-delay="3000">

    <div class="d-flex">
      <div class="toast-body">
        <?php
          if ($_GET['toast']=='approved') echo "Booking approved successfully.";
          elseif ($_GET['toast']=='added') echo "Booking added successfully.";
          else echo "Booking rejected with reason.";
        ?>
      </div>
      <button type="button"
              class="btn-close btn-close-white me-2 m-auto"
              data-bs-dismiss="toast"></button>
    </div>

  </div>
</div>
<?php } ?>

<script>
const clientInput = document.getElementById('clientInput');
const clientId = document.getElementById('clientId');
const petSelect = document.getElementById('petSelect');

clientInput.addEventListener('input', () => {
    const option = [...document.querySelectorAll('#clients option')]
        .find(o => o.value === clientInput.value);

    if (!option) {
        petSelect.disabled = true;
        return;
    }

    clientId.value = option.dataset.id;
    petSelect.disabled = false;

    [...petSelect.options].forEach(opt => {
        opt.hidden = opt.dataset.owner && opt.dataset.owner !== clientId.value;
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toastEl = document.getElementById('actionToast');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>

</body>
</html>