<?php
session_start();
require 'koneksi.php';

// Jika sudah login, lempar ke index
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['daftar'])) {
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = $_POST['password']; // Ingat, ini masih format plain text

    // Cek apakah NIK sudah pernah didaftarkan
    $cek_nik = mysqli_query($koneksi, "SELECT * FROM pasien WHERE nik = '$nik'");
    
    if (mysqli_num_rows($cek_nik) > 0) {
        $error_nik = true;
    } else {
        // Masukkan data ke database
        $query = "INSERT INTO pasien (nik, nama, password) VALUES ('$nik', '$nama', '$password')";
        if (mysqli_query($koneksi, $query)) {
            $success = true;
        } else {
            $error_db = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - RS Ludira Husada Tama</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #e9ecef; display: flex; justify-content: center; margin: 0; }
        .mobile-app { 
            width: 100%; max-width: 400px; background-color: white; 
            min-height: 100vh; position: relative; display: flex; flex-direction: column;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        
        .header-login {
            background: linear-gradient(135deg, #1e90ff, #00bfff);
            color: white; padding: 30px 20px; text-align: center;
            border-bottom-left-radius: 30px; border-bottom-right-radius: 30px;
            margin-bottom: 25px;
        }
        .header-login h1 { margin: 0; font-size: 20px; }
        
        .content { padding: 0 25px; flex-grow: 1; }
        
        .form-group { margin-bottom: 15px; position: relative; }
        .form-group i { position: absolute; top: 14px; left: 15px; color: #1e90ff; }
        .form-group input {
            width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #ddd;
            border-radius: 8px; font-size: 14px; outline: none; transition: 0.3s;
        }
        .form-group input:focus { border-color: #1e90ff; box-shadow: 0 0 5px rgba(30, 144, 255, 0.3); }
        
        .btn-primary {
            width: 100%; padding: 12px; background: #28a745; color: white; border: none;
            border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;
            margin-top: 10px; box-shadow: 0 4px 6px rgba(40,167,69,0.3);
        }
        .btn-primary:active { background: #218838; }
        
        .alert { padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 15px; text-align: center; }
        .alert-error { background: #ffe6e6; color: #d9534f; }
        .alert-success { background: #d4edda; color: #155724; }
        
        .footer-login { text-align: center; margin-top: 20px; font-size: 14px; color: #666; padding-bottom: 20px;}
        .footer-login a { color: #1e90ff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="mobile-app">
    <div class="header-login">
        <i class="fas fa-user-plus" style="font-size: 40px; margin-bottom: 10px;"></i>
        <h1>Pendaftaran Pasien</h1>
    </div>

    <div class="content">
        <h2 style="font-size: 18px; margin-bottom: 15px; color: #333;">Lengkapi Data Diri</h2>
        
        <?php if(isset($error_nik)) : ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> NIK sudah terdaftar! Silakan login.</div>
        <?php endif; ?>

        <?php if(isset($success)) : ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Pendaftaran berhasil!<br>
                <a href="login.php" style="color:#155724; font-weight:bold; text-decoration:underline;">Klik di sini untuk Login</a>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <i class="fas fa-id-card"></i>
                <input type="number" name="nik" placeholder="Nomor Induk Kependudukan (NIK)" required>
            </div>
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="nama" placeholder="Nama Lengkap" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Buat Password" required>
            </div>
            <button type="submit" name="daftar" class="btn-primary">Daftar Sekarang</button>
        </form>

        <div class="footer-login">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
    </div>
</div>

</body>
</html>