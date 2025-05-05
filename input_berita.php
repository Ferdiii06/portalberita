<?php
include 'koneksi.php';

// Proses input berita
if(isset($_POST['input'])){
    // Validasi dan sanitasi input
    $judul = mysqli_real_escape_string($koneksi_db, htmlspecialchars($_POST['judul']));
    $kategori = (int)$_POST['kategori'];
    $headline = mysqli_real_escape_string($koneksi_db, htmlspecialchars($_POST['headline']));
    $isi_berita = mysqli_real_escape_string($koneksi_db, $_POST['isi']);
    $pengirim = mysqli_real_escape_string($koneksi_db, htmlspecialchars($_POST['pengirim']));
    $upload_dir = 'uploads/';
$gambar_name = uniqid() . '.' . $ext;
$target_file = $upload_dir . $gambar_name;

if(move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
    $gambar = $target_file; // Simpan path relatif
}
    // Validasi input
    $errors = [];
    if(empty($judul)) $errors[] = "Judul harus diisi";
    if(empty($kategori)) $errors[] = "Kategori harus dipilih";
    if(empty($headline)) $errors[] = "Headline harus diisi";
    if(empty($isi_berita)) $errors[] = "Isi berita harus diisi";
    if(empty($pengirim)) $errors[] = "Pengirim harus diisi";

    // Proses upload gambar
    $gambar = '';
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        
        if(in_array(strtolower($ext), $allowed)) {
            $upload_dir = 'uploads/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $gambar_name = uniqid() . '.' . $ext;
            $target_file = $upload_dir . $gambar_name;
            
            if(move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = $target_file;
            } else {
                $errors[] = "Gagal mengupload gambar";
            }
        } else {
            $errors[] = "Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF";
        }
    }

    if(empty($errors)) {
        // Insert ke tabel dengan prepared statement
        $query = "INSERT INTO berita (id_kategori, judul, headline, isi, pengirim, tanggal, gambar, status) 
                  VALUES (?, ?, ?, ?, ?, NOW(), ?, 1)";
        
        $stmt = mysqli_prepare($koneksi_db, $query);
        mysqli_stmt_bind_param($stmt, "isssss", $kategori, $judul, $headline, $isi_berita, $pengirim, $gambar);
        
        if(mysqli_stmt_execute($stmt)) {
            $success = "Berita berhasil ditambahkan!";
            // Reset form setelah submit berhasil
            $_POST = array();
        } else {
            $errors[] = "Gagal menambahkan berita: " . mysqli_error($koneksi_db);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Berita - Portal Berita</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
   
        
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <h2><i class="fas fa-plus-circle"></i> Input Berita Baru</h2>
        
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
        
        <form action="" method="POST" class="form-berita" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul">Judul Berita</label>
                <input type="text" id="judul" name="judul" value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php
                    $query = "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori";
                    $sql = mysqli_query($koneksi_db, $query);
                    if(mysqli_num_rows($sql) > 0) {
                        while ($hasil = mysqli_fetch_array($sql)) {
                            $selected = (isset($_POST['kategori']) && $_POST['kategori'] == $hasil['id_kategori']) ? 'selected' : '';
                            echo "<option value='".$hasil['id_kategori']."' $selected>".htmlspecialchars($hasil['nama_kategori'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="gambar">Gambar Berita (Opsional)</label>
                <input type="file" id="gambar" name="gambar" accept="image/*">
                <img id="preview" class="preview-gambar" src="#" alt="Preview Gambar">
            </div>
            
            <div class="form-group">
                <label for="headline">Headline Berita (Ringkasan)</label>
                <textarea id="headline" name="headline" rows="4" required><?= isset($_POST['headline']) ? htmlspecialchars($_POST['headline']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="isi">Isi Lengkap Berita</label>
                <textarea id="isi" name="isi" rows="10" required><?= isset($_POST['isi']) ? htmlspecialchars($_POST['isi']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="pengirim">Nama Pengirim</label>
                <input type="text" id="pengirim" name="pengirim" value="<?= isset($_POST['pengirim']) ? htmlspecialchars($_POST['pengirim']) : '' ?>" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="input" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Simpan Berita
                </button>
                <button type="reset" class="btn btn-reset">
                    <i class="fas fa-undo"></i> Reset Form
                </button>
            </div>
        </form>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // Preview gambar sebelum upload
        document.getElementById('gambar').addEventListener('change', function(e) {
            const preview = document.getElementById('preview');
            const file = e.target.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            if(file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>