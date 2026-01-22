<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: my-bookings.php");
    exit();
}

$booking_id = (int)$_GET['id'];
$user_id    = $_SESSION['user_id'];

/* VERIFY BOOKING OWNERSHIP */
$booking = mysqli_query($conn, "
    SELECT b.*, p.pet_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    WHERE b.booking_id='$booking_id'
    AND b.user_id='$user_id'
");

if (mysqli_num_rows($booking) === 0) {
    header("Location: my-bookings.php");
    exit();
}

$booking_data = mysqli_fetch_assoc($booking);

/* FETCH STAFF PET CARE UPDATES */
$updates = mysqli_query($conn, "
    SELECT *
    FROM pet_care_updates
    WHERE booking_id='$booking_id'
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pet Care Updates | Dakimomo</title>

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
        <li class="nav-item"><a class="nav-link" href="chat.php">Messages</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
</div>
</nav>

<div class="container mt-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="section-title mb-0">
            Care Updates for <?= htmlspecialchars($booking_data['pet_name']); ?>
        </h3>
        <small class="text-muted">
            <?= date("M d, Y", strtotime($booking_data['start_date'])); ?> –
            <?= date("M d, Y", strtotime($booking_data['end_date'])); ?>
        </small>
    </div>

    <a href="my-bookings.php" class="btn btn-sm btn-dark">← Back</a>
</div>

<hr>

<!-- UPDATES -->
<?php if (mysqli_num_rows($updates) > 0) { ?>

    <?php while ($u = mysqli_fetch_assoc($updates)) { ?>
        <div class="card shadow-sm mb-3">
            <div class="card-body">

                <span class="badge bg-primary mb-2 fs-6">
                    <?= htmlspecialchars($u['status_title']); ?>
                </span>

                <p class="mb-2">
                    <?= nl2br(htmlspecialchars($u['status_message'] ?: 'No additional remarks.')); ?>
                </p>

                <small class="text-muted">
                    <?= date("M d, Y • h:i A", strtotime($u['created_at'])); ?>
                </small>

            </div>
        </div>
    <?php } ?>

<?php } else { ?>

    <div class="alert alert-info text-center">
        🐾 No care updates yet.<br>
        Our staff will post updates as your pet is being cared for.
    </div>

<?php } ?>

</div>

</body>
</html>