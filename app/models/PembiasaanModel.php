<?php
/**
 * PembiasaanModel.php
 */

class PembiasaanModel
{
    public static function getAll($pdo)
    {
        $sql = "SELECT p.*, g.nama AS nama_pembina 
                FROM pembiasaan p 
                LEFT JOIN guru g ON p.id_guru_pembina = g.id_guru 
                ORDER BY p.nama_kegiatan ASC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($pdo, $id)
    {
        $sql = "SELECT p.*, g.nama AS nama_pembina 
                FROM pembiasaan p 
                LEFT JOIN guru g ON p.id_guru_pembina = g.id_guru 
                WHERE p.id_pembiasaan = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function save($pdo, $data)
    {
        $id = $data['id_pembiasaan'] ?? null;
        $params = [
            ':nama' => $data['nama_kegiatan'],
            ':hari' => $data['hari'] ?? null,
            ':jam'  => $data['jam'] ?? null,
            ':ket'  => $data['keterangan'] ?? null,
            ':status' => $data['status'] ?? 'Aktif',
            ':pembina' => $data['id_guru_pembina'] ?? null
        ];

        if ($id) {
            $sql = "UPDATE pembiasaan SET nama_kegiatan=:nama, hari=:hari, jam=:jam, keterangan=:ket, status=:status, id_guru_pembina=:pembina WHERE id_pembiasaan=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO pembiasaan (nama_kegiatan, hari, jam, keterangan, status, id_guru_pembina) VALUES (:nama, :hari, :jam, :ket, :status, :pembina)";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM pembiasaan WHERE id_pembiasaan = ?");
        return $stmt->execute([$id]);
    }

    // --- MEMBER MANAGEMENT ---

    public static function getAnggota($pdo, $id_pem, $id_ta)
    {
        $sql = "SELECT ap.*, s.nama AS nama_siswa, s.nisn, k.nama_kelas 
                FROM anggota_pembiasaan ap
                JOIN siswa s ON ap.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ap.id_pembiasaan = ? AND ap.id_ta = ?
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_pem, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addAnggota($pdo, $id_pem, $id_siswa_array, $id_ta)
    {
        if (empty($id_siswa_array)) return 0;
        $sql = "INSERT IGNORE INTO anggota_pembiasaan (id_pembiasaan, id_siswa, id_ta) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $count = 0;
        foreach ($id_siswa_array as $id_siswa) {
            if ($stmt->execute([$id_pem, $id_siswa, $id_ta])) {
                $count++;
            }
        }
        return $count;
    }

    public static function removeAnggota($pdo, $id_pem, $id_siswa_array, $id_ta)
    {
        if (empty($id_siswa_array)) return 0;
        $placeholders = implode(',', array_fill(0, count($id_siswa_array), '?'));
        $sql = "DELETE FROM anggota_pembiasaan WHERE id_pembiasaan = ? AND id_ta = ? AND id_siswa IN ($placeholders)";
        $params = array_merge([$id_pem, $id_ta], $id_siswa_array);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    public static function getAvailableStudents($pdo, $id_pem, $id_ta, $keyword = '', $id_kelas = '')
    {
        $sql = "SELECT s.id_siswa, s.nama AS nama_siswa, k.nama_kelas 
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE s.status_aktif = 'Aktif' 
                AND s.id_siswa NOT IN (
                    SELECT id_siswa FROM anggota_pembiasaan WHERE id_pembiasaan = ? AND id_ta = ?
                )";
        $params = [$id_ta, $id_pem, $id_ta];

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

    // --- JURNAL & ABSENSI (HARIAN) ---

    public static function getJurnal($pdo, $id_pem)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_pembiasaan WHERE id_pembiasaan = ? ORDER BY tanggal DESC, created_at DESC");
        $stmt->execute([$id_pem]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findJurnal($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_pembiasaan WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveJurnal($pdo, $data)
    {
        $id = $data['id_jurnal'] ?? null;
        $params = [
            ':id_pem' => $data['id_pembiasaan'],
            ':tgl' => $data['tanggal'],
            ':materi' => $data['materi'],
            ':ket' => $data['keterangan'] ?? null,
            ':guru' => $data['id_guru'] ?? null
        ];

        if ($id) {
            $sql = "UPDATE jurnal_pembiasaan SET id_pembiasaan=:id_pem, tanggal=:tgl, materi=:materi, keterangan=:ket, id_guru=:guru WHERE id_jurnal=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO jurnal_pembiasaan (id_pembiasaan, tanggal, materi, keterangan, id_guru) VALUES (:id_pem, :tgl, :materi, :ket, :guru)";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $id ? $id : $pdo->lastInsertId();
    }

    public static function deleteJurnal($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM jurnal_pembiasaan WHERE id_jurnal = ?");
        return $stmt->execute([$id]);
    }

    public static function getPresensi($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT id_siswa, status FROM presensi_pembiasaan WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function savePresensi($pdo, $id_jurnal, $presensi_data)
    {
        $sql = "INSERT INTO presensi_pembiasaan (id_jurnal, id_siswa, status) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status)";
        $stmt = $pdo->prepare($sql);
        foreach ($presensi_data as $id_siswa => $status) {
            $stmt->execute([$id_jurnal, $id_siswa, $status]);
        }
    }

    // --- REKAP ABSENSI MANUAL (BULANAN) ---
    
    public static function getRekapPresensi($pdo, $id_pem, $bulan, $tahun)
    {
        $stmt = $pdo->prepare("SELECT * FROM rekap_presensi_pembiasaan WHERE id_pembiasaan = ? AND bulan = ? AND tahun = ?");
        $stmt->execute([$id_pem, $bulan, $tahun]);
        
        // Return keyed by id_siswa for easy lookup
        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $result[$row['id_siswa']] = $row;
        }
        return $result;
    }

    public static function getRekapAuto($pdo, $id_pem, $bulan, $tahun)
    {
        $sql = "SELECT p.id_siswa, 
                       SUM(CASE WHEN p.status = 'H' THEN 1 ELSE 0 END) as jml_H,
                       SUM(CASE WHEN p.status = 'S' THEN 1 ELSE 0 END) as jml_S,
                       SUM(CASE WHEN p.status = 'I' THEN 1 ELSE 0 END) as jml_I,
                       SUM(CASE WHEN p.status = 'A' THEN 1 ELSE 0 END) as jml_A
                FROM presensi_pembiasaan p
                JOIN jurnal_pembiasaan j ON p.id_jurnal = j.id_jurnal
                WHERE j.id_pembiasaan = ? AND MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?
                GROUP BY p.id_siswa";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_pem, $bulan, $tahun]);
        
        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $result[$row['id_siswa']] = $row;
        }
        return $result;
    }

    public static function getRekapHybrid($pdo, $id_pem, $bulan, $tahun)
    {
        // 1. Check if manual rekap exists for any student
        $manual = self::getRekapPresensi($pdo, $id_pem, $bulan, $tahun);
        if (!empty($manual)) {
            return $manual;
        }

        // 2. If no manual rekap, calculate from daily input
        return self::getRekapAuto($pdo, $id_pem, $bulan, $tahun);
    }

    public static function saveRekapPresensi($pdo, $id_pem, $data_rekap, $bulan, $tahun)
    {
        // data_rekap = [id_siswa => ['H'=>x, 'S'=>y, 'I'=>z, 'A'=>w]]
        $sql = "INSERT INTO rekap_presensi_pembiasaan (id_pembiasaan, id_siswa, bulan, tahun, jml_H, jml_S, jml_I, jml_A) 
                VALUES (:id_pem, :id_siswa, :bulan, :tahun, :h, :s, :i, :a)
                ON DUPLICATE KEY UPDATE jml_H=VALUES(jml_H), jml_S=VALUES(jml_S), jml_I=VALUES(jml_I), jml_A=VALUES(jml_A)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($data_rekap as $id_siswa => $rekap) {
             $stmt->execute([
                 ':id_pem' => $id_pem,
                 ':id_siswa' => $id_siswa,
                 ':bulan' => $bulan,
                 ':tahun' => $tahun,
                 ':h' => $rekap['H'] ?? 0,
                 ':s' => $rekap['S'] ?? 0,
                 ':i' => $rekap['I'] ?? 0,
                 ':a' => $rekap['A'] ?? 0
             ]);
        }
    }

    // --- PENILAIAN ---

    public static function getPenilaian($pdo, $id_pem, $bulan, $tahun)
    {
        $stmt = $pdo->prepare("SELECT * FROM penilaian_pembiasaan WHERE id_pembiasaan = ? AND bulan = ? AND tahun = ?");
        $stmt->execute([$id_pem, $bulan, $tahun]);
        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $result[$row['id_siswa']] = $row;
        }
        return $result;
    }

    public static function savePenilaian($pdo, $id_pem, $data_nilai, $bulan, $tahun)
    {
         $sql = "INSERT INTO penilaian_pembiasaan (id_pembiasaan, id_siswa, bulan, tahun, persentase_kehadiran, nilai, deskripsi, catatan)
                 VALUES (:id_pem, :id_siswa, :bulan, :tahun, :persen, :nilai, :desk, :cat)
                 ON DUPLICATE KEY UPDATE persentase_kehadiran=VALUES(persentase_kehadiran), nilai=VALUES(nilai), deskripsi=VALUES(deskripsi), catatan=VALUES(catatan)";
         $stmt = $pdo->prepare($sql);

         foreach ($data_nilai as $id_siswa => $d) {
             $stmt->execute([
                 ':id_pem' => $id_pem,
                 ':id_siswa' => $id_siswa,
                 ':bulan' => $bulan,
                 ':tahun' => $tahun,
                 ':persen' => $d['persentase'],
                 ':nilai' => $d['nilai'],
                 ':desk' => $d['deskripsi'],
                 ':cat' => $d['catatan']
             ]);
         }
    }
    // --- PROGRAM KERJA & AGENDA ---

    public static function getAgenda($pdo, $id_pem)
    {
        $stmt = $pdo->prepare("SELECT * FROM agenda_pembiasaan WHERE id_pembiasaan = ? ORDER BY tanggal DESC");
        $stmt->execute([$id_pem]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveAgenda($pdo, $data)
    {
        $id = $data['id_agenda'] ?? null;
        $params = [
            ':id_pem' => $data['id_pembiasaan'],
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
            $sql = "UPDATE agenda_pembiasaan SET id_pembiasaan=:id_pem, tanggal=:tgl, nama_agenda=:nama, lokasi=:lokasi, keterangan=:ket, tipe=:tipe";
            if (isset($data['file_path'])) $sql .= ", file_path=:file_path";
            $sql .= " WHERE id_agenda=:id";
            $params[':id'] = $id;
        } else {
             $cols = "id_pembiasaan, tanggal, nama_agenda, lokasi, keterangan, tipe";
             $vals = ":id_pem, :tgl, :nama, :lokasi, :ket, :tipe";
             if (isset($data['file_path'])) { $cols.=", file_path"; $vals.=", :file_path"; }
             $sql = "INSERT INTO agenda_pembiasaan ($cols) VALUES ($vals)";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function deleteAgenda($pdo, $id)
    {
        // Get file path first
        $stmt = $pdo->prepare("SELECT file_path FROM agenda_pembiasaan WHERE id_agenda = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }
        
        return $pdo->prepare("DELETE FROM agenda_pembiasaan WHERE id_agenda = ?")->execute([$id]);
    }
    
    public static function updateAgendaFile($pdo, $id, $file_path) {
        $stmt = $pdo->prepare("UPDATE agenda_pembiasaan SET file_path = ? WHERE id_agenda = ?");
        return $stmt->execute([$file_path, $id]);
    }

    // --- GALERI ---

    public static function getGaleri($pdo, $id_pem)
    {
        $stmt = $pdo->prepare("SELECT * FROM galeri_pembiasaan WHERE id_pembiasaan = ? ORDER BY created_at DESC");
        $stmt->execute([$id_pem]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveGaleri($pdo, $data)
    {
        $sql = "INSERT INTO galeri_pembiasaan (id_pembiasaan, judul, file_path) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$data['id_pembiasaan'], $data['judul'], $data['file_path']]);
    }

    public static function deleteGaleri($pdo, $id)
    {
        // Get file path
        $stmt = $pdo->prepare("SELECT file_path FROM galeri_pembiasaan WHERE id_galeri = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }
        
        return $pdo->prepare("DELETE FROM galeri_pembiasaan WHERE id_galeri = ?")->execute([$id]);
    }
}
