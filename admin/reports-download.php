<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') exit();

$from = $_GET['from'];
$to   = $_GET['to'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="dakimomo-bookings-report.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['Client', 'Pet', 'Service', 'Date', 'Status']);

$result = mysqli_query($conn, "
    SELECT u.full_name, p.pet_name, b.service_type, b.booking_date, b.status
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN pets p ON b.pet_id = p.pet_id
    WHERE b.booking_date BETWEEN '$from' AND '$to'
");

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit();