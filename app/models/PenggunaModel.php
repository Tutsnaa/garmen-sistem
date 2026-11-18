<?php

class PenggunaModel {
    private $db;
    private $table = 'pengguna';

    public function __construct($pdo) {
        if (!$pdo instanceof PDO) {
            throw new Exception("Koneksi database (PDO) tidak valid pada PenggunaModel.");
        }
        $this->db = $pdo;
    }

    // Ambil semua data pengguna
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Cari pengguna berdasarkan ID
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

   // Cari pengguna berdasarkan username atau email (digunakan untuk login)
    public function findByUsernameOrEmail($usernameOrEmail) {
        $sql = "SELECT * FROM {$this->table} WHERE nama_pengguna = :username OR email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'username' => $usernameOrEmail,
            'email'    => $usernameOrEmail
        ]);
        return $stmt->fetch();
    }

    // Tambah pengguna baru
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (nama_lengkap, email, no_telepon, nama_pengguna, kata_sandi, role, status) 
                VALUES (:nama_lengkap, :email, :no_telepon, :nama_pengguna, :kata_sandi, :role, :status)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nama_lengkap'  => $data['nama_lengkap'],
            'email'         => $data['email'],
            'no_telepon'    => $data['no_telepon'],
            'nama_pengguna' => $data['nama_pengguna'],
            'kata_sandi'    => password_hash($data['kata_sandi'], PASSWORD_DEFAULT),
            'role'          => $data['role'] ?? 'Pegawai',
            'status'        => $data['status'] ?? 'Aktif'
        ]);
    }

    // Update data pengguna
    public function update($id, $data) {
        if (!empty($data['kata_sandi'])) {
            $sql = "UPDATE {$this->table} SET 
                    nama_lengkap = :nama_lengkap, 
                    email = :email, 
                    no_telepon = :no_telepon, 
                    nama_pengguna = :nama_pengguna, 
                    kata_sandi = :kata_sandi, 
                    role = :role, 
                    status = :status 
                    WHERE id = :id";
            
            $params = [
                'id'            => $id,
                'nama_lengkap'  => $data['nama_lengkap'],
                'email'         => $data['email'],
                'no_telepon'    => $data['no_telepon'],
                'nama_pengguna' => $data['nama_pengguna'],
                'kata_sandi'    => password_hash($data['kata_sandi'], PASSWORD_DEFAULT),
                'role'          => $data['role'],
                'status'        => $data['status']
            ];
        } else {
            $sql = "UPDATE {$this->table} SET 
                    nama_lengkap = :nama_lengkap, 
                    email = :email, 
                    no_telepon = :no_telepon, 
                    nama_pengguna = :nama_pengguna, 
                    role = :role, 
                    status = :status 
                    WHERE id = :id";
            
            $params = [
                'id'            => $id,
                'nama_lengkap'  => $data['nama_lengkap'],
                'email'         => $data['email'],
                'no_telepon'    => $data['no_telepon'],
                'nama_pengguna' => $data['nama_pengguna'],
                'role'          => $data['role'],
                'status'        => $data['status']
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // Hapus pengguna
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}