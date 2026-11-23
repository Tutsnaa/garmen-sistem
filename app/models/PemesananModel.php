<?php
// app/models/PemesananModel.php

class PemesananModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Cari pesanan pelanggan berdasarkan kode_pemesanan (wajib)
     * dan opsional pencocokan no_telepon member agar lebih aman
     */
    public function cariStatusPelanggan(string $kodePemesanan, string$noTelepon = ''): ?array
    {
        $kodeClean = strtoupper(trim($kodePemesanan));
        $telpClean = preg_replace('/[^0-9+]/', '', trim($noTelepon));

        if ($kodeClean === '') {
            return null;
        }

        $sql = "SELECT p.id,
                       p.kode_pemesanan,
                       p.tanggal_pesan,
                       p.tanggal_deadline,
                       p.total_jumlah_item,
                       p.total_harga_keseluruhan,
                       p.jenis_pembayaran,
                       p.status_pemesanan,
                       m.nama_lengkap AS nama_member,
                       m.nama_pic,
                       m.no_telepon
                FROM pemesanan p
                INNER JOIN member m ON p.id_member = m.id
                WHERE UPPER(p.kode_pemesanan) = :kode";

        $params = [':kode' =>$kodeClean];

        if ($telpClean !== '') {$sql .= " AND REPLACE(REPLACE(REPLACE(TRIM(m.no_telepon), ' ', ''), '-', ''), '+', '') = :telp";
            $params[':telp'] = ltrim($telpClean, '+');
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $pesanan =$stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pesanan) {
            return null;
        }

        $idPemesanan = (int)$pesanan['id'];

        $details =$this->getDetailsByPemesananId($idPemesanan);$pembayarans = $this->getPembayaranByPemesananId($idPemesanan);

        $totalHarga = (float) ($pesanan['total_harga_keseluruhan'] ?? 0);$sudahBayar = 0.0;
        foreach ($pembayarans as$pb) {
            $sudahBayar += (float) ($pb['jumlah_bayar'] ?? 0);
        }

        $pesanan['details'] =$details;
        $pesanan['pembayarans'] =$pembayarans;
        $pesanan['total_sudah_bayar'] =$sudahBayar;
        $pesanan['sisa_bayar'] = max(0.0, $totalHarga -$sudahBayar);

        return $pesanan;
    }

/**
     * Ambil 1 data pemesanan lengkap dengan info member
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT p.*,
                       m.nama_lengkap AS nama_member,
                       m.nama_pic,
                       m.no_telepon,
                       m.email,
                       m.alamat
                FROM pemesanan p
                LEFT JOIN member m ON p.id_member = m.id
                WHERE p.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Ambil daftar detail_pemesanan beserta nama & foto model pakaian
     */
    public function getDetailsByPemesananId(int $idPemesanan): array
    {
        $sql = "SELECT dp.*,
                       mp.nama_model,
                       mp.foto_sampel,
                       mp.catatan AS catatan_model
                FROM detail_pemesanan dp
                LEFT JOIN model_pakaian mp ON dp.id_model_pakaian = mp.id
                WHERE dp.id_pemesanan = :id_pemesanan
                ORDER BY dp.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_pemesanan' => $idPemesanan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil riwayat pembayaran (DP / Pelunasan) per pemesanan
     */
    public function getPembayaranByPemesananId(int $idPemesanan): array
    {
        $sql = "SELECT *
                FROM pembayaran
                WHERE id_pemesanan = :id_pemesanan
                ORDER BY tanggal_pembayaran ASC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_pemesanan' => $idPemesanan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Catat pembayaran baru (DP atau Pelunasan)
     */
    public function tambahPembayaran(int $idPemesanan, array $data): bool
    {
        $pemesanan = $this->getById($idPemesanan);
        if (!$pemesanan) {
            throw new Exception('Data pemesanan tidak ditemukan.');
        }

        $jumlahBayar = (float) ($data['jumlah_bayar'] ?? 0);
        if ($jumlahBayar <= 0) {
            throw new Exception('Jumlah bayar harus lebih dari 0.');
        }

        $sql = "INSERT INTO pembayaran (
                    id_pemesanan,
                    tanggal_pembayaran,
                    jumlah_bayar,
                    jenis_pembayaran,
                    tipe_pembayaran,
                    bukti_pembayaran,
                    keterangan
                ) VALUES (
                    :id_pemesanan,
                    :tanggal_pembayaran,
                    :jumlah_bayar,
                    :jenis_pembayaran,
                    :tipe_pembayaran,
                    :bukti_pembayaran,
                    :keterangan
                )";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_pemesanan'       => $idPemesanan,
            ':tanggal_pembayaran' => $data['tanggal_pembayaran'] ?: date('Y-m-d'),
            ':jumlah_bayar'       => $jumlahBayar,
            ':jenis_pembayaran'   => trim((string) ($data['jenis_pembayaran'] ?? '')),
            ':tipe_pembayaran'    => $data['tipe_pembayaran'] ?? 'Pelunasan',
            ':bukti_pembayaran'  => $data['bukti_pembayaran'] ?? null,
            ':keterangan'         => !empty($data['keterangan']) ? trim((string) $data['keterangan']) : null,
        ]);
    }

    /**
     * Update status pemesanan
     */
    public function updateStatus(int $idPemesanan, string $status): bool
    {
        $allowed = ['Pending', 'Sedang Dijahit', 'Selesai', 'Dibatalkan'];
        if (!in_array($status, $allowed, true)) {
            throw new Exception('Status pemesanan tidak valid.');
        }

        $sql = "UPDATE pemesanan
                SET status_pemesanan = :status
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $idPemesanan,
        ]);
    }

    public function getMembers(): array
    {
        $sql = "SELECT id, nama_lengkap, nama_pic, no_telepon, status
                FROM member
                WHERE status = 'Aktif' OR status IS NULL
                ORDER BY nama_lengkap ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModelPakaians(): array
    {
        $sql = "SELECT id, nama_model, id_member, ukuran, harga_dasar, catatan, foto_sampel
                FROM model_pakaian
                ORDER BY nama_model ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   /**
     * Generate kode pemesanan acak dan unik, contoh: ORD-20261123-7K9M2X
     */
    public function generateKodePemesanan(string $prefix = 'ORD', int $randomLength = 6): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa karakter ambigu seperti 0/O dan 1/I
        $maxIndex = strlen($chars) - 1;
        $datePart = date('Ymd');

        $checkSql = "SELECT COUNT(*) FROM pemesanan WHERE kode_pemesanan = :kode LIMIT 1";
        $checkStmt = $this->db->prepare($checkSql);

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $randomPart = '';
            for ($i = 0; $i < $randomLength; $i++) {
                $randomPart .= $chars[random_int(0, $maxIndex)];
            }

            $kode = sprintf('%s-%s-%s', $prefix, $datePart, $randomPart);

            $checkStmt->execute([':kode' => $kode]);
            if ((int) $checkStmt->fetchColumn() === 0) {
                return $kode;
            }
        }

        // Fallback jika terjadi tabrakan berulang
        return sprintf('%s-%s-%s', $prefix, $datePart, strtoupper(bin2hex(random_bytes(3))));
    }

    public function isKodeExists(string $kode): bool
    {
        $sql = "SELECT COUNT(*) FROM pemesanan WHERE kode_pemesanan = :kode LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':kode' => trim($kode)]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getAll(): array
    {
        $sql = "SELECT p.*,
                       m.nama_lengkap AS nama_member,
                       m.nama_pic,
                       m.no_telepon,
                       COALESCE(SUM(pb.jumlah_bayar), 0) AS total_sudah_bayar
                FROM pemesanan p
                LEFT JOIN member m ON p.id_member = m.id
                LEFT JOIN pembayaran pb ON p.id = pb.id_pemesanan
                GROUP BY p.id
                ORDER BY p.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createWithDetails(array $header, array $items, ?array $pembayaran = null): int
    {
        $this->db->beginTransaction();

        try {
            if (empty($items)) {
                throw new Exception('Minimal harus ada 1 item detail pemesanan.');
            }

            $totalJumlahItem = 0;
            $totalHargaKeseluruhan = 0.0;
            $cleanItems = [];

            foreach ($items as $item) {
                $idModel = (int) ($item['id_model_pakaian'] ?? 0);
                $jumlah  = (int) ($item['jumlah'] ?? 0);
                $ukuran  = trim((string) ($item['ukuran'] ?? ''));
                $harga   = (float) ($item['harga_satuan'] ?? 0);
                $catatan = trim((string) ($item['catatan_item'] ?? ''));

                if ($idModel <= 0 || $jumlah <= 0) {
                    continue;
                }

                $subtotal = $jumlah * $harga;
                $totalJumlahItem += $jumlah;
                $totalHargaKeseluruhan += $subtotal;

                $cleanItems[] = [
                    'id_model_pakaian' => $idModel,
                    'jumlah'           => $jumlah,
                    'ukuran'           => $ukuran,
                    'harga_satuan'     => $harga,
                    'subtotal_harga'   => $subtotal,
                    'catatan_item'     => $catatan !== '' ? $catatan : null,
                ];
            }

            if (empty($cleanItems)) {
                throw new Exception('Data item pemesanan tidak valid.');
            }

            $sqlPemesanan = "INSERT INTO pemesanan (
                                id_member,
                                kode_pemesanan,
                                tanggal_pesan,
                                tanggal_deadline,
                                total_jumlah_item,
                                total_harga_keseluruhan,
                                jenis_pembayaran,
                                status_pemesanan
                            ) VALUES (
                                :id_member,
                                :kode_pemesanan,
                                :tanggal_pesan,
                                :tanggal_deadline,
                                :total_jumlah_item,
                                :total_harga_keseluruhan,
                                :jenis_pembayaran,
                                :status_pemesanan
                            )";
            $stmtPemesanan = $this->db->prepare($sqlPemesanan);
            $stmtPemesanan->execute([
                ':id_member'               => (int) $header['id_member'],
                ':kode_pemesanan'          => trim((string) $header['kode_pemesanan']),
                ':tanggal_pesan'           => $header['tanggal_pesan'],
                ':tanggal_deadline'        => $header['tanggal_deadline'],
                ':total_jumlah_item'       => $totalJumlahItem,
                ':total_harga_keseluruhan' => $totalHargaKeseluruhan,
                ':jenis_pembayaran'        => trim((string) $header['jenis_pembayaran']),
                ':status_pemesanan'        => $header['status_pemesanan'] ?? 'Pending',
            ]);

            $idPemesanan = (int) $this->db->lastInsertId();

            $sqlDetail = "INSERT INTO detail_pemesanan (
                              id_pemesanan,
                              id_model_pakaian,
                              jumlah,
                              ukuran,
                              harga_satuan,
                              subtotal_harga,
                              catatan_item
                          ) VALUES (
                              :id_pemesanan,
                              :id_model_pakaian,
                              :jumlah,
                              :ukuran,
                              :harga_satuan,
                              :subtotal_harga,
                              :catatan_item
                          )";
            $stmtDetail = $this->db->prepare($sqlDetail);

            foreach ($cleanItems as $row) {
                $stmtDetail->execute([
                    ':id_pemesanan'     => $idPemesanan,
                    ':id_model_pakaian' => $row['id_model_pakaian'],
                    ':jumlah'           => $row['jumlah'],
                    ':ukuran'           => $row['ukuran'],
                    ':harga_satuan'     => $row['harga_satuan'],
                    ':subtotal_harga'   => $row['subtotal_harga'],
                    ':catatan_item'     => $row['catatan_item'],
                ]);
            }

            if ($pembayaran !== null && (float) ($pembayaran['jumlah_bayar'] ?? 0) > 0) {
                $sqlBayar = "INSERT INTO pembayaran (
                                 id_pemesanan,
                                 tanggal_pembayaran,
                                 jumlah_bayar,
                                 jenis_pembayaran,
                                 tipe_pembayaran,
                                 bukti_pembayaran,
                                 keterangan
                             ) VALUES (
                                 :id_pemesanan,
                                 :tanggal_pembayaran,
                                 :jumlah_bayar,
                                 :jenis_pembayaran,
                                 :tipe_pembayaran,
                                 :bukti_pembayaran,
                                 :keterangan
                             )";
                $stmtBayar = $this->db->prepare($sqlBayar);
                $stmtBayar->execute([
                    ':id_pemesanan'       => $idPemesanan,
                    ':tanggal_pembayaran' => $pembayaran['tanggal_pembayaran'] ?: date('Y-m-d'),
                    ':jumlah_bayar'       => (float) $pembayaran['jumlah_bayar'],
                    ':jenis_pembayaran'   => trim((string) ($pembayaran['jenis_pembayaran'] ?: $header['jenis_pembayaran'])),
                    ':tipe_pembayaran'    => $pembayaran['tipe_pembayaran'] ?? 'DP',
                    ':bukti_pembayaran'  => $pembayaran['bukti_pembayaran'] ?? null,
                    ':keterangan'         => !empty($pembayaran['keterangan']) ? trim((string) $pembayaran['keterangan']) : null,
                ]);
            }

            $this->db->commit();
            return $idPemesanan;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}