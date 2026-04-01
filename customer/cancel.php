<?php
// cancel.php
require_once '../config.php';

$booking_id = $_GET['booking_id'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Cancelled</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .cancel { border: 2px solid #ff0000; padding: 30px; max-width: 500px; margin: 50px auto; background: white; text-align: center; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="cancel">
        <h2 style="color: red;">❌ Payment Cancelled</h2>
        <p>Your payment was cancelled.</p>
        <p>Booking ID: <?php echo htmlspecialchars($booking_id); ?></p>
        <a href="../customer/test_api.php" class="btn">Try Again</a>
    </div>
</body>
</html>