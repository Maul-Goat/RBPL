<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) { 
    header("Location: login.php"); 
    exit; 
}

// Mengambil NIK pasien dari session
$nik_pasien = $_SESSION['nik'];

// Query untuk mengambil hasil pemeriksaan khusus untuk pasien ini
$query_hasil = mysqli_query($koneksi, "
    SELECT hp.*, p.klinik, p.tanggal 
    FROM hasil_pemeriksaan hp 
    JOIN periksa p ON hp.id_periksa = p.id 
    WHERE p.nik_pasien = '$nik_pasien' 
    ORDER BY hp.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Lab & Radiologi - RS Ludira Husada</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .hasil-card {
            background: white; border-radius: 12px; padding: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px;
            border-top: 5px solid #17a2b8;
        }
        .hasil-header {
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;
        }
        .hasil-title { font-weight: bold; color: #333; font-size: 15px; }
        .hasil-date { font-size: 12px; color: #888; }
        
        /* Desain area foto Rontgen/Lab */
        .foto-container {
            width: 100%; background: #f8f9fa; border-radius: 8px;
            overflow: hidden; text-align: center; margin-bottom: 15px;
            border: 1px solid #ddd; position: relative;
        }
        .foto-container img {
            width: 100%; height: auto; display: block;
        }
        /* Ikon placeholder jika foto tidak diload dengan baik */
        .foto-placeholder {
            padding: 40px 20px; color: #ccc;
        }
        
        .keterangan-box {
            background: #f0f8ff; padding: 12px; border-radius: 8px;
            border-left: 4px solid #1e90ff; font-size: 13px; color: #555;
            line-height: 1.5;
        }
        .btn-download {
            display: block; text-align: center; background: #17a2b8;
            color: white; padding: 10px; border-radius: 8px; text-decoration: none;
            font-weight: bold; margin-top: 15px; font-size: 14px;
        }
        .btn-download:hover { background: #138496; }
    </style>
</head>
<body>

<div class="mobile-app">
    <div class="top-header">
        <a href="profil.php"><i class="fas fa-arrow-left"></i></a> Hasil Pemeriksaan
    </div>

    <div class="content">
        <?php if(mysqli_num_rows($query_hasil) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query_hasil)): ?>
                
                <div class="hasil-card">
                    <div class="hasil-header">
                        <div class="hasil-title">
                            <i class="fas fa-x-ray" style="color: #17a2b8; margin-right: 5px;"></i> 
                            <?php echo $row['jenis_pemeriksaan']; ?>
                        </div>
                        <div class="hasil-date"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></div>
                    </div>
                    
                    <div style="font-size: 12px; color: #666; margin-bottom: 10px;">
                        <strong>Klinik Pengirim:</strong> <?php echo $row['klinik']; ?>
                    </div>

                    <div class="foto-container">
                        <?php if(!empty($row['file_foto'])): ?>
                           <img src="img/lab/<?php echo $row['file_foto']; ?>" alt="Hasil Radiologi">
                        <?php else: ?>
                            <div class="foto-placeholder">
                                <i class="fas fa-image" style="font-size: 40px; margin-bottom: 10px;"></i>
                                <br>Tidak ada foto yang dilampirkan
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(!empty($row['file_foto'])): ?>
                        <a href="img/lab/<?php echo $row['file_foto']; ?>" download class="btn-download">
                            <i class="fas fa-download"></i> Unduh Hasil (PDF/JPG)
                        </a>
                    <?php endif; ?>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; color: #888; margin-top: 50px;">
                <i class="fas fa-folder-open" style="font-size: 50px; color: #ddd; margin-bottom: 15px;"></i>
                <p>Belum ada hasil laboratorium atau radiologi untuk Anda.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>