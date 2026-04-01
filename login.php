<?php
session_start();
include "db.php";

if (isset($_POST['login'])) {

    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['id']   = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: reports.php");
        } else {
            header("Location: customer/dashboard.php");
        }
        exit();

    } else {
        $error = "Wrong email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | UrbanDrive</title>
    
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>

<!-- NAVBAR (KEEP THIS) -->
<nav class="navbar">
    <div class="logo">Urban<span>Drive</span></div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="vehicles.php">Vehicles</a></li>
        <li><a href="pricing.php">Pricing</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a class="login-btn" href="login.php">Login</a></li>
    </ul>
</nav>

<!-- HERO BACKGROUND -->
<section class="hero login-hero">

    <!-- FLOATING LOGIN BOX -->
    <div class="login-card">
        <h2>Welcome back 👋</h2>
        <p>Please enter your details</p>

        <?php if (!empty($error)) { ?>
            <div class="error"><?= $error ?></div>
        <?php } ?>

        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Log in</button>
        </form>

        <p class="register-text">
            Don’t have an account? <a href="register.php">Sign up</a>
        </p>
    </div>

</section>

</body>


</body>
</html>
