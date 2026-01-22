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
   ADMIN ADD BOOKING (WITH SLOT CHECKING)
========================== */
if (isset($_POST['add_booking'])) {

    $user_id  = (int)$_POST['user_id'];
    $pet_id   = (int)$_POST['pet_id'];
    $service  = $_POST['service_type'];
    $start    = $_POST['start_date'];
    $end      = !empty($_POST['end_date']) ? $_POST['end_date'] : $start;

    // SLOT LIMITS
    $service_capacity = [
        'Pet Sitting' => 5,
        'Pet Hotel'   => 5
    ];

    $max_slots = $service_capacity[$service] ?? 5;

    $current = $start;
    $dates_to_book = [];

    /* ========= CHECK ALL DATES FIRST ========= */
    while ($current <= $end) {

        $check = mysqli_query($conn, "
            SELECT COUNT(*) AS total
            FROM bookings
            WHERE service_type='$service'
            AND status!='Rejected'
            AND (
                '$current' BETWEEN start_date AND end_date
            )
        ");

        $row = mysqli_fetch_assoc($check);
        $remaining = $max_slots - $row['total'];

        if ($remaining <= 0) {
            header("Location: bookings.php?toast=full");
            exit();
        }

        $dates_to_book[] = $current;
        $current = date('Y-m-d', strtotime($current . ' +1 day'));
    }

    /* ========= PRICING ========= */
    $pet = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT pet_size FROM pets WHERE pet_id='$pet_id'"
    ));

    if ($service === 'Pet Sitting') {
        $price_per_day = 350;
    } else {
        $rates = [
            'Small'  => 350,
            'Medium' => 450,
            'Large'  => 550
        ];
        $price_per_day = $rates[$pet['pet_size']];
    }

    $total_days = count($dates_to_book);
    $total_amount = $price_per_day * $total_days;

    $payment_type   = $_POST['payment_type'];
    $payment_method = $_POST['payment_method'];
    $reference      = $_POST['reference_number'] ?? null;

    if ($payment_type === 'Down Payment') {
        $down_payment_amount = round($total_amount * 0.30, 2);
        $balance_amount      = round($total_amount - $down_payment_amount, 2);
        $payment_status      = 'Partially Paid';
    } else {
        $down_payment_amount = $total_amount;
        $balance_amount      = 0.00;
        $payment_status      = 'Fully Paid';
    }

    /* ========= INSERT ONE TRANSACTION ========= */
    mysqli_query($conn, "
        INSERT INTO bookings (
            user_id, pet_id, service_type,
            start_date, end_date,
            price_per_day, total_days, total_amount,
            payment_type, payment_method,
            reference_number,
            down_payment_amount, balance_amount,
            status, payment_status
        ) VALUES (
            '$user_id','$pet_id','$service',
            '$start','$end',
            '$price_per_day','$total_days','$total_amount',
            '$payment_type','$payment_method',
            ".($payment_method === 'GCash' ? "'$reference'" : "NULL").",
            '$down_payment_amount','$balance_amount',
            'Confirmed','$payment_status'
        )
    ");

    header("Location: bookings.php?toast=added");
    exit();
}

/* ==========================
   UPDATE BOOKING (ADMIN EDIT)
========================== */
if (isset($_POST['update_booking'])) {

    $booking_id = (int)$_POST['booking_id'];
    $start = $_POST['start_date'];
    $end   = !empty($_POST['end_date']) ? $_POST['end_date'] : $start;

    // Get existing booking
    $booking = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT service_type, price_per_day 
        FROM bookings WHERE booking_id=$booking_id
    "));

    $service = $booking['service_type'];
    $price_per_day = $booking['price_per_day'];

    // SLOT LIMITS
    $service_capacity = [
        'Pet Sitting' => 5,
        'Pet Hotel'   => 5
    ];

    $max_slots = $service_capacity[$service] ?? 5;

    // SLOT CHECK
    $current = $start;
    while ($current <= $end) {

        $check = mysqli_query($conn, "
            SELECT COUNT(*) AS total
            FROM bookings
            WHERE service_type='$service'
            AND status!='Rejected'
            AND booking_id!=$booking_id
            AND '$current' BETWEEN start_date AND end_date
        ");

        $row = mysqli_fetch_assoc($check);

        if ($row['total'] >= $max_slots) {
            header("Location: bookings.php?toast=full");
            exit();
        }

        $current = date('Y-m-d', strtotime($current . ' +1 day'));
    }

    // RECOMPUTE
    $total_days = (strtotime($end) - strtotime($start)) / (60*60*24) + 1;
    $total_amount = $total_days * $price_per_day;

    mysqli_query($conn, "
        UPDATE bookings SET
            start_date='$start',
            end_date='$end',
            total_days='$total_days',
            total_amount='$total_amount',
            payment_status='Unpaid',
            status='Reserved'
        WHERE booking_id=$booking_id
    ");

    header("Location: bookings.php?toast=updated");
    exit();
}

/* ==========================
   VERIFY PAYMENT
========================== */
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'verify') {
    $id = (int) $_GET['id'];

    mysqli_query($conn, "
        UPDATE bookings
        SET
            status = 'Confirmed'
        WHERE booking_id = $id
        AND payment_status IN ('Fully Paid', 'Partially Paid')
    ");

    header("Location: bookings.php?toast=verified");
    exit();
}

/* ==========================
   MARK AS COMPLETED
========================== */
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'complete') {
    $id = (int) $_GET['id'];

    mysqli_query($conn, "
        UPDATE bookings
        SET status='Completed'
        WHERE booking_id=$id
        AND status='Confirmed'
        AND payment_status='Fully Paid'
    ");

    header("Location: bookings.php?toast=completed");
    exit();
}

/* ==========================
   APPROVE BOOKING
========================== */
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'approve') {
    $id = (int) $_GET['id'];

    mysqli_query($conn, "
        UPDATE bookings
        SET status='Confirmed'
        WHERE booking_id=$id AND status='Reserved'
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
        SET status='Rejected', rejection_reason='$reason', payment_status='Not Required'
        WHERE booking_id=$booking_id
    ");

    header("Location: bookings.php?toast=rejected");
    exit();
}

/* ==========================
   DELETE BOOKING
========================== */
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int) $_GET['id'];

    mysqli_query($conn, "DELETE FROM bookings WHERE booking_id=$id");

    header("Location: bookings.php?toast=deleted");
    exit();
}

/* ==========================
   SEARCH
========================== */
$search = $_GET['search'] ?? '';
$where = '';

if ($search !== '') {
    $safe = mysqli_real_escape_string($conn, $search);
    $where = "
        WHERE u.full_name LIKE '%$safe%'
        OR p.pet_name LIKE '%$safe%'
        OR b.service_type LIKE '%$safe%'
    ";
}

/* ==========================
   FETCH BOOKINGS
========================== */
$bookings = mysqli_query($conn, "
    SELECT
        b.booking_id,
        b.service_type,
        b.start_date,
        b.end_date,
        b.status,
        b.payment_status,
        b.payment_proof,
        b.total_amount,
        b.rejection_reason,
        p.pet_name,
        u.full_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    JOIN users u ON b.user_id = u.user_id
    $where
    ORDER BY 
        FIELD(b.status,'Pending','Reserved','Confirmed','Completed','Rejected'),
        b.start_date DESC
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
    <h3 class="section-title">Booking Management</h3>
    <button class="btn btn-brown" data-bs-toggle="modal" data-bs-target="#addBookingModal">
        + Add Booking
    </button>
</div>

<form class="mb-3">
    <input type="text" name="search" class="form-control"
           placeholder="Search client, pet, service"
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
    <th>Date Range</th>
    <th>Status</th>
    <th>Payment</th>
    <th>Remarks</th>
    <th class="text-center">Actions</th>
</tr>
</thead>
<tbody>

<?php while ($b = mysqli_fetch_assoc($bookings)) { ?>
<tr>
<td><?= htmlspecialchars($b['full_name']); ?></td>
<td><?= htmlspecialchars($b['pet_name']); ?></td>
<td><?= htmlspecialchars($b['service_type']); ?></td>
<td>
<?= date("M d, Y", strtotime($b['start_date'])) ?>
–
<?= date("M d, Y", strtotime($b['end_date'])) ?>
</td>
<td>
<?php
$status_badge = [
    'Pending' => 'warning',
    'Reserved' => 'primary',
    'Confirmed' => 'success',
    'Completed' => 'dark',
    'Rejected' => 'danger'
];
echo '<span class="badge bg-' . ($status_badge[$b['status']] ?? 'secondary') . '">' . $b['status'] . '</span>';
?>
</td>

<td>
<?php
$payBadge = [
    'Unpaid' => 'secondary',
    'For Verification' => 'info',
    'Partially Paid' => 'warning',
    'Fully Paid' => 'success',
    'Not Required' => 'secondary'
];
echo '<span class="badge bg-' . ($payBadge[$b['payment_status']] ?? 'secondary') . '">' 
     . ($b['payment_status'] ?? 'Unpaid') . '</span>';
?>
</td>
<td><?= htmlspecialchars($b['rejection_reason'] ?? '—'); ?></td>

<td class="text-center">

<a href="booking-details.php?id=<?= $b['booking_id']; ?>"
   class="btn btn-sm btn-primary">
   View
</a>

<?php if ($b['status'] === 'Reserved') { ?>
    <a href="?action=approve&id=<?= $b['booking_id']; ?>" class="btn btn-sm btn-success">Approve</a>
    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
            data-bs-target="#reject<?= $b['booking_id']; ?>">Reject</button>

<?php } elseif ($b['payment_status'] === 'For Verification') { ?>
    <?php if (!empty($b['payment_proof'])) { ?>
        <a href="../assets/uploads/payments/<?= $b['payment_proof']; ?>"
        target="_blank"
        class="btn btn-sm btn-outline-primary">View Proof</a>
    <?php } ?>

    <a href="?action=verify&id=<?= $b['booking_id']; ?>"
       class="btn btn-sm btn-success">Verify</a>

<?php } elseif ($b['status'] === 'Paid') { ?>
    <a href="?action=complete&id=<?= $b['booking_id']; ?>"
       class="btn btn-sm btn-dark">Mark Completed</a>

<?php } ?>

</td>
</tr>

<!-- REJECT MODAL -->
<div class="modal fade" id="reject<?= $b['booking_id']; ?>">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5 class="modal-title">Reject Booking</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="hidden" name="booking_id" value="<?= $b['booking_id']; ?>">
<textarea name="rejection_reason" class="form-control" required
placeholder="Reason for rejection"></textarea>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="reject_booking" class="btn btn-danger">Reject</button>
</div>
</form>
</div>
</div>
</div>

<!-- EDIT BOOKING MODAL -->
<div class="modal fade" id="edit<?= $b['booking_id']; ?>" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<form method="POST">

<div class="modal-header">
<h5 class="modal-title">Edit Booking</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="booking_id" value="<?= $b['booking_id']; ?>">

<label>Start Date</label>
<input type="date"
name="start_date"
class="form-control mb-2"
value="<?= $b['start_date']; ?>"
required>

<label>End Date</label>
<input type="date"
name="end_date"
class="form-control"
value="<?= $b['end_date']; ?>">

<p class="text-muted mt-2 small">
⚠ Editing dates will reset payment verification.
</p>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="update_booking" class="btn btn-primary">
Save Changes
</button>
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

<!-- Modal -->
<!-- ADD BOOKING MODAL -->
<div class="modal fade" id="addBookingModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content">
<form method="POST">

<div class="modal-header">
    <h5 class="modal-title">Add Booking (Admin)</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<!-- CLIENT SEARCH -->
<label>Client</label>
<input list="clientList" id="clientInput" class="form-control mb-2"
       placeholder="Search client name..." required>
<datalist id="clientList">
<?php
$clients = mysqli_query($conn,"SELECT user_id, full_name FROM users WHERE role='client'");
while ($c = mysqli_fetch_assoc($clients)) {
    echo "<option value='{$c['full_name']}' data-id='{$c['user_id']}'>";
}
?>
</datalist>
<input type="hidden" name="user_id" id="clientId">

<!-- PET -->
<label>Pet</label>
<select name="pet_id" id="petSelect" class="form-control mb-3" disabled required>
<option value="">Select pet</option>
<?php
$pets = mysqli_query($conn,"SELECT pet_id, pet_name, pet_size, user_id FROM pets");
while ($p = mysqli_fetch_assoc($pets)) {
    echo "<option value='{$p['pet_id']}'
            data-owner='{$p['user_id']}'
            data-size='{$p['pet_size']}'>
            {$p['pet_name']} ({$p['pet_size']})
          </option>";
}
?>
</select>

<!-- SERVICE -->
<label>Service</label>
<select name="service_type" id="serviceSelect" class="form-control mb-3" required>
<option value="">Select service</option>
<option value="Pet Sitting">Pet Sitting</option>
<option value="Pet Hotel">Pet Hotel</option>
</select>

<!-- DATE RANGE -->
<label>Start Date</label>
<input type="date" name="start_date" id="startDate" class="form-control mb-2" required>

<label>End Date</label>
<input type="date" name="end_date" id="endDate" class="form-control mb-3">

<!-- PRICE PREVIEW -->
<div class="alert alert-info">
    <strong>Price per day:</strong> ₱<span id="pricePerDay">0</span><br>
    <strong>Total days:</strong> <span id="totalDays">0</span><br>
    <strong>Total amount:</strong> ₱<span id="totalAmount">0</span>
</div>

<hr>

<h6 class="fw-bold">Payment (Walk-in)</h6>

<div class="mb-2">
    <label>Payment Type</label>
    <select name="payment_type" id="adminPaymentType" class="form-control" required>
        <option value="Full Payment">Full Payment</option>
        <option value="Down Payment">Down Payment (30%)</option>
    </select>
</div>

<div class="mb-2">
    <label>Payment Method</label>
    <select name="payment_method" id="adminPaymentMethod" class="form-control" required>
        <option value="Cash">Cash</option>
        <option value="GCash">GCash</option>
    </select>
</div>

<div class="mb-2 d-none" id="adminReferenceBox">
    <label>GCash Reference Number</label>
    <input type="text" name="reference_number" class="form-control">
</div>

</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button name="add_booking" class="btn btn-success">Save Booking</button>
</div>

</form>
</div>
</div>
</div>

<!-- End Modal -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const clientInput = document.getElementById('clientInput');
const clientId    = document.getElementById('clientId');
const petSelect   = document.getElementById('petSelect');
const serviceSel  = document.getElementById('serviceSelect');
const startDate   = document.getElementById('startDate');
const endDate     = document.getElementById('endDate');

const pricePerDayEl = document.getElementById('pricePerDay');
const totalDaysEl   = document.getElementById('totalDays');
const totalAmountEl = document.getElementById('totalAmount');

/* CLIENT → ENABLE PETS */
clientInput.addEventListener('input', () => {
    const option = [...document.querySelectorAll('#clientList option')]
        .find(o => o.value === clientInput.value);

    if (!option) {
        petSelect.disabled = true;
        return;
    }

    clientId.value = option.dataset.id;
    petSelect.disabled = false;

    [...petSelect.options].forEach(opt => {
        if (!opt.dataset.owner) return;
        opt.hidden = opt.dataset.owner !== clientId.value;
    });
});

/* PRICE CALCULATION */
function computeTotal() {
    const petOpt = petSelect.selectedOptions[0];
    if (!petOpt || !serviceSel.value || !startDate.value) return;

    const size = petOpt.dataset.size;
    const service = serviceSel.value;

    let price = 0;

    if (service === 'Pet Sitting') {
        price = 350;
    } else {
        if (size === 'Small') price = 350;
        if (size === 'Medium') price = 450;
        if (size === 'Large') price = 550;
    }

    const start = new Date(startDate.value);
    const end   = endDate.value ? new Date(endDate.value) : start;
    const days  = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;

    pricePerDayEl.textContent = price;
    totalDaysEl.textContent   = days;
    totalAmountEl.textContent = price * days;
}

[petSelect, serviceSel, startDate, endDate].forEach(el =>
    el.addEventListener('change', computeTotal)
);
</script>

<?php if (isset($_GET['toast'])) { ?>
<div class="toast-container position-fixed top-0 end-0 p-3">

    <?php
    $toastMap = [
        'added'     => ['success', 'Booking added successfully.'],
        'approved'  => ['primary', 'Booking approved. Waiting for payment.'],
        'rejected'  => ['danger',  'Booking rejected with reason.'],
        'deleted'   => ['dark',    'Booking deleted successfully.'],
        'updated'   => ['info',    'Booking updated. Payment has been reset.'],
        'full'      => ['warning', 'One or more selected dates are fully booked.'],
        'verified'  => ['success', 'Payment verified successfully.'],
        'completed'=> ['dark', 'Booking marked as completed.']
    ];

    [$color, $message] = $toastMap[$_GET['toast']] ?? ['secondary', 'Action completed.'];
    ?>

    <div id="adminToast"
         class="toast align-items-center text-white bg-<?= $color ?> border-0"
         role="alert"
         aria-live="assertive"
         aria-atomic="true"
         data-bs-delay="3000">

        <div class="d-flex">
            <div class="toast-body">
                <?= $message ?>
            </div>
            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>

    </div>
</div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toastEl = document.getElementById('adminToast');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>

</body>
</html>