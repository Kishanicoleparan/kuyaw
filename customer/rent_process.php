<?php
session_start();
require_once "../db.php";

// Only customer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$customer_id = $_SESSION['id'];
$car_id = $_GET['id'] ?? null;

if (!$car_id) {
    header("Location: available_cars.php");
    exit();
}

// Fetch car info
$car_query = mysqli_query($conn, "SELECT * FROM cars WHERE car_id='$car_id'");
$car = mysqli_fetch_assoc($car_query);

if (!$car) {
    header("Location: available_cars.php");
    exit();
}

// Handle form submission
if (isset($_POST['rent'])) {
    $booking_date = $_POST['booking_date'];
    $return_date = $_POST['return_date'];
    
    // Calculate total price
    $days = (strtotime($return_date) - strtotime($booking_date)) / (60*60*24);
    $days = max(1, $days); // at least 1 day
    $total_price = $days * $car['price_per_day'];

    mysqli_query($conn, "INSERT INTO bookings 
        (id, car_id, booking_date, return_date, status, total_price) 
        VALUES ('$customer_id', '$car_id', '$booking_date', '$return_date', 'Pending', '$total_price')");

    header("Location: my_bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Rent Car | UrbanDrive</title>
<link rel="stylesheet" href="../adashboard.css">
<link rel="stylesheet" href="available_cars.css">
<style>
.rent-form-container {
    max-width: 600px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 25px 50px rgba(255,106,0,0.15);
}

.rent-form-container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2b1d16;
}

.rent-form-container label {
    display: block;
    margin-top: 15px;
    font-weight: 600;
    color: #5a3e30;
}

.rent-form-container input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.rent-form-container .total-price {
    margin: 20px 0;
    font-size: 18px;
    font-weight: 700;
    color: #ff6a00;
}

.rent-form-container button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #ff6a00, #ff914d);
    color: #fff;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    margin-top: 15px;
    cursor: pointer;
}

.rent-form-container button:hover {
    opacity: 0.85;
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

<main class="page-content container">

    <div class="rent-form-container">
        <h2>Rent Car: <?= htmlspecialchars($car['car_name']) ?></h2>

        <form method="post">
            <label>Booking Date</label>
            <input type="date" name="booking_date" required>

            <label>Return Date</label>
            <input type="date" name="return_date" required>

            <div class="total-price">
                Price per day: ₱<?= number_format($car['price_per_day'],2) ?>
            </div>

            <button type="submit" name="rent">Confirm Booking</button>
        </form>
    </div>

</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>
