<?php

class CetakRaporModel {
    private $pdo;
    private static $sekolahCache = null;
    private static $subjectsCache = [];
    private static $bobotCache = [];
    private static $rekapCache = [];
    private static $mapelInfoCache = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getActiveTa() {
        return $this->pdo->query("SELECT * FROM tahun_ajaran WHERE status = 'Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    }

    public function getKelasByWaliKelas($id_guru, $id_ta) {
        $stmt = $this->pdo->prepare("
            SELECT k.id_kelas, k.nama_kelas, k.tingkat
            FROM penugasan_wali_kelas p
            JOIN kelas k ON k.id_kelas = p.id_kelas
            WHERE p.id_guru = ? AND p.id_ta = ? AND p.jenis_tugas = 'Wali Kelas'
            ORDER BY k.nama_kelas
        ");
        $stmt->execute([$id_guru, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllKelas($id_ta) {
        $stmt = $this->pdo->prepare("
            SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ?
            ORDER BY tingkat, nama_kelas
        ");
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isWaliKelas($id_guru, $id_kelas, $id_ta) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM penugasan_wali_kelas
            WHERE id_guru = ? AND id_kelas = ? AND id_ta = ? AND jenis_tugas = 'Wali Kelas'
        ");
        $stmt->execute([$id_guru, $id_kelas, $id_ta]);
        return $stmt->fetchColumn() > 0;
    }

    public function getSiswaByKelas($id_kelas, $id_ta, $semester = 1) {
        $stmt = $this->pdo->prepare("
            SELECT ps.id_penempatan, s.id_siswa, s.nama, s.nisn, s.nipd,
                   cwk.catatan, cwk.is_generated,
                   IF(cwk.id IS NOT NULL, 1, 0) AS has_catatan
            FROM penempatan_siswa ps
            JOIN siswa s ON s.id_siswa = ps.id_siswa
            LEFT JOIN catatan_wali_kelas cwk
                ON cwk.id_penempatan = ps.id_penempatan
                AND cwk.id_ta = ? AND cwk.semester = ?
            WHERE ps.id_kelas = ? AND ps.id_ta = ? AND ps.status_penempatan = 'Aktif'
            ORDER BY s.nama
        ");
        $stmt->execute([$id_ta, $semester, $id_kelas, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRaporData($id_penempatan, $id_ta, $semester = 1) {
        // --- Biodata + Kelas + Wali Kelas ---
        $stmt = $this->pdo->prepare("
            SELECT s.nama, s.nisn, s.nipd, s.nik, s.jk, s.tempat_lahir, s.tanggal_lahir,
                   k.nama_kelas, k.tingkat, k.id_kelas,
                   ta.nama_ta,
                   g.nama AS nama_wali_kelas, g.nuptk AS nuptk_wali_kelas,
                   ps.id_siswa
            FROM penempatan_siswa ps
            JOIN siswa s ON s.id_siswa = ps.id_siswa
            JOIN kelas k ON k.id_kelas = ps.id_kelas
            JOIN tahun_ajaran ta ON ta.id_ta = ps.id_ta
            LEFT JOIN penugasan_wali_kelas pwk ON (pwk.id_kelas = ps.id_kelas AND pwk.id_ta = ps.id_ta AND pwk.jenis_tugas = 'Wali Kelas')
            LEFT JOIN guru g ON g.id_guru = pwk.id_guru
            WHERE ps.id_penempatan = ?
        ");
        $stmt->execute([$id_penempatan]);
        $biodata = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$biodata) return null;

        $id_kelas = $biodata['id_kelas'];
        $id_siswa = $biodata['id_siswa'];

        // --- Profil Sekolah (Cached) ---
        if (self::$sekolahCache === null) {
            self::$sekolahCache = $this->pdo->query("SELECT * FROM profil_sekolah LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        $sekolah = self::$sekolahCache;

        // --- Fase dari tingkat ---
        $fase_map = ['X' => 'E', 'XI' => 'F', 'XII' => 'F'];
        $fase = $fase_map[$biodata['tingkat']] ?? 'E';

        // --- Nilai per Mapel (Cached per class) ---
        require_once __DIR__ . '/RekapNilaiModel.php';
        $subKey = $id_kelas . '_' . $id_ta;
        if (!isset(self::$subjectsCache[$subKey])) {
            self::$subjectsCache[$subKey] = RekapNilaiModel::getSubjectsInClass($this->pdo, $id_kelas, $id_ta);
        }
        $subjects = self::$subjectsCache[$subKey];

        $nilai_grouped = [];
        $urutan_kat = [
            'Mata Pelajaran Wajib'   => 1,
            'Mata Pelajaran Pilihan' => 2,
            'Muatan Lokal'           => 3,
            'Mulok Yayasan'          => 4,
        ];

        foreach ($subjects as $sub) {
            $id_gm = $sub['id_guru_mapel'];
            $gmKey = $id_gm . '_' . $id_kelas;
            
            if (!isset(self::$bobotCache[$gmKey])) {
                self::$bobotCache[$gmKey] = RekapNilaiModel::getBobotConfig($this->pdo, $id_gm, $id_kelas);
            }
            $bobot = self::$bobotCache[$gmKey];
            $limits = $bobot;

            $rekapKey = $id_kelas . '_' . $id_gm . '_' . $id_ta;
            if (!isset(self::$rekapCache[$rekapKey])) {
                self::$rekapCache[$rekapKey] = RekapNilaiModel::getRekapData($this->pdo, $id_kelas, $id_gm, $id_ta, $limits);
            }
            $rekap = self::$rekapCache[$rekapKey];

            $r = $rekap[$id_penempatan] ?? null;
            if (!$r) continue;

            $na = ($r['sikap'] ?? 0) * ($bobot['sikap']/100)
                + ($r['lms']      ?? 0) * ($bobot['lms']/100)
                + ($r['formatif'] ?? 0) * ($bobot['formatif']/100)
                + ($r['sumatif_lm'] ?? 0) * ($bobot['sumatif_lm']/100)
                + ($r['sts']      ?? 0) * ($bobot['sts']/100)
                + ($r['sas']      ?? 0) * ($bobot['sas']/100);

            // Get kategori from mapel (Cached)
            if (!isset(self::$mapelInfoCache[$id_gm])) {
                $stmtM = $this->pdo->prepare("SELECT m.kategori_mapel, m.urutan FROM mapel m JOIN guru_mapel gm ON gm.id_mapel = m.id_mapel WHERE gm.id_guru_mapel = ?");
                $stmtM->execute([$id_gm]);
                self::$mapelInfoCache[$id_gm] = $stmtM->fetch(PDO::FETCH_ASSOC) ?: ['kategori_mapel' => 'Mata Pelajaran Wajib', 'urutan' => 0];
            }
            $mapelInfo = self::$mapelInfoCache[$id_gm];
            $kat = $mapelInfo['kategori_mapel'] ?? 'Mata Pelajaran Wajib';

            $nilai_grouped[$kat][] = [
                'nama_mapel'      => $sub['nama_mapel'],
                'nilai_akhir'     => round($na, 0),
                'deskripsi_rapor' => $r['deskripsi_rapor'] ?? '',
            ];
        }

        // Sort kategori
        uksort($nilai_grouped, fn($a,$b) => ($urutan_kat[$a]??9) - ($urutan_kat[$b]??9));

        // --- Sikap ---
        $stmt = $this->pdo->prepare("
            SELECT ns.predikat, ns.deskripsi_sikap
            FROM nilai_sikap ns
            JOIN agenda_penilaian_sikap a ON a.id_agenda = ns.id_agenda
            WHERE ns.id_penempatan = ? AND a.id_ta = ?
            ORDER BY a.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$id_penempatan, $id_ta]);
        $sikap = $stmt->fetch(PDO::FETCH_ASSOC);

        // --- Ekskul & Kokurikuler ---
        $stmt = $this->pdo->prepare("
            SELECT e.nama_ekskul, e.kategori, ae.nilai, ae.predikat, ae.deskripsi
            FROM anggota_ekskul ae
            JOIN ekstrakurikuler e ON e.id_ekskul = ae.id_ekskul
            WHERE ae.id_siswa = ? AND ae.id_ta = ?
            ORDER BY e.kategori, e.nama_ekskul
        ");
        $stmt->execute([$id_siswa, $id_ta]);
        $ekskul_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ekskul     = array_values(array_filter($ekskul_raw, fn($e) => $e['kategori'] === 'Ekstrakurikuler'));
        $kokurikuler = array_values(array_filter($ekskul_raw, fn($e) => $e['kategori'] === 'Kokulikuler'));

        // --- Kehadiran (from absensi_siswa_piket) ---
        $stmt = $this->pdo->prepare("
            SELECT
                SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END) AS sakit,
                SUM(CASE WHEN status='Izin'  THEN 1 ELSE 0 END) AS izin,
                SUM(CASE WHEN status='Alpa'  THEN 1 ELSE 0 END) AS alpa
            FROM absensi_siswa_piket
            WHERE id_siswa = ? AND id_ta = ?
        ");
        $stmt->execute([$id_siswa, $id_ta]);
        $kehadiran = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['sakit'=>0,'izin'=>0,'alpa'=>0];

        // --- Catatan Wali Kelas ---
        $stmt = $this->pdo->prepare("
            SELECT catatan, is_generated FROM catatan_wali_kelas
            WHERE id_penempatan = ? AND id_ta = ? AND semester = ?
        ");
        $stmt->execute([$id_penempatan, $id_ta, $semester]);
        $catatan = $stmt->fetch(PDO::FETCH_ASSOC);

        return compact('biodata','sekolah','fase','semester','nilai_grouped','sikap','ekskul','kokurikuler','kehadiran','catatan');
    }

    public function generateCatatanTemplate($id_penempatan, $id_ta, $semester = 1) {
        // Nama siswa
        $stmt = $this->pdo->prepare("SELECT s.nama, k.id_kelas FROM penempatan_siswa ps JOIN siswa s ON s.id_siswa=ps.id_siswa JOIN kelas k ON k.id_kelas=ps.id_kelas WHERE ps.id_penempatan=?");
        $stmt->execute([$id_penempatan]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        $nama = $info['nama'] ?? 'Ananda';
        $id_kelas = $info['id_kelas'];
        $panggilan = explode(' ', $nama)[0];

        // Nilai statistik dari RekapNilaiModel
        require_once __DIR__ . '/RekapNilaiModel.php';
        $subjects = RekapNilaiModel::getSubjectsInClass($this->pdo, $id_kelas, $id_ta);
        $all_na = []; $mapel_names = [];
        foreach ($subjects as $sub) {
            $id_gm = $sub['id_guru_mapel'];
            $bobot = RekapNilaiModel::getBobotConfig($this->pdo, $id_gm, $id_kelas);
            $limits = $bobot; // Limits are included in the bobot config
            $rekap = RekapNilaiModel::getRekapData($this->pdo, $id_kelas, $id_gm, $id_ta, $limits);
            $r = $rekap[$id_penempatan] ?? null;
            if (!$r) continue;
            $na = ($r['sikap']??0)*($bobot['sikap']/100) + ($r['lms']??0)*($bobot['lms']/100)
                + ($r['formatif']??0)*($bobot['formatif']/100) + ($r['sumatif_lm']??0)*($bobot['sumatif_lm']/100)
                + ($r['sts']??0)*($bobot['sts']/100) + ($r['sas']??0)*($bobot['sas']/100);
            $all_na[$sub['nama_mapel']] = round($na, 1);
        }

        $rata_na = !empty($all_na) ? round(array_sum($all_na)/count($all_na), 1) : 0;
        arsort($all_na);
        $mapel_terbaik = array_key_first($all_na) ?? '';
        asort($all_na);
        $mapel_perlu   = array_key_first($all_na) ?? '';

        // Sikap
        $stmt = $this->pdo->prepare("SELECT ns.predikat FROM nilai_sikap ns JOIN agenda_penilaian_sikap a ON a.id_agenda=ns.id_agenda WHERE ns.id_penempatan=? AND a.id_ta=? ORDER BY a.created_at DESC LIMIT 1");
        $stmt->execute([$id_penempatan, $id_ta]);
        $sikap = $stmt->fetch(PDO::FETCH_ASSOC);
        $predikat = $sikap['predikat'] ?? 'B';

        // Kehadiran
        $id_siswa_stmt = $this->pdo->prepare("SELECT id_siswa FROM penempatan_siswa WHERE id_penempatan=?");
        $id_siswa_stmt->execute([$id_penempatan]);
        $id_siswa = $id_siswa_stmt->fetchColumn();
        $stmt = $this->pdo->prepare("SELECT SUM(CASE WHEN status='Alpa' THEN 1 ELSE 0 END) AS alpa FROM absensi_siswa_piket WHERE id_siswa=? AND id_ta=?");
        $stmt->execute([$id_siswa, $id_ta]);
        $alpa = $stmt->fetchColumn() ?: 0;

        // Ekskul
        $stmt = $this->pdo->prepare("SELECT e.nama_ekskul FROM anggota_ekskul ae JOIN ekstrakurikuler e ON e.id_ekskul=ae.id_ekskul WHERE ae.id_siswa=? AND ae.id_ta=? AND e.kategori='Ekstrakurikuler'");
        $stmt->execute([$id_siswa, $id_ta]);
        $ekskulList = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Build narasi
        if ($rata_na >= 85)      $kalimat_na = "{$panggilan} menunjukkan capaian akademik yang sangat memuaskan dengan rata-rata nilai {$rata_na}";
        elseif ($rata_na >= 75)  $kalimat_na = "{$panggilan} menunjukkan perkembangan belajar yang baik dengan rata-rata nilai {$rata_na}";
        elseif ($rata_na >= 65)  $kalimat_na = "{$panggilan} menunjukkan perkembangan belajar yang cukup dengan rata-rata nilai {$rata_na}";
        else                      $kalimat_na = "{$panggilan} memerlukan bimbingan lebih intensif dalam kegiatan pembelajaran";

        $kalimat_mapel = '';
        if ($mapel_terbaik) $kalimat_mapel = ", dengan capaian terbaik pada {$mapel_terbaik}";
        if ($mapel_perlu && $mapel_perlu !== $mapel_terbaik) $kalimat_mapel .= " dan perlu peningkatan pada {$mapel_perlu}";

        $sikap_narasi = [
            'A' => "Sikap dan karakter {panggilan} sangat baik, mencerminkan profil pelajar yang berakhlak mulia.",
            'B' => "Sikap dan perilaku {panggilan} secara umum baik dan sesuai harapan sekolah.",
            'C' => "Sikap dan perilaku {panggilan} cukup baik dan terus menunjukkan perkembangan.",
            'D' => "{panggilan} masih memerlukan pembinaan lebih intensif terkait sikap dan perilaku.",
        ];
        $kalimat_sikap = str_replace('{panggilan}', $panggilan, $sikap_narasi[$predikat] ?? $sikap_narasi['B']);

        $kalimat_kehadiran = '';
        if ($alpa >= 5)     $kalimat_kehadiran = " Kehadiran {$panggilan} perlu mendapat perhatian dari orang tua/wali murid.";
        elseif ($alpa >= 3) $kalimat_kehadiran = " Diharapkan kehadiran {$panggilan} dapat lebih ditingkatkan.";

        $kalimat_ekskul = '';
        if (!empty($ekskulList)) {
            $kalimat_ekskul = " {$panggilan} aktif mengikuti ekstrakurikuler " . implode(', ', $ekskulList) . ".";
        }

        return trim("{$kalimat_na}{$kalimat_mapel}. {$kalimat_sikap}{$kalimat_kehadiran}{$kalimat_ekskul}");
    }

    public function saveCatatan($id_penempatan, $id_ta, $semester, $catatan, $is_generated = 0) {
        $stmt = $this->pdo->prepare("
            INSERT INTO catatan_wali_kelas (id_penempatan, id_ta, semester, catatan, is_generated)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE catatan=VALUES(catatan), is_generated=VALUES(is_generated)
        ");
        return $stmt->execute([$id_penempatan, $id_ta, $semester, $catatan, $is_generated]);
    }
}
