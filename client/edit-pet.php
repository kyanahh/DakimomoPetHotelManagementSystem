<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: pets.php");
    exit();
}

$pet_id  = $_GET['id'];
$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "
    SELECT * FROM pets 
    WHERE pet_id='$pet_id' AND user_id='$user_id'
");

if (mysqli_num_rows($result) == 0) {
    header("Location: pets.php");
    exit();
}

$pet = mysqli_fetch_assoc($result);

/* AGE CONVERSION */
$age_months = (int)$pet['age_months'];
if ($age_months >= 12) {
    $age_value = floor($age_months / 12);
    $age_unit  = "years";
} else {
    $age_value = $age_months;
    $age_unit  = "months";
}

if (isset($_POST['update_pet'])) {

    $pet_name  = $_POST['pet_name'];
    $pet_type  = $_POST['pet_type'];
    $breed     = $_POST['breed'];
    $age_input = $_POST['age'];
    $age_unit  = $_POST['age_unit'];
    $notes     = $_POST['notes'];
    $pet_size  = $_POST['pet_size'];
    $medications = $_POST['medications'];

    $age = ($age_unit === 'years') ? $age_input * 12 : $age_input;

    $image_query = "";
    if (!empty($_FILES['pet_image']['name'])) {
        $new_image = time() . "_" . $_FILES['pet_image']['name'];
        move_uploaded_file(
            $_FILES['pet_image']['tmp_name'],
            "../assets/uploads/pets/" . $new_image
        );
        $image_query = ", pet_image='$new_image'";
    }

    mysqli_query($conn, "
        UPDATE pets SET
            pet_name='$pet_name',
            pet_type='$pet_type',
            breed='$breed',
            age_months='$age',
            notes='$notes',
            pet_size='$pet_size',
            medications='$medications'
            $image_query
        WHERE pet_id='$pet_id' AND user_id='$user_id'
    ");

    header("Location: pets.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Pet | Dakimomo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.form-section {
    border-left: 4px solid #c8a97e;
    padding-left: 12px;
    margin-bottom: 20px;
}
.pet-preview {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid #ddd;
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

<h3 class="section-title mb-4">✏️ Edit Pet Details</h3>

<form method="POST" enctype="multipart/form-data">

<!-- BASIC INFO -->
<div class="form-section">
<h6 class="fw-bold mb-3">Basic Information</h6>

<div class="mb-3">
    <label class="form-label">Pet Name</label>
    <input type="text" name="pet_name" class="form-control"
           value="<?= htmlspecialchars($pet['pet_name']); ?>" required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Species</label>
        <select name="pet_type" class="form-select" required>
            <option <?= $pet['pet_type']=="Dog" ? "selected" : ""; ?>>Dog</option>
            <option <?= $pet['pet_type']=="Cat" ? "selected" : ""; ?>>Cat</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Size</label>
        <select name="pet_size" class="form-select" required>
            <option <?= $pet['pet_size']=="Small" ? "selected" : ""; ?>>Small</option>
            <option <?= $pet['pet_size']=="Medium" ? "selected" : ""; ?>>Medium</option>
            <option <?= $pet['pet_size']=="Large" ? "selected" : ""; ?>>Large</option>
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Breed</label>
    <input type="text" name="breed" class="form-control"
           value="<?= htmlspecialchars($pet['breed']); ?>">
</div>
</div>

<!-- AGE -->
<div class="form-section">
<h6 class="fw-bold mb-3">Age</h6>

<div class="input-group mb-3">
    <input type="number" name="age" class="form-control"
           value="<?= $age_value; ?>" min="0" required>
    <select name="age_unit" class="form-select">
        <option value="months" <?= $age_unit=="months" ? "selected" : ""; ?>>Months</option>
        <option value="years" <?= $age_unit=="years" ? "selected" : ""; ?>>Years</option>
    </select>
</div>
</div>

<!-- HEALTH -->
<div class="form-section">
<h6 class="fw-bold mb-3">Health & Notes</h6>

<div class="mb-3">
    <label class="form-label">Medications</label>
    <textarea name="medications" class="form-control" rows="2"><?= htmlspecialchars($pet['medications']); ?></textarea>
</div>

<div class="mb-3">
    <label class="form-label">Special Notes</label>
    <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($pet['notes']); ?></textarea>
</div>
</div>

<!-- IMAGE -->
<div class="form-section">
<h6 class="fw-bold mb-3">Pet Image</h6>

<div class="d-flex align-items-center gap-3 mb-3">
<?php
$image = $pet['pet_image']
    ? "../assets/uploads/pets/".$pet['pet_image']
    : "../assets/images/default-pet.png";
?>
<img src="<?= $image ?>" class="pet-preview">
<div>
    <label class="form-label">Change Image</label>
    <input type="file" name="pet_image" class="form-control" accept="image/*">
</div>
</div>
</div>

<!-- ACTIONS -->
<div class="d-flex justify-content-between mt-4">
    <a href="pets.php" class="btn btn-secondary">Cancel</a>
    <button name="update_pet" class="btn btn-brown px-4">
        Update Pet
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