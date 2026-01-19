<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Fetch pets of logged-in user
$pets = mysqli_query($conn, "SELECT * FROM pets WHERE user_id='$user_id'");

$error = null;
$success = null;
$remaining_slots = null;

if (isset($_POST['book_service'])) {

    $pet_id   = $_POST['pet_id'];
    $service  = $_POST['service_type'];
    $date     = $_POST['booking_date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $date;

    // ❌ No booking today or past dates
    if ($date < $tomorrow) {
        $error = "Bookings can only be made starting tomorrow.";
    } else {

        $max_slots = 5; // MAX PETS PER DAY
        $current_date = $date;
        $success_days = 0;

        while ($current_date <= $end_date) {

            // Check availability per day (NO time slot)
            $check = "SELECT COUNT(*) AS total 
                      FROM bookings 
                      WHERE booking_date='$current_date'
                      AND service_type='$service'
                      AND status!='Rejected'";

            $result = mysqli_query($conn, $check);
            $row = mysqli_fetch_assoc($result);

            $remaining_slots = $max_slots - $row['total'];

            if ($remaining_slots <= 0) {
                $error = "One or more selected dates are already fully booked.";
                break;
            }

            // Insert booking for each day
            mysqli_query($conn, "
                INSERT INTO bookings (user_id, pet_id, service_type, booking_date)
                VALUES ('$user_id','$pet_id','$service','$current_date')
            ");

            $success_days++;
            $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
        }

        if (!$error && $success_days > 0) {
            $success = "Booking submitted successfully for $success_days day(s). Waiting for approval.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Service | Dakimomo</title>

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
      <li class="nav-item"><a class="nav-link" href="../chat/chat.php">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4 w-50">

    <h3 class="section-title mb-3">Book a Service</h3>

    <?php if ($error) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>

    <?php if ($success) { ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php } ?>

    <form method="POST" class="card p-4 shadow-sm">

        <!-- PET -->
        <div class="mb-3">
            <label>Pet</label>
            <select name="pet_id" class="form-control" required>
                <?php while ($pet = mysqli_fetch_assoc($pets)) { ?>
                    <option value="<?= $pet['pet_id']; ?>">
                        <?= htmlspecialchars($pet['pet_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- SERVICE -->
        <div class="mb-3">
            <label>Service</label>
            <select name="service_type" class="form-control" required>
                <option>Pet Sitting</option>
                <option>Pet Hotel</option>
            </select>
        </div>

        <!-- START DATE -->
        <div class="mb-3">
            <label>Start Date</label>
            <input type="date"
                   name="booking_date"
                   class="form-control"
                   min="<?= $tomorrow; ?>"
                   required>
        </div>

        <!-- END DATE -->
        <div class="mb-3">
            <label>End Date (Optional)</label>
            <input type="date"
                   name="end_date"
                   class="form-control"
                   min="<?= $tomorrow; ?>">
            <small class="text-muted">Leave empty for single-day booking.</small>
        </div>

        <!-- SLOT INFO -->
        <?php if ($remaining_slots !== null) { ?>
            <div class="alert alert-info">
                <?= $remaining_slots > 0
                    ? "$remaining_slots slot(s) available for the selected date."
                    : "No available slots for the selected date." ?>
            </div>
        <?php } ?>

        <button type="submit" name="book_service" class="btn btn-brown w-100">
            Submit Booking
        </button>

    </form>
</div>

</body>
</html>