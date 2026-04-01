<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

include 'db.php';

$success = "";
$error = "";

/* ==============================
   CHANGE PASSWORD
============================== */
if(isset($_POST['change_password'])){

    $current = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    $admin_id = $_SESSION['id'];

    $check = mysqli_query($conn, "SELECT password FROM users WHERE id='$admin_id'");
    $row = mysqli_fetch_assoc($check);

    if($row['password'] != $current){
        $error = "Current password is incorrect!";
    } elseif($new != $confirm){
        $error = "New passwords do not match!";
    } else {
        mysqli_query($conn, "UPDATE users SET password='$new' WHERE id='$admin_id'");
        $success = "Password updated successfully!";
    }
}

/* ==============================
   SAVE COMPANY INFO
============================== */
if(isset($_POST['save_company'])){

    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $company_email = mysqli_real_escape_string($conn, $_POST['company_email']);
    $company_phone = mysqli_real_escape_string($conn, $_POST['company_phone']);

    mysqli_query($conn, "UPDATE settings 
        SET company_name='$company_name',
            company_email='$company_email',
            company_phone='$company_phone'
        WHERE id=1");

    $success = "Company information updated!";
}

/* ==============================
   SAVE BOOKING RULES
============================== */
if(isset($_POST['save_rules'])){

    $max_days = mysqli_real_escape_string($conn, $_POST['max_days']);
    $cancellation = mysqli_real_escape_string($conn, $_POST['cancellation_policy']);

    mysqli_query($conn, "UPDATE settings 
        SET max_booking_days='$max_days',
            cancellation_policy='$cancellation'
        WHERE id=1");

    $success = "Booking rules updated!";
}

/* ==============================
   GET SETTINGS DATA
============================== */
$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings WHERE id=1"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings | UrbanDrive Admin</title>

<link rel="stylesheet" href="adashboard.css">
<link rel="stylesheet" href="settings.css">

</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span> Admin</div>
    <nav>
         <a href="reports.php">Dashboard</a>
        <a href="addcar.php">Add Car</a>
        <a href="viewcar.php">View Cars</a>
        <a href="bookings.php">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="settings.php" class="active">Settings</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<div class="page-content">
    <div class="settings-container">

        <?php if($success != "") echo "<div class='success'>$success</div>"; ?>
        <?php if($error != "") echo "<div class='error'>$error</div>"; ?>

        <!-- CHANGE PASSWORD -->
        <div class="settings-card">
            <h3>🔐 Change Password</h3>
            <form method="post">
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <button type="submit" name="change_password">Update Password</button>
            </form>
        </div>

        <!-- COMPANY INFO -->
        <div class="settings-card">
            <h3>🏢 Company Information</h3>
            <form method="post">
                <input type="text" name="company_name" 
                    value="<?= $settings['company_name'] ?? '' ?>" 
                    placeholder="Company Name">

                <input type="email" name="company_email" 
                    value="<?= $settings['company_email'] ?? '' ?>" 
                    placeholder="Company Email">

                <input type="text" name="company_phone" 
                    value="<?= $settings['company_phone'] ?? '' ?>" 
                    placeholder="Company Phone">

                <button type="submit" name="save_company">Save Company Info</button>
            </form>
        </div>

        <!-- BOOKING RULES -->
        <div class="settings-card">
            <h3>📅 Booking Rules</h3>
            <form method="post">
                <input type="number" name="max_days" 
                    value="<?= $settings['max_booking_days'] ?? '' ?>" 
                    placeholder="Maximum Booking Days">

                <select name="cancellation_policy">
                    <option value="Allowed" 
                        <?= ($settings['cancellation_policy'] ?? '') == 'Allowed' ? 'selected' : '' ?>>
                        Cancellation Allowed
                    </option>

                    <option value="Not Allowed" 
                        <?= ($settings['cancellation_policy'] ?? '') == 'Not Allowed' ? 'selected' : '' ?>>
                        Cancellation Not Allowed
                    </option>
                </select>

                <button type="submit" name="save_rules">Save Booking Rules</button>
            </form>
        </div>

    </div>
</div>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>
