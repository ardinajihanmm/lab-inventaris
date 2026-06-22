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

$cari     = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$status   = isset($_GET['status']) ? trim($_GET['status']) : '';

$kategoriValid = ['Komputer', 'Elektronika', 'Jaringan', 'Lainnya'];
$statusValid   = ['tersedia', 'dipinjam', 'menunggu', 'dikembalikan', 'rusak'];

$where  = [];
$params = [];
$types  = '';

if ($cari !== '') {
    $where[]  = "nama_alat LIKE ?";
    $params[] = '%' . $cari . '%';
    $types   .= 's';
}
if ($kategori !== '' && in_array($kategori, $kategoriValid, true)) {
    $where[]  = "kategori = ?";
    $params[] = $kategori;
    $types   .= 's';
}
if ($status !== '' && in_array($status, $statusValid, true)) {
    if ($status === 'rusak') {
        $where[] = "status_alat = 'rusak'";
    } elseif ($status === 'tersedia') {
        $where[] = "status_alat = 'tersedia' AND stok > 0";
    } elseif ($status === 'dipinjam') {
        $where[] = "id_alat IN (SELECT id_alat FROM peminjaman WHERE status='dipinjam')";
    } elseif ($status === 'menunggu') {
        $where[] = "id_alat IN (SELECT id_alat FROM peminjaman WHERE status='menunggu')";
    } elseif ($status === 'dikembalikan') {
        $where[] = "id_alat IN (SELECT id_alat FROM peminjaman WHERE status='dikembalikan')";
    }
}

$sql = "SELECT * FROM alat";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY nama_alat";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);

$pageTitle  = 'Data Alat';
$activePage = 'alat';
include 'layout_top.php';
?>

<div class="page-banner">
    <h1><i class="bi bi-box-seam me-2"></i>Data Alat Laboratorium</h1>
    <p>Pilih alat laboratorium yang ingin Anda pinjam di bawah ini.</p>
</div>

<div class="filter-card mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label"><i class="bi bi-search me-1"></i>Cari Nama Alat</label>
            <input type="text" name="cari" class="form-control" placeholder="Ketik nama alat..."
                   value="<?= htmlspecialchars($cari) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label"><i class="bi bi-tags me-1"></i>Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoriValid as $k): ?>
                <option value="<?= $k ?>" <?= $kategori === $k ? 'selected' : '' ?>><?= $k ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label"><i class="bi bi-funnel me-1"></i>Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="tersedia"     <?= $status === 'tersedia'     ? 'selected' : '' ?>>Tersedia</option>
                <option value="dipinjam"     <?= $status === 'dipinjam'     ? 'selected' : '' ?>>Dipinjam</option>
                <option value="menunggu"     <?= $status === 'menunggu'     ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                <option value="dikembalikan" <?= $status === 'dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
                <option value="rusak"        <?= $status === 'rusak'        ? 'selected' : '' ?>>Rusak</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-grad flex-fill">
                <i class="bi bi-search me-1"></i>Cari
            </button>
            <a href="alat.php" class="btn btn-outline-secondary flex-fill text-center">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<div class="row g-3">
    <?php if (mysqli_num_rows($data) === 0): ?>
        <div class="col-12">
            <div class="table-card text-center text-muted py-5">
                <i class="bi bi-search fs-2 d-block mb-2 text-muted"></i>
                Tidak ada alat yang cocok dengan pencarian/filter Anda.
            </div>
        </div>
    <?php else: while ($d = mysqli_fetch_assoc($data)): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="alat-card">
                <div class="alat-thumb">
                    <?php if (!empty($d['foto']) && file_exists('../uploads/alat/' . $d['foto'])): ?>
                        <img src="../uploads/alat/<?= htmlspecialchars($d['foto']) ?>" alt="<?= htmlspecialchars($d['nama_alat']) ?>">
                    <?php else: ?>
                        <div class="alat-thumb-placeholder"><i class="bi bi-tools"></i></div>
                    <?php endif; ?>
                    <?php if ($d['status_alat'] === 'rusak'): ?>
                        <span class="status-badge badge-rusak alat-badge">Rusak</span>
                    <?php elseif ($d['stok'] > 0): ?>
                        <span class="status-badge badge-dikembalikan alat-badge">Tersedia</span>
                    <?php else: ?>
                        <span class="status-badge badge-terlambat alat-badge">Habis</span>
                    <?php endif; ?>
                </div>
                <div class="alat-body">
                    <h6><?= htmlspecialchars($d['nama_alat']) ?></h6>
                    <span class="badge-kategori mb-1"><?= htmlspecialchars($d['kategori']) ?></span>
                    <span class="text-muted d-block mt-1" style="font-size:12px;"><i class="bi bi-layers me-1"></i>Stok: <?= $d['stok'] ?> unit</span>
                </div>
                <div class="alat-footer d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick='bukaDetailAlat(<?= json_encode($d, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php if ($d['status_alat'] !== 'rusak' && $d['stok'] > 0): ?>
                        <a href="pinjam.php?id=<?= $d['id_alat'] ?>" class="btn btn-sm btn-primary-grad flex-fill">
                            <i class="bi bi-bag-plus me-1"></i>Pinjam
                        </a>
                    <?php else: ?>
                        <button class="btn btn-sm btn-outline-secondary flex-fill" disabled>
                            <?= $d['status_alat'] === 'rusak' ? 'Rusak' : 'Stok Habis' ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; endif; ?>
</div>

<?php include '../detail_alat_modal.php'; ?>

<style>
.alat-card {
    background: #fff; border-radius: 14px; overflow: hidden;
    box-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.05); height: 100%;
    display: flex; flex-direction: column; transition: transform .15s, box-shadow .15s;
}
.alat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(15,23,42,.1); }
.alat-thumb { position: relative; aspect-ratio: 4/3; max-height: 220px; background: #f1f5f9; }
.alat-thumb img { width: 100%; height: 100%; object-fit: cover; }
.alat-thumb-placeholder {
    width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
    color: #94a3b8; font-size: 30px;
    background: linear-gradient(135deg, #e0f2fe, #dbeafe);
}
.alat-badge { position: absolute; top: 10px; right: 10px; }
.alat-body { padding: 14px 14px 6px; flex: 1; }
.alat-body h6 { font-size: 13.5px; font-weight: 700; color: #111827; margin: 0 0 6px; }
.alat-footer { padding: 12px 14px 14px; }
</style>

<?php include 'layout_bottom.php'; ?>
