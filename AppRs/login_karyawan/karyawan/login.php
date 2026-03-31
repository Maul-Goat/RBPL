<?php
session_start();

require '../koneksi.php'; 

if (isset($_SESSION['karyawan_role'])) {
    $redirects = [
        'administrasi' => 'admin_antrean.php',
        'kasir'        => 'kasir.php',
        'perawat'      => 'lab.php',
        'sdm'          => 'sdm.php',
        'manajer'      => 'laporan.php'
    ];
    header("Location: " . $redirects[$_SESSION['karyawan_role']]);
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        // Pembuatan Session Karyawan
        $_SESSION['karyawan_login'] = true;
        $_SESSION['karyawan_nama'] = $user['nama_lengkap'];
        $_SESSION['karyawan_role'] = $user['role'];

        $redirects = [
            'administrasi' => 'admin_antrean.php',
            'kasir'        => 'kasir.php',
            'perawat'      => 'perawat_klinis.php', // Kita ubah arah perawat ke halaman klinis
            'sdm'          => 'sdm.php',
            'manajer'      => 'laporan.php',
            'dokter'       => 'dokter.php' // Tambahan untuk dokter
        ];

        header("Location: " . $redirects[$user['role']]);
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login SIMRS</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #1e90ff; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 style="color: #1e90ff;">SIMRS Login</h2>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Masuk</button>
        </form>
    </div>
</body>
</html>