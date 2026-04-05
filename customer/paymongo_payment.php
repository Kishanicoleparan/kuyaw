<?php
session_start();
require_once "../db.php";

// Only customers allowed
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if booking_id is provided
if (!isset($_GET['booking_id'])) {
    die("Invalid booking ID");
}

$booking_id = intval($_GET['booking_id']);
$user_id = intval($_SESSION['id']);

// Verify booking exists, belongs to user, and is pending
$stmt = mysqli_prepare($conn, "SELECT * FROM bookings WHERE booking_id = ? AND id = ? AND payment_status = 'pending'");
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Booking not found or already paid");
}

// Convert amount to centavos (PayMongo requires smallest currency unit)
$amount = (int)($booking['total_price'] * 100);

// PayMongo Secret Key (test mode)
$secret = '';

// Success and cancel URLs
$success_url = "http://localhost/php_kapoy/customer/payment_success.php?booking_id=$booking_id";
$cancel_url = "http://localhost/php_kapoy/customer/my_bookings.php";

// Prepare line item description - CORRECTED FOR YOUR TABLE STRUCTURE
$car_details = "";
if (isset($booking['car_id']) && !empty($booking['car_id'])) {
    $car_query = mysqli_prepare($conn, "SELECT car_name, brand FROM cars WHERE car_id = ?");
    mysqli_stmt_bind_param($car_query, "i", $booking['car_id']);
    mysqli_stmt_execute($car_query);
    $car_result = mysqli_stmt_get_result($car_query);
    $car = mysqli_fetch_assoc($car_result);
    
    if ($car) {
        $car_details = $car['brand'] . " " . $car['car_name'] . " - ";
    }
    mysqli_stmt_close($car_query);
}

// Using PayMongo CHECKOUT API
$data = [
    "data" => [
        "attributes" => [
            "amount" => $amount,
            "currency" => "PHP",
            "description" => "Car Rental Booking #$booking_id",
            "payment_method_types" => ["card", "gcash"],
            "send_email_receipt" => true,
            "show_description" => true,
            "show_line_items" => true,
            "success_url" => $success_url,
            "cancel_url" => $cancel_url,
            "metadata" => [
                "booking_id" => (string)$booking_id,
                "user_id" => (string)$user_id,
                "pickup_date" => $booking['pickup_date'],
                "return_date" => $booking['return_date']
            ],
            "line_items" => [
                [
                    "name" => "Car Rental - " . $car_details . "Booking #$booking_id",
                    "amount" => $amount,
                    "currency" => "PHP",
                    "quantity" => 1,
                    "description" => "Pickup: " . date('M d, Y', strtotime($booking['pickup_date'])) . " | Return: " . date('M d, Y', strtotime($booking['return_date']))
                ]
            ]
        ]
    ]
];

// Initialize cURL
$ch = curl_init("https://api.paymongo.com/v1/checkout_sessions");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($secret . ":")
]);

// Execute cURL request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}

curl_close($ch);

// Parse response
$result = json_decode($response, true);

// Handle API errors
if ($http_code !== 200) {
    echo "<div style='padding: 20px; font-family: Arial;'>";
    echo "<h2>Payment Error</h2>";
    echo "<p>Unable to process payment. Please try again later.</p>";
    echo "<p><strong>Debug Info:</strong></p>";
    echo "<pre>HTTP Code: $http_code\nResponse: ";
    print_r($result);
    echo "</pre>";
    echo "<a href='my_bookings.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Back to My Bookings</a>";
    echo "</div>";
    exit;
}

// Check if checkout session was created successfully
if (!isset($result['data']['attributes']['checkout_url'])) {
    echo "<div style='padding: 20px; font-family: Arial;'>";
    echo "<h2>Payment Error</h2>";
    echo "<p>Invalid response from payment gateway.</p>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    echo "<a href='my_bookings.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Back to My Bookings</a>";
    echo "</div>";
    exit;
}

// Get checkout URL and session ID
$checkout_url = $result['data']['attributes']['checkout_url'];
$session_id = $result['data']['id'];

// Store session ID in bookings table for reference
$update_stmt = mysqli_prepare($conn, "UPDATE bookings SET transaction_id = ? WHERE booking_id = ?");
mysqli_stmt_bind_param($update_stmt, "si", $session_id, $booking_id);
mysqli_stmt_execute($update_stmt);

// Redirect to PayMongo checkout page
header("Location: " . $checkout_url);
exit();
?>