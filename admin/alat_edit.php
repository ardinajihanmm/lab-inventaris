<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
if ($_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$uploadDir = '../uploads/alat/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$kategoriValid = ['Komputer', 'Elektronika', 'Jaringan', 'Lainnya'];
$statusValid   = ['tersedia', 'rusak'];

if (isset($_POST['edit'])) {
    $eid       = (int)$_POST['id_alat'];
    $nama      = mysqli_real_escape_string($conn, trim($_POST['nama_alat']));
    $stok      = (int)$_POST['stok'];
    $kodeAlat  = mysqli_real_escape_string($conn, trim($_POST['kode_alat']));
    $kategori  = in_array($_POST['kategori'], $kategoriValid, true) ? $_POST['kategori'] : 'Lainnya';
    $kondisi   = mysqli_real_escape_string($conn, trim($_POST['kondisi']));
    $lokasi    = mysqli_real_escape_string($conn, trim($_POST['lokasi']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $statusAlat = in_array($_POST['status_alat'], $statusValid, true) ? $_POST['status_alat'] : 'tersedia';

    $kodeSql = $kodeAlat !== '' ? "'$kodeAlat'" : "NULL";

    $fotoSql = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['foto']['size'] <= 2 * 1024 * 1024) {
            $namaFile = 'alat_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $namaFile)) {
                // Hapus foto lama
                $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM alat WHERE id_alat=$eid"));
                if ($old && !empty($old['foto']) && file_exists($uploadDir . $old['foto'])) {
                    @unlink($uploadDir . $old['foto']);
                }
                $fotoSql = ", foto='" . mysqli_real_escape_string($conn, $namaFile) . "'";
            }
        }
    }

    mysqli_query($conn, "UPDATE alat SET
        nama_alat='$nama',
        kode_alat=$kodeSql,
        kategori='$kategori',
        kondisi='$kondisi',
        lokasi='$lokasi',
        deskripsi='$deskripsi',
        status_alat='$statusAlat',
        stok=$stok
        $fotoSql
        WHERE id_alat=$eid");
    header("Location: alat.php?msg=updated");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: alat.php");
    exit;
}
$id = (int)$_GET['id'];

$alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM alat WHERE id_alat=$id"));
if (!$alat) {
    header("Location: alat.php");
    exit;
}

$pageTitle  = 'Edit Alat';
$activePage = 'alat';
include 'layout_top.php';
?>

<div class="page-banner">
    <h1><i class="bi bi-pencil-square"></i> Edit Data Alat</h1>
    <p>Perbarui informasi lengkap alat laboratorium.</p>
</div>

<div class="form-card" style="max-width:640px;">
    <h5 class="mb-3"><i class="bi bi-box-seam text-primary me-2"></i><?= htmlspecialchars($alat['nama_alat']) ?></h5>

    <div class="mb-3 text-center">
        <?php if (!empty($alat['foto']) && file_exists($uploadDir . $alat['foto'])): ?>
            <img src="../uploads/alat/<?= htmlspecialchars($alat['foto']) ?>" alt="Foto Alat"
                 style="width:120px;height:120px;object-fit:cover;border-radius:12px;">
        <?php else: ?>
            <div style="width:120px;height:120px;border-radius:12px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;margin:0 auto;">
                <i class="bi bi-image fs-2"></i>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_alat" value="<?= $alat['id_alat'] ?>">

        <div class="row g-3">
            <div class="col-md-7">
                <label class="form-label">Nama Alat</label>
                <input type="text" name="nama_alat" class="form-control" value="<?= htmlspecialchars($alat['nama_alat']) ?>" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Kode Alat</label>
                <input type="text" name="kode_alat" class="form-control" placeholder="Contoh: ALT-001"
                       value="<?= htmlspecialchars($alat['kode_alat'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                    <?php foreach ($kategoriValid as $k): ?>
                    <option value="<?= $k ?>" <?= $alat['kategori'] === $k ? 'selected' : '' ?>><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status Alat</label>
                <select name="status_alat" class="form-select">
                    <option value="tersedia" <?= $alat['status_alat'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                    <option value="rusak"    <?= $alat['status_alat'] === 'rusak'    ? 'selected' : '' ?>>Rusak</option>
                </select>
                <div class="form-text">Status "Tersedia" otomatis tampil "Habis" jika stok 0.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Kondisi</label>
                <input type="text" name="kondisi" class="form-control" placeholder="Contoh: Baik, Perlu Servis"
                       value="<?= htmlspecialchars($alat['kondisi'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Lokasi Penyimpanan</label>
                <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Lemari A - Rak 2"
                       value="<?= htmlspecialchars($alat['lokasi'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Jumlah Stok</label>
                <input type="number" name="stok" class="form-control" min="0" value="<?= $alat['stok'] ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ganti Foto Alat</label>
                <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
            </div>

            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat alat..."><?= htmlspecialchars($alat['deskripsi'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" name="edit" class="btn btn-primary-grad flex-fill">
                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
            </button>
            <a href="alat.php" class="btn btn-outline-secondary flex-fill text-center">
                <i class="bi bi-x-lg me-1"></i> Batal
            </a>
        </div>
    </form>
</div>

<?php include 'layout_bottom.php'; ?>
