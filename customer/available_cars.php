<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$cars = mysqli_query($conn, "
    SELECT * FROM cars c
    WHERE c.car_id NOT IN (
        SELECT b.car_id 
        FROM bookings b 
        WHERE b.status = 'Approved'
    )
    ORDER BY c.car_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Cars | UrbanDrive</title>
    <link rel="stylesheet" href="../adashboard.css">
    
    <!-- EMBEDDED CSS - This will definitely work -->
    <style>
        /* Container */
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 40px;
        }

        /* Page Title */
        .page-title {
            text-align: left;
            font-size: 42px;
            margin-bottom: 50px;
            color: #2b1d16;
            border-bottom: 3px solid #f0f0f0;
            padding-bottom: 20px;
        }

        /* --- BIGGER Car Grid Layout (4 Columns) --- */
        .car-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            padding: 30px 0;
            margin-bottom: 60px;
        }

        /* --- BIGGER Card Design --- */
        .car-card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        .car-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border-color: #ff6a00;
        }

        /* --- BIGGER Image Container --- */
        .car-image {
            width: 100%;
            height: 280px;
            overflow: hidden;
            position: relative;
            background-color: #f5f5f5;
        }

        .car-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .car-card:hover .car-image img {
            transform: scale(1.15);
        }

        /* --- Modern Placeholder --- */
        .no-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            color: #adb5bd;
        }

        .no-image-placeholder svg {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .no-image-placeholder span {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* --- Card Details --- */
        .car-info {
            padding: 35px;
            text-align: left;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .car-info h3 {
            margin: 0 0 12px;
            color: #2c3e50;
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.3;
        }

        .car-info p {
            color: #7f8c8d;
            margin: 0 0 20px 0;
            font-size: 1.1rem;
        }

        /* --- Price Tag --- */
        .car-price {
            font-weight: 900;
            color: #e74c3c;
            font-size: 1.8rem;
            margin-bottom: 25px;
        }

        .car-price span {
            font-size: 1rem;
            color: #999;
            font-weight: 400;
        }

        /* --- Button Styling --- */
        .rent-btn {
            display: block;
            width: 100%;
            padding: 18px;
            background: #2c3e50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 800;
            text-align: center;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin-top: auto;
            box-sizing: border-box;
        }

        .rent-btn:hover {
            background: #ff6a00;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 106, 0, 0.4);
        }

        /* --- Empty State --- */
        .empty {
            text-align: center;
            font-size: 24px;
            color: #777;
            padding: 100px 0;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }

        /* --- Mobile Adjustments --- */
        @media (max-width: 1400px) {
            .car-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 1000px) {
            .car-grid { grid-template-columns: repeat(2, 1fr); }
            .container { padding: 0 20px; }
        }

        @media (max-width: 600px) {
            .car-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .car-image { height: 240px; }
            .page-title { 
                text-align: center;
                font-size: 32px;
            }
            .car-info { padding: 25px; }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="available_cars.php" class="active">Available Cars</a>
        <a href="profile.php">Profile</a>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<main class="page-content container">
    <h1 class="page-title">Available Cars</h1>

    <?php if(mysqli_num_rows($cars) == 0): ?>
        <div class="empty">No cars available at the moment.</div>
    <?php else: ?>
        <div class="car-grid">
            <?php while($car = mysqli_fetch_assoc($cars)) { ?>
                <div class="car-card">
                    <div class="car-image">
                        <?php if(!empty($car['car_image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($car['car_image']) ?>" alt="<?= htmlspecialchars($car['car_name']) ?>">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                                <span>No Image</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="car-info">
                        <h3><?= htmlspecialchars($car['car_name']) ?></h3>
                        <p><?= htmlspecialchars($car['brand']) ?></p>
                        <div class="car-price">₱<?= number_format($car['price_per_day'], 2) ?> <span>/ day</span></div>
                        <a class="rent-btn" href="rent_process.php?id=<?= $car['car_id'] ?>" onclick="return confirm('Rent this car?');">Rent Now</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php endif; ?>
</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>