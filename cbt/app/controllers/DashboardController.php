<?php
/**
 * CBT - DashboardController
 */
class DashboardController
{

    public static function index($pdo)
    {
        // Statistik ringkas
        $stats = [
            'total_soal' => $pdo->query("SELECT COUNT(*) FROM cbt_soal")->fetchColumn(),
            'total_ujian' => $pdo->query("SELECT COUNT(*) FROM cbt_jadwal")->fetchColumn(),
            'ujian_aktif' => $pdo->query("SELECT COUNT(*) FROM cbt_jadwal WHERE status = 'aktif'")->fetchColumn(),
            'total_siswa' => $pdo->query("SELECT COUNT(*) FROM cbt_siswa")->fetchColumn(),
        ];

        // 5 jadwal ujian terbaru
        $jadwal_terbaru = $pdo->query("
            SELECT j.nama_ujian, j.tanggal_mulai, j.status,
                   (SELECT COUNT(*) FROM cbt_peserta p WHERE p.id_jadwal = j.id_jadwal) as jml_peserta
            FROM cbt_jadwal j
            ORDER BY j.created_at DESC LIMIT 5
        ")->fetchAll();

        require_once CBT_ROOT . '/app/views/dashboard/index.php';
    }
}
