<?php
// app/models/ModelPakaian.php

class ModelPakaian
{
    private PDO $db;
    private string $table = 'model_pakaian';
    private string $memberTable = 'member'; // sesuaikan jika nama tabel di DB berbeda

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Ambil semua data model pakaian beserta informasi member
     */
    public function getAll(): array
    {
        $sql = "SELECT 
                    mp.id,
                    mp.id_member,
                    mp.nama_model,
                    mp.catatan,
                    mp.ukuran,
                    mp.foto_sampel,
                    mp.harga_dasar,
                    mp.created_at,
                    mp.updated_at,
                    m.nama_lengkap AS nama_member,
                    m.nama_pic
                FROM {$this->table} mp
                LEFT JOIN {$this->memberTable} m ON mp.id_member = m.id
                ORDER BY mp.id DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil satu data model pakaian berdasarkan ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Tambah data model pakaian baru
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (
                    id_member,
                    nama_model,
                    catatan,
                    ukuran,
                    foto_sampel,
                    harga_dasar
                ) VALUES (
                    :id_member,
                    :nama_model,
                    :catatan,
                    :ukuran,
                    :foto_sampel,
                    :harga_dasar
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_member'   => !empty($data['id_member']) ? (int) $data['id_member'] : null,
            ':nama_model'  => trim($data['nama_model']),
            ':catatan'     => $data['catatan'] !== '' ? trim($data['catatan']) : null,
            ':ukuran'      => $data['ukuran'] !== '' ? trim($data['ukuran']) : null,
            ':foto_sampel' => $data['foto_sampel'] ?? null,
            ':harga_dasar' => isset($data['harga_dasar']) && $data['harga_dasar'] !== ''
                ? (float) $data['harga_dasar']
                : 0.00,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update data model pakaian berdasarkan ID
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} SET
                    id_member   = :id_member,
                    nama_model  = :nama_model,
                    catatan     = :catatan,
                    ukuran      = :ukuran,
                    foto_sampel = :foto_sampel,
                    harga_dasar = :harga_dasar,
                    updated_at  = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'          => $id,
            ':id_member'   => !empty($data['id_member']) ? (int) $data['id_member'] : null,
            ':nama_model'  => trim($data['nama_model']),
            ':catatan'     => $data['catatan'] !== '' ? trim($data['catatan']) : null,
            ':ukuran'      => $data['ukuran'] !== '' ? trim($data['ukuran']) : null,
            ':foto_sampel' => $data['foto_sampel'] ?? null,
            ':harga_dasar' => isset($data['harga_dasar']) && $data['harga_dasar'] !== ''
                ? (float) $data['harga_dasar']
                : 0.00,
        ]);
    }

    /**
     * Hapus data model pakaian berdasarkan ID
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Ambil daftar member untuk dropdown pilihan id_member di form
     */
    public function getMemberOptions(): array
    {
        $sql = "SELECT id, nama_lengkap, nama_pic, status
                FROM {$this->memberTable}
                ORDER BY nama_lengkap ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}