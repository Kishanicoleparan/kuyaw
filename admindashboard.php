<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// COUNTS
$total_customers = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='customer'")
)['total'];

$total_cars = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM cars")
)['total'];

$total_bookings = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings")
)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | UrbanDrive</title>
<link rel="stylesheet" href="adashboard.css">
</head>

<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span> Admin</div>
    <nav>
        <a href="reports.php" class="active">Dashboard</a>
        <a href="addcar.php">Add Car</a>
        <a href="viewcar.php">View Cars</a>
        <a href="bookings.php">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<main class="page-content">

    <h1>Admin Dashboard</h1>

    <div class="stats">
        <div class="stat-card">
            <h3>Total Customers</h3>
            <p><?= $total_customers ?></p>
        </div>

        <div class="stat-card">
            <h3>Total Cars</h3>
            <p><?= $total_cars ?></p>
        </div>

        <div class="stat-card">
            <h3>Total Bookings</h3>
            <p><?= $total_bookings ?></p>
        </div>
    </div>

</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>
