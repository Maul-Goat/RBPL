<?php
session_start();
// Pastikan hanya manajer yang bisa masuk
if (!isset($_SESSION['karyawan_role']) || $_SESSION['karyawan_role'] !== 'manajer') { header("Location: login.php"); exit; }

// Pastikan path koneksi ini sesuai dengan foldermu
require '../koneksi.php'; 

// --- LOGIKA DEVOPS (MAINTENANCE) ---
if (isset($_POST['toggle_maintenance'])) {
    $status_baru = $_POST['status_maintenance'];
    mysqli_query($koneksi, "UPDATE pengaturan SET nilai = '$status_baru' WHERE nama_pengaturan = 'maintenance_mode'");
    echo "<script>alert('Status Server berhasil diubah!'); window.location='laporan.php';</script>";
}

// Cek status saat ini
$q_mt = mysqli_query($koneksi, "SELECT nilai FROM pengaturan WHERE nama_pengaturan = 'maintenance_mode'");
if ($q_mt && mysqli_num_rows($q_mt) > 0) {
    $mt = mysqli_fetch_assoc($q_mt);
    $status_mt = $mt['nilai'];
} else {
    $status_mt = '0'; // Default jika tabel pengaturan belum terisi
}
// -----------------------------------

// 1. Hitung Total Kunjungan Pasien
$q_pasien = mysqli_query($koneksi, "SELECT COUNT(id) AS total_kunjungan FROM periksa");
$data_pasien = mysqli_fetch_assoc($q_pasien);
$kunjungan = $data_pasien['total_kunjungan'];

// 2. Hitung Total Pendapatan (Hanya yang Lunas)
$q_uang = mysqli_query($koneksi, "SELECT SUM(jumlah_tagihan) AS total_uang FROM tagihan WHERE status_bayar = 'Lunas'");
$data_uang = mysqli_fetch_assoc($q_uang);
$pendapatan = $data_uang['total_uang'] ? $data_uang['total_uang'] : 0;

// 3. Ambil data transaksi terbaru untuk tabel laporan
$q_detail = mysqli_query($koneksi, "
    SELECT t.*, p.nama, pr.klinik, pr.tanggal 
    FROM tagihan t 
    JOIN periksa pr ON t.id_periksa = pr.id 
    JOIN pasien p ON pr.nik_pasien = p.nik 
    ORDER BY t.id DESC LIMIT 10
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan & DevOps - SIMRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #fd7e14; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 330px); }
        .stat-card { background: white; padding: 25px; border-radius: 15px; border-left: 5px solid #fd7e14; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .stat-card h3 { font-size: 28px; margin: 10px 0 0 0; color: #333; }
        .table-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f8f9fa; color: #888; padding: 12px; text-align: left; font-size: 13px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>SIMRS</h2>
        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
            <strong><i class="fas fa-user-tie"></i> <?php echo $_SESSION['karyawan_nama']; ?></strong>
        </div>
        <p style="margin-top:20px; font-size:14px;"><i class="fas fa-chart-line"></i> Dashboard Manajer</p>
        <a href="logout.php" style="color: #fff; text-decoration: none; background: rgba(255,0,0,0.5); padding: 10px; border-radius: 8px; display: block; margin-top: 20px; text-align: center; font-weight: bold;">Logout</a>
    </div>

    <div class="main-content">
        <h2 style="margin-top:0;">Laporan Strategis Rumah Sakit</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="stat-card">
                <small style="color:#888; font-weight:bold; text-transform:uppercase;">Total Kunjungan Pasien</small>
                <h3 style="color:#fd7e14;"><i class="fas fa-users"></i> <?php echo $kunjungan; ?> Orang</h3>
            </div>
            <div class="stat-card" style="border-left-color: #28a745;">
                <small style="color:#888; font-weight:bold; text-transform:uppercase;">Total Pendapatan Kasir</small>
                <h3 style="color:#28a745;"><i class="fas fa-wallet"></i> Rp <?php echo number_format($pendapatan, 0, ',', '.'); ?></h3>
            </div>
        </div>

        <div class="table-card" style="border-left: 5px solid #dc3545; margin-top: 20px;">
            <h3 style="margin-top:0; color:#dc3545;"><i class="fas fa-tools"></i> Kontrol Server (DevOps Mode)</h3>
            <p style="color:#666; font-size:14px;">Aktifkan mode ini jika sistem sedang melakukan update modul. Pasien tidak akan bisa mengakses portal aplikasi mandiri, namun seluruh karyawan tetap bisa bekerja secara internal.</p>
            
            <form method="POST">
                <input type="hidden" name="status_maintenance" value="<?php echo $status_mt == '1' ? '0' : '1'; ?>">
                
                <?php if($status_mt == '1'): ?>
                    <button type="submit" name="toggle_maintenance" style="background:#28a745; color:white; padding:12px 20px; border:none; border-radius:8px; cursor:pointer; font-weight:bold;"><i class="fas fa-play"></i> Matikan Mode Maintenance (Online-kan)</button>
                    <span style="color:#dc3545; font-weight:bold; margin-left:15px; font-size:14px;"><i class="fas fa-exclamation-triangle"></i> SISTEM PORTAL PASIEN SEDANG OFFLINE</span>
                <?php else: ?>
                    <button type="submit" name="toggle_maintenance" style="background:#dc3545; color:white; padding:12px 20px; border:none; border-radius:8px; cursor:pointer; font-weight:bold;"><i class="fas fa-stop"></i> Aktifkan Mode Maintenance (Offline-kan)</button>
                    <span style="color:#28a745; font-weight:bold; margin-left:15px; font-size:14px;"><i class="fas fa-check-circle"></i> SISTEM ONLINE NORMAL</span>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-card">
            <h3 style="margin-top:0;"><i class="fas fa-history"></i> 10 Transaksi Pembayaran Terakhir</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Pasien</th>
                        <th>Klinik</th>
                        <th>Rincian Layanan</th>
                        <th>Total Tagihan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($q_detail)): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td><strong><?php echo $row['nama']; ?></strong></td>
                        <td><?php echo $row['klinik']; ?></td>
                        <td><small style="color:#666;"><?php echo $row['riwayat_pemeriksaan']; ?></small></td>
                        <td style="color:#28a745; font-weight:bold;">Rp <?php echo number_format($row['jumlah_tagihan'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>