<?php

class MemberModel
{
    private PDO $pdo;
    private string $table = 'member';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        return $member ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (
                    nama_lengkap,
                    nama_pic,
                    no_telepon,
                    alamat,
                    email,
                    keterangan,
                    status
                ) VALUES (
                    :nama_lengkap,
                    :nama_pic,
                    :no_telepon,
                    :alamat,
                    :email,
                    :keterangan,
                    :status
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nama_lengkap' => $data['nama_lengkap'],
            ':nama_pic'     => $data['nama_pic'],
            ':no_telepon'   => $data['no_telepon'],
            ':alamat'       => $data['alamat'],
            ':email'        => $data['email'],
            ':keterangan'   => $data['keterangan'],
            ':status'       => $data['status'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} SET
                    nama_lengkap = :nama_lengkap,
                    nama_pic = :nama_pic,
                    no_telepon = :no_telepon,
                    alamat = :alamat,
                    email = :email,
                    keterangan = :keterangan,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nama_lengkap' => $data['nama_lengkap'],
            ':nama_pic'     => $data['nama_pic'],
            ':no_telepon'   => $data['no_telepon'],
            ':alamat'       => $data['alamat'],
            ':email'        => $data['email'],
            ':keterangan'   => $data['keterangan'],
            ':status'       => $data['status'],
            ':id'           => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}