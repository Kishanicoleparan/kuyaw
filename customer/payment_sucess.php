<?php
session_start();
require_once "../db.php";

if (!isset($_GET['booking_id'])) {
    die("Invalid booking ID");
}

$booking_id = intval($_GET['booking_id']);
$user_id = intval($_SESSION['id']);

// Verify booking
$stmt = mysqli_prepare($conn, "SELECT * FROM bookings WHERE booking_id = ? AND id = ?");
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) {
    die("Booking not found");
}

// ✅ UPDATE PAYMENT STATUS TO PAID
if ($booking['payment_status'] === 'pending') {
    $update_stmt = mysqli_prepare($conn, "
        UPDATE bookings 
        SET payment_status = 'paid', 
            payment_method = 'PayMongo',
            payment_date = NOW()
        WHERE booking_id = ?
    ");
    mysqli_stmt_bind_param($update_stmt, "i", $booking_id);
    mysqli_stmt_execute($update_stmt);
    
    // Add to payment history
    $insert_stmt = mysqli_prepare($conn, "
        INSERT INTO payment_history (booking_id, id, amount, payment_method, transaction_id, status) 
        VALUES (?, ?, ?, 'PayMongo', ?, 'success')
    ");
    $transaction_id = $_GET['session_id'] ?? 'paymongo_' . $booking_id;
    mysqli_stmt_bind_param($insert_stmt, "isds", $booking_id, $user_id, $booking['total_price'], $transaction_id);
    mysqli_stmt_execute($insert_stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful! | UrbanDrive</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            max-width: 600px; 
            margin: 50px auto; 
            padding: 40px; 
            text-align: center; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-container { 
            background: rgba(255,255,255,0.95); 
            padding: 50px; 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }
        .success-icon { 
            font-size: 80px; 
            color: #27ae60; 
            margin-bottom: 20px; 
        }
        .success-title { 
            color: #2c3e50; 
            font-size: 32px; 
            margin-bottom: 10px; 
            font-weight: 700; 
        }
        .success-text { 
            color: #7f8c8d; 
            font-size: 18px; 
            margin-bottom: 30px; 
        }
        .booking-details { 
            background: #f8f9fa; 
            padding: 25px; 
            border-radius: 15px; 
            margin: 30px 0; 
            text-align: left; 
        }
        .amount { 
            font-size: 28px; 
            font-weight: 800; 
            color: #e74c3c; 
            margin: 10px 0; 
        }
        .btn { 
            display: inline-block; 
            padding: 15px 30px; 
            margin: 10px; 
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 16px; 
            transition: all 0.3s; 
            border: none; 
            cursor: pointer; 
        }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; transform: translateY(-2px); }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #219a52; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-text">Your booking has been confirmed and payment received.</p>
        
        <div class="booking-details">
            <h3>Booking #<?= $booking_id ?></h3>
            <p><strong>Car:</strong> <?= htmlspecialchars($booking['car_name'] ?? 'N/A') ?></p>
            <div class="amount">₱<?= number_format($booking['total_price'], 2) ?></div>
            <p><strong>Status:</strong> <span style="color: #27ae60; font-weight: bold;">PAID</span></p>
        </div>
        
        <a href="my_bookings.php?msg=paid" class="btn btn-primary">View My Bookings</a>
        <a href="payment_receipt.php?booking_id=<?= $booking_id ?>" class="btn btn-success">View Receipt</a>
    </div>
</body>
</html>