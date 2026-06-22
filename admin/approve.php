<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE peminjaman SET status='dipinjam' WHERE id_pinjam=$id AND status='menunggu'");
}

header("Location: peminjaman.php?msg=approved");
exit;
?>
