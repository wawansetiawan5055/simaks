<?php
require_once __DIR__ . '/../models/PenilaianSumatifModel.php';
require_once __DIR__ . '/../models/JurnalKbmModel.php';
require_once __DIR__ . '/../models/NilaiModel.php';
require_once __DIR__ . '/../models/CpTpModel.php';
require_once __DIR__ . '/../models/KelasModel.php';

function penilaian_sumatif_index($pdo)
{
    if (!check_access('penilaian_sumatif', 'index'))
        redirect('index.php');
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $id_guru = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }
    if (!$id_guru || !$id_ta)
        die("Error: Informasi Guru atau TA tidak valid.");

    $kelas_diajar = [];
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $kelas_diajar = KelasModel::all($pdo, $id_ta);
    } else {
        $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru, $id_ta);
    }

    $id_kelas_filter = $_GET['id_kelas'] ?? null;
    $id_guru_mapel_filter = $_GET['id_guru_mapel'] ?? null;
    $mapel_diajar = [];
    $agenda_list = [];
    if ($id_kelas_filter) {
        $mapel_diajar = NilaiModel::getMapelDiajarByKelas($pdo, $id_guru, $id_kelas_filter, $id_ta);
        if ($id_guru_mapel_filter) {
            $agenda_list = PenilaianSumatifModel::getAgendaSumatifList($pdo, $id_guru_mapel_filter, $id_kelas_filter, $id_ta);
        }
    }
    $data_for_view = compact('kelas_diajar', 'id_kelas_filter', 'mapel_diajar', 'id_guru_mapel_filter', 'agenda_list');
    extract($data_for_view);
    include __DIR__ . '/../views/penilaian_sumatif_index.php';
}

function penilaian_sumatif_form_agenda($pdo)
{
    if (!can_do($pdo, 'penilaian_sumatif', 'create')) { // Agenda = Create
        redirect('index.php?mod=penilaian_sumatif');
    }
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $id_guru = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }
    if (!$id_guru || !$id_ta)
        die("Error: Informasi Guru atau TA tidak valid.");
    $kelas_diajar = [];
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $kelas_diajar = KelasModel::all($pdo, $id_ta);
    } else {
        $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru, $id_ta);
    }
    include __DIR__ . '/../views/penilaian_sumatif_form_agenda.php';
}

function penilaian_sumatif_save_agenda($pdo)
{
    if (!can_do($pdo, 'penilaian_sumatif', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk membuat agenda.";
        redirect('index.php?mod=penilaian_sumatif');
        return;
    }

    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    // Validasi input
    if (empty($_POST['id_guru_mapel']) || empty($_POST['id_kelas']) || empty($_POST['nama_penilaian']) || empty($_POST['jenis_sumatif']) || !$id_ta) {
        $_SESSION['pesan_error'] = "Gagal membuat agenda: Data tidak lengkap atau sesi TA tidak valid.";
        redirect('index.php?mod=penilaian_sumatif&act=form_agenda');
        return;
    }

    $data_agenda = [
        'id_guru_mapel' => $_POST['id_guru_mapel'],
        'id_kelas' => $_POST['id_kelas'],
        'id_ta' => $id_ta,
        'nama_penilaian' => $_POST['nama_penilaian'],
        'jenis_sumatif' => $_POST['jenis_sumatif'],
        'tanggal_penilaian' => !empty($_POST['tanggal_penilaian']) ? $_POST['tanggal_penilaian'] : null,
        'keterangan' => $_POST['keterangan'] ?? null
    ];

    try {
        $id_sumatif_baru = PenilaianSumatifModel::saveAgendaSumatif($pdo, $data_agenda);

        if ($id_sumatif_baru) {
            // Simpan relasi TP jika dipilih (pastikan name="selected_tps[]" ada di form view)
            if (!empty($_POST['selected_tps']) && is_array($_POST['selected_tps'])) {
                // Anda perlu tabel relasi baru, misal 'penilaian_sumatif_has_tp' (id_sumatif, id_tp)
                // $stmtRelasi = $pdo->prepare("INSERT INTO penilaian_sumatif_has_tp (id_sumatif, id_tp) VALUES (?, ?)");
                // foreach($_POST['selected_tps'] as $id_tp) {
                //     $stmtRelasi->execute([$id_sumatif_baru, $id_tp]);
                // }
            }
            $_SESSION['pesan_sukses'] = "Agenda Penilaian Sumatif berhasil dibuat.";
            redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif_baru);
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan agenda penilaian ke database (ID tidak didapatkan).";
            redirect('index.php?mod=penilaian_sumatif&act=form_agenda');
        }
    } catch (PDOException $e) {
        error_log("PDO Error saving agenda sumatif: " . $e->getMessage()); // Log error detail
        $_SESSION['pesan_error'] = "Terjadi kesalahan database. Silakan coba lagi atau hubungi admin."; // Pesan umum untuk user
        redirect('index.php?mod=penilaian_sumatif&act=form_agenda');
    } catch (Exception $e) {
        error_log("General Error saving agenda sumatif: " . $e->getMessage());
        $_SESSION['pesan_error'] = "Terjadi kesalahan sistem. Silakan coba lagi.";
        redirect('index.php?mod=penilaian_sumatif&act=form_agenda');
    }
}


function penilaian_sumatif_form_nilai($pdo)
{
    // Input Nilai = Update Agenda atau Create Nilai? 
    // Kita anggap Create/Update izin yang sama pentingnya untuk guru.
    if (!can_do($pdo, 'penilaian_sumatif', 'create') && !can_do($pdo, 'penilaian_sumatif', 'update')) {
        redirect('index.php?mod=penilaian_sumatif');
    }

    $id_sumatif = $_GET['id_sumatif'] ?? 0;
    if (!$id_sumatif)
        redirect('index.php?mod=penilaian_sumatif');
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    $agenda = PenilaianSumatifModel::findAgendaSumatif($pdo, $id_sumatif);
    if (!$agenda)
        die("Agenda penilaian tidak ditemukan.");

    $stmtMapel = $pdo->prepare("SELECT id_mapel FROM guru_mapel WHERE id_guru_mapel = ?");
    $stmtMapel->execute([$agenda['id_guru_mapel']]);
    $id_mapel_asli = $stmtMapel->fetchColumn();
    $tp_list = CpTpModel::getTpByMapel($pdo, $id_mapel_asli);
    $selected_tps_ids = PenilaianSumatifModel::getSelectedTpIdsForSumatif($pdo, $id_sumatif);
    $siswa_nilai = PenilaianSumatifModel::getSiswaWithNilaiSumatif($pdo, $agenda['id_kelas'], $id_sumatif, $id_ta);
    $data_for_view = compact('agenda', 'tp_list', 'selected_tps_ids', 'siswa_nilai');
    extract($data_for_view);
    include __DIR__ . '/../views/penilaian_sumatif_form_nilai.php';
}

function penilaian_sumatif_save_nilai($pdo)
{
    if (!can_do($pdo, 'penilaian_sumatif', 'create') && !can_do($pdo, 'penilaian_sumatif', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan nilai.";
        $id_sumatif = $_POST['id_sumatif'] ?? 0;
        redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif);
        return;
    }

    $id_sumatif = $_POST['id_sumatif'] ?? 0;
    $nilai_data = $_POST['nilai'] ?? [];
    $selected_tps = $_POST['selected_tps'] ?? [];
    $id_guru_mapel = $_POST['id_guru_mapel'] ?? 0;
    if (!$id_sumatif || !$id_guru_mapel)
        die("Gagal menyimpan: Data tidak lengkap.");
    if (empty($selected_tps)) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: Tidak ada TP yang dipilih.";
        redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif);
        return;
    }

    $data_to_save = [
        'id_sumatif' => $id_sumatif,
        'selected_tps' => $selected_tps,
        'nilai' => $nilai_data,
        'id_guru_mapel' => $id_guru_mapel
    ];

    try {
        PenilaianSumatifModel::saveNilaiSumatif($pdo, $data_to_save);
        $_SESSION['pesan_sukses'] = "Nilai sumatif berhasil disimpan.";
    } catch (Exception $e) {
        error_log("Error saving sumatif score: " . $e->getMessage());
        $_SESSION['pesan_error'] = "Terjadi kesalahan saat menyimpan nilai.";
    }
    redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif);
}