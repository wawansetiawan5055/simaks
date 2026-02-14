<?php
/**
 * EkskulModel.php
 * Model untuk mengelola data Ekstrakurikuler dan Kokulikuler (P5)
 */

class EkskulModel
{
    /**
     * Ambil semua data ekskul
     */
    public static function getAll($pdo)
    {
        $stmt = $pdo->query("
            SELECT e.*, g.nama AS nama_pembina 
            FROM ekstrakurikuler e
            LEFT JOIN guru g ON e.id_guru_pembina = g.id_guru
            ORDER BY e.nama_ekskul ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil detail ekskul berdasarkan ID
     */
    public static function find($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM ekstrakurikuler WHERE id_ekskul = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Simpan (Insert/Update) Ekskul
     */
    public static function save($pdo, $data)
    {
        $id = $data['id_ekskul'] ?? null;

        // ROBUST LOOKUP: If id_penugasan is provided, look up the authoritative details.
        if (!empty($data['id_penugasan'])) {
            $stmtLookup = $pdo->prepare("
                SELECT pp.id_guru, mk.nama_kegiatan 
                FROM penugasan_pembina pp
                JOIN master_kegiatan mk ON pp.id_kegiatan = mk.id_kegiatan
                WHERE pp.id_penugasan_pembina = ?
            ");
            $stmtLookup->execute([$data['id_penugasan']]);
            $details = $stmtLookup->fetch(PDO::FETCH_ASSOC);
            
            if ($details) {
                $data['id_guru_pembina'] = $details['id_guru'];
                $data['nama_ekskul'] = $details['nama_kegiatan'];
            }
        }

        $params = [
            ':nama' => $data['nama_ekskul'] ?? '', 
            ':kategori' => 'Ekstrakurikuler',
            ':pembina' => !empty($data['id_guru_pembina']) ? $data['id_guru_pembina'] : null,
            ':hari' => $data['hari'] ?? null,
            ':jam_mulai' => $data['jam_mulai'] ?? null,
            ':jam_selesai' => $data['jam_selesai'] ?? null,
            ':status' => $data['status'] ?? 'Aktif'
        ];

        if ($id) {
            // Update
            $sql = "UPDATE ekstrakurikuler SET 
                    nama_ekskul = :nama, 
                    kategori = :kategori, 
                    id_guru_pembina = :pembina, 
                    hari = :hari, 
                    jam_mulai = :jam_mulai, 
                    jam_selesai = :jam_selesai, 
                    status = :status 
                    WHERE id_ekskul = :id";
            $params[':id'] = $id;
        } else {
            // Insert
            $sql = "INSERT INTO ekstrakurikuler 
                    (nama_ekskul, kategori, id_guru_pembina, hari, jam_mulai, jam_selesai, status) 
                    VALUES 
                    (:nama, :kategori, :pembina, :hari, :jam_mulai, :jam_selesai, :status)";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Hapus Ekskul
     */
    public static function delete($pdo, $id)
    {
        // Cek dependencies (anggota)
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM anggota_ekskul WHERE id_ekskul = ?");
        $stmtCheck->execute([$id]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new Exception("Tidak bisa menghapus ekskul yang memiliki anggota.");
        }

        $stmt = $pdo->prepare("DELETE FROM ekstrakurikuler WHERE id_ekskul = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Ambil Anggota Ekskul pada TA tertentu
     */
    public static function getAnggota($pdo, $id_ekskul, $id_ta)
    {
        $sql = "SELECT ae.*, s.nama AS nama_siswa, s.nisn AS nis, ps.id_kelas, k.nama_kelas
                FROM anggota_ekskul ae
                JOIN siswa s ON ae.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ae.id_ekskul = ? AND ae.id_ta = ?
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_ekskul, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah Anggota (Bulk Insert/Single)
     */
    public static function addAnggota($pdo, $id_ekskul, $id_siswa_array, $id_ta)
    {
        $sql = "INSERT IGNORE INTO anggota_ekskul (id_ekskul, id_siswa, id_ta) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $count = 0;
        foreach ($id_siswa_array as $id_siswa) {
            if ($stmt->execute([$id_ekskul, $id_siswa, $id_ta])) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Hapus Anggota
     */
    public static function removeAnggota($pdo, $id_ekskul, $id_siswa_array, $id_ta)
    {
        // Loop atau IN clause
        if (empty($id_siswa_array))
            return 0;

        $placeholders = implode(',', array_fill(0, count($id_siswa_array), '?'));
        $sql = "DELETE FROM anggota_ekskul WHERE id_ekskul = ? AND id_ta = ? AND id_siswa IN ($placeholders)";

        $params = array_merge([$id_ekskul, $id_ta], $id_siswa_array);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Update Nilai Anggota
     */
    public static function updateNilai($pdo, $id_anggota, $nilai, $deskripsi)
    {
        $sql = "UPDATE anggota_ekskul SET nilai = ?, deskripsi = ? WHERE id_anggota_ekskul = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nilai, $deskripsi, $id_anggota]);
    }

    /**
     * Ambil Calon Anggota (Siswa yang BELUM masuk ke ekskul ini di TA ini)
     * Filter by Nama/Kelas optional
     */
    public static function getAvailableStudents($pdo, $id_ekskul, $id_ta, $keyword = '', $id_kelas = '')
    {
        $sql = "SELECT s.id_siswa, s.nama AS nama_siswa, k.nama_kelas 
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE s.status_aktif = 'Aktif' 
                AND s.id_siswa NOT IN (
                    SELECT id_siswa FROM anggota_ekskul WHERE id_ekskul = ? AND id_ta = ?
                )";

        $params = [$id_ta, $id_ekskul, $id_ta];

        if (!empty($id_kelas)) {
            $sql .= " AND ps.id_kelas = ?";
            $params[] = $id_kelas;
        }

        if (!empty($keyword)) {
            $sql .= " AND (s.nama LIKE ? OR k.nama_kelas LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY k.nama_kelas, s.nama LIMIT 50"; // Limit agar tidak berat

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- JURNAL & ABSENSI ---

    public static function getJurnal($pdo, $id_ekskul)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_ekstrakurikuler WHERE id_ekskul = ? ORDER BY tanggal DESC, created_at DESC");
        $stmt->execute([$id_ekskul]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findJurnal($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_ekstrakurikuler WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveJurnal($pdo, $data)
    {
        $id = $data['id_jurnal'] ?? null;
        $params = [
            ':id_ekskul' => $data['id_ekskul'],
            ':tgl' => $data['tanggal'],
            ':materi' => $data['materi'],
            ':ket' => $data['keterangan'] ?? null,
            ':guru' => $data['id_guru'] ?? null
        ];

        if ($id) {
            $sql = "UPDATE jurnal_ekstrakurikuler SET id_ekskul=:id_ekskul, tanggal=:tgl, materi=:materi, keterangan=:ket, id_guru=:guru WHERE id_jurnal=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO jurnal_ekstrakurikuler (id_ekskul, tanggal, materi, keterangan, id_guru) VALUES (:id_ekskul, :tgl, :materi, :ket, :guru)";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $id ? $id : $pdo->lastInsertId();
    }

    public static function deleteJurnal($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM jurnal_ekstrakurikuler WHERE id_jurnal = ?");
        return $stmt->execute([$id]);
    }

    public static function getPresensi($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT id_siswa, status FROM presensi_ekstrakurikuler WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function savePresensi($pdo, $id_jurnal, $presensi_data)
    {
        $sql = "INSERT INTO presensi_ekstrakurikuler (id_jurnal, id_siswa, status) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status)";
        $stmt = $pdo->prepare($sql);
        foreach ($presensi_data as $id_siswa => $status) {
            $stmt->execute([$id_jurnal, $id_siswa, $status]);
        }
    }

    /**
     * Ambil Daftar Kegiatan yang SUDAH DITUGASKAN di Penugasan Guru (Untuk Dropdown Form)
     */
    public static function getAssignedActivities($pdo, $id_ta)
    {
        $stmt = $pdo->prepare(
            "SELECT pp.id_penugasan_pembina, mk.id_kegiatan, mk.nama_kegiatan, pp.id_guru, g.nama AS nama_guru
             FROM penugasan_pembina pp
             JOIN master_kegiatan mk ON pp.id_kegiatan = mk.id_kegiatan
             JOIN guru g ON pp.id_guru = g.id_guru
             WHERE pp.id_ta = ? AND mk.jenis_kegiatan = 'Ekstrakurikuler'
             ORDER BY mk.nama_kegiatan ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- PROGRAM KERJA ---

    public static function getProgramKerja($pdo, $id_ekskul)
    {
        $stmt = $pdo->prepare("SELECT * FROM ekskul_program_kerja WHERE id_ekskul = ? ORDER BY tanggal ASC");
        $stmt->execute([$id_ekskul]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveProgramKerja($pdo, $data)
    {
        $id = $data['id_program'] ?? null;
        $params = [
            ':id_ekskul' => $data['id_ekskul'],
            ':tgl' => $data['tanggal'],
            ':kegiatan' => $data['nama_kegiatan'],
            ':lokasi' => $data['lokasi'],
            ':keterangan' => $data['keterangan'] ?? null,
            ':file' => $data['file_path'] ?? null,
            ':tipe' => $data['tipe'] ?? 'agenda'
        ];

        if ($id) {
            $sql = "UPDATE ekskul_program_kerja SET id_ekskul=:id_ekskul, tanggal=:tgl, nama_kegiatan=:kegiatan, lokasi=:lokasi, keterangan=:keterangan, file_path=COALESCE(:file, file_path), tipe=:tipe WHERE id_program=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO ekskul_program_kerja (id_ekskul, tipe, tanggal, nama_kegiatan, lokasi, keterangan, file_path) VALUES (:id_ekskul, :tipe, :tgl, :kegiatan, :lokasi, :keterangan, :file)";
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function deleteProgramKerja($pdo, $id)
    {
        // Get file path to delete
        $stmt = $pdo->prepare("SELECT file_path FROM ekskul_program_kerja WHERE id_program = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }

        $stmt = $pdo->prepare("DELETE FROM ekskul_program_kerja WHERE id_program = ?");
        return $stmt->execute([$id]);
    }

    public static function deleteProgramFile($pdo, $id)
    {
        // Get file path to delete
        $stmt = $pdo->prepare("SELECT file_path FROM ekskul_program_kerja WHERE id_program = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }
        
        // Update DB to NULL
        $stmt = $pdo->prepare("UPDATE ekskul_program_kerja SET file_path = NULL WHERE id_program = ?");
        return $stmt->execute([$id]);
    }

    public static function updateProgramFile($pdo, $id, $file_path)
    {
        // Delete old file if exists
        $stmt = $pdo->prepare("SELECT file_path FROM ekskul_program_kerja WHERE id_program = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }

        $stmt = $pdo->prepare("UPDATE ekskul_program_kerja SET file_path = ? WHERE id_program = ?");
        return $stmt->execute([$file_path, $id]);
    }

    // --- GALERY ---

    public static function getGaleri($pdo, $id_ekskul)
    {
        $stmt = $pdo->prepare("SELECT * FROM ekskul_galeri WHERE id_ekskul = ? ORDER BY created_at DESC");
        $stmt->execute([$id_ekskul]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveGaleri($pdo, $id_ekskul, $file_path, $judul = null)
    {
        $sql = "INSERT INTO ekskul_galeri (id_ekskul, file_path, judul) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_ekskul, $file_path, $judul]);
    }

    public static function deleteGaleri($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT file_path FROM ekskul_galeri WHERE id_galeri = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }

        $stmt = $pdo->prepare("DELETE FROM ekskul_galeri WHERE id_galeri = ?");
        return $stmt->execute([$id]);
    }

    // --- PENILAIAN ---

    public static function getNilai($pdo, $id_ekskul)
    {
        // Get all members and their grades if any
        $sql = "SELECT ak.id_siswa, s.nama AS nama_siswa, s.nipd, s.nisn, k.nama_kelas, ne.nilai, ne.deskripsi, ne.id_nilai
                FROM anggota_ekskul ak
                JOIN siswa s ON ak.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ak.id_ta
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                LEFT JOIN nilai_ekskul ne ON ak.id_ekskul = ne.id_ekskul AND ak.id_siswa = ne.id_siswa
                WHERE ak.id_ekskul = ?
                ORDER BY k.nama_kelas, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ekskul]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveNilai($pdo, $id_ekskul, $nilai_data)
    {
        // nilai_data array of [id_siswa => ['nilai'=>..., 'deskripsi'=>...]]
        $sql = "INSERT INTO nilai_ekskul (id_ekskul, id_siswa, nilai, deskripsi) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE nilai = VALUES(nilai), deskripsi = VALUES(deskripsi)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($nilai_data as $id_siswa => $data) {
            $stmt->execute([$id_ekskul, $id_siswa, $data['nilai'], $data['deskripsi']]);
        }
    }
}
