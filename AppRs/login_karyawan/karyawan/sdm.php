<?php
session_start();
// Proteksi Role
if ($_SESSION['karyawan_role'] !== 'sdm') { header("Location: login.php"); exit; }
require '../koneksi.php';

// --- LOGIKA DATABASE ---

// 1. Simpan pegawai baru
if (isset($_POST['simpan_pegawai'])) {
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $status = $_POST['status'];

    $query_insert = "INSERT INTO pegawai (nip, nama, jabatan, no_hp, status, tanggal_masuk) 
                     VALUES ('$nip', '$nama', '$jabatan', '$no_hp', '$status', '$tanggal_masuk')";
    
    if (mysqli_query($koneksi, $query_insert)) {
        echo "<script>alert('Pegawai berhasil ditambahkan!'); window.location='sdm.php?page=pegawai';</script>";
    } else {
        echo "<script>alert('Gagal! Mungkin NIP sudah terdaftar.');</script>";
    }
}

// 2. Ubah Status Pegawai
if (isset($_GET['ubah_status']) && isset($_GET['id'])) {
    $id_pegawai = $_GET['id'];
    $status_baru = $_GET['ubah_status'];
    mysqli_query($koneksi, "UPDATE pegawai SET status = '$status_baru' WHERE id = '$id_pegawai'");
    header("Location: sdm.php?page=pegawai");
    exit;
}

// Navigasi Halaman Internal
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen SDM - SIMRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; margin: 0; display: flex; }
        
        /* Sidebar - Tema Ungu (#6f42c1) */
        .sidebar { width: 250px; background: #6f42c1; height: 100vh; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { font-size: 22px; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; }
        .sidebar nav a { color: white; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .sidebar nav a:hover, .sidebar nav a.active { background: rgba(255,255,255,0.2); }
        
        /* Main Content */
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 330px); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* Card Style */
        .card { background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px; }
        .grid-menu { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .stat-card { background: white; padding: 30px; border-radius: 15px; text-align: center; text-decoration: none; color: #333; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); border-bottom: 4px solid #6f42c1; }
        .stat-card i { font-size: 40px; color: #6f42c1; margin-bottom: 15px; }

        /* Table & Form */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; color: #888; text-transform: uppercase; font-size: 12px; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 11px; font-weight: bold; color: white; }
        .badge-aktif { background: #28a745; }
        .badge-nonaktif { background: #dc3545; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-purple { background: #6f42c1; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; }

        .btn-logout { background: rgba(255,255,255,0.1); text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fas fa-hospital-symbol"></i> SIMRS</h2>
        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <small style="opacity: 0.7;">Login Sebagai:</small><br>
            <strong><i class="fas fa-user-tie"></i> <?php echo $_SESSION['karyawan_nama']; ?></strong>
        </div>
        <nav>
            <p style="font-size: 11px; opacity: 0.6; text-transform: uppercase; margin-top: 20px;">Menu SDM</p>
            <a href="sdm.php?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="sdm.php?page=pegawai" class="<?php echo ($page == 'pegawai' || $page == 'tambah') ? 'active' : ''; ?>"><i class="fas fa-users"></i> Data Pegawai</a>
            <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        
        <?php if($page == 'dashboard'): ?>
            <div class="header">
                <h2>Dashboard SDM</h2>
                <div style="color: #888;"><?php echo date('d F Y'); ?></div>
            </div>
            <div class="grid-menu">
                <a href="sdm.php?page=pegawai" class="stat-card">
                    <i class="fas fa-id-card"></i>
                    <h3>Data Pegawai</h3>
                    <p>Kelola NIP, Jabatan, dan Status</p>
                </a>
            
            </div>

        <?php elseif($page == 'pegawai'): ?>
            <div class="header">
                <h2>Manajemen Pegawai</h2>
                <a href="sdm.php?page=tambah" class="btn-purple" style="width: auto;"><i class="fas fa-plus"></i> Tambah Pegawai</a>
            </div>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Nama Pegawai</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $q = mysqli_query($koneksi, "SELECT * FROM pegawai ORDER BY nama ASC");
                        while($row = mysqli_fetch_assoc($q)): ?>
                        <tr>
                            <td style="color:#6f42c1; font-weight:bold;"><?php echo $row['nip']; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['jabatan']; ?></td>
                            <td>
                                <span class="badge <?php echo $row['status'] == 'Aktif' ? 'badge-aktif' : 'badge-nonaktif'; ?>">
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Aktif'): ?>
                                    <a href="sdm.php?ubah_status=Nonaktif&id=<?php echo $row['id']; ?>" style="color:#dc3545; text-decoration:none; font-size:12px;" onclick="return confirm('Nonaktifkan pegawai?')">Nonaktifkan</a>
                                <?php else: ?>
                                    <a href="sdm.php?ubah_status=Aktif&id=<?php echo $row['id']; ?>" style="color:#28a745; text-decoration:none; font-size:12px;">Aktifkan</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'tambah'): ?>
            <div class="header">
                <h2>Tambah Pegawai Baru</h2>
                <a href="sdm.php?page=pegawai" style="color:#6f42c1; text-decoration:none;"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
            <div class="card" style="max-width: 600px;">
                <form action="" method="post">
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" required placeholder="Contoh: PEG-001">
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <select name="jabatan" required>
                            <option value="Dokter">Dokter</option>
                            <option value="Perawat">Perawat</option>
                            <option value="Kasir">Kasir</option>
                            <option value="Administrasi">Administrasi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" name="no_hp">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <input type="hidden" name="status" value="Aktif">
                    <button type="submit" name="simpan_pegawai" class="btn-purple">SIMPAN DATA PEGAWAI</button>
                </form>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>