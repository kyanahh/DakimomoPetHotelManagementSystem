<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dakimomo Pet Sitting & Boarding</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="assets/css/style.css">

<style>
/* HERO SECTION */
.hero {
    background: linear-gradient(
        rgba(0,0,0,0.55),
        rgba(0,0,0,0.55)
    ), url('assets/images/tile.jpg') center/cover no-repeat;
    min-height: 85vh;
    display: flex;
    align-items: center;
    color: #fff;
}

.hero h1 {
    font-size: 3rem;
    font-weight: 700;
}

.hero p {
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

/* SERVICE CARDS */
.service-card img {
    height: 220px;
    object-fit: cover;
}

.service-card h5 {
    font-weight: 600;
}

.service-card p {
    font-size: 0.95rem;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white border-bottom">
<div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="assets/images/logo.png" height="45" class="me-2" alt="Dakimomo Logo">
        <strong>Dakimomo</strong>
    </a>

    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
            <li class="nav-item"><a class="nav-link" href="#">About</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            <li class="nav-item ms-3">
                <a class="btn btn-brown" href="login.php">Book Now</a>
            </li>
        </ul>
    </div>
</div>
</nav>

<!-- HERO -->
<section class="hero text-center">
<div class="container">
    <h1 class="mb-3">Safe & Loving Care for Your Pets</h1>
    <p class="mb-4">
        A trusted pet sitting and boarding service designed for comfort,
        safety, and peace of mind.
    </p>

    <a href="register.php" class="btn btn-brown px-4 py-2">
        Get Started
    </a>
</div>
</section>

<!-- SERVICES -->
<section class="section py-5 bg-light">
<div class="container">

    <h2 class="section-title text-center mb-5">Our Services</h2>

    <div class="row g-4">

        <!-- PET SITTING -->
        <div class="col-md-4">
            <div class="card shadow-sm service-card h-100">
                <img src="assets/images/dashboard-pets.jpg" class="card-img-top" alt="Pet Sitting">
                <div class="card-body text-center">
                    <h5>Pet Sitting</h5>
                    <p>
                        Personalized daily care, feeding, and companionship
                        while you’re away.
                    </p>
                </div>
            </div>
        </div>

        <!-- PET HOTEL -->
        <div class="col-md-4">
            <div class="card shadow-sm service-card h-100">
                <img src="assets/images/dashboard-booking.jfif" class="card-img-top" alt="Pet Hotel">
                <div class="card-body text-center">
                    <h5>Pet Hotel</h5>
                    <p>
                        Comfortable and secure overnight stays in a clean,
                        pet-friendly environment.
                    </p>
                </div>
            </div>
        </div>

        <!-- SMART BOOKING -->
        <div class="col-md-4">
            <div class="card shadow-sm service-card h-100">
                <img src="assets/images/dashboard-chat.jfif" class="card-img-top" alt="Smart Booking">
                <div class="card-body text-center">
                    <h5>Smart Booking</h5>
                    <p>
                        Easy online scheduling with real-time booking
                        and instant confirmations.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
</section>

<!-- FOOTER -->
<footer class="py-4 border-top bg-white">
<div class="container text-center">
    <p class="mb-0">
        &copy; 2026 Dakimomo Pet Sitting & Boarding. All rights reserved.
    </p>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>