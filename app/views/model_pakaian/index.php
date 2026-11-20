<?php
// app/views/model_pakaian/index.php
// Variabel $models, $members, $pesan, dan $tipe diproses di ModelPakaianController::index()
$models  = $models ?? [];
$members = $members ?? [];
$pesan   = $pesan ?? ($_GET['pesan'] ?? null);
$tipe    = $tipe ?? (($_GET['tipe'] ?? 'success') === 'danger' ? 'danger' : 'success');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Model Pakaian - Sistem Garmen</title>
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

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div
                class="card-header card-header-custom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-shirt me-2"></i>Data Model Pakaian
                </h5>
                <div class="d-flex gap-2">
                    <a href="index.php?action=dashboard" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                    <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal"
                        data-bs-target="#modalTambahModel">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Model
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
                                <th>Foto</th>
                                <th>Nama Model</th>
                                <th>Member</th>
                                <th>Ukuran</th>
                                <th>Harga Dasar</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($models) > 0): ?>
                            <?php $no = 1; foreach ($models as $row): ?>
                            <?php $fotoUrl = !empty($row['foto_sampel']) ? htmlspecialchars($row['foto_sampel'], ENT_QUOTES) : ''; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="text-center">
                                    <?php if (!empty($row['foto_sampel'])): ?>
                                    <img src="<?= $fotoUrl ?>"
                                        alt="<?= htmlspecialchars($row['nama_model'], ENT_QUOTES) ?>"
                                        class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">
                                    <?php else: ?>
                                    <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($row['nama_model']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_member'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['ukuran'] ?? '-') ?></td>
                                <td class="text-nowrap">
                                    Rp <?= number_format((float) ($row['harga_dasar'] ?? 0), 0, ',', '.') ?>
                                </td>
                                <td><?= htmlspecialchars($row['catatan'] ?? '-') ?></td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit-model"
                                        data-bs-toggle="modal" data-bs-target="#modalEditModel"
                                        data-id="<?= (int) $row['id'] ?>"
                                        data-id_member="<?= htmlspecialchars((string) ($row['id_member'] ?? ''), ENT_QUOTES) ?>"
                                        data-nama_model="<?= htmlspecialchars($row['nama_model'], ENT_QUOTES) ?>"
                                        data-ukuran="<?= htmlspecialchars($row['ukuran'] ?? '', ENT_QUOTES) ?>"
                                        data-harga_dasar="<?= htmlspecialchars((string) ($row['harga_dasar'] ?? '0.00'), ENT_QUOTES) ?>"
                                        data-catatan="<?= htmlspecialchars($row['catatan'] ?? '', ENT_QUOTES) ?>"
                                        data-foto_sampel="<?= $fotoUrl ?>">
                                        Edit
                                    </button>
                                    <a href="index.php?action=model_pakaian_delete&id=<?= (int) $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus model pakaian ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data model pakaian.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Form Tambah & Edit Model Pakaian -->
    <?php require_once __DIR__ . '/create.php'; ?>
    <?php require_once __DIR__ . '/update.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_GET['open_create']) && $_GET['open_create'] === '1'): ?>
        const modalTambahEl = document.getElementById('modalTambahModel');
        if (modalTambahEl) {
            new bootstrap.Modal(modalTambahEl).show();
        }
        <?php endif; ?>

        const modalEditEl = document.getElementById('modalEditModel');
        if (modalEditEl) {
            modalEditEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button) return;

                document.getElementById('edit_id').value = button.getAttribute('data-id') || '';
                document.getElementById('edit_id_member').value = button.getAttribute(
                    'data-id_member') || '';
                document.getElementById('edit_nama_model').value = button.getAttribute(
                    'data-nama_model') || '';
                document.getElementById('edit_ukuran').value = button.getAttribute('data-ukuran') || '';
                document.getElementById('edit_harga_dasar').value = button.getAttribute(
                    'data-harga_dasar') || '0.00';
                document.getElementById('edit_catatan').value = button.getAttribute('data-catatan') ||
                    '';

                const fotoSampel = button.getAttribute('data-foto_sampel') || '';
                const previewImg = document.getElementById('edit_foto_preview');
                const noFotoText = document.getElementById('edit_no_foto');
                const hapusFotoBox = document.getElementById('edit_hapus_foto_wrapper');
                const hapusFotoCheckbox = document.getElementById('edit_hapus_foto');
                const fileInput = document.getElementById('edit_foto_sampel');

                if (fileInput) fileInput.value = '';
                if (hapusFotoCheckbox) hapusFotoCheckbox.checked = false;

                if (fotoSampel) {
                    previewImg.src = fotoSampel;
                    previewImg.classList.remove('d-none');
                    noFotoText.classList.add('d-none');
                    hapusFotoBox.classList.remove('d-none');
                } else {
                    previewImg.src = '';
                    previewImg.classList.add('d-none');
                    noFotoText.classList.remove('d-none');
                    hapusFotoBox.classList.add('d-none');
                }
            });
        }
    });
    </script>
</body>

</html>