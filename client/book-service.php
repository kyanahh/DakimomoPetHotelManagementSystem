<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch pets of logged-in user
$pets = mysqli_query($conn, "SELECT * FROM pets WHERE user_id='$user_id'");

if (isset($_POST['book_service'])) {

    $pet_id = $_POST['pet_id'];
    $service = $_POST['service_type'];
    $date = $_POST['booking_date'];
    $time = $_POST['time_slot'];

    // Smart availability check (max 5 bookings per slot)
    $check = "SELECT COUNT(*) AS total FROM bookings 
              WHERE booking_date='$date' 
              AND service_type='$service'
              AND time_slot='$time'
              AND status!='Rejected'";

    $result = mysqli_query($conn, $check);
    $row = mysqli_fetch_assoc($result);

    if ($row['total'] >= 5) {
        $error = "Selected slot is already full. Please choose another schedule.";
    } else {

        $insert = "INSERT INTO bookings (user_id, pet_id, service_type, booking_date, time_slot)
                   VALUES ('$user_id','$pet_id','$service','$date','$time')";

        if (mysqli_query($conn, $insert)) {
            $success = "Booking submitted successfully. Waiting for approval.";
        } else {
            $error = "Booking failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container section">
    <h3 class="section-title mb-3">Book a Service</h3>

    <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>

    <?php if (isset($success)) { ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php } ?>

    <form method="POST" class="card p-4 shadow-sm">

        <div class="mb-3">
            <label>Pet</label>
            <select name="pet_id" class="form-control" required>
                <?php while ($pet = mysqli_fetch_assoc($pets)) { ?>
                    <option value="<?= $pet['pet_id']; ?>">
                        <?= $pet['pet_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Service</label>
            <select name="service_type" class="form-control" required>
                <option>Pet Sitting</option>
                <option>Pet Hotel</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="booking_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Time Slot</label>
            <select name="time_slot" class="form-control" required>
                <option>Morning</option>
                <option>Afternoon</option>
            </select>
        </div>

        <button type="submit" name="book_service" class="btn btn-brown w-100">
            Submit Booking
        </button>

    </form>
</div>

</body>
</html>