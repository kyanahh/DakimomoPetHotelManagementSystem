<?php
include '../includes/auth.php';
include '../includes/db.php';

// ADMIN ONLY
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ======================
   DASHBOARD COUNTS
====================== */
$totalBookings = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM bookings"))['total'];

$pendingBookings = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM bookings WHERE status='Pending'"))['total'];

$totalPets = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM pets"))['total'];

$activeClients = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM users WHERE role='client' AND status='active'"))['total'];

/* ======================
   BOOKINGS PER MONTH
====================== */
$monthlyQuery = mysqli_query($conn, "
    SELECT DATE_FORMAT(booking_date, '%b') AS month, COUNT(*) AS total
    FROM bookings
    WHERE YEAR(booking_date) = YEAR(CURDATE())
    GROUP BY MONTH(booking_date)
");

$months = [];
$monthlyTotals = [];

while ($row = mysqli_fetch_assoc($monthlyQuery)) {
    $months[] = $row['month'];
    $monthlyTotals[] = $row['total'];
}

/* ======================
   SERVICE DISTRIBUTION
====================== */
$serviceQuery = mysqli_query($conn, "
    SELECT service_type, COUNT(*) AS total
    FROM bookings
    GROUP BY service_type
");

$services = [];
$serviceTotals = [];

while ($row = mysqli_fetch_assoc($serviceQuery)) {
    $services[] = $row['service_type'];
    $serviceTotals[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Dakimomo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="admin-wrapper">

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">
<div class="container-fluid">

<h2 class="section-title mb-4">Dashboard</h2>

<!-- KPI CARDS -->
<div class="row g-4 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Bookings</h6>
                <h3 class="fw-bold"><?= $totalBookings ?></h3>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body">
                <h6 class="text-muted">Pending Requests</h6>
                <h3 class="fw-bold"><?= $pendingBookings ?></h3>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body">
                <h6 class="text-muted">Registered Pets</h6>
                <h3 class="fw-bold"><?= $totalPets ?></h3>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body">
                <h6 class="text-muted">Active Clients</h6>
                <h3 class="fw-bold"><?= $activeClients ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- CHARTS -->
<div class="row g-4">

    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">📈 Monthly Booking Trend</h6>
                <canvas id="bookingChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">🐾 Service Usage</h6>
                <canvas id="serviceChart"></canvas>
            </div>
        </div>
    </div>

</div>

</div>

</div>
</div>

<script>
// BOOKINGS LINE CHART
new Chart(document.getElementById('bookingChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Bookings',
            data: <?= json_encode($monthlyTotals) ?>,
            borderColor: '#6f4e37',
            backgroundColor: 'rgba(111, 78, 55, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});

// SERVICE PIE CHART
new Chart(document.getElementById('serviceChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($services) ?>,
        datasets: [{
            data: <?= json_encode($serviceTotals) ?>,
            backgroundColor: ['#6f4e37', '#d2b48c']
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>