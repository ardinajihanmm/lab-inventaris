<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
if ($_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$pesan = '';
$tipe  = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'approved') { $pesan = "Peminjaman berhasil disetujui."; $tipe = "success"; }
if (isset($_GET['msg']) && $_GET['msg'] === 'rejected') { $pesan = "Peminjaman berhasil ditolak."; $tipe = "success"; }

$pageTitle  = 'Data Peminjaman';
$activePage = 'peminjaman';
include 'layout_top.php';
?>

<div class="page-banner">
    <h1><i class="bi bi-clipboard-list"></i> Data Peminjaman</h1>
    <p>Pantau dan kelola seluruh pengajuan peminjaman alat dari pengguna.</p>
</div>

<?php if ($pesan): ?>
<div class="alert alert-<?= $tipe ?> rounded-3 border-0 d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="table-card">
    <div class="table-card-header">
        <i class="bi bi-list-ul text-muted"></i>
        <h5>Riwayat Peminjaman</h5>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Alat</th>
                    <th>Jumlah</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $data = mysqli_query($conn, "
                    SELECT p.*, u.username, a.nama_alat
                    FROM peminjaman p
                    JOIN user u ON p.id_user = u.id_user
                    JOIN alat a ON p.id_alat = a.id_alat
                    ORDER BY p.id_pinjam DESC
                ");
                if (mysqli_num_rows($data) === 0):
                ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data peminjaman.</td></tr>
                <?php
                else:
                    while ($d = mysqli_fetch_assoc($data)):
                ?>
                <tr>
                    <td>#<?= $d['id_pinjam'] ?></td>
                    <td><?= htmlspecialchars($d['username']) ?></td>
                    <td><?= htmlspecialchars($d['nama_alat']) ?></td>
                    <td><?= $d['jumlah'] ?></td>
                    <td><?= date('d M Y', strtotime($d['tgl_pinjam'])) ?></td>
                    <td><?= date('d M Y', strtotime($d['tgl_kembali'])) ?></td>
                    <td><span class="status-badge badge-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
                    <td class="text-end">
                        <?php if ($d['status'] == 'menunggu'): ?>
                            <a href="approve.php?id=<?= $d['id_pinjam'] ?>" class="btn btn-sm btn-outline-success me-1" title="Setujui">
                                <i class="bi bi-check-lg"></i>
                            </a>
                            <a href="tolak.php?id=<?= $d['id_pinjam'] ?>" class="btn btn-sm btn-outline-danger" title="Tolak"
                               onclick="return confirm('Tolak peminjaman ini?')">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                    endwhile;
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
