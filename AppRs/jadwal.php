<?php 
session_start();
require 'koneksi.php';

// Atur zona waktu ke Indonesia
date_default_timezone_set('Asia/Jakarta');

// Daftar hari dalam bahasa Indonesia
$nama_hari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");

// Ambil hari ini secara otomatis dari sistem komputer/server
$hari_ini = $nama_hari[date('w')];

$hari_aktif = isset($_GET['hari']) ? $_GET['hari'] : $hari_ini;

// Ambil jadwal dokter dari database KHUSUS untuk hari yang sedang aktif
$query_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal_dokter WHERE hari = '$hari_aktif' ORDER BY jam_mulai ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Dokter - RS Ludira Husada Tama</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Desain khusus untuk navigasi scroll hari (Senin-Minggu) */
        .day-nav { display: flex; overflow-x: auto; gap: 10px; margin-bottom: 20px; padding-bottom: 10px; }
        .day-nav::-webkit-scrollbar { display: none; } /* Sembunyikan scrollbar bawaan */
        .day-btn { 
            padding: 8px 18px; border-radius: 20px; background: white; color: #555; 
            text-decoration: none; font-size: 13px; border: 1px solid #ddd; white-space: nowrap; 
        }
        .day-btn.active { 
            background: #1e90ff; color: white; border-color: #1e90ff; font-weight: bold; 
            box-shadow: 0 4px 6px rgba(30,144,255,0.3);
        }
    </style>
</head>
<body>

<div class="mobile-app">
    <div class="top-header" style="justify-content: center; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
        Jadwal Dokter
    </div>

    <div class="content">
        <input type="text" class="search-box" placeholder="Cari Klinik atau Dokter...">
        
        <div class="day-nav">
            <?php 
            // Putar array nama hari untuk membuat tombol dari Senin - Minggu
            // Kita mulai dari index 1 (Senin) sampai 6 (Sabtu), lalu 0 (Minggu) di akhir
            $urutan_hari = [1, 2, 3, 4, 5, 6, 0];
            foreach($urutan_hari as $index) : 
                $h = $nama_hari[$index];
            ?>
                <a href="jadwal.php?hari=<?php echo $h; ?>" class="day-btn <?php echo ($hari_aktif == $h) ? 'active' : ''; ?>">
                    <?php echo $h; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="date-picker">
            <span>Jadwal Praktik</span> Hari <?php echo $hari_aktif; ?>
        </div>

        <?php 
        // Cek apakah ada jadwal di hari tersebut
        if (mysqli_num_rows($query_jadwal) > 0) {
            while($row = mysqli_fetch_assoc($query_jadwal)) : 
                $jam_mulai = date('H:i', strtotime($row['jam_mulai']));
                $jam_selesai = date('H:i', strtotime($row['jam_selesai']));
                $class_unavailable = ($row['status'] == 'Tidak Tersedia') ? 'unavailable' : '';
        ?>

                <div class="doctor-card <?php echo $class_unavailable; ?>">
                    <?php if($row['status'] == 'Tidak Tersedia') : ?>
                        <div class="badge-unavailable">TIDAK TERSEDIA</div>
                    <?php endif; ?>

                    <div class="klinik-name"><?php echo $row['nama_klinik']; ?></div>
                    <div class="doc-name"><?php echo $row['nama_dokter']; ?></div>
                    <div class="time-badge">(Pukul <?php echo $jam_mulai; ?> - <?php echo $jam_selesai; ?> WIB)</div>
                    
                    <?php if($row['status'] == 'Tersedia') : ?>
                        <a href="admisi.php?klinik=<?php echo urlencode($row['nama_klinik']); ?>" class="btn-plus">
                            <i class="fas fa-plus"></i> Daftar
                        </a>
                    <?php endif; ?>
                </div>

        <?php 
            endwhile; 
        } else {
            // Tampilan jika dokter kosong di hari tersebut
            echo '<div style="text-align:center; margin-top:30px; color:#888;">
                    <i class="fas fa-calendar-times" style="font-size:40px; color:#ccc; margin-bottom:10px;"></i>
                    <p>Tidak ada jadwal dokter untuk hari '.$hari_aktif.'</p>
                  </div>';
        }
        ?>

    </div>

    <div class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="fas fa-home"></i> Beranda
        </a>
        <a href="jadwal.php" class="nav-item active">
            <i class="far fa-calendar-alt"></i> Jadwal
        </a>
        <a href="profil.php" class="nav-center">
            <i class="fas fa-user-plus"></i> Pasien
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-bed"></i> Bed
        </a>
        <a href="infokes.php" class="nav-item">
            <i class="far fa-file-alt"></i> Infokes
        </a>
    </div>
</div>

</body>
</html>