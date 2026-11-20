<?php
// app/views/pengguna/index.php
$penggunaList = $penggunaList ?? [];
$pesan        = $pesan ?? ($_GET['pesan'] ?? null);
$tipe         = $tipe ?? (($_GET['tipe'] ?? 'success') === 'danger' ? 'danger' : 'success');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Sistem Garmen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2b5748;
        --primary-hover: #204135;
    }

    body {
        background-color: #f4f7f6;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card-header-custom {
        background-color: var(--primary-color);
        color: #fff;
    }

    .btn-custom {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
    }

    .btn-custom:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #fff;
    }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div
                class="card-header card-header-custom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-user-gear me-2"></i>Data Pengguna
                </h5>
                <div class="d-flex gap-2">
                    <a href="index.php?action=dashboard" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                    <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal"
                        data-bs-target="#modalTambahPengguna">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Pengguna
                    </button>
                </div>
            </div>

            <div class="card-body">

                <?php if ($pesan !== null): ?>
                <div class="alert alert-<?= $tipe ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($pesan) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Nama Pengguna</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($penggunaList) > 0): ?>
                            <?php $no = 1; foreach ($penggunaList as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                                <td>
                                    <span
                                        class="badge <?= $row['role'] === 'Admin' ? 'bg-primary' : 'bg-info text-dark' ?>">
                                        <?= htmlspecialchars($row['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Aktif'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit-pengguna"
                                        data-id="<?= (int) $row['id'] ?>"
                                        data-nama-lengkap="<?= htmlspecialchars($row['nama_lengkap'], ENT_QUOTES) ?>"
                                        data-nama-pengguna="<?= htmlspecialchars($row['nama_pengguna'], ENT_QUOTES) ?>"
                                        data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>"
                                        data-no-telepon="<?= htmlspecialchars($row['no_telepon'], ENT_QUOTES) ?>"
                                        data-role="<?= htmlspecialchars($row['role'], ENT_QUOTES) ?>"
                                        data-status="<?= htmlspecialchars($row['status'], ENT_QUOTES) ?>">
                                        Edit
                                    </button>
                                    <a href="index.php?action=pengguna_delete&id=<?= (int) $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data pengguna.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Form Tambah & Edit Pengguna -->
    <?php require_once __DIR__ . '/create.php'; ?>
    <?php require_once __DIR__ . '/update.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModalEl = document.getElementById('modalEditPengguna');
        const editFormEl = document.getElementById('formEditPengguna');

        document.querySelectorAll('.btn-edit-pengguna').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;

                editFormEl.action = 'index.php?action=pengguna_update&id=' + encodeURIComponent(
                    id);
                document.getElementById('edit_nama_lengkap').value = this.dataset.namaLengkap ||
                    '';
                document.getElementById('edit_nama_pengguna').value = this.dataset
                    .namaPengguna || '';
                document.getElementById('edit_email').value = this.dataset.email || '';
                document.getElementById('edit_no_telepon').value = this.dataset.noTelepon || '';
                document.getElementById('edit_role').value = this.dataset.role || 'Pegawai';
                document.getElementById('edit_status').value = this.dataset.status || 'Aktif';
                document.getElementById('edit_kata_sandi').value = '';

                if (editModalEl) {
                    bootstrap.Modal.getOrCreateInstance(editModalEl).show();
                }
            });
        });

        <?php if (isset($_GET['open_create']) && $_GET['open_create'] === '1'): ?>
        const createModalEl = document.getElementById('modalTambahPengguna');
        if (createModalEl) {
            bootstrap.Modal.getOrCreateInstance(createModalEl).show();
        }
        <?php endif; ?>
    });
    </script>
</body>

</html>