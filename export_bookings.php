<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}
include 'db.php';

// Set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=bookings_report.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output column headings
fputcsv($output, ['Booking ID', '', 'Car', 'Booking Date', 'Status']);

// Fetch bookings
$bookings = mysqli_query($conn, "SELECT b.booking_id, c.name as c.name, ca.car_name, b.booking_date, b.status
                                FROM bookings b 
                                JOIN users c ON b.id = c.id
                                JOIN cars ca ON b.car_id = ca.car_id
                                ORDER BY b.booking_date DESC");

// Output each row
while($row = mysqli_fetch_assoc($bookings)) {
    fputcsv($output, $row);
}

fclose($output);
exit();
