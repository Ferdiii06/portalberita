<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "webberita";
$db = "portal_berita";



// Membuat koneksi
$koneksi_db = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi_db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke utf8mb4 untuk mendukung semua karakter
mysqli_set_charset($koneksi_db, "utf8mb4");

//buat variabel $koneksi untuk kompatibilitas dengan arsip_berita.php
$koneksi = $koneksi_db;

?>