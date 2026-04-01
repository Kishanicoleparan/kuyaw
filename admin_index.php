<?php
// admin/index.php
require_once 'config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get all payments
$stmt = $pdo->prepare("SELECT * FROM payments ORDER BY created_at DESC");
$stmt->execute();
$payments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Payments</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #333; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .status-paid { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-failed { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>📊 Admin Dashboard - Payments</h1>
    <a href="logout.php">Logout</a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Booking ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Reference</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?php echo $payment['id']; ?></td>
                <td><?php echo $payment['booking_id']; ?></td>
                <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                <td class="status-<?php echo $payment['status']; ?>">
                    <?php echo strtoupper($payment['status']); ?>
                </td>
                <td><?php echo $payment['paymongo_reference_id']; ?></td>
                <td><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                <td>
                    <a href="receipt.php?id=<?php echo $payment['id']; ?>">View Receipt</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h3>Total Revenue: ₱<?php 
        $stmt = $pdo->query("SELECT SUM(amount) as total FROM payments WHERE status = 'paid'");
        $result = $stmt->fetch();
        echo number_format($result['total'] ?? 0, 2);
    ?></h3>
</body>
</html>