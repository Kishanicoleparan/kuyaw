<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch all bookings with customer, car info + PAYMENT STATUS
$bookings = mysqli_query($conn, "
    SELECT b.booking_id, b.booking_date, b.return_date, b.status AS booking_status, 
           b.payment_status, b.total_price, b.payment_method,
           c.car_name, c.brand, c.car_image, cu.name AS customer_name
    FROM bookings b
    JOIN cars c ON b.car_id = c.car_id
    JOIN users cu ON b.id = cu.id
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bookings | UrbanDrive Admin</title>
<link rel="stylesheet" href="adashboard.css">
<style>
body {
    background: #f8f9fa;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
}
.page-content {
    max-width: 1400px;
    margin: 40px auto;
    padding: 0 20px;
}
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.page-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: #1a1a2e;
}
.btn-primary {
    background: linear-gradient(135deg, #ff6a00, #ff914d);
    color: #fff;
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255,106,0,0.4);
}
.search-bar {
    margin-bottom: 20px;
    width: 300px;
    padding: 10px 14px;
    font-size: 16px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

/* Table Styles - UNCHANGED */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}
th, td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
    font-size: 16px;
}
th {
    background: #ff6a00;
    color: white;
    font-weight: 600;
}
tr:hover {
    background: #fff3ea;
}
.car-img {
    width: 60px;
    height: 40px;
    object-fit: cover;
    border-radius: 6px;
    border: 2px solid #ff914d;
}

/* ORIGINAL Status Styles + NEW Payment Status */
.status {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
}
.status-pending { background: #fff3cd; color: #856404; }
.status-approved { background: #d4edda; color: #155724; }
.status-completed { background: #d1ecf1; color: #0c5460; }
.status-cancelled { background: #f8d7da; color: #721c24; }

/* NEW: Payment Status Badges */
.payment-pending { background: #fff3cd; color: #856404; border: 1px solid #f0ad4e; }
.payment-paid { background: #d4edda; color: #155724; border: 1px solid #28a745; }
.payment-failed { background: #f8d7da; color: #721c24; border: 1px solid #dc3545; }
.payment-refunded { background: #e2e3e5; color: #6c757d; border: 1px solid #6c757d; }

.action-btn {
    padding: 6px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    color: white;
    font-weight: 600;
    margin-right: 5px;
    display: inline-block;
    text-align: center;
    min-width: 60px;
}
.edit-btn { background: #4361ee; }
.edit-btn:hover { background: #3a56d4; }
.delete-btn { background: #e63946; }
.delete-btn:hover { background: #c1121f; }
/* NEW: View Receipt Button */
.view-receipt-btn { 
    background: #27ae60; 
    min-width: 80px;
}
.view-receipt-btn:hover { 
    background: #219a52; 
}
.view-receipt-disabled {
    background: #6c757d !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}

.empty {
    text-align: center;
    padding: 50px;
    color: #777;
    font-size: 18px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

/* Responsive - UNCHANGED */
@media (max-width: 768px){
    table, th, td { font-size: 14px; }
    .search-bar { width: 100%; margin-bottom: 15px; }
    .page-header { flex-direction: column; gap: 15px; text-align: center; }
    .action-btn { 
        display: block; 
        margin: 5px 0; 
        min-width: 100%; 
    }
}
</style>
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span> Admin</div>
    <nav>
        <a href="reports.php">Dashboard</a>
        <a href="addcar.php">Add Car</a>
        <a href="viewcar.php">View Cars</a>
        <a href="bookings.php" class="active">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a class="logout-btn" href="logout.php">Logout</a>
    </nav>
</header>

<div class="page-content">
    <div class="page-header">
        <h1>All Bookings</h1>
    </div>

    <input type="text" id="searchInput" class="search-bar" placeholder="Search bookings...">

    <table id="bookingTable">
        <thead>
            <tr>
                <th>Car</th>
                <th>Car Name</th>
                <th>Brand</th>
                <th>Customer</th>
                <th>Booking Dates</th>
                <th>Total Price</th>
                <th>Payment Status</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($bookings) == 0): ?>
                <tr>
                    <td colspan="9" style="text-align:center;">No bookings found.</td>
                </tr>
            <?php else: ?>
                <?php while($booking = mysqli_fetch_assoc($bookings)): ?>
                    <tr>
                        <td>
                            <?php if(!empty($booking['car_image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($booking['car_image']) ?>" class="car-img" alt="Car">
                            <?php else: ?>
                                <div style="width:60px;height:40px;background:#f8f9fa;color:#adb5bd;display:flex;align-items:center;justify-content:center;font-size:12px;border-radius:6px;border:2px solid #ff914d;">N/A</div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($booking['car_name']) ?></td>
                        <td><?= htmlspecialchars($booking['brand']) ?></td>
                        <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($booking['booking_date'])) ?> - <?= date('M d, Y', strtotime($booking['return_date'])) ?></td>
                        <td>₱<?= number_format($booking['total_price'],2) ?></td>
                        <!-- Payment Status -->
                        <td>
                            <span class="status payment-<?= $booking['payment_status'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $booking['payment_status'])) ?>
                            </span>
                        </td>
                        <td><span class="status status-<?= strtolower($booking['booking_status']) ?>"><?= $booking['booking_status'] ?></span></td>
                        <td>
                            <a href="edit_booking.php?id=<?= $booking['booking_id'] ?>" class="action-btn edit-btn">Edit</a>
                            <a href="delete_booking.php?id=<?= $booking['booking_id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                            <!-- NEW: View Receipt Button - Only for PAID bookings -->
                            <?php if($booking['payment_status'] === 'paid'): ?>
                                <a href="view_receipt_admin.php?id=<?= $booking['booking_id'] ?>" class="action-btn view-receipt-btn" title="View Receipt">Receipt</a>
                            <?php else: ?>
                                <span class="action-btn view-receipt-disabled" title="Receipt only available for paid bookings">Receipt</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Client-side search
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#bookingTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>