<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../db.php";

if (!isset($_GET['booking_id'])) {
    die("Invalid booking ID");
}

$booking_id = intval($_GET['booking_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed | UrbanDrive</title>
    <link rel="stylesheet" href="../adashboard.css">
    <style>
        .container {
            max-width: 500px;
            margin: 100px auto;
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .icon {
            font-size: 60px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 14px 30px;
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn:hover {
            background: #34495e;
        }
    </style>
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="available_cars.php">Available Cars</a>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<main class="page-content">
    <div class="container">
        <div class="icon">❌</div>
        <h2>Payment Failed</h2>
        <p>Your payment for Booking #<?= $booking_id ?> was not completed. Please try again.</p>
        <a href="my_bookings.php" class="btn">Back to My Bookings</a>
    </div>
</main>

</body>
</html>