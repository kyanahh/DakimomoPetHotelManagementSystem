<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id   = $_SESSION['user_id'];
$tomorrow  = date('Y-m-d', strtotime('+1 day'));
$capacity  = 5;

$error = null;
$success = null;

/* ==========================
   HANDLE BOOKING SUBMISSION
========================== */
if (isset($_POST['book_service'])) {

    $pet_id       = (int)$_POST['pet_id'];
    $service      = $_POST['service_type'];
    $start        = $_POST['booking_date'];
    $end          = !empty($_POST['end_date']) ? $_POST['end_date'] : $start;
    $payment_type = $_POST['payment_type'] ?? 'Down Payment';
    $payment_method   = $_POST['payment_method'] ?? 'GCash';
    $reference_number = trim($_POST['reference_number'] ?? '');

    /* REQUIRE PROOF OF PAYMENT (STRICT & CORRECT) */
    if ($payment_type === 'Down Payment') {

        if (
            !isset($_FILES['down_payment_proof']) ||
            $_FILES['down_payment_proof']['error'] === UPLOAD_ERR_NO_FILE
        ) {
            $error = "Please upload proof of down payment.";
        }

    } else {

        if (
            !isset($_FILES['payment_proof']) ||
            $_FILES['payment_proof']['error'] === UPLOAD_ERR_NO_FILE
        ) {
            $error = "Please upload proof of full payment.";
        }

    }

    if (!$error && empty($reference_number)) {
        $error = "Please enter the GCash reference number.";
    }

    if (!$error) {
        if ($payment_type === 'Down Payment') {
            if ($_FILES['down_payment_proof']['error'] !== 0) {
                $error = "Error uploading down payment proof.";
            }
        } elseif ($payment_type === 'Full Payment') {
            if ($_FILES['payment_proof']['error'] !== 0) {
                $error = "Error uploading full payment proof.";
            }
        }
    }

    if ($end < $start) {
        $error = "End date cannot be earlier than start date.";
    } elseif ($start < $tomorrow) {
        $error = "Bookings must start from tomorrow onward.";
    }

    /* GET PET SIZE */
    if (!$error) {
        $pet = mysqli_fetch_assoc(mysqli_query($conn,"
            SELECT pet_size FROM pets
            WHERE pet_id='$pet_id' AND user_id='$user_id'
        "));

        if (!$pet) {
            $error = "Invalid pet selected.";
        }
    }

    /* PRICING (ALIGNED WITH ADMIN) */
    if (!$error) {
        if ($service === 'Pet Sitting') {
            $price_per_day = 350;
        } elseif ($service === 'Pet Hotel') {
            $rates = [
                'Small'  => 350,
                'Medium' => 450,
                'Large'  => 550
            ];
            $price_per_day = $rates[$pet['pet_size']];
        }

        $total_days   = (strtotime($end) - strtotime($start)) / 86400 + 1;
        $total_amount = $price_per_day * $total_days;

        if ($payment_type === 'Down Payment') {

            $down_payment_amount = round($total_amount * 0.30, 2);
            $balance_amount      = round($total_amount - $down_payment_amount, 2);
            $payment_status      = 'Partially Paid';

        } else {

            $down_payment_amount = $total_amount;
            $balance_amount      = 0.00;
            $payment_status      = 'Fully Paid';
        }
    }

    /* SLOT CHECK (PER DAY – SAME AS ADMIN) */
    if (!$error) {
        $current = $start;
        while ($current <= $end) {

            $check = mysqli_fetch_assoc(mysqli_query($conn,"
                SELECT COUNT(*) AS total
                FROM bookings
                WHERE service_type='$service'
                AND status!='Rejected'
                AND '$current' BETWEEN start_date AND end_date
            "));

            if ($check['total'] >= $capacity) {
                $error = "Some selected dates are already fully booked.";
                break;
            }

            $current = date('Y-m-d', strtotime($current . ' +1 day'));
        }
    }

    /* SAVE PAYMENT PROOF */
    $down_payment_filename = null;
    $full_payment_filename = null;

    if (!$error) {

        $uploadDir = "../assets/uploads/payments/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if ($payment_type === 'Down Payment') {

            $file = $_FILES['down_payment_proof'];
            $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);

            $down_payment_filename = 'down_' . time() . '_' . $user_id . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $uploadDir . $down_payment_filename);

        } else {

            $file = $_FILES['payment_proof'];
            $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);

            $full_payment_filename = 'full_' . time() . '_' . $user_id . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $uploadDir . $full_payment_filename);
        }
    }

    if ($payment_type === 'Full Payment') {
        $payment_status = 'Fully Paid';
    } else {
        $payment_status = 'Partially Paid';
    }

    /* INSERT (DO NOT CHANGE — AS REQUESTED) */
    if (!$error) {

        mysqli_query($conn,"
            INSERT INTO bookings (
                user_id, pet_id, service_type,
                start_date, end_date,
                price_per_day, total_days, total_amount,
                payment_type, payment_method, reference_number,
                down_payment_amount, balance_amount,
                down_payment_proof, full_payment_proof,
                status, payment_status
            ) VALUES (
                '$user_id','$pet_id','$service',
                '$start','$end',
                '$price_per_day','$total_days','$total_amount',
                '$payment_type','GCash','$reference_number',
                '$down_payment_amount','$balance_amount',
                '$down_payment_filename','$full_payment_filename',
                'Reserved','$payment_status'
            )
        ");

        $success = "Booking submitted successfully and is now reserved.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Service | Dakimomo</title>

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
      <li class="nav-item"><a class="nav-link" href="my-bookings.php">Bookings</a></li>
      <li class="nav-item"><a class="nav-link" href="chat.php">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4 w-50">

    <h3 class="section-title mb-3">Book a Service</h3>

    <?php if ($error) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>

    <?php if ($success) { ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm mb-5">

        <!-- PET -->
        <div class="mb-3">
            <label>Pet</label>
            <select name="pet_id" class="form-control" required id="petSelect">
                <option disabled selected>Select Pet</option>
                <?php
                $pets = mysqli_query($conn,"
                    SELECT pet_id, pet_name, pet_size
                    FROM pets WHERE user_id='$user_id'
                ");
                while ($p = mysqli_fetch_assoc($pets)) {
                    echo "<option value='{$p['pet_id']}' data-size='{$p['pet_size']}'>
                            {$p['pet_name']} ({$p['pet_size']})
                        </option>";
                }
                ?>
            </select>
        </div>

        <!-- SERVICE -->
        <div class="mb-3">
            <label>Service</label>
            <select name="service_type" class="form-control" required>
                <option disabled selected>Select Service</option>
                <option>Pet Sitting</option>
                <option>Pet Hotel</option>
            </select>
        </div>

        <!-- START DATE -->
        <div class="mb-3">
            <label>Start Date</label>
            <input type="date"
                   name="booking_date"
                   class="form-control"
                   min="<?= $tomorrow; ?>"
                   required>
        </div>

        <!-- END DATE -->
        <div class="">
            <label>End Date</label>
            <input type="date"
                   name="end_date"
                   class="form-control"
                   min="<?= $tomorrow; ?>">
            <small class="text-muted">Leave empty for single-day booking.</small>
        </div>

        <!-- PAYMENT METHOD -->
            <div class="mb-3">
                <label class="fw-semibold">Payment Method</label>
                <input type="text"
                    class="form-control"
                    value="GCash"
                    readonly>
                <input type="hidden" name="payment_method" value="GCash">
            </div>

            <!-- GCASH QR -->
            <div class="mb-3 text-center">
                <label class="fw-semibold mb-2">Scan to Pay (GCash)</label><br>

                <img src="../assets/images/gcash-qr.png"
                    alt="GCash QR Code"
                    class="img-fluid border rounded"
                    style="max-width:220px">

                <p class="small text-muted mt-2">
                    Scan the QR code using GCash and enter the reference number below.
                </p>
            </div>

            <!-- REFERENCE NUMBER -->
            <div class="mb-3">
                <label class="fw-semibold">GCash Reference Number</label>
                <input type="text"
                    name="reference_number"
                    class="form-control"
                    placeholder="Enter GCash reference number"
                    required>
            </div>

        <!-- PROOF OF PAYMENT -->
        <div class="mb-3 mt-3">
            <label class="fw-semibold">Upload Proof of Payment</label>

            <!-- DOWN PAYMENT -->
            <input type="file"
                name="down_payment_proof"
                id="downPaymentProof"
                class="form-control mb-2"
                accept="image/*,.pdf">

            <!-- FULL / REMAINING PAYMENT -->
            <input type="file"
                name="payment_proof"
                id="fullPaymentProof"
                class="form-control d-none"
                accept="image/*,.pdf">

            <small class="text-muted">
                Upload proof based on selected payment option. <br>
                The downpayment is non-refundable.
            </small>
        </div>

        <!-- PAYMENT OPTION -->
        <div class="mb-3 mt-2">
            <label class="fw-semibold">Payment Option</label>

            <div class="form-check">
                <input class="form-check-input"
                    type="radio"
                    name="payment_type"
                    id="payDown"
                    value="Down Payment"
                    checked>
                <label class="form-check-label" for="payDown">
                    30% Down Payment (Reservation)
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input"
                    type="radio"
                    name="payment_type"
                    id="payFull"
                    value="Full Payment">
                <label class="form-check-label" for="payFull">
                    Full Payment
                </label>
            </div>
        </div>

        <div id="bookingSummary" class="card mt-4 d-none">
            <div class="card-body">
                <h6 class="mb-3">Payment Summary</h6>

                <div class="row mb-2">
                    <div class="col-6 text-muted">Total Cost</div>
                    <div class="col-6 text-end fw-bold">
                        ₱<span id="totalCost">0</span>
                    </div>
                </div>

                <div class="row mb-2" id="downRow">
                    <div class="col-6 text-muted">Down Payment (30%)</div>
                    <div class="col-6 text-end fw-bold text-primary">
                        ₱<span id="downPayment">0</span>
                    </div>
                </div>

                <div class="row mb-3" id="balanceRow">
                    <div class="col-6 text-muted">Remaining Balance</div>
                    <div class="col-6 text-end fw-bold text-success">
                        ₱<span id="balance">0</span>
                    </div>
                </div>

                <div id="availabilityBox"></div>
            </div>
        </div>

        <button type="submit" name="book_service" class="btn btn-brown w-100">
            Submit Booking
        </button>

    </form>
</div>

<script>
const serviceInput = document.querySelector('[name="service_type"]');
const startInput   = document.querySelector('[name="booking_date"]');
const endInput     = document.querySelector('[name="end_date"]');
const petSelect    = document.querySelector('[name="pet_id"]');
const paymentRadios= document.querySelectorAll('[name="payment_type"]');
const submitBtn    = document.querySelector('[name="book_service"]');

const summaryCard  = document.getElementById('bookingSummary');
const totalCostEl  = document.getElementById('totalCost');
const downEl       = document.getElementById('downPayment');
const balanceEl    = document.getElementById('balance');
const availabilityBox = document.getElementById('availabilityBox');

const downProof = document.getElementById('downPaymentProof');
const fullProof = document.getElementById('fullPaymentProof');

const downRow = document.getElementById('downRow');
const balanceRow = document.getElementById('balanceRow');

function toggleProofInput() {
    const selected = document.querySelector('[name="payment_type"]:checked').value;

    if (selected === 'Down Payment') {
        downProof.classList.remove('d-none');
        fullProof.classList.add('d-none');
    } else {
        fullProof.classList.remove('d-none');
        downProof.classList.add('d-none');
    }

    // Require correct file BEFORE submit
    validateSubmit();
}

// payment type change
paymentRadios.forEach(r =>
    r.addEventListener('change', toggleProofInput)
);

// file change validation
downProof.addEventListener('change', () => {
    if (!downProof.classList.contains('d-none')) {
        submitBtn.disabled = !downProof.files.length;
    }
});

fullProof.addEventListener('change', () => {
    if (!fullProof.classList.contains('d-none')) {
        submitBtn.disabled = !fullProof.files.length;
    }
});

const PET_RATES = {
    'Pet Sitting': { default: 350 },
    'Pet Hotel': {
        Small: 350,
        Medium: 450,
        Large: 550
    }
};

function getPetSize() {
    const opt = petSelect.selectedOptions[0];
    return opt ? opt.dataset.size : null;
}

function computeDays(start, end) {
    const s = new Date(start);
    const e = new Date(end || start);
    return Math.floor((e - s) / 86400000) + 1;
}

function updateSummary() {

    const service = serviceInput.value;
    const start   = startInput.value;
    const end     = endInput.value || start;
    const petSize = getPetSize();

    if (!service || !start || !petSelect.value) {
        summaryCard.classList.add('d-none');
        submitBtn.disabled = true;
        return;
    }

    let price = 0;
    if (service === 'Pet Sitting') {
        price = PET_RATES['Pet Sitting'].default;
    } else if (petSize) {
        price = PET_RATES['Pet Hotel'][petSize];
    }

    const days  = computeDays(start, end);
    const total = price * days;
    const down  = total * 0.30;
    const bal   = total - down;

    totalCostEl.textContent = total.toLocaleString();
    downEl.textContent      = down.toLocaleString();
    balanceEl.textContent   = bal.toLocaleString();

    summaryCard.classList.remove('d-none');

    const selectedPayment = document.querySelector('[name="payment_type"]:checked').value;

    if (selectedPayment === 'Full Payment') {
        downRow.classList.add('d-none');
        balanceRow.classList.add('d-none');
    } else {
        downRow.classList.remove('d-none');
        balanceRow.classList.remove('d-none');
    }

    /* SLOT CHECK (VISIBLE & CRITICAL) */
    fetch(`check-slots.php?service=${service}&start=${start}&end=${end}`)
        .then(res => res.json())
        .then(data => {

            let hasFull = false;
            let html = `
                <hr>
                <h6 class="mb-2">Availability per Day</h6>
                <ul class="list-group list-group-flush">
            `;

            data.forEach(d => {

                let badge = 'success';
                let label = 'Available';

                if (d.status === 'full') {
                    badge = 'danger';
                    label = 'FULL';
                    hasFull = true;
                } else if (d.status === 'limited') {
                    badge = 'warning';
                    label = 'Limited';
                }

                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${d.date}
                        <span>
                            <span class="badge bg-${badge} me-2">${label}</span>
                            <small class="text-muted">${d.remaining} slot(s) left</small>
                        </span>
                    </li>
                `;
            });

            html += '</ul>';
            availabilityBox.innerHTML = html;

            submitBtn.disabled = hasFull;
        });
}

[serviceInput, startInput, endInput, petSelect].forEach(el =>
    el.addEventListener('change', updateSummary)
);

paymentRadios.forEach(r =>
    r.addEventListener('change', updateSummary)
);

</script>


</body>
</html>