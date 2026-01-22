<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ==========================
   SUMMARY QUERIES
========================== */
$totalIncome = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT SUM(total_amount) AS total
    FROM bookings
    WHERE payment_status='Fully Paid'
"));

$gcashIncome = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT SUM(total_amount) AS total
    FROM bookings
    WHERE payment_status='Fully Paid' AND payment_method='GCash'
"));

$cashIncome = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT SUM(total_amount) AS total
    FROM bookings
    WHERE payment_status='Fully Paid' AND payment_method='Cash'
"));

$monthlyIncome = mysqli_query($conn,"
    SELECT DATE_FORMAT(date_created,'%b %Y') AS month,
           SUM(total_amount) AS total
    FROM bookings
    WHERE payment_status='Fully Paid'
    GROUP BY month
    ORDER BY date_created ASC
");

$payments = mysqli_query($conn,"
    SELECT b.booking_id, b.service_type, b.payment_method,
           b.reference_number, b.total_amount, b.date_created,
           u.full_name, p.pet_name
    FROM bookings b
    JOIN users u ON b.user_id=u.user_id
    JOIN pets p ON b.pet_id=p.pet_id
    WHERE b.payment_status='Fully Paid'
    ORDER BY b.date_created DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payments & Income | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.stat-card {
    border-left: 5px solid;
}
.stat-total { border-color: #6c757d; }
.stat-gcash { border-color: #198754; }
.stat-cash  { border-color: #0d6efd; }
</style>

</head>
<body>

<div class="admin-wrapper">
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<h3 class="section-title mb-4">Payments & Income</h3>

<!-- ================= SUMMARY CARDS ================= -->
<div class="row g-3 mb-4">

<div class="col-md-4">
<div class="card shadow-sm h-100 stat-card stat-total">
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div>
            <small class="text-muted">Total Income</small>
            <h4 class="fw-bold mt-1">
                ₱<?= number_format($totalIncome['total'] ?? 0,2); ?>
            </h4>
        </div>
        <div class="fs-1 text-muted">💰</div>
    </div>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow-sm h-100 stat-card stat-gcash">
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div>
            <small class="text-muted">GCash Income</small>
            <h4 class="fw-bold mt-1 text-success">
                ₱<?= number_format($gcashIncome['total'] ?? 0,2); ?>
            </h4>
        </div>
        <div class="fs-1">📱</div>
    </div>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow-sm h-100 stat-card stat-cash">
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div>
            <small class="text-muted">Cash Income</small>
            <h4 class="fw-bold mt-1 text-primary">
                ₱<?= number_format($cashIncome['total'] ?? 0,2); ?>
            </h4>
        </div>
        <div class="fs-1">💵</div>
    </div>
</div>
</div>
</div>

</div>

<!-- ================= CHARTS ================= -->
<div class="row g-4 mb-5">

<div class="col-md-7">
<div class="card shadow-sm h-100">
<div class="card-body">
<h6 class="fw-bold mb-3">📈 Monthly Income</h6>
<canvas id="monthlyChart" height="120"></canvas>
</div>
</div>
</div>

<div class="col-md-5">
<div class="card shadow-sm h-100">
<div class="card-body">
<h6 class="fw-bold mb-3">💳 Payment Method Breakdown</h6>
<canvas id="methodChart" height="120"></canvas>
</div>
</div>
</div>

</div>

<!-- ================= PAYMENT HISTORY ================= -->
<div class="card shadow-sm">
<div class="card-body p-0">

<h6 class="fw-bold p-3 mb-0">Payment History</h6>

<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead class="table-light sticky-top">
<tr>
    <th>#</th>
    <th>Client</th>
    <th>Pet</th>
    <th>Service</th>
    <th>Method</th>
    <th>Reference</th>
    <th>Amount</th>
    <th>Date</th>
</tr>
</thead>
<tbody>

<?php if (mysqli_num_rows($payments) > 0) { ?>
<?php while ($row = mysqli_fetch_assoc($payments)) { ?>
<tr>
<td>#<?= $row['booking_id']; ?></td>
<td><?= htmlspecialchars($row['full_name']); ?></td>
<td><?= htmlspecialchars($row['pet_name']); ?></td>
<td><?= htmlspecialchars($row['service_type']); ?></td>
<td>
<span class="badge bg-<?= $row['payment_method']==='GCash'?'success':'primary'; ?>">
<?= $row['payment_method']; ?>
</span>
</td>
<td><?= $row['reference_number'] ?? '—'; ?></td>
<td class="fw-bold">₱<?= number_format($row['total_amount'],2); ?></td>
<td><?= date("M d, Y", strtotime($row['date_created'])); ?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="8" class="text-center text-muted py-4">
No completed payments recorded.
</td>
</tr>
<?php } ?>

</tbody>
</table>
</div>

</div>
</div>

</div>
</div>

<!-- ================= CHART SCRIPTS ================= -->
<script>
const labels = [];
const values = [];

<?php
mysqli_data_seek($monthlyIncome,0);
while ($m = mysqli_fetch_assoc($monthlyIncome)) {
    echo "labels.push('{$m['month']}');";
    echo "values.push({$m['total']});";
}
?>

new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Income',
            data: values,
            fill: true,
            tension: 0.4
        }]
    }
});

new Chart(document.getElementById('methodChart'), {
    type: 'doughnut',
    data: {
        labels: ['GCash','Cash'],
        datasets: [{
            data: [
                <?= $gcashIncome['total'] ?? 0 ?>,
                <?= $cashIncome['total'] ?? 0 ?>
            ]
        }]
    }
});
</script>

</body>
</html>