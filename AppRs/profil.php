<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$nama_pasien = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pasien';
$nik_pasien = isset($_SESSION['nik']) ? $_SESSION['nik'] : '-';

// Ambil riwayat pendaftaran pasien ini, urutkan dari yang terbaru
$query_riwayat = mysqli_query($koneksi, "SELECT * FROM periksa WHERE nik_pasien = '$nik_pasien' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pasien - RS Ludira Husada Tama</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .antrean-link { text-decoration: none; color: inherit; display: block; }
        .antrean-card:hover { border-color: #00bfff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="mobile-app">
    <div class="top-header">
        <a href="index.php"><i class="fas fa-arrow-left"></i></a> Profil Pasien
    </div>

    <div class="content">
        <div class="profile-card">
            <i class="fas fa-user-circle profile-icon"></i>
            <div class="profile-info">
                <h3><?php echo htmlspecialchars($nama_pasien); ?></h3>
                <p>NIK: <?php echo htmlspecialchars($nik_pasien); ?></p>
            </div>
        </div>

        <h4 class="section-title">Menu Utama</h4>
        <div class="menu-list">
            <a href="admisi.php" class="menu-item">
                <div><i class="fas fa-heartbeat icon-left"></i> Admisi</div>
                <i class="fas fa-chevron-right icon-right"></i>
            </a>
            <a href="tagihan.php" class="menu-item">
                <div><i class="fas fa-file-invoice-dollar icon-left"></i> Tagihan</div>
                <i class="fas fa-chevron-right icon-right"></i>
            </a>
            <a href="hasil_pemeriksaan.php" class="menu-item">
                <div><i class="fas fa-flask icon-left"></i> Hasil Lab / Radiologi</div>
                <i class="fas fa-chevron-right icon-right"></i>
            </a>
            <a href="logout.php" class="menu-item" style="color: #dc3545; font-weight: bold;">
                <div><i class="fas fa-sign-out-alt icon-left" style="color: #dc3545;"></i> Keluar</div>
            </a>
        </div>

        <h4 class="section-title">Antrean Pasien</h4>
        
        <?php 
        if (mysqli_num_rows($query_riwayat) > 0) {
            while($row = mysqli_fetch_assoc($query_riwayat)) {
                // Format tanggal agar lebih rapi (contoh: 18 Juni 2026)
                $tgl_format = date('d F Y', strtotime($row['tanggal']));
        ?>
            <a href="tiket.php?id=<?php echo $row['id']; ?>" class="antrean-link">
                <div class="antrean-card">
                    <div class="antrean-header"><?php echo $row['klinik']; ?></div>
                    <div class="antrean-status">
                        <div>
                            <div style="font-weight:bold; font-size:14px; color:#333;"><?php echo htmlspecialchars($nama_pasien); ?></div>
                            <div style="font-size:11px; color:#888;">Tanggal: <?php echo $tgl_format; ?></div>
                        </div>
                        <div class="badge-verified">KLIK DETAIL</div>
                    </div>
                </div>
            </a>
        <?php 
            }
        } else {
            echo '<div style="text-align:center; color:#888; font-size:12px; padding:20px; background:white; border-radius:10px;">Belum ada riwayat pendaftaran.</div>';
        }
        ?>
    </div>

    <div class="bottom-nav">
        <a href="index.php" class="nav-item"><i class="fas fa-home"></i> Beranda</a>
        <a href="jadwal.php" class="nav-item"><i class="far fa-calendar-alt"></i> Jadwal</a>
        <a href="profil.php" class="nav-center"><i class="fas fa-user-plus"></i> Pasien</a>
        <a href="#" class="nav-item"><i class="fas fa-bed"></i> Bed</a>
        <a href="infokes.php" class="nav-item"><i class="far fa-file-alt"></i> Infokes</a>
    </div>
</div>

</body>
</html>