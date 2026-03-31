<?php
session_start();
if (!isset($_SESSION['karyawan_role']) || $_SESSION['karyawan_role'] !== 'dokter') { header("Location: login.php"); exit; }
require '../koneksi.php';

// Logika menyimpan diagnosa dan resep
if (isset($_POST['simpan_diagnosa'])) {
    $id_periksa = $_POST['id_periksa'];
    $diagnosa = mysqli_real_escape_string($koneksi, $_POST['diagnosa']);
    $resep = mysqli_real_escape_string($koneksi, $_POST['resep']);

    // Update diagnosa ke rekam medis
    mysqli_query($koneksi, "UPDATE rekam_medis SET diagnosa = '$diagnosa' WHERE id_periksa = '$id_periksa'");
    
    // Simpan resep obat
    mysqli_query($koneksi, "INSERT INTO resep_obat (id_periksa, rincian_resep) VALUES ('$id_periksa', '$resep')");
    
    // Ubah status antrean agar pasien pergi ke Kasir
    mysqli_query($koneksi, "UPDATE periksa SET status = 'Menunggu Pembayaran' WHERE id = '$id_periksa'");
    
    echo "<script>alert('Pemeriksaan selesai! Pasien diarahkan ke Kasir.'); window.location='dokter.php';</script>";
}

// Ambil pasien yang statusnya 'Menunggu Dokter' (sudah dicek perawat)
$query_pasien = mysqli_query($koneksi, "
    SELECT p.id, pa.nama, rm.tensi, rm.suhu, rm.keluhan 
    FROM periksa p 
    JOIN pasien pa ON p.nik_pasien = pa.nik 
    JOIN rekam_medis rm ON p.id = rm.id_periksa 
    WHERE p.status = 'Menunggu Dokter'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ruang Pemeriksaan - Dokter</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #007bff; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 330px); }
        .card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        textarea { width: 100%; padding: 12px; margin-top: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-primary { background: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .info-box { background: #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; border-left: 4px solid #17a2b8; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>SIMRS</h2>
        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
            <strong><i class="fas fa-user-md"></i> <?php echo $_SESSION['karyawan_nama']; ?></strong>
        </div>
        <p style="margin-top:20px; font-size:14px;"><i class="fas fa-laptop-medical"></i> Ruang Pemeriksaan</p>
        <a href="logout.php" style="color:#ff4d4d; text-decoration:none; display:block; margin-top:30px; font-weight:bold;">Logout</a>
    </div>

    <div class="main-content">
        <h2>Daftar Pasien Menunggu Pemeriksaan</h2>
        
        <?php if(mysqli_num_rows($query_pasien) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query_pasien)): ?>
                <div class="card">
                    <h3 style="margin-top:0; color:#007bff;"><i class="fas fa-user"></i> Pasien: <?php echo $row['nama']; ?></h3>
                    
                    <div class="info-box">
                        <strong>Laporan Perawat:</strong><br>
                        Tensi: <?php echo $row['tensi']; ?> | Suhu: <?php echo $row['suhu']; ?>°C <br>
                        Keluhan: <i>"<?php echo $row['keluhan']; ?>"</i>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="id_periksa" value="<?php echo $row['id']; ?>">
                        
                        <label><strong>Diagnosa Penyakit (ICD-10 / Medis)</strong></label>
                        <textarea name="diagnosa" rows="3" placeholder="Tulis diagnosa penyakit pasien di sini..." required></textarea>
                        
                        <label style="display:block; margin-top:15px;"><strong>Resep Obat (E-Prescription)</strong></label>
                        <textarea name="resep" rows="3" placeholder="Contoh: Paracetamol 500mg (3x1), Amoxicillin (2x1)" required></textarea>
                        
                        <button type="submit" name="simpan_diagnosa" class="btn-primary" style="margin-top:15px;">
                            <i class="fas fa-save"></i> Simpan Diagnosa & Resep
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card">
                <p style="color:#888; text-align:center;"><i class="fas fa-coffee" style="font-size:30px; display:block; margin-bottom:10px;"></i> Belum ada pasien di ruang tunggu dokter.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>