<?php
// app/views/dashboard/index.php
// Pastikan session sudah aktif di router/index.php
$user = $_SESSION['user'] ?? [
    'nama_lengkap' => 'Pengguna',
    'role'         => 'Pegawai',
    'username'     => 'guest'
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Garmen & Tailoring</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2b5748;
        --primary-hover: #204135;
        --primary-soft: rgba(43, 87, 72, 0.1);
    }

    body {
        background-color: #f4f7f6;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar-custom {
        background-color: var(--primary-color);
    }

    .brand-icon-box {
        background-color: var(--primary-soft);
        color: var(--primary-color);
    }

    .card-stat {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
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

    .btn-outline-custom {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline-custom:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
    }
    </style>
</head>

<body>

    <!-- Navbar Atas -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php?action=dashboard">
                <i class="fa-solid fa-scissors me-2"></i> Garmen Sistem
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2">
                    <li class="nav-item">
                        <a href="index.php?action=member" class="nav-link text-white px-2">
                            <i class="fa-solid fa-users me-1"></i> Member
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?action=pengguna" class="nav-link text-white px-2">
                            <i class="fa-solid fa-user-gear me-1"></i> Pengguna
                        </a>
                    </li>
                    <li class="nav-item me-lg-2 text-white-50 small">
                        Halo, <span
                            class="text-white fw-semibold"><?= htmlspecialchars($user['nama_lengkap']); ?></span>
                        <span class="badge bg-light text-dark ms-1"><?= htmlspecialchars($user['role']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?action=logout" class="btn btn-danger btn-sm px-3">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama Dashboard -->
    <div class="container my-5">
        <!-- Banner Sambutan -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="p-4 p-md-5 bg-white rounded-4 shadow-sm border-start border-4"
                    style="border-color: var(--primary-color) !important;">
                    <h2 class="fw-bold text-dark mb-2">Selamat Datang di Panel Utama</h2>
                    <p class="text-muted mb-0">
                        Sistem Informasi Pengelolaan Pesanan Garmen & Tailoring. Pilih menu di bawah atau navigasi
                        sistem untuk mengelola data operasional toko.
                    </p>
                </div>
            </div>
        </div>

        <!-- Kartu Ringkasan / Menu Cepat -->
        <div class="row g-4">
            <!-- Card 1: Pemesanan -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-stat p-4 bg-white h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="brand-icon-box p-3 rounded-3 fs-4 me-3">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small text-uppercase fw-bold">Modul</h6>
                            <h5 class="fw-bold text-dark mb-0">Pemesanan</h5>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">
                        Kelola pesanan pakaian baru, detail item jahitan, pembayaran DP/pelunasan, dan status produksi.
                    </p>
                    <a href="index.php?action=pemesanan_index" class="btn btn-outline-custom btn-sm mt-auto">
                        Kelola Pemesanan <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card: Cek Status Pesanan -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-stat p-4 bg-white h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="brand-icon-box p-3 rounded-3 fs-4 me-3">
                            <i class="fa-solid fa-magnifying-glass-chart"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small text-uppercase fw-bold">Pelanggan</h6>
                            <h5 class="fw-bold text-dark mb-0">Cek Status</h5>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">
                        Lacak progres jahitan, estimasi deadline, dan sisa pembayaran pesanan menggunakan kode
                        pemesanan.
                    </p>
                    <a href="index.php?action=cek_status" class="btn btn-outline-custom btn-sm mt-auto" target="_blank">
                        Buka Halaman Cek <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2: Member / Pelanggan -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-stat p-4 bg-white h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="brand-icon-box p-3 rounded-3 fs-4 me-3">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small text-uppercase fw-bold">Modul</h6>
                            <h5 class="fw-bold text-dark mb-0">Data Member</h5>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Daftar pelanggan, PIC, kontak, alamat, dan status keaktifan
                        member.</p>
                    <a href="index.php?action=member" class="btn btn-custom btn-sm mt-auto">
                        Kelola Member <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3: Model Pakaian -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-stat p-4 bg-white h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="brand-icon-box p-3 rounded-3 fs-4 me-3">
                            <i class="fa-solid fa-shirt"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small text-uppercase fw-bold">Modul</h6>
                            <h5 class="fw-bold text-dark mb-0">Model Pakaian</h5>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Katalog jenis pakaian, referensi model, dan estimasi harga dasar
                        jahitan.</p>
                    <a href="index.php?action=model_pakaian" class="btn btn-custom btn-sm mt-auto">
                        Kelola Model <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card 4: Pengguna -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-stat p-4 bg-white h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="brand-icon-box p-3 rounded-3 fs-4 me-3">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small text-uppercase fw-bold">Modul</h6>
                            <h5 class="fw-bold text-dark mb-0">Pengguna</h5>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Kelola akun admin dan pegawai, hak akses role, serta status akun.
                    </p>
                    <a href="index.php?action=pengguna" class="btn btn-custom btn-sm mt-auto">
                        Kelola Pengguna <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted small">
        &copy; 2026 Sistem Informasi Garmen & Tailoring. All rights reserved.
    </footer>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>