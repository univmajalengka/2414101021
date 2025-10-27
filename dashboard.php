<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Upload foto pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
    $conn->query("UPDATE users SET foto='$target_file' WHERE id=$user_id");
}

// CRUD Tiket (hanya admin)
if ($role == 'admin' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $deskripsi = $_POST['deskripsi'];
        $conn->query("INSERT INTO tiket (nama, harga, deskripsi) VALUES ('$nama', $harga, '$deskripsi')");
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM tiket WHERE id=$id");
    }
}

// Statistik pemesanan
$total_pemesanan = $conn->query("SELECT COUNT(*) as total FROM pemesanan WHERE user_id=$user_id")->fetch_assoc()['total'];
$total_harga = $conn->query("SELECT SUM(total_harga) as total FROM pemesanan WHERE user_id=$user_id")->fetch_assoc()['total'] ?: 0;

// Daftar pemesanan user
$pemesanan = $conn->query("SELECT p.*, t.nama FROM pemesanan p JOIN tiket t ON p.tiket_id = t.id WHERE p.user_id=$user_id");

$result = $conn->query("SELECT * FROM tiket");
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Paralayang Booking</a>
            <div class="ms-auto">
                <span class="navbar-text me-3">Welcome, <?php echo $user['username']; ?></span>
                <img src="uploads/<?php echo $user['foto'] ?: 'ramapoto.jpg'; ?>" width="40" class="rounded-circle me-3">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="fade-in">Dashboard</h2>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card stats-card fade-in">
                    <div class="card-body text-center">
                        <h5>Total Pemesanan</h5>
                        <h2><?php echo $total_pemesanan; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stats-card fade-in">
                    <div class="card-body text-center">
                        <h5>Total Pengeluaran</h5>
                        <h2>Rp <?php echo number_format($total_harga); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Statistik -->
        <div class="card mb-4 fade-in">
            <div class="card-body">
                <h5>Statistik Pemesanan</h5>
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <!-- Upload Foto -->
        <div class="card mb-4 fade-in">
            <div class="card-body">
                <h5>Upload Foto Anda</h5>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="foto" class="form-control mb-2" required>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>

        <!-- Daftar Pemesanan -->
        <div class="card mb-4 fade-in">
            <div class="card-body">
                <h5>Riwayat Pemesanan</h5>
                <table class="table">
                    <thead><tr><th>Tiket</th><th>Jumlah</th><th>Total</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        <?php while ($row = $pemesanan->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['nama']; ?></td>
                                <td><?php echo $row['jumlah']; ?></td>
                                <td>Rp <?php echo number_format($row['total_harga']); ?></td>
                                <td><?php echo $row['tanggal_pemesanan']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($role == 'admin'): ?>
            <!-- CRUD Tiket -->
            <div class="card fade-in">
                <div class="card-body">
                    <h5>Kelola Tiket</h5>
                    <form method="POST" class="mb-3">
                        <input type="text" name="nama" placeholder="Nama Tiket" class="form-control mb-2" required>
                        <input type="number" name="harga" placeholder="Harga" class="form-control mb-2" required>
                        <textarea name="deskripsi" placeholder="Deskripsi" class="form-control mb-2"></textarea>
                        <button type="submit" name="add" class="btn btn-success">Tambah Tiket</button>
                    </form>
                    <table class="table">
                        <thead><tr><th>Nama</th><th>Harga</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['nama']; ?></td>
                                    <td>Rp <?php echo number_format($row['harga']); ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $row['id']; ?>">Hapus</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus tiket ini?
                </div>
                <div class="modal-footer">
                    <form method="POST" id="deleteForm">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Chart.js
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Pemesanan', 'Pengeluaran'],
                datasets: [{
                    label: 'Statistik',
                    data: [<?php echo $total_pemesanan; ?>, <?php echo $total_harga; ?>],
                    backgroundColor: ['#74b9ff', '#0984e3']
                }]
            }
        });

        // Modal Hapus
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            document.getElementById('deleteId').value = id;
        });
    </script>
</body>
</html>