<?php
include("koneksi.php");

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: arsip_berita.php");
    exit();
}

$id_berita = (int)$_GET['id'];

// Proses delete berita
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    // Gunakan prepared statement untuk menghindari SQL injection
    $query = "DELETE FROM berita WHERE id_berita=?";
    $stmt = mysqli_prepare($koneksi_db, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_berita);
    
    if(mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: arsip_berita.php?delete_success=1");
        exit();
    } else {
        $error = "Gagal menghapus berita: " . mysqli_error($koneksi_db);
    }
    mysqli_stmt_close($stmt);
}

// Ambil judul berita untuk konfirmasi
$query = "SELECT judul FROM berita WHERE id_berita=?";
$stmt = mysqli_prepare($koneksi_db, $query);
mysqli_stmt_bind_param($stmt, "i", $id_berita);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("Location: arsip_berita.php");
    exit();
}

$berita = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Berita - Portal Berita</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <h2><i class="fas fa-trash-alt"></i> Hapus Berita</h2>
        
        <?php if(isset($error)): ?>
            <div class="error-message">
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>
        
        <div class="confirmation-box">
            <p>Anda yakin ingin menghapus berita berikut?</p>
            <h3><?= htmlspecialchars($berita['judul']) ?></h3>
            
            <form action="" method="POST">
                <div class="form-actions">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">
                        <i class="fas fa-check"></i> Ya, Hapus
                    </button>
                    <a href="arsip_berita.php" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>