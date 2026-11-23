<?php
// app/views/pemesanan/index.php
// Variabel yang diharapkan dari PemesananController::index():
// $pemesanans (array), $flashSuccess (?string), $flashError (?string)
$pemesanans = $pemesanans ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pemesanan - Sistem Garmen</title>

    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
    :root {
        --primary-color: #2b5748;
        --primary-hover: #214438;
        --bg-soft: #f4f7f6;
    }

    body {
        background-color: var(--bg-soft);
        color: #212529;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .page-title-green {
        color: var(--primary-color);
    }

    .btn-primary-green {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #ffffff;
    }

    .btn-primary-green:hover,
    .btn-primary-green:focus {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #ffffff;
    }

    .pemesanan-card {
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        background-color: #ffffff;
        overflow: hidden;
    }

    .pemesanan-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #495057;
        background-color: #eef3f1 !important;
        white-space: nowrap;
    }

    .pemesanan-table td {
        font-size: 0.88rem;
        vertical-align: middle;
    }
    </style>
</head>

<body>
    <div class="container-fluid py-4 px-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h4 class="fw-bold mb-0 page-title-green">
                <i class="fa-solid fa-clipboard-list me-2"></i>Daftar Pemesanan
            </h4>
            <div class="d-flex gap-2">
                <a href="index.php?action=dashboard" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-2">
                    <i class="fa-solid fa-house me-1"></i> Dashboard
                </a>
                <a href="index.php?action=pemesanan_create" class="btn btn-primary-green btn-sm px-3 py-2 rounded-2">
                    <i class="fa-solid fa-plus me-1"></i> Buat Pemesanan Baru
                </a>
            </div>
        </div>

        <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="fa-solid fa-circle-check me-1"></i> <?= htmlspecialchars($flashSuccess) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($flashError) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
        <?php endif; ?>

        <div class="pemesanan-card">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0 pemesanan-table">
                    <thead>
                        <tr>
                            <th style="width: 4%;" class="text-center">#</th>
                            <th>Kode Pemesanan</th>
                            <th>Member</th>
                            <th>Tgl. Pesan</th>
                            <th>Deadline</th>
                            <th class="text-end">Total Item</th>
                            <th class="text-end">Total Harga</th>
                            <th class="text-end">Sudah Bayar</th>
                            <th class="text-end">Sisa Bayar</th>
                            <th>Pembayaran</th>
                            <th class="text-center">Status</th>
                            <th style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pemesanans)): ?>
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada data pemesanan. Klik <strong>Buat Pemesanan Baru</strong> untuk memulai.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($pemesanans as $index => $row): ?>
                        <?php
                                $totalHarga = (float) ($row['total_harga_keseluruhan'] ?? 0);
                                $sudahBayar = (float) ($row['total_sudah_bayar'] ?? 0);
                                $sisaBayar  = max(0, $totalHarga - $sudahBayar);
                                $status     = $row['status_pemesanan'] ?? 'Pending';

                                $badgeClass = match ($status) {
                                    'Selesai'        => 'bg-success',
                                    'Sedang Dijahit' => 'bg-primary',
                                    default         => 'bg-warning text-dark',
                                };
                                ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['kode_pemesanan'] ?? '') ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($row['nama_member'] ?? '-') ?></div>
                                <?php if (!empty($row['nama_pic']) || !empty($row['no_telepon'])): ?>
                                <small class="text-muted">
                                    <?= htmlspecialchars($row['nama_pic'] ?? '') ?>
                                    <?= (!empty($row['nama_pic']) && !empty($row['no_telepon'])) ? ' • ' : '' ?>
                                    <?= htmlspecialchars($row['no_telepon'] ?? '') ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td><?= !empty($row['tanggal_pesan']) ? htmlspecialchars(date('d/m/Y', strtotime($row['tanggal_pesan']))) : '-' ?>
                            </td>
                            <td><?= !empty($row['tanggal_deadline']) ? htmlspecialchars(date('d/m/Y', strtotime($row['tanggal_deadline']))) : '-' ?>
                            </td>
                            <td class="text-end"><?= (int) ($row['total_jumlah_item'] ?? 0) ?></td>
                            <td class="text-end fw-semibold">Rp <?= number_format($totalHarga, 2, ',', '.') ?></td>
                            <td class="text-end text-success">Rp <?= number_format($sudahBayar, 2, ',', '.') ?></td>
                            <td class="text-end <?= $sisaBayar > 0 ? 'text-danger fw-semibold' : 'text-muted' ?>">
                                Rp <?= number_format($sisaBayar, 2, ',', '.') ?>
                            </td>
                            <td><?= htmlspecialchars($row['jenis_pembayaran'] ?? '-') ?></td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                            <td class="text-center">
                                <a href="index.php?action=pemesanan_show&id=<?= (int) $row['id'] ?>"
                                    class="btn btn-sm btn-outline-secondary" title="Detail & Pembayaran">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>