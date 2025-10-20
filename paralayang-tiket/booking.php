<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: index.php");
include 'config.php';
$user_id = $_SESSION['user_id'];

// Ambil background user
$user = $conn->query("SELECT background_pic FROM users WHERE id = $user_id")->fetch_assoc();

if (isset($_POST['book'])) {
    $ticket_id = $_POST['ticket_id'];
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, ticket_id, status) VALUES (?, ?, 'confirmed')");
    $stmt->bind_param("ii", $user_id, $ticket_id);
    if ($stmt->execute()) {
        echo "Pemesanan berhasil dikonfirmasi!";
        // Kurangi available (opsional)
        $conn->query("UPDATE tickets SET available = available - 1 WHERE id = $ticket_id AND available > 0");
    } else {
        echo "Gagal memesan tiket!";
    }
}

$tickets = $conn->query("SELECT * FROM tickets WHERE available > 0");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Tiket</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style> body { background-image: url('assets/images/<?php echo $user['background_pic']; ?>'); } </style>
</head>
<body>
    <div class="overlay">
        <header>
            <div class="navbar">
                <h1>Pesan Tiket Paralayang</h1>
                <div>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>
        <div class="main-layout">
            <div class="sidebar">
                <h3>Menu</h3>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="tickets.php">Kelola Tiket</a></li>
                    <li><a href="profile.php">Profil</a></li>
                </ul>
            </div>
            <div class="content">
                <div class="card">
                    <h3>Pilih Tiket</h3>
                    <?php if ($tickets->num_rows == 0) { ?>
                        <p style="color: red;">Tidak ada tiket tersedia! Pastikan tiket sudah ditambahkan dan available > 0 di halaman kelola tiket.</p>
                    <?php } else { ?>
                        <form method="POST">
                            <select name="ticket_id" required>
                                <option value="">Pilih Tiket</option>
                                <?php while ($row = $tickets->fetch_assoc()) { ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name'] . " - Rp" . $row['price'] . " (Tersedia: " . $row['available'] . ")"; ?></option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="book">Pesan</button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
        <footer>&copy; 2023 Paralayang Adventure. Semua hak dilindungi.</footer>
    </div>
</body>
</html>
