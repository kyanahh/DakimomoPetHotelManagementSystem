<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Care Status | Staff</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include 'includes/sidebar.php'; ?>

    <div class="admin-content">

        <h2 class="section-title mb-4">Update Pet Care Status</h2>

        <div class="card shadow-sm p-4">
            <form>
                <div class="mb-3">
                    <label>Select Pet</label>
                    <select class="form-control">
                        <option>Browny</option>
                        <option>Snow</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Status</label>
                    <select class="form-control">
                        <option>Fed</option>
                        <option>Walked</option>
                        <option>Resting</option>
                        <option>Under Observation</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Remarks</label>
                    <textarea class="form-control" rows="3" placeholder="Optional remarks"></textarea>
                </div>

                <button class="btn btn-brown w-100">Update Status</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>