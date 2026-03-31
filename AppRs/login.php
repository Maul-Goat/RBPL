<?php
session_start();
require 'koneksi.php';

// Jika sudah login, langsung lempar ke index
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $password = $_POST['password'];

    $cek_user = mysqli_query($koneksi, "SELECT * FROM pasien WHERE nik = '$nik'");
    
    if (mysqli_num_rows($cek_user) === 1) {
        $row = mysqli_fetch_assoc($cek_user);
        
        // Cek password biasa (sesuai kesepakatan sebelumnya)
        if ($password == $row['password']) {
            $_SESSION['login'] = true;
            $_SESSION['nik'] = $row['nik'];
            $_SESSION['nama'] = $row['nama'];
            
            header("Location: index.php");
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RS Ludira Husada Tama</title>
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
            color: white; padding: 40px 20px; text-align: center;
            border-bottom-left-radius: 30px; border-bottom-right-radius: 30px;
            margin-bottom: 30px;
        }
        .header-login h1 { margin: 0; font-size: 24px; }
        .header-login p { margin: 5px 0 0 0; font-size: 14px; opacity: 0.9; }
        .header-login i { font-size: 50px; margin-bottom: 10px; }

        .content { padding: 0 25px; flex-grow: 1; }
        
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group i { position: absolute; top: 14px; left: 15px; color: #1e90ff; }
        .form-group input {
            width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #ddd;
            border-radius: 8px; font-size: 14px; outline: none; transition: 0.3s;
        }
        .form-group input:focus { border-color: #1e90ff; box-shadow: 0 0 5px rgba(30, 144, 255, 0.3); }
        
        .btn-primary {
            width: 100%; padding: 12px; background: #1e90ff; color: white; border: none;
            border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;
            margin-top: 10px; box-shadow: 0 4px 6px rgba(30,144,255,0.3);
        }
        .btn-primary:active { background: #0073e6; }
        
        .alert { background: #ffe6e6; color: #d9534f; padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 15px; text-align: center; }
        
        .footer-login { text-align: center; margin-top: 30px; font-size: 14px; color: #666; }
        .footer-login a { color: #1e90ff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="mobile-app">
    <div class="header-login">
        <i class="fas fa-hospital-user"></i>
        <h1>RS Ludira Husada</h1>
        <p>Portal Pasien Online</p>
    </div>

    <div class="content">
        <h2 style="font-size: 20px; margin-bottom: 20px; color: #333;">Masuk Akun</h2>
        
        <?php if(isset($error)) : ?>
            <div class="alert"><i class="fas fa-exclamation-circle"></i> NIK atau Password salah!</div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <i class="fas fa-id-card"></i>
                <input type="number" name="nik" placeholder="Masukkan NIK Anda" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" name="login" class="btn-primary">Masuk</button>
        </form>

        <div class="footer-login">
            Belum punya akun? <a href="daftar.php">Daftar Sekarang</a>
        </div>
    </div>
</div>

</body>
</html>