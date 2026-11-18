<?php
// views/auth/login.php
$error = $error ?? '';
$success = $success ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Garmen & Tailoring</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f4f7f6;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
    }

    .btn-primary-custom {
        background-color: #2b5748;
        border-color: #2b5748;
        color: #fff;
    }

    .btn-primary-custom:hover {
        background-color: #204135;
        border-color: #204135;
        color: #fff;
    }

    .text-custom {
        color: #2b5748;
    }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card p-4 p-md-5 bg-white">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-scissors fa-3x text-custom"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Garmen Sistem</h4>
                        <p class="text-muted small">Silakan masuk ke akun Anda</p>
                    </div>

                    <!-- Notifikasi Pesan Error / Success -->
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <?= htmlspecialchars($error); ?>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="fa-solid fa-circle-check me-1"></i> <?= htmlspecialchars($success); ?>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Form Login mengarah ke index.php dengan action login -->
                    <form action="index.php?action=login_process" method="POST">
                        <div class="mb-3">
                            <label for="nama_pengguna" class="form-label small fw-semibold text-secondary">Nama Pengguna
                                / Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="fa-solid fa-user text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-start-0 ps-0" id="nama_pengguna"
                                    name="nama_pengguna" placeholder="Masukkan username atau email" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="kata_sandi" class="form-label small fw-semibold text-secondary">Kata
                                Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="fa-solid fa-lock text-muted"></i></span>
                                <input type="password" class="form-control bg-light border-start-0 ps-0" id="kata_sandi"
                                    name="kata_sandi" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-custom py-2 fw-semibold">Masuk</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted small">&copy; 2026 Sistem Garmen & Tailoring</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>