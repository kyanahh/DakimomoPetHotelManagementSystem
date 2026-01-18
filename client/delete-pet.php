<?php
include '../includes/auth.php';
include '../includes/db.php';

$pet_id = $_GET['id'];

$query = "DELETE FROM pets WHERE pet_id='$pet_id'";
mysqli_query($conn, $query);

header("Location: pets.php");
exit();
?>