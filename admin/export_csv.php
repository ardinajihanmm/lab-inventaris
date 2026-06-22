<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_inventaris.csv');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=data_inventaris_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'ID',
    'Nama Alat',
    'Kode Alat',
    'Kategori',
    'Kondisi',
    'Lokasi',
    'Deskripsi',
    'Status',
    'Stok'
], ';');

$query = mysqli_query($conn, "
    SELECT *
    FROM alat
    ORDER BY id_alat ASC
");

while ($row = mysqli_fetch_assoc($query)) {

fputcsv($output, [
    $row['id_alat'],
    $row['nama_alat'],
    $row['kode_alat'],
    $row['kategori'],
    $row['kondisi'],
    $row['lokasi'],
    $row['deskripsi'],
    $row['status_alat'],
    $row['stok']
], ';');
}

fclose($output);
exit;
?>