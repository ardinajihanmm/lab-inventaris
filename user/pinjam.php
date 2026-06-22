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

if (!isset($_GET['id'])) {
    header("Location: alat.php");
    exit;
}

$id_user = (int)$_SESSION['user']['id_user'];
$id_alat = (int)$_GET['id'];

$alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM alat WHERE id_alat=$id_alat"));
if (!$alat) {
    header("Location: alat.php");
    exit;
}

$pesan = '';

if (isset($_POST['pinjam'])) {
    $jumlah = (int)$_POST['jumlah'];

    if ($jumlah <= 0) {
        $pesan = "Jumlah tidak valid!";
    } elseif ($alat['stok'] < $jumlah) {
        $pesan = "Stok tidak cukup! Tersedia: " . $alat['stok'];
    } else {
        $tgl_pinjam  = date('Y-m-d');
        $tgl_kembali = date('Y-m-d', strtotime('+7 days'));

        mysqli_query($conn, "INSERT INTO peminjaman (id_user,id_alat,jumlah,status,tgl_pinjam,tgl_kembali)
            VALUES ($id_user,$id_alat,$jumlah,'menunggu','$tgl_pinjam','$tgl_kembali')");

        header("Location: riwayat.php?msg=submitted");
        exit;
    }
}

$pageTitle  = 'Form Peminjaman';
$activePage = 'alat';
include 'layout_top.php';
?>

<div class="page-banner">
    <h1><i class="bi bi-bag-plus"></i> Form Peminjaman Alat</h1>
    <p>Lengkapi jumlah alat yang ingin dipinjam. Durasi peminjaman <strong>7 hari</strong> sejak tanggal pinjam.</p>
</div>

<?php if ($pesan): ?>
<div class="alert alert-danger rounded-3 border-0 d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="form-card" style="max-width:480px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <?php if (!empty($alat['foto']) && file_exists('../uploads/alat/' . $alat['foto'])): ?>
            <img src="../uploads/alat/<?= htmlspecialchars($alat['foto']) ?>" alt="<?= htmlspecialchars($alat['nama_alat']) ?>"
                 style="width:56px;height:56px;object-fit:cover;border-radius:14px;">
        <?php else: ?>
            <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#0ea5e9,#2563eb);display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff;">
                <i class="bi bi-tools"></i>
            </div>
        <?php endif; ?>
        <div>
            <h5 class="mb-0"><?= htmlspecialchars($alat['nama_alat']) ?></h5>
            <span class="text-muted" style="font-size:13px;">Stok tersedia: <?= $alat['stok'] ?> unit</span>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label d-block text-muted" style="font-size:12px;">Tanggal Pinjam</label>
            <strong><?= date('d M Y') ?></strong>
        </div>
        <div class="col-6">
            <label class="form-label d-block text-muted" style="font-size:12px;">Tanggal Kembali</label>
            <strong><?= date('d M Y', strtotime('+7 days')) ?></strong>
        </div>
    </div>

    <form method="POST">
        <div class="mb-4">
            <label class="form-label">Jumlah yang Dipinjam</label>
            <input type="number" name="jumlah" class="form-control" min="1" max="<?= $alat['stok'] ?>" value="1" required>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" name="pinjam" class="btn btn-primary-grad flex-fill">
                <i class="bi bi-check-lg me-1"></i> Ajukan Peminjaman
            </button>
            <a href="alat.php" class="btn btn-outline-secondary flex-fill text-center">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </form>
</div>

<?php include 'layout_bottom.php'; ?>
