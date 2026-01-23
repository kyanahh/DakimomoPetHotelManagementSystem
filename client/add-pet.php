<?php
include '../includes/auth.php';
include '../includes/db.php';

if (isset($_POST['save_pet'])) {

    $user_id  = $_SESSION['user_id'];
    $pet_name = $_POST['pet_name'];
    $pet_type = $_POST['pet_type'];
    $breed    = $_POST['breed'];
    $age_input = $_POST['age'];
    $age_unit  = $_POST['age_unit'];
    $pet_size  = $_POST['pet_size'];
    $medications  = $_POST['medications'];
    $gender  = $_POST['gender'];

    $age = ($age_unit === 'years') ? $age_input * 12 : $age_input;
    $notes = $_POST['notes'];

    $image_name = null;
    if (!empty($_FILES['pet_image']['name'])) {
        $image_name = time() . "_" . $_FILES['pet_image']['name'];
        move_uploaded_file(
            $_FILES['pet_image']['tmp_name'],
            "../assets/uploads/pets/" . $image_name
        );
    }

    mysqli_query($conn, "
        INSERT INTO pets (
            user_id, pet_name, pet_type, breed,
            age_months, notes, pet_image,
            pet_size, medications, gender
        ) VALUES (
            '$user_id','$pet_name','$pet_type','$breed',
            '$age','$notes','$image_name',
            '$pet_size','$medications','$gender'
        )
    ");

    header("Location: pets.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Pet | Dakimomo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.form-section {
    border-left: 4px solid #c8a97e;
    padding-left: 12px;
    margin-bottom: 20px;
}
</style>
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
        <li class="nav-item"><a class="nav-link active" href="pets.php">My Pets</a></li>
        <li class="nav-item"><a class="nav-link" href="my-bookings.php">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="chat.php">Messages</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
</div>
</nav>

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-7">

<div class="card shadow-sm">
<div class="card-body p-4">

<h3 class="section-title mb-4">🐾 Add New Pet</h3>

<form method="POST" enctype="multipart/form-data">

<!-- BASIC INFO -->
<div class="form-section">
<h6 class="fw-bold mb-3">Basic Information</h6>

<div class="mb-3">
    <label class="form-label">Pet Name</label>
    <input type="text" name="pet_name" class="form-control" required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Species</label>
        <select name="pet_type" class="form-select" required>
            <option disabled selected>Select species</option>
            <option>Dog</option>
            <option>Cat</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Size</label>
        <select name="pet_size" class="form-select" required>
            <option disabled selected>Select size</option>
            <option>Small</option>
            <option>Medium</option>
            <option>Large</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select" required>
            <option disabled selected>Select gender</option>
            <option value="Male">♂ Male</option>
            <option value="Female">♀ Female</option>
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Breed</label>
    <input type="text" name="breed" class="form-control">
</div>
</div>

<!-- AGE -->
<div class="form-section">
<h6 class="fw-bold mb-3">Age</h6>

<div class="input-group mb-3">
    <input type="number" name="age" class="form-control" min="0" required>
    <select name="age_unit" class="form-select">
        <option value="months">Months</option>
        <option value="years">Years</option>
    </select>
</div>
</div>

<!-- HEALTH -->
<div class="form-section">
<h6 class="fw-bold mb-3">Health & Notes</h6>

<div class="mb-3">
    <label class="form-label">Medications (if any)</label>
    <textarea name="medications" class="form-control" rows="2"></textarea>
</div>

<div class="mb-3">
    <label class="form-label">Special Notes</label>
    <textarea name="notes" class="form-control" rows="3"></textarea>
</div>
</div>

<!-- IMAGE -->
<div class="form-section">
<h6 class="fw-bold mb-3">Pet Image</h6>

<input type="file" name="pet_image" class="form-control" accept="image/*">
<small class="text-muted">Optional. JPG or PNG recommended.</small>
</div>

<!-- ACTION -->
<div class="d-flex justify-content-between mt-4">
    <a href="pets.php" class="btn btn-secondary">Cancel</a>
    <button name="save_pet" class="btn btn-brown px-4">
        Save Pet
    </button>
</div>

</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>