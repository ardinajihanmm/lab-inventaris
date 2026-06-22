<?php
if (!isset($notif_count) || $notif_count === 0) return;
?>

<div class="notif-h1-banner mb-4">
    <div class="notif-h1-icon"><i class="bi bi-bell-fill"></i></div>
    <div class="notif-h1-body">
        <div class="notif-h1-title">
            Pengingat Pengembalian — Besok!
        </div>
        <div class="notif-h1-sub">
            <?= $notif_count ?> alat berikut harus dikembalikan <strong>besok,
            <?= date('d M Y', strtotime('+1 day')) ?></strong>. Segera kembalikan sebelum dinyatakan terlambat.
        </div>
        <div class="notif-h1-list mt-2">
            <?php while ($n = mysqli_fetch_assoc($notif_h1)): ?>
            <div class="notif-h1-item">
                <i class="bi bi-box-seam"></i>
                <span>
                    <strong><?= htmlspecialchars($n['nama_alat']) ?></strong>
                    &mdash; <?= $n['jumlah'] ?> unit
                    <span class="notif-h1-date">
                        <i class="bi bi-calendar-x ms-1"></i>
                        <?= date('d M Y', strtotime($n['tgl_kembali'])) ?>
                    </span>
                </span>
                <a href="kembalikan.php?id=<?= $n['id_pinjam'] ?>"
                   class="notif-h1-btn"
                   onclick="return confirm('Kembalikan alat <?= htmlspecialchars(addslashes($n['nama_alat'])) ?> sekarang?')">
                    <i class="bi bi-arrow-return-left me-1"></i>Kembalikan
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
.notif-h1-banner {
    display: flex;
    gap: 18px;
    background: linear-gradient(135deg, #fef9c3 0%, #fef3c7 100%);
    border: 1.5px solid #fbbf24;
    border-radius: 16px;
    padding: 20px 24px;
    box-shadow: 0 4px 16px rgba(251,191,36,.15);
    animation: notifSlideIn .3s ease;
}
@keyframes notifSlideIn {
    from { opacity:0; transform:translateY(-6px); }
    to   { opacity:1; transform:translateY(0); }
}
.notif-h1-icon {
    flex-shrink: 0;
    width: 46px; height: 46px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fff;
    box-shadow: 0 4px 10px rgba(245,158,11,.3);
}
.notif-h1-body { flex: 1; }
.notif-h1-title {
    font-weight: 800;
    font-size: 15px;
    color: #78350f;
    margin-bottom: 4px;
}
.notif-h1-sub { font-size: 13.5px; color: #92400e; }
.notif-h1-list { display: flex; flex-direction: column; gap: 10px; }
.notif-h1-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,.65);
    border: 1px solid #fde68a;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13.5px;
    color: #78350f;
    flex-wrap: wrap;
}
.notif-h1-item > i { color: #d97706; font-size: 15px; flex-shrink: 0; }
.notif-h1-item > span { flex: 1; }
.notif-h1-date { color: #b45309; font-size: 12px; margin-left: 4px; }
.notif-h1-btn {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    transition: opacity .15s, transform .15s;
    box-shadow: 0 2px 8px rgba(217,119,6,.25);
}
.notif-h1-btn:hover { opacity:.9; transform:translateY(-1px); color:#fff; }
</style>