<?php
session_start();
if (!isset($_SESSION['karyawan_role']) || $_SESSION['karyawan_role'] !== 'administrasi') { header("Location: login.php"); exit; }
require '../koneksi.php'; 
date_default_timezone_set('Asia/Jakarta');

$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'lihat';

// 1. Logika Administrasi memanggil pasien (Mengubah status jadi Dilayani)
if ($aksi == 'layani' && isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($koneksi, "UPDATE periksa SET status = 'Dilayani' WHERE id = '$id'");
    header("Location: admin_antrean.php?tanggal=$filter_tanggal");
    exit;
}

// 2. Logika Pendaftaran Manual (Walk-In) oleh Administrasi
if (isset($_POST['simpan_manual'])) {
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jaminan = mysqli_real_escape_string($koneksi, $_POST['jaminan']);
    $klinik = mysqli_real_escape_string($koneksi, $_POST['klinik']);
    
    // Cek apakah pasien sudah pernah terdaftar di database sebelumnya
    $cek_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE nik = '$nik'");
    if (mysqli_num_rows($cek_pasien) == 0) {
        // Jika belum ada, buatkan akun otomatis (password default: 123)
        mysqli_query($koneksi, "INSERT INTO pasien (nik, nama, password) VALUES ('$nik', '$nama', '123')");
    }

    // Ambil nomor antrean terakhir di klinik & hari yang sama
    $cek_antrean = mysqli_query($koneksi, "SELECT MAX(no_antrean) AS max_q FROM periksa WHERE klinik = '$klinik' AND tanggal = '$filter_tanggal'");
    $data_q = mysqli_fetch_assoc($cek_antrean);
    $no_antrean_baru = $data_q['max_q'] ? $data_q['max_q'] + 1 : 1;

    // Masukkan ke tabel periksa (antrean)
    mysqli_query($koneksi, "INSERT INTO periksa (nik_pasien, no_wa, jaminan, klinik, tanggal, no_antrean, status) 
                            VALUES ('$nik', '-', '$jaminan', '$klinik', '$filter_tanggal', '$no_antrean_baru', 'Menunggu')");
    
    echo "<script>alert('Pasien berhasil didaftarkan secara manual!'); window.location='admin_antrean.php?tanggal=$filter_tanggal';</script>";
}

// Query mengambil daftar antrean
$query_antrean = mysqli_query($koneksi, "SELECT periksa.*, pasien.nama FROM periksa JOIN pasien ON periksa.nik_pasien = pasien.nik WHERE periksa.tanggal = '$filter_tanggal' ORDER BY no_antrean ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Antrean - SIMRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7fa; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #1e90ff; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 330px); }
        .card { background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; color: #888; text-transform: uppercase; font-size: 12px; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        .q-number { font-size: 20px; font-weight: 800; color: #1e90ff; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-wait { background: #fff4e5; color: #ff9800; }
        .badge-process { background: #e3f2fd; color: #1e90ff; }
        .badge-done { background: #d4edda; color: #28a745; }
        .btn-action { padding: 8px 16px; border-radius: 8px; text-decoration: none; color: white; font-size: 12px; font-weight: 600; cursor: pointer; border: none; display: inline-block;}
        .btn-layani { background: #ff9800; }
        .btn-primary { background: #1e90ff; }
        .btn-danger { background: #dc3545; }
        
        /* Form Manual */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>SIMRS</h2>
        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
            <strong><i class="fas fa-user-circle"></i> <?php echo $_SESSION['karyawan_nama']; ?></strong>
        </div>
        <p style="margin-top:20px; font-size:14px;"><i class="fas fa-calendar-day"></i> Administrasi Front Office</p>
        <a href="logout.php" style="color:#ff4d4d; text-decoration:none; display:block; margin-top:30px; font-weight:bold;">Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2 style="margin:0;">Antrean Pasien</h2>
            <div>
                <?php if($aksi == 'lihat'): ?>
                    <form style="display:inline-block; margin-right:15px;">
                        <input type="date" name="tanggal" value="<?php echo $filter_tanggal; ?>" onchange="this.form.submit()" style="padding:8px; border-radius:8px; border:1px solid #ddd;">
                    </form>
                    <a href="admin_antrean.php?aksi=tambah_manual" class="btn-action btn-primary"><i class="fas fa-user-plus"></i> Pendaftaran Pasien Offline</a>
                <?php else: ?>
                    <a href="admin_antrean.php" class="btn-action btn-danger"><i class="fas fa-arrow-left"></i> Kembali ke Antrean</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if($aksi == 'tambah_manual'): ?>
            <div class="card" style="max-width: 600px; background: #f8f9fa; border: 1px solid #1e90ff;">
                <h3 style="margin-top:0; color:#1e90ff;"><i class="fas fa-id-card"></i> Form Pendaftaran Offline (Walk-In)</h3>
                <p style="font-size:13px; color:#666;">Jika NIK belum terdaftar, sistem akan otomatis membuatkan akun dengan password default: <b>123</b>.</p>
                
                <form method="POST">
                    <div class="form-group">
                        <label>NIK Pasien (KTP)</label>
                        <input type="number" name="nik" required placeholder="Masukkan 16 digit NIK">
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap Pasien</label>
                        <input type="text" name="nama" required placeholder="Nama sesuai KTP">
                    </div>
                    <div class="form-group">
                        <label>Pilih Klinik / Poli</label>
                        <select name="klinik" required>
                            <option value="">-- Pilih Poliklinik --</option>
                            <option value="Klinik Umum">Klinik Umum</option>
                            <option value="Klinik Gigi">Klinik Gigi</option>
                            <option value="Klinik Anak 1">Klinik Anak 1</option>
                            <option value="Klinik Penyakit Dalam">Klinik Penyakit Dalam</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Jaminan</label>
                        <select name="jaminan" required>
                            <option value="Umum / Pribadi">Umum / Pribadi</option>
                            <option value="BPJS Kesehatan">BPJS Kesehatan</option>
                            <option value="Asuransi Swasta">Asuransi Swasta</option>
                        </select>
                    </div>
                    <button type="submit" name="simpan_manual" class="btn-action btn-primary" style="width:100%; padding:12px; font-size:14px;"><i class="fas fa-save"></i> Daftarkan Pasien Sekarang</button>
                </form>
            </div>

        <?php else: ?>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>No. Antrean</th>
                            <th>Nama Pasien</th>
                            <th>Klinik</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($query_antrean) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query_antrean)): ?>
                            <tr>
                                <td class="q-number">#<?php echo sprintf("%03d", $row['no_antrean']); ?></td>
                                <td><strong><?php echo $row['nama']; ?></strong><br><small style="color:#aaa;">NIK: <?php echo $row['nik_pasien']; ?></small></td>
                                <td><?php echo $row['klinik']; ?></td>
                                <td>
                                    <?php if($row['status'] == 'Menunggu' || $row['status'] == 'Menunggu Konfirmasi'): ?>
                                        <span class="badge badge-wait">MENUNGGU</span>
                                    <?php elseif($row['status'] == 'Selesai'): ?>
                                        <span class="badge badge-done">SELESAI</span>
                                    <?php else: ?>
                                        <span class="badge badge-process"><?php echo strtoupper($row['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'Menunggu' || $row['status'] == 'Menunggu Konfirmasi'): ?>
                                        <a href="?aksi=layani&id=<?php echo $row['id']; ?>&tanggal=<?php echo $filter_tanggal; ?>" class="btn-action btn-layani"><i class="fas fa-bullhorn"></i> Panggil Pasien</a>
                                    <?php else: ?>
                                        <small style="color:#888;"><i>Menunggu unit lain</i></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center; padding:20px; color:#888;">Belum ada pasien terdaftar pada tanggal ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>