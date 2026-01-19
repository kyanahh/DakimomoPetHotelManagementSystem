<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch latest 3 bookings of the user
$recentBookings = mysqli_query($conn, "
    SELECT b.*, p.pet_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    WHERE b.user_id = '$user_id'
    ORDER BY b.booking_date DESC
    LIMIT 3
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Dakimomo</title>

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
      <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="pets.php">My Pets</a></li>
      <li class="nav-item"><a class="nav-link" href="my-bookings.php">Bookings</a></li>
      <li class="nav-item"><a class="nav-link" href="chat.php">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container mt-5">

    <!-- WELCOME -->
    <div class="mb-4">
        <h2 class="section-title mb-1">Welcome back 👋</h2>
        <p class="text-muted">Manage your pets, bookings, and messages in one place.</p>
    </div>

    <!-- ACTION CARDS -->
    <div class="row g-4 mb-5">

        <!-- BOOK SERVICE -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <img src="../assets/images/dashboard-booking.jfif"
                     class="card-img-top"
                     style="height:180px; object-fit:cover;"
                     alt="Book Service">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold">Book a Service</h5>
                    <p class="card-text text-muted">
                        Schedule pet sitting or hotel services easily.
                    </p>
                    <a href="book-service.php" class="btn btn-brown w-100">
                        Book Now
                    </a>
                </div>
            </div>
        </div>

        <!-- MY PETS -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <img src="../assets/images/dashboard-pets.jpg"
                     class="card-img-top"
                     style="height:180px; object-fit:cover;"
                     alt="My Pets">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold">My Pets</h5>
                    <p class="card-text text-muted">
                        View and manage your pet profiles.
                    </p>
                    <a href="pets.php" class="btn btn-brown w-100">
                        View Pets
                    </a>
                </div>
            </div>
        </div>

        <!-- MESSAGES -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <img src="../assets/images/dashboard-chat.jfif"
                     class="card-img-top"
                     style="height:180px; object-fit:cover;"
                     alt="Messages">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold">Messages</h5>
                    <p class="card-text text-muted">
                        Chat with staff for updates and concerns.
                    </p>
                    <a href="../chat/chat.php" class="btn btn-brown w-100">
                        Open Chat
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- RECENT BOOKINGS -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="section-title mb-0">Recent Bookings</h4>
            <a href="my-bookings.php" class="btn btn-dark btn-sm">
                View All Bookings
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Pet</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (mysqli_num_rows($recentBookings) > 0) { ?>
                            <?php while ($row = mysqli_fetch_assoc($recentBookings)) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['pet_name']); ?></td>
                                    <td><?= htmlspecialchars($row['service_type']); ?></td>
                                    <td><?= date("M d, Y", strtotime($row['booking_date'])); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'Pending') { ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php } elseif ($row['status'] == 'Approved') { ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php } else { ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No bookings found.
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

</body>
</html>