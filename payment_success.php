<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['booking_id'] ?? 0;
$txn_id = $_GET['txn_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful - UrbanDrive</title>
    <link rel="stylesheet" href="../adashboard.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 80px auto;
            text-align: center;
            padding: 40px;
        }
        
        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        .success-title {
            font-size: 32px;
            color: #2b1d16;
            margin-bottom: 10px;
        }
        
        .success-message {
            color: #666;
            margin-bottom: 30px;
            font-size: 18px;
        }
        
        .transaction-details {
            background: #f8f8f8;
            padding: 25px;
            border-radius: 15px;
            margin: 30px 0;
        }
        
        .transaction-details h3 {
            color: #2b1d16;
            margin-bottom: 20px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #ff6a00, #ff914d);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;