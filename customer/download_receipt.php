<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once "../db.php";

// ⚠️ Make sure this path matches your folder
require_once "../vendor/dompdf/autoload.inc.php"; 
use Dompdf\Dompdf;

// Only customer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['booking_id'])) die("Booking ID missing");
$booking_id = intval($_GET['booking_id']);

// Fetch booking + car info
$result = mysqli_query($conn, "
    SELECT b.booking_id, b.booking_date, b.return_date, b.status, b.total_price,
           b.payment_status, b.payment_method, b.payment_date,
           c.car_name, c.brand, c.price_per_day, c.car_image
    FROM bookings b
    JOIN cars c ON b.car_id = c.car_id
    WHERE b.booking_id = $booking_id AND b.id = {$_SESSION['id']}
");
$booking = mysqli_fetch_assoc($result);
if (!$booking) die("Booking not found");

// Calculate rental days and totals
$start = new DateTime($booking['booking_date']);
$end = new DateTime($booking['return_date']);
$days = $end->diff($start)->days + 1;
$subtotal = $booking['price_per_day'] * $days;
$tax_rate = 0.12;
$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax;

// Generate HTML for PDF
$html = '
<h1 style="text-align:center; color:#ff6a00;">UrbanDrive Invoice</h1>
<p style="text-align:center;">Booking #' . $booking['booking_id'] . '</p>
<p><strong>Customer:</strong> ' . ($_SESSION['username'] ?? 'Customer') . '</p>
<p><strong>Car:</strong> ' . htmlspecialchars($booking['brand'] . ' ' . $booking['car_name']) . '</p>
<p><strong>Rental Period:</strong> ' . date('M d, Y', strtotime($booking['booking_date'])) . ' → ' . date('M d, Y', strtotime($booking['return_date'])) . ' (' . $days . ' days)</p>
<p><strong>Status:</strong> ' . $booking['status'] . '</p>
<p><strong>Payment Status:</strong> ' . $booking['payment_status'] . '</p>
<p><strong>Payment Date:</strong> ' . (!empty($booking['payment_date']) ? date('M d, Y H:i', strtotime($booking['payment_date'])) : 'N/A') . '</p>

<table border="1" cellpadding="10" cellspacing="0" width="100%" style="margin-top:20px; border-collapse:collapse;">
<tr style="background:#ff6a00; color:#fff;">
<th>Description</th><th>Days</th><th>Price/Day (₱)</th><th>Amount (₱)</th>
</tr>
<tr>
<td>' . htmlspecialchars($booking['brand'] . ' ' . $booking['car_name']) . ' Rental</td>
<td>' . $days . '</td>
<td>' . number_format($booking['price_per_day'], 2) . '</td>
<td>' . number_format($subtotal, 2) . '</td>
</tr>
<tr>
<td colspan="3"><strong>Tax (12%)</strong></td>
<td>' . number_format($tax, 2) . '</td>
</tr>
<tr>
<td colspan="3"><strong>Total</strong></td>
<td>' . number_format($total, 2) . '</td>
</tr>
</table>
';

// Initialize DOMPDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF to browser
$dompdf->stream("Invoice_Booking_" . $booking['booking_id'] . ".pdf", ["Attachment" => true]);
exit;
?>