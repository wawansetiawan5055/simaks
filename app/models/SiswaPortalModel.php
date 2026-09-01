<?php
/**
 * SiswaPortalModel.php
 * Model khusus untuk fitur-fitur portal siswa (read-only dari database SIMAKS).
 */
class SiswaPortalModel
{
    // =====================================================================
    // JADWAL
    // =====================================================================

    /**
     * Ambil jadwal mingguan berdasarkan kelas siswa yang login (otomatis merge jam berurutan).
     */
    public static function getJadwalByKelas(PDO $pdo, int $id_kelas, int $id_ta): array
    {
        $hari_urutan = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $jadwal = [];

        // Cek jenis kelas
        $stmt_jk = $pdo->prepare("SELECT jenis_kelas FROM kelas WHERE id_kelas = ?");
        $stmt_jk->execute([$id_kelas]);
        $jenis_kelas = $stmt_jk->fetchColumn() ?: 'reguler';

        foreach ($hari_urutan as $hari) {
            $sql = "SELECT 
                        jp.urutan, jp.jam_mulai, jp.jam_selesai, jp.label_jam_ke, jp.jenis_kegiatan AS jenis_jam,
                        COALESCE(mk.nama_kegiatan, jp.nama_kegiatan_custom, 'KBM') AS nama_kegiatan,
                        COALESCE(mk.jenis_kegiatan, jp.jenis_kegiatan) AS jenis_kegiatan,
                        m.id_mapel, m.nama_mapel, g.nama AS nama_guru, jm.mode_kbm
                    FROM jam_pelajaran jp
                    LEFT JOIN master_kegiatan mk ON jp.id_kegiatan = mk.id_kegiatan
                    LEFT JOIN jadwal_mengajar jm ON jp.id_jam = jm.id_jam AND jm.hari_kbm = ? AND jm.id_kelas = ?
                    LEFT JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel AND gm.id_ta = ?
                    LEFT JOIN mapel m ON gm.id_mapel = m.id_mapel
                    LEFT JOIN guru g ON gm.id_guru = g.id_guru
                    WHERE FIND_IN_SET(?, jp.hari_pelaksanaan)
                    ORDER BY jp.urutan ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$hari, $id_kelas, $id_ta, $hari]);
            $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($slots)) {
                // Merge jam berurutan dengan mapel yang sama (mendukung Team Teaching / Multi-Guru)
                $merged = [];
                $current = null;

                foreach ($slots as $slot) {
                    $is_kbm = (($slot['jenis_jam'] ?? '') === 'KBM' || ($slot['jenis_kegiatan'] ?? '') === 'KBM');
                    if ($is_kbm) {
                        if (empty($slot['nama_mapel'])) continue; // Slot KBM kosong tidak ditampilkan di portal siswa
                    } else {
                        // Jika kelas PJJ, sembunyikan kegiatan Non-KBM reguler sekolah induk
                        if ($jenis_kelas === 'pjj') {
                            continue;
                        }
                    }

                    $signature = $is_kbm
                        ? ('KBM|' . ($slot['nama_mapel'] ?? '') . '|' . ($slot['mode_kbm'] ?? 'offline'))
                        : ('NON_KBM|' . ($slot['nama_kegiatan'] ?? '') . '|' . ($slot['jenis_kegiatan'] ?? ''));

                    if ($current === null) {
                        $current = $slot;
                        $current['signature'] = $signature;
                        $current['jp_count'] = 1;
                        $current['jam_mulai'] = $slot['jam_mulai'];
                        $current['jam_selesai'] = $slot['jam_selesai'];
                        $current['id_mapel'] = $slot['id_mapel'] ?? null;
                        $current['mode_kbm'] = $slot['mode_kbm'] ?? 'offline';
                        $current['guru_list'] = $slot['nama_guru'] ? [$slot['nama_guru']] : [];
                    } else {
                        if ($current['signature'] === $signature) {
                            $current['jam_selesai'] = $slot['jam_selesai'];
                            $current['jp_count']++;
                            if ($slot['nama_guru'] && !in_array($slot['nama_guru'], $current['guru_list'])) {
                                $current['guru_list'][] = $slot['nama_guru'];
                            }
                        } else {
                            $current['nama_guru'] = !empty($current['guru_list']) ? implode(', ', $current['guru_list']) : '-';
                            $merged[] = $current;

                            $current = $slot;
                            $current['signature'] = $signature;
                            $current['jp_count'] = 1;
                            $current['jam_mulai'] = $slot['jam_mulai'];
                            $current['jam_selesai'] = $slot['jam_selesai'];
                            $current['id_mapel'] = $slot['id_mapel'] ?? null;
                            $current['mode_kbm'] = $slot['mode_kbm'] ?? 'offline';
                            $current['guru_list'] = $slot['nama_guru'] ? [$slot['nama_guru']] : [];
                        }
                    }
                }

                if ($current !== null) {
                    $current['nama_guru'] = !empty($current['guru_list']) ? implode(', ', $current['guru_list']) : '-';
                    $merged[] = $current;
                }

                $jadwal[$hari] = $merged;
            }
        }

        return $jadwal;
    }

    /**
     * Ambil kelas siswa berdasarkan id_siswa dan id_ta aktif.
     */
    public static function getKelasSiswa(PDO $pdo, int $id_siswa, int $id_ta): ?array
    {
        $stmt = $pdo->prepare(
            "SELECT k.id_kelas, k.nama_kelas, k.tingkat, k.jenis_kelas
             FROM kelas k
             JOIN penempatan_siswa p ON k.id_kelas = p.id_kelas
             WHERE p.id_siswa = ? AND k.id_ta = ?
             LIMIT 1"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // =====================================================================
    // NILAI
    // =====================================================================

    /**
     * Ambil rekap nilai sumatif siswa per mata pelajaran.
     */
    public static function getNilaiSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                m.nama_mapel, m.kode_mapel,
                ps.jenis_sumatif AS semester, ps.nama_penilaian AS tipe_penilaian, ns.nilai, ns.deskripsi_capaian AS created_at,
                g.nama AS nama_guru
             FROM nilai_sumatif ns
             JOIN penilaian_sumatif ps ON ns.id_sumatif = ps.id_sumatif
             JOIN penempatan_siswa pt ON ns.id_penempatan = pt.id_penempatan
             JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel
             JOIN mapel m ON gm.id_mapel = m.id_mapel
             LEFT JOIN guru g ON gm.id_guru = g.id_guru
             WHERE pt.id_siswa = ? AND ps.id_ta = ?
             ORDER BY m.nama_mapel ASC, ps.tanggal_penilaian ASC"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kelompokkan nilai per mata pelajaran (Legacy).
     */
    public static function getNilaiGrouped(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $rows = self::getNilaiSiswa($pdo, $id_siswa, $id_ta);
        $grouped = [];
        foreach ($rows as $r) {
            $mapel = $r['nama_mapel'];
            if (!isset($grouped[$mapel])) {
                $grouped[$mapel] = [
                    'nama_mapel'  => $r['nama_mapel'],
                    'kode_mapel'  => $r['kode_mapel'],
                    'nama_guru'   => $r['nama_guru'],
                    'nilai_list'  => [],
                    'rata_rata'   => 0,
                ];
            }
            $grouped[$mapel]['nilai_list'][] = [
                'semester'      => $r['semester'],
                'tipe'          => $r['tipe_penilaian'],
                'nilai'         => $r['nilai'],
            ];
        }

        // Hitung rata-rata per mapel
        foreach ($grouped as &$item) {
            $vals = array_column($item['nilai_list'], 'nilai');
            $item['rata_rata'] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 1) : 0;
        }

        return array_values($grouped);
    }

    /**
     * Ambil data nilai komprehensif terpadu (Formatif TP, Sumatif, LMS Tugas & CBT Ujian).
     */
    public static function getNilaiKomprehensif(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        // 1. Ambil Penilaian Formatif TP (Kurikulum Merdeka)
        $q_formatif = $pdo->prepare("
            SELECT 
                m.id_mapel, m.nama_mapel, m.kode_mapel, COALESCE(m.kktp, 75) AS kktp,
                g.nama AS nama_guru,
                COALESCE(tp.kode_tp, '-') AS kode_tp,
                COALESCE(tp.deskripsi_tp, 'Tujuan Pembelajaran') AS deskripsi_tp,
                n.nilai, n.deskripsi, n.keterangan, n.jenis_penilaian
            FROM nilai n
            JOIN penempatan_siswa ps ON n.id_penempatan = ps.id_penempatan
            JOIN guru_mapel gm ON n.id_guru_mapel = gm.id_guru_mapel
            JOIN mapel m ON gm.id_mapel = m.id_mapel
            LEFT JOIN tujuan_pembelajaran tp ON n.id_tp = tp.id_tp
            LEFT JOIN guru g ON gm.id_guru = g.id_guru
            WHERE ps.id_siswa = ? AND ps.id_ta = ?
            ORDER BY m.nama_mapel ASC, tp.kode_tp ASC
        ");
        $q_formatif->execute([$id_siswa, $id_ta]);
        $rows_formatif = $q_formatif->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil Penilaian Sumatif (Lingkup Materi & Akhir Semester SAS/SAT)
        $q_sumatif = $pdo->prepare("
            SELECT 
                m.id_mapel, m.nama_mapel, m.kode_mapel, COALESCE(m.kktp, 75) AS kktp,
                g.nama AS nama_guru,
                ps.nama_penilaian, ps.jenis_sumatif, ps.tanggal_penilaian,
                ns.nilai, ns.deskripsi_capaian
            FROM nilai_sumatif ns
            JOIN penilaian_sumatif ps ON ns.id_sumatif = ps.id_sumatif
            JOIN penempatan_siswa pt ON ns.id_penempatan = pt.id_penempatan
            JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel
            JOIN mapel m ON gm.id_mapel = m.id_mapel
            LEFT JOIN guru g ON gm.id_guru = g.id_guru
            WHERE pt.id_siswa = ? AND ps.id_ta = ?
            ORDER BY m.nama_mapel ASC, ps.tanggal_penilaian ASC
        ");
        $q_sumatif->execute([$id_siswa, $id_ta]);
        $rows_sumatif = $q_sumatif->fetchAll(PDO::FETCH_ASSOC);

        // 3. Ambil Penilaian Tugas & Modul LMS
        $q_lms = $pdo->prepare("
            SELECT 
                m.id_mapel, m.nama_mapel, m.kode_mapel, COALESCE(m.kktp, 75) AS kktp,
                g.nama AS nama_guru,
                lt.judul_tugas, lt.deadline,
                lp.nilai, lp.catatan_guru, lp.tgl_upload
            FROM lms_pengumpulan lp
            JOIN lms_tugas lt ON lp.id_tugas = lt.id_tugas
            JOIN mapel m ON lt.id_mapel = m.id_mapel
            LEFT JOIN guru g ON lt.id_guru = g.id_guru
            WHERE lp.id_siswa = ? AND lp.nilai IS NOT NULL
            ORDER BY m.nama_mapel ASC, lp.tgl_upload DESC
        ");
        $q_lms->execute([$id_siswa]);
        $rows_lms = $q_lms->fetchAll(PDO::FETCH_ASSOC);

        // 4. Ambil Penilaian Ujian CBT
        $q_cbt = $pdo->prepare("
            SELECT 
                COALESCE(m.id_mapel, 0) AS id_mapel,
                COALESCE(m.nama_mapel, cb.nama_bank, 'Ujian CBT') AS nama_mapel,
                COALESCE(m.kode_mapel, cb.kode_bank, 'CBT') AS kode_mapel,
                COALESCE(m.kktp, cj.passing_grade, 75) AS kktp,
                cj.nama_ujian, cj.passing_grade,
                cn.nilai_akhir, cn.nilai_pg, cn.nilai_essay, cn.status_lulus, cn.dihitung_pada
            FROM cbt_nilai cn
            JOIN cbt_jadwal cj ON cn.id_jadwal = cj.id_jadwal
            LEFT JOIN cbt_paket cp ON cj.id_paket = cp.id_paket
            LEFT JOIN cbt_bank_soal cb ON cp.id_bank = cb.id_bank
            LEFT JOIN mapel m ON cb.id_mapel = m.id_mapel
            WHERE cn.id_siswa = ?
            ORDER BY cn.dihitung_pada DESC
        ");
        $q_cbt->execute([$id_siswa]);
        $rows_cbt = $q_cbt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Ambil data penempatan dan kelas siswa
        require_once __DIR__ . '/RekapNilaiModel.php';
        
        $stmt_pen = $pdo->prepare("SELECT id_penempatan, id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND id_ta = ? AND status_penempatan = 'Aktif' LIMIT 1");
        $stmt_pen->execute([$id_siswa, $id_ta]);
        $pen_data = $stmt_pen->fetch(PDO::FETCH_ASSOC);
        $id_penempatan = (int)($pen_data['id_penempatan'] ?? 0);
        $id_kelas = (int)($pen_data['id_kelas'] ?? 0);

        // Ambil daftar seluruh mapel resmi di kelas siswa
        $subjects_in_class = $id_kelas ? RekapNilaiModel::getSubjectsInClass($pdo, $id_kelas, $id_ta) : [];
        $mapel_map = [];

        foreach ($subjects_in_class as $sub) {
            $id_gm = (int)$sub['id_guru_mapel'];
            $bobot = RekapNilaiModel::getBobotConfig($pdo, $id_gm, $id_kelas);
            $limits = [
                'limit_tp_tinggi' => $bobot['limit_tp_tinggi'],
                'limit_tp_rendah' => $bobot['limit_tp_rendah']
            ];

            $rekap_sub = RekapNilaiModel::getRekapData($pdo, $id_kelas, $id_gm, $id_ta, $limits);
            $student_r = $rekap_sub[$id_penempatan] ?? null;

            $val_sikap    = $student_r['sikap'] ?? null;
            $val_lms      = $student_r['lms'] ?? null;
            $val_formatif = $student_r['formatif'] ?? null;
            $val_lm       = $student_r['sumatif_lm'] ?? null;
            $val_sts      = $student_r['sts'] ?? null;
            $val_sas      = $student_r['sas'] ?? null;
            $deskripsi    = $student_r['deskripsi_rapor'] ?? '';

            // Hitung Nilai Akhir (NA) resmi sesuai persentase pembobotan guru (Total 100%)
            $na = (($val_sikap ?? 0) * ($bobot['sikap'] / 100)) + 
                  (($val_lms ?? 0) * ($bobot['lms'] / 100)) + 
                  (($val_formatif ?? 0) * ($bobot['formatif'] / 100)) + 
                  (($val_lm ?? 0) * ($bobot['sumatif_lm'] / 100)) + 
                  (($val_sts ?? 0) * ($bobot['sts'] / 100)) + 
                  (($val_sas ?? 0) * ($bobot['sas'] / 100));

            $has_any_score = ($val_sikap !== null || $val_lms !== null || $val_formatif !== null || $val_lm !== null || $val_sts !== null || $val_sas !== null);
            $kktp = (float)($sub['kktp'] ?? 75);

            // Detail rincian per mapel
            $formatif_details = array_values(array_filter($rows_formatif, fn($f) => $f['nama_mapel'] === $sub['nama_mapel']));
            $sumatif_details  = array_values(array_filter($rows_sumatif, fn($s) => $s['nama_mapel'] === $sub['nama_mapel']));
            $lms_details      = array_values(array_filter($rows_lms, fn($l) => $l['nama_mapel'] === $sub['nama_mapel']));
            $cbt_details      = array_values(array_filter($rows_cbt, fn($c) => $c['nama_mapel'] === $sub['nama_mapel']));

            $final_na = $has_any_score ? round($na, 2) : null;

            // Predikat & Ketuntasan
            $predikat = '-';
            $is_tuntas = null;
            if ($final_na !== null) {
                if ($final_na >= 90) $predikat = 'A';
                elseif ($final_na >= 80) $predikat = 'B';
                elseif ($final_na >= 70) $predikat = 'C';
                else $predikat = 'D';

                $is_tuntas = ($final_na >= $kktp);
            }

            $mapel_map[$sub['nama_mapel']] = [
                'id_guru_mapel' => $id_gm,
                'nama_mapel'    => $sub['nama_mapel'],
                'kode_mapel'    => $sub['kode_mapel'] ?? '',
                'nama_guru'     => $sub['nama_guru'] ?? '-',
                'kktp'          => $kktp,
                'bobot'         => $bobot,
                'val_sikap'     => $val_sikap,
                'val_lms'       => $val_lms,
                'val_formatif'  => $val_formatif,
                'val_sumatif_lm'=> $val_lm,
                'val_sts'       => $val_sts,
                'val_sas'       => $val_sas,
                'nilai_akhir'   => $final_na,
                'deskripsi_rapor'=> $deskripsi,
                'predikat'      => $predikat,
                'is_tuntas'     => $is_tuntas,
                'formatif'      => $formatif_details,
                'sumatif'       => $sumatif_details,
                'lms'           => $lms_details,
                'cbt'           => $cbt_details,
            ];
        }

        // Summary counts
        $all_final_scores = [];
        $tuntas_count = 0;
        foreach ($mapel_map as $m) {
            if ($m['nilai_akhir'] !== null) {
                $all_final_scores[] = $m['nilai_akhir'];
                if ($m['is_tuntas']) $tuntas_count++;
            }
        }
        $global_avg = count($all_final_scores) > 0 ? round(array_sum($all_final_scores) / count($all_final_scores), 1) : 0;

        return [
            'mapel_list' => array_values($mapel_map),
            'rows_formatif' => $rows_formatif,
            'rows_sumatif' => $rows_sumatif,
            'rows_lms' => $rows_lms,
            'rows_cbt' => $rows_cbt,
            'summary' => [
                'total_mapel' => count($mapel_map),
                'global_avg' => $global_avg,
                'total_formatif' => count($rows_formatif),
                'total_sumatif' => count($rows_sumatif),
                'total_lms' => count($rows_lms),
                'total_cbt' => count($rows_cbt),
                'tuntas_count' => $tuntas_count,
                'belum_tuntas_count' => count($all_final_scores) - $tuntas_count
            ]
        ];
    }

    // =====================================================================
    // ABSENSI
    // =====================================================================

    /**
     * Rekap absensi piket (kehadiran kelas) per bulan.
     */
    public static function getAbsensiPiketSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                DATE_FORMAT(ap.tanggal, '%Y-%m') AS bulan_label,
                DATE_FORMAT(ap.tanggal, '%M %Y') AS bulan_nama,
                SUM(CASE WHEN ap.status = 'Hadir' THEN 1 ELSE 0 END) AS hadir,
                SUM(CASE WHEN ap.status = 'Sakit' THEN 1 ELSE 0 END) AS sakit,
                SUM(CASE WHEN ap.status = 'Izin' THEN 1 ELSE 0 END) AS izin,
                SUM(CASE WHEN ap.status = 'Alpa' THEN 1 ELSE 0 END) AS alpa,
                COUNT(*) AS total
             FROM absensi_siswa_piket ap
             JOIN kelas k ON ap.id_kelas = k.id_kelas
             WHERE ap.id_siswa = ? AND k.id_ta = ?
             GROUP BY bulan_label
             ORDER BY bulan_label ASC"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Rekap absensi per mata pelajaran.
     */
    public static function getAbsensiMapelSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                m.nama_mapel,
                m.kode_mapel,
                g.nama AS nama_guru,
                SUM(CASE WHEN am.status = 'Hadir' THEN 1 ELSE 0 END) AS hadir,
                SUM(CASE WHEN am.status = 'Sakit' THEN 1 ELSE 0 END) AS sakit,
                SUM(CASE WHEN am.status = 'Izin' THEN 1 ELSE 0 END) AS izin,
                SUM(CASE WHEN am.status = 'Alpa' THEN 1 ELSE 0 END) AS alpa,
                COUNT(*) AS total,
                ROUND(SUM(CASE WHEN am.status = 'Hadir' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) AS pct_hadir
             FROM absensi_siswa_mapel am
             JOIN guru_mapel gm ON am.id_guru_mapel = gm.id_guru_mapel
             JOIN mapel m ON gm.id_mapel = m.id_mapel
             LEFT JOIN guru g ON gm.id_guru = g.id_guru
             WHERE am.id_siswa = ? AND gm.id_ta = ?
             GROUP BY m.id_mapel, m.nama_mapel, m.kode_mapel, g.nama
             ORDER BY m.nama_mapel ASC"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Riwayat absensi harian kelas (piket) terbaru.
     */
    public static function getRiwayatHarianPiket(PDO $pdo, int $id_siswa, int $id_ta, int $limit = 50): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                ap.id_absensi, ap.tanggal, ap.status, ap.keterangan,
                k.nama_kelas, g.nama AS dicatat_oleh
             FROM absensi_siswa_piket ap
             JOIN kelas k ON ap.id_kelas = k.id_kelas
             LEFT JOIN guru g ON ap.id_guru_piket = g.id_guru
             WHERE ap.id_siswa = ? AND ap.id_ta = ?
             ORDER BY ap.tanggal DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $id_siswa, PDO::PARAM_INT);
        $stmt->bindValue(2, $id_ta, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================================
    // TAGIHAN / KEUANGAN
    // =====================================================================

    /**
     * Ambil tagihan SPP dan status pembayaran siswa.
     */
    public static function getTagihanSiswa(PDO $pdo, int $id_siswa): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                t.id_tagihan, j.nama_jenis AS nama_tagihan, t.jumlah_tagihan,
                t.tanggal_jatuh_tempo, t.status,
                (t.jumlah_tagihan - t.sisa_tagihan) AS total_dibayar,
                t.sisa_tagihan
             FROM keuangan_tagihan_siswa t
             JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
             WHERE t.id_siswa = ?
             ORDER BY t.tanggal_jatuh_tempo DESC, t.periode DESC"
        );
        $stmt->execute([$id_siswa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================================
    // PENGEMBANGAN DIRI
    // =====================================================================

    /**
     * Rekap pembiasaan akhlak mulia siswa.
     */
    /**
     * Rekap pembiasaan akhlak mulia siswa.
     */
    public static function getPembiasaanSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                p.nama_kegiatan,
                (SELECT COUNT(*) FROM jurnal_pembiasaan jp WHERE jp.id_pembiasaan = p.id_pembiasaan) as total_pertemuan,
                (SELECT COUNT(*) FROM presensi_pembiasaan pp JOIN jurnal_pembiasaan jp2 ON pp.id_jurnal = jp2.id_jurnal WHERE jp2.id_pembiasaan = p.id_pembiasaan AND pp.id_siswa = ? AND pp.status = 'Hadir') as total_hadir,
                (SELECT MAX(jp3.tanggal) FROM presensi_pembiasaan pp2 JOIN jurnal_pembiasaan jp3 ON pp2.id_jurnal = jp3.id_jurnal WHERE jp3.id_pembiasaan = p.id_pembiasaan AND pp2.id_siswa = ? AND pp2.status = 'Hadir') as terakhir_hadir
             FROM pembiasaan p
             JOIN anggota_pembiasaan a ON p.id_pembiasaan = a.id_pembiasaan
             WHERE a.id_siswa = ? AND a.id_ta = ?"
        );
        $stmt->execute([$id_siswa, $id_siswa, $id_siswa, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Progress tahfidz siswa.
     */
    public static function getTahfidzSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                s.tanggal, s.jenis_setoran, rs.nama_surah, s.ayat_awal, s.ayat_akhir, s.nilai, s.keterangan,
                g.nama as nama_guru
             FROM setoran_tahfidz s
             LEFT JOIN ref_surah rs ON s.id_surah = rs.id_surah
             LEFT JOIN guru g ON s.id_guru = g.id_guru
             JOIN anggota_tahfidz a ON s.id_tahfidz = a.id_tahfidz
             WHERE a.id_siswa = ? AND a.id_ta = ?
             ORDER BY s.tanggal DESC"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        $jurnal = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $pdo->prepare(
            "SELECT COUNT(*) as total_setoran, AVG(nilai) as rata_nilai
             FROM setoran_tahfidz s
             JOIN anggota_tahfidz a ON s.id_tahfidz = a.id_tahfidz
             WHERE a.id_siswa = ? AND a.id_ta = ?"
        );
        $stmt2->execute([$id_siswa, $id_ta]);
        $summary = $stmt2->fetch(PDO::FETCH_ASSOC);

        return ['jurnal' => $jurnal, 'summary' => $summary];
    }

    /**
     * Ekskul yang diikuti siswa.
     */
    public static function getEkskulSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                e.nama_ekskul, e.hari, e.jam_mulai, e.jam_selesai,
                a.nilai, a.predikat, a.deskripsi,
                g.nama as nama_pembina
             FROM anggota_ekskul a
             JOIN ekstrakurikuler e ON a.id_ekskul = e.id_ekskul
             LEFT JOIN guru g ON e.id_guru_pembina = g.id_guru
             WHERE a.id_siswa = ? AND a.id_ta = ?"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kokulikuler yang diikuti siswa.
     */
    public static function getKokulikulerSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                k.nama_kegiatan as nama_kokulikuler, k.hari, k.jam_mulai, k.jam_selesai,
                a.nilai, a.deskripsi,
                g.nama as nama_pembina
             FROM anggota_kokulikuler a
             JOIN kokulikuler k ON a.id_kokulikuler = k.id_kokulikuler
             LEFT JOIN guru g ON k.id_guru_pembina = g.id_guru
             WHERE a.id_siswa = ? AND a.id_ta = ?"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kewirausahaan yang diikuti siswa.
     */
    public static function getKewirausahaanSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                k.nama_kegiatan, k.kelompok, k.hari, k.jam,
                (SELECT COUNT(*) FROM jurnal_kewirausahaan jk WHERE jk.id_kewirausahaan = k.id_kewirausahaan) as total_pertemuan,
                (SELECT COUNT(*) FROM presensi_kewirausahaan pk JOIN jurnal_kewirausahaan jk2 ON pk.id_jurnal = jk2.id_jurnal WHERE jk2.id_kewirausahaan = k.id_kewirausahaan AND pk.id_siswa = ? AND pk.status = 'Hadir') as total_hadir,
                g.nama as nama_pembina
             FROM anggota_kewirausahaan a
             JOIN kewirausahaan k ON a.id_kewirausahaan = k.id_kewirausahaan
             LEFT JOIN guru g ON k.id_guru_pembina = g.id_guru
             WHERE a.id_siswa = ? AND a.id_ta = ?"
        );
        $stmt->execute([$id_siswa, $id_siswa, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================================
    // KALENDER AKADEMIK
    // =====================================================================

    /**
     * Ambil kegiatan kalender akademik (bulan berjalan & mendatang).
     */
    public static function getKalenderAkademik(PDO $pdo, int $id_ta): array
    {
        $stmt = $pdo->prepare(
            "SELECT 
                ka.id_kalender AS id_kegiatan, ka.judul_kegiatan AS nama_kegiatan, ka.kategori,
                ka.tanggal_mulai, ka.tanggal_selesai, ka.warna, ka.deskripsi AS keterangan
             FROM kalender_akademik ka
             WHERE ka.id_ta = ?
             ORDER BY ka.tanggal_mulai ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================================
    // DASHBOARD WIDGET DATA
    // =====================================================================

    /**
     * Data ringkasan untuk dashboard siswa.
     */
    public static function getDashboardSummary(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        // Tagihan belum lunas
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM keuangan_tagihan_siswa WHERE id_siswa = ? AND sisa_tagihan > 0"
        );
        $stmt->execute([$id_siswa]);
        $tagihan_belum_lunas = (int)$stmt->fetchColumn();

        // Total absensi alpa bulan ini
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM absensi_siswa_piket ap
             JOIN kelas k ON ap.id_kelas = k.id_kelas
             WHERE ap.id_siswa = ? AND k.id_ta = ? 
             AND ap.status = 'Alpa'
             AND MONTH(ap.tanggal) = MONTH(CURDATE())
             AND YEAR(ap.tanggal) = YEAR(CURDATE())"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        $alpa_bulan_ini = (int)$stmt->fetchColumn();

        // Total nilai (rata-rata)
        $stmt = $pdo->prepare(
            "SELECT ROUND(AVG(ns.nilai), 1) 
             FROM nilai_sumatif ns
             JOIN penilaian_sumatif ps ON ns.id_sumatif = ps.id_sumatif
             JOIN penempatan_siswa pt ON ns.id_penempatan = pt.id_penempatan
             WHERE pt.id_siswa = ? AND ps.id_ta = ?"
        );
        $stmt->execute([$id_siswa, $id_ta]);
        $rata_nilai = $stmt->fetchColumn() ?? 0;

        // Tugas pending LMS
        $stmt = $pdo->prepare(
            "SELECT COUNT(DISTINCT lt.id_tugas) 
             FROM lms_tugas lt
             JOIN penempatan_siswa p ON lt.id_kelas = p.id_kelas AND p.id_siswa = ?
             WHERE p.id_ta = ?
             AND lt.deadline >= CURDATE()
             AND lt.id_tugas NOT IN (
                 SELECT id_tugas FROM lms_pengumpulan WHERE id_siswa = ?
             )"
        );
        $stmt->execute([$id_siswa, $id_ta, $id_siswa]);
        $tugas_pending = (int)$stmt->fetchColumn();

        // Jadwal hari ini
        $hari_ini_en = date('l');
        $hari_map = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
        $hari_ini = $hari_map[$hari_ini_en] ?? 'Senin';

        $kelas = self::getKelasSiswa($pdo, $id_siswa, $id_ta);
        $jadwal_hari_ini = [];
        if ($kelas) {
            $all_jadwal = self::getJadwalByKelas($pdo, (int)$kelas['id_kelas'], $id_ta);
            $jadwal_hari_ini = $all_jadwal[$hari_ini] ?? [];
        }

        return [
            'tagihan_belum_lunas' => $tagihan_belum_lunas,
            'alpa_bulan_ini'      => $alpa_bulan_ini,
            'rata_nilai'          => $rata_nilai,
            'tugas_pending'       => $tugas_pending,
            'jadwal_hari_ini'     => $jadwal_hari_ini,
            'hari_ini'            => $hari_ini,
            'kelas'               => $kelas,
        ];
    }

    // =====================================================================
    // CBT (COMPUTER BASED TEST) SISWA
    // =====================================================================

    /**
     * Ambil seluruh agenda ujian CBT untuk siswa yang sedang login.
     */
    public static function getCbtListSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $sql = "
            SELECT cp.id_peserta, cp.id_jadwal, cp.token, cp.status AS status_peserta,
                   cp.waktu_mulai, cp.waktu_selesai,
                   j.nama_ujian, j.jenis_ujian, j.tanggal_mulai, j.tanggal_selesai, j.durasi_menit, j.pin_proktor,
                   j.passing_grade, j.tampilkan_nilai, j.status AS status_jadwal,
                   p.nama_paket, p.jml_soal_pg, p.jml_soal_essay,
                   m.nama_mapel, COALESCE(ks.nama_kelas, kj.nama_kelas, '-') AS nama_kelas,
                   n.nilai_pg, n.nilai_essay, n.nilai_akhir, n.status_lulus
            FROM cbt_peserta cp
            JOIN cbt_jadwal j ON cp.id_jadwal = j.id_jadwal
            LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
            LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
            LEFT JOIN kelas kj ON j.id_kelas = kj.id_kelas
            LEFT JOIN penempatan_siswa ps ON ps.id_siswa = cp.id_siswa 
                 AND ps.id_ta = (SELECT id_ta FROM tahun_ajaran WHERE status = 'Aktif' LIMIT 1)
            LEFT JOIN kelas ks ON ps.id_kelas = ks.id_kelas
            LEFT JOIN cbt_nilai n ON cp.id_peserta = n.id_peserta
            WHERE cp.id_siswa = ?
            ORDER BY j.id_jadwal DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_siswa]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $aktif = [];
        $selesai = [];
        $mendatang = [];
        $now = date('Y-m-d H:i:s');

        foreach ($rows as $r) {
            $tgl_mulai = $r['tanggal_mulai'] ?? '1970-01-01 00:00:00';
            $tgl_selesai = $r['tanggal_selesai'] ?? '2099-12-31 23:59:59';
            $st_p = strtolower($r['status_peserta'] ?? 'belum');

            if ($st_p === 'selesai') {
                $selesai[] = $r;
            } elseif ($r['status_jadwal'] === 'aktif' && $now >= $tgl_mulai && $now <= $tgl_selesai) {
                $aktif[] = $r;
            } elseif ($now < $tgl_mulai && $r['status_jadwal'] === 'aktif') {
                $mendatang[] = $r;
            } else {
                // Lewat jadwal / selesai
                $selesai[] = $r;
            }
        }

        return [
            'aktif'     => $aktif,
            'mendatang' => $mendatang,
            'selesai'   => $selesai,
            'all'       => $rows,
        ];
    }

    /**
     * Ambil data untuk Halaman Konfirmasi Data Peserta & Token Ujian (ANBK Style).
     */
    public static function getCbtKonfirmasiData(PDO $pdo, int $id_peserta, int $id_siswa): ?array
    {
        $stmt = $pdo->prepare("
            SELECT cp.*, cp.status AS status_peserta,
                   j.nama_ujian, j.jenis_ujian, j.tanggal_mulai, j.tanggal_selesai, j.durasi_menit, j.pin_proktor,
                   j.passing_grade, j.tampilkan_nilai, j.status AS status_jadwal,
                   p.nama_paket, p.jenis_asesmen, p.petunjuk_umum, p.jml_soal_pg, p.jml_soal_essay,
                   m.nama_mapel, COALESCE(ks.nama_kelas, kj.nama_kelas, '-') AS nama_kelas,
                   s.nama AS nama_siswa, s.nisn, s.nipd, s.jk AS jk_siswa, s.id_pengguna,
                   pg.foto AS foto_siswa
            FROM cbt_peserta cp
            JOIN cbt_jadwal j ON cp.id_jadwal = j.id_jadwal
            LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
            LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
            LEFT JOIN kelas kj ON j.id_kelas = kj.id_kelas
            LEFT JOIN penempatan_siswa ps ON ps.id_siswa = cp.id_siswa 
                 AND ps.id_ta = (SELECT id_ta FROM tahun_ajaran WHERE status = 'Aktif' LIMIT 1)
            LEFT JOIN kelas ks ON ps.id_kelas = ks.id_kelas
            LEFT JOIN siswa s ON cp.id_siswa = s.id_siswa
            LEFT JOIN pengguna pg ON s.id_pengguna = pg.id_pengguna
            WHERE cp.id_peserta = ? AND cp.id_siswa = ?
        ");
        $stmt->execute([$id_peserta, $id_siswa]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Ambil data sesi ujian aktif untuk dikerjakan siswa (soal, opsi, durasi sisa).
     */
    public static function getCbtSession(PDO $pdo, int $id_peserta, int $id_siswa): ?array
    {
        // 1. Verifikasi kepemilikan peserta
        $stmt = $pdo->prepare("
            SELECT cp.*, j.nama_ujian, j.jenis_ujian, j.tanggal_mulai, j.tanggal_selesai, j.durasi_menit, j.pin_proktor,
                   j.passing_grade, j.tampilkan_nilai, j.id_paket, j.status AS status_jadwal,
                   p.nama_paket, p.id_bank, p.acak_soal, p.acak_opsi,
                   m.nama_mapel, s.nama AS nama_siswa, s.nisn, s.nipd, s.jk AS jk_siswa, s.id_pengguna,
                   COALESCE(ks.nama_kelas, kj.nama_kelas, '-') AS nama_kelas, pg.foto AS foto_siswa
            FROM cbt_peserta cp
            JOIN cbt_jadwal j ON cp.id_jadwal = j.id_jadwal
            LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
            LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
            LEFT JOIN kelas kj ON j.id_kelas = kj.id_kelas
            LEFT JOIN penempatan_siswa ps ON ps.id_siswa = cp.id_siswa 
                 AND ps.id_ta = (SELECT id_ta FROM tahun_ajaran WHERE status = 'Aktif' LIMIT 1)
            LEFT JOIN kelas ks ON ps.id_kelas = ks.id_kelas
            LEFT JOIN siswa s ON cp.id_siswa = s.id_siswa
            LEFT JOIN pengguna pg ON s.id_pengguna = pg.id_pengguna
            WHERE cp.id_peserta = ? AND cp.id_siswa = ?
        ");
        $stmt->execute([$id_peserta, $id_siswa]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$session) {
            return null;
        }

        // 2. Inisialisasi waktu_mulai jika baru pertama kali masuk
        if (empty($session['waktu_mulai']) || $session['status'] === 'belum') {
            $now = date('Y-m-d H:i:s');
            $pdo->prepare("UPDATE cbt_peserta SET status = 'mengerjakan', waktu_mulai = ? WHERE id_peserta = ?")
                ->execute([$now, $id_peserta]);
            $session['waktu_mulai'] = $now;
            $session['status'] = 'mengerjakan';
        }

        // 3. Hitung sisa waktu dalam detik
        $durasi_detik = (int)($session['durasi_menit'] ?? 60) * 60;
        $waktu_mulai_ts = strtotime($session['waktu_mulai']);
        $elapsed = time() - $waktu_mulai_ts;
        $sisa_detik = max(0, $durasi_detik - $elapsed);

        // Cek juga batas jadwal selesai
        if (!empty($session['tanggal_selesai'])) {
            $jadwal_selesai_ts = strtotime($session['tanggal_selesai']);
            $sisa_jadwal = max(0, $jadwal_selesai_ts - time());
            $sisa_detik = min($sisa_detik, $sisa_jadwal);
        }

        // 4. Ambil butir-butir soal
        $id_bank = (int)($session['id_bank'] ?? 0);
        $order_by = !empty($session['acak_soal']) ? "RAND({$id_peserta})" : "nomor_urut ASC, id_soal ASC";
        
        $st_soal = $pdo->prepare("SELECT * FROM cbt_soal WHERE id_bank = ? ORDER BY $order_by");
        $st_soal->execute([$id_bank]);
        $soal_raw = $st_soal->fetchAll(PDO::FETCH_ASSOC);

        // 5. Ambil jawaban siswa yang sudah tersimpan
        $st_jawaban = $pdo->prepare("SELECT * FROM cbt_jawaban WHERE id_peserta = ?");
        $st_jawaban->execute([$id_peserta]);
        $jawaban_rows = $st_jawaban->fetchAll(PDO::FETCH_ASSOC);
        $jawaban_map = [];
        foreach ($jawaban_rows as $jw) {
            $jawaban_map[$jw['id_soal']] = $jw;
        }

        // 6. Lengkapi opsi jawaban per soal
        $soal_list = [];
        foreach ($soal_raw as $s) {
            $st_opsi = $pdo->prepare("SELECT id_opsi, id_soal, label, isi_opsi, gambar FROM cbt_soal_opsi WHERE id_soal = ? ORDER BY label ASC");
            $st_opsi->execute([$s['id_soal']]);
            $s['opsi_list'] = $st_opsi->fetchAll(PDO::FETCH_ASSOC);

            // Jawaban siswa jika ada
            $jw = $jawaban_map[$s['id_soal']] ?? null;
            $s['jawaban_siswa'] = $jw ? $jw['jawaban_pg'] : null;
            $s['jawaban_essay'] = $jw ? $jw['jawaban_essay'] : null;
            $s['is_ragu']       = $jw ? (int)$jw['is_ragu'] : 0;

            $soal_list[] = $s;
        }

        $session['sisa_detik'] = $sisa_detik;
        $session['soal_list'] = $soal_list;

        return $session;
    }

    /**
     * Simpan jawaban butir soal CBT realtime (AJAX).
     */
    public static function saveCbtJawaban(PDO $pdo, int $id_peserta, int $id_soal, int $id_jadwal, ?string $jawaban_pg, ?string $jawaban_essay, int $is_ragu): bool
    {
        // Cek apakah opsi benar
        $is_benar = 0;
        if (!empty($jawaban_pg)) {
            $chk = $pdo->prepare("SELECT is_benar FROM cbt_soal_opsi WHERE id_soal = ? AND label = ?");
            $chk->execute([$id_soal, $jawaban_pg]);
            $is_benar = (int)$chk->fetchColumn();
        }

        // Cek apakah jawaban sudah ada
        $stmt = $pdo->prepare("SELECT id_jawaban FROM cbt_jawaban WHERE id_peserta = ? AND id_soal = ?");
        $stmt->execute([$id_peserta, $id_soal]);
        $id_jawaban = $stmt->fetchColumn();

        if ($id_jawaban) {
            $upd = $pdo->prepare("
                UPDATE cbt_jawaban 
                SET jawaban_pg = ?, jawaban_essay = ?, is_ragu = ?, is_benar = ?, waktu_jawab = NOW() 
                WHERE id_jawaban = ?
            ");
            return $upd->execute([$jawaban_pg, $jawaban_essay, $is_ragu, $is_benar, $id_jawaban]);
        } else {
            $ins = $pdo->prepare("
                INSERT INTO cbt_jawaban 
                (id_peserta, id_soal, id_jadwal, jawaban_pg, jawaban_essay, is_ragu, is_benar, waktu_jawab) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            return $ins->execute([$id_peserta, $id_soal, $id_jadwal, $jawaban_pg, $jawaban_essay, $is_ragu, $is_benar]);
        }
    }

    /**
     * Selesaikan ujian dan hitung nilai akhir otomatis.
     */
    public static function finishCbtExam(PDO $pdo, int $id_peserta, int $id_siswa): array
    {
        // 1. Ambil data sesi peserta
        $st = $pdo->prepare("
            SELECT cp.*, j.passing_grade, j.tampilkan_nilai, j.id_paket, p.id_bank, b.bobot_pg, b.bobot_esai
            FROM cbt_peserta cp
            JOIN cbt_jadwal j ON cp.id_jadwal = j.id_jadwal
            LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
            LEFT JOIN cbt_bank_soal b ON p.id_bank = b.id_bank
            WHERE cp.id_peserta = ? AND cp.id_siswa = ?
        ");
        $st->execute([$id_peserta, $id_siswa]);
        $p = $st->fetch(PDO::FETCH_ASSOC);

        if (!$p) {
            return ['status' => 'error', 'message' => 'Peserta tidak valid.'];
        }

        $id_jadwal = (int)$p['id_jadwal'];
        $id_bank = (int)($p['id_bank'] ?? 0);
        $tampilkan_nilai = isset($p['tampilkan_nilai']) ? (int)$p['tampilkan_nilai'] : 1;

        // 2. Hitung total soal PG & jawaban benar
        $st_total_pg = $pdo->prepare("SELECT COUNT(*) FROM cbt_soal WHERE id_bank = ? AND tipe_soal IN ('pg', 'tf')");
        $st_total_pg->execute([$id_bank]);
        $total_pg = (int)$st_total_pg->fetchColumn();

        $st_benar_pg = $pdo->prepare("
            SELECT COUNT(*) 
            FROM cbt_jawaban jw
            JOIN cbt_soal s ON jw.id_soal = s.id_soal
            WHERE jw.id_peserta = ? AND s.tipe_soal IN ('pg', 'tf') AND jw.is_benar = 1
        ");
        $st_benar_pg->execute([$id_peserta]);
        $benar_pg = (int)$st_benar_pg->fetchColumn();

        $nilai_pg = $total_pg > 0 ? round(($benar_pg / $total_pg) * 100, 2) : 0;
        $nilai_essay = 0; // Nilai esai menunggu koreksi guru
        $nilai_akhir = $nilai_pg; // Default nilai akhir dari PG
        $passing_grade = (float)($p['passing_grade'] ?? 75);
        $status_lulus = $nilai_akhir >= $passing_grade ? 1 : 0;

        // 3. Simpan / update ke cbt_nilai
        $st_chk = $pdo->prepare("SELECT id_nilai FROM cbt_nilai WHERE id_peserta = ?");
        $st_chk->execute([$id_peserta]);
        $id_nilai = $st_chk->fetchColumn();

        if ($id_nilai) {
            $pdo->prepare("
                UPDATE cbt_nilai 
                SET nilai_pg = ?, nilai_essay = ?, nilai_akhir = ?, status_lulus = ?, dihitung_pada = NOW() 
                WHERE id_nilai = ?
            ")->execute([$nilai_pg, $nilai_essay, $nilai_akhir, $status_lulus, $id_nilai]);
        } else {
            $pdo->prepare("
                INSERT INTO cbt_nilai 
                (id_peserta, id_jadwal, id_siswa, nilai_pg, nilai_essay, nilai_akhir, status_lulus, dihitung_pada) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ")->execute([$id_peserta, $id_jadwal, $id_siswa, $nilai_pg, $nilai_essay, $nilai_akhir, $status_lulus]);
        }

        // 4. Update status peserta menjadi 'selesai'
        $pdo->prepare("UPDATE cbt_peserta SET status = 'selesai', waktu_selesai = NOW() WHERE id_peserta = ?")
            ->execute([$id_peserta]);

        return [
            'status'          => 'ok',
            'tampilkan_nilai' => $tampilkan_nilai,
            'nilai_pg'        => $nilai_pg,
            'nilai_akhir'     => $nilai_akhir,
            'status_lulus'    => $status_lulus,
            'benar_pg'        => $benar_pg,
            'total_pg'        => $total_pg,
        ];
    }
}

