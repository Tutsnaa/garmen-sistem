<?php
// app/views/model_pakaian/create.php
// Dipanggil di dalam app/views/model_pakaian/index.php sebagai popup modal
?>
<div class="modal fade" id="modalTambahModel" tabindex="-1" aria-labelledby="modalTambahModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-3 shadow">
            <form action="index.php?action=model_pakaian_store" method="POST" enctype="multipart/form-data">
                <div class="modal-header py-3" style="background-color: var(--primary-color, #2b5748); color: #fff;">
                    <h5 class="modal-title fw-bold" id="modalTambahModelLabel">
                        <i class="fa-solid fa-shirt me-2"></i>Tambah Model Pakaian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Nama Model <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_model" class="form-control" maxlength="100" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Member</label>
                            <select name="id_member" class="form-select">
                                <option value="">-- Pilih Member (Opsional) --</option>
                                <?php foreach ($members as $m): ?>
                                <option value="<?= (int) $m['id'] ?>">
                                    <?= htmlspecialchars($m['nama_lengkap']) ?>
                                    <?= !empty($m['nama_pic']) ? ' - ' . htmlspecialchars($m['nama_pic']) : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Ukuran</label>
                            <input type="text" name="ukuran" class="form-control" maxlength="100"
                                placeholder="Contoh: S, M, L, XL">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Harga Dasar <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="harga_dasar" class="form-control" step="0.01" min="0"
                                value="0.00" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Foto Sampel</label>
                            <input type="file" name="foto_sampel" class="form-control"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-custom btn-sm px-3">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>