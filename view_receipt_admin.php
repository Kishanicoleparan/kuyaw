<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$booking_id = intval($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "
    SELECT b.*, c.car_name, c.brand, c.price_per_day, u.name as customer_name, u.email, u.phone, c.car_image
    FROM bookings b 
    JOIN cars c ON b.car_id = c.car_id 
    JOIN users u ON b.id = u.id 
    WHERE b.booking_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking || $booking['payment_status'] !== 'paid') {
    die("Receipt not found or payment not completed");
}

// Helper function for safe date formatting
function safeDate($date) {
    return $date ? date('M d, Y', strtotime($date)) : 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?= $booking_id ?> | UrbanDrive Admin</title>
    <link rel="stylesheet" href="adashboard.css">
    <style>
        .page-content { max-width: 900px; margin: 40px auto; padding: 0 30px; }
        .page-title { 
            text-align: center; 
            font-size: 36px; 
            margin-bottom: 40px; 
            color: #2b1d16; 
            border-bottom: 3px solid #ff6a00; 
            padding-bottom: 20px;
        }
        
        /* Receipt Card */
        .receipt-container { 
            background: #ffffff; 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            border: 1px solid #eaeaea; 
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Header */
        .receipt-header { 
            background: linear-gradient(135deg, #3498db, #2980b9); 
            color: white; 
            padding: 40px 30px; 
            text-align: center;
        }
        .receipt-title { 
            font-size: 32px; 
            font-weight: 800; 
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .receipt-number { 
            font-size: 24px; 
            font-weight: 700; 
            opacity: 0.95;
        }
        .issued-date { 
            font-size: 16px; 
            opacity: 0.9; 
            margin-top: 10px;
        }
        
        /* Details Grid */
        .details-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 40px; 
            padding: 40px 30px; 
            background: #fafbfc;
        }
        .detail-section { }
        .detail-section h3 { 
            color: #2c3e50; 
            margin-bottom: 25px; 
            padding-bottom: 15px; 
            border-bottom: 3px solid #ff6a00; 
            font-size: 22px;
            font-weight: 700;
        }
        .detail-item { 
            display: flex; 
            justify-content: space-between; 
            margin: 18px 0; 
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 16px;
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-label { 
            font-weight: 600; 
            color: #555; 
            min-width: 150px;
        }
        .detail-value { 
            color: #2c3e50; 
            font-weight: 600; 
            text-align: right;
            font-size: 16px;
        }
        
        /* Total Section */
        .total-section { 
            background: linear-gradient(135deg, #e8f5e8, #d5f4d5); 
            padding: 50px 40px; 
            text-align: center; 
            border-top: 4px solid #27ae60;
        }
        .total-title { 
            color: #27ae60; 
            font-size: 28px; 
            font-weight: 700; 
            margin-bottom: 20px;
        }
        .amount-big { 
            font-size: 52px; 
            font-weight: 900; 
            color: #27ae60; 
            margin: 25px 0;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.1);
        }
        .status-paid { 
            color: #27ae60; 
            font-weight: 800; 
            font-size: 26px; 
            text-transform: uppercase;
            letter-spacing: 2px;
            background: rgba(39, 174, 96, 0.15);
            padding: 15px 30px;
            border-radius: 30px;
            display: inline-block;
            margin-top: 15px;
            border: 2px solid rgba(39, 174, 96, 0.3);
        }
        
        /* Action Buttons */
        .action-buttons { 
            padding: 40px 30px; 
            text-align: center; 
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        .btn { 
            display: inline-block; 
            padding: 16px 40px; 
            margin: 12px 10px; 
            border-radius: 12px; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 16px; 
            transition: all 0.3s; 
            border: none; 
            cursor: pointer; 
            min-width: 200px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .btn-print { 
            background: linear-gradient(135deg, #27ae60, #219a52); 
            color: white; 
        }
        .btn-print:hover { 
            background: linear-gradient(135deg, #219a52, #1e7e34); 
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(39, 174, 96, 0.4);
        }
        .btn-back { 
            background: linear-gradient(135deg, #3498db, #2980b9); 
            color: white; 
        }
        .btn-back:hover { 
            background: linear-gradient(135deg, #2980b9, #1e3c72); 
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.4);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .details-grid { grid-template-columns: 1fr; gap: 30px; }
            .page-content { padding: 0 20px; margin: 20px auto; }
            .amount-big { font-size: 42px; }
            .receipt-container { margin: 0 10px; }
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .receipt-container { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>

<!-- ADMIN HEADER -->
<header class="admin-header">
    <div class="logo">Urban<span>Drive</span> Admin</div>
    <nav>
        <a href="reports.php">Dashboard</a>
        <a href="bookings.php" class="active">Bookings</a>
        <a href="addcar.php">Add Car</a>
        <a href="viewcar.php">View Cars</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<div class="page-content">
    <h1 class="page-title">📄 Receipt #<?= $booking_id ?></h1>
    
    <div class="receipt-container">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="receipt-title">🏎️ Car Rental Receipt</div>
            <div class="receipt-number">Receipt #<?= $booking_id ?></div>
            <div class="issued-date">Issued: <?= date('F j, Y g:i A') ?> | Admin View</div>
        </div>
        
        <!-- Details -->
        <div class="details-grid">
            <div class="detail-section">
                <h3>👤 Customer Information</h3>
                <div class="detail-item">
                    <span class="detail-label">Full Name:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['email'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['phone'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Booking ID:</span>
                    <span class="detail-value">#<?= $booking_id ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Booking Date:</span>
                    <span class="detail-value"><?= safeDate($booking['booking_date']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Rental Period:</span>
                    <span class="detail-value"><?= safeDate($booking['booking_date']) ?> → <?= safeDate($booking['return_date']) ?></span>
                </div>
            </div>
            
            <div class="detail-section">
                <h3>🚗 Vehicle & Payment Details</h3>
                <div class="detail-item">
                    <span class="detail-label">Car Model:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['car_name'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Brand:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['brand'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Daily Rate:</span>
                    <span class="detail-value">₱<?= number_format($booking['price_per_day'] ?? 0, 2) ?>/day</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['payment_method'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value" style="color: #27ae60; font-weight: 800;">PAID ✓</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Booking Status:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['status'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
        
        <!-- Total -->
        <div class="total-section">
            <div class="total-title">💰 Total Amount Paid</div>
            <div class="amount-big">₱<?= number_format($booking['total_price'], 2) ?></div>
            <div class="status-paid">PAID & CONFIRMED ✓</div>
        </div>
        
        <!-- Admin Action Buttons -->
        <div class="action-buttons no-print">
            <button class="btn btn-print" onclick="window.print()">🖨️ Print Receipt</button>
            <a href="bookings.php" class="btn btn-back">← Back to All Bookings</a>
            <a href="edit_booking.php?id=<?= $booking_id ?>" class="btn" style="background: linear-gradient(135deg, #ff6a00, #ff914d); color: white;">✏️ Edit Booking</a>
        </div>
    </div>
</div>

</body>
</html>