<?php
// app/controllers/AuditLogController.php

require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/PenggunaModel.php'; // Updated: Correct Model Name

function audit_log_index($pdo) {
    // 1. Cek Hak Akses (Hanya Admin)
    if (!has_role('Admin')) {
        // Catat percobaan akses ilegal (Optional)
        // audit_log('ACCESS_DENIED', 'Mencoba akses log audit tanpa izin');
        redirect('index.php?mod=dashboard');
        return;
    }

    // 2. Siapkan Filter
    $filters = [
        'aksi' => $_GET['aksi'] ?? '',
        'id_pengguna' => $_GET['user'] ?? '',
        'tanggal_mulai' => $_GET['start'] ?? '',
        'tanggal_akhir' => $_GET['end'] ?? ''
    ];

    // 3. Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;

    // 4. Ambil Data
    $logs = AuditLogModel::getAll($pdo, $filters, $limit, $offset);
    $total_logs = AuditLogModel::countAll($pdo, $filters);
    $total_pages = ceil($total_logs / $limit);
    
    // Data untuk Dropdown Filter
    $list_aksi = AuditLogModel::getDistinctActions($pdo);
    // Kita butuh model pengguna untuk list user (asumsi ada PenggunaModel::getAll)
    // Jika belum ada, kita query manual simple saja demi efisiensi
    $stmt_users = $pdo->query("SELECT id_pengguna, nama_pengguna FROM pengguna ORDER BY nama_pengguna ASC");
    $list_users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    // 5. Render View
    // Variabel yang dikirim ke view: $logs, $filters, $page, $total_pages, $list_aksi, $list_users
    // Gunakan extract agar coding di view lebih bersih
    extract(compact('logs', 'filters', 'page', 'total_pages', 'list_aksi', 'list_users', 'total_logs'));
    
    include __DIR__ . '/../views/audit_log_index.php';
}
