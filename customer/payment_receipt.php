<?php
session_start();
require_once "../db.php";

$booking_id = intval($_GET['booking_id'] ?? 0);
$user_id = intval($_SESSION['id'] ?? 0);

$stmt = mysqli_prepare($conn, "
    SELECT b.*, c.car_name, c.brand, u.name as customer_name 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.car_id 
    JOIN users u ON b.id = u.id 
    WHERE b.booking_id = ? AND b.id = ?
");
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking || $booking['payment_status'] !== 'paid') {
    die("Receipt not found or payment not completed");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?= $booking_id ?> | UrbanDrive</title>
    <style>
        @media print { body { margin: 0; } .no-print { display: none; } }
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 30px; }
        .receipt { border: 2px solid #333; padding: 40px; border-radius: 10px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 40px; }
        .receipt-number { background: #3498db; color: white; padding: 15px; border-radius: 10px; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 30px 0; }
        .total { background: #e8f5e8; padding: 25px; border-radius: 15px; text-align: center; border: 3px solid #27ae60; }
        .amount-big { font-size: 36px; font-weight: 800; color: #27ae60; }
        .btn { padding: 12px 25px; background: #3498db; color: white; text-decoration: none; border-radius: 8px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #2980b9; }
        .no-print { text-align: center; margin: 30px 0; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>🏎️ UrbanDrive Car Rental</h1>
            <p>Receipt | <?= date('F j, Y g:i A') ?></p>
        </div>
        
        <div class="receipt-number">Receipt #<?= $booking_id ?></div>
        
        <div class="details-grid">
            <div>
                <h3>Customer Details</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($booking['customer_name']) ?></p>
                <p><strong>Booking ID:</strong> #<?= $booking_id ?></p>
                <p><strong>Date:</strong> <?= date('M d, Y', strtotime($booking['booking_date'])) ?></p>
                <p><strong>Period:</strong> <?= date('M d, Y', strtotime($booking['booking_date'])) ?> to <?= date('M d, Y', strtotime($booking['return_date'])) ?></p>
            </div>
            
            <div>
                <h3>Vehicle Details</h3>
                <p><strong>Car:</strong> <?= htmlspecialchars($booking['car_name']) ?></p>
                <p><strong>Brand:</strong> <?= htmlspecialchars($booking['brand']) ?></p>
                <p><strong>Rate:</strong> ₱<?= number_format($booking['price_per_day'], 2) ?>/day</p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method']) ?></p>
            </div>
        </div>
        
        <div class="total">
            <h2>Total Amount Paid</h2>
            <div class="amount-big">₱<?= number_format($booking['total_price'], 2) ?></div>
            <p>Payment Status: <span style="color: #27ae60; font-weight: bold; font-size: 20px;">PAID</span></p>
        </div>
        
        <div class="no-print">
            <button class="btn" onclick="window.print()">🖨️ Print Receipt</button>
            <a href="my_bookings.php" class="btn">← Back to Bookings</a>
        </div>
    </div>
</body>
</html>