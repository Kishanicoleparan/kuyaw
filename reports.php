<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Summary counts
$total_cars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cars"))['total'];
$available_cars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cars WHERE status='Available'"))['total'];
$rented_cars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cars WHERE status='Rented'"))['total'];

$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='customer'"))['total'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'];
$completed_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status='Completed'"))['total'];

// Fetch all bookings with customer and car info
$bookings = mysqli_query($conn, "
    SELECT b.booking_id, b.booking_date, b.status,
           u.name AS customer_name, c.car_name
    FROM bookings b
    JOIN users u ON b.id = u.id
    JOIN cars c ON b.car_id = c.car_id
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports - Admin Dashboard | UrbanDrive</title>
<link rel="stylesheet" href="adashboard.css">
<style>
.page-content { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
h1 { font-size: 36px; margin-bottom: 30px; }
.stats { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 50px; }
.stat-card { flex: 1 1 200px; background: #fff; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05);}
.stat-card h3 { margin: 0 0 10px; color: #ff6a00; font-size: 18px; }
.stat-card p { font-size: 24px; font-weight: bold; margin: 0; color: #1a1a2e; }

.card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);}
.card h2 { margin-top: 0; margin-bottom: 20px; }

.search-bar { margin-bottom: 20px; width: 300px; padding: 10px 14px; font-size: 16px; border-radius: 8px; border: 1px solid #ddd; }

table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 16px; }
th { background: #ff6a00; color: white; }
tr:hover { background: #fff3ea; }

@media (max-width: 768px){
    .stats { flex-direction: column; }
    .search-bar { width: 100%; margin-bottom: 15px; }
}
</style>
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

    <h1>Reports</h1>

    <!-- Summary Cards -->
    <div class="stats">
        <div class="stat-card">
            <h3>Total Cars</h3>
            <p><?= $total_cars ?></p>
        </div>
        <div class="stat-card">
            <h3>Available Cars</h3>
            <p><?= $available_cars ?></p>
        </div>
        <div class="stat-card">
            <h3>Rented Cars</h3>
            <p><?= $rented_cars ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Customers</h3>
            <p><?= $total_customers ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Bookings</h3>
            <p><?= $total_bookings ?></p>
        </div>
        <div class="stat-card">
            <h3>Completed Bookings</h3>
            <p><?= $completed_bookings ?></p>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <h2>All Bookings</h2>
        <input type="text" id="searchInput" class="search-bar" placeholder="Search bookings...">
        <table id="bookingTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Car</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($booking = mysqli_fetch_assoc($bookings)) { ?>
                <tr>
                    <td><?= $booking['booking_id'] ?></td>
                    <td><?= $booking['customer_name'] ?></td>
                    <td><?= $booking['car_name'] ?></td>
                    <td><?= date("M d, Y", strtotime($booking['booking_date'])) ?></td>
                    <td>
                        <?php 
                        $status = $booking['status'];
                        if ($status=="Pending") echo "<span style='color:orange;font-weight:bold;'>Pending</span>";
                        elseif ($status=="Approved") echo "<span style='color:blue;font-weight:bold;'>Approved</span>";
                        elseif ($status=="Completed") echo "<span style='color:green;font-weight:bold;'>Completed</span>";
                        elseif ($status=="Cancelled") echo "<span style='color:red;font-weight:bold;'>Cancelled</span>";
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

<script>
// Simple client-side search
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#bookingTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>