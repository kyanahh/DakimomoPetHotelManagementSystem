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
<nav class="navbar navbar-expand-lg bg-white border-bottom fixed-top">
<div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="assets/images/logo.png" height="45" class="me-2" alt="Dakimomo Logo">
        <strong>Dakimomo</strong>
    </a>

    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
            <li class="nav-item">
            <a class="nav-link" href="#services">Services</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="#about">About</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="#contact">Contact</a>
            </li>
            <li class="nav-item ms-3">
                <a class="btn btn-brown" href="login.php">Book Now</a>
            </li>
        </ul>
    </div>
</div>
</nav>

<!-- HERO -->
<section class="hero text-center mt-5" id="home">
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
<section class="section py-5 bg-light" >
<div class="container" id="services">

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

<!-- ABOUT -->
<section class="section py-5" id="about">
<div class="container">

    <h2 class="section-title text-center mb-4">About Dakimomo</h2>

    <div class="row align-items-center g-4">

        <div class="col-md-6">
            <p>
                <strong>Dakimomo Pet Sitting and Boarding</strong> is a trusted
                local pet care service that provides safe, loving, and reliable
                care for pets while their owners are away. The business officially
                started operations in <strong>January 2025</strong> and is based
                in Muntinlupa City, Philippines.
            </p>

            <p>
                Dakimomo offers personalized pet sitting and pet hotel services
                designed to ensure comfort, security, and peace of mind for
                pet owners. Each pet is treated with care and attention, taking
                into consideration their individual needs, routines, and health
                conditions.
            </p>

            <p>
                Through the use of modern technology, Dakimomo continues to
                improve customer experience by providing smart booking,
                organized service management, and transparent communication.
            </p>
        </div>

        <!-- PRICE LIST IMAGE -->
        <div class="col-md-6 text-center">
            <h5 class="mb-3">Price List</h5>
            <img src="assets/images/dashboard-chat.jfif"
                 class="img-fluid rounded shadow-sm"
                 alt="Dakimomo Price List">
        </div>

    </div>
</div>
</section>

<!-- CONTACT -->
<section class="section py-5 bg-light" id="contact">
<div class="container">

    <h2 class="section-title text-center mb-5">Contact Us</h2>

    <div class="row g-4">

        <!-- CONTACT DETAILS -->
        <div class="col-md-5">
            <h5>Get in Touch</h5>

            <p class="mb-2">
                <strong>Email:</strong><br>
                dakimomo.businessemail@gmail.com
            </p>

            <p class="mb-2">
                <strong>Location:</strong><br>
                Multiland 1 Subdivision, Muntinlupa City
            </p>

            <p class="mb-2">
                <strong>Social Media:</strong><br>
                <a class="text-decoration-none" href="https://www.facebook.com/profile.php?id=61575965877626"
                   target="_blank">Facebook: Dakimomo - Pet Sitting & Pet Boarding</a><br>
                <a class="text-decoration-none"     href="https://www.instagram.com/dakimomopetsittingandboarding/"
                   target="_blank">Instagram: Dakimomo - Pet Sitting & Pet Boarding</a>
            </p>
        </div>

        <!-- MAP -->
        <div class="col-md-7">
            <div class="ratio ratio-4x3 shadow-sm rounded overflow-hidden">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3864.552053318043!2d121.04631182383754!3d14.395309682072352!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d058ecc0190d%3A0x7f1454e4da37fbd1!2sMultiland%201%20Subdivision%2C%20Muntinlupa%2C%201772%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1768891323426!5m2!1sen!2sph"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
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

<script>
document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function (e) {
        const targetId = this.getAttribute('href');
        const target = document.querySelector(targetId);

        if (!target) return;

        e.preventDefault();

        const startPosition = window.pageYOffset;
        const targetPosition =
            target.getBoundingClientRect().top + window.pageYOffset - 80;
        const distance = targetPosition - startPosition;
        const duration = 1400; // 👈 slow-mo speed (ms)

        let start = null;

        function animation(currentTime) {
            if (!start) start = currentTime;
            const timeElapsed = currentTime - start;

            const run = easeInOut(timeElapsed, startPosition, distance, duration);
            window.scrollTo(0, run);

            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }

        function easeInOut(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t + b;
            t--;
            return -c / 2 * (t * (t - 2) - 1) + b;
        }

        requestAnimationFrame(animation);
    });
});
</script>

</body>
</html>