<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: index.php");
include 'config.php';
$user_id = $_SESSION['user_id'];

// Ambil data user
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

if (isset($_POST['update'])) {
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ?, address = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $email, $full_name, $address, $phone, $user_id);
    $stmt->execute();
    echo "Profil diperbarui!";
    // Refresh data
    $user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
}

// Upload foto profil
if (isset($_FILES['profile_pic'])) {
    $target = "assets/images/" . basename($_FILES['profile_pic']['name']);
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
        $conn->query("UPDATE users SET profile_pic = '" . $_FILES['profile_pic']['name'] . "' WHERE id = $user_id");
        $user['profile_pic'] = $_FILES['profile_pic']['name'];  // Update variabel agar langsung terlihat
        echo "Foto profil diperbarui!";
    } else {
        echo "Gagal upload foto!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-image: url('assets/images/<?php echo $user['background_pic']; ?>'); }
        .profile-pic { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 5px solid #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.2); margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="overlay">
        <header>
            <h1>Edit Profil</h1>
        </header>
        <div class="main-layout">
            <div class="sidebar">
                <h3>Menu</h3>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="booking.php">Pesan Tiket</a></li>
                    <li><a href="tickets.php">Kelola Tiket</a></li>
                </ul>
            </div>
            <div class="content">
                <div class="card">
                    <h3>Informasi Profil</h3>
                    <!-- Tampilkan Foto Profil -->
                    <img src="assets/images/<?php echo $user['profile_pic'] ?: 'default_profile.jpg'; ?>" alt="Foto Profil" class="profile-pic">
                    
                    <!-- Form Update Data Diri -->
                    <form method="POST">
                        <input type="email" name="email" value="<?php echo $user['email']; ?>" placeholder="Email" required>
                        <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" placeholder="Nama Lengkap">
                        <textarea name="address" placeholder="Alamat"><?php echo $user['address']; ?></textarea>
                        <input type="text" name="phone" value="<?php echo $user['phone']; ?>" placeholder="Nomor Telepon">
                        <button type="submit" name="update">Update Profil</button>
                    </form>
                    
                    <!-- Upload Foto Profil -->
                    <h4>Upload Foto Profil</h4>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_pic" accept="image/*" required>
                        <button type="submit">Upload Foto</button>
                    </form>
                </div>
            </div>
        </div>
        <footer>&copy; 2023 Paralayang Adventure. Semua hak dilindungi.</footer>
    </div>
</body>
</html>