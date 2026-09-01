<?php

require_once __DIR__ . '/../models/CetakRaporModel.php';

function cetak_rapor_index($pdo) {
    $model  = new CetakRaporModel($pdo);
    $ta     = $model->getActiveTa();
    $id_ta  = $ta['id_ta'] ?? null;

    $id_guru  = $_SESSION['id_guru_terkait'] ?? null;
    $is_admin = !empty(array_intersect(['Admin', 'Superadmin'], $_SESSION['roles'] ?? []));
    $semester = (int)($_GET['semester'] ?? 1);
    $id_kelas = (int)($_GET['id_kelas'] ?? 0);

    $list_kelas = $is_admin ? $model->getAllKelas($id_ta) : $model->getKelasByWaliKelas($id_guru, $id_ta);

    $siswa = [];
    $nama_kelas = '';

    if ($id_kelas) {
        if (!$is_admin && !$model->isWaliKelas($id_guru, $id_kelas, $id_ta)) {
            $_SESSION['pesan_error'] = 'Anda tidak memiliki akses ke kelas ini.';
            redirect('index.php?mod=cetak_rapor');
            return;
        }
        $siswa = $model->getSiswaByKelas($id_kelas, $id_ta, $semester);
        foreach ($list_kelas as $k) {
            if ($k['id_kelas'] == $id_kelas) { $nama_kelas = $k['nama_kelas']; break; }
        }
    }

    require __DIR__ . '/../views/cetak_rapor_index.php';
}

function cetak_rapor_preview($pdo) {
    $model  = new CetakRaporModel($pdo);
    $ta     = $model->getActiveTa();
    $id_ta  = $ta['id_ta'] ?? null;

    $id_penempatan = (int)($_GET['id_penempatan'] ?? 0);
    $semester      = (int)($_GET['semester'] ?? 1);

    if (!$id_penempatan) { echo 'Data tidak ditemukan.'; exit; }

    $id_guru  = $_SESSION['id_guru_terkait'] ?? null;
    $is_admin = !empty(array_intersect(['Admin', 'Superadmin'], $_SESSION['roles'] ?? []));

    $stmtK = $pdo->prepare("SELECT id_kelas FROM penempatan_siswa WHERE id_penempatan = ?");
    $stmtK->execute([$id_penempatan]);
    $id_kelas = $stmtK->fetchColumn();

    if (!$is_admin && !$model->isWaliKelas($id_guru, $id_kelas, $id_ta)) {
        http_response_code(403); echo 'Akses ditolak.'; exit;
    }

    $data = $model->getRaporData($id_penempatan, $id_ta, $semester);
    if (!$data) { echo 'Data rapor tidak ditemukan.'; exit; }

    require __DIR__ . '/../views/cetak_rapor_print.php';
    exit;
}

function cetak_rapor_batch($pdo) {
    $model  = new CetakRaporModel($pdo);
    $ta     = $model->getActiveTa();
    $id_ta  = $ta['id_ta'] ?? null;

    $id_kelas = (int)($_GET['id_kelas'] ?? 0);
    $semester = (int)($_GET['semester'] ?? 1);

    $id_guru  = $_SESSION['id_guru_terkait'] ?? null;
    $is_admin = !empty(array_intersect(['Admin', 'Superadmin'], $_SESSION['roles'] ?? []));

    if (!$is_admin && !$model->isWaliKelas($id_guru, $id_kelas, $id_ta)) {
        http_response_code(403); echo 'Akses ditolak.'; exit;
    }

    $siswa_list = $model->getSiswaByKelas($id_kelas, $id_ta, $semester);
    $all_data   = [];
    foreach ($siswa_list as $s) {
        $d = $model->getRaporData($s['id_penempatan'], $id_ta, $semester);
        if ($d) $all_data[] = $d;
    }

    require __DIR__ . '/../views/cetak_rapor_batch.php';
    exit;
}

function cetak_rapor_save_catatan($pdo) {
    $model  = new CetakRaporModel($pdo);
    $ta     = $model->getActiveTa();
    $id_ta  = $ta['id_ta'] ?? null;

    $id_penempatan = (int)($_POST['id_penempatan'] ?? 0);
    $semester      = (int)($_POST['semester'] ?? 1);
    $catatan       = trim($_POST['catatan'] ?? '');
    $is_generated  = (int)($_POST['is_generated'] ?? 0);

    $ok = $model->saveCatatan($id_penempatan, $id_ta, $semester, $catatan, $is_generated);

    header('Content-Type: application/json');
    echo json_encode(['success' => $ok]);
    exit;
}

function cetak_rapor_generate_catatan($pdo) {
    $model  = new CetakRaporModel($pdo);
    $ta     = $model->getActiveTa();
    $id_ta  = $ta['id_ta'] ?? null;

    $id_penempatan = (int)($_GET['id_penempatan'] ?? 0);
    $semester      = (int)($_GET['semester'] ?? 1);

    $catatan = $model->generateCatatanTemplate($id_penempatan, $id_ta, $semester);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'catatan' => $catatan]);
    exit;
}

function cetak_rapor_get_catatan($pdo) {
    $ta    = (new CetakRaporModel($pdo))->getActiveTa();
    $id_ta = $ta['id_ta'] ?? null;

    $id_penempatan = (int)($_GET['id_penempatan'] ?? 0);
    $semester      = (int)($_GET['semester'] ?? 1);

    $stmt = $pdo->prepare("SELECT catatan FROM catatan_wali_kelas WHERE id_penempatan=? AND id_ta=? AND semester=?");
    $stmt->execute([$id_penempatan, $id_ta, $semester]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['catatan' => $row['catatan'] ?? '']);
    exit;
}

