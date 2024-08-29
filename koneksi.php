<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "berita";

$koneksi_db = mysqli_connect ($host, $user, $pass, $db);
if (!$koneksi_db) {
    die ("Database tidak dapat dibuka");
}
?>