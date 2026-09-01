<?php
/**
 * PermohonanAbsensiController.php
 * Controller untuk sisi PETUGAS (Guru Piket, Wali Kelas, TU, Guru Mapel, Admin)
 *
 * Yang bisa APPROVE/TOLAK : Guru Piket (5), Wali Kelas (14), Admin (1)
 * Yang bisa LIHAT SAJA    : TU (2), Guru (4), semua peran lain yang punya akses menu
 */

require_once __DIR__ . '/../models/PermohonanAbsensiModel.php';

// -------------------------------------------------------------------
// HELPER: cek apakah user punya hak approve
// -------------------------------------------------------------------
function _can_approve_permohonan(): bool
{
    $roles = $_SESSION['role_ids'] ?? [];
    // 1=Admin, 5=Guru Piket, 14=Wali Kelas
    return count(array_intersect([1, 5, 14], $roles)) > 0;
}

// -------------------------------------------------------------------
// 1. INDEX — Daftar semua permohonan dengan filter
// -------------------------------------------------------------------
function permohonan_absensi_index(PDO $pdo): void
{
    if (!check_access('permohonan_absensi')) {
        $_SESSION['pesan_error'] = 'Anda tidak memiliki akses ke fitur ini.';
        redirect('index.php?mod=dashboard');
        return;
    }

    $filters = [
        'status'        => $_GET['status']        ?? 'Menunggu', // Default to Menunggu so it's focused on action items
        'tanggal'       => $_GET['tanggal']        ?? '', // Empty default to show all dates
        'id_kelas'      => $_GET['id_kelas']       ?? '',
        'jenis_absensi' => $_GET['jenis_absensi']  ?? '',
    ];

    $daftar_permohonan = PermohonanAbsensiModel::getDaftar($pdo, $filters);
    $kelas_list        = PermohonanAbsensiModel::getKelasList($pdo);
    $can_approve       = _can_approve_permohonan();

    include __DIR__ . '/../views/permohonan_absensi_index.php';
}

// -------------------------------------------------------------------
// 2. PROSES — Approve atau Tolak permohonan (POST)
// -------------------------------------------------------------------
function permohonan_absensi_proses(PDO $pdo): void
{
    // Hanya Guru Piket, Wali Kelas, Admin
    if (!_can_approve_permohonan()) {
        $_SESSION['pesan_error'] = 'Anda tidak memiliki wewenang untuk memproses permohonan ini.';
        redirect('index.php?mod=permohonan_absensi');
        return;
    }

    $id_permohonan = (int)($_POST['id_permohonan'] ?? 0);
    $action        = $_POST['action'] ?? '';          // 'setujui' atau 'tolak'
    $catatan       = trim($_POST['catatan_petugas'] ?? '');
    $id_petugas    = (int)($_SESSION['user_id'] ?? 0);

    if (!$id_permohonan || !in_array($action, ['setujui', 'tolak'])) {
        $_SESSION['pesan_error'] = 'Data tidak valid.';
        redirect('index.php?mod=permohonan_absensi');
        return;
    }

    $permohonan = PermohonanAbsensiModel::getById($pdo, $id_permohonan);
    if (!$permohonan) {
        $_SESSION['pesan_error'] = 'Permohonan tidak ditemukan.';
        redirect('index.php?mod=permohonan_absensi');
        return;
    }

    if ($permohonan['status'] !== 'Menunggu') {
        $_SESSION['pesan_error'] = 'Permohonan ini sudah diproses sebelumnya.';
        redirect('index.php?mod=permohonan_absensi');
        return;
    }

    $status_baru = ($action === 'setujui') ? 'Disetujui' : 'Ditolak';

    try {
        $pdo->beginTransaction();

        // 1. Update status permohonan
        PermohonanAbsensiModel::updateStatus($pdo, $id_permohonan, $status_baru, $catatan, $id_petugas);

        // 2. Jika disetujui → terapkan ke tabel absensi
        if ($status_baru === 'Disetujui') {
            PermohonanAbsensiModel::applyToAbsensi($pdo, $permohonan);
        }

        $pdo->commit();

        $nama_siswa = htmlspecialchars($permohonan['nama_siswa']);
        if ($status_baru === 'Disetujui') {
            $_SESSION['pesan_sukses'] = "Permohonan {$permohonan['jenis_izin']} siswa {$nama_siswa} telah <b>disetujui</b> dan status absensi diperbarui.";
        } else {
            $_SESSION['pesan_sukses'] = "Permohonan siswa {$nama_siswa} telah <b>ditolak</b>.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['pesan_error'] = 'Gagal memproses permohonan: ' . $e->getMessage();
    }

    // Redirect kembali dengan filter tanggal yang sama
    $tanggal_filter = $permohonan['tanggal'] ?? date('Y-m-d');
    redirect("index.php?mod=permohonan_absensi&tanggal={$tanggal_filter}");
}
