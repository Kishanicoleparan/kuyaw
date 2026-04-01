<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Fetch available cars only
$cars = mysqli_query($conn, "SELECT * FROM cars WHERE status='Available'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Dashboard | UrbanDrive</title>

<link rel="stylesheet" href="../css/cdashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- NAVBAR -->
<header class="navbar">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="#">My Bookings</a>
        <a href="#">Profile</a>
        <a href="logout.php" class="btn">Logout</a>
    </nav>
</header>

<!-- MAIN -->
<main class="container">
    <h1>Available Cars</h1>

    <div class="car-grid">
        <?php while ($car = mysqli_fetch_assoc($cars)) { ?>
            <div class="car-card">
                <div class="car-image">
                    <?php if ($car['car_image'] && file_exists('../uploads/'.$car['car_image'])) { ?>
                        <img src="../uploads/<?= $car['car_image'] ?>" alt="Car">
                    <?php } else { ?>
                        <img src="../assets/images/default-car.png" alt="Car">
                    <?php } ?>
                </div>

                <div class="car-info">
                    <h3><?= $car['car_name'] ?></h3>
                    <p class="brand"><?= $car['brand'] ?></p>

                    <div class="price">
                        ₱<?= number_format($car['price_per_day']) ?>
                        <span>/ day</span>
                    </div>

                    <a href="#" class="rent-btn">Rent Now</a>
                </div>
            </div>
        <?php } ?>
    </div>
</main>

</body>
</html>
