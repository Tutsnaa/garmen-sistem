<?php
// app/views/member/update.php
// Variabel $member dan $errorMessage disiapkan di public/index.php
$member = $member ?? [];
$errorMessage = $errorMessage ?? null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member - Sistem Garmen</title>
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

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header card-header-custom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-user-pen me-2"></i>Edit Data Member
                        </h5>
                        <a href="index.php?action=member" class="btn btn-outline-light btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body p-4">
                        <?php if ($errorMessage !== null): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($errorMessage) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form action="index.php?action=member_update&id=<?= (int) $member['id'] ?>" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold mb-1">Nama Lengkap <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" class="form-control" maxlength="100"
                                        value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? $member['nama_lengkap']) ?>"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold mb-1">Nama PIC</label>
                                    <input type="text" name="nama_pic" class="form-control" maxlength="100"
                                        value="<?= htmlspecialchars($_POST['nama_pic'] ?? ($member['nama_pic'] ?? '')) ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold mb-1">No. Telepon <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="no_telepon" class="form-control" maxlength="20"
                                        value="<?= htmlspecialchars($_POST['no_telepon'] ?? $member['no_telepon']) ?>"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold mb-1">Email</label>
                                    <input type="email" name="email" class="form-control" maxlength="100"
                                        value="<?= htmlspecialchars($_POST['email'] ?? ($member['email'] ?? '')) ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold mb-1">Alamat <span
                                            class="text-danger">*</span></label>
                                    <textarea name="alamat" class="form-control" rows="2"
                                        required><?= htmlspecialchars($_POST['alamat'] ?? $member['alamat']) ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold mb-1">Keterangan</label>
                                    <textarea name="keterangan" class="form-control"
                                        rows="2"><?= htmlspecialchars($_POST['keterangan'] ?? ($member['keterangan'] ?? '')) ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold mb-1">Status <span
                                            class="text-danger">*</span></label>
                                    <?php $currentStatus = $_POST['status'] ?? $member['status']; ?>
                                    <select name="status" class="form-select" required>
                                        <option value="Aktif" <?= $currentStatus === 'Aktif' ? 'selected' : '' ?>>Aktif
                                        </option>
                                        <option value="Nonaktif" <?= $currentStatus === 'Nonaktif' ? 'selected' : '' ?>>
                                            Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="index.php?action=member" class="btn btn-secondary btn-sm px-3">Batal</a>
                                <button type="submit" class="btn btn-custom btn-sm px-3">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>