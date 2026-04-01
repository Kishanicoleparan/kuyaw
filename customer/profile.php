<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

$id = $_SESSION['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$id"));

/* UPDATE PROFILE */
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    /* IMAGE UPLOAD */
    if(!empty($_FILES['profile_pic']['name'])){
        $image_name = time() . "_" . $_FILES['profile_pic']['name'];
        $target = "../uploads/" . $image_name;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target);
        mysqli_query($conn, "UPDATE users SET profile_pic='$image_name' WHERE id=$id");
    }

    mysqli_query($conn, "
        UPDATE users SET 
        name='$name',
        email='$email',
        phone='$phone',
        address='$address'
        WHERE id=$id
    ");

    header("Location: profile.php");
    exit();
}

/* DELETE PROFILE PICTURE */
if(isset($_GET['delete_pic'])){
    mysqli_query($conn, "UPDATE users SET profile_pic=NULL WHERE id=$id");
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>My Profile | UrbanDrive</title>
<link rel="stylesheet" href="../adashboard.css">

<style>
body{
    background:linear-gradient(to right,#fff5ec,#fffaf6);
    font-family:Arial, sans-serif;
}
.page-content {
    max-width: 1200px; 
    margin: 40px auto; 
    padding: 0 20px;
    min-height: auto;
}
.page-title{
    text-align: left;
    font-size:36px;
    margin-bottom:30px;
    color:#2b1d16;
}
.profile-wrapper{
    display:flex;
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(0,0,0,0.1);
}
.profile-left{
    width:45%;
    background:linear-gradient(135deg,#ff6a00,#ff914d);
    padding:60px 40px;
    text-align:center;
    color:white;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    position:relative;
}
.profile-right{
    width:55%;
    padding:40px;
}

/* PROFILE IMAGE */
.profile-pic-wrapper{
    position:relative;
    width:190px;
    height:190px;
    margin-bottom:25px;
}
.profile-pic-wrapper img{
    width:190px;
    height:190px;
    border-radius:50%;
    object-fit:cover;
    border:5px solid rgba(255,255,255,0.4);
}
.upload-btn{
    position:absolute;
    bottom:0;
    right:0;
    width:45px;
    height:45px;
    border-radius:50%;
    background:white;
    color:#ff6a00;
    font-size:28px;
    font-weight:bold;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
    transition:0.3s;
    border:2px solid #fff;
}
.upload-btn:hover{
    background:#ff6a00;
    color:white;
    transform:scale(1.1);
}

.profile-left h3{
    margin:10px 0 5px 0;
    font-size:24px;
}
.profile-left p{
    opacity:0.9;
    font-size:16px;
}

/* FORM INPUTS */
.form-group{
    margin-bottom:20px;
}
.form-group label{
    display:block;
    margin-bottom:6px;
    font-weight:600;
    font-size:18px;
}
.form-group input,
.form-group textarea{
    width:100%;
    padding:14px;
    font-size:18px;
    border-radius:10px;
    border:1px solid #ddd;
    background:#f9f9f9;
}
.form-group textarea{
    height:100px;
    resize:vertical;
}

/* BUTTONS */
.btn-primary{
    background:#ff6a00;
    color:white;
    padding:16px 28px;
    border:none;
    border-radius:10px;
    font-weight:bold;
    font-size:18px;
    cursor:pointer;
}
.btn-primary:hover{
    background:#e65a00;
}
.btn-danger{
    margin-top:10px;
    display:inline-block;
    color:white;
    border:1px solid rgba(255,255,255,0.6);
    padding:6px 14px;
    border-radius:6px;
    text-decoration:none;
    font-size:14px;
}
.btn-danger:hover{
    background:white;
    color:#ff6a00;
}

@media(max-width:768px){
    .profile-wrapper{
        flex-direction:column;
    }
    .profile-left,
    .profile-right{
        width:100%;
    }
}
</style>
</head>
<body>

<header class="admin-header">
<div class="logo">Urban<span>Drive</span></div>
<nav>
<a href="dashboard.php">Dashboard</a>
<a href="my_bookings.php">My Bookings</a>
<a href="available_cars.php">Available Cars</a>
<a href="profile.php" class="active">Profile</a>
<a href="../logout.php" class="logout-btn">Logout</a>
</nav>
</header>

<div class="page-content">
<h1 class="page-title">My Profile</h1>

<div class="profile-wrapper">

<!-- LEFT SIDE -->
<div class="profile-left">
<form method="POST" enctype="multipart/form-data">
<div class="profile-pic-wrapper">
    <?php if(!empty($user['profile_pic'])){ ?>
        <img id="currentImage" src="../uploads/<?= htmlspecialchars($user['profile_pic']) ?>">
    <?php } else { ?>
        <img id="currentImage" src="https://cdn-icons-png.flaticon.com/512/149/149071.png">
    <?php } ?>
    <label for="uploadPic" class="upload-btn">+</label>
    <input type="file" id="uploadPic" name="profile_pic" accept="image/*" onchange="previewImage(event)" hidden>
</div>

<h3><?= htmlspecialchars($user['name']) ?></h3>
<p><?= ucfirst(htmlspecialchars($user['role'])) ?> Account</p>

<?php if(!empty($user['profile_pic'])){ ?>
<a href="?delete_pic=1" class="btn-danger">Delete Picture</a>
<?php } ?>

</div>

<!-- RIGHT SIDE -->
<div class="profile-right">

<div class="form-group">
<label>Full Name</label>
<input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
</div>

<div class="form-group">
<label>Email Address</label>
<input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
</div>

<div class="form-group">
<label>Phone Number</label>
<input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
</div>

<div class="form-group">
<label>Address</label>
<textarea name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
</div>

<button type="submit" name="update" class="btn-primary">
Save Changes
</button>
</form>

</div>
</div>
</div>

<footer class="admin-footer">
© 2026 UrbanDrive
</footer>

<script>
function previewImage(event){
    var reader = new FileReader();
    var preview = document.getElementById("currentImage");
    reader.onload = function(){
        preview.src = reader.result;
    }
    if(event.target.files[0]){
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>

</body>
</html>