<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

$customer_id = $_SESSION['id'];
$customer_name = $_SESSION['name'];

// Fetch stats
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE id='$customer_id'"))['total'];
$active_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE id='$customer_id' AND status='Approved'"))['total'];
$completed_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE id='$customer_id' AND status='Completed'"))['total'];
$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total FROM bookings WHERE id='$customer_id' AND status='Completed'"))['total'];

// Fetch latest 4 available cars
$cars = mysqli_query($conn, "SELECT * FROM cars WHERE status='Available' ORDER BY car_id DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Dashboard | UrbanDrive</title>
<link rel="stylesheet" href="../adashboard.css">
<link rel="stylesheet" href="customer.css">
<link rel="stylesheet" href="available_cars.css">
<style>
/* Custom Action Buttons Styling */
.action-buttons-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 40px 0;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    min-width: 180px;
    justify-content: center;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s ease;
}

.action-btn:hover::before {
    left: 100%;
}

/* View My Bookings Button - Blue Theme */
.action-btn.bookings {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
}

.action-btn.bookings:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.5);
    background: linear-gradient(135deg, #2980b9, #1a5276);
}

.action-btn.bookings svg {
    width: 20px;
    height: 20px;
}

/* Browse Cars Button - Green Theme */
.action-btn.browse {
    background: linear-gradient(135deg, #27ae60, #229954);
    color: white;
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
}

.action-btn.browse:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(39, 174, 96, 0.5);
    background: linear-gradient(135deg, #229954, #1e8449);
}

.action-btn.browse svg {
    width: 20px;
    height: 20px;
}

/* Edit Profile Button - Purple Theme */
.action-btn.profile {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: white;
    box-shadow: 0 4px 15px rgba(155, 89, 182, 0.4);
}

.action-btn.profile:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(155, 89, 182, 0.5);
    background: linear-gradient(135deg, #8e44ad, #7d3c98);
}

.action-btn.profile svg {
    width: 20px;
    height: 20px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .action-buttons-container {
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    
    .action-btn {
        width: 100%;
        max-width: 280px;
    }
}
</style>
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="available_cars.php">Available Cars</a>
        <a href="profile.php">Profile</a>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<div class="page-content">
    <div class="container">

        <!-- Welcome -->
        <h1>Welcome, <?= htmlspecialchars($customer_name) ?>!</h1>

        <!-- Stats Cards -->
        <div class="stats">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <p><?= $total_bookings ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Bookings</h3>
                <p><?= $active_bookings ?></p>
            </div>
            <div class="stat-card">
                <h3>Completed Bookings</h3>
                <p><?= $completed_bookings ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Spent</h3>
               <p>₱<?= number_format($total_spent ?? 0, 2) ?></p>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="action-buttons-container">
            <a href="my_bookings.php" class="action-btn bookings">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                View My Bookings
            </a>
            <a href="available_cars.php" class="action-btn browse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Browse Cars
            </a>
            <a href="profile.php" class="action-btn profile">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Edit Profile
            </a>
        </div>

        <!-- Latest Cars -->
        <h2>Recently Added Cars</h2>
        <div class="car-grid">
            <?php while($car = mysqli_fetch_assoc($cars)) { ?>
                <div class="car-card">
                    <div class="car-image">
                        <img src="../uploads/<?= $car['car_image'] ?: 'car-placeholder.png' ?>" alt="<?= $car['car_name'] ?>">
                    </div>
                    <div class="car-info">
                        <h3><?= $car['car_name'] ?></h3>
                        <p>Brand: <?= $car['brand'] ?></p>
                        <p class="car-price">₱<?= number_format($car['price_per_day'],2) ?>/day</p>
                        <a href="rent_process.php?id=<?= $car['car_id'] ?>" class="rent-btn">Rent</a>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</div>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>