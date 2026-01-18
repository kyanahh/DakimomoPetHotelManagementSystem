<?php
include '../includes/auth.php';
include '../includes/db.php';

// Approve booking
if (isset($_GET['approve'])) {
    mysqli_query($conn, "UPDATE bookings SET status='Approved' WHERE booking_id=".$_GET['approve']);
    header("Location: bookings.php");
    exit();
}

// Reject booking
if (isset($_GET['reject'])) {
    mysqli_query($conn, "UPDATE bookings SET status='Rejected' WHERE booking_id=".$_GET['reject']);
    header("Location: bookings.php");
    exit();
}

// Fetch all bookings
$bookings = mysqli_query($conn, "
    SELECT b.*, p.pet_name, u.full_name
    FROM bookings b
    JOIN pets p ON b.pet_id = p.pet_id
    JOIN users u ON b.user_id = u.user_id
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container section">
    <h3 class="section-title mb-3">Manage Bookings</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Client</th>
                <th>Pet</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($b = mysqli_fetch_assoc($bookings)) { ?>
            <tr>
                <td><?= $b['full_name']; ?></td>
                <td><?= $b['pet_name']; ?></td>
                <td><?= $b['service_type']; ?></td>
                <td><?= $b['booking_date']; ?></td>
                <td><?= $b['time_slot']; ?></td>
                <td><?= $b['status']; ?></td>
                <td>
                    <?php if ($b['status'] == 'Pending') { ?>
                        <a href="?approve=<?= $b['booking_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                        <a href="?reject=<?= $b['booking_id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
</div>

</body>
</html>