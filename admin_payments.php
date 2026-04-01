
<?php
// admin/payments.php
require_once 'config.php';
session_start();

// Filter by status
$status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$sql = "SELECT * FROM payments WHERE 1=1";
$params = [];

if ($status !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status;
}

if (!empty($search)) {
    $sql .= " AND (booking_id LIKE ? OR paymongo_reference_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Records</title>
</head>
<body>
    <h1>💳 Payment Records</h1>
    
    <!-- Filter Form -->
    <form method="GET" action="payments.php">
        <select name="status">
            <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
            <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Paid</option>
            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="failed" <?php echo $status === 'failed' ? 'selected' : ''; ?>>Failed</option>
        </select>
        <input type="text" name="search" placeholder="Search booking ID or reference" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Filter</button>
    </form>
    
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
                <td><?php echo date('M d, Y g:i A', strtotime($payment['created_at'])); ?></td>
                <td>
                    <a href="receipt.php?id=<?php echo $payment['id']; ?>">View</a>
                    <a href="print_receipt.php?id=<?php echo $payment['id']; ?>">Print</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>