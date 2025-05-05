<?php
include 'koneksi.php';

// Proses edit berita
if(isset($_POST['edit'])){
    // Validasi dan sanitasi input
    $id_berita = (int)$_POST['id_berita'];
    $judul = mysqli_real_escape_string($koneksi_db, htmlspecialchars($_POST['judul']));
    $kategori = (int)$_POST['kategori'];
    $headline = mysqli_real_escape_string($koneksi_db, htmlspecialchars($_POST['headline']));
    $isi_berita = mysqli_real_escape_string($koneksi_db, $_POST['isi']);
    $pengirim = mysqli_real_escape_string($koneksi_db, htmlspecialchars($_POST['pengirim']));
    
    // Validasi input
    $errors = [];
    if(empty($judul)) $errors[] = "Judul harus diisi";
    if(empty($kategori)) $errors[] = "Kategori harus dipilih";
    if(empty($headline)) $errors[] = "Headline harus diisi";
    if(empty($isi_berita)) $errors[] = "Isi berita harus diisi";
    if(empty($pengirim)) $errors[] = "Pengirim harus diisi";

    if(empty($errors)) {
        // Update data berita
        $query = "UPDATE berita SET 
                  id_kategori = ?, 
                  judul = ?, 
                  headline = ?, 
                  isi = ?, 
                  pengirim = ?,
                  tanggal = NOW()
                  WHERE id_berita = ?";
        
        $stmt = mysqli_prepare($koneksi_db, $query);
        mysqli_stmt_bind_param($stmt, "issssi", $kategori, $judul, $headline, $isi_berita, $pengirim, $id_berita);
        
        if(mysqli_stmt_execute($stmt)) {
            $success = "Berita berhasil diperbarui!";
        } else {
            $errors[] = "Gagal memperbarui berita: " . mysqli_error($koneksi_db);
        }
        mysqli_stmt_close($stmt);
    }
}

// Ambil data berita yang akan diedit
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_berita = (int)$_GET['id'];

$query = "SELECT b.id_berita, b.id_kategori, b.judul, b.headline, b.isi, b.pengirim 
          FROM berita b 
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
    <title>Edit Berita - Portal Berita</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-berita {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .form-actions {
            margin-top: 20px;
            text-align: right;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-reset {
            background-color: #f0f0f0;
            color: #333;
            margin-right: 10px;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .error-message ul {
            margin: 0;
            padding-left: 20px;
        }
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <h2><i class="fas fa-edit"></i> Edit Berita</h2>
        
        <?php
        // Tampilkan pesan error
        if(!empty($errors)) {
            echo '<div class="error-message"><ul>';
            foreach($errors as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul></div>';
        }
        
        // Tampilkan pesan sukses
        if(isset($success)) {
            echo '<div class="success-message">'.$success.'</div>';
        }
        ?>
        
        <form action="" method="POST" class="form-berita">
            <input type="hidden" name="id_berita" value="<?= $berita['id_berita'] ?>">
            
            <div class="form-group">
                <label for="judul">Judul Berita</label>
                <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($berita['judul']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php
                    $query = "SELECT id_kategori, nm_kategori FROM kategori ORDER BY nm_kategori";
                    $sql = mysqli_query($koneksi_db, $query);
                    if(mysqli_num_rows($sql) > 0) {
                        while ($hasil = mysqli_fetch_array($sql)) {
                            $selected = ($berita['id_kategori'] == $hasil['id_kategori']) ? 'selected' : '';
                            echo "<option value='".$hasil['id_kategori']."' $selected>".htmlspecialchars($hasil['nm_kategori'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="headline">Headline Berita (Ringkasan)</label>
                <textarea id="headline" name="headline" rows="4" required><?= htmlspecialchars($berita['headline']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="isi">Isi Lengkap Berita</label>
                <textarea id="isi" name="isi" rows="10" required><?= htmlspecialchars($berita['isi']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="pengirim">Nama Pengirim</label>
                <input type="text" id="pengirim" name="pengirim" value="<?= htmlspecialchars($berita['pengirim']) ?>" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="edit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="berita_lengkap.php?id=<?= $berita['id_berita'] ?>" class="btn btn-reset">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>