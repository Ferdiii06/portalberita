<?php
include 'koneksi.php';

// Verifikasi koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Pagination setup
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = max(0, ($page - 1) * $per_page);

// Search functionality
$search = isset($_GET['q']) ? $_GET['q'] : '';
$search_condition = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $search_condition = "AND (b.judul LIKE '%$search%' OR b.isi LIKE '%$search%')";
}

try {
    // Query untuk data berita
    $query = "SELECT * FROM berita ORDER BY tanggal DESC";
    $query = "SELECT 
              b.id_berita, 
              b.judul, 
              b.isi as isi_berita, 
              b.gambar, 
              b.tanggal,
              b.pengirim,
              k.nama_kategori
              FROM berita b
              LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
              WHERE b.status='publish' $search_condition
              ORDER BY b.tanggal DESC
              LIMIT ?, ?";
    
    

    $stmt = mysqli_prepare($koneksi, $query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($koneksi));
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $start, $per_page);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $berita_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // Query untuk total data (pagination)
    $count_query = "SELECT COUNT(*) as total FROM berita b WHERE status='publish' $search_condition";
    $count_result = mysqli_query($koneksi, $count_query);
    $total_data = mysqli_fetch_assoc($count_result);
    $pages = ceil($total_data['total'] / $per_page);
    
    $count_result = mysqli_query($koneksi, $count_query);
if (!$count_result) {
    die("Query count gagal: " . mysqli_error($koneksi));
}


} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Berita - Portal Berita</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <h2><i class="fas fa-archive"></i> Arsip Berita</h2>
        
        <div class="search-box">
            <form action="" method="GET">
                <input type="text" name="q" placeholder="Cari berita..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        
        <?php if(!empty($berita_data)): ?>
            <div class="berita-list">
                <?php foreach($berita_data as $hasil): ?>
                    <article class="berita-item">
                        <?php if(!empty($hasil['gambar'])): ?>
                            <img src="uploads/6814fca1d5a21.jpg" >
                        <?php endif; ?>
                        <h3><a href="berita_lengkap.php?id=<?= $hasil['id_berita'] ?>"><?= htmlspecialchars($hasil['judul']) ?></a></h3>
                        <div class="meta-info">
                            <span><i class="far fa-user"></i> <?= htmlspecialchars($hasil['pengirim']) ?></span>
                            <span><i class="far fa-calendar-alt"></i> <?= date('d M Y H:i', strtotime($hasil['tanggal'])) ?></span>
                            <?php if(!empty($hasil['nama_kategori'])): ?>
                                <span><i class="far fa-folder"></i> <?= htmlspecialchars($hasil['nama_kategori']) ?></span>
                            <?php endif; ?>
                        </div>
                        <p><?= substr(strip_tags($hasil['isi_berita']), 0, 200) ?>...</p>
                        <div class="actions">
                            <a href="edit_berita.php?id=<?= $hasil['id_berita'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="delete-berita.php?id=<?= $hasil['id_berita'] ?>" onclick="return confirmDelete()" class="btn-delete"><i class="fas fa-trash-alt"></i> Hapus</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=<?= $page-1 ?><?= !empty($search) ? '&q='.urlencode($search) : '' ?>" class="page-link">&laquo; Sebelumnya</a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <a href="?page=<?= $i ?><?= !empty($search) ? '&q='.urlencode($search) : '' ?>" class="page-link <?= ($page == $i) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if($page < $pages): ?>
                    <a href="?page=<?= $page+1 ?><?= !empty($search) ? '&q='.urlencode($search) : '' ?>" class="page-link">Berikutnya &raquo;</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p><i class="fas fa-info-circle"></i> Tidak ada berita yang ditemukan.</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
        function confirmDelete() {
            return confirm('Apakah Anda yakin akan menghapus berita ini?');
        }
    </script>
</body>
</html>
<?php
// Tutup koneksi
if (isset($stmt)) mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>