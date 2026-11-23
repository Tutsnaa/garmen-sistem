<?php
// app/views/pemesanan/show.php
$pemesanan   = $pemesanan ?? [];
$details     = $details ?? [];
$pembayarans = $pembayarans ?? [];
$totalHarga  = (float) ($pemesanan['total_harga_keseluruhan'] ?? 0);
$sudahBayar  = $sudahBayar ?? 0.0;
$sisaBayar   = $sisaBayar ?? max(0, $totalHarga - $sudahBayar);
$status      = $pemesanan['status_pemesanan'] ?? 'Pending';

$badgeClass = match ($status) {
    'Selesai'        => 'bg-success',
    'Sedang Dijahit' => 'bg-primary',
    'Dibatalkan'     => 'bg-danger',
    default          => 'bg-warning text-dark',
};
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan <?= htmlspecialchars($pemesanan['kode_pemesanan'] ?? '') ?> - Sistem Garmen</title>

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

    .detail-card {
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        background-color: #ffffff;
    }

    .custom-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #495057;
        background-color: #eef3f1 !important;
        white-space: nowrap;
    }
    </style>
</head>

<body>
    <div class="container-fluid py-4 px-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h4 class="fw-bold mb-0 page-title-green">
                    <i class="fa-solid fa-file-lines me-2"></i>Detail Pemesanan:
                    <?= htmlspecialchars($pemesanan['kode_pemesanan'] ?? '') ?>
                </h4>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?action=pemesanan_index" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
                <a href="index.php?action=pemesanan_create" class="btn btn-primary-green btn-sm px-3 py-2 rounded-2">
                    <i class="fa-solid fa-plus me-1"></i> Pemesanan Baru
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

        <!-- Ringkasan Pesanan & Member -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card detail-card h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Informasi Pesanan & Member</span>
                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-3">
                            <tr>
                                <td class="text-muted" style="width: 35%;">Member</td>
                                <td class="fw-semibold"><?= htmlspecialchars($pemesanan['nama_member'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">PIC / No. Telepon</td>
                                <td>
                                    <?= htmlspecialchars($pemesanan['nama_pic'] ?? '-') ?>
                                    <?= !empty($pemesanan['no_telepon']) ? ' • ' . htmlspecialchars($pemesanan['no_telepon']) : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Pesan</td>
                                <td><?= !empty($pemesanan['tanggal_pesan']) ? date('d/m/Y', strtotime($pemesanan['tanggal_pesan'])) : '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Deadline</td>
                                <td><?= !empty($pemesanan['tanggal_deadline']) ? date('d/m/Y', strtotime($pemesanan['tanggal_deadline'])) : '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Pembayaran</td>
                                <td><?= htmlspecialchars($pemesanan['jenis_pembayaran'] ?? '-') ?></td>
                            </tr>
                        </table>

                        <!-- Form Update Status -->
                        <form action="index.php?action=pemesanan_update_status" method="POST"
                            class="d-flex gap-2 align-items-center pt-2 border-top">
                            <input type="hidden" name="id_pemesanan" value="<?= (int) ($pemesanan['id'] ?? 0) ?>">
                            <select name="status_pemesanan" class="form-select form-select-sm"
                                style="max-width: 220px;">
                                <?php foreach (['Pending', 'Sedang Dijahit', 'Selesai', 'Dibatalkan'] as $st): ?>
                                <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= $st ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-dark">
                                <i class="fa-solid fa-rotate me-1"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card detail-card h-100">
                    <div class="card-header bg-white py-3">
                        <span class="fw-bold">Ringkasan Tagihan & Pembayaran</span>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="row g-3 text-center">
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-light">
                                    <div class="small text-muted">Total Tagihan</div>
                                    <div class="fw-bold mt-1">Rp <?= number_format($totalHarga, 0, ',', '.') ?></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-light">
                                    <div class="small text-muted">Sudah Dibayar</div>
                                    <div class="fw-bold text-success mt-1">Rp
                                        <?= number_format($sudahBayar, 0, ',', '.') ?></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-light">
                                    <div class="small text-muted">Sisa Bayar</div>
                                    <div class="fw-bold <?= $sisaBayar > 0 ? 'text-danger' : 'text-success' ?> mt-1">
                                        Rp <?= number_format($sisaBayar, 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-muted">
                                Total Item: <strong><?= (int) ($pemesanan['total_jumlah_item'] ?? 0) ?> pcs</strong>
                            </span>
                            <?php if ($sisaBayar > 0): ?>
                            <button type="button" class="btn btn-sm btn-primary-green" data-bs-toggle="modal"
                                data-bs-target="#modalBayar">
                                <i class="fa-solid fa-money-bill-wave me-1"></i> Bayar Pelunasan
                            </button>
                            <?php else: ?>
                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Lunas</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Item Pakaian -->
        <div class="card detail-card mb-4">
            <div class="card-header bg-white py-3">
                <span class="fw-bold">Detail Item Pemesanan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0 custom-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th>Model Pakaian</th>
                                <th>Ukuran</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                                <th>Catatan Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($details)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada detail item.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($details as $idx => $d): ?>
                            <tr>
                                <td class="text-center"><?= $idx + 1 ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($d['nama_model'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($d['ukuran'] ?? '-') ?></td>
                                <td class="text-end"><?= (int) ($d['jumlah'] ?? 0) ?></td>
                                <td class="text-end">Rp
                                    <?= number_format((float) ($d['harga_satuan'] ?? 0), 2, ',', '.') ?></td>
                                <td class="text-end fw-semibold">Rp
                                    <?= number_format((float) ($d['subtotal_harga'] ?? 0), 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($d['catatan_item'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="card detail-card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold">Riwayat Pembayaran (DP & Pelunasan)</span>
                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                    data-bs-target="#modalBayar">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Pembayaran
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0 custom-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Jenis Pembayaran</th>
                                <th class="text-end">Jumlah Bayar</th>
                                <th>Bukti</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pembayarans)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi pembayaran
                                    tercatat.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($pembayarans as $pIdx => $pb): ?>
                            <?php $tipe = $pb['tipe_pembayaran'] ?? 'DP'; ?>
                            <tr>
                                <td class="text-center"><?= $pIdx + 1 ?></td>
                                <td><?= !empty($pb['tanggal_pembayaran']) ? date('d/m/Y', strtotime($pb['tanggal_pembayaran'])) : '-' ?>
                                </td>
                                <td>
                                    <span
                                        class="badge <?= $tipe === 'Pelunasan' ? 'bg-success' : 'bg-info text-dark' ?>">
                                        <?= htmlspecialchars($tipe) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($pb['jenis_pembayaran'] ?? '-') ?></td>
                                <td class="text-end fw-semibold text-success">
                                    Rp <?= number_format((float) ($pb['jumlah_bayar'] ?? 0), 2, ',', '.') ?>
                                </td>
                                <td>
                                    <?php if (!empty($pb['bukti_pembayaran'])): ?>
                                    <a href="<?= htmlspecialchars($pb['bukti_pembayaran']) ?>" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-file-image me-1"></i> Lihat
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($pb['keterangan'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran / Pelunasan -->
    <div class="modal fade" id="modalBayar" tabindex="-1" aria-labelledby="modalBayarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-3 shadow">
                <form action="index.php?action=pemesanan_bayar" method="POST" enctype="multipart/form-data">
                    <div class="modal-header py-3"
                        style="background-color: var(--primary-color, #2b5748); color: #fff;">
                        <h5 class="modal-title fw-bold" id="modalBayarLabel">
                            <i class="fa-solid fa-money-bill-wave me-2"></i>Catat Pembayaran / Pelunasan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_pemesanan" value="<?= (int) ($pemesanan['id'] ?? 0) ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Pembayaran <span
                                    class="text-danger">*</span></label>
                            <select name="tipe_pembayaran" class="form-select" required>
                                <option value="Pelunasan" <?= $sisaBayar > 0 ? 'selected' : '' ?>>Pelunasan</option>
                                <option value="DP">DP</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Pembayaran <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembayaran" class="form-control"
                                    value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenis Pembayaran <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="jenis_pembayaran" class="form-control" maxlength="50"
                                    value="<?= htmlspecialchars($pemesanan['jenis_pembayaran'] ?? '') ?>"
                                    placeholder="Transfer BCA, Tunai, QRIS" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Bayar <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="jumlah_bayar" class="form-control" step="0.01" min="0.01"
                                value="<?= number_format($sisaBayar, 2, '.', '') ?>" required>
                            <div class="form-text">Sisa tagihan saat ini: <strong>Rp
                                    <?= number_format($sisaBayar, 2, ',', '.') ?></strong></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bukti Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" class="form-control"
                                accept=".jpg,.jpeg,.png,.webp,.pdf">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"
                                placeholder="Opsional..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-green btn-sm px-3">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>