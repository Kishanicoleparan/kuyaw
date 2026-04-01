<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = $_GET['id'];
$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id=$id"));

if(isset($_POST['update'])){
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE bookings SET status='$status' WHERE booking_id=$id");
    header("Location: bookings.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Booking Status | UrbanDrive Admin</title>
<link rel="stylesheet" href="adashboard.css">
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        min-height: 100vh;
    }

    .page-content {
        padding: 50px 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 36px;
        color: #1a1a2e;
        margin: 0;
        font-weight: 800;
    }

    .back-link {
        background: white;
        color: #1a1a2e;
        padding: 14px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .back-link:hover {
        background: #ff6a00;
        color: white;
    }

    /* --- WIDE FORM CARD --- */
    .form-card {
        background: white;
        border-radius: 24px;
        padding: 60px 80px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.12);
        border: 1px solid #eee;
    }

    .form-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .form-header h2 {
        font-size: 30px;
        color: #1a1a2e;
        margin: 0 0 12px 0;
        font-weight: 800;
    }

    .form-header p {
        color: #6c757d;
        margin: 0;
        font-size: 16px;
    }

    /* --- BOOKING INFO --- */
    .booking-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .booking-info .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .booking-info .info-row:last-child {
        border-bottom: none;
    }

    .booking-info .label {
        font-weight: 600;
        color: #6c757d;
    }

    .booking-info .value {
        font-weight: 700;
        color: #1a1a2e;
    }

    /* --- SELECT STYLING --- */
    .form-group {
        margin-bottom: 28px;
    }

    .form-group label {
        display: block;
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 12px;
    }

    .form-group select {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #f8f9fa;
        box-sizing: border-box;
        cursor: pointer;
    }

    .form-group select:focus {
        outline: none;
        border-color: #ff6a00;
        background: white;
        box-shadow: 0 0 0 4px rgba(255, 106, 0, 0.15);
    }

    /* --- BUTTON --- */
    .btn-submit {
        width: 100%;
        padding: 20px;
        background: linear-gradient(135deg, #ff6a00, #ff914d);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 30px;
        box-shadow: 0 10px 30px rgba(255, 106, 0, 0.4);
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 45px rgba(255, 106, 0, 0.5);
    }

    /* --- STATUS COLORS --- */
    .status-pending { color: #ffc107; }
    .status-approved { color: #28a745; }
    .status-completed { color: #17a2b8; }
    .status-cancelled { color: #dc3545; }

    /* --- RESPONSIVE --- */
    @media (max-width: 1250px) {
        .page-content {
            padding: 40px 20px;
            max-width: 100%;
        }
        .form-card {
            padding: 40px 50px;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        .form-card {
            padding: 30px;
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
        <a href="bookings.php">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<main class="page-content">
    <div class="page-header">
        <h1>Edit Booking</h1>
        <a href="bookings.php" class="back-link">← Back to Bookings</a>
    </div>

    <div class="form-card">
        <div class="form-header">
            <h2>Update Booking Status</h2>
            <p>Change the status for this booking</p>
        </div>

        <!-- Booking Info Display -->
        <div class="booking-info">
            <div class="info-row">
                <span class="label">Booking ID:</span>
                <span class="value">#<?= $booking['booking_id'] ?></span>
            </div>
            <div class="info-row">
                <span class="label">Booking Date:</span>
                <span class="value"><?= date('M d, Y', strtotime($booking['booking_date'])) ?></span>
            </div>
            <div class="info-row">
                <span class="label">Return Date:</span>
                <span class="value"><?= date('M d, Y', strtotime($booking['return_date'])) ?></span>
            </div>
            <div class="info-row">
                <span class="label">Current Status:</span>
                <span class="value status-<?= strtolower($booking['status']) ?>"><?= $booking['status'] ?></span>
            </div>
        </div>

        <form method="post">
            <div class="form-group">
                <label for="status">Select New Status:</label>
                <select name="status" id="status" required>
                    <option value="Pending" <?= $booking['status']=="Pending"?"selected":"" ?>>Pending</option>
                    <option value="Approved" <?= $booking['status']=="Approved"?"selected":"" ?>>Approved</option>
                    <option value="Completed" <?= $booking['status']=="Completed"?"selected":"" ?>>Completed</option>
                    <option value="Cancelled" <?= $booking['status']=="Cancelled"?"selected":"" ?>>Cancelled</option>
                </select>
            </div>

            <button type="submit" name="update" class="btn-submit">Update Status</button>
        </form>
    </div>
</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>