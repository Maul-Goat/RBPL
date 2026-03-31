<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$id_periksa = isset($_GET['id']) ? $_GET['id'] : 0;
$nik_pasien = $_SESSION['nik'];

// Ambil data tiket pasien ini
$query = mysqli_query($koneksi, "SELECT * FROM periksa WHERE id = '$id_periksa' AND nik_pasien = '$nik_pasien'");

if (mysqli_num_rows($query) == 0) {
    echo "<script>alert('Data tiket tidak ditemukan!'); window.location='profil.php';</script>";
    exit;
}

$data = mysqli_fetch_assoc($query);
$nama_pasien = $_SESSION['nama'];
$tgl_format = date('d F Y', strtotime($data['tanggal']));
$klinik_pasien = $data['klinik'];
$tanggal_pasien = $data['tanggal'];

// LOGIKA ANTREAN BERJALAN: Mencari nomor antrean yang statusnya 'Diperiksa' atau nomor 'Selesai' tertinggi di klinik dan tanggal yang sama
$query_berjalan = mysqli_query($koneksi, "
    SELECT MAX(no_antrean) as antrean_sekarang 
    FROM periksa 
    WHERE klinik = '$klinik_pasien' 
    AND tanggal = '$tanggal_pasien' 
    AND (status = 'Diperiksa' OR status = 'Selesai')
");
$data_berjalan = mysqli_fetch_assoc($query_berjalan);
$antrean_berjalan = ($data_berjalan['antrean_sekarang'] != null) ? $data_berjalan['antrean_sekarang'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tiket Antrean</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .ticket-container {
            background: white; border-radius: 15px; margin: 20px; padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; border-top: 8px solid #1e90ff;
        }
        .ticket-hospital { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .ticket-clinic { font-size: 14px; color: #1e90ff; font-weight: bold; margin-bottom: 20px; }
        .ticket-detail { display: flex; justify-content: space-between; border-bottom: 1px dashed #ccc; padding-bottom: 15px; margin-bottom: 20px; text-align: left; }
        .ticket-detail div p { margin: 0 0 5px 0; font-size: 11px; color: #888; text-transform: uppercase; }
        .ticket-detail div h4 { margin: 0; font-size: 14px; color: #333; }
        
        /* Desain Angka Besar */
        .queue-box { margin-top: 10px; }
        .queue-title { font-size: 14px; color: #666; font-weight: bold; margin-bottom: 10px; }
        .queue-number {
            font-size: 80px; font-weight: bold; color: #1e90ff; 
            background: #f0f8ff; width: 150px; height: 150px; line-height: 150px;
            margin: 0 auto; border-radius: 50%; border: 5px solid #1e90ff;
            box-shadow: 0 4px 10px rgba(30,144,255,0.2);
        }
        .queue-status { margin-top: 20px; font-size: 12px; color: #28a745; font-weight: bold; background: #d4edda; padding: 8px; border-radius: 8px; }
    </style>
</head>
<body>

<div class="mobile-app">
    <div class="top-header">
        <a href="profil.php"><i class="fas fa-arrow-left"></i></a> Tiket Antrean
    </div>

    <div class="ticket-container">
        <div class="ticket-hospital">RS Ludira Husada Tama</div>
        <div class="ticket-clinic"><?php echo $data['klinik']; ?></div>
        
        <div class="ticket-detail">
            <div>
                <p>Nama Pasien</p>
                <h4><?php echo htmlspecialchars($nama_pasien); ?></h4>
            </div>
            <div style="text-align: right;">
                <p>Tanggal</p>
                <h4><?php echo $tgl_format; ?></h4>
            </div>
        </div>

        <div class="queue-box">
            <div class="queue-title">NOMOR ANTREAN ANDA</div>
            <div class="queue-number">
                <?php 
                // Format angka jadi 3 digit, misal: 1 jadi 001
                echo sprintf("%03d", $data['no_antrean']); 
                ?>
            </div>
        </div>

        <div class="queue-status" style="background: <?php echo ($data['status']=='Selesai') ? '#d4edda' : (($data['status']=='Diperiksa') ? '#fff3cd' : '#e2e3e5'); ?>; color: #333;">
            <strong>Status Anda:</strong> <?php echo $data['status']; ?>
        </div>

        <div style="margin-top: 15px; padding: 15px; background: #1e90ff; color: white; border-radius: 10px;">
            <div style="font-size: 12px; margin-bottom: 5px;">ANTREAN SAAT INI DILAYANI</div>
            <div style="font-size: 24px; font-weight: bold;">
                <?php echo ($antrean_berjalan == 0) ? "Belum Dimulai" : "#" . sprintf("%03d", $antrean_berjalan); ?>
            </div>
        </div>
        
        <p style="font-size: 11px; color: #aaa; margin-top: 25px;">Tunjukkan layar ini kepada petugas klinik saat nama Anda dipanggil.</p>
    </div>

</div>

</body>
</html>