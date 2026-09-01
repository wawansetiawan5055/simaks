<?php
// File: app/controllers/PenilaianSikapController.php

require_once __DIR__.'/../models/PenilaianSikapModel.php';
require_once __DIR__.'/../models/KomponenSikapModel.php';

class PenilaianSikapController {
    
    public static function index() {
        require_access('penilaian_sikap');
        global $pdo;
        $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

        if (!$id_guru && !is_admin()) {
            $_SESSION['pesan_error'] = "Akses ditolak. Anda bukan Guru atau Admin.";
            header("Location: index.php?mod=dashboard");
            exit;
        }

        // Jika admin, $id_guru bisa 0 untuk melihat semua agenda
        $display_guru_id = is_admin() ? 0 : $id_guru;
        $agendas = PenilaianSikapModel::getAgendas($pdo, $display_guru_id, $id_ta);
        require __DIR__.'/../views/penilaian_sikap_index.php';
    }

    public static function form_agenda() {
        global $pdo;
        if (!can_do($pdo, 'penilaian_sikap', 'create') && !can_do($pdo, 'penilaian_sikap', 'update')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengelola agenda.";
            header("Location: index.php?mod=penilaian_sikap");
            exit;
        }
        $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
        $id_agenda = $_GET['id'] ?? null;

        $agenda = null;
        $selected_komponen_ids = [];
        if ($id_agenda) {
            $agenda = PenilaianSikapModel::getAgendaById($pdo, $id_agenda);
            $selected_komponen = PenilaianSikapModel::getSelectedKomponen($pdo, $id_agenda);
            $selected_komponen_ids = array_column($selected_komponen, 'id_komponen');
        }

        if (is_admin()) {
            // Admin bisa pilih semua kelas dan mapel
            $list_kelas = $pdo->query("SELECT id_kelas, nama_kelas FROM kelas WHERE id_ta = $id_ta ORDER BY nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
            $list_mapel = $pdo->query("SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
            $list_guru = $pdo->query("SELECT id_guru, nama FROM guru WHERE status = 'Aktif' ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Guru hanya kelas & mapel yang diajar
            $stmtKelas = $pdo->prepare("SELECT DISTINCT k.id_kelas, k.nama_kelas 
                                        FROM kelas k 
                                        JOIN guru_mapel gm ON k.id_kelas = gm.id_kelas 
                                        WHERE gm.id_guru = ? AND k.id_ta = ?
                                        UNION
                                        SELECT k.id_kelas, k.nama_kelas
                                        FROM kelas k
                                        JOIN penugasan_wali_kelas pwk ON k.id_kelas = pwk.id_kelas
                                        WHERE pwk.id_guru = ? AND k.id_ta = ?");
            $stmtKelas->execute([$id_guru, $id_ta, $id_guru, $id_ta]);
            $list_kelas = $stmtKelas->fetchAll(PDO::FETCH_ASSOC);

            $stmtMapel = $pdo->prepare("SELECT DISTINCT m.id_mapel, m.nama_mapel 
                                        FROM mapel m 
                                        JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel 
                                        WHERE gm.id_guru = ?");
            $stmtMapel->execute([$id_guru]);
            $list_mapel = $stmtMapel->fetchAll(PDO::FETCH_ASSOC);
            $list_guru = [];
        }

        $komponen_master = KomponenSikapModel::getAll($pdo);

        require __DIR__.'/../views/penilaian_sikap_form_agenda.php';
    }

    public static function save_agenda() {
        global $pdo;
        if (!can_do($pdo, 'penilaian_sikap', 'create') && !can_do($pdo, 'penilaian_sikap', 'update')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menyimpan agenda.";
            header("Location: index.php?mod=penilaian_sikap");
            exit;
        }
        
        $data = [
            'id_agenda' => $_POST['id_agenda'] ?? null,
            'id_guru' => $_POST['id_guru'] ?? $_SESSION['id_guru_terkait'],
            'id_ta' => $_SESSION['id_ta_aktif'],
            'id_kelas' => $_POST['id_kelas'],
            'periode' => $_POST['periode'],
            'kategori_penilai' => $_POST['kategori_penilai'],
            'id_mapel' => $_POST['id_mapel'] ?: null,
            'is_nilai_tambahan' => isset($_POST['is_nilai_tambahan']) ? 1 : 0,
            'bobot_tambahan' => $_POST['bobot_tambahan'] ?: 0,
            'komponen_ids' => $_POST['komponen_ids'] ?? []
        ];

        if (PenilaianSikapModel::saveAgenda($pdo, $data)) {
            $_SESSION['pesan_sukses'] = "Agenda berhasil disimpan.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan agenda.";
        }
        header("Location: index.php?mod=penilaian_sikap");
        exit;
    }

    public static function form_nilai() {
        global $pdo;
        if (!can_do($pdo, 'penilaian_sikap', 'read')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk melihat form nilai.";
            header("Location: index.php?mod=penilaian_sikap");
            exit;
        }
        $id_agenda = $_GET['id'] ?? null;
        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

        if (!$id_agenda) {
            header("Location: index.php?mod=penilaian_sikap");
            exit;
        }

        $agenda = PenilaianSikapModel::getAgendaById($pdo, $id_agenda);
        $komponen_list = PenilaianSikapModel::getSelectedKomponen($pdo, $id_agenda);
        $siswa_list = PenilaianSikapModel::getSiswaWithNilai($pdo, $id_agenda, $agenda['id_kelas'], $id_ta);

        require __DIR__.'/../views/penilaian_sikap_form_nilai.php';
    }

    public static function save_nilai() {
        global $pdo;
        if (!can_do($pdo, 'penilaian_sikap', 'update')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menyimpan nilai.";
            header("Location: index.php?mod=penilaian_sikap");
            exit;
        }
        $id_agenda = $_POST['id_agenda'];
        $nilai_data = $_POST['nilai'] ?? []; // [id_penempatan][id_komponen] = 'A'

        foreach ($nilai_data as $id_penempatan => $scores) {
            PenilaianSikapModel::saveNilai($pdo, $id_agenda, $id_penempatan, $scores);
        }

        $_SESSION['pesan_sukses'] = "Nilai berhasil disimpan.";
        header("Location: index.php?mod=penilaian_sikap&act=form_nilai&id=".$id_agenda);
        exit;
    }

    public static function delete() {
        global $pdo;
        if (!can_do($pdo, 'penilaian_sikap', 'delete')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menghapus agenda.";
            header("Location: index.php?mod=penilaian_sikap");
            exit;
        }
        $id = $_POST['id_agenda'] ?? null;
        if ($id && PenilaianSikapModel::deleteAgenda($pdo, $id)) {
            $_SESSION['pesan_sukses'] = "Agenda berhasil dihapus.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menghapus agenda.";
        }
        header("Location: index.php?mod=penilaian_sikap");
        exit;
    }
}
