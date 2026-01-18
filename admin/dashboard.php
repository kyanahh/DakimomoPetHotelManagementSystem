<?php include '../includes/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include 'includes/sidebar.php'; ?>

    <div class="admin-content">

        <h2 class="section-title mb-4">Dashboard</h2>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <h6>Total Bookings</h6>
                    <h3>25</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <h6>Pending Requests</h6>
                    <h3>6</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <h6>Registered Pets</h6>
                    <h3>18</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <h6>Active Clients</h6>
                    <h3>12</h3>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>