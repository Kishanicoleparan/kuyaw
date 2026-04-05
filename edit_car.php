<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = intval($_GET['id']); // Secure ID
$car_query = mysqli_query($conn, "SELECT * FROM cars WHERE car_id=$id");
$car = mysqli_fetch_assoc($car_query);

if (!$car) {
    die("Car not found.");
}

if (isset($_POST['update'])) {
    $car_name = mysqli_real_escape_string($conn, $_POST['car_name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = $_POST['status'];

    $image_sql = "";
    if (!empty($_FILES['car_image']['name'])) {
        $image_name = time().'_'.basename($_FILES['car_image']['name']);
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        move_uploaded_file($_FILES['car_image']['tmp_name'], $target_dir.$image_name);

        if($car['car_image'] && file_exists($target_dir.$car['car_image'])){
            unlink($target_dir.$car['car_image']);
        }

        $image_sql = ", car_image='$image_name'";
    }

    mysqli_query($conn, "UPDATE cars SET car_name='$car_name', brand='$brand', price_per_day='$price', status='$status' $image_sql WHERE car_id=$id");
    header("Location: viewcar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Car | UrbanDrive Admin</title>
<link rel="stylesheet" href="adashboard.css">
<style>
body {
    background: #f8f9fa;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
}

.page-content {
    max-width: 1100px;
    margin: 60px auto;
    padding: 40px 30px;
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.page-header h1 {
    font-size: 36px;
    font-weight: 800;
    color: #1a1a2e;
}

.back-link {
    text-decoration: none;
    background: white;
    color: #1a1a2e;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.back-link:hover {
    background: #ff6a00;
    color: white;
}

/* Form + Plus Button Layout */
.form-card-wrapper {
    display: flex;
    gap: 40px;
    align-items: flex-start;
}

/* Form Card */
.form-card {
    flex: 1;
    background: white;
    padding: 60px 50px;
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.12);
    border: 1px solid #eee;
}

.form-header h2 {
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: 800;
    color: #1a1a2e;
}
.form-header p {
    color: #6c757d;
    margin-bottom: 30px;
    font-size: 16px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    font-weight: 700;
    margin-bottom: 8px;
    color: #1a1a2e;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 18px 22px;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    font-size: 16px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #ff6a00;
    background: white;
    box-shadow: 0 0 0 4px rgba(255,106,0,0.15);
}

/* Submit Button */
.btn-submit {
    width: 100%;
    padding: 20px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #ff6a00, #ff914d);
    color: white;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 20px;
    transition: all 0.3s ease;
}
.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(255,106,0,0.4);
}

/* Plus Button on Right (Large Square) */
.plus-button-wrapper {
    flex-shrink: 0;
    width: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 50px;
    background: #f8f9fa;
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.12);
    transition: transform 0.3s ease;
}

.plus-button {
    width: 100%;
    height: 0;
    padding-bottom: 100%;
    background: white;
    border: 4px dashed #3498db;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 80px;
    color: #3498db;
    font-weight: bold;
    cursor: pointer;
    border-radius: 24px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.plus-button img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 20px;
    display: <?= $car['car_image'] && file_exists('../uploads/'.$car['car_image']) ? 'block' : 'none' ?>;
    transition: transform 0.3s ease;
}

.plus-button::before {
    content: '+';
    font-size: 80px;
    color: #3498db;
    font-weight: bold;
    position: absolute;
    z-index: 2;
    <?= $car['car_image'] && file_exists('../uploads/'.$car['car_image']) ? 'opacity: 0;' : '' ?>
}

/* Hover Effects */
.plus-button-wrapper:hover .plus-button {
    transform: scale(1.05);
    box-shadow: 0 30px 70px rgba(0,0,0,0.15);
}
.plus-button:hover {
    border-color: #2980b9;
}
.file-upload-input {
    display: none;
}

/* Responsive */
@media (max-width: 1000px) {
    .form-card-wrapper {
        flex-direction: column;
        gap: 30px;
    }
    .plus-button-wrapper {
        width: 100%;
    }
    .plus-button {
        height: 0;
        padding-bottom: 100%;
        font-size: 48px;
    }
    .plus-button::before {
        font-size: 48px;
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
        <a href="viewcar.php" class="active">View Cars</a>
        <a href="bookings.php">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<main class="page-content">
    <div class="page-header">
        <h1>Edit Car</h1>
        <a href="viewcar.php" class="back-link">← Back to Cars</a>
    </div>

    <div class="form-card-wrapper">
        <!-- FORM LEFT -->
        <div class="form-card">
            <div class="form-header">
                <h2>Update Car Details</h2>
                <p>Modify the car information below</p>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="car_name">Car Model</label>
                    <input type="text" name="car_name" id="car_name" value="<?= htmlspecialchars($car['car_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="brand">Brand</label>
                    <input type="text" name="brand" id="brand" value="<?= htmlspecialchars($car['brand']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="price">Price Per Day (₱)</label>
                    <input type="number" name="price" id="price" step="0.01" value="<?= $car['price_per_day'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="Available" <?= $car['status']=="Available" ? "selected" : "" ?>>Available</option>
                        <option value="Rented" <?= $car['status']=="Rented" ? "selected" : "" ?>>Rented</option>
                    </select>
                </div>

                <button type="submit" name="update" class="btn-submit">Update Car</button>
            </form>
        </div>

        <!-- PLUS BUTTON RIGHT (Shows Current Image or +) -->
        <div class="plus-button-wrapper">
            <label class="plus-button" id="plusButton">
                <img id="previewImg" src="<?= $car['car_image'] && file_exists('../uploads/'.$car['car_image']) ? '../uploads/'.$car['car_image'] : '' ?>" alt="Car Preview">
                <input type="file" name="car_image" class="file-upload-input" id="fileInput" accept="image/*">
            </label>
        </div>
    </div>
</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

<script>
// Show uploaded image in plus button
const fileInput = document.getElementById('fileInput');
const previewImg = document.getElementById('previewImg');
const plusButton = document.getElementById('plusButton');

fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        previewImg.src = URL.createObjectURL(file);
        previewImg.style.display = 'block';
        plusButton.style.fontSize = '0'; // hide plus sign
    }
});
</script>

</body>
</html>