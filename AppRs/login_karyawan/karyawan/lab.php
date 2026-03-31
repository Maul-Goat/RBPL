<?php
session_start();
if (!isset($_SESSION['karyawan_role']) || $_SESSION['karyawan_role'] !== 'perawat') { header("Location: login.php"); exit; }
require '../koneksi.php'; // Pastikan path koneksinya benar

// --- LOGIKA UPLOAD ---
if (isset($_POST['upload'])) {
    $id_periksa = $_POST['id_periksa'];
    $nama_file  = $_FILES['gambar']['name'];
    $tmp_name   = $_FILES['gambar']['tmp_name'];
    
    // Simpan ke folder img/lab utama (naik 2 folder)
    $upload_dir = "../../img/lab/";
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

    if (move_uploaded_file($tmp_name, $upload_dir . $nama_file)) {
        $query = "INSERT INTO hasil_pemeriksaan (id_periksa, file_foto, jenis_pemeriksaan, keterangan) 
                  VALUES ('$id_periksa', '$nama_file', 'Lab/Radiologi', '-')";
        mysqli_query($koneksi, $query);
        echo "<script>alert('Hasil pemeriksaan berhasil disimpan!'); window.location='lab.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lab & Radiologi - SIMRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #17a2b8; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 330px); }
        .card { background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px; }
        .btn-upload { background: #17a2b8; color: white; border: none; padding: 15px; border-radius: 10px; width: 100%; font-weight: bold; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; color: #888; padding: 15px; text-align: left; font-size: 12px; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>SIMRS</h2>
        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
            <strong><i class="fas fa-user-nurse"></i> <?php echo $_SESSION['karyawan_nama']; ?></strong>
        </div>
        
        <a href="perawat_klinis.php" style="color:white; text-decoration:none; display:block; margin-top:20px; font-size:14px;"><i class="fas fa-stethoscope"></i> Pemeriksaan Awal</a>
        <p style="margin-top:10px; font-size:14px;"><i class="fas fa-flask"></i> Upload Hasil Lab</p>
        <a href="logout.php" style="color:#ff4d4d; text-decoration:none; display:block; margin-top:30px; font-weight:bold;">Logout</a>
    </div>

    <div class="main-content">
        <h2>Modul Lab & Radiologi</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px;">
            <div class="card">
                <h3><i class="fas fa-upload"></i> Upload Hasil</h3>
                <form method="POST" enctype="multipart/form-data">
                    <label>Pilih Pasien:</label>
                    <select name="id_periksa" required style="width:100%; padding:10px; margin:10px 0; border-radius:8px;">
                        <option value="">-- Pilih Pasien --</option>
                        <?php
                        // Ambil id, nama pasien, DAN KLINIK (ruangan)
                        $q = mysqli_query($koneksi, "SELECT periksa.id, pasien.nama, periksa.klinik FROM periksa JOIN pasien ON periksa.nik_pasien = pasien.nik WHERE periksa.status != 'Menunggu' AND periksa.status != 'Menunggu Konfirmasi'");
                        
                        // Tampilkan nama beserta kliniknya
                        while($p = mysqli_fetch_assoc($q)) { 
                            echo "<option value='".$p['id']."'>".$p['nama']." - Ruang: ".$p['klinik']."</option>"; 
                        }
                        ?>
                    </select>
                    <label>File Gambar:</label>
                    <input type="file" name="gambar" required style="margin:10px 0; display:block;">
                    <button type="submit" name="upload" class="btn-upload">SIMPAN HASIL LAB</button>
                </form>
            </div>

            <div class="card">
                <h3><i class="fas fa-history"></i> Upload Terbaru</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>File</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_riwayat = mysqli_query($koneksi, "SELECT hasil_pemeriksaan.*, pasien.nama 
                                                            FROM hasil_pemeriksaan 
                                                            JOIN periksa ON hasil_pemeriksaan.id_periksa = periksa.id 
                                                            JOIN pasien ON periksa.nik_pasien = pasien.nik 
                                                            ORDER BY hasil_pemeriksaan.id DESC LIMIT 5");
                        while($h = mysqli_fetch_assoc($q_riwayat)) {
                            echo "<tr>
                                <td><strong>".$h['nama']."</strong></td>
                                <td><a href='../../img/lab/".$h['file_foto']."' target='_blank' style='color:#17a2b8;'>".$h['file_foto']."</a></td>
                                <td>".date('d/m H:i', strtotime($h['waktu_upload']))."</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>