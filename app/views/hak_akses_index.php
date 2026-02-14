<?php
// app/views/hak_akses_index.php

// 1. INCLUDE HEADER
$path_header = __DIR__ . '/partials/header.php';
if (file_exists($path_header))
    include $path_header;

// ======================================================================
// FUNGSI HELPER UNTUK IKON DAN WARNA (Memastikan warna unik untuk 9 peran)
// ======================================================================
if (!function_exists('get_role_style')) {
    function get_role_style($role_name)
    {
        $clean_name = strtolower(str_replace(' ', '', trim($role_name)));
        $map = [
            'administrator' => ['icon' => 'user-astronaut', 'color' => 'bg-primary'],
            'admin' => ['icon' => 'user-shield', 'color' => 'bg-primary'],
            'guru' => ['icon' => 'chalkboard-teacher', 'color' => 'bg-info'],
            'gurupiket' => ['icon' => 'user-clock', 'color' => 'bg-teal'],
            'kurikulum' => ['icon' => 'sitemap', 'color' => 'bg-warning'],
            'kesiswaan' => ['icon' => 'shield-alt', 'color' => 'bg-danger'],
            'tu' => ['icon' => 'tools', 'color' => 'bg-secondary'],
            'kepalasekolah' => ['icon' => 'graduation-cap', 'color' => 'bg-success'],
            'keuangan' => ['icon' => 'cash-register', 'color' => 'bg-maroon'],
            'ppdb' => ['icon' => 'user-plus', 'color' => 'bg-indigo'],
        ];
        // Menggunakan array merge untuk mengantisipasi jika role_name_asli tersedia
        $style = $map[$clean_name] ?? ['icon' => 'user-tag', 'color' => 'bg-gray'];
        return $style;
    }
}
?>

<style>
    /* Style Premium untuk Grid Peran */
    .role-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }

    .role-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        text-decoration: none !important;
    }

    .role-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .role-card-top {
        padding: 15px;
        text-align: center;
        flex-grow: 1;
    }

    .role-icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 1.4rem;
        position: relative;
    }

    .role-icon-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 12px;
        opacity: 0.15;
    }

    .role-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
        margin-bottom: 4px;
        display: block;
    }

    .role-id-badge {
        font-size: 0.65rem;
        color: #64748b;
        font-family: 'JetBrains Mono', monospace;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 100px;
    }

    .role-footer {
        padding: 8px 15px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .role-card:hover .role-footer {
        background: #2563eb;
        color: #fff;
    }

    .role-footer i {
        margin-right: 6px;
        font-size: 0.8rem;
    }

    /* Accent Colors */
    .role-accent-admin {
        color: #dc2626;
        border-top: 3px solid #dc2626;
    }

    .role-accent-admin .role-icon-wrapper {
        background: #fef2f2;
        color: #dc2626;
    }

    .role-accent-guru {
        color: #16a34a;
        border-top: 3px solid #16a34a;
    }

    .role-accent-guru .role-icon-wrapper {
        background: #f0fdf4;
        color: #16a34a;
    }

    .role-accent-siswa {
        color: #2563eb;
        border-top: 3px solid #2563eb;
    }

    .role-accent-siswa .role-icon-wrapper {
        background: #eff6ff;
        color: #2563eb;
    }

    .role-accent-default {
        color: #475569;
        border-top: 3px solid #64748b;
    }

    .role-accent-default .role-icon-wrapper {
        background: #f8fafc;
        color: #64748b;
    }
</style>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1><i class="fas fa-key mr-2"></i> Manajemen Hak Akses</h1>
                <p class="text-muted small mb-0">Konfigurasi matriks perizinan modul untuk setiap peran pengguna di
                    sistem.</p>
            </div>
            <a href="index.php?mod=peran" class="btn btn-outline-primary btn-sm px-3 shadow-none"
                style="border-radius: 8px;">
                <i class="fas fa-user-tag mr-1"></i> Manajemen Peran
            </a>
        </div>

        <div class="alert alert-info border-0 shadow-sm mb-4"
            style="border-radius: 12px; background: linear-gradient(to right, #eff6ff, #fff); border-left: 4px solid #3b82f6 !important;">
            <div class="d-flex align-items-center">
                <div class="mr-3 bg-primary text-white p-2 rounded-circle"
                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="small">
                    <strong>Pilih Peran:</strong> Klik kartu peran di bawah untuk mengelola perizinan menu (Lihat,
                    Tambah, Ubah, Hapus) secara detail.
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="role-grid-container mb-4">
            <?php if (empty($list_peran)): ?>
                <div class="text-center p-5 w-100 bg-white rounded shadow-sm border" style="border-radius: 15px;">
                    <i class="fas fa-ghost fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data peran yang didefinisikan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($list_peran as $p):
                    $style = get_role_style($p['nama_peran']);
                    $accent_class = 'role-accent-default';
                    $role_low = strtolower($p['nama_peran']);
                    if (strpos($role_low, 'admin') !== false)
                        $accent_class = 'role-accent-admin';
                    elseif (strpos($role_low, 'guru') !== false || strpos($role_low, 'kependidikan') !== false || strpos($role_low, 'gtk') !== false)
                        $accent_class = 'role-accent-guru';
                    elseif (strpos($role_low, 'siswa') !== false)
                        $accent_class = 'role-accent-siswa';
                    ?>
                    <a href="index.php?mod=hak_akses&id_peran=<?= $p['id_peran'] ?>" class="role-card <?= $accent_class ?>">
                        <div class="role-card-top">
                            <div class="role-icon-wrapper">
                                <i class="fas fa-<?= $style['icon'] ?>"></i>
                            </div>
                            <span class="role-name"><?= htmlspecialchars($p['nama_peran']) ?></span>
                            <span class="role-id-badge">ID: <?= $p['id_peran'] ?></span>
                        </div>
                        <div class="role-footer small">
                            <i class="fas fa-sliders-h"></i> KELOLA AKSES
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
// INCLUDE FOOTER
$path_footer = __DIR__ . '/partials/footer.php';
if (file_exists($path_footer))
    include $path_footer;
?>