<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UrbanDrive - Home</title>
    <link rel="stylesheet" href="css/landing.css">
</head>
<body>

<!-- Navigation -->
<header class="navbar">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="index.php" class="active">Home</a>
        <a href="vehicles.php">Vehicles</a>
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

<!-- Hero Section -->
<section class="hero">
    <div class="hero-text">
        <h1>Drive the Experience<br><span>You Deserve</span></h1>
        <p>Choose from a fleet of well-maintained, high-performance vehicles.</p>
        
        <div class="search-box">
            <form method="GET" action="vehicles.php">
                <select name="brand">
                    <option value="">Car Brand</option>
                    <option value="Toyota">Toyota</option>
                    <option value="Honda">Honda</option>
                    <option value="Kia">Kia</option>
                    <option value="Ford">Ford</option>
                    <option value="BMW">BMW</option>
                </select>
                
                <select name="type">
                    <option value="">Car Type</option>
                    <option value="Sedan">Sedan</option>
                    <option value="SUV">SUV</option>
                    <option value="Sports">Sports</option>
                    <option value="Luxury">Luxury</option>
                </select>
                
                <select name="location">
                    <option value="">Pickup Location</option>
                    <option value="Manila">Manila</option>
                    <option value="Quezon City">Quezon City</option>
                    <option value="Makati">Makati</option>
                    <option value="Taguig">Taguig</option>
                </select>
                
                <button type="submit" name="search">Book Now</button>
            </form>
        </div>
    </div>
</section>

</body>
</html>