<?php
require_once __DIR__ . '/../models/PenugasanModel.php';

function penugasan_guru_index($pdo)
{
    if (!check_access('penugasan_guru', 'index'))
        redirect('index.php');

    // Tampilkan data berdasarkan TA yang DIPILIH
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_tampil) {
        $_SESSION['pesan_error'] = "Tahun Ajaran belum diatur.";
        redirect('index.php?mod=ta');
    }

    $data = [
        'walas_available_guru' => PenugasanModel::walas_available_guru($pdo, $id_ta_tampil),
        'walas_available_kelas' => PenugasanModel::walas_available_kelas($pdo, $id_ta_tampil),
        'walas_list' => PenugasanModel::walas_list($pdo, $id_ta_tampil),
        // [BARU] Data Jabatan
        'jabatan_list' => PenugasanModel::jabatan_list($pdo, $id_ta_tampil),
        'master_jabatan_guru' => PenugasanModel::getJabatanByKategori($pdo, 'GURU'),
        'master_jabatan_staff' => PenugasanModel::getJabatanByKategori($pdo, 'STAFF'),
        // [BARU] Data Pembina Non-Akademik
        'pembina_list' => PenugasanModel::pembina_list($pdo, $id_ta_tampil),
        'master_kegiatan_nona' => PenugasanModel::getMasterKegiatanNonAkademik($pdo),
        'all_guru' => PenugasanModel::all_guru($pdo),
        'all_mapel' => PenugasanModel::all_mapel($pdo),
        'guru_mapel_list' => PenugasanModel::guru_mapel_list($pdo, $id_ta_tampil),
        'nama_ta' => $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '',
        // [BARU] Active Tab Logic
        'active_tab' => $_GET['tab'] ?? 'walas'
    ];
    extract($data);
    include __DIR__ . '/../views/penugasan_guru_index.php';
}

function penugasan_walas_save($pdo)
{
    if (!can_do($pdo, 'penugasan_guru', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan data.";
        redirect('index.php?mod=penugasan_guru');
        return;
    }

    // Simpan data HANYA ke TA AKTIF
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif)
        die("Tidak ada TA Aktif untuk menyimpan data.");

    try {
        PenugasanModel::walas_save($pdo, $_POST['id_guru'], $_POST['id_kelas'], $id_ta_aktif);
        $_SESSION['pesan_sukses'] = "Wali kelas berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan wali kelas: " . $e->getMessage();
    }
    redirect('index.php?mod=penugasan_guru&tab=walas');
}

function penugasan_guru_mapel_save($pdo)
{
    if (!can_do($pdo, 'penugasan_guru', 'create')) { // Asumsi Create = Assign
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan data.";
        redirect('index.php?mod=penugasan_guru&tab=mapel');
        return;
    }

    // Simpan data HANYA ke TA AKTIF
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif)
        die("Tidak ada TA Aktif untuk menyimpan data.");

    try {
        PenugasanModel::guru_mapel_save($pdo, $_POST['id_guru'], $_POST['id_mapel'], $id_ta_aktif);
        $_SESSION['pesan_sukses'] = "Guru mapel berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan guru mapel: " . $e->getMessage();
    }
    redirect('index.php?mod=penugasan_guru&tab=mapel');
}

function penugasan_walas_delete($pdo, $id)
{
    if (!can_do($pdo, 'penugasan_guru', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=penugasan_guru&tab=walas');
        return;
    }

    try {
        PenugasanModel::walas_delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Wali kelas berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=penugasan_guru&tab=walas');
}

function penugasan_guru_mapel_delete($pdo, $id)
{
    if (!can_do($pdo, 'penugasan_guru', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=penugasan_guru&tab=mapel');
        return;
    }

    try {
        PenugasanModel::guru_mapel_delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Guru mapel berhasil dihapus.";
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            $_SESSION['pesan_error'] = "Gagal menghapus: Guru mapel ini mungkin sudah memiliki jadwal. Hapus jadwal terkait terlebih dahulu.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=penugasan_guru&tab=mapel');
}

function penugasan_jabatan_save($pdo)
{
    if (!can_do($pdo, 'penugasan_guru', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=penugasan_guru&tab=jabatan');
        return;
    }

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif) {
        $_SESSION['pesan_error'] = "Tidak ada Tahun Ajaran Aktif.";
        redirect('index.php?mod=penugasan_guru&tab=jabatan');
        return;
    }

    try {
        PenugasanModel::jabatan_save($pdo, $_POST['id_guru'], $_POST['jenis_jabatan'], $id_ta_aktif);
        $_SESSION['pesan_sukses'] = "Jabatan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan jabatan: " . $e->getMessage();
    }
    $tab = $_POST['tab'] ?? 'jabatan_guru';
    redirect('index.php?mod=penugasan_guru&tab=' . $tab);
}

function penugasan_jabatan_delete($pdo, $id)
{
    if (!can_do($pdo, 'penugasan_guru', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=penugasan_guru&tab=jabatan');
        return;
    }

    try {
        PenugasanModel::jabatan_delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Jabatan berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    $tab = $_GET['tab'] ?? 'jabatan_guru';
    redirect('index.php?mod=penugasan_guru&tab=' . $tab);
}

function penugasan_pembina_save($pdo)
{
    if (!can_do($pdo, 'penugasan_guru', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=penugasan_guru&tab=pembina');
        return;
    }

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif) {
        $_SESSION['pesan_error'] = "Tidak ada Tahun Ajaran Aktif.";
        redirect('index.php?mod=penugasan_guru&tab=pembina');
        return;
    }

    try {
        PenugasanModel::pembina_save($pdo, $_POST['id_kegiatan'], $_POST['id_guru'], $id_ta_aktif);
        $_SESSION['pesan_sukses'] = "Pembina berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan pembina: " . $e->getMessage();
    }
    redirect('index.php?mod=penugasan_guru&tab=pembina');
}

function penugasan_pembina_delete($pdo, $id)
{
    if (!can_do($pdo, 'penugasan_guru', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=penugasan_guru&tab=pembina');
        return;
    }

    try {
        PenugasanModel::pembina_delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Pembina berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=penugasan_guru&tab=pembina');
}