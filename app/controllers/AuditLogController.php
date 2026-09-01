<?php
// app/controllers/AuditLogController.php

require_once __DIR__ . '/../models/AuditLogModel.php';

function audit_log_index($pdo) {
    // 1. Cek Hak Akses (Hanya Admin)
    if (!has_role('Admin')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Hanya Administrator yang dapat melihat Audit Log Aktivitas.";
        redirect('index.php?mod=dashboard');
        return;
    }

    // 2. Siapkan Filter & Pencarian
    $filters = [
        'q'             => trim($_GET['q'] ?? ''),
        'aksi'          => trim($_GET['aksi'] ?? ''),
        'id_pengguna'   => trim($_GET['user'] ?? ''),
        'tanggal_mulai' => trim($_GET['start'] ?? ''),
        'tanggal_akhir' => trim($_GET['end'] ?? '')
    ];

    // 3. Pagination & Limit
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(10, min(200, (int)$_GET['limit'])) : 30;
    $offset = ($page - 1) * $limit;

    // 4. Ambil Data Log & Statistik Ringkasan
    $logs = AuditLogModel::getAll($pdo, $filters, $limit, $offset);
    $total_logs = AuditLogModel::countAll($pdo, $filters);
    $total_pages = ceil($total_logs / $limit);
    $summary_stats = AuditLogModel::getSummaryStats($pdo);
    
    // Data untuk Dropdown Filter Aksi
    $list_aksi = AuditLogModel::getDistinctActions($pdo);

    // List Pengguna Lengkap dengan Nama Asli & Peran
    $sql_users = "SELECT 
                    u.id_pengguna, 
                    u.username,
                    u.nama_pengguna,
                    COALESCE(g.nama, s.nama, u.nama_pengguna) AS nama_lengkap,
                    (SELECT GROUP_CONCAT(DISTINCT p.nama_peran SEPARATOR ', ')
                     FROM pengguna_peran pp 
                     JOIN peran p ON pp.id_peran = p.id_peran 
                     WHERE pp.id_pengguna = u.id_pengguna) AS roles
                  FROM pengguna u 
                  LEFT JOIN guru g ON u.id_pengguna = g.id_pengguna
                  LEFT JOIN siswa s ON u.id_pengguna = s.id_pengguna
                  ORDER BY nama_lengkap ASC";
    $list_users = $pdo->query($sql_users)->fetchAll(PDO::FETCH_ASSOC);

    // 5. Render View
    extract(compact('logs', 'filters', 'page', 'limit', 'total_pages', 'list_aksi', 'list_users', 'total_logs', 'summary_stats'));
    
    include __DIR__ . '/../views/audit_log_index.php';
}

