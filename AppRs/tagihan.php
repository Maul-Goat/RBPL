<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
$nik_pasien = $_SESSION['nik'];

// Ambil data tagihan pasien yang sedang login (JOIN antara tabel tagihan dan periksa)
$query_tagihan = mysqli_query($koneksi, "
    SELECT t.*, p.klinik, p.tanggal 
    FROM tagihan t 
    JOIN periksa p ON t.id_periksa = p.id 
    WHERE p.nik_pasien = '$nik_pasien' 
    ORDER BY t.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat & Tagihan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="mobile-app">
    <div class="top-header"><a href="profil.php"><i class="fas fa-arrow-left"></i></a> Riwayat & Tagihan</div>
    <div class="content">
        <?php if(mysqli_num_rows($query_tagihan) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query_tagihan)): ?>
                <div class="card" style="border-left: 5px solid #1e90ff;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                        <strong style="color: #333;"><?php echo $row['klinik']; ?></strong>
                        <span style="font-size: 12px; color: #888;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                    </div>
                    <p style="font-size: 13px; color: #555; margin: 5px 0;"><strong>Diagnosa:</strong> <?php echo $row['riwayat_pemeriksaan']; ?></p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                        <h4 style="margin: 0; color: #ff4d4d;">Rp <?php echo number_format($row['jumlah_tagihan'], 0, ',', '.'); ?></h4>
                        <span style="background: <?php echo ($row['status_bayar']=='Lunas') ? '#28a745' : '#ffc107'; ?>; color: <?php echo ($row['status_bayar']=='Lunas') ? 'white' : 'black'; ?>; padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: bold;">
                            <?php echo $row['status_bayar']; ?>
                        </span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; color: #888; margin-top: 50px;">Belum ada tagihan.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
