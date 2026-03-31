<?php
session_start();

// HANYA hapus memori milik karyawan, milik pasien dibiarkan aman
unset($_SESSION['karyawan_login']);
unset($_SESSION['karyawan_nama']);
unset($_SESSION['karyawan_role']);

header("Location: login.php");
exit;
?>