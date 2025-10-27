<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $tiket_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jumlah = $_POST['jumlah'];
        $tiket = $conn->query("SELECT harga FROM tiket WHERE id=$tiket_id")->fetch_assoc();
        $total = $jumlah * $tiket['harga'];
        $conn->query("INSERT INTO pemesanan (user_id, tiket_id, jumlah, total_harga) VALUES ($user_id, $tiket_id, $jumlah, $total)");
        echo "<script>alert('Pemesanan berhasil!'); window.location='index.php';</script>";
    }

    $tiket = $conn->query("SELECT * FROM tiket WHERE id=$tiket_id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Tiket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background: linear-gradient(to right, #74b9ff, #0984e3); } </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="width: 400px;">
        <h2>Pesan Tiket: <?php echo $tiket['nama']; ?></h2>
        <p>Harga: Rp <?php echo number_format($tiket['harga']); ?></p>
        <form method="POST">
            <input type="number" name="jumlah" class="form-control mb-3" placeholder="Jumlah" min="1" required>
            <button type="submit" class="btn btn-primary w-100">Pesan</button>
        </form>
    </div>
</body>
</html>