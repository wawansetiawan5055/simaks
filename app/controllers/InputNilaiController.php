<?php
require_once __DIR__ . '/../models/NilaiModel.php';
require_once __DIR__ . '/../models/JurnalKbmModel.php';
require_once __DIR__ . '/../models/CpTpModel.php';

function input_nilai_index($pdo)
{
    if (!check_access('input_nilai', 'index'))
        redirect('index.php');

    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    if (in_array('Admin', $_SESSION['roles'] ?? []) || in_array('TU', $_SESSION['roles'] ?? [])) {
        $id_guru = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }
    if (!$id_guru || !$id_ta)
        die("Error: Informasi Guru atau TA tidak valid.");

    $kelas_diajar = [];
    if (in_array('Admin', $_SESSION['roles'] ?? []) || in_array('TU', $_SESSION['roles'] ?? [])) {
        $stmt = $pdo->prepare("SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
        $stmt->execute([$id_ta]);
        $kelas_diajar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru, $id_ta);
    }

    $id_kelas_filter = $_GET['id_kelas'] ?? null;
    $id_guru_mapel_filter = $_GET['id_guru_mapel'] ?? null;
    $id_cp_filter = $_GET['id_cp'] ?? null;
    $id_tp_filter = $_GET['id_tp'] ?? null;

    $mapel_diajar = [];
    $cp_list = [];
    $tp_list = [];
    $siswa_nilai = [];
    $nama_mapel_terpilih = '';

    if ($id_kelas_filter) {
        $mapel_diajar = NilaiModel::getMapelDiajarByKelas($pdo, $id_guru, $id_kelas_filter, $id_ta);

        if ($id_guru_mapel_filter) {
            // =============================================
            // PERBAIKAN UTAMA DI SINI (Sekitar baris 47)
            // =============================================
            // Query diubah untuk menentukan alias tabel (m.id_mapel)
            $stmtMapel = $pdo->prepare("
                SELECT m.id_mapel, m.nama_mapel 
                FROM mapel m 
                JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel 
                WHERE gm.id_guru_mapel = ? 
            ");
            // =============================================
            // AKHIR PERBAIKAN
            // =============================================
            $stmtMapel->execute([$id_guru_mapel_filter]);
            $mapelInfo = $stmtMapel->fetch(PDO::FETCH_ASSOC);
            $id_mapel_asli = $mapelInfo['id_mapel'] ?? 0;
            $nama_mapel_terpilih = $mapelInfo['nama_mapel'] ?? '';

            $kelas_info = $pdo->prepare("SELECT tingkat FROM kelas WHERE id_kelas = ?");
            $kelas_info->execute([$id_kelas_filter]);
            $tingkat = $kelas_info->fetchColumn();
            $fase_kelas = ($tingkat == 'X') ? 'E' : (($tingkat == 'XI' || $tingkat == 'XII') ? 'F' : '');

            if ($id_mapel_asli && $fase_kelas) {
                $cp_list = CpTpModel::getAllCpByMapelAndFase($pdo, $id_mapel_asli, $fase_kelas);

                if ($id_cp_filter) {
                    $tp_list = CpTpModel::getAllTpByCp($pdo, $id_cp_filter);

                    if ($id_tp_filter) {
                        $siswa_nilai = NilaiModel::getSiswaWithNilai($pdo, $id_kelas_filter, $id_guru_mapel_filter, $id_ta, $id_tp_filter);
                    }
                }
            }
        }
    }

    $data_for_view = compact(
        'kelas_diajar',
        'id_kelas_filter',
        'mapel_diajar',
        'id_guru_mapel_filter',
        'nama_mapel_terpilih',
        'cp_list',
        'id_cp_filter',
        'tp_list',
        'id_tp_filter',
        'siswa_nilai'
    );
    extract($data_for_view);

    include __DIR__ . '/../views/input_nilai_index.php';
}

function input_nilai_save($pdo)
{
    if (!can_do($pdo, 'input_nilai', 'create') && !can_do($pdo, 'input_nilai', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan nilai.";
        // Construct redirect URL from POST data if possible, or fallback
        $id_kelas = $_POST['id_kelas'] ?? 0;
        if ($id_kelas) {
            redirect("index.php?mod=input_nilai&id_kelas={$id_kelas}");
        } else {
            redirect("index.php?mod=input_nilai");
        }
        return;
    }

    $id_kelas = $_POST['id_kelas'];
    $id_guru_mapel = $_POST['id_guru_mapel'];
    $id_tp = $_POST['id_tp'];
    $nilai_data = $_POST['nilai'];

    $data_to_save = [
        'id_kelas' => $id_kelas,
        'id_guru_mapel' => $id_guru_mapel,
        'id_tp' => $id_tp,
        'nilai' => $nilai_data
    ];

    try {
        NilaiModel::save($pdo, $data_to_save);
        $_SESSION['pesan_sukses'] = "Nilai berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan nilai: " . $e->getMessage();
    }
    redirect("index.php?mod=input_nilai&id_kelas={$id_kelas}&id_guru_mapel={$id_guru_mapel}&id_cp={$_POST['id_cp']}&id_tp={$id_tp}");
}