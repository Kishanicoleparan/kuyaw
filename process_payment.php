<?php
session_start();
require_once "../db.php";

// Only customer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../my_bookings.php");
    exit();
}

$booking_id = (int)$_POST['booking_id'];
$amount = (float)$_POST['amount'];
$payment_method = $_POST['payment_method'];
$user_id = $_SESSION['id'];

// Validate booking belongs to user
$check = mysqli_query($conn, "
    SELECT * FROM bookings 
    WHERE booking_id = $booking_id AND id = $user_id
");

if (mysqli_num_rows($check) == 0) {
    header("Location: ../my_bookings.php");
    exit();
}

// Simulate payment processing
// In production, integrate with actual payment gateway (PayPal, Stripe, GCash API, etc.)

$transaction_id = 'TXN' . time() . rand(1000, 9999);
$payment_status = 'paid'; // In real API, check response

// Simulate API call delay
sleep(1);

// Payment successful - update database
$update = mysqli_query($conn, "
    UPDATE bookings SET 
        payment_status = '$payment_status',
        payment_method = '$payment_method',
        transaction_id = '$transaction_id',
        status = 'Confirmed'
    WHERE booking_id = $booking_id
");

if ($update) {
    // Log payment
    $log = mysqli_query($conn, "
        INSERT INTO payment_logs (booking_id, transaction_id, amount, payment_method, status)
        VALUES ($booking_id, '$transaction_id', $amount, '$payment_method', '$payment_status')
    ");
    
    header("Location: payment_success.php?booking_id=$booking_id&txn_id=$transaction_id");
} else {
    header("Location: payment_failed.php?booking_id=$booking_id&error=" . mysqli_error($conn));
}
?>