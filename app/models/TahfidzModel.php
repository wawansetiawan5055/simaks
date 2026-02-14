<?php
/**
 * TahfidzModel.php
 */

class TahfidzModel
{
    public static function getAll($pdo)
    {
        $sql = "SELECT t.*, g.nama AS nama_guru 
                FROM tahfidz t 
                LEFT JOIN guru g ON t.id_guru_pembina = g.id_guru 
                ORDER BY t.nama_kelompok ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($pdo, $id)
    {
        $sql = "SELECT t.*, g.nama AS nama_pembina 
                FROM tahfidz t 
                LEFT JOIN guru g ON t.id_guru_pembina = g.id_guru 
                WHERE t.id_tahfidz = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function save($pdo, $data)
    {
        $id = $data['id_tahfidz'] ?? null;
        $params = [
            ':nama' => $data['nama_kelompok'],
            ':kegiatan' => $data['nama_kegiatan'] ?? null,
            ':tingkat' => $data['tingkat'] ?? null,
            ':hari' => $data['hari'] ?? null,
            ':jam' => $data['jam'] ?? null,
            ':target' => $data['tingkat_target'] ?? null,
            ':status' => $data['status'] ?? 'Aktif',
            ':pembina' => $data['id_guru_pembina'] ?? null
        ];

        if ($id) {
            $sql = "UPDATE tahfidz SET nama_kelompok=:nama, nama_kegiatan=:kegiatan, tingkat=:tingkat, hari=:hari, jam=:jam, tingkat_target=:target, status=:status, id_guru_pembina=:pembina WHERE id_tahfidz=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO tahfidz (nama_kelompok, nama_kegiatan, tingkat, hari, jam, tingkat_target, status, id_guru_pembina) 
                    VALUES (:nama, :kegiatan, :tingkat, :hari, :jam, :target, :status, :pembina)";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM tahfidz WHERE id_tahfidz = ?");
        return $stmt->execute([$id]);
    }

    // --- MEMBER MANAGEMENT ---

    public static function getAnggota($pdo, $id_tah, $id_ta)
    {
        $sql = "SELECT at.*, s.nama AS nama_siswa, s.nisn, k.nama_kelas 
                FROM anggota_tahfidz at
                JOIN siswa s ON at.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE at.id_tahfidz = ? AND at.id_ta = ?
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_tah, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addAnggota($pdo, $id_tah, $id_siswa_array, $id_ta)
    {
        if (empty($id_siswa_array))
            return 0;
        $sql = "INSERT IGNORE INTO anggota_tahfidz (id_tahfidz, id_siswa, id_ta) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $count = 0;
        foreach ($id_siswa_array as $id_siswa) {
            if ($stmt->execute([$id_tah, $id_siswa, $id_ta])) {
                $count++;
            }
        }
        return $count;
    }

    public static function removeAnggota($pdo, $id_tah, $id_siswa_array, $id_ta)
    {
        if (empty($id_siswa_array))
            return 0;
        $placeholders = implode(',', array_fill(0, count($id_siswa_array), '?'));
        $sql = "DELETE FROM anggota_tahfidz WHERE id_tahfidz = ? AND id_ta = ? AND id_siswa IN ($placeholders)";
        $params = array_merge([$id_tah, $id_ta], $id_siswa_array);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public static function getAvailableStudents($pdo, $id_tah, $id_ta, $keyword = '', $id_kelas = '')
    {
        $sql = "SELECT s.id_siswa, s.nama AS nama_siswa, k.nama_kelas 
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE s.status_aktif = 'Aktif' 
                AND s.id_siswa NOT IN (
                    SELECT id_siswa FROM anggota_tahfidz WHERE id_tahfidz = ? AND id_ta = ?
                )";
        $params = [$id_ta, $id_tah, $id_ta];

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

    // --- JURNAL UMUM ---

    public static function getJurnal($pdo, $id_tah)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_tahfidz WHERE id_tahfidz = ? ORDER BY tanggal DESC, created_at DESC");
        $stmt->execute([$id_tah]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findJurnal($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_tahfidz WHERE id_jurnal = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveJurnal($pdo, $data)
    {
        $id = $data['id_jurnal'] ?? null;
        $params = [
            ':id_tah' => $data['id_tahfidz'],
            ':tgl' => $data['tanggal'],
            ':materi' => $data['materi'],
            ':ket' => $data['keterangan'] ?? null,
            ':guru' => $data['id_guru'] ?? null
        ];
        $presensi = $data['presensi'] ?? [];

        // 1. Save Jurnal
        if ($id) {
            $sql = "UPDATE jurnal_tahfidz SET id_tahfidz=:id_tah, tanggal=:tgl, materi=:materi, keterangan=:ket, id_guru=:guru WHERE id_jurnal=:id";
            $params[':id'] = $id;
            $pdo->prepare($sql)->execute($params);
            $id_jurnal = $id;
        } else {
            $sql = "INSERT INTO jurnal_tahfidz (id_tahfidz, tanggal, materi, keterangan, id_guru) VALUES (:id_tah, :tgl, :materi, :ket, :guru)";
            $pdo->prepare($sql)->execute($params);
            $id_jurnal = $pdo->lastInsertId();
        }

        // 2. Save Presensi
        if (!empty($presensi)) {
            // Delete existing presensi for this jurnal
            $pdo->prepare("DELETE FROM presensi_tahfidz WHERE id_jurnal = ?")->execute([$id_jurnal]);

            $sql_p = "INSERT INTO presensi_tahfidz (id_jurnal, id_siswa, status) VALUES (?, ?, ?)";
            $stmt_p = $pdo->prepare($sql_p);

            foreach ($presensi as $id_siswa => $status) {
                $stmt_p->execute([$id_jurnal, $id_siswa, $status]);
            }
        }

        return $id_jurnal;
    }

    public static function deleteJurnal($pdo, $id)
    {
        $pdo->prepare("DELETE FROM presensi_tahfidz WHERE id_jurnal=?")->execute([$id]);
        $stmt = $pdo->prepare("DELETE FROM jurnal_tahfidz WHERE id_jurnal=?");
        return $stmt->execute([$id]);
    }

    public static function getPresensi($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT id_siswa, status FROM presensi_tahfidz WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // --- SETORAN TAHFIDZ ---

    public static function getRefSurah($pdo)
    {
        return $pdo->query("SELECT * FROM ref_surah ORDER BY juz DESC, id_surah ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getSetoranBySiswa($pdo, $id_siswa, $id_tahfidz = null, $jenis = 'Harian')
    {
        $sql = "SELECT st.*, rs.nama_surah, rs.juz 
                FROM setoran_tahfidz st 
                JOIN ref_surah rs ON st.id_surah = rs.id_surah
                WHERE st.id_siswa = ?";
        $params = [$id_siswa];

        if ($id_tahfidz) {
            $sql .= " AND st.id_tahfidz = ?";
            $params[] = $id_tahfidz;
        }

        if ($jenis) {
            $sql .= " AND st.jenis_setoran = ?";
            $params[] = $jenis;
        }

        $sql .= " ORDER BY st.tanggal DESC, st.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveSetoran($pdo, $data)
    {
        $id = $data['id_setoran'] ?? null;
        $params = [
            ':idt' => $data['id_tahfidz'],
            ':ids' => $data['id_siswa'],
            ':tgl' => $data['tanggal'],
            ':surah' => $data['id_surah'],
            ':aa' => $data['ayat_awal'],
            ':az' => $data['ayat_akhir'],
            ':nilai' => $data['nilai'] ?? '',
            ':n_haf' => $data['nilai_hafal'] ?? null,
            ':n_taj' => $data['nilai_tajwid'] ?? null,
            ':n_mak' => $data['nilai_makhroj'] ?? null,
            ':n_nag' => $data['nilai_naghom'] ?? null,
            ':jenis' => $data['jenis_setoran'] ?? 'Harian',
            ':ket' => $data['keterangan'] ?? '',
            ':cat' => $data['catatan_guru'] ?? null,
            ':guru' => $data['id_guru'] ?? null
        ];

        if ($id) {
            $sql = "UPDATE setoran_tahfidz SET 
                     id_tahfidz=:idt, id_siswa=:ids, tanggal=:tgl, id_surah=:surah, 
                     ayat_awal=:aa, ayat_akhir=:az, nilai=:nilai, 
                     nilai_hafal=:n_haf, nilai_tajwid=:n_taj, nilai_makhroj=:n_mak, nilai_naghom=:n_nag, 
                     jenis_setoran=:jenis, keterangan=:ket, catatan_guru=:cat, id_guru=:guru 
                     WHERE id_setoran=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO setoran_tahfidz (id_tahfidz, id_siswa, tanggal, id_surah, ayat_awal, ayat_akhir, nilai, nilai_hafal, nilai_tajwid, nilai_makhroj, nilai_naghom, jenis_setoran, keterangan, catatan_guru, id_guru) 
                     VALUES (:idt, :ids, :tgl, :surah, :aa, :az, :nilai, :n_haf, :n_taj, :n_mak, :n_nag, :jenis, :ket, :cat, :guru)";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function deleteSetoran($pdo, $id)
    {
        return $pdo->prepare("DELETE FROM setoran_tahfidz WHERE id_setoran=?")->execute([$id]);
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
             WHERE pp.id_ta = ? AND mk.jenis_kegiatan = 'Tahfidz'
             ORDER BY mk.nama_kegiatan ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil Daftar Guru yang SUDAH DITUGASKAN di Penugasan Guru (Untuk Dropdown Pembina)
     * Mengambil unik berdasarkan guru, karena satu guru bisa pegang banyak kegiatan
     */
    public static function getAssignedPembinaList($pdo, $id_ta)
    {
        $stmt = $pdo->prepare(
            "SELECT DISTINCT g.id_guru, g.nama
             FROM penugasan_pembina pp
             JOIN master_kegiatan mk ON pp.id_kegiatan = mk.id_kegiatan
             JOIN guru g ON pp.id_guru = g.id_guru
             WHERE pp.id_ta = ? AND mk.jenis_kegiatan = 'Tahfidz'
             ORDER BY g.nama ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- PROGRAM KERJA & AGENDA ---

    // --- AGENDA MANAGEMENT ---

    public static function getAgenda($pdo, $id_tah)
    {
        $sql = "SELECT * FROM tahfidz_agenda WHERE id_tahfidz = ? ORDER BY tanggal DESC, created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tah]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveAgenda($pdo, $data)
    {
        $id = $data['id_agenda'] ?? null;
        $params = [
            ':id_tah' => $data['id_tahfidz'],
            ':tgl' => $data['tanggal'],
            ':nama' => $data['nama_agenda'],
            ':lokasi' => $data['lokasi'] ?? null,
            ':ket' => $data['keterangan'] ?? null,
            ':tipe' => $data['tipe'] ?? 'agenda'
        ];

        // Add file_path if provided
        if (isset($data['file_path'])) {
            $params[':file_path'] = $data['file_path'];
        }

        if ($id) {
            $sql = "UPDATE tahfidz_agenda SET id_tahfidz=:id_tah, tanggal=:tgl, nama_agenda=:nama, lokasi=:lokasi, keterangan=:ket, tipe=:tipe";
            if (isset($data['file_path'])) {
                $sql .= ", file_path=:file_path";
            }
            $sql .= " WHERE id_agenda=:id";
            $params[':id'] = $id;
        } else {
            $cols = "id_tahfidz, tanggal, nama_agenda, lokasi, keterangan, tipe";
            $vals = ":id_tah, :tgl, :nama, :lokasi, :ket, :tipe";

            if (isset($data['file_path'])) {
                $cols .= ", file_path";
                $vals .= ", :file_path";
            }

            $sql = "INSERT INTO tahfidz_agenda ($cols) VALUES ($vals)";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function updateAgendaFile($pdo, $id, $file_path)
    {
        // Delete old file if exists
        $stmt = $pdo->prepare("SELECT file_path FROM tahfidz_agenda WHERE id_agenda = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }

        $stmt = $pdo->prepare("UPDATE tahfidz_agenda SET file_path = ? WHERE id_agenda = ?");
        return $stmt->execute([$file_path, $id]);
    }

    public static function deleteAgenda($pdo, $id)
    {
        // Delete file first
        $stmt = $pdo->prepare("SELECT file_path FROM tahfidz_agenda WHERE id_agenda = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }

        $stmt = $pdo->prepare("DELETE FROM tahfidz_agenda WHERE id_agenda = ?");
        return $stmt->execute([$id]);
    }

    public static function updateProkerFile($pdo, $id_tah, $file_path)
    {
        $stmt = $pdo->prepare("UPDATE tahfidz SET file_proker = ? WHERE id_tahfidz = ?");
        return $stmt->execute([$file_path, $id_tah]);
    }

    public static function deleteProkerFile($pdo, $id_tah)
    {
        $stmt = $pdo->prepare("SELECT file_proker FROM tahfidz WHERE id_tahfidz = ?");
        $stmt->execute([$id_tah]);
        $row = $stmt->fetch();
        if ($row && $row['file_proker'] && file_exists(__DIR__ . '/../../' . $row['file_proker'])) {
            unlink(__DIR__ . '/../../' . $row['file_proker']);
        }
        $stmt = $pdo->prepare("UPDATE tahfidz SET file_proker = NULL WHERE id_tahfidz = ?");
        return $stmt->execute([$id_tah]);
    }

    // --- GALERI ---
    public static function getGaleri($pdo, $id_tah)
    {
        $stmt = $pdo->prepare("SELECT * FROM tahfidz_galeri WHERE id_tahfidz = ? ORDER BY created_at DESC");
        $stmt->execute([$id_tah]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
