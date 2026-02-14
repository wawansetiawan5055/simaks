<?php
/**
 * KewirausahaanModel.php
 */

class KewirausahaanModel
{
    public static function getAll($pdo)
    {
        $sql = "SELECT k.*, g.nama AS nama_pembina,
                (SELECT COUNT(*) FROM kewirausahaan_tahapan WHERE id_kewirausahaan = k.id_kewirausahaan) as total_tahapan,
                (SELECT COUNT(*) FROM kewirausahaan_tahapan WHERE id_kewirausahaan = k.id_kewirausahaan AND status = 'Selesai') as selesai_tahapan
                FROM kewirausahaan k 
                LEFT JOIN guru g ON k.id_guru_pembina = g.id_guru 
                ORDER BY k.nama_kegiatan ASC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($pdo, $id)
    {
        $sql = "SELECT k.*, g.nama AS nama_pembina 
                FROM kewirausahaan k 
                LEFT JOIN guru g ON k.id_guru_pembina = g.id_guru 
                WHERE k.id_kewirausahaan = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function save($pdo, $data)
    {
    	    $id = $data['id_kewirausahaan'] ?? null;
            
            // ROBUST LOOKUP: If id_penugasan is provided, look up the authoritative details.
            // This bypasses any frontend JS issues or hidden input failures.
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

    	    $pembina = $data['id_guru_pembina'] ?? null;
    	    
    	    $params = [
    	        ':nama' => $data['nama_kegiatan'] ?? '', // Default to empty string if missing, though lookup should fix it
    	        ':kelompok' => $data['kelompok'] ?? null,
	        ':hari' => $data['hari'] ?? null,
	        ':jam'  => $data['jam'] ?? null,
	        ':ket'  => $data['keterangan'] ?? null,
	        ':status' => $data['status'] ?? 'Aktif',
	        ':pembina' => $pembina
	    ];

	    if ($id) {
	        $sql = "UPDATE kewirausahaan SET nama_kegiatan=:nama, kelompok=:kelompok, hari=:hari, jam=:jam, keterangan=:ket, status=:status, id_guru_pembina=:pembina WHERE id_kewirausahaan=:id";
	        $params[':id'] = $id;
	    } else {
	        $sql = "INSERT INTO kewirausahaan (nama_kegiatan, kelompok, hari, jam, keterangan, status, id_guru_pembina) VALUES (:nama, :kelompok, :hari, :jam, :ket, :status, :pembina)";
	    }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM kewirausahaan WHERE id_kewirausahaan = ?");
        return $stmt->execute([$id]);
    }

    // --- MEMBER MANAGEMENT ---

    public static function getAnggota($pdo, $id_kew, $id_ta)
    {
        $sql = "SELECT ak.*, s.nama AS nama_siswa, s.nisn, k.nama_kelas 
                FROM anggota_kewirausahaan ak
                JOIN siswa s ON ak.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ak.id_kewirausahaan = ? AND ak.id_ta = ?
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_kew, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addAnggota($pdo, $id_kew, $id_siswa_array, $id_ta)
    {
        if (empty($id_siswa_array)) return 0;
        $sql = "INSERT IGNORE INTO anggota_kewirausahaan (id_kewirausahaan, id_siswa, id_ta) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $count = 0;
        foreach ($id_siswa_array as $id_siswa) {
            if ($stmt->execute([$id_kew, $id_siswa, $id_ta])) {
                $count++;
            }
        }
        return $count;
    }

    public static function removeAnggota($pdo, $id_kew, $id_siswa_array, $id_ta)
    {
        if (empty($id_siswa_array)) return 0;
        $placeholders = implode(',', array_fill(0, count($id_siswa_array), '?'));
        $sql = "DELETE FROM anggota_kewirausahaan WHERE id_kewirausahaan = ? AND id_ta = ? AND id_siswa IN ($placeholders)";
        $params = array_merge([$id_kew, $id_ta], $id_siswa_array);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    public static function getAvailableStudents($pdo, $id_kew, $id_ta, $keyword = '', $id_kelas = '')
    {
        $sql = "SELECT s.id_siswa, s.nama AS nama_siswa, k.nama_kelas 
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE s.status_aktif = 'Aktif' 
                AND s.id_siswa NOT IN (
                    SELECT id_siswa FROM anggota_kewirausahaan WHERE id_kewirausahaan = ? AND id_ta = ?
                )";
        $params = [$id_ta, $id_kew, $id_ta];

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

    public static function getJurnal($pdo, $id_kew)
    {
        $stmt = $pdo->prepare("SELECT j.*, t.nama_tahapan 
                               FROM jurnal_kewirausahaan j 
                               LEFT JOIN kewirausahaan_tahapan t ON j.id_tahapan = t.id_tahapan
                               WHERE j.id_kewirausahaan = ? 
                               ORDER BY j.tanggal DESC, j.created_at DESC");
        $stmt->execute([$id_kew]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findJurnal($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT * FROM jurnal_kewirausahaan WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveJurnal($pdo, $data)
    {
        $id = $data['id_jurnal'] ?? null;
        $params = [
            ':id_kew' => $data['id_kewirausahaan'],
            ':id_tahapan' => !empty($data['id_tahapan']) ? $data['id_tahapan'] : null,
            ':tgl' => $data['tanggal'],
            ':materi' => $data['materi'],
            ':ket' => $data['keterangan'] ?? null,
            ':guru' => $data['id_guru'] ?? null
        ];

        if ($id) {
            $sql = "UPDATE jurnal_kewirausahaan SET id_kewirausahaan=:id_kew, id_tahapan=:id_tahapan, tanggal=:tgl, materi=:materi, keterangan=:ket, id_guru=:guru WHERE id_jurnal=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO jurnal_kewirausahaan (id_kewirausahaan, id_tahapan, tanggal, materi, keterangan, id_guru) VALUES (:id_kew, :id_tahapan, :tgl, :materi, :ket, :guru)";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $id ? $id : $pdo->lastInsertId();
    }

    public static function deleteJurnal($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM jurnal_kewirausahaan WHERE id_jurnal = ?");
        return $stmt->execute([$id]);
    }

    public static function getPresensi($pdo, $id_jurnal)
    {
        $stmt = $pdo->prepare("SELECT id_siswa, status FROM presensi_kewirausahaan WHERE id_jurnal = ?");
        $stmt->execute([$id_jurnal]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function savePresensi($pdo, $id_jurnal, $presensi_data)
    {
        $sql = "INSERT INTO presensi_kewirausahaan (id_jurnal, id_siswa, status) VALUES (?, ?, ?)
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
             WHERE pp.id_ta = ? AND mk.jenis_kegiatan = 'Kewirausahaan'
             ORDER BY mk.nama_kegiatan ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- TAHAPAN (STAGES) ---

    public static function getTahapan($pdo, $id_kew)
    {
        $stmt = $pdo->prepare("SELECT * FROM kewirausahaan_tahapan WHERE id_kewirausahaan = ? ORDER BY urutan ASC, id_tahapan ASC");
        $stmt->execute([$id_kew]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveTahapan($pdo, $data)
    {
        $id = $data['id_tahapan'] ?? null;
        $params = [
            ':id_kew' => $data['id_kewirausahaan'],
            ':nama' => $data['nama_tahapan'],
            ':tgl_mulai' => $data['tanggal_mulai'] ?? null,
            ':tgl_selesai' => $data['tanggal_selesai'] ?? null,
            ':status' => $data['status'] ?? 'Belum Mulai',
            ':ket' => $data['keterangan'] ?? null,
            ':urutan' => $data['urutan'] ?? 0
        ];

        if ($id) {
            $sql = "UPDATE kewirausahaan_tahapan SET id_kewirausahaan=:id_kew, nama_tahapan=:nama, tanggal_mulai=:tgl_mulai, tanggal_selesai=:tgl_selesai, status=:status, keterangan=:ket, urutan=:urutan WHERE id_tahapan=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO kewirausahaan_tahapan (id_kewirausahaan, nama_tahapan, tanggal_mulai, tanggal_selesai, status, keterangan, urutan) VALUES (:id_kew, :nama, :tgl_mulai, :tgl_selesai, :status, :ket, :urutan)";
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function deleteTahapan($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM kewirausahaan_tahapan WHERE id_tahapan = ?");
        return $stmt->execute([$id]);
    }

    public static function updateUrutan($pdo, $id, $urutan)
    {
        $stmt = $pdo->prepare("UPDATE kewirausahaan_tahapan SET urutan = ? WHERE id_tahapan = ?");
        return $stmt->execute([$urutan, $id]);
    }

    public static function initDefaultTahapan($pdo, $id_kew)
    {
        $default_stages = [
            ['nama' => 'Training Big Dream', 'urutan' => 1],
            ['nama' => 'Sharing Bisnis', 'urutan' => 2],
            ['nama' => 'Partner Bisnis', 'urutan' => 3],
            ['nama' => 'Mentoring', 'urutan' => 4],
            ['nama' => 'Market Day', 'urutan' => 5],
            ['nama' => 'Magang', 'urutan' => 6]
        ];

        $sql = "INSERT INTO kewirausahaan_tahapan (id_kewirausahaan, nama_tahapan, urutan) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($default_stages as $stage) {
            $stmt->execute([$id_kew, $stage['nama'], $stage['urutan']]);
        }
    }

    // --- PRODUK (PRODUCTS) ---

    public static function getProduk($pdo, $id_kew)
    {
        $stmt = $pdo->prepare("SELECT * FROM kewirausahaan_produk WHERE id_kewirausahaan = ? ORDER BY created_at DESC");
        $stmt->execute([$id_kew]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // --- GALERI ---

    public static function getGaleri($pdo, $id_kew)
    {
        $stmt = $pdo->prepare("SELECT * FROM kewirausahaan_galeri WHERE id_kewirausahaan = ? ORDER BY created_at DESC");
        $stmt->execute([$id_kew]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveGaleri($pdo, $id_kew, $file_path, $judul = null)
    {
        $sql = "INSERT INTO kewirausahaan_galeri (id_kewirausahaan, file_path, judul) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_kew, $file_path, $judul]);
    }

    public static function deleteGaleri($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT file_path FROM kewirausahaan_galeri WHERE id_galeri = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }

        $stmt = $pdo->prepare("DELETE FROM kewirausahaan_galeri WHERE id_galeri = ?");
        return $stmt->execute([$id]);
    }


    public static function saveProduk($pdo, $data)
    {
        $id = $data['id_produk'] ?? null;
        $params = [
            ':id_kew' => $data['id_kewirausahaan'],
            ':nama' => $data['nama_produk'],
            ':desk' => $data['deskripsi'] ?? null,
            ':harga' => $data['harga_jual'] ?? 0,
            ':stok' => $data['stok'] ?? 0,
            ':foto' => $data['foto_produk'] ?? null
        ];

        if ($id) {
            $sql = "UPDATE kewirausahaan_produk SET id_kewirausahaan=:id_kew, nama_produk=:nama, deskripsi=:desk, harga_jual=:harga, stok=:stok, foto_produk=COALESCE(:foto, foto_produk) WHERE id_produk=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO kewirausahaan_produk (id_kewirausahaan, nama_produk, deskripsi, harga_jual, stok, foto_produk) VALUES (:id_kew, :nama, :desk, :harga, :stok, :foto)";
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function deleteProduk($pdo, $id)
    {
        // Get file path to delete
        $stmt = $pdo->prepare("SELECT foto_produk FROM kewirausahaan_produk WHERE id_produk = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['foto_produk'] && file_exists(__DIR__ . '/../../' . $row['foto_produk'])) {
            unlink(__DIR__ . '/../../' . $row['foto_produk']);
        }

        $stmt = $pdo->prepare("DELETE FROM kewirausahaan_produk WHERE id_produk = ?");
        return $stmt->execute([$id]);
    }

    // --- KEUANGAN (FINANCIAL) ---

    public static function getKeuangan($pdo, $id_kew)
    {
        $stmt = $pdo->prepare("SELECT * FROM kewirausahaan_keuangan WHERE id_kewirausahaan = ? ORDER BY tanggal DESC, created_at DESC");
        $stmt->execute([$id_kew]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveKeuangan($pdo, $data)
    {
        $id = $data['id_transaksi'] ?? null;
        $params = [
            ':id_kew' => $data['id_kewirausahaan'],
            ':tgl' => $data['tanggal'],
            ':jenis' => $data['jenis'],
            ':ket' => $data['keterangan'] ?? null,
            ':jumlah' => $data['jumlah']
        ];

        if ($id) {
            $sql = "UPDATE kewirausahaan_keuangan SET id_kewirausahaan=:id_kew, tanggal=:tgl, jenis=:jenis, keterangan=:ket, jumlah=:jumlah WHERE id_transaksi=:id";
            $params[':id'] = $id;
        } else {
            $sql = "INSERT INTO kewirausahaan_keuangan (id_kewirausahaan, tanggal, jenis, keterangan, jumlah) VALUES (:id_kew, :tgl, :jenis, :ket, :jumlah)";
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function deleteKeuangan($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM kewirausahaan_keuangan WHERE id_transaksi = ?");
        return $stmt->execute([$id]);
    }

    public static function getSummary($pdo, $id_kew)
    {
        $sql = "SELECT 
                    SUM(CASE WHEN jenis = 'Modal' THEN jumlah ELSE 0 END) as total_modal,
                    SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
                    SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
                FROM kewirausahaan_keuangan 
                WHERE id_kewirausahaan = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kew]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $result['saldo'] = ($result['total_modal'] + $result['total_pemasukan']) - $result['total_pengeluaran'];
        return $result;
    }

    // --- AGENDA MANAGEMENT ---

    public static function getAgenda($pdo, $id_kew)
    {
        $sql = "SELECT * FROM kewirausahaan_agenda WHERE id_kewirausahaan = ? ORDER BY tanggal DESC, created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kew]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveAgenda($pdo, $data)
    {
        $id = $data['id_agenda'] ?? null;
        $params = [
            ':id_kew' => $data['id_kewirausahaan'],
            ':tgl' => $data['tanggal'],
            ':kegiatan' => $data['nama_kegiatan'],
            ':lokasi' => $data['lokasi'] ?? null,
            ':ket' => $data['keterangan'] ?? null,
            ':tipe' => $data['tipe'] ?? 'agenda'
        ];
        
        // Add file_path if provided
        if (isset($data['file_path'])) {
            $params[':file_path'] = $data['file_path'];
        }

        if ($id) {
            $sql = "UPDATE kewirausahaan_agenda SET id_kewirausahaan=:id_kew, tanggal=:tgl, nama_kegiatan=:kegiatan, lokasi=:lokasi, keterangan=:ket, tipe=:tipe";
            if (isset($data['file_path'])) $sql .= ", file_path=:file_path";
            $sql .= " WHERE id_agenda=:id";
            $params[':id'] = $id;
        } else {
             $cols = "id_kewirausahaan, tanggal, nama_kegiatan, lokasi, keterangan, tipe";
             $vals = ":id_kew, :tgl, :kegiatan, :lokasi, :ket, :tipe";
             if (isset($data['file_path'])) { $cols.=", file_path"; $vals.=", :file_path"; }
             $sql = "INSERT INTO kewirausahaan_agenda ($cols) VALUES ($vals)";
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public static function updateAgendaFile($pdo, $id, $file_path)
    {
        // Delete old file if exists
        $stmt = $pdo->prepare("SELECT file_path FROM kewirausahaan_agenda WHERE id_agenda = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }

        $stmt = $pdo->prepare("UPDATE kewirausahaan_agenda SET file_path = ? WHERE id_agenda = ?");
        return $stmt->execute([$file_path, $id]);
    }

    public static function deleteAgenda($pdo, $id)
    {
        // Delete file first
        $stmt = $pdo->prepare("SELECT file_path FROM kewirausahaan_agenda WHERE id_agenda = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(__DIR__ . '/../../' . $row['file_path'])) {
            unlink(__DIR__ . '/../../' . $row['file_path']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM kewirausahaan_agenda WHERE id_agenda = ?");
        return $stmt->execute([$id]);
    }

    public static function updateProkerFile($pdo, $id_kew, $file_path)
    {
        $stmt = $pdo->prepare("UPDATE kewirausahaan SET file_proker = ? WHERE id_kewirausahaan = ?");
        return $stmt->execute([$file_path, $id_kew]);
    }

    public static function deleteProkerFile($pdo, $id_kew)
    {
        $stmt = $pdo->prepare("SELECT file_proker FROM kewirausahaan WHERE id_kewirausahaan = ?");
        $stmt->execute([$id_kew]);
        $row = $stmt->fetch();
        if ($row && $row['file_proker'] && file_exists(__DIR__ . '/../../' . $row['file_proker'])) {
            unlink(__DIR__ . '/../../' . $row['file_proker']);
        }
        $stmt = $pdo->prepare("UPDATE kewirausahaan SET file_proker = NULL WHERE id_kewirausahaan = ?");
        return $stmt->execute([$id_kew]);
    }
}
