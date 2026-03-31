<?php
session_start();
if (!isset($_SESSION['karyawan_role']) || $_SESSION['karyawan_role'] !== 'kasir') { header("Location: login.php"); exit; }
require '../koneksi.php'; // Penyesuaian path koneksi

// Logika menyimpan tagihan
if (isset($_POST['simpan_tagihan'])) {
    $id_p = $_POST['id_periksa'];
    // Gabungkan diagnosa dan resep sebagai riwayat pemeriksaan untuk arsip kasir
    $riwayat = mysqli_real_escape_string($koneksi, $_POST['riwayat_lengkap']);
    $total = $_POST['jumlah_tagihan'];
    
    // 1. Simpan ke tabel tagihan (Status langsung dibuat Lunas untuk kemudahan)
    mysqli_query($koneksi, "INSERT INTO tagihan (id_periksa, riwayat_pemeriksaan, jumlah_tagihan, status_bayar) VALUES ('$id_p', '$riwayat', '$total', 'Lunas')");
    
    // 2. Ubah status antrean pasien menjadi 'Selesai'
    mysqli_query($koneksi, "UPDATE periksa SET status = 'Selesai' WHERE id = '$id_p'");
    
    echo "<script>alert('Pembayaran Berhasil! Status antrean pasien telah Selesai.'); window.location='kasir.php';</script>";
}

// Ambil HANYA pasien yang statusnya 'Menunggu Pembayaran'
$query_pasien = mysqli_query($koneksi, "
    SELECT p.id, pa.nama, rm.diagnosa, ro.rincian_resep 
    FROM periksa p 
    JOIN pasien pa ON p.nik_pasien = pa.nik 
    LEFT JOIN rekam_medis rm ON p.id = rm.id_periksa 
    LEFT JOIN resep_obat ro ON p.id = ro.id_periksa 
    WHERE p.status = 'Menunggu Pembayaran'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kasir - SIMRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #28a745; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 330px); }
        .card { background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 20px; }
        .info-box { background: #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; border-left: 4px solid #28a745; }
        input { width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; margin-bottom: 15px; box-sizing: border-box; font-size: 16px; }
        .btn-save { background: #28a745; color: white; border: none; padding: 15px; border-radius: 10px; width: 100%; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>SIMRS</h2>
        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
            <strong><i class="fas fa-cash-register"></i> <?php echo $_SESSION['karyawan_nama']; ?></strong>
        </div>
        <p style="margin-top:20px; font-size:14px;"><i class="fas fa-money-bill-wave"></i> Pembayaran Kasir</p>
        <a href="logout.php" class="btn-logout" style="color: #ff4d4d; text-decoration: none; display: block; margin-top: 30px; font-weight:bold;">Logout</a>
    </div>
    
    <div class="main-content">
        <h2>Daftar Pasien Menunggu Pembayaran</h2>
        
        <?php if(mysqli_num_rows($query_pasien) > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
                <?php while($row = mysqli_fetch_assoc($query_pasien)): ?>
                    <div class="card">
                        <h3 style="margin-top:0; color:#28a745;"><i class="fas fa-user-circle"></i> Pasien: <?php echo $row['nama']; ?></h3>
                        
                        <div class="info-box">
                            <strong>Diagnosa:</strong> <?php echo $row['diagnosa'] ? $row['diagnosa'] : '-'; ?><br><br>
                            <strong>Resep Obat:</strong><br>
                            <?php echo $row['rincian_resep'] ? nl2br($row['rincian_resep']) : '-'; ?>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="id_periksa" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="riwayat_lengkap" value="Diagnosa: <?php echo $row['diagnosa']; ?> | Obat: <?php echo $row['rincian_resep']; ?>">
                            
                            <label><strong>Total Tagihan (Rp)</strong></label>
                            <input type="number" name="jumlah_tagihan" placeholder="Contoh: 150000" required>
                            
                            <button type="submit" name="simpan_tagihan" class="btn-save">
                                <i class="fas fa-check-circle"></i> SIMPAN & SELESAIKAN
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <p style="color:#888; text-align:center;"><i class="fas fa-check-circle" style="font-size:30px; display:block; margin-bottom:10px; color:#ddd;"></i> Semua tagihan pasien sudah lunas.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>