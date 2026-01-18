<?php
// ===============================
// AUTH & DATABASE
// ===============================
include '../includes/auth.php';
include '../includes/db.php';

// ===============================
// GET PET ID
// ===============================
if (!isset($_GET['id'])) {
    header("Location: pets.php");
    exit();
}

$pet_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// ===============================
// FETCH PET DATA (SECURE)
// ===============================
$query = "SELECT * FROM pets WHERE pet_id='$pet_id' AND user_id='$user_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: pets.php");
    exit();
}

$pet = mysqli_fetch_assoc($result);

// ===============================
// UPDATE PET
// ===============================
if (isset($_POST['update_pet'])) {

    $pet_name = $_POST['pet_name'];
    $pet_type = $_POST['pet_type'];
    $breed    = $_POST['breed'];
    $age      = $_POST['age'];
    $notes    = $_POST['notes'];

    // IMAGE UPDATE
    $image_query = "";

    if (!empty($_FILES['pet_image']['name'])) {
        $new_image = time() . "_" . $_FILES['pet_image']['name'];
        move_uploaded_file($_FILES['pet_image']['tmp_name'], "../assets/uploads/pets/" . $new_image);
        $image_query = ", pet_image='$new_image'";
    }

    $update = "UPDATE pets SET
           pet_name='$pet_name',
           pet_type='$pet_type',
           breed='$breed',
           age='$age',
           notes='$notes'
           $image_query
           WHERE pet_id='$pet_id' AND user_id='$user_id'";

    if (mysqli_query($conn, $update)) {
        header("Location: pets.php");
        exit();
    } else {
        $error = "Failed to update pet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pet | DM Services</title>

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
      <li class="nav-item"><a class="nav-link" href="#">Messages</a></li>
      <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container section">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm p-4">
                <h3 class="section-title mb-3">Edit Pet</h3>

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?= $error; ?></div>
                <?php } ?>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label>Pet Name</label>
                        <input type="text" name="pet_name" class="form-control"
                               value="<?= htmlspecialchars($pet['pet_name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Pet Type</label>
                        <select name="pet_type" class="form-control" required>
                            <option value="Dog" <?= $pet['pet_type']=="Dog" ? "selected" : ""; ?>>Dog</option>
                            <option value="Cat" <?= $pet['pet_type']=="Cat" ? "selected" : ""; ?>>Cat</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Breed</label>
                        <input type="text" name="breed" class="form-control"
                               value="<?= htmlspecialchars($pet['breed']); ?>">
                    </div>

                    <div class="mb-3">
                        <label>Age</label>
                        <input type="number" name="age" class="form-control"
                               value="<?= htmlspecialchars($pet['age']); ?>">
                    </div>

                    <div class="mb-3">
                        <label>Special Notes</label>
                        <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($pet['notes']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Change Pet Image</label>
                        <input type="file" name="pet_image" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" name="update_pet" class="btn btn-brown w-100">
                        Update Pet
                    </button>

                </form>

            </div>

        </div>
    </div>
</div>

</body>
</html>