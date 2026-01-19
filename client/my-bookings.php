<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "
    SELECT b.*, p.pet_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    WHERE b.user_id='$user_id'
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | Dakimomo</title>

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
      <li class="nav-item"><a class="nav-link active" href="my-bookings.php">Bookings</a></li>
      <li class="nav-item"><a class="nav-link" href="../chat/chat.php">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

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
                        <th>Pet</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['pet_name']); ?></td>
                            <td><?= htmlspecialchars($row['service_type']); ?></td>
                            <td><?= date("M d, Y", strtotime($row['booking_date'])); ?></td>
                            <td>
                                <?php if ($row['status'] === 'Pending') { ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php } elseif ($row['status'] === 'Approved') { ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No bookings found. You can start by booking a service.
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>