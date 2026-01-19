<?php
include '../includes/auth.php';
include '../includes/db.php';

// ADMIN ONLY
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// DATE FILTER
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to'] ?? date('Y-m-d');

// DAILY BOOKINGS
$daily = mysqli_query($conn, "
    SELECT booking_date, COUNT(*) AS total
    FROM bookings
    WHERE booking_date BETWEEN '$from' AND '$to'
    GROUP BY booking_date
");

// SERVICE USAGE
$services = mysqli_query($conn, "
    SELECT service_type, COUNT(*) AS total
    FROM bookings
    WHERE booking_date BETWEEN '$from' AND '$to'
    GROUP BY service_type
");

// STATUS SUMMARY
$status = mysqli_query($conn, "
    SELECT status, COUNT(*) AS total
    FROM bookings
    WHERE booking_date BETWEEN '$from' AND '$to'
    GROUP BY status
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports | Dakimomo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20px;
    }
    .no-print {
        display: none !important;
    }
    table {
        font-size: 12px;
    }
}
</style>
</head>

<body>

<div class="admin-wrapper">

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h3 class="section-title">Reports</h3>
</div>

<!-- FILTER -->
<form method="GET" class="row g-3 mb-4 no-print">
    <div class="col-md-4">
        <label>From</label>
        <input type="date" name="from" class="form-control" value="<?= $from ?>" required>
    </div>
    <div class="col-md-4">
        <label>To</label>
        <input type="date" name="to" class="form-control" value="<?= $to ?>" required>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <button class="btn btn-brown me-2">Generate</button>
        <button type="button" onclick="printReport()" class="btn btn-dark">
            🖨 Print
        </button>
    </div>
</form>

<!-- PRINTABLE AREA -->
<div id="printArea">

<!-- REPORT HEADER -->
<div class="text-center mb-4">
    <img src="../assets/images/logo.png" height="60" class="mb-2">
    <h4 class="fw-bold mb-0">DAKIMOMO PET SITTING & BOARDING</h4>
    <small>Official Business Report</small><br>
    <small>
        Period: <?= date("M d, Y", strtotime($from)) ?> –
        <?= date("M d, Y", strtotime($to)) ?>
    </small>
    <hr>
</div>

<!-- DAILY BOOKINGS -->
<h5 class="fw-bold mb-2">📅 Daily Booking Report</h5>
<table class="table table-bordered mb-4">
<thead class="table-light">
<tr>
    <th>Date</th>
    <th>Total Bookings</th>
</tr>
</thead>
<tbody>
<?php while ($d = mysqli_fetch_assoc($daily)) { ?>
<tr>
    <td><?= date("M d, Y", strtotime($d['booking_date'])) ?></td>
    <td><?= $d['total'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<!-- SERVICE USAGE -->
<h5 class="fw-bold mb-2">🐾 Service Usage Summary</h5>
<table class="table table-bordered mb-4">
<thead class="table-light">
<tr>
    <th>Service</th>
    <th>Total Usage</th>
</tr>
</thead>
<tbody>
<?php while ($s = mysqli_fetch_assoc($services)) { ?>
<tr>
    <td><?= htmlspecialchars($s['service_type']) ?></td>
    <td><?= $s['total'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<!-- STATUS SUMMARY -->
<h5 class="fw-bold mb-2">📊 Booking Status Summary</h5>
<table class="table table-bordered mb-4">
<thead class="table-light">
<tr>
    <th>Status</th>
    <th>Total</th>
</tr>
</thead>
<tbody>
<?php while ($st = mysqli_fetch_assoc($status)) { ?>
<tr>
    <td><?= htmlspecialchars($st['status']) ?></td>
    <td><?= $st['total'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<!-- SIGNATURE -->
<div class="mt-5">
    <p>Prepared by:</p>
    <br>
    <strong>______________________________</strong><br>
    <small>Administrator</small>
</div>

</div> <!-- END PRINT AREA -->

</div>
</div>

<script>
function printReport() {
    window.print();
}
</script>

</body>
</html>