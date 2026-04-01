<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pricing - UrbanDrive</title>
    <link rel="stylesheet" href="css/landing.css">
</head>
<body>

<!-- Navigation -->
<header class="navbar" style="position: relative; background: white;">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="vehicles.php">Vehicles</a>
        <a href="pricing.php" class="active">Pricing</a>
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
    <h1 style="font-size: 48px;">Our Pricing</h1>
    <p style="margin-top: 10px; opacity: 0.8;">Choose the perfect plan for your needs</p>
</section>

<!-- Pricing Section -->
<section class="pricing-section">
    <div class="pricing-grid">
        <div class="pricing-card">
            <h3>Daily</h3>
            <div class="price">$50<span>/day</span></div>
            <ul>
                <li>✓ 24-hour rental</li>
                <li>✓ Unlimited mileage</li>
                <li>✓ Basic insurance</li>
                <li>✓ 24/7 support</li>
            </ul>
            <a href="vehicles.php" class="btn-pricing">Select Plan</a>
        </div>
        
        <div class="pricing-card featured">
            <h3>Weekly</h3>
            <div class="price">$300<span>/week</span></div>
            <ul>
                <li>✓ 7-day rental</li>
                <li>✓ Unlimited mileage</li>
                <li>✓ Full insurance</li>
                <li>✓ Free cancellation</li>
                <li>✓ Priority support</li>
            </ul>
            <a href="vehicles.php" class="btn-pricing">Select Plan</a>
        </div>
        
        <div class="pricing-card">
            <h3>Monthly</h3>
            <div class="price">$1,000<span>/month</span></div>
            <ul>
                <li>✓ 30-day rental</li>
                <li>✓ Unlimited mileage</li>
                <li>✓ Premium insurance</li>
                <li>✓ Free cancellation</li>
                <li>✓ Dedicated support</li>
                <li>✓ Maintenance included</li>
            </ul>
            <a href="vehicles.php" class="btn-pricing">Select Plan</a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <p>&copy; 2026 UrbanDrive. All rights reserved.</p>
</footer>

</body>
</html>