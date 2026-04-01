<?php
// success.php
require_once '../config.php';

// Get booking_id from URL
$booking_id = $_GET['booking_id'] ?? '';

// Get payment details from database
$stmt = $pdo->prepare("SELECT * FROM payments WHERE booking_id = ?");
$stmt->execute([$booking_id]);
$payment = $stmt->fetch();

if (!$payment) {
    die("❌ Payment not found");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .receipt { border: 2px solid #333; padding: 30px; max-width: 500px; margin: 50px auto; background: white; }
        .status-paid { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-failed { color: red; font-weight: bold; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 30px; border-top: 2px solid #333; padding-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>🧾 PAYMENT RECEIPT</h2>
            <p>Official Payment Confirmation</p>
        </div>
        
        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($payment['booking_id']); ?></p>
        <p><strong>Amount:</strong> ₱<?php echo number_format($payment['amount'], 2); ?></p>
        <p><strong>Status:</strong> 
            <span class="status-<?php echo $payment['status']; ?>">
                <?php echo strtoupper($payment['status']); ?>
            </span>
        </p>
        <p><strong>Reference Number:</strong> <?php echo htmlspecialchars($payment['paymongo_reference_id']); ?></p>
        <p><strong>Payment Link ID:</strong> <?php echo htmlspecialchars($payment['paymongo_link_id']); ?></p>
        <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($payment['created_at'])); ?></p>
        
        <div class="footer">
            <p><em>Thank you for your payment!</em></p>
            <a href="../customer/test_api.php" class="btn">Create New Payment</a>
            <a href="../admin/payments.php" class="btn">Admin Dashboard</a>
        </div>
    </div>
</body>
</html>