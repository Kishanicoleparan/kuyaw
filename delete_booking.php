<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM bookings WHERE booking_id=$id");

header("Location: bookings.php");
exit();
