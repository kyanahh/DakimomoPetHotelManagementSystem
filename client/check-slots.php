<?php
include '../includes/db.php';

$service = $_GET['service'] ?? null;
$start   = $_GET['start'] ?? null;
$end     = $_GET['end'] ?? null;

if (!$service || !$start || !$end) {
    echo json_encode([]);
    exit();
}

/* SLOT LIMITS */
$slot_limits = [
    'Pet Sitting' => 5,
    'Pet Hotel'   => 5
];

$max_slots = $slot_limits[$service] ?? 5;

$response = [];

$current = $start;
while ($current <= $end) {

    // COUNT how many bookings cover THIS date
    $query = "
        SELECT COUNT(*) AS total
        FROM bookings
        WHERE service_type = '$service'
        AND status != 'Rejected'
        AND start_date <= '$current'
        AND end_date >= '$current'
    ";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    $used = (int)$row['total'];
    $remaining = $max_slots - $used;

    if ($remaining <= 0) {
        $status = 'full';
    } elseif ($remaining <= 2) {
        $status = 'limited';
    } else {
        $status = 'available';
    }

    $response[] = [
        'date' => $current,
        'used' => $used,
        'remaining' => max(0, $remaining),
        'status' => $status
    ];

    $current = date('Y-m-d', strtotime($current . ' +1 day'));
}

header('Content-Type: application/json');
echo json_encode($response);