<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}


$klinik_pilihan = isset($_GET['klinik']) ? $_GET['klinik'] : '';

// ambil daftar klinik dari tabel jadwal_dokter sehingga opsi selalu sinkron dengan database
$klinik_list = [];
$klinik_query = mysqli_query($koneksi, "SELECT DISTINCT nama_klinik FROM jadwal_dokter ORDER BY nama_klinik ASC");
if ($klinik_query) {
    while ($row = mysqli_fetch_assoc($klinik_query)) {
        $klinik_list[] = $row['nama_klinik'];
    }
}

if (isset($_POST['submit'])) {
    $nik = $_SESSION['nik'];
    $no_wa = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $jaminan = mysqli_real_escape_string($koneksi, $_POST['jaminan']);
    $klinik = mysqli_real_escape_string($koneksi, $_POST['klinik']);
    $tanggal = $_POST['tanggal'];

    // LOGIKA ANTREAN OTOMATIS: Cari antrean terbesar di klinik & tanggal yang sama
    $cek_antrean = mysqli_query($koneksi, "SELECT MAX(no_antrean) AS max_antrean FROM periksa WHERE klinik = '$klinik' AND tanggal = '$tanggal'");
    $data_antrean = mysqli_fetch_assoc($cek_antrean);
    
    // Jika sudah ada antrean, tambah 1. Jika belum, mulai dari 1.
    $no_antrean = ($data_antrean['max_antrean'] != null) ? $data_antrean['max_antrean'] + 1 : 1;

    // Simpan ke database beserta no_antrean
    $query = "INSERT INTO periksa (nik_pasien, no_wa, jaminan, klinik, tanggal, no_antrean) VALUES ('$nik', '$no_wa', '$jaminan', '$klinik', '$tanggal', '$no_antrean')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Pendaftaran Berhasil! Antrean Anda nomor $no_antrean.'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal mendaftar!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admisi Pasien - RS Ludira Husada Tama</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="mobile-app">
    <div class="top-header">
        <a href="jadwal.php"><i class="fas fa-arrow-left"></i></a> Admisi Pasien
    </div>

    <div class="content" style="background-color: white; margin-top: 10px; border-radius: 15px; padding: 25px 20px;">
        <form action="" method="post">
            
            <div class="form-group">
                <label style="color: #666; font-weight: normal;">No. WhatsApp Aktif <span style="color: red;">*</span> <i class="fas fa-info-circle" style="color: #1e90ff; font-size: 14px;"></i></label>
                <input type="number" name="no_wa" placeholder="62xxxxxxxxxxx" required style="border-color: #87cefa;">
            </div>

            <div class="form-group">
                <label style="color: #666; font-weight: normal;">Jaminan <span style="color: red;">*</span></label>
                <select name="jaminan" required style="border-color: #87cefa; background-color: white;">
                    <option value="Umum">Umum</option>
                    <option value="BPJS">BPJS Kesehatan</option>
                    <option value="Asuransi Lain">Asuransi Lain</option>
                </select>
            </div>

            <div class="form-group">
                <label style="color: #666; font-weight: normal;">Tujuan <span style="color: red;">*</span></label>
                <select name="tujuan" required style="border-color: #87cefa; background-color: white; color: #888;">
                    <option value="" disabled selected>Pilih Tujuan</option>
                    <option value="Poliklinik">Poliklinik</option>
                    <option value="IGD">IGD</option>
                </select>
            </div>

            <div class="form-group">
                <label style="color: #666; font-weight: normal;">Tanggal Kunjungan <span style="color: red;">*</span></label>
                <input type="date" name="tanggal" required style="border-color: #87cefa; color: #333;">
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label style="color: #666; font-weight: normal;">Klinik <span style="color: red;">*</span></label>
                <?php if($klinik_pilihan != ''): ?>
                    <input type="text" name="klinik" value="<?php echo htmlspecialchars($klinik_pilihan); ?>" readonly style="border: none; text-align: center; font-weight: bold; color: #333; font-size: 16px;">
                <?php else: ?>
                    <select name="klinik" required style="border: none; text-align: center; font-weight: bold; color: #333; font-size: 16px; width: 100%; appearance: none; -webkit-appearance: none;">
                        <?php if(count($klinik_list) === 0): ?>
                            <option value="" disabled selected>Klinik belum tersedia (Klik di sini)</option>
                        <?php else: ?>
                            <option value="" disabled selected>Pilih Klinik</option>
                            <?php foreach($klinik_list as $k): ?>
                                <option value="<?php echo htmlspecialchars($k); ?>"><?php echo htmlspecialchars($k); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                <?php endif; ?>
            </div>

            <button type="submit" name="submit" class="btn-primary" style="border-radius: 30px; padding: 12px; font-size: 16px; background-color: #00bfff; margin-bottom: 10px;">
                Submit
            </button>
            
            <p style="text-align: center; font-size: 11px; color: #888; font-style: italic; margin-top: 15px;">
                Bidang isian yang bertanda (<span style="color:red;">*</span>) wajib diisi
            </p>
        </form>
    </div>
</div>

</body>
</html>