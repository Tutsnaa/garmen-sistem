<?php
// app/views/member/create.php
// Dipanggil di dalam views/member/index.php sebagai popup modal
?>
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-3 shadow">
            <form action="index.php?action=member_store" method="POST">
                <div class="modal-header py-3" style="background-color: var(--primary-color, #2b5748); color: #fff;">
                    <h5 class="modal-title fw-bold" id="modalTambahLabel">
                        <i class="fa-solid fa-user-plus me-2"></i>Tambah Member Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" maxlength="100" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Nama PIC</label>
                            <input type="text" name="nama_pic" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">No. Telepon <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="no_telepon" class="form-control" maxlength="20" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Email</label>
                            <input type="email" name="email" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Status</label>
                            <select name="status" class="form-select">
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
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