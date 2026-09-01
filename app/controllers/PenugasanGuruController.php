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
        'all_kelas' => PenugasanModel::all_kelas($pdo, $id_ta_tampil), 
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
        $id_mapel_list = $_POST['id_mapel'] ?? [];
        $id_kelas_list = $_POST['id_kelas'] ?? [];
        
        if (empty($id_mapel_list) || empty($id_kelas_list)) {
            $_SESSION['pesan_error'] = "Pilih minimal satu mata pelajaran dan satu kelas.";
            redirect('index.php?mod=penugasan_guru&tab=mapel');
            return;
        }

        $total_saved = 0;
        foreach ($id_mapel_list as $id_mapel) {
            foreach ($id_kelas_list as $id_kelas) {
                PenugasanModel::guru_mapel_save($pdo, $_POST['id_guru'], $id_mapel, $id_kelas, $id_ta_aktif);
                $total_saved++;
            }
        }
        
        $_SESSION['pesan_sukses'] = "Berhasil menyimpan $total_saved penugasan guru mapel.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan guru mapel: " . $e->getMessage();
    }
    redirect('index.php?mod=penugasan_guru&tab=mapel');
}

function penugasan_guru_mapel_update($pdo)
{
    if (!can_do($pdo, 'penugasan_guru', 'update') && !can_do($pdo, 'penugasan_guru', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk mengubah data.";
        redirect('index.php?mod=penugasan_guru&tab=mapel');
        return;
    }

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif)
        die("Tidak ada TA Aktif untuk menyimpan data.");

    try {
        $id_guru = (int)($_POST['id_guru'] ?? 0);
        $id_mapel_list = $_POST['id_mapel'] ?? [];
        $id_kelas_list = $_POST['id_kelas'] ?? [];

        if (!$id_guru || empty($id_mapel_list) || empty($id_kelas_list)) {
            $_SESSION['pesan_error'] = "Pilih minimal satu mata pelajaran dan satu kelas.";
            redirect('index.php?mod=penugasan_guru&tab=mapel');
            return;
        }

        PenugasanModel::guru_mapel_update($pdo, $id_guru, $id_mapel_list, $id_kelas_list, $id_ta_aktif);
        $_SESSION['pesan_sukses'] = "Penugasan guru mapel berhasil diperbarui.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal memperbarui guru mapel: " . $e->getMessage();
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
        // ID bisa berupa satu angka atau list ID dipisahkan koma (dari grouped list)
        $ids = explode(',', $id);
        foreach ($ids as $id_item) {
            PenugasanModel::guru_mapel_delete($pdo, $id_item);
        }
        $_SESSION['pesan_sukses'] = "Penugasan guru mapel berhasil dihapus.";
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
        $jabatan_list = $_POST['jenis_jabatan'] ?? [];
        if (empty($jabatan_list)) {
            $_SESSION['pesan_error'] = "Pilih minimal satu jabatan.";
            $tab = $_POST['tab'] ?? 'jabatan_guru';
            redirect('index.php?mod=penugasan_guru&tab=' . $tab);
            return;
        }

        foreach ($jabatan_list as $jabatan) {
            PenugasanModel::jabatan_save($pdo, $_POST['id_guru'], $jabatan, $id_ta_aktif);
        }
        
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
        $ids = explode(',', $id);
        foreach ($ids as $id_item) {
            PenugasanModel::jabatan_delete($pdo, $id_item);
        }
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