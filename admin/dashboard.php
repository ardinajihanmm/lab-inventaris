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

mysqli_query($conn, "
UPDATE peminjaman
SET status='terlambat'
WHERE status='dipinjam'
AND tgl_kembali < CURDATE()
");

$total_alat = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alat"));
$dipinjam   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='dipinjam'"));
$tersedia   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alat WHERE stok > 0"));
$terlambat  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='terlambat'"));
$total_user = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM user"));
$menunggu   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='menunggu'"));

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
include 'layout_top.php';
?>

<div class="page-banner">
    <h1><i class="bi bi-hand-thumbs-up-fill me-2"></i>Selamat datang, <?= htmlspecialchars($_SESSION['user']['username']) ?>!</h1>
    <p>Berikut ringkasan inventaris dan peminjaman alat Laboratorium Komputer & Informatika hari ini.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card indigo">
            <div class="card-icon"><i class="bi bi-box-seam"></i></div>
            <div class="card-num"><?= $total_alat ?></div>
            <div class="card-label">Total Alat</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card emerald">
            <div class="card-icon"><i class="bi bi-check-circle"></i></div>
            <div class="card-num"><?= $tersedia ?></div>
            <div class="card-label">Tersedia</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card blue">
            <div class="card-icon"><i class="bi bi-arrow-left-right"></i></div>
            <div class="card-num"><?= $dipinjam ?></div>
            <div class="card-label">Dipinjam</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card red">
            <div class="card-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="card-num"><?= $terlambat ?></div>
            <div class="card-label">Terlambat</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card amber">
            <div class="card-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="card-num"><?= $menunggu ?></div>
            <div class="card-label">Menunggu</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card cyan">
            <div class="card-icon"><i class="bi bi-people"></i></div>
            <div class="card-num"><?= $total_user ?></div>
            <div class="card-label">Total User</div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <i class="bi bi-clock-history text-muted"></i>
        <h5>Peminjaman Terbaru</h5>
        <a href="peminjaman.php" class="btn btn-sm btn-primary-grad">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Alat</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $data = mysqli_query($conn, "
                    SELECT p.*, u.username, a.nama_alat
                    FROM peminjaman p
                    JOIN user u ON p.id_user = u.id_user
                    JOIN alat a ON p.id_alat = a.id_alat
                    ORDER BY p.id_pinjam DESC LIMIT 10
                ");
                if (mysqli_num_rows($data) === 0):
                ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data peminjaman.</td></tr>
                <?php
                else:
                    while ($d = mysqli_fetch_assoc($data)):
                ?>
                <tr>
                    <td>#<?= $d['id_pinjam'] ?></td>
                    <td><?= htmlspecialchars($d['username']) ?></td>
                    <td><?= htmlspecialchars($d['nama_alat']) ?></td>
                    <td><?= date('d M Y', strtotime($d['tgl_pinjam'])) ?></td>
                    <td><?= date('d M Y', strtotime($d['tgl_kembali'])) ?></td>
                    <td><span class="status-badge badge-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
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
