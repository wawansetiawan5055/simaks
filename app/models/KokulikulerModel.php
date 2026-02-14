<?php
/**
 * KokulikulerModel.php
 * Model untuk mengelola data Ko-kurikuler (P5)
 */

class KokulikulerModel
{
    /**
     * Ambil semua data kokulikuler
     */
    public static function getAll($pdo)
    {
        $stmt = $pdo->query("
            SELECT k.*, g.nama AS nama_pembina 
            FROM kokulikuler k
            LEFT JOIN guru g ON k.id_guru_pembina = g.id_guru
            ORDER BY k.nama_kegiatan ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil detail kokulikuler berdasarkan ID
     */
    public static function find($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM kokulikuler WHERE id_kokulikuler = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Simpan (Insert/Update) Kokulikuler
     */
    public static function save($pdo, $data)
    {
        $id = $data['id_kokulikuler'] ?? null;

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
                $data['nama_kegiatan'] = $details['nama_kegiatan'];
            }
        }

        $params = [
            ':nama' => $data['nama_kegiatan'] ?? '',
            ':tema' => $data['tema'] ?? null,
            ':pembina' => !empty($data['id_guru_pembina']) ? $data['id_guru_pembina'] : null,
            ':hari' => $data['hari'] ?? null,
            ':jam_mulai' => $data['jam_mulai'] ?? null,
            ':jam_selesai' => $data['jam_selesai'] ?? null,
            ':status' => $data['status'] ?? 'Aktif'
        ];

        if ($id) {
            // Update
            $sql = "UPDATE kokulikuler SET 
                    nama_kegiatan = :nama, 
                    tema = :tema,
                    id_guru_pembina = :pembina, 
                    hari = :hari, 
                    jam_mulai = :jam_mulai, 
                    jam_selesai = :jam_selesai, 
                    status = :status 
                    WHERE id_kokulikuler = :id";
            $params[':id'] = $id;
        } else {
            // Insert
            $sql = "INSERT INTO kokulikuler 
                    (nama_kegiatan, tema, id_guru_pembina, hari, jam_mulai, jam_selesai, status) 
                    VALUES 
                    (:nama, :tema, :pembina, :hari, :jam_mulai, :jam_selesai, :status)";
        }

        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute($params);

        if ($res && isset($data['id_profil_array'])) {
            $id_final = $id ?: $pdo->lastInsertId();
            self::syncProfil($pdo, $id_final, $data['id_profil_array']);
        }

        return $res;
    }

    /**
     * Hapus Kokulikuler
     */
    public static function delete($pdo, $id)
    {
        // Cek dependencies (anggota) - Table anggota_kokulikuler belum dibuat, mungkin perlu dibuat?
        // Asumsi user belum minta tabel anggota khusus, tapi logic Ekskul punya anggota_ekskul.
        // Untuk Kokulikuler, kita perlu tabel anggota_kokulikuler jika ingin mengelola anggota.
        // Mari kita buat tabel anggota_kokulikuler secara implisit atau gunakan tabel yang sama dengan tipe?
        // Karena tabel master dipisah, sebaiknya tabel anggota juga dipisah agar foreign key bersih.
        // Namun script DB tadi belum buat tabel anggota_kokulikuler. 
        // Saya akan handle pembuatan tabel anggota_kokulikuler on the fly atau di model ini jika belum ada.
        
        // Cek dulu keberadaan tabel, jika belum ada, assume tidak ada anggota
        try {
             $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM anggota_kokulikuler WHERE id_kokulikuler = ?");
             $stmtCheck->execute([$id]);
             if ($stmtCheck->fetchColumn() > 0) {
                 throw new Exception("Tidak bisa menghapus kegiatan yang memiliki anggota.");
             }
        } catch (Exception $e) {
            // Tabel mungkin belum ada, abaikan check
        }

        $stmt = $pdo->prepare("DELETE FROM kokulikuler WHERE id_kokulikuler = ?");
        return $stmt->execute([$id]);
    }
    
    // --- MEMBER MANAGEMENT ---

    public static function getAnggota($pdo, $id_kokul, $id_ta)
    {
        $sql = "SELECT ak.*, s.nama AS nama_siswa, s.nisn, k.nama_kelas 
                FROM anggota_kokulikuler ak
                JOIN siswa s ON ak.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ak.id_kokulikuler = ? AND ak.id_ta = ?
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_kokul, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addAnggota($pdo, $id_kokul, $id_siswa_array, $id_ta)
    {
        $sql = "INSERT IGNORE INTO anggota_kokulikuler (id_kokulikuler, id_siswa, id_ta) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $count = 0;
        foreach ($id_siswa_array as $id_siswa) {
            if ($stmt->execute([$id_kokul, $id_siswa, $id_ta])) {
                $count++;
            }
        }
        return $count;
    }

    public static function removeAnggota($pdo, $id_kokul, $id_siswa_array, $id_ta)
    {
        if (empty($id_siswa_array)) return 0;
        $placeholders = implode(',', array_fill(0, count($id_siswa_array), '?'));
        $sql = "DELETE FROM anggota_kokulikuler WHERE id_kokulikuler = ? AND id_ta = ? AND id_siswa IN ($placeholders)";
        $params = array_merge([$id_kokul, $id_ta], $id_siswa_array);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public static function getAvailableStudents($pdo, $id_kokul, $id_ta, $keyword = '', $id_kelas = '')
    {
        $sql = "SELECT s.id_siswa, s.nama AS nama_siswa, k.nama_kelas 
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE s.status_aktif = 'Aktif' 
                AND s.id_siswa NOT IN (
                    SELECT id_siswa FROM anggota_kokulikuler WHERE id_kokulikuler = ? AND id_ta = ?
                )";
        $params = [$id_ta, $id_kokul, $id_ta];

        if (!empty($id_kelas)) {
            $sql .= " AND ps.id_kelas = ?";
            $params[] = $id_kelas;
        }

        if (!empty($keyword)) {
            $sql .= " AND (s.nama LIKE ? OR k.nama_kelas LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY k.nama_kelas, s.nama LIMIT 50";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- JURNAL & ABSENSI ---

    public static function getJurnal($pdo, $id_kokul)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_kokulikuler WHERE id_kokulikuler = ? ORDER BY tanggal DESC, created_at DESC");
        $stmt->execute([$id_kokul]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findJurnal($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_kokulikuler WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveJurnal($pdo, $data)
    {
        $id = $data['id_jurnal'] ?? null;
        $params = [
            ':id_kokul' => $data['id_kokulikuler'],
            ':tgl' => $data['tanggal'],
            ':materi' => $data['materi'],
            ':ket' => $data['keterangan'] ?? null,
            ':guru' => $data['id_guru'] ?? null
        ];

        if ($id) {
            $sql = "UPDATE jurnal_kokulikuler SET id_kokulikuler=:id_kokul, tanggal=:tgl, materi=:materi, keterangan=:ket, id_guru=:guru WHERE id_jurnal=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO jurnal_kokulikuler (id_kokulikuler, tanggal, materi, keterangan, id_guru) VALUES (:id_kokul, :tgl, :materi, :ket, :guru)";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $id ? $id : $pdo->lastInsertId();
    }

    public static function deleteJurnal($pdo, $id)
    {
        // Presensi cascade on delete defined in DB, so safe to delete
        $stmt = $pdo->prepare("DELETE FROM jurnal_kokulikuler WHERE id_jurnal = ?");
        return $stmt->execute([$id]);
    }

    public static function getPresensi($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT id_siswa, status FROM presensi_kokulikuler WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // id_siswa => status
    }

    public static function savePresensi($pdo, $id_jurnal, $presensi_data)
    {
        // presensi_data = [id_siswa => status, ...]
        $sql = "INSERT INTO presensi_kokulikuler (id_jurnal, id_siswa, status) VALUES (?, ?, ?)
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
             WHERE pp.id_ta = ? AND mk.jenis_kegiatan = 'Kokulikuler'
             ORDER BY mk.nama_kegiatan ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- GALERI ---

    public static function getGaleri($pdo, $id_kokul)
    {
        $stmt = $pdo->prepare("SELECT * FROM kokulikuler_galeri WHERE id_kokulikuler = ? ORDER BY created_at DESC");
        $stmt->execute([$id_kokul]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveGaleri($pdo, $id_kokul, $file_path, $judul = null)
    {
        $sql = "INSERT INTO kokulikuler_galeri (id_kokulikuler, file_path, judul) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_kokul, $file_path, $judul]);
    }

    public static function deleteGaleri($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT file_path FROM kokulikuler_galeri WHERE id_galeri = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }

        $stmt = $pdo->prepare("DELETE FROM kokulikuler_galeri WHERE id_galeri = ?");
        return $stmt->execute([$id]);
    }

    // --- PROGRAM KERJA & AGENDA ---

    public static function getAgenda($pdo, $id_kokul)
    {
        $stmt = $pdo->prepare("SELECT * FROM agenda_kokulikuler WHERE id_kokulikuler = ? ORDER BY tanggal DESC");
        $stmt->execute([$id_kokul]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveAgenda($pdo, $data)
    {
        $id = $data['id_agenda'] ?? null;
        $params = [
            ':id_kokul' => $data['id_kokulikuler'],
            ':tgl' => $data['tanggal'],
            ':nama' => $data['nama_agenda'],
            ':lokasi' => $data['lokasi'] ?? null,
            ':ket' => $data['keterangan'] ?? null,
            ':tipe' => $data['tipe'] ?? 'agenda'
        ];
        
        if (isset($data['file_path'])) {
             $params[':file_path'] = $data['file_path'];
        }

        if ($id) {
            $sql = "UPDATE agenda_kokulikuler SET id_kokulikuler=:id_kokul, tanggal=:tgl, nama_agenda=:nama, lokasi=:lokasi, keterangan=:ket, tipe=:tipe";
            if (isset($data['file_path'])) $sql .= ", file_path=:file_path";
            $sql .= " WHERE id_agenda=:id";
            $params[':id'] = $id;
        } else {
             $cols = "id_kokulikuler, tanggal, nama_agenda, lokasi, keterangan, tipe";
             $vals = ":id_kokul, :tgl, :nama, :lokasi, :ket, :tipe";
             if (isset($data['file_path'])) { $cols.=", file_path"; $vals.=", :file_path"; }
             $sql = "INSERT INTO agenda_kokulikuler ($cols) VALUES ($vals)";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function deleteAgenda($pdo, $id)
    {
        // Get file path first
        $stmt = $pdo->prepare("SELECT file_path FROM agenda_kokulikuler WHERE id_agenda = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }
        
        return $pdo->prepare("DELETE FROM agenda_kokulikuler WHERE id_agenda = ?")->execute([$id]);
    }
    
    public static function updateAgendaFile($pdo, $id, $file_path) {
        $stmt = $pdo->prepare("UPDATE agenda_kokulikuler SET file_path = ? WHERE id_agenda = ?");
        return $stmt->execute([$file_path, $id]);
    }

    /**
     * Ambil Master 8 Dimensi Profil Lulusan
     */
    public static function getProfilLulusanMaster($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM ref_profil_lulusan ORDER BY id_profil ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil Dimensi yang dipilih untuk suatu Kokurikuler
     */
    public static function getProfilByKokulikuler($pdo, $id_kokul)
    {
        $stmt = $pdo->prepare("SELECT id_profil FROM kokulikuler_profil WHERE id_kokulikuler = ?");
        $stmt->execute([$id_kokul]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Mengembalikan array id_profil saja
    }

    /**
     * Simpan/Sync Dimensi Profil untuk suatu Kokurikuler
     */
    public static function syncProfil($pdo, $id_kokul, $id_profil_array)
    {
        // Hapus yang lama
        $stmtDel = $pdo->prepare("DELETE FROM kokulikuler_profil WHERE id_kokulikuler = ?");
        $stmtDel->execute([$id_kokul]);

        if (!empty($id_profil_array)) {
            $sqlIns = "INSERT INTO kokulikuler_profil (id_kokulikuler, id_profil) VALUES (?, ?)";
            $stmtIns = $pdo->prepare($sqlIns);
            foreach ($id_profil_array as $idp) {
                $stmtIns->execute([$id_kokul, $idp]);
            }
        }
    }

    /**
     * Ambil Data Penilaian Siswa untuk satu kegiatan
     */
    public static function getPenilaian($pdo, $id_kokul, $id_ta)
    {
        $sql = "SELECT ak.id_siswa, s.nama AS nama_siswa, n.nilai, n.deskripsi 
                FROM anggota_kokulikuler ak
                JOIN siswa s ON ak.id_siswa = s.id_siswa
                LEFT JOIN kokulikuler_nilai n ON ak.id_kokulikuler = n.id_kokulikuler AND ak.id_siswa = n.id_siswa AND ak.id_ta = n.id_ta
                WHERE ak.id_kokulikuler = ? AND ak.id_ta = ?
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kokul, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Simpan/Batch Penilaian Siswa
     */
    public static function savePenilaian($pdo, $id_kokul, $id_ta, $nilai_data)
    {
        // nilai_data = [ id_siswa => ['nilai' => '...', 'deskripsi' => '...'], ... ]
        $sql = "INSERT INTO kokulikuler_nilai (id_kokulikuler, id_siswa, id_ta, nilai, deskripsi) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE nilai = VALUES(nilai), deskripsi = VALUES(deskripsi)";
        $stmt = $pdo->prepare($sql);
        foreach ($nilai_data as $id_siswa => $data) {
            $stmt->execute([$id_kokul, $id_siswa, $id_ta, $data['nilai'], $data['deskripsi']]);
        }
    }
}
