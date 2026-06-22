<?php

if (!isset($mode)) $mode = 'user';

$besok = date('Y-m-d', strtotime('+1 day'));

if ($mode === 'user' && isset($id_user)) {
    $q = mysqli_prepare($conn,
        "SELECT p.id_pinjam, a.nama_alat, p.jumlah, p.tgl_kembali
         FROM peminjaman p
         JOIN alat a ON p.id_alat = a.id_alat
         WHERE p.id_user = ?
           AND p.status = 'dipinjam'
           AND p.tgl_kembali = ?
         ORDER BY p.id_pinjam ASC"
    );
    mysqli_stmt_bind_param($q, 'is', $id_user, $besok);
    mysqli_stmt_execute($q);
    $notif_h1 = mysqli_stmt_get_result($q);
    $notif_count = mysqli_num_rows($notif_h1);
}
?>