<?php
// app/views/pengguna/update.php
// Dipanggil di dalam app/views/pengguna/index.php sebagai popup modal
?>
<div class="modal fade" id="modalEditPengguna" tabindex="-1" aria-labelledby="modalEditPenggunaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-3 shadow">
            <form id="formEditPengguna" action="index.php?action=pengguna_update" method="POST">
                <div class="modal-header py-3" style="background-color: var(--primary-color, #2b5748); color: #fff;">
                    <h5 class="modal-title fw-bold" id="modalEditPenggunaLabel">
                        <i class="fa-solid fa-user-pen me-2"></i>Edit Data Pengguna
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="edit_nama_lengkap" name="nama_lengkap" class="form-control"
                                maxlength="100" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Nama Pengguna <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="edit_nama_pengguna" name="nama_pengguna" class="form-control"
                                maxlength="50" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Email <span class="text-danger">*</span></label>
                            <input type="email" id="edit_email" name="email" class="form-control" maxlength="100"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">No. Telepon <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="edit_no_telepon" name="no_telepon" class="form-control"
                                maxlength="20" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">
                                Kata Sandi Baru <span class="text-muted small">(kosongkan jika tidak diubah)</span>
                            </label>
                            <input type="password" id="edit_kata_sandi" name="kata_sandi" class="form-control"
                                minlength="6">
                        </div>

                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold mb-1">Role <span
                                            class="text-danger">*</span></label>
                                    <select id="edit_role" name="role" class="form-select" required>
                                        <option value="Pegawai">Pegawai</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold mb-1">Status <span
                                            class="text-danger">*</span></label>
                                    <select id="edit_status" name="status" class="form-select" required>
                                        <option value="Aktif">Aktif</option>
                                        <option value="Nonaktif">Nonaktif</option>
                                    </select>
                                </div>
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