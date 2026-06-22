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

$sedang_dipinjam = mysqli_num_rows(mysqli_query($conn, "SELECT id_pinjam FROM peminjaman WHERE id_user=$id_user AND status='dipinjam'"));
$menunggu        = mysqli_num_rows(mysqli_query($conn, "SELECT id_pinjam FROM peminjaman WHERE id_user=$id_user AND status='menunggu'"));
$terlambat       = mysqli_num_rows(mysqli_query($conn, "SELECT id_pinjam FROM peminjaman WHERE id_user=$id_user AND status='terlambat'"));
$total_riwayat   = mysqli_num_rows(mysqli_query($conn, "SELECT id_pinjam FROM peminjaman WHERE id_user=$id_user"));
$alat_tersedia   = mysqli_num_rows(mysqli_query($conn, "SELECT id_alat FROM alat WHERE stok > 0"));

$mode = 'user';
include '../notifikasi_h1.php';

$terbaru = mysqli_query($conn, "
    SELECT p.*, a.nama_alat
    FROM peminjaman p
    JOIN alat a ON p.id_alat = a.id_alat
    WHERE p.id_user = $id_user
    ORDER BY p.id_pinjam DESC
    LIMIT 5
");

$pageTitle  = 'Beranda';
$activePage = 'home';
include 'layout_top.php';
?>

<div class="page-banner">
    <h1><i class="bi bi-hand-thumbs-up-fill me-2"></i>Selamat datang, <?= htmlspecialchars($_SESSION['user']['username']) ?>!</h1>
    <p>Berikut ringkasan aktivitas peminjaman alat laboratorium Anda.</p>
</div>

<?php include '../notifikasi_h1_user.php'; ?>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card blue">
            <div class="card-icon"><i class="bi bi-arrow-left-right"></i></div>
            <div class="card-num"><?= $sedang_dipinjam ?></div>
            <div class="card-label">Sedang Dipinjam</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card amber">
            <div class="card-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="card-num"><?= $menunggu ?></div>
            <div class="card-label">Menunggu Persetujuan</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card red">
            <div class="card-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="card-num"><?= $terlambat ?></div>
            <div class="card-label">Terlambat</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card emerald">
            <div class="card-icon"><i class="bi bi-box-seam"></i></div>
            <div class="card-num"><?= $alat_tersedia ?></div>
            <div class="card-label">Alat Tersedia</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="table-card h-100">
            <div class="table-card-header">
                <i class="bi bi-clock-history text-muted"></i>
                <h5>Aktivitas Terbaru</h5>
                <a href="riwayat.php" class="btn btn-sm btn-primary-grad">Lihat Semua</a>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($terbaru) === 0): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas peminjaman.</td></tr>
                        <?php else: while ($d = mysqli_fetch_assoc($terbaru)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($d['nama_alat']) ?></strong></td>
                            <td><?= $d['jumlah'] ?></td>
                            <td><?= date('d M Y', strtotime($d['tgl_pinjam'])) ?></td>
                            <td><?= date('d M Y', strtotime($d['tgl_kembali'])) ?></td>
                            <td><span class="status-badge badge-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
                        </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="table-card h-100">
            <div class="table-card-header">
                <i class="bi bi-lightning-charge text-muted"></i>
                <h5>Aksi Cepat</h5>
            </div>
            <div class="p-3 d-flex flex-column gap-2">
                <a href="alat.php" class="btn btn-outline-primary text-start d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-box-seam fs-5"></i>
                    <span>
                        <strong class="d-block" style="font-size:13.5px;">Lihat Data Alat</strong>
                        <span class="text-muted" style="font-size:11.5px;">Pilih alat & ajukan peminjaman</span>
                    </span>
                </a>
                <a href="riwayat.php" class="btn btn-outline-secondary text-start d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-clock-history fs-5"></i>
                    <span>
                        <strong class="d-block" style="font-size:13.5px;">Riwayat Saya</strong>
                        <span class="text-muted" style="font-size:11.5px;">Lihat status & kembalikan alat</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="table-card guide-card">
            <div class="table-card-header">
                <i class="bi bi-signpost-2 text-muted"></i>
                <h5>Tata Cara Peminjaman & Pengembalian</h5>
            </div>
            <div class="row g-4 p-4">
                <div class="col-md-6">
                    <div class="guide-subtitle"><i class="bi bi-1-circle-fill"></i> Langkah Peminjaman</div>
                    <div class="guide-timeline">
                        <div class="guide-step">
                            <div class="guide-step-icon"><i class="bi bi-search"></i></div>
                            <div class="guide-step-text">
                                <strong>Pilih alat yang tersedia</strong>
                                <span>Cari dan pilih alat lab yang ingin dipinjam.</span>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-icon"><i class="bi bi-bag-plus"></i></div>
                            <div class="guide-step-text">
                                <strong>Klik tombol Pinjam</strong>
                                <span>Buka form pengajuan peminjaman pada alat tersebut.</span>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-icon"><i class="bi bi-pencil-square"></i></div>
                            <div class="guide-step-text">
                                <strong>Isi jumlah & kebutuhan</strong>
                                <span>Lengkapi jumlah alat dan tanggal peminjaman.</span>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-icon"><i class="bi bi-hourglass-split"></i></div>
                            <div class="guide-step-text">
                                <strong>Tunggu persetujuan admin</strong>
                                <span>Pengajuan akan diverifikasi oleh admin lab.</span>
                            </div>
                        </div>
                        <div class="guide-step is-last">
                            <div class="guide-step-icon"><i class="bi bi-box-arrow-in-down"></i></div>
                            <div class="guide-step-text">
                                <strong>Ambil alat di laboratorium</strong>
                                <span>Setelah disetujui, ambil alat langsung di lab.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="guide-subtitle"><i class="bi bi-2-circle-fill"></i> Langkah Pengembalian</div>
                    <div class="guide-timeline">
                        <div class="guide-step">
                            <div class="guide-step-icon"><i class="bi bi-tools"></i></div>
                            <div class="guide-step-text">
                                <strong>Gunakan sesuai aturan</strong>
                                <span>Gunakan alat sesuai aturan dan SOP laboratorium.</span>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-icon"><i class="bi bi-arrow-return-left"></i></div>
                            <div class="guide-step-text">
                                <strong>Kembalikan sebelum batas waktu</strong>
                                <span>Pastikan alat dikembalikan tepat waktu.</span>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-icon"><i class="bi bi-clipboard-check"></i></div>
                            <div class="guide-step-text">
                                <strong>Verifikasi kondisi alat</strong>
                                <span>Admin memeriksa kondisi alat yang dikembalikan.</span>
                            </div>
                        </div>
                        <div class="guide-step is-last">
                            <div class="guide-step-icon"><i class="bi bi-check-circle"></i></div>
                            <div class="guide-step-text">
                                <strong>Status menjadi Dikembalikan</strong>
                                <span>Riwayat peminjaman otomatis diperbarui.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.guide-card .table-card-header { margin-bottom: 0; }
.guide-subtitle {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 18px;
}
.guide-subtitle i { color: var(--brand-secondary); font-size: 17px; }
.guide-timeline { position: relative; padding-left: 6px; }
.guide-step { position: relative; display: flex; gap: 14px; padding-bottom: 24px; }
.guide-step.is-last { padding-bottom: 0; }
.guide-step::before {
    content: '';
    position: absolute;
    left: 17px; top: 36px; bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #bae6fd, #e0e7ff);
}
.guide-step.is-last::before { display: none; }
.guide-step-icon {
    flex-shrink: 0;
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--grad-brand);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 15px;
    box-shadow: 0 4px 10px rgba(37,99,235,.22);
    z-index: 1;
}
.guide-step-text { padding-top: 5px; }
.guide-step-text strong { display: block; font-size: 13.5px; color: #0f172a; margin-bottom: 2px; }
.guide-step-text span { font-size: 12.5px; color: #64748b; line-height: 1.5; }
</style>

<?php include 'layout_bottom.php'; ?>
