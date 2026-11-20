<?php
// app/views/member/index.php
// Variabel $members, $pesan, dan $tipe diproses di public/index.php (case 'member')
$members = $members ?? [];
$pesan   = $pesan ?? ($_GET['pesan'] ?? null);
$tipe    = $tipe ?? (($_GET['tipe'] ?? 'success') === 'danger' ? 'danger' : 'success');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Member - Sistem Garmen</title>
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
                    <i class="fa-solid fa-users me-2"></i>Data Member
                </h5>
                <div class="d-flex gap-2">
                    <a href="index.php?action=dashboard" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                    <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal"
                        data-bs-target="#modalTambah">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Member
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
                                <th>Nama PIC</th>
                                <th>No. Telepon</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($members) > 0): ?>
                            <?php $no = 1; foreach ($members as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_pic'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                                <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Aktif'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit-member"
                                        data-bs-toggle="modal" data-bs-target="#modalEditMember"
                                        data-id="<?= (int) $row['id'] ?>"
                                        data-nama_lengkap="<?= htmlspecialchars($row['nama_lengkap'], ENT_QUOTES) ?>"
                                        data-nama_pic="<?= htmlspecialchars($row['nama_pic'] ?? '', ENT_QUOTES) ?>"
                                        data-no_telepon="<?= htmlspecialchars($row['no_telepon'], ENT_QUOTES) ?>"
                                        data-email="<?= htmlspecialchars($row['email'] ?? '', ENT_QUOTES) ?>"
                                        data-alamat="<?= htmlspecialchars($row['alamat'], ENT_QUOTES) ?>"
                                        data-keterangan="<?= htmlspecialchars($row['keterangan'] ?? '', ENT_QUOTES) ?>"
                                        data-status="<?= htmlspecialchars($row['status'], ENT_QUOTES) ?>">
                                        Edit
                                    </button>
                                    <a href="index.php?action=member_delete&id=<?= (int) $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus member ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada data member.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Form Tambah & Edit Member -->
    <?php require_once __DIR__ . '/create.php'; ?>
    <?php require_once __DIR__ . '/update.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_GET['open_create']) && $_GET['open_create'] === '1'): ?>
        const modalTambahEl = document.getElementById('modalTambah');
        if (modalTambahEl) {
            new bootstrap.Modal(modalTambahEl).show();
        }
        <?php endif; ?>

        const modalEditEl = document.getElementById('modalEditMember');
        if (modalEditEl) {
            modalEditEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button) return;

                document.getElementById('edit_id').value = button.getAttribute('data-id') || '';
                document.getElementById('edit_nama_lengkap').value = button.getAttribute(
                    'data-nama_lengkap') || '';
                document.getElementById('edit_nama_pic').value = button.getAttribute('data-nama_pic') ||
                    '';
                document.getElementById('edit_no_telepon').value = button.getAttribute(
                    'data-no_telepon') || '';
                document.getElementById('edit_email').value = button.getAttribute('data-email') || '';
                document.getElementById('edit_alamat').value = button.getAttribute('data-alamat') || '';
                document.getElementById('edit_keterangan').value = button.getAttribute(
                    'data-keterangan') || '';
                document.getElementById('edit_status').value = button.getAttribute('data-status') ||
                    'Aktif';
            });
        }
    });
    </script>
</body>

</html>