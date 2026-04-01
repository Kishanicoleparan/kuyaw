<?php
session_start();
require_once "../db.php";

// Only customers
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['booking_id'])) {
    die("Invalid booking ID");
}

$booking_id = intval($_GET['booking_id']);
$user_id = intval($_SESSION['id']);

// ✅ SECURE: Prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM bookings WHERE booking_id = ? AND id = ? AND payment_status = 'pending'");
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Booking not found or already paid");
}

// Convert to centavos
$amount = (int)($booking['total_price'] * 100);

// PayMongo Secret Key (from DB or hardcoded for test)
$secret = ''; // ✅ Your test key

// URLs - CHANGE "your_project" to your folder name!
$success_url = "http://localhost/php_kapoy/customer/payment_success.php?booking_id=$booking_id"; 
$back_url = "http://localhost/php_kapoy/customer/my_bookings.php";

// ✅ FIXED: Correct PayMongo endpoint
$data = [
    "data" => [
        "attributes" => [
            "amount" => $amount,
            "description" => "Car Rental Booking #$booking_id - CARD PAYMENT",
            "remarks" => "Credit/Debit Card Payment",
            "metadata" => [
                "booking_id" => (string)$booking_id,
                "user_id" => (string)$user_id
            ],
            // ✅ FORCE CARDS ONLY - NO QR!
            "payment_method_allowed" => ["card"],
            "success_url" => $success_url,
            "back_url" => $back_url,
            // ✅ HIDE QR - SHOW CARDS FIRST
            "payment_method_option" => [
                "card" => [
                    "allowed_brands" => ["visa", "mastercard", "jcb"]
                ]
            ]
        ]
    ]
];


// cURL - ✅ FIXED ENDPOINT
$ch = curl_init("https://api.paymongo.com/v1/links"); // ← CORRECT ENDPOINT

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($secret . ":")
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}

curl_close($ch);

$result = json_decode($response, true);

// After curl_exec - REPLACE this block:
if ($http_code !== 200 || isset($result['data']['errors'])) {  // ← CHANGED TO 200
    echo "<pre>";
    echo "HTTP Code: $http_code\n";
    echo "Response: ";
    print_r($result);
    echo "</pre>";
    exit;
}

// Store payment link ID
$payment_link_id = $result['data']['id'];
$update_stmt = mysqli_prepare($conn, "UPDATE bookings SET transaction_id = ? WHERE booking_id = ?");
mysqli_stmt_bind_param($update_stmt, "si", $payment_link_id, $booking_id);
mysqli_stmt_execute($update_stmt);

// ✅ Redirect to PayMongo
header("Location: " . $result['data']['attributes']['checkout_url']);
exit();
?>