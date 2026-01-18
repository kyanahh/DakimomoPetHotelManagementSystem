<?php include '../includes/auth.php'; ?>

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
      <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="pets.php">My Pets</a></li>
      <li class="nav-item"><a class="nav-link" href="my-bookings.php">Bookings</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container section">

    <!-- WELCOME -->
    <div class="mb-5">
        <h2 class="section-title">Welcome, Pet Owner!</h2>
        <p>Manage your pets and bookings easily.</p>
    </div>

    <!-- ACTION CARDS -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Book a Service</h5>
                    <p>Schedule pet sitting or hotel services.</p>
                    <a href="#" class="btn btn-brown">Book Now</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>My Pets</h5>
                    <p>View and manage your pet profiles.</p>
                    <a href="pets.php" class="btn btn-brown">View Pets</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Messages</h5>
                    <p>Chat with staff for updates and inquiries.</p>
                    <a href="#" class="btn btn-brown">Open Chat</a>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT BOOKINGS -->
    <div>
        <h4 class="section-title mb-3">Recent Bookings</h4>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pet</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Browny</td>
                            <td>Pet Hotel</td>
                            <td>Jan 20, 2026</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                        </tr>
                        <tr>
                            <td>Snow</td>
                            <td>Pet Sitting</td>
                            <td>Jan 10, 2026</td>
                            <td><span class="badge bg-success">Approved</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>