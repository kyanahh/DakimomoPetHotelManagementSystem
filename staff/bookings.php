<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | Staff</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include 'includes/sidebar.php'; ?>

    <div class="admin-content">

        <h2 class="section-title mb-4">My Bookings</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pet</th>
                            <th>Owner</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Browny</td>
                            <td>John Doe</td>
                            <td>Pet Hotel</td>
                            <td>Jan 20, 2026</td>
                            <td>Feed twice daily</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>