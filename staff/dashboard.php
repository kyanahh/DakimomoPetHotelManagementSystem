<?php include '../includes/auth.php'; ?>

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

    <div class="admin-content">

        <h2 class="section-title mb-4">Staff Dashboard</h2>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h6>Assigned Bookings</h6>
                    <h3>5</h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h6>Pets Under Care</h6>
                    <h3>4</h3>
                </div>
            </div>
        </div>

        <p>View your assigned bookings and update pet care status.</p>

    </div>
</div>

</body>
</html>