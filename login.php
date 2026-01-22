<?php
session_start();
include 'includes/db.php';

$error = false;

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email' AND status='active' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['full_name'];

        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($user['role'] === 'staff') {
            header("Location: staff/dashboard.php");
        } else {
            header("Location: client/dashboard.php");
        }
        exit();

    } else {
        $error = true; // trigger toast
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Dakimomo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background-color: #c8a97e;">

<?php if ($error): ?>
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="loginToast" class="toast align-items-center text-bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Invalid email or password. Please try again.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

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


<div class="container mt-5">
    <div class="row justify-content-center" style="min-height:100vh;">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h3 class="text-center section-title mb-3">Welcome Back</h3>
                <p class="text-center mb-4">Login to your account</p>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" class="form-control" placeholder="Enter your email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="Enter your password" name="password" required>
                    </div>

                    <button class="btn btn-brown w-100 mt-3" name="login" >Login</button>
                </form>

                <p class="text-center mt-3">
                    Don’t have an account? <a href="register.php" class="text-decoration-none">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($error): ?>
<script>
const toastEl = document.getElementById('loginToast');
const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
toast.show();
</script>
<?php endif; ?>

</body>
</html>