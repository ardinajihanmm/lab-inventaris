<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

$id_user = (int)$_SESSION['user']['id_user'];

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $check = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_pinjam=$id AND id_user=$id_user AND status='dipinjam'");
    if (mysqli_num_rows($check) > 0) {
        $tgl_aktual = date('Y-m-d');
        mysqli_query($conn, "UPDATE peminjaman SET status='dikembalikan', tgl_kembali_aktual='$tgl_aktual' WHERE id_pinjam=$id");

        $row = mysqli_fetch_assoc($check);
        mysqli_query($conn, "UPDATE alat SET stok = stok + {$row['jumlah']} WHERE id_alat = {$row['id_alat']}");
    }
}

header("Location: riwayat.php?msg=returned");
exit;
?>
