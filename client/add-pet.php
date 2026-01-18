<?php
include '../includes/auth.php';
include '../includes/db.php';

if (isset($_POST['save_pet'])) {

    $user_id  = $_SESSION['user_id'];
    $pet_name = $_POST['pet_name'];
    $pet_type = $_POST['pet_type'];
    $breed    = $_POST['breed'];
    $age      = $_POST['age'];
    $notes    = $_POST['notes'];

    // IMAGE UPLOAD
    $image_name = null;

    if (!empty($_FILES['pet_image']['name'])) {
        $image_name = time() . "_" . $_FILES['pet_image']['name'];
        $target = "../assets/uploads/pets/" . $image_name;
        move_uploaded_file($_FILES['pet_image']['tmp_name'], $target);
    }

    $query = "INSERT INTO pets (user_id, pet_name, pet_type, breed, age, notes, pet_image)
              VALUES ('$user_id', '$pet_name', '$pet_type', '$breed', '$age', '$notes', '$image_name')";

    if (mysqli_query($conn, $query)) {
        header("Location: pets.php");
        exit();
    } else {
        echo "Failed to add pet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Pet | Dakimomo</title>

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
                <h3 class="section-title mb-3">Add Pet</h3>

                <form method="POST" enctype="multipart/form-data">

                    <input type="text" name="pet_name" class="form-control mb-3" placeholder="Pet Name" required>

                    <select name="pet_type" class="form-control mb-3" required>
                        <option value="">Select Type</option>
                        <option>Dog</option>
                        <option>Cat</option>
                    </select>

                    <input type="text" name="breed" class="form-control mb-3" placeholder="Breed">

                    <input type="number" name="age" class="form-control mb-3" placeholder="Age">

                    <textarea name="notes" class="form-control mb-3" placeholder="Special Notes"></textarea>
                    
                    <div class="mb-3">
                        <label>Pet Image</label>
                        <input type="file" name="pet_image" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" name="save_pet" class="btn btn-brown w-100">
                        Save Pet
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>