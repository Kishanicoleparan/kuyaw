<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact - UrbanDrive</title>
    <link rel="stylesheet" href="css/landing.css">
</head>
<body>

<!-- Navigation -->
<header class="navbar" style="position: relative; background: white;">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="vehicles.php">Vehicles</a>
        <a href="contact.php" class="active">Contact</a>
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
    <h1 style="font-size: 48px;">Contact Us</h1>
    <p style="margin-top: 10px; opacity: 0.8;">Get in touch with us for any inquiries</p>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="contact-container">
        <div class="contact-info">
            <div class="contact-item">
                <span class="icon">📍</span>
                <div>
                    <h4>Address</h4>
                    <p>123 Urban Drive, Manila, Philippines</p>
                </div>
            </div>
            <div class="contact-item">
                <span class="icon">📞</span>
                <div>
                    <h4>Phone</h4>
                    <p>+63 123 456 7890</p>
                </div>
            </div>
            <div class="contact-item">
                <span class="icon">✉️</span>
                <div>
                    <h4>Email</h4>
                    <p>info@urbandrive.com</p>
                </div>
            </div>
            <div class="contact-item">
                <span class="icon">🕐</span>
                <div>
                    <h4>Hours</h4>
                    <p>Mon-Sat: 8AM - 8PM<br>Sun: 9AM - 5PM</p>
                </div>
            </div>
        </div>
        
        <div class="contact-form">
            <form method="POST">
                <div class="form-group">
                    <input type="text" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <input type="text" placeholder="Subject" required>
                </div>
                <div class="form-group">
                    <textarea placeholder="Your Message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn-contact">Send Message</button>
            </form>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <p>&copy; 2026 UrbanDrive. All rights reserved.</p>
</footer>

</body>
</html>