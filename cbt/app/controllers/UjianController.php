<?php
// Placeholder - will be implemented in Phase 4 & 6
class UjianController
{
    public static function index($pdo, $act)
    {
        $page_title = 'Setting Ujian';
        require_once CBT_ROOT . '/app/views/partials/header.php';
        echo '<section class="content"><div class="container-fluid"><div class="alert alert-info mt-3"><i class="fas fa-info-circle mr-2"></i>Modul Manajemen Ujian akan diimplementasikan pada Fase 4.</div></div></section>';
        require_once CBT_ROOT . '/app/views/partials/footer.php';
    }
    public static function peserta($pdo, $act)
    {
        self::index($pdo, $act);
    }
    public static function hasil($pdo, $act)
    {
        self::index($pdo, $act);
    }
}
