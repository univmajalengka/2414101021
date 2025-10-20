<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pemesanan Tiket Paralayang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style> body { background-image: url('assets/images/default_bg.jpg'); } </style>
</head>
<body>
    <div class="overlay">
        <header>
            <h1>Selamat Datang di Paralayang Adventure</h1>
        </header>
        <div class="content">
            <div class="card">
                <h3>Login</h3>
                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <p>Belum punya akun? <a href="register.php">Daftar</a></p>
            </div>
        </div>
        <footer>&copy; 2025 Paralayang Adventure. Semua hak dilindungi.</footer>
    </div>
</body>
</html>