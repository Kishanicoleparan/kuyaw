<?php
session_start();
include 'db.php';

// Get cars for display
$query = "SELECT * FROM cars WHERE availability = 'available'";
$result = mysqli_query($conn, $query);
$cars = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicles - UrbanDrive</title>
    <link rel="stylesheet" href="css/landing.css">
</head>
<body>

<!-- Navigation -->
<header class="navbar" style="position: relative; background: white;">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="vehicles.php" class="active">Vehicles</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="booking_history.php" class="btn-login">My Bookings</a>
            <a href="logout.php" class="btn-register">Logout (<?php echo $_SESSION['username']; ?>)</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        <?php endif; ?>
    </nav>
</header>

<!-- Page Header -->
<section style="background: #2b1d16; color: white; padding: 60px 80px; text-align: center;">
    <h1 style="font-size: 48px;">Our Fleet</h1>
    <p style="margin-top: 10px; opacity: 0.8;">Choose from our wide selection of vehicles</p>
</section>

<!-- Vehicles Section -->
<section class="vehicles-section">
    <div class="cars-grid">
        <?php if (count($cars) > 0): ?>
            <?php foreach ($cars as $car): ?>
                <div class="car-card">
                    <div class="image-box" style="width: 100%; height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f5f5f5;">
    <?php if (!empty($car['car_image']) && file_exists("uploads/".$car['car_image'])) { ?>
        <img src="uploads/<?php echo $car['car_image']; ?>" 
             style="width: 100%; height: 100%; object-fit: cover;">
    <?php } else { ?>
        <div class="no-image-placeholder" style="text-align: center; color: #999;">
            <!-- Keep your existing SVG code here -->
        </div>
    <?php } ?>
</div>
                    <div class="car-info">
                        <h3><?php echo htmlspecialchars(($car['brand'] ?? '') . ' ' . ($car['model'] ?? '')); ?></h3>
                        <p class="car-details">
                            <?php echo htmlspecialchars($car['year'] ?? '2020'); ?> • 
                            <?php echo htmlspecialchars($car['color'] ?? 'White'); ?>
                        </p>
                        <p class="car-type"><?php echo htmlspecialchars($car['type'] ?? 'Sedan'); ?></p>
                        <p class="car-price">$<?php echo number_format($car['price_per_day'] ?? 0, 2); ?>/day</p>
                        
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="book_car.php?car_id=<?php echo $car['id']; ?>" class="btn-book">Book Now</a>
                        <?php else: ?>
                            <a href="login.php" class="btn-book">Book Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-cars">
                <p>No cars available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <p>&copy; 2026 UrbanDrive. All rights reserved.</p>
</footer>

</body>
</html>