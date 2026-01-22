<?php
include 'includes/db.php';

if (isset($_POST['register'])) {

    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $date_created = date("Y-m-d H:i:s");

    $query = "INSERT INTO users (full_name, email, password, contact_number, role)
              VALUES ('$name', '$email', '$password', '$contact', 'client')";

    if (mysqli_query($conn, $query)) {
        header("Location: login.php");
    } else {
        echo "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Dakimomo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background-color: #c8a97e;">

 <nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/images/logo.png" height="45" class="me-2">
      <strong>Dakimomo</strong>
    </a>

    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item">
          <a class="btn btn-brown ms-3" href="login.php">Book Now</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height:100vh;">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h3 class="text-center section-title mb-3">Create Account</h3>
                <p class="text-center mb-4">Register to book pet services</p>

                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" class="form-control" placeholder="Your name" name="full_name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" placeholder="Phone number" name="contact" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" class="form-control" placeholder="Enter email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="Create password" name="password" required>
                    </div>

                    <button class="btn btn-brown w-100 mt-3" name="register">Register</button>
                </form>

                <p class="text-center mt-3">
                    Already have an account? <a href="login.php" class="text-decoration-none">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>