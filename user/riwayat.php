<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
if ($_SESSION['user']['role'] != 'user') {
    header("Location: ../admin/dashboard.php");
    exit;
}

$id_user = (int)$_SESSION['user']['id_user'];

$pesan = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'submitted') { $pesan = "Pengajuan peminjaman berhasil dikirim! Menunggu persetujuan admin."; }
if (isset($_GET['msg']) && $_GET['msg'] === 'returned')  { $pesan = "Alat berhasil dikembalikan. Terima kasih!"; }

$mode = 'user';
include '../notifikasi_h1.php';

$data = mysqli_query($conn, "
    SELECT p.*, a.nama_alat
    FROM peminjaman p
    JOIN alat a ON p.id_alat = a.id_alat
    WHERE p.id_user = $id_user
    ORDER BY p.id_pinjam DESC
");

$pageTitle  = 'Riwayat Peminjaman';
$activePage = 'riwayat';
include 'layout_top.php';
?>

<div class="page-banner">
    <h1><i class="bi bi-clock-history"></i> Riwayat Peminjaman Saya</h1>
    <p>Lihat status seluruh pengajuan peminjaman alat yang pernah Anda ajukan.</p>
</div>

<?php include '../notifikasi_h1_user.php'; ?>

<?php if ($pesan): ?>
<div class="alert alert-success rounded-3 border-0 d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="table-card">
    <div class="table-card-header">
        <i class="bi bi-list-ul text-muted"></i>
        <h5>Daftar Peminjaman</h5>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Alat</th>
                    <th>Jumlah</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($data) === 0): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Anda belum memiliki riwayat peminjaman.</td></tr>
                <?php else: while ($d = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($d['nama_alat']) ?></strong></td>
                    <td><?= $d['jumlah'] ?></td>
                    <td><?= date('d M Y', strtotime($d['tgl_pinjam'])) ?></td>
                    <td><?= date('d M Y', strtotime($d['tgl_kembali'])) ?></td>
                    <td><span class="status-badge badge-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
                    <td class="text-end">
                        <?php if ($d['status'] == 'dipinjam'): ?>
                            <a href="kembalikan.php?id=<?= $d['id_pinjam'] ?>" class="btn btn-sm btn-outline-success"
                               onclick="return confirm('Konfirmasi pengembalian alat ini?')">
                                <i class="bi bi-arrow-return-left me-1"></i>Kembalikan
                            </a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
