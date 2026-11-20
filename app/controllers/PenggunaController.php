<?php

require_once __DIR__ . '/../models/PenggunaModel.php';

class PenggunaController {
    private PenggunaModel $penggunaModel;

    public function __construct($pdo) {
        $this->penggunaModel = new PenggunaModel($pdo);
    }

    public function index(): array {
        return $this->penggunaModel->getAll();
    }

    public function edit(int $id): ?array {
        return $this->penggunaModel->findById($id);
    }

    public function store(array $post): array {
        $validationError = $this->validate($post, true);
        if ($validationError !== null) {
            return ['success' => false, 'message' => $validationError];
        }

        $data = $this->sanitizePayload($post);

        if ($this->penggunaModel->existsByUsernameOrEmail($data['nama_pengguna'], $data['email'])) {
            return ['success' => false, 'message' => 'Nama pengguna atau email sudah terdaftar.'];
        }

        $saved = $this->penggunaModel->create($data);

        return $saved
            ? ['success' => true, 'message' => 'Pengguna baru berhasil ditambahkan!']
            : ['success' => false, 'message' => 'Gagal menambahkan data pengguna.'];
    }

    public function update(int $id, array $post): array {
        $existing = $this->penggunaModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Data pengguna tidak ditemukan.'];
        }

        $validationError = $this->validate($post, false);
        if ($validationError !== null) {
            return ['success' => false, 'message' => $validationError];
        }

        $data = $this->sanitizePayload($post);

        if ($this->penggunaModel->existsByUsernameOrEmail($data['nama_pengguna'], $data['email'], $id)) {
            return ['success' => false, 'message' => 'Nama pengguna atau email sudah digunakan pengguna lain.'];
        }

        $updated = $this->penggunaModel->update($id, $data);

        return $updated
            ? ['success' => true, 'message' => 'Data pengguna berhasil diperbarui!']
            : ['success' => false, 'message' => 'Gagal memperbarui data pengguna.'];
    }

    public function destroy(int $id, ?int $currentUserId = null): array {
        if ($currentUserId !== null && $id === $currentUserId) {
            return ['success' => false, 'message' => 'Anda tidak dapat menghapus akun yang sedang digunakan.'];
        }

        $existing = $this->penggunaModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Data pengguna tidak ditemukan.'];
        }

        $deleted = $this->penggunaModel->delete($id);

        return $deleted
            ? ['success' => true, 'message' => 'Pengguna berhasil dihapus!']
            : ['success' => false, 'message' => 'Gagal menghapus data pengguna.'];
    }

    private function validate(array $post, bool $isCreate): ?string {
        $namaLengkap  = trim($post['nama_lengkap'] ?? '');
        $email        = trim($post['email'] ?? '');
        $noTelepon    = trim($post['no_telepon'] ?? '');
        $namaPengguna = trim($post['nama_pengguna'] ?? '');
        $kataSandi    = (string) ($post['kata_sandi'] ?? '');
        $role         = $post['role'] ?? 'Pegawai';
        $status       = $post['status'] ?? 'Aktif';

        if ($namaLengkap === '' || mb_strlen($namaLengkap) > 100) {
            return 'Nama lengkap wajib diisi (maksimal 100 karakter).';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
            return 'Email wajib diisi dengan format yang valid.';
        }

        if ($noTelepon === '' || mb_strlen($noTelepon) > 20) {
            return 'No. telepon wajib diisi (maksimal 20 karakter).';
        }

        if ($namaPengguna === '' || mb_strlen($namaPengguna) > 50) {
            return 'Nama pengguna wajib diisi (maksimal 50 karakter).';
        }

        if ($isCreate && strlen($kataSandi) < 6) {
            return 'Kata sandi wajib diisi minimal 6 karakter.';
        }

        if (!$isCreate && $kataSandi !== '' && strlen($kataSandi) < 6) {
            return 'Kata sandi baru minimal 6 karakter.';
        }

        if (!in_array($role, ['Admin', 'Pegawai'], true)) {
            return 'Role pengguna tidak valid.';
        }

        if (!in_array($status, ['Aktif', 'Nonaktif'], true)) {
            return 'Status pengguna tidak valid.';
        }

        return null;
    }

    private function sanitizePayload(array $post): array {
        $payload = [
            'nama_lengkap'  => trim($post['nama_lengkap']),
            'email'         => trim($post['email']),
            'no_telepon'    => trim($post['no_telepon']),
            'nama_pengguna' => trim($post['nama_pengguna']),
            'role'          => $post['role'] ?? 'Pegawai',
            'status'        => $post['status'] ?? 'Aktif',
        ];

        $kataSandi = (string) ($post['kata_sandi'] ?? '');
        if ($kataSandi !== '') {
            $payload['kata_sandi'] = $kataSandi;
        }

        return $payload;
    }
}