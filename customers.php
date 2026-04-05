<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

/* --- PAGINATION SETUP --- */
$limit = 10; // rows per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$totalResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalCustomers = $totalRow['total'];
$totalPages = ceil($totalCustomers / $limit);

/* --- FETCH CUSTOMERS --- */
$customers = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC LIMIT $offset, $limit");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | UrbanDrive</title>
<link rel="stylesheet" href="adashboard.css">
<style>
body{
    font-family: Arial, sans-serif;
    background: #f8f9fa;
    margin: 0;
}
.page-content {
    max-width: 1400px;
    margin: 40px auto;
    padding: 0 20px;
}
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.page-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: #1a1a2e;
}
.btn-primary {
    background: linear-gradient(135deg, #ff6a00, #ff914d);
    color: #fff;
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255,106,0,0.4);
}
.search-bar {
    margin-bottom: 20px;
    width: 300px;
    padding: 10px 14px;
    font-size: 16px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

/* CUSTOMER TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}
th, td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
    font-size: 16px;
}
th {
    background: #ff6a00;
    color: white;
    font-weight: 600;
}
tr:hover {
    background: #fff3ea;
}
.profile-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ff914d;
}
.action-btn {
    padding: 6px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    color: white;
    font-weight: 600;
    margin-right: 5px;
}
.edit-btn {
    background: #4361ee;
}
.edit-btn:hover { background: #3a56d4; }
.delete-btn {
    background: #e63946;
}
.delete-btn:hover { background: #c1121f; }

/* PAGINATION */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 5px;
}
.pagination a {
    padding: 8px 14px;
    background: #ff6a00;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}
.pagination a.active {
    background: #ff914d;
}
.pagination a:hover { transform: translateY(-2px); }

/* RESPONSIVE */
@media (max-width: 768px){
    table, th, td {
        font-size: 14px;
    }
    .search-bar {
        width: 100%;
        margin-bottom: 15px;
    }
    .page-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
</style>
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span> Admin</div>
    <nav>
        <a href="reports.php">Dashboard</a>
        <a href="addcar.php">Add Cars</a>
        <a href="viewcar.php">View Cars</a>
        <a href="bookings.php">Bookings</a>
        <a href="customers.php" class="active">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="logout.php" class="logout">Logout</a>
    </nav>
</header>

<div class="page-content">
    <div class="page-header">
        <h1>Customers</h1>
        <a href="addcustomer.php" class="btn-primary">+ Add Customer</a>
    </div>

    <input type="text" id="searchInput" class="search-bar" placeholder="Search customers...">

    <table id="customerTable">
        <thead>
            <tr>
                <th>Profile</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($customers) == 0): ?>
            <tr>
                <td colspan="7" style="text-align:center;">No customers found.</td>
            </tr>
        <?php else: ?>
            <?php while($customer = mysqli_fetch_assoc($customers)): ?>
                <tr>
                    <td>
                        <?php if(!empty($customer['profile_pic'])): ?>
                            <img src="uploads/<?= htmlspecialchars($customer['profile_pic']) ?>" class="profile-img">
                        <?php else: ?>
                            <div class="profile-img" style="background:#ff6a00;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                <?= strtoupper(substr($customer['name'],0,2)) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($customer['name']) ?></td>
                    <td><?= htmlspecialchars($customer['email']) ?></td>
                    <td><?= htmlspecialchars($customer['phone'] ?? 'N/A') ?></td>
                    <td><?= ucfirst($customer['role']) ?></td>
                    <td><?= date("M d, Y", strtotime($customer['created_at'])) ?></td>
                    <td>
                        <a href="editcustomer.php?id=<?= $customer['id'] ?>" class="action-btn edit-btn">Edit</a>
                        <a href="deletecustomer.php?id=<?= $customer['id'] ?>" onclick="return confirm('Delete this customer?')" class="action-btn delete-btn">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i==$page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

<script>
// Client-side search
document.getElementById('searchInput').addEventListener('keyup', function(){
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#customerTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>