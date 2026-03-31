<?php
session_start();
if (!isset($_SESSION['karyawan_role']) || $_SESSION['karyawan_role'] !== 'perawat') { header("Location: login.php"); exit; }
require '../koneksi.php';

// Logika menyimpan pemeriksaan awal
if (isset($_POST['simpan_pemeriksaan'])) {
    $id_periksa = $_POST['id_periksa'];
    $tensi = mysqli_real_escape_string($koneksi, $_POST['tensi']);
    $suhu = mysqli_real_escape_string($koneksi, $_POST['suhu']);
    $keluhan = mysqli_real_escape_string($koneksi, $_POST['keluhan']);

    // Masukkan ke rekam medis
    mysqli_query($koneksi, "INSERT INTO rekam_medis (id_periksa, tensi, suhu, keluhan) VALUES ('$id_periksa', '$tensi', '$suhu', '$keluhan')");
    
    // Ubah status antrean agar masuk ke ruangan Dokter
    mysqli_query($koneksi, "UPDATE periksa SET status = 'Menunggu Dokter' WHERE id = '$id_periksa'");
    
    echo "<script>alert('Data awal pasien berhasil disimpan! Pasien diteruskan ke Dokter.'); window.location='perawat_klinis.php';</script>";
}

// Ambil pasien yang statusnya 'Dilayani' (baru dipanggil admin)
$query_pasien = mysqli_query($koneksi, "SELECT periksa.id, pasien.nama, periksa.klinik FROM periksa JOIN pasien ON periksa.nik_pasien = pasien.nik WHERE periksa.status = 'Dilayani'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pemeriksaan Awal - Perawat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #17a2b8; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 330px); }
        .card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-save { background: #17a2b8; color: white; padding: 12px; border: none; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>SIMRS</h2>
        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
            <strong><i class="fas fa-user-nurse"></i> <?php echo $_SESSION['karyawan_nama']; ?></strong>
        </div>
        <p style="margin-top:20px; font-size:14px;"><i class="fas fa-stethoscope"></i> Pemeriksaan Awal</p>
        <a href="lab.php" style="color:white; text-decoration:none; display:block; margin-top:10px; font-size:14px;"><i class="fas fa-flask"></i> Upload Hasil Lab</a>
        <a href="logout.php" style="color:#ff4d4d; text-decoration:none; display:block; margin-top:30px; font-weight:bold;">Logout</a>
    </div>

    <div class="main-content">
        <h2>Pemeriksaan Awal (Triage)</h2>
        <div class="card">
            <form method="POST">
                <label>Pilih Pasien</label>
                <select name="id_periksa" required>
                    <option value="">-- Pilih Pasien dari Ruang Tunggu --</option>
                    <?php while($p = mysqli_fetch_assoc($query_pasien)) { echo "<option value='".$p['id']."'>".$p['nama']." - ".$p['klinik']."</option>"; } ?>
                </select>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div><label>Tekanan Darah (Tensi)</label><input type="text" name="tensi" placeholder="Cth: 120/80" required></div>
                    <div><label>Suhu Tubuh (°C)</label><input type="text" name="suhu" placeholder="Cth: 36.5" required></div>
                </div>
                <label>Keluhan Pasien</label>
                <textarea name="keluhan" rows="3" placeholder="Tuliskan keluhan yang dirasakan pasien..." required></textarea>
                <button type="submit" name="simpan_pemeriksaan" class="btn-save">SIMPAN & TERUSKAN KE DOKTER</button>
            </form>
        </div>
    </div>
</body>
</html>