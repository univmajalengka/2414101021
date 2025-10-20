<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: index.php");
include 'config.php';
$user_id = $_SESSION['user_id'];

// Ambil background user
$user = $conn->query("SELECT background_pic FROM users WHERE id = $user_id")->fetch_assoc();

// Create
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $available = $_POST['available'];
    $stmt = $conn->prepare("INSERT INTO tickets (name, description, price, available) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $name, $desc, $price, $available);
    $stmt->execute();
}

 // Delete
   if (isset($_GET['delete'])) {
       $id = $_GET['delete'];
       // Hapus pemesanan terkait dulu
       $conn->query("DELETE FROM bookings WHERE ticket_id = $id");
       // Baru hapus tiket
       $conn->query("DELETE FROM tickets WHERE id = $id");
       echo "Tiket dan pemesanan terkait berhasil dihapus!";
   }

$tickets = $conn->query("SELECT * FROM tickets");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Tiket</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style> body { background-image: url('assets/images/<?php echo $user['background_pic']; ?>'); background-size: cover; background-position: center; } </style>  <!-- Background dinamis -->
</head>
<body>
    <div class="container">
        <h1>Kelola Tiket</h1>
        <form method="POST">
            <input type="text" name="name" placeholder="Nama Tiket" required>
            <textarea name="description" placeholder="Deskripsi"></textarea>
            <input type="number" name="price" placeholder="Harga" step="0.01" required>
            <input type="number" name="available" placeholder="Tersedia" required>
            <button type="submit" name="add">Tambah</button>
        </form>
        <table>
            <tr><th>Nama</th><th>Harga</th><th>Tersedia</th><th>Aksi</th></tr>
            <?php while ($row = $tickets->fetch_assoc()) { ?>
            <tr><td><?php echo $row['name']; ?></td><td><?php echo $row['price']; ?></td><td><?php echo $row['available']; ?></td><td><a href="?delete=<?php echo $row['id']; ?>">Hapus</a></td></tr>
            <?php } ?>
        </table>
        <a href="dashboard.php">Kembali</a>
    </div>
</body>
</html>