<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - RS Ludira Husada Tama</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="mobile-app">
    <div class="top-header"><a href="index.php"><i class="fas fa-arrow-left"></i></a> Info RS</div>
    
    <div style="width: 100%; height: 180px; background: #007bff; display:flex; justify-content:center; align-items:center; color:white;">
        <i class="far fa-hospital" style="font-size: 80px;"></i>
    </div>

    <div class="content">
        <h3 style="margin-top:0; color:#1e90ff;">RS Ludira Husada Tama</h3>
        <p style="font-size: 13px; color: #555; line-height: 1.6; text-align: justify;">
            Rumah Sakit Ludira Husada Tama adalah fasilitas pelayanan kesehatan modern yang berkomitmen untuk memberikan pelayanan medis terbaik dan profesional bagi masyarakat. Dengan mengedepankan inovasi teknologi seperti Portal Pasien Online ini, kami berusaha memberikan kemudahan akses kesehatan bagi Anda dan keluarga.
        </p>
        
        <div class="card">
            <h4 style="margin:0 0 10px 0; font-size:14px;"><i class="fas fa-map-marker-alt" style="color:#ff4d4d;"></i> Lokasi Kami</h4>
            <p style="font-size: 12px; color: #666; margin:0;">Jl. Kesehatan No. 123, Kota Yogyakarta, Indonesia.</p>
        </div>

        <div class="card">
            <h4 style="margin:0 0 10px 0; font-size:14px;"><i class="fas fa-phone-alt" style="color:#28a745;"></i> Kontak Darurat</h4>
            <p style="font-size: 12px; color: #666; margin:0;">IGD: (0274) 123-4567<br>CS: 0811-2856-210</p>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="index.php" class="nav-item"><i class="fas fa-home"></i> Beranda</a>
        <a href="jadwal.php" class="nav-item"><i class="far fa-calendar-alt"></i> Jadwal</a>
        <a href="profil.php" class="nav-center"><i class="fas fa-user-plus"></i> Pasien</a>
        <a href="#" class="nav-item"><i class="fas fa-bed"></i> Bed</a>
        <a href="infokes.php" class="nav-item active"><i class="far fa-file-alt"></i> Infokes</a>
    </div>
</div>
</body>
</html>
