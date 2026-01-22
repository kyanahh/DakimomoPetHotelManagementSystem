<?php
include '../includes/auth.php';

if ($_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Dashboard | Dakimomo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-wrapper">

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content px-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="section-title mb-1">Staff Dashboard</h2>
        <small class="text-muted">Manage daily pet care tasks and bookings</small>
    </div>
</div>

<!-- STATS CARDS -->
<div class="row g-4 mb-4">

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Assigned Bookings</h6>
                <h2 class="fw-bold mb-0">5</h2>
                <small class="text-muted">Active bookings today</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pets Under Care</h6>
                <h2 class="fw-bold mb-0">4</h2>
                <small class="text-muted">Currently checked-in pets</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pending Updates</h6>
                <h2 class="fw-bold mb-0">2</h2>
                <small class="text-muted">Pets needing status update</small>
            </div>
        </div>
    </div>

</div>

<!-- QUICK ACTIONS -->
<div class="row g-4 mb-4">

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                🐾 Quick Actions
            </div>
            <div class="card-body d-grid gap-2">
                <a href="bookings.php" class="btn btn-outline-dark">
                    View Assigned Bookings
                </a>
                <a href="pet-status.php" class="btn btn-brown">
                    Update Pet Care Status
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                📋 Staff Guidelines
            </div>
            <div class="card-body">
                <ul class="mb-0 small text-muted">
                    <li>Update pet status after feeding, walking, or checks</li>
                    <li>Ensure remarks are clear and professional</li>
                    <li>Report unusual pet behavior immediately</li>
                    <li>Completed bookings no longer need updates</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- FOOTER NOTE -->
<div class="alert alert-light border small">
    This dashboard is designed to help staff efficiently monitor pet care and daily responsibilities.
</div>

</div>
</div>

</body>
</html>