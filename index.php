<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM tiket");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pemesanan Tiket Paralayang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="index-page"> <!-- Background satu foto statis -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Paralayang Booking</a>
            <div class="ms-auto">
                <a href="dashboard.php" class="btn btn-light">Dashboard</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Parallax Section (opsional, bisa dihapus jika ingin lebih simpel) -->
    <div class="parallax" style="background-image: url('uploads/mjlk.jpg');">
        <h1 class="fade-in">Rasakan Sensasi Terbang Paralayang!</h1>
    </div>

    <div class="container mt-5">
        <h1 class="text-center fade-in">Pemesanan Tiket</h1>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4 fade-in">
                    <div class="card">
                        <img src="uploads/<?php echo $row['gambar'] ?: 'pots.jpg'; ?>" class="card-img-top" alt="Tiket">
                        <div class="card-body">
                            <h5><?php echo $row['nama']; ?></h5>
                            <p><?php echo $row['deskripsi']; ?></p>
                            <p>Harga: Rp <?php echo number_format($row['harga']); ?></p>
                            <a href="pesan.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Foto Pembuat Web -->
    <div class="text-center mt-5 fade-in">
        <h3>Pembuat Web</h3>
        <img src="uploads/ramapoto.jpg" alt="Foto Pembuat" class="rounded-circle" width="150">
        <p>Nama: RAMA KUSUMA RAMDANI</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>