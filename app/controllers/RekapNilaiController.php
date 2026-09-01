<?php

require_once __DIR__ . '/../models/RekapNilaiModel.php';

function rekap_nilai_index($pdo) {
    if (!can_do($pdo, 'penilaian_sumatif', 'read') && !can_do($pdo, 'input_nilai', 'read')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php');
        return;
    }

    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;

    $kelas_diajar = RekapNilaiModel::getKelasDiajar($pdo, $id_guru, $id_ta);

    $id_kelas_filter = $_GET['id_kelas'] ?? '';
    $id_guru_mapel_filter = $_GET['id_guru_mapel'] ?? '';

    $mapel_diajar = [];
    if ($id_kelas_filter) {
        $mapel_diajar = RekapNilaiModel::getMapelDiajarByKelas($pdo, $id_guru, $id_kelas_filter, $id_ta);
    }

    $rekap_data = [];
    $bobot = [];
    $nama_mapel_terpilih = '';

    if ($id_kelas_filter && $id_guru_mapel_filter) {
        foreach ($mapel_diajar as $m) {
            if ($m['id_guru_mapel'] == $id_guru_mapel_filter) {
                $nama_mapel_terpilih = $m['nama_mapel'];
                break;
            }
        }

        $bobot = RekapNilaiModel::getBobotConfig($pdo, $id_guru_mapel_filter, $id_kelas_filter);
        
        $limits = [
            'limit_tp_tinggi' => $bobot['limit_tp_tinggi'],
            'limit_tp_rendah' => $bobot['limit_tp_rendah']
        ];
        
        $rekap_data = RekapNilaiModel::getRekapData($pdo, $id_kelas_filter, $id_guru_mapel_filter, $id_ta, $limits);

        // Kalkulasi nilai akhir
        foreach ($rekap_data as &$r) {
            $val_sikap = $r['sikap'] ?? 0;
            $val_lms = $r['lms'] ?? 0;
            $val_formatif = $r['formatif'] ?? 0;
            $val_lm = $r['sumatif_lm'] ?? 0;
            $val_sts = $r['sts'] ?? 0;
            $val_sas = $r['sas'] ?? 0;

            $na = ($val_sikap * ($bobot['sikap']/100)) + 
                  ($val_lms * ($bobot['lms']/100)) + 
                  ($val_formatif * ($bobot['formatif']/100)) + 
                  ($val_lm * ($bobot['sumatif_lm']/100)) + 
                  ($val_sts * ($bobot['sts']/100)) + 
                  ($val_sas * ($bobot['sas']/100));

            $r['nilai_akhir'] = round($na, 2);
        }
    }

    // Wali Kelas / Admin Ledger Data
    $is_wali = false;
    $collective_data = [];
    if ($id_kelas_filter) {
        $is_admin = in_array('Admin', $_SESSION['roles'] ?? []);
        $is_wali = $is_admin || RekapNilaiModel::isWaliKelas($pdo, $id_guru, $id_kelas_filter, $id_ta);
        
        if ($is_wali) {
            $collective_data = [
                'subjects' => RekapNilaiModel::getSubjectsInClass($pdo, $id_kelas_filter, $id_ta),
                'ledger' => RekapNilaiModel::getAllNaForClass($pdo, $id_kelas_filter, $id_ta),
                'sikap' => RekapNilaiModel::getSikapRekap($pdo, $id_kelas_filter, $id_ta),
                'absensi' => RekapNilaiModel::getAbsensiRekap($pdo, $id_kelas_filter, $id_ta),
                'siswa' => [] // Will be populated from rekap_data if available or separate fetch
            ];

            // Ensure we have the student list even if mapel filter is not selected
            if (empty($rekap_data)) {
                 $stmtSiswa = $pdo->prepare("SELECT p.id_penempatan, s.id_siswa, s.nama, s.nisn 
                    FROM penempatan_siswa p 
                    JOIN siswa s ON p.id_siswa = s.id_siswa 
                    WHERE p.id_kelas = ? AND p.id_ta = ? AND s.status_aktif = 'Aktif'
                    ORDER BY s.nama ASC");
                $stmtSiswa->execute([$id_kelas_filter, $id_ta]);
                $collective_data['siswa'] = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $collective_data['siswa'] = array_values($rekap_data);
            }
        }
    }

    include __DIR__ . '/../views/rekap_nilai_index.php';
}

function rekap_nilai_simpan_bobot($pdo) {
    if (!isset($_POST['id_kelas']) || !isset($_POST['id_guru_mapel'])) {
        redirect('index.php?mod=rekap_nilai');
        return;
    }

    $id_kelas = $_POST['id_kelas'];
    $id_guru_mapel = $_POST['id_guru_mapel'];

    if (isset($_POST['reset_default'])) {
        RekapNilaiModel::resetBobotConfig($pdo, $id_guru_mapel, $id_kelas);
        $_SESSION['pesan_sukses'] = "Bobot berhasil dikembalikan ke default sekolah.";
    } else {
        $sikap = floatval($_POST['bobot_sikap'] ?? 0);
        $lms = floatval($_POST['bobot_lms'] ?? 0);
        $formatif = floatval($_POST['bobot_formatif'] ?? 0);
        $lm = floatval($_POST['bobot_sumatif_lm'] ?? 0);
        $sts = floatval($_POST['bobot_sts'] ?? 0);
        $sas = floatval($_POST['bobot_sas'] ?? 0);
        
        $lim_tinggi = intval($_POST['limit_tp_tinggi'] ?? 3);
        $lim_rendah = intval($_POST['limit_tp_rendah'] ?? 2);

        $total = $sikap + $lms + $formatif + $lm + $sts + $sas;

        if (abs($total - 100.0) > 0.01) {
            $_SESSION['pesan_error'] = "Gagal menyimpan: Total persentase bobot harus 100%. (Saat ini: $total%)";
        } else {
            $data = [
                'id_guru_mapel' => $id_guru_mapel,
                'id_kelas' => $id_kelas,
                'bobot_sikap' => $sikap,
                'bobot_lms' => $lms,
                'bobot_formatif' => $formatif,
                'bobot_sumatif_lm' => $lm,
                'bobot_sts' => $sts,
                'bobot_sas' => $sas,
                'limit_tp_tinggi' => $lim_tinggi,
                'limit_tp_rendah' => $lim_rendah
            ];
            RekapNilaiModel::saveBobotConfig($pdo, $data);
            $_SESSION['pesan_sukses'] = "Konfigurasi bobot dan limit deskripsi berhasil disimpan.";
        }
    }

    redirect("index.php?mod=rekap_nilai&id_kelas={$id_kelas}&id_guru_mapel={$id_guru_mapel}");
}
