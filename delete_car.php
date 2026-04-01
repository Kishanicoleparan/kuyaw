<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = $_GET['id'];
$car = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cars WHERE car_id=$id"));

// Delete image if exists
if($car['car_image'] && file_exists('../uploads/'.$car['car_image'])){
    unlink('../uploads/'.$car['car_image']);
}

// Delete car record
mysqli_query($conn, "DELETE FROM cars WHERE car_id=$id");

header("Location: viewcar.php");
exit();
