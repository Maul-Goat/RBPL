<?php
session_start();
// Wajib panggil koneksi untuk mengambil data database
require 'koneksi.php'; 
$q_cek_mt = mysqli_query($koneksi, "SELECT nilai FROM pengaturan WHERE nama_pengaturan = 'maintenance_mode'");
if ($q_cek_mt && mysqli_num_rows($q_cek_mt) > 0) {
    $mt_data = mysqli_fetch_assoc($q_cek_mt);
    if ($mt_data['nilai'] == '1') {
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sistem Maintenance</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body { margin: 0; padding: 0; background: #eef2f5; font-family: "Segoe UI", sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; overflow: hidden; }
                .maintenance-card { background: white; width: 90%; max-width: 400px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; text-align: center; }
                .card-header { background: #1877f2; padding: 40px 20px; color: white; }
                .card-header i { font-size: 50px; margin-bottom: 15px; }
                .card-header h2 { margin: 0; font-size: 24px; font-weight: bold; }
                .card-header p { margin: 5px 0 0 0; font-size: 14px; opacity: 0.9; }
                .card-body { padding: 40px 30px; }
                .card-body i { font-size: 60px; color: #ff9800; margin-bottom: 20px; }
                .card-body h3 { margin: 0 0 10px 0; color: #333; font-size: 20px; }
                .card-body p { color: #666; font-size: 15px; line-height: 1.6; margin: 0; }
            </style>
        </head>
        <body>
            <div class="maintenance-card">
                <div class="card-header">
                    <i class="fas fa-hospital-user"></i>
                    <h2>RS Ludira Husada</h2>
                    <p>Portal Pasien Online</p>
                </div>
                <div class="card-body">
                    <i class="fas fa-tools"></i>
                    <h3>Sistem Sedang Maintenance</h3>
                    <p>Mohon maaf, Portal Pasien saat ini sedang dalam pembaruan rutin. Silakan coba beberapa saat lagi.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        exit; // Menghentikan seluruh proses PHP di bawahnya agar form asli tidak dimuat sama sekali
    }
}
// ----------------------------
// Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Mengambil nama dari session
$nama_pasien = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pasien';

// Mengambil daftar dokter unik dari database (maksimal 5 dokter untuk preview di Beranda)
$query_dokter = mysqli_query($koneksi, "SELECT DISTINCT nama_dokter, spesialis FROM jadwal_dokter LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - RS Ludira Husada Tama</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="mobile-app">
    
    <div class="header">
        <div class="header-top">
            <div class="greeting">
                Selamat Datang, 
                <span><?php echo htmlspecialchars($nama_pasien); ?> <i class="fas fa-chevron-down" style="font-size:12px;"></i></span>
            </div>
            <div class="header-icons">
                <i class="far fa-bell"></i>
                <a href="profil.php" style="color:white;"><i class="far fa-user-circle"></i></a>
            </div>
        </div>
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Cari Jadwal Dokter">
        </div>
    </div>

    <div class="content content-index">
        <div class="banner">
            <i class="fas fa-stethoscope" style="font-size:30px; margin-bottom:10px;"></i><br>
            "Konsultasi Dokter Spesialis dari Rumah"
        </div>

        <div class="darurat-card">
            <div class="darurat-icon">
                <i class="fas fa-ambulance"></i>
            </div>
            <div class="darurat-text">
                <h4>Darurat</h4>
                <p>Hubungi kami jika ada situasi darurat</p>
            </div>
        </div>

        <div class="section-header">
            <h3>Daftar Dokter</h3>
            <a href="jadwal.php">Lihat Semua</a> 
        </div>
        
        <div class="doctor-list">
            <?php 
            // Cek apakah data dokter tersedia
            if (mysqli_num_rows($query_dokter) > 0) {
                // Lakukan perulangan untuk menampilkan setiap dokter
                while($doc = mysqli_fetch_assoc($query_dokter)) : 
            ?>
                <div class="doctor-card-hz">
                    <div class="doc-img">
                        <i class="fas fa-user-md" style="font-size: 24px; color: white; display:flex; justify-content:center; align-items:center; height:100%; width:100%; background:#00bfff; border-radius:8px;"></i>
                    </div>
                    <div class="doc-info">
                        <h5><?php echo $doc['nama_dokter']; ?></h5>
                        <p><?php echo $doc['spesialis']; ?></p>
                    </div>
                </div>
            <?php 
                endwhile;
            } else {
                echo "<p style='font-size:12px; color:#888;'>Belum ada data dokter.</p>";
            }
            ?>
        </div>

        <br>
        <div class="section-header">
            <h3>Artikel Kesehatan</h3>
            <a href="infokes.php">Lihat Semua</a>
        </div>
        <div class="article-card">
            <div class="article-img"></div>
            <div class="article-info">
                <h5>Memahami Ages Pada Diabetes Melitus</h5>
                <p>16 Juni 2026</p>
            </div>
        </div>
        <div class="article-card">
            <div class="article-img" style="background-color:#20c997;"></div>
            <div class="article-info">
                <h5>Pentingnya Menjaga Pola Makan</h5>
                <p>10 Juni 2026</p>
            </div>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="index.php" class="nav-item active">
            <i class="fas fa-home"></i> Beranda
        </a>
        <a href="jadwal.php" class="nav-item">
            <i class="far fa-calendar-alt"></i> Jadwal
        </a>
        
        <a href="profil.php" class="nav-center">
            <i class="fas fa-user-plus"></i> Pasien
        </a>
        
        <a href="#" class="nav-item">
            <i class="fas fa-bed"></i> Bed
        </a>
        <a href="infokes.php" class="nav-item">
            <i class="far fa-file-alt"></i> Infokes
        </a>
    </div>

</div>

</body>
</html>