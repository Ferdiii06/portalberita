<?php
include 'koneksi.php';

// Check database connection
if (!$koneksi_db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Get latest news
$query = "SELECT b.id_berita, b.judul, b.headline, b.tanggal, b.pengirim, k.nama_kategori
          FROM  berita b
          JOIN kategori k ON b.id_kategori = k.id_kategori 
          WHERE b.status='publish'
          ORDER BY b.tanggal DESC LIMIT 5";

$result = mysqli_query($koneksi_db, $query);

if (!$result) {
    die("Query error: " . mysqli_error($koneksi_db));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Berita</title>
    <meta name="description" content="Portal berita terkini dan terpercaya">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <h1><i class="fas fa-newspaper"></i> Berita Terkini</h1>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="berita-terbaru">
                <?php while($berita = mysqli_fetch_assoc($result)): ?>
                    <article class="berita-item">
                        <h2><a href="berita_lengkap.php?id=<?= $berita['id_berita'] ?>"><?= htmlspecialchars($berita['judul']) ?></a></h2>
                        <div class="meta-info">
                            <span><i class="far fa-user"></i> <?= htmlspecialchars($berita['pengirim']) ?></span>
                            <span><i class="far fa-calendar-alt"></i> <?= date('d M Y H:i', strtotime($berita['tanggal'])) ?></span>
                            <span><i class="far fa-folder"></i> <?= htmlspecialchars($berita['nama_kategori']) ?></span>
                        </div>
                        <div class="headline">
                            <p><?= htmlspecialchars($berita['headline']) ?></p>
                        </div>
                        <a href="berita_lengkap.php?id=<?= $berita['id_berita'] ?>" class="read-more">Baca selengkapnya...</a>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <div class="more-news">
                <a href="arsip_berita.php" class="btn-more"><i class="fas fa-book-open"></i> Lihat Arsip Berita</a>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p><i class="fas fa-info-circle"></i> Belum ada berita yang tersedia.</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>