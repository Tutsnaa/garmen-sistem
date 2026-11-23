<?php
// app/views/pemesanan/create.php
// Variabel yang diharapkan dari controller:
// $members (array), $modelPakaians (array), $kodeOtomatis (string), $flashError (?string)
$members = $members ?? [];
$modelPakaians = $modelPakaians ?? [];
$kodeOtomatis = $kodeOtomatis ?? 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pemesanan Baru - Sistem Garmen</title>

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

    .btn-primary-green,
    .btn-custom {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #ffffff;
    }

    .btn-primary-green:hover,
    .btn-primary-green:focus,
    .btn-custom:hover,
    .btn-custom:focus {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #ffffff;
    }

    .step-badge {
        background-color: var(--primary-color);
        color: #ffffff;
    }

    .form-card {
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        background-color: #ffffff;
    }

    .detail-table th {
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
            <h4 class="fw-bold mb-0 page-title-green">
                <i class="fa-solid fa-file-invoice me-2"></i>Buat Pemesanan Baru
            </h4>
            <a href="index.php?action=pemesanan_index" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($flashError) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
        <?php endif; ?>

        <form action="index.php?action=pemesanan_store" method="POST" enctype="multipart/form-data" id="formPemesanan">
            <!-- STEP 1: PILIH MEMBER -->
            <div class="card form-card mb-4">
                <div
                    class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="fw-bold"><span class="badge step-badge me-2">1</span>Data Member</span>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                        data-bs-target="#modalTambah">
                        <i class="fa-solid fa-user-plus me-1"></i> Tambah Member Baru
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cari Member (Nama / PIC / No. Telepon)</label>
                            <input type="text" id="cariMember" class="form-control"
                                placeholder="Ketik nama member atau no. HP...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih Member <span
                                    class="text-danger">*</span></label>
                            <select name="id_member" id="selectMember" class="form-select" required>
                                <option value="">-- Pilih Member --</option>
                                <?php foreach ($members as $m): ?>
                                <option value="<?= (int) $m['id'] ?>"
                                    data-search="<?= strtolower(htmlspecialchars($m['nama_lengkap'] . ' ' . ($m['nama_pic'] ?? '') . ' ' . ($m['no_telepon'] ?? ''))) ?>">
                                    <?= htmlspecialchars($m['nama_lengkap']) ?>
                                    <?= !empty($m['nama_pic']) ? ' (PIC: ' . htmlspecialchars($m['nama_pic']) . ')' : '' ?>
                                    <?= !empty($m['no_telepon']) ? ' - ' . htmlspecialchars($m['no_telepon']) : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="memberKosongAlert" class="form-text text-danger d-none mt-1">
                                Member tidak ditemukan. Silakan klik <strong>Tambah Member Baru</strong> di kanan atas.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: DETAIL PEMESANAN (MODEL PAKAIAN) -->
            <div class="card form-card mb-4">
                <div
                    class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="fw-bold"><span class="badge step-badge me-2">2</span>Detail Pemesanan (Model
                        Pakaian)</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                            data-bs-target="#modalTambahModel">
                            <i class="fa-solid fa-shirt me-1"></i> Tambah Model Baru
                        </button>
                        <button type="button" class="btn btn-sm btn-primary-green" id="btnTambahBaris">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 detail-table" id="tabelDetailItem">
                            <thead>
                                <tr>
                                    <th style="width: 26%;">Model Pakaian <span class="text-danger">*</span></th>
                                    <th style="width: 12%;">Ukuran <span class="text-danger">*</span></th>
                                    <th style="width: 10%;">Jumlah <span class="text-danger">*</span></th>
                                    <th style="width: 16%;">Harga Satuan <span class="text-danger">*</span></th>
                                    <th style="width: 16%;">Subtotal</th>
                                    <th style="width: 10%;">Catatan</th>
                                    <th style="width: 4%;" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="bodyDetailItem">
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][id_model_pakaian]"
                                            class="form-select form-select-sm select-model" required>
                                            <option value="" data-harga="0" data-ukuran="">-- Pilih Model --</option>
                                            <?php foreach ($modelPakaians as $mp): ?>
                                            <option value="<?= (int) $mp['id'] ?>"
                                                data-harga="<?= htmlspecialchars($mp['harga_dasar']) ?>"
                                                data-ukuran="<?= htmlspecialchars($mp['ukuran'] ?? '') ?>">
                                                <?= htmlspecialchars($mp['nama_model']) ?>
                                                <?= !empty($mp['ukuran']) ? ' [' . htmlspecialchars($mp['ukuran']) . ']' : '' ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][ukuran]"
                                            class="form-control form-control-sm input-ukuran" maxlength="50"
                                            placeholder="M / XL" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][jumlah]"
                                            class="form-control form-control-sm input-jumlah" min="1" value="1"
                                            required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][harga_satuan]"
                                            class="form-control form-control-sm input-harga" step="0.01" min="0"
                                            value="0.00" required>
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm bg-light input-subtotal-display"
                                            value="0.00" readonly>
                                        <input type="hidden" name="items[0][subtotal_harga]" class="input-subtotal"
                                            value="0.00">
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][catatan_item]"
                                            class="form-control form-control-sm" placeholder="Catatan...">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris"
                                            title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row justify-content-end g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1">Total Jumlah Item</label>
                            <input type="number" name="total_jumlah_item" id="totalJumlahItem"
                                class="form-control form-control-sm fw-bold bg-white" value="1" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">Total Harga Keseluruhan</label>
                            <input type="number" name="total_harga_keseluruhan" id="totalHargaKeseluruhan"
                                class="form-control form-control-sm fw-bold bg-white" step="0.01" value="0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3 & 4: INFORMASI PESANAN & PEMBAYARAN -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card form-card h-100">
                        <div class="card-header bg-white py-3">
                            <span class="fw-bold"><span class="badge step-badge me-2">3</span>Informasi Pesanan</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kode Pemesanan <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="kode_pemesanan" id="kodePemesanan" class="form-control"
                                            maxlength="50" value="<?= htmlspecialchars($kodeOtomatis) ?>"
                                            autocomplete="off" required>
                                        <button type="button" class="btn btn-outline-secondary" id="btnAcakKode"
                                            title="Acak Kode Baru">
                                            <i class="fa-solid fa-shuffle"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Terisi kode acak otomatis, bisa diketik manual atau diacak
                                        ulang.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status Pemesanan</label>
                                    <select name="status_pemesanan" class="form-select">
                                        <option value="Pending" selected>Pending</option>
                                        <option value="Sedang Dijahit">Sedang Dijahit</option>
                                        <option value="Selesai">Selesai</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Pesan <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pesan" class="form-control"
                                        value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Deadline <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_deadline" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card form-card h-100">
                        <div class="card-header bg-white py-3">
                            <span class="fw-bold"><span class="badge step-badge me-2">4</span>Pembayaran</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Pembayaran</label>
                                    <input type="date" name="tanggal_pembayaran" class="form-control"
                                        value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tipe Pembayaran</label>
                                    <select name="tipe_pembayaran" class="form-select">
                                        <option value="DP" selected>DP</option>
                                        <option value="Pelunasan">Pelunasan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jenis Pembayaran <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="jenis_pembayaran" id="jenisPembayaran" class="form-control"
                                        maxlength="50" placeholder="Contoh: Transfer BCA, Tunai, QRIS" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jumlah Bayar</label>
                                    <input type="number" name="jumlah_bayar" id="jumlahBayar" class="form-control"
                                        step="0.01" min="0" value="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Bukti Pembayaran</label>
                                    <input type="file" name="bukti_pembayaran" class="form-control"
                                        accept=".jpg,.jpeg,.png,.webp,.pdf">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Keterangan Pembayaran</label>
                                    <input type="text" name="keterangan_pembayaran" class="form-control"
                                        placeholder="Opsional...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="reset" class="btn btn-secondary px-4">Reset</button>
                <button type="submit" class="btn btn-primary-green px-4">
                    <i class="fa-solid fa-check-circle me-1"></i> Simpan Pemesanan
                </button>
            </div>
        </form>
    </div>

    <?php
    // Sertakan kedua modal agar bisa diakses langsung dari halaman pemesanan
    require_once __DIR__ . '/../member/create.php';
    require_once __DIR__ . '/../model_pakaian/create.php';
    ?>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // acak kode pemesanan
        const kodeInput = document.getElementById('kodePemesanan');
        const btnAcakKode = document.getElementById('btnAcakKode');

        if (btnAcakKode && kodeInput) {
            btnAcakKode.addEventListener('click', function() {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                const now = new Date();
                const ymd = now.getFullYear().toString() +
                    String(now.getMonth() + 1).padStart(2, '0') +
                    String(now.getDate()).padStart(2, '0');

                let rand = '';
                for (let i = 0; i < 6; i++) {
                    rand += chars.charAt(Math.floor(Math.random() * chars.length));
                }

                kodeInput.value = `ORD-${ymd}-${rand}`;
                kodeInput.focus();
            });
        }
        // 1. Pencarian Member
        const cariMember = document.getElementById('cariMember');
        const selectMember = document.getElementById('selectMember');
        const memberKosongAlert = document.getElementById('memberKosongAlert');

        cariMember.addEventListener('input', function() {
            const keyword = this.value.toLowerCase().trim();
            let visibleCount = 0;

            Array.from(selectMember.options).forEach((opt, idx) => {
                if (idx === 0) return;
                const hay = opt.getAttribute('data-search') || '';
                const match = !keyword || hay.includes(keyword);
                opt.hidden = !match;
                if (match) visibleCount++;
            });

            memberKosongAlert.classList.toggle('d-none', visibleCount > 0);
        });

        // 2. Hitung Subtotal & Total Pesanan
        const bodyDetail = document.getElementById('bodyDetailItem');
        const totalJumlahEl = document.getElementById('totalJumlahItem');
        const totalHargaEl = document.getElementById('totalHargaKeseluruhan');

        function hitungTotal() {
            let totalQty = 0;
            let totalHarga = 0;

            bodyDetail.querySelectorAll('.item-row').forEach(row => {
                const qty = parseInt(row.querySelector('.input-jumlah').value, 10) || 0;
                const harga = parseFloat(row.querySelector('.input-harga').value) || 0;
                const subtotal = qty * harga;

                row.querySelector('.input-subtotal').value = subtotal.toFixed(2);
                row.querySelector('.input-subtotal-display').value = subtotal.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                totalQty += qty;
                totalHarga += subtotal;
            });

            totalJumlahEl.value = totalQty;
            totalHargaEl.value = totalHarga.toFixed(2);
        }

        bodyDetail.addEventListener('change', function(e) {
            if (e.target.classList.contains('select-model')) {
                const opt = e.target.selectedOptions[0];
                const row = e.target.closest('.item-row');
                const hargaDasar = parseFloat(opt.getAttribute('data-harga')) || 0;
                const ukuranDefault = opt.getAttribute('data-ukuran') || '';

                row.querySelector('.input-harga').value = hargaDasar.toFixed(2);
                if (ukuranDefault && !row.querySelector('.input-ukuran').value) {
                    row.querySelector('.input-ukuran').value = ukuranDefault;
                }
                hitungTotal();
            }
        });

        bodyDetail.addEventListener('input', function(e) {
            if (e.target.classList.contains('input-jumlah') || e.target.classList.contains(
                    'input-harga')) {
                hitungTotal();
            }
        });

        // Tambah baris item baru
        let rowIndex = 1;
        document.getElementById('btnTambahBaris').addEventListener('click', function() {
            const firstRow = bodyDetail.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('input, select').forEach(el => {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace(/items\[\d+\]/, `items[${rowIndex}]`));
                }
                if (el.classList.contains('input-jumlah')) el.value = 1;
                else if (el.classList.contains('input-harga') || el.classList.contains(
                        'input-subtotal')) el.value = '0.00';
                else if (el.classList.contains('input-subtotal-display')) el.value = '0.00';
                else if (el.tagName === 'SELECT') el.selectedIndex = 0;
                else el.value = '';
            });

            rowIndex++;
            bodyDetail.appendChild(newRow);
            hitungTotal();
        });

        // Hapus baris item
        bodyDetail.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-hapus-baris');
            if (!btn) return;
            const rows = bodyDetail.querySelectorAll('.item-row');
            if (rows.length <= 1) {
                alert('Minimal harus ada 1 item pemesanan.');
                return;
            }
            btn.closest('.item-row').remove();
            hitungTotal();
        });

        hitungTotal();
    });
    </script>
</body>

</html>