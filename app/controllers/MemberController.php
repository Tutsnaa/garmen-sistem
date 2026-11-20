<?php

require_once __DIR__ . '/../models/MemberModel.php';

class MemberController
{
    private MemberModel $memberModel;

    public function __construct(PDO $pdo)
    {
        $this->memberModel = new MemberModel($pdo);
    }

    public function index(): array
    {
        return $this->memberModel->getAll();
    }

    public function edit(int $id): ?array
    {
        return $this->memberModel->findById($id);
    }

    public function store(array $post): array
    {
        $validationError = $this->validate($post);
        if ($validationError !== null) {
            return ['success' => false, 'message' => $validationError];
        }

        $data = $this->sanitizePayload($post);
        $saved = $this->memberModel->create($data);

        return $saved
            ? ['success' => true, 'message' => 'Member baru berhasil ditambahkan!']
            : ['success' => false, 'message' => 'Gagal menambahkan data member.'];
    }

    public function update(int $id, array $post): array
    {
        $existing = $this->memberModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Data member tidak ditemukan.'];
        }

        $validationError = $this->validate($post);
        if ($validationError !== null) {
            return ['success' => false, 'message' => $validationError];
        }

        $data = $this->sanitizePayload($post);
        $updated = $this->memberModel->update($id, $data);

        return $updated
            ? ['success' => true, 'message' => 'Data member berhasil diperbarui!']
            : ['success' => false, 'message' => 'Gagal memperbarui data member.'];
    }

    public function destroy(int $id): array
    {
        $existing = $this->memberModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Data member tidak ditemukan.'];
        }

        $deleted = $this->memberModel->delete($id);

        return $deleted
            ? ['success' => true, 'message' => 'Member berhasil dihapus!']
            : ['success' => false, 'message' => 'Gagal menghapus data member.'];
    }

    private function validate(array $post): ?string
    {
        $namaLengkap = trim($post['nama_lengkap'] ?? '');
        $noTelepon   = trim($post['no_telepon'] ?? '');
        $alamat      = trim($post['alamat'] ?? '');
        $email       = trim($post['email'] ?? '');
        $status      = $post['status'] ?? 'Aktif';

        if ($namaLengkap === '' || mb_strlen($namaLengkap) > 100) {
            return 'Nama lengkap wajib diisi (maksimal 100 karakter).';
        }

        if ($noTelepon === '' || mb_strlen($noTelepon) > 20) {
            return 'No. telepon wajib diisi (maksimal 20 karakter).';
        }

        if ($alamat === '') {
            return 'Alamat wajib diisi.';
        }

        if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100)) {
            return 'Format email tidak valid atau terlalu panjang.';
        }

        if (!in_array($status, ['Aktif', 'Nonaktif'], true)) {
            return 'Status member tidak valid.';
        }

        return null;
    }

    private function sanitizePayload(array $post): array
    {
        $namaPic    = trim($post['nama_pic'] ?? '');
        $email      = trim($post['email'] ?? '');
        $keterangan = trim($post['keterangan'] ?? '');

        return [
            'nama_lengkap' => trim($post['nama_lengkap']),
            'nama_pic'     => $namaPic !== '' ? $namaPic : null,
            'no_telepon'   => trim($post['no_telepon']),
            'alamat'       => trim($post['alamat']),
            'email'        => $email !== '' ? $email : null,
            'keterangan'   => $keterangan !== '' ? $keterangan : null,
            'status'       => $post['status'] ?? 'Aktif',
        ];
    }
}