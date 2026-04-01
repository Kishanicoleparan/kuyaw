<?php
include 'db.php';

if (isset($_POST['register'])) {

    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Always customer
    $role = 'customer';

    // Check if email exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists!";
    } else {

        $sql = "INSERT INTO users (name, email, password, role)
                VALUES ('$name', '$email', '$password', '$role')";

        if (mysqli_query($conn, $sql)) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - UrbanDrive</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="#">Vehicles</a>
        <a href="#">Pricing</a>
        <a href="#">Contact</a>
        <a href="login.php" class="btn-login">Login</a>
    </nav>
</header>

<!-- HERO -->
<section class="hero">

    <!-- REGISTER BOX -->
    <div class="auth-box">
        <h2>Create account ✨</h2>
        <p>Please fill in your details</p>

        <?php if (!empty($error)) { ?>
            <div class="error"><?= $error ?></div>
        <?php } ?>

        <form method="post">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="register">Sign up</button>
        </form>

        <div class="switch-link">
            Already have an account?
            <a href="login.php">Log in</a>
        </div>
    </div>

</section>

</body>
</html>
