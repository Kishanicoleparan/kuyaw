<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'];
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$id"));

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    mysqli_query($conn, "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$id");
    header("Location: customers.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Customer | UrbanDrive Admin</title>
<link rel="stylesheet" href="adashboard.css">
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        min-height: 100vh;
    }

 .page-content {
    padding: 50px 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 36px;
    color: #1a1a2e;
    margin: 0;
    font-weight: 800;
}

.back-link {
    background: white;
    color: #1a1a2e;
    padding: 14px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.back-link:hover {
    background: #ff6a00;
    color: white;
}

/* --- WIDE FORM CARD --- */
.form-card {
    background: white;
    border-radius: 24px;
    padding: 60px 80px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.12);
    border: 1px solid #eee;
}

.form-header {
    text-align: center;
    margin-bottom: 40px;
}

.form-header h2 {
    font-size: 30px;
    color: #1a1a2e;
    margin: 0 0 12px 0;
    font-weight: 800;
}

.form-header p {
    color: #6c757d;
    margin: 0;
    font-size: 16px;
}

.form-group {
    margin-bottom: 28px;
}

.form-group label {
    display: block;
    font-size: 16px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 12px;
}

.form-group input {
    width: 100%;
    padding: 18px 22px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 17px;
    transition: all 0.3s ease;
    background: #f8f9fa;
    box-sizing: border-box;
}

.form-group input:focus {
    outline: none;
    border-color: #ff6a00;
    background: white;
    box-shadow: 0 0 0 4px rgba(255, 106, 0, 0.15);
}

.btn-submit {
    width: 100%;
    padding: 20px;
    background: linear-gradient(135deg, #ff6a00, #ff914d);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 30px;
    box-shadow: 0 10px 30px rgba(255, 106, 0, 0.4);
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 45px rgba(255, 106, 0, 0.5);
}

/* --- RESPONSIVE --- */
@media (max-width: 1250px) {
    .page-content {
        padding: 40px 20px;
        max-width: 100%;
    }
    .form-card {
        padding: 40px 50px;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    .form-card {
        padding: 30px;
    }
}
</style>
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
        <a href="settings.php">Settings</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<main class="page-content">
    <div class="page-header">
        <h1>Edit Customer</h1>
        <a href="customers.php" class="back-link">← Back</a>
    </div>
<div class="form-card">
    <div class="form-header">
        <h2>Update Information</h2>
        <p>Edit the customer details below</p>
    </div>
    
    <form method="post">
        
        <div class="form-group">
            <label for="name">Customer Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($customer['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($customer['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
        </div>

        <button type="submit" name="update" class="btn-submit">Save Changes</button>
    </form>
</div>
</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>