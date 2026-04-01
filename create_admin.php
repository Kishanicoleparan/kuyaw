<?php
require "db.php";

$name = "Admin";
$email = "admin@gmail.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$sql = "INSERT INTO users (name, email, password, role)
        VALUES ('$name', '$email', '$password', '$role')";

if (mysqli_query($conn, $sql)) {
    echo "✅ Admin account created successfully!";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
