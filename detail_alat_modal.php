<div class="modal fade" id="modalDetailAlat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content detail-alat-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detail Alat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="row g-4">
                 
                    <div class="col-md-5">
                        <div class="detail-alat-foto" id="detailAlatFotoWrap">
                            <img id="detailAlatFoto" src="" alt="Foto Alat" style="display:none;">
                            <div id="detailAlatFotoPlaceholder" class="detail-alat-foto-placeholder">
                                <i class="bi bi-tools"></i>
                            </div>
                        </div>
                    </div>
                  
                    <div class="col-md-7">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h4 class="mb-0" id="detailAlatNama">-</h4>
                            <span id="detailAlatStatus" class="status-badge">-</span>
                        </div>
                        <p class="text-muted mb-3" id="detailAlatKode">-</p>

                        <div class="detail-alat-grid">
                            <div class="detail-alat-item">
                                <span class="detail-alat-label"><i class="bi bi-tags me-1"></i>Kategori</span>
                                <span class="detail-alat-value" id="detailAlatKategori">-</span>
                            </div>
                            <div class="detail-alat-item">
                                <span class="detail-alat-label"><i class="bi bi-stars me-1"></i>Kondisi</span>
                                <span class="detail-alat-value" id="detailAlatKondisi">-</span>
                            </div>
                            <div class="detail-alat-item">
                                <span class="detail-alat-label"><i class="bi bi-geo-alt me-1"></i>Lokasi Penyimpanan</span>
                                <span class="detail-alat-value" id="detailAlatLokasi">-</span>
                            </div>
                            <div class="detail-alat-item">
                                <span class="detail-alat-label"><i class="bi bi-layers me-1"></i>Stok</span>
                                <span class="detail-alat-value" id="detailAlatStok">-</span>
                            </div>
                            <div class="detail-alat-item">
                                <span class="detail-alat-label"><i class="bi bi-calendar3 me-1"></i>Tanggal Input</span>
                                <span class="detail-alat-value" id="detailAlatTanggal">-</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="detail-alat-label d-block mb-1"><i class="bi bi-card-text me-1"></i>Deskripsi</span>
                            <p class="detail-alat-desc" id="detailAlatDeskripsi">Tidak ada deskripsi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-alat-modal {
    border-radius: 18px;
    border: none;
    box-shadow: 0 20px 50px rgba(15,23,42,.18);
    animation: detailPopIn .22s ease;
}
@keyframes detailPopIn {
    from { opacity: 0; transform: translateY(8px) scale(.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.detail-alat-foto {
    width: 100%; aspect-ratio: 1/1; border-radius: 14px; overflow: hidden;
    background: linear-gradient(135deg, #e0f2fe, #dbeafe);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.05);
}
.detail-alat-foto img { width: 100%; height: 100%; object-fit: cover; }
.detail-alat-foto-placeholder { font-size: 64px; color: #93c5fd; }
.detail-alat-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px 18px;
    background: #f8fafc; border-radius: 12px; padding: 16px;
}
.detail-alat-item { display: flex; flex-direction: column; gap: 2px; }
.detail-alat-label { font-size: 11.5px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
.detail-alat-value { font-size: 14px; font-weight: 600; color: #0f172a; }
.detail-alat-desc { font-size: 13.5px; color: #475569; line-height: 1.6; margin: 0; }
#detailAlatStatus { font-size: 11.5px; }
</style>

<script>
function bukaDetailAlat(alat) {
    document.getElementById('detailAlatNama').textContent = alat.nama_alat || '-';
    document.getElementById('detailAlatKode').textContent = 'Kode: ' + (alat.kode_alat || '-');
    document.getElementById('detailAlatKategori').textContent = alat.kategori || '-';
    document.getElementById('detailAlatKondisi').textContent = alat.kondisi || '-';
    document.getElementById('detailAlatLokasi').textContent = alat.lokasi || '-';
    document.getElementById('detailAlatStok').textContent = (alat.stok ?? '-') + ' unit';
    document.getElementById('detailAlatDeskripsi').textContent = alat.deskripsi || 'Tidak ada deskripsi.';

    if (alat.created_at) {
        const d = new Date(alat.created_at.replace(' ', 'T'));
        if (!isNaN(d)) {
            document.getElementById('detailAlatTanggal').textContent = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        } else {
            document.getElementById('detailAlatTanggal').textContent = alat.created_at;
        }
    } else {
        document.getElementById('detailAlatTanggal').textContent = '-';
    }

    const statusEl = document.getElementById('detailAlatStatus');
    statusEl.className = 'status-badge';
    if (alat.status_alat === 'rusak') {
        statusEl.classList.add('badge-rusak');
        statusEl.textContent = 'Rusak';
    } else if (parseInt(alat.stok) > 0) {
        statusEl.classList.add('badge-dikembalikan');
        statusEl.textContent = 'Tersedia';
    } else {
        statusEl.classList.add('badge-terlambat');
        statusEl.textContent = 'Habis';
    }

    const img = document.getElementById('detailAlatFoto');
    const placeholder = document.getElementById('detailAlatFotoPlaceholder');
    if (alat.foto) {
        img.src = (window.location.pathname.includes('/admin/') || window.location.pathname.includes('/user/'))
            ? '../uploads/alat/' + alat.foto
            : 'uploads/alat/' + alat.foto;
        img.style.display = 'block';
        placeholder.style.display = 'none';
    } else {
        img.style.display = 'none';
        placeholder.style.display = 'flex';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalDetailAlat'));
    modal.show();
}
</script>