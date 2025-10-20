<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: index.php");
include 'config.php';
$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Ambil pemesanan
$bookings = $conn->query("SELECT b.*, t.name FROM bookings b JOIN tickets t ON b.ticket_id = t.id WHERE b.user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pemesanan Tiket Paralayang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-image: url('assets/images/<?php echo $user['background_pic']; ?>'); }
        .toast { position: fixed; top: 20px; right: 20px; background: #28a745; color: #fff; padding: 10px; border-radius: 5px; display: none; z-index: 1000; }
        #search { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="overlay">
        <header>
            <div class="navbar">
                <h1>Dashboard Paralayang</h1>
                <div>
                    <a href="profile.php">Profil</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>

        <div class="main-layout">
            <div class="sidebar">
                <h3>Menu</h3>
                <ul>
                    <li><a href="booking.php">Pesan Tiket</a></li>
                    <li><a href="tickets.php">Kelola Tiket</a></li>
                    <li><a href="profile.php">Edit Profil</a></li>
                </ul>
            </div>

            <div class="content">
                <div class="card">
                    <h3>Selamat Datang, <?php echo $user['username']; ?>!</h3>
                    <p>Kelola pemesanan tiket paralayang Anda dengan mudah.</p>
                </div>

                <!-- Riwayat Pemesanan dengan Quick Search -->
                <div class="card">
                    <h3>Riwayat Pemesanan</h3>
                    <input type="text" id="search" placeholder="Cari tiket..." onkeyup="filterTable()">
                    <table id="booking-table">
                        <tr><th>Tiket</th><th>Status</th><th>Tanggal</th></tr>
                        <?php while ($row = $bookings->fetch_assoc()) { ?>
                        <tr><td><?php echo $row['name']; ?></td><td><?php echo $row['status']; ?></td><td><?php echo $row['booking_date']; ?></td></tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="toast" id="notification">Pemesanan berhasil diperbarui!</div>
        <footer>&copy; 2025 Paralayang Adventure. Semua hak dilindungi.</footer>
    </div>

    <script>
        // Filter Tabel
        function filterTable() {
            const input = document.getElementById('search');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('booking-table');
            const tr = table.getElementsByTagName('tr');
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
                }
            }
        }

        // Notifikasi Toast (contoh trigger)
        function showToast() {
            const toast = document.getElementById('notification');
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }
        // Trigger contoh: showToast(); (bisa panggil setelah update database)
    </script>
</body>
</html>
