<?php
include("koneksi.php");

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_berita = (int)$_GET['id'];

// Query untuk mengambil berita
$query = "SELECT b.id_berita, k.nama_kategori, b.judul, b.headline, b.isi, b.pengirim, b.tanggal, b.gambar
          FROM berita b
          JOIN kategori k ON b.id_kategori = k.id_kategori
          WHERE b.id_berita = ?";
$stmt = mysqli_prepare($koneksi_db, $query);
mysqli_stmt_bind_param($stmt, "i", $id_berita);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("Location: index.php");
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
    <title><?= htmlspecialchars($berita['judul']) ?> - Portal Berita</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .berita-detail {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        .meta-info {
            margin: 15px 0;
            color: #666;
            font-size: 14px;
        }
        .meta-info span {
            margin-right: 15px;
        }
        .headline {
            font-size: 18px;
            margin: 20px 0;
            color: #333;
        }
        .content {
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .berita-gambar {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px auto;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        .actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
        .btn-edit, .btn-back {
            display: inline-block;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-edit {
            background: #2c3e50;
            color: white;
        }
        .btn-back {
            background: #f0f0f0;
            color: #333;
        }
        @media (max-width: 600px) {
            .meta-info span {
                display: block;
                margin-bottom: 5px;
            }
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <article class="berita-detail">
            <h1><?= htmlspecialchars($berita['judul']) ?></h1>
            
            <div class="meta-info">
                <span><i class="far fa-user"></i> <?= htmlspecialchars($berita['pengirim']) ?></span>
                <span><i class="far fa-calendar-alt"></i> <?= date('d F Y H:i', strtotime($berita['tanggal'])) ?></span>
                <span><i class="far fa-folder"></i> <?= htmlspecialchars($berita['nama_kategori']) ?></span>
            </div>
            
            <?php if(!empty($berita['gambar'])): ?>
                <div class="berita-gambar-container">
                    <img src="<?= htmlspecialchars($berita['gambar']) ?>" alt="<?= htmlspecialchars($berita['judul']) ?>" class="berita-gambar">
                </div>
            <?php endif; ?>
            
            <div class="headline">
                <p><strong><?= htmlspecialchars($berita['headline']) ?></strong></p>
            </div>
            
            <div class="content">
                <?= nl2br(htmlspecialchars($berita['isi'])) ?>
            </div>
            
            <div class="actions">
                <a href="edit_berita.php?id=<?= $berita['id_berita'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit Berita</a>
                <a href="arsip_berita.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Arsip</a>
            </div>
        </article>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>