<?php
session_start();
require_once "../db.php";

// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid booking ID'); window.location.href='my_bookings.php';</script>";
    exit();
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['id'];

// Get the booking details
$check_booking = mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $booking_id AND id = $user_id");
$booking = mysqli_fetch_assoc($check_booking);

if (!$booking) {
    echo "<script>alert('Booking not found or you do not have permission'); window.location.href='my_bookings.php';</script>";
    exit();
}

// Check if booking can be cancelled (only pending bookings)
if ($booking['status'] !== 'Pending') {
    echo "<script>alert('Only pending bookings can be cancelled'); window.location.href='my_bookings.php';</script>";
    exit();
}

// Start transaction to ensure data consistency
mysqli_begin_transaction($conn);

try {
    // 1. Update booking status to Cancelled
    $update_booking = mysqli_query($conn, "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = $booking_id");
    
    if (!$update_booking) {
        throw new Exception("Failed to update booking status");
    }
    
    // 2. Update payment status to cancelled
    $update_payment = mysqli_query($conn, "UPDATE bookings SET payment_status = 'cancelled' WHERE booking_id = $booking_id");
    
    if (!$update_payment) {
        throw new Exception("Failed to update payment status");
    }
    
    // 3. Make the car available again
    $car_id = $booking['car_id'];
    $update_car = mysqli_query($conn, "UPDATE cars SET availability = 'available' WHERE car_id = $car_id");
    
    if (!$update_car) {
        throw new Exception("Failed to update car availability");
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    echo "<script>alert('Booking cancelled successfully!'); window.location.href='my_bookings.php';</script>";
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='my_bookings.php';</script>";
    exit();
}
?>