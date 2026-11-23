<?php
// app/views/pemesanan/cek_status.php
$kodeInput      = $kodeInput ?? '';
$telpInput      = $telpInput ?? '';
$sudahCari      = $sudahCari ?? false;
$hasilPencarian = $hasilPencarian ?? null;
$errorPencarian = $errorPencarian ?? null;

$status = $hasilPencarian['status_pemesanan'] ?? 'Pending';
$progressMap = [
    'Pending'        => ['percent' => 25,  'badge' => 'bg-warning text-dark', 'label' => 'Menunggu Antrean / Proses'],
    'Sedang Dijahit' => ['percent' => 65,  'badge' => 'bg-primary',           'label' => 'Sedang Dijahit di Produksi'],
    'Selesai'        => ['percent' => 100, 'badge' => 'bg-success',           'label' => 'Pesanan Selesai Dijahit'],
];
$progressInfo = $progressMap[$status] ?? ['percent' => 25, 'badge' => 'bg-secondary', 'label' => $status];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pesanan Pelanggan - Sistem Garmen</title>

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

    .brand-header {
        background-color: var(--primary-color);
        color: #ffffff;
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

    .progress-bar-green {
        background-color: var(--primary-color);
    }

    .status-card {
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
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
    <!-- Top Bar Sederhana -->
    <nav class="navbar brand-header py-3 shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h5 fw-bold text-white">
                <i class="fa-solid fa-magnifying-glass-chart me-2"></i>Lacak Status Pesanan
            </span>
        </div>
    </nav>

    <div class="container py-4 py-md-5" style="max-width: 960px;">
        <!-- Form Pencarian -->
        <div class="card status-card mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1" style="color: var(--primary-color);">Cek Progres Pesanan Anda</h4>
                <p class="text-muted small mb-4">
                    Masukkan <strong>Kode Pemesanan</strong> Anda (contoh: <code>ORD-20261123-7K9M2X</code>) untuk
                    melihat status jahitan, deadline, dan ringkasan pembayaran.
                </p>

                <form action="index.php" method="GET" class="row g-3">
                    <input type="hidden" name="action" value="cek_status">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Pemesanan <span class="text-danger">*</span></label>
                        <input type="text" name="kode" class="form-control text-uppercase"
                            value="<?= htmlspecialchars($kodeInput) ?>" placeholder="Contoh: ORD-20261123-7K9M2X"
                            required>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-green w-100 py-2">
                            <i class="fa-solid fa-search me-1"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($errorPencarian)): ?>
        <div class="alert alert-warning rounded-3 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-info me-1"></i> <?= htmlspecialchars($errorPencarian) ?>
        </div>
        <?php endif; ?>

        <?php if ($hasilPencarian): ?>
        <?php
            $totalHarga = (float) ($hasilPencarian['total_harga_keseluruhan'] ?? 0);
            $sudahBayar = (float) ($hasilPencarian['total_sudah_bayar'] ?? 0);
            $sisaBayar  = (float) ($hasilPencarian['sisa_bayar'] ?? 0);
            $details    = $hasilPencarian['details'] ?? [];
            $pembayarans = $hasilPencarian['pembayarans'] ?? [];
            ?>

        <!-- Hasil Status & Progres -->
        <div class="card status-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3 mb-3 border-bottom">
                    <div>
                        <div class="small text-muted">Kode Pemesanan</div>
                        <h5 class="fw-bold mb-0"><?= htmlspecialchars($hasilPencarian['kode_pemesanan']) ?></h5>
                    </div>
                    <div class="text-md-end">
                        <span class="badge <?= $progressInfo['badge'] ?> fs-6 px-3 py-2">
                            <?= htmlspecialchars($status) ?>
                        </span>
                        <div class="small text-muted mt-1"><?= htmlspecialchars($progressInfo['label']) ?></div>
                    </div>
                </div>

                <!-- Progress Bar Tahapan -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Progres Pengerjaan</span>
                        <span><?= (int) $progressInfo['percent'] ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 999px;">
                        <div class="progress-bar progress-bar-green" role="progressbar"
                            style="width: <?= (int) $progressInfo['percent'] ?>%;"
                            aria-valuenow="<?= (int) $progressInfo['percent'] ?>" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small mt-2">
                        <span class="<?= $progressInfo['percent'] >= 25 ? 'fw-semibold text-dark' : 'text-muted' ?>">1.
                            Pending</span>
                        <span class="<?= $progressInfo['percent'] >= 65 ? 'fw-semibold text-dark' : 'text-muted' ?>">2.
                            Sedang Dijahit</span>
                        <span
                            class="<?= $progressInfo['percent'] >= 100 ? 'fw-semibold text-success' : 'text-muted' ?>">3.
                            Selesai</span>
                    </div>
                </div>

                <!-- Informasi Waktu & Pelanggan -->
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <div class="small text-muted">Pelanggan / Member</div>
                            <div class="fw-semibold mt-1"><?= htmlspecialchars($hasilPencarian['nama_member'] ?? '-') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <div class="small text-muted">Tanggal Pesan</div>
                            <div class="fw-semibold mt-1">
                                <?= !empty($hasilPencarian['tanggal_pesan']) ? date('d/m/Y', strtotime($hasilPencarian['tanggal_pesan'])) : '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <div class="small text-muted">Estimasi Deadline</div>
                            <div class="fw-semibold mt-1" style="color: var(--primary-color);">
                                <?= !empty($hasilPencarian['tanggal_deadline']) ? date('d/m/Y', strtotime($hasilPencarian['tanggal_deadline'])) : '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <div class="small text-muted">Total Item</div>
                            <div class="fw-semibold mt-1"><?= (int) ($hasilPencarian['total_jumlah_item'] ?? 0) ?> pcs
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rincian Item Pesanan -->
        <div class="card status-card mb-4">
            <div class="card-header bg-white py-3">
                <span class="fw-bold"><i class="fa-solid fa-shirt me-2" style="color: var(--primary-color);"></i>Rincian
                    Item Pesanan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0 custom-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th>Model Pakaian</th>
                                <th>Ukuran</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($details)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada rincian item.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($details as $i => $d): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($d['nama_model'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($d['ukuran'] ?? '-') ?></td>
                                <td class="text-end"><?= (int) ($d['jumlah'] ?? 0) ?></td>
                                <td class="text-end fw-semibold">Rp
                                    <?= number_format((float) ($d['subtotal_harga'] ?? 0), 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($d['catatan_item'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ringkasan & Riwayat Pembayaran -->
        <div class="card status-card">
            <div class="card-header bg-white py-3">
                <span class="fw-bold"><i class="fa-solid fa-wallet me-2" style="color: var(--primary-color);"></i>Status
                    Pembayaran</span>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center mb-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="small text-muted">Total Pesanan</div>
                            <div class="fw-bold mt-1">Rp <?= number_format($totalHarga, 0, ',', '.') ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="small text-muted">Sudah Dibayar</div>
                            <div class="fw-bold text-success mt-1">Rp <?= number_format($sudahBayar, 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="small text-muted">Sisa Pembayaran</div>
                            <div class="fw-bold <?= $sisaBayar > 0 ? 'text-danger' : 'text-success' ?> mt-1">
                                Rp <?= number_format($sisaBayar, 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($pembayarans)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0 custom-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Metode</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pembayarans as $pb): ?>
                            <tr>
                                <td><?= !empty($pb['tanggal_pembayaran']) ? date('d/m/Y', strtotime($pb['tanggal_pembayaran'])) : '-' ?>
                                </td>
                                <td><?= htmlspecialchars($pb['tipe_pembayaran'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($pb['jenis_pembayaran'] ?? '-') ?></td>
                                <td class="text-end fw-semibold">Rp
                                    <?= number_format((float) ($pb['jumlah_bayar'] ?? 0), 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>