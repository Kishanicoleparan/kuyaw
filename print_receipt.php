<?php
// admin/print_receipt.php
require_once 'config.php';

$id = $_GET['id'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ?");
$stmt->execute([$id]);
$payment = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Receipt</title>
    <style>
        @media print {
            body { font-family: Arial, sans-serif; }
            .no-print { display: none; }
        }
        .receipt {
            border: 2px solid #000;
            padding: 30px;
            max-width: 600px;
            margin: 20px auto;
        }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .footer { text-align: center; margin-top: 30px; border-top: 2px solid #000; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()">🖨️ Print Receipt</button>
        <a href="payments.php">← Back to List</a>
    </div>
    
    <div class="receipt">
        <div class="header">
            <h1>OFFICIAL RECEIPT</h1>
            <p>Company Name</p>
            <p>Address Line 1</p>
            <p>Address Line 2</p>
        </div>
        
        <div style="margin: 20px 0;">
            <p><strong>Receipt No:</strong> <?php echo $payment['paymongo_reference_id']; ?></p>
            <p><strong>Date:</strong> <?php echo date('F j, Y, g:i A', strtotime($payment['created_at'])); ?></p>
            <p><strong>Booking ID:</strong> <?php echo $payment['booking_id']; ?></p>
        </div>
        
        <div style="border: 1px solid #000; padding: 10px; margin: 20px 0;">
            <p><strong>Description:</strong> Booking Payment</p>
            <p><strong>Amount:</strong> ₱<?php echo number_format($payment['amount'], 2); ?></p>
        </div>
        
        <div style="margin: 20px 0;">
            <p><strong>Status:</strong> <?php echo strtoupper($payment['status']); ?></p>
            <p><strong>Payment Method:</strong> PayMongo</p>
            <p><strong>Transaction ID:</strong> <?php echo $payment['paymongo_link_id']; ?></p>
        </div>
        
        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>This is an official receipt.</p>
        </div>
    </div>
</body>
</html>