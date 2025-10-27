<?php
$host = 'localhost'; // Ganti dengan host cPanel jika diupload
$user = 'root'; // Ganti dengan username cPanel
$pass = ''; // Ganti dengan password cPanel
$db = 'paralayang_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>