<?php
// app/views/model_pakaian/update.php
// Dipanggil di dalam app/views/model_pakaian/index.php sebagai popup modal
?>
<div class="modal fade" id="modalEditModel" tabindex="-1" aria-labelledby="modalEditModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-3 shadow">
            <form id="formEditModel" action="index.php?action=model_pakaian_update" method="POST"
                enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">

                <div class="modal-header py-3" style="background-color: var(--primary-color, #2b5748); color: #fff;">
                    <h5 class="modal-title fw-bold" id="modalEditModelLabel">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Edit Model Pakaian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Nama Model <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="edit_nama_model" name="nama_model" class="form-control"
                                maxlength="100" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Member</label>
                            <select id="edit_id_member" name="id_member" class="form-select">
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
                            <input type="text" id="edit_ukuran" name="ukuran" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Harga Dasar <span
                                    class="text-danger">*</span></label>
                            <input type="number" id="edit_harga_dasar" name="harga_dasar" class="form-control"
                                step="0.01" min="0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Catatan</label>
                            <textarea id="edit_catatan" name="catatan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Foto Sampel Baru</label>
                            <input type="file" id="edit_foto_sampel" name="foto_sampel" class="form-control"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <img id="edit_foto_preview" src="" alt="Foto Sampel" class="rounded border d-none"
                                    style="width: 48px; height: 48px; object-fit: cover;">
                                <span id="edit_no_foto" class="small text-muted">Tidak ada foto</span>
                            </div>
                            <div id="edit_hapus_foto_wrapper" class="form-check mt-1 d-none">
                                <input class="form-check-input" type="checkbox" value="1" id="edit_hapus_foto"
                                    name="hapus_foto">
                                <label class="form-check-label small text-danger" for="edit_hapus_foto">
                                    Hapus foto sampel
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-custom btn-sm px-3">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>