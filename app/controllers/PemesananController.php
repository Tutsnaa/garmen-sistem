<?php
// app/controllers/PemesananController.php

require_once __DIR__ . '/../models/PemesananModel.php';

class PemesananController
{
    private PemesananModel $pemesananModel;

    public function __construct(PDO $db)
    {
        $this->pemesananModel = new PemesananModel($db);
    }


    /**
     * Halaman pencarian status pesanan pelanggan:
     * index.php?action=cek_status (atau index.php?action=pemesanan_cek_status)
     */
    public function cekStatus(): void
    {
        $kodeInput = strtoupper(trim((string) ($_GET['kode'] ?? $_POST['kode_pemesanan'] ?? '')));
        $telpInput = trim((string) ($_GET['no_telepon'] ?? $_POST['no_telepon'] ?? ''));

        $hasilPencarian = null;
        $sudahCari = ($kodeInput !== '');
        $errorPencarian = null;

        if ($sudahCari) {
            $hasilPencarian = $this->pemesananModel->cariStatusPelanggan($kodeInput, $telpInput);
            if (!$hasilPencarian) {
                $errorPencarian = 'Pesanan tidak ditemukan. Periksa kembali kode pemesanan atau no. telepon Anda.';
            }
        }

        require_once __DIR__ . '/../views/pemesanan/cek_status.php';
    }
    /**
     * Halaman detail pemesanan + riwayat pembayaran + form pelunasan:
     * index.php?action=pemesanan_show&id=...
     */
    public function show(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['flash_error'] = 'ID pemesanan tidak valid.';
            header('Location: index.php?action=pemesanan_index');
            exit;
        }

        $pemesanan = $this->pemesananModel->getById($id);
        if (!$pemesanan) {
            $_SESSION['flash_error'] = 'Data pemesanan tidak ditemukan.';
            header('Location: index.php?action=pemesanan_index');
            exit;
        }

        $details     = $this->pemesananModel->getDetailsByPemesananId($id);
        $pembayarans = $this->pemesananModel->getPembayaranByPemesananId($id);

        $totalHarga = (float) ($pemesanan['total_harga_keseluruhan'] ?? 0);
        $sudahBayar = 0.0;
        foreach ($pembayarans as $pb) {
            $sudahBayar += (float) ($pb['jumlah_bayar'] ?? 0);
        }
        $sisaBayar = max(0, $totalHarga - $sudahBayar);

        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once __DIR__ . '/../views/pemesanan/show.php';
    }

    /**
     * Simpan pembayaran baru (Pelunasan / DP):
     * index.php?action=pemesanan_bayar
     */
    public function bayar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=pemesanan_index');
            exit;
        }

        $idPemesanan = (int) ($_POST['id_pemesanan'] ?? 0);
        if ($idPemesanan <= 0) {
            $_SESSION['flash_error'] = 'ID pemesanan tidak valid.';
            header('Location: index.php?action=pemesanan_index');
            exit;
        }

        try {
            $jumlahBayar     = (float) ($_POST['jumlah_bayar'] ?? 0);
            $jenisPembayaran = trim((string) ($_POST['jenis_pembayaran'] ?? ''));
            $tipePembayaran  = in_array($_POST['tipe_pembayaran'] ?? '', ['DP', 'Pelunasan'], true)
                ? $_POST['tipe_pembayaran']
                : 'Pelunasan';
            $tanggalBayar    = trim((string) ($_POST['tanggal_pembayaran'] ?? date('Y-m-d')));

            if ($jumlahBayar <= 0) {
                throw new Exception('Jumlah bayar harus lebih besar dari 0.');
            }
            if ($jenisPembayaran === '') {
                throw new Exception('Jenis pembayaran wajib diisi.');
            }

            $buktiPath = $this->handleUploadBukti($_FILES['bukti_pembayaran'] ?? null);

            $this->pemesananModel->tambahPembayaran($idPemesanan, [
                'tanggal_pembayaran' => $tanggalBayar ?: date('Y-m-d'),
                'jumlah_bayar'       => $jumlahBayar,
                'jenis_pembayaran'   => $jenisPembayaran,
                'tipe_pembayaran'    => $tipePembayaran,
                'bukti_pembayaran'  => $buktiPath,
                'keterangan'         => $_POST['keterangan'] ?? null,
            ]);

            $_SESSION['flash_success'] = 'Pembayaran (' . $tipePembayaran . ') berhasil dicatat!';
        } catch (Throwable $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: index.php?action=pemesanan_show&id=' . $idPemesanan);
        exit;
    }

    /**
     * Update status pemesanan:
     * index.php?action=pemesanan_update_status
     */
    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=pemesanan_index');
            exit;
        }

        $idPemesanan = (int) ($_POST['id_pemesanan'] ?? 0);
        $status      = trim((string) ($_POST['status_pemesanan'] ?? 'Pending'));

        if ($idPemesanan <= 0) {
            $_SESSION['flash_error'] = 'ID pemesanan tidak valid.';
            header('Location: index.php?action=pemesanan_index');
            exit;
        }

        try {
            $this->pemesananModel->updateStatus($idPemesanan, $status);
            $_SESSION['flash_success'] = 'Status pemesanan berhasil diperbarui menjadi "' . $status . '".';
        } catch (Throwable $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: index.php?action=pemesanan_show&id=' . $idPemesanan);
        exit;
    }

    public function index(): void
    {
        $pemesanans = $this->pemesananModel->getAll();
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once __DIR__ . '/../views/pemesanan/index.php';
    }

    public function create(): void
    {
        $members      = $this->pemesananModel->getMembers();
        $modelPakaians = $this->pemesananModel->getModelPakaians();
        $kodeOtomatis = $this->pemesananModel->generateKodePemesanan();

        $selectedMemberId = $_SESSION['new_member_id'] ?? null;
        $selectedModelId  = $_SESSION['new_model_id'] ?? null;
        $flashError       = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['new_member_id'], $_SESSION['new_model_id'], $_SESSION['flash_error']);

        require_once __DIR__ . '/../views/pemesanan/create.php';
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=pemesanan_create');
            exit;
        }

        $buktiPath = null;

        try {
            $idMember        = (int) ($_POST['id_member'] ?? 0);
            $kodePemesanan   = strtoupper(trim((string) ($_POST['kode_pemesanan'] ?? '')));
            $tanggalPesan    = trim((string) ($_POST['tanggal_pesan'] ?? ''));
            $tanggalDeadline = trim((string) ($_POST['tanggal_deadline'] ?? ''));
            $jenisPembayaran = trim((string) ($_POST['jenis_pembayaran'] ?? ''));
            $statusPemesanan = trim((string) ($_POST['status_pemesanan'] ?? 'Pending'));
            $items           = $_POST['items'] ?? [];

            if ($idMember <= 0) {
                throw new Exception('Silakan pilih data member terlebih dahulu.');
            }
            if ($tanggalPesan === '' || $tanggalDeadline === '') {
                throw new Exception('Tanggal pesan dan tanggal deadline wajib diisi.');
            }
            if (strtotime($tanggalDeadline) < strtotime($tanggalPesan)) {
                throw new Exception('Tanggal deadline tidak boleh lebih awal dari tanggal pesan.');
            }
            if ($jenisPembayaran === '') {
                throw new Exception('Jenis pembayaran wajib diisi.');
            }
            if (!is_array($items) || empty($items)) {
                throw new Exception('Minimal harus menambahkan 1 model pakaian pada detail pemesanan.');
            }

            // Jika kode dikosongkan, buatkan kode acak otomatis; jika diisi, pastikan belum dipakai
            if ($kodePemesanan === '') {
                $kodePemesanan = $this->pemesananModel->generateKodePemesanan();
            } elseif ($this->pemesananModel->isKodeExists($kodePemesanan)) {
                throw new Exception('Kode pemesanan "' . $kodePemesanan . '" sudah digunakan.');
            }

            $buktiPath = $this->handleUploadBukti($_FILES['bukti_pembayaran'] ?? null);

            $jumlahBayar = (float) ($_POST['jumlah_bayar'] ?? 0);
            if ($jumlahBayar < 0) {
                throw new Exception('Jumlah bayar tidak boleh negatif.');
            }

            $tipeBayar = in_array($_POST['tipe_pembayaran'] ?? '', ['DP', 'Pelunasan'], true)
                ? $_POST['tipe_pembayaran']
                : 'DP';

            $pembayaranData = null;
            if ($jumlahBayar > 0) {
                $pembayaranData = [
                    'tanggal_pembayaran' => !empty($_POST['tanggal_pembayaran']) ? $_POST['tanggal_pembayaran'] : date('Y-m-d'),
                    'jumlah_bayar'       => $jumlahBayar,
                    'jenis_pembayaran'   => $jenisPembayaran,
                    'tipe_pembayaran'    => $tipeBayar,
                    'bukti_pembayaran'  => $buktiPath,
                    'keterangan'         => $_POST['keterangan_pembayaran'] ?? null,
                ];
            }

            $headerData = [
                'id_member'        => $idMember,
                'kode_pemesanan'   => $kodePemesanan,
                'tanggal_pesan'    => $tanggalPesan,
                'tanggal_deadline' => $tanggalDeadline,
                'jenis_pembayaran' => $jenisPembayaran,
                'status_pemesanan' => $statusPemesanan,
            ];

            $this->pemesananModel->createWithDetails($headerData, $items, $pembayaranData);

            $_SESSION['flash_success'] = 'Pemesanan (' . $kodePemesanan . ') berhasil disimpan!';
            header('Location: index.php?action=pemesanan_index');
            exit;
        } catch (Throwable $e) {
            // Hapus file bukti jika sudah terupload namun penyimpanan database gagal
            if (!empty($buktiPath)) {
                $fullFile = __DIR__ . '/../../public/' . ltrim($buktiPath, '/');
                if (is_file($fullFile)) {
                    @unlink($fullFile);
                }
            }

            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: index.php?action=pemesanan_create');
            exit;
        }
    }

    private function handleUploadBukti(?array $file): ?string
    {
        if (empty($file) || empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new Exception('Terjadi kesalahan saat mengunggah bukti pembayaran.');
        }

        $maxSize = 3 * 1024 * 1024; // 3 MB
        if (($file['size'] ?? 0) > $maxSize) {
            throw new Exception('Ukuran bukti pembayaran maksimal 3 MB.');
        }

        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        if (!in_array($ext, $allowedExt, true)) {
            throw new Exception('Format bukti pembayaran harus JPG, JPEG, PNG, WEBP, atau PDF.');
        }

        $uploadDir = __DIR__ . '/../../public/uploads/bukti_pembayaran/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new Exception('Gagal membuat folder upload bukti pembayaran.');
        }

        $fileName = 'bukti_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            throw new Exception('Gagal menyimpan file bukti pembayaran.');
        }

        return 'uploads/bukti_pembayaran/' . $fileName;
    }
}