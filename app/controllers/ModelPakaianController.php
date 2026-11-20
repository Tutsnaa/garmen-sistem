<?php
// app/controllers/ModelPakaianController.php

require_once __DIR__ . '/../models/ModelPakaian.php';

class ModelPakaianController
{
    private ModelPakaian $model;
    private string $uploadDir;
    private string $uploadUrlPrefix;

    public function __construct(PDO $db)
    {
        $this->model = new ModelPakaian($db);

        // Folder penyimpanan fisik foto sampel di dalam public/uploads/model_pakaian
        $this->uploadDir = __DIR__ . '/../../public/uploads/model_pakaian';
        $this->uploadUrlPrefix = 'uploads/model_pakaian/';

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    /**
     * Menampilkan halaman daftar model pakaian
     */
    public function index(?string $pesan = null, string $tipe = 'success'): void
    {
        $models  = $this->model->getAll();
        $members = $this->model->getMemberOptions();

        require_once __DIR__ . '/../views/model_pakaian/index.php';
    }

    /**
     * Menyimpan data model pakaian baru (POST)
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=model_pakaian');
        }

        $namaModel  = trim($_POST['nama_model'] ?? '');
        $hargaDasar = $_POST['harga_dasar'] ?? '0';

        if ($namaModel === '') {
            $this->redirect('index.php?action=model_pakaian&open_create=1&pesan=' . urlencode('Nama model wajib diisi.') . '&tipe=danger');
        }

        if (!is_numeric($hargaDasar) || (float) $hargaDasar < 0) {
            $this->redirect('index.php?action=model_pakaian&open_create=1&pesan=' . urlencode('Harga dasar harus berupa angka valid (>= 0).') . '&tipe=danger');
        }

        $fotoSampel = null;
        if (!empty($_FILES['foto_sampel']['name'])) {
            $uploadResult = $this->handleUpload($_FILES['foto_sampel']);
            if (!$uploadResult['success']) {
                $this->redirect('index.php?action=model_pakaian&open_create=1&pesan=' . urlencode($uploadResult['error']) . '&tipe=danger');
            }
            $fotoSampel = $uploadResult['path'];
        }

        try {
            $this->model->create([
                'id_member'   => $_POST['id_member'] ?? null,
                'nama_model'  => $namaModel,
                'catatan'     => $_POST['catatan'] ?? '',
                'ukuran'      => $_POST['ukuran'] ?? '',
                'foto_sampel' => $fotoSampel,
                'harga_dasar' => (float) $hargaDasar,
            ]);

            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Data model pakaian berhasil ditambahkan.') . '&tipe=success');
        } catch (Throwable $e) {
            if ($fotoSampel) {
                $this->deletePhysicalFile($fotoSampel);
            }
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Gagal menyimpan data model pakaian.') . '&tipe=danger');
        }
    }

    /**
     * Memperbarui data model pakaian (POST)
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=model_pakaian');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('ID model pakaian tidak valid.') . '&tipe=danger');
        }

        $existing = $this->model->findById($id);
        if (!$existing) {
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Data model pakaian tidak ditemukan.') . '&tipe=danger');
        }

        $namaModel  = trim($_POST['nama_model'] ?? '');
        $hargaDasar = $_POST['harga_dasar'] ?? '0';

        if ($namaModel === '') {
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Nama model wajib diisi.') . '&tipe=danger');
        }

        if (!is_numeric($hargaDasar) || (float) $hargaDasar < 0) {
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Harga dasar harus berupa angka valid (>= 0).') . '&tipe=danger');
        }

        $fotoSampel = $existing['foto_sampel'];
        $newUploaded = null;

        // Jika centang hapus foto lama (opsional di form update)
        if (!empty($_POST['hapus_foto']) && $_POST['hapus_foto'] === '1') {
            if ($fotoSampel) {
                $this->deletePhysicalFile($fotoSampel);
            }
            $fotoSampel = null;
        }

        // Jika upload foto baru
        if (!empty($_FILES['foto_sampel']['name'])) {
            $uploadResult = $this->handleUpload($_FILES['foto_sampel']);
            if (!$uploadResult['success']) {
                $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode($uploadResult['error']) . '&tipe=danger');
            }
            $newUploaded = $uploadResult['path'];
        }

        try {
            $this->model->update($id, [
                'id_member'   => $_POST['id_member'] ?? null,
                'nama_model'  => $namaModel,
                'catatan'     => $_POST['catatan'] ?? '',
                'ukuran'      => $_POST['ukuran'] ?? '',
                'foto_sampel' => $newUploaded ?? $fotoSampel,
                'harga_dasar' => (float) $hargaDasar,
            ]);

            if ($newUploaded && $existing['foto_sampel']) {
                $this->deletePhysicalFile($existing['foto_sampel']);
            }

            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Data model pakaian berhasil diperbarui.') . '&tipe=success');
        } catch (Throwable $e) {
            if ($newUploaded) {
                $this->deletePhysicalFile($newUploaded);
            }
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Gagal memperbarui data model pakaian.') . '&tipe=danger');
        }
    }

    /**
     * Menghapus data model pakaian (GET)
     */
    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('ID model pakaian tidak valid.') . '&tipe=danger');
        }

        $existing = $this->model->findById($id);
        if (!$existing) {
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Data model pakaian tidak ditemukan.') . '&tipe=danger');
        }

        try {
            $this->model->delete($id);
            if (!empty($existing['foto_sampel'])) {
                $this->deletePhysicalFile($existing['foto_sampel']);
            }

            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Data model pakaian berhasil dihapus.') . '&tipe=success');
        } catch (Throwable $e) {
            $this->redirect('index.php?action=model_pakaian&pesan=' . urlencode('Gagal menghapus data model pakaian.') . '&tipe=danger');
        }
    }

    /**
     * Validasi dan simpan file gambar sampel
     */
    private function handleUpload(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Terjadi kesalahan saat mengunggah foto sampel.'];
        }

        $maxSize = 3 * 1024 * 1024; // 3 MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Ukuran foto sampel maksimal 3 MB.'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowedMimes[$mimeType])) {
            return ['success' => false, 'error' => 'Format foto harus JPG, PNG, atau WEBP.'];
        }

        $extension = $allowedMimes[$mimeType];
        $fileName  = 'model_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetPath = $this->uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'error' => 'Gagal memindahkan file foto ke folder tujuan.'];
        }

        return [
            'success' => true,
            'path'    => $this->uploadUrlPrefix . $fileName,
        ];
    }

    /**
     * Hapus file foto sampel dari direktori public jika ada
     */
    private function deletePhysicalFile(string $relativePath): void
    {
        $cleanPath = ltrim($relativePath, '/\\');
        $fullPath  = realpath(__DIR__ . '/../../public/' . $cleanPath);
        $baseDir   = realpath($this->uploadDir);

        if ($fullPath && $baseDir && strpos($fullPath, $baseDir) === 0 && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}