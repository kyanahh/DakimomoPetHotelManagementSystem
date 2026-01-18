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
<html>
<head>
    <title>My Bookings</title>
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
      <li class="nav-item"><a class="nav-link" href="#">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container section">
    <h3 class="section-title mb-3">My Bookings</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Pet</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['pet_name']; ?></td>
                <td><?= $row['service_type']; ?></td>
                <td><?= $row['booking_date']; ?></td>
                <td><?= $row['time_slot']; ?></td>
                <td><?= $row['status']; ?></td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
</div>

</body>
</html>