<?php
include '../includes/auth.php';
include '../includes/db.php';

$id = (int)$_GET['id'];

$booking = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM bookings WHERE booking_id=$id"
));

if (isset($_POST['update_booking'])) {

    $start = $_POST['start_date'];
    $end   = $_POST['end_date'] ?: $start;

    /* SLOT CHECK */
    $current = $start;
    while ($current <= $end) {

        $check = mysqli_query($conn, "
            SELECT COUNT(*) AS total
            FROM bookings
            WHERE service_type='{$booking['service_type']}'
            AND status!='Rejected'
            AND booking_id!=$id
            AND '$current' BETWEEN start_date AND end_date
        ");

        $row = mysqli_fetch_assoc($check);
        if ($row['total'] >= 5) {
            die("Slot full on $current");
        }

        $current = date('Y-m-d', strtotime($current.' +1 day'));
    }

    /* RECOMPUTE */
    $days = (strtotime($end)-strtotime($start))/(60*60*24)+1;
    $total = $days * $booking['price_per_day'];

    mysqli_query($conn, "
        UPDATE bookings SET
        start_date='$start',
        end_date='$end',
        total_days='$days',
        total_amount='$total',
        payment_status='Unpaid',
        status='Approved'
        WHERE booking_id=$id
    ");

    header("Location: bookings.php?toast=updated");
}
?>