<?php
include '../includes/auth.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM pets WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Pets | Dakimomo</title>

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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">My Pets</h2>
        <a href="add-pet.php" class="btn btn-brown">Add Pet</a>
    </div>

    <div class="row g-4">

        <?php while ($pet = mysqli_fetch_assoc($result)) { 
            
            $image = $pet['pet_image'] 
            ? "../assets/uploads/pets/" . $pet['pet_image'] 
            : "../assets/images/default-pet.png";
    ?>

            <div class="col-md-4">
                <div class="card shadow-md">
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <img src="<?= $image ?>" class="img-fluid rounded mb-3"
                            style="height:200px; object-fit:cover;">
                        </div>
                        <h4 class="fw-bold section-title"><?= $pet['pet_name']; ?></h4>
                        <strong>Type:</strong> <?= $pet['pet_type']; ?><br>
                        <strong>Breed:</strong> <?= $pet['breed']; ?><br>
                        <strong>Age:</strong> <?= $pet['age']; ?><br>
                        <strong>Special Notes:</strong> <?= $pet['notes']; ?>
                        </p>

                        <a href="edit-pet.php?id=<?= $pet['pet_id']; ?>" class="btn btn-sm btn-primary px-3">Edit</a>
                        <a href="delete-pet.php?id=<?= $pet['pet_id']; ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this pet?')">Delete</a>
                    </div>
                </div>
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>