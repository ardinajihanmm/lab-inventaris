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

$pesan = '';
$tipe  = '';

$uploadDir = '../uploads/alat/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function uploadFotoAlat($file, $uploadDir) {

    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [true, null]; 
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, "Terjadi kesalahan saat upload foto."];
    }
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return [false, "Format foto harus JPG, JPEG, PNG, atau WEBP."];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [false, "Ukuran foto maksimal 2MB."];
    }
    $namaFile = 'alat_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
        return [false, "Gagal menyimpan foto ke server."];
    }
    return [true, $namaFile];
}

if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_alat']));
    $stok = (int)$_POST['stok'];
    if ($nama != '' && $stok >= 0) {
        list($okFoto, $hasilFoto) = uploadFotoAlat($_FILES['foto'] ?? null, $uploadDir);
        if (!$okFoto) {
            $pesan = $hasilFoto;
            $tipe  = "danger";
        } else {
            $fotoVal = $hasilFoto ? "'" . mysqli_real_escape_string($conn, $hasilFoto) . "'" : "NULL";
            mysqli_query($conn, "INSERT INTO alat (nama_alat, stok, foto) VALUES ('$nama', $stok, $fotoVal)");
            $pesan = "Alat \"$nama\" berhasil ditambahkan.";
            $tipe  = "success";
        }
    } else {
        $pesan = "Nama alat dan stok wajib diisi dengan benar.";
        $tipe  = "danger";
    }
}

if (isset($_GET['hapus'])) {
    $hid = (int)$_GET['hapus'];
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM alat WHERE id_alat=$hid"));
    if ($row && !empty($row['foto']) && file_exists($uploadDir . $row['foto'])) {
        @unlink($uploadDir . $row['foto']);
    }
    mysqli_query($conn, "DELETE FROM alat WHERE id_alat=$hid");
    header("Location: alat.php?msg=deleted");
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $pesan = "Alat berhasil dihapus.";
    $tipe  = "success";
}
if (isset($_GET['msg']) && $_GET['msg'] === 'updated') {
    $pesan = "Alat berhasil diperbarui.";
    $tipe  = "success";
}

// ===== Pencarian & Filter (prepared statement, aman dari SQL injection) =====
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
$sql .= " ORDER BY id_alat";

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
    <h1><i class="bi bi-tools"></i> Data Alat Laboratorium</h1>
    <p>Kelola daftar alat, tambah stok baru, dan perbarui informasi inventaris.</p>
</div>

<?php if ($pesan): ?>
<div class="alert alert-<?= $tipe ?> rounded-3 border-0 d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-<?= $tipe == 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
    <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="form-card mb-4">
    <h5 class="mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Alat Baru</h5>
    <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Nama Alat</label>
            <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Multimeter Digital" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Jumlah Stok</label>
            <input type="number" name="stok" class="form-control" min="0" placeholder="0" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Foto Alat</label>
            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        </div>
        <div class="col-md-2">
            <button type="submit" name="tambah" class="btn btn-primary-grad w-100">
                <i class="bi bi-plus-lg me-1"></i> Tambah Alat
            </button>
        </div>
    </form>
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

<div class="table-card">
    <div class="table-card-header">
        <i class="bi bi-list-ul text-muted"></i>
        <h5>Daftar Alat (<?= mysqli_num_rows($data) ?> item)</h5>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($data) === 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data alat yang cocok dengan pencarian/filter.</td></tr>
                <?php else: while ($d = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td>#<?= $d['id_alat'] ?></td>
                    <td>
                        <?php if (!empty($d['foto']) && file_exists($uploadDir . $d['foto'])): ?>
                            <img src="../uploads/alat/<?= htmlspecialchars($d['foto']) ?>"
                                 alt="<?= htmlspecialchars($d['nama_alat']) ?>"
                                 style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
                        <?php else: ?>
                            <div style="width:48px;height:48px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;">
                                <i class="bi bi-image"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($d['nama_alat']) ?></strong></td>
                    <td><span class="badge-kategori"><?= htmlspecialchars($d['kategori']) ?></span></td>
                    <td><?= $d['stok'] ?></td>
                    <td>
                        <?php if ($d['status_alat'] === 'rusak'): ?>
                            <span class="status-badge badge-rusak">Rusak</span>
                        <?php elseif ($d['stok'] > 0): ?>
                            <span class="status-badge badge-dikembalikan">Tersedia</span>
                        <?php else: ?>
                            <span class="status-badge badge-terlambat">Habis</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary me-1"
                                onclick='bukaDetailAlat(<?= json_encode($d, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                            <i class="bi bi-eye"></i>
                        </button>
                        <a href="alat_edit.php?id=<?= $d['id_alat'] ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="alat.php?hapus=<?= $d['id_alat'] ?>"
                           onclick="return confirm('Hapus alat \'<?= htmlspecialchars($d['nama_alat'], ENT_QUOTES) ?>\'?')"
                           class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../detail_alat_modal.php'; ?>

<?php include 'layout_bottom.php'; ?>
