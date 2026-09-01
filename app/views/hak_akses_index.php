<?php
// app/views/hak_akses_index.php

// 1. INCLUDE HEADER
$path_header = __DIR__ . '/partials/header.php';
if (file_exists($path_header))
    include $path_header;

// ======================================================================
// FUNGSI HELPER UNTUK IKON DAN WARNA
// ======================================================================
if (!function_exists('get_role_style')) {
    function get_role_style($role_name)
    {
        $clean_name = strtolower(str_replace([' ', '-', '_'], '', trim($role_name)));
        $map = [
            'administrator' => ['icon' => 'user-astronaut', 'color' => '#ef4444', 'bg' => '#fef2f2', 'border' => '#fca5a5'],
            'admin'         => ['icon' => 'user-shield',    'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fca5a5'],
            'guru'          => ['icon' => 'chalkboard-teacher', 'color' => '#10b981', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
            'gurupiket'     => ['icon' => 'user-clock',     'color' => '#0d9488', 'bg' => '#f0fdfa', 'border' => '#99f6e4'],
            'kurikulum'     => ['icon' => 'sitemap',        'color' => '#f59e0b', 'bg' => '#fffbeb', 'border' => '#fde68a'],
            'kesiswaan'     => ['icon' => 'user-graduate',  'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'],
            'tu'            => ['icon' => 'briefcase',      'color' => '#64748b', 'bg' => '#f8fafc', 'border' => '#cbd5e1'],
            'kepalasekolah' => ['icon' => 'graduation-cap', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'border' => '#bae6fd'],
            'keuangan'      => ['icon' => 'cash-register',  'color' => '#e11d48', 'bg' => '#fff1f2', 'border' => '#fecdd3'],
            'ppdb'          => ['icon' => 'user-plus',      'color' => '#4f46e5', 'bg' => '#eef2ff', 'border' => '#c7d2fe'],
            'siswa'         => ['icon' => 'user',           'color' => '#0284c7', 'bg' => '#f0f9ff', 'border' => '#bae6fd'],
        ];
        return $map[$clean_name] ?? ['icon' => 'user-tag', 'color' => '#6366f1', 'bg' => '#eef2ff', 'border' => '#c7d2fe'];
    }
}
?>

<style>
    /* Card Grid System */
    .role-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 14px;
    }

    .role-card-modern {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 16px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        position: relative;
        overflow: hidden;
    }

    .role-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -4px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
        text-decoration: none !important;
    }

    .role-card-modern .role-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .role-card-modern .role-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
        line-height: 1.3;
    }

    .role-card-modern .role-meta {
        font-size: 0.74rem;
        color: #64748b;
    }

    .role-progress-bar {
        height: 5px;
        border-radius: 10px;
        background: #f1f5f9;
        overflow: hidden;
        margin: 8px 0 12px 0;
    }

    .role-progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .btn-kelola-akses {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 700;
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .role-card-modern:hover .btn-kelola-akses {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }
</style>

<div class="content-header pt-3 pb-2">
    <div class="container-fluid">
        <!-- HEADER UTAMA -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; background: linear-gradient(135deg, #1e293b 0%, #293548 100%); color: #ffffff;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="gap: 12px;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; color: #38bdf8; flex-shrink: 0;">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-white mb-1" style="font-family: 'Poppins', sans-serif; font-size: 1.15rem;">
                                Manajemen Hak Akses &amp; Perizinan
                            </h4>
                            <p class="mb-0 text-white-50 small">
                                Kelola izin akses modular (Read, Create, Update, Delete) untuk setiap peran pengguna.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <span class="badge px-3 py-2 font-weight-bold text-white" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; font-size: 0.74rem;">
                            <i class="fas fa-users-cog mr-1 text-warning"></i> <?= count($list_peran ?? []) ?> Peran Terdaftar
                        </span>
                        <span class="badge px-3 py-2 font-weight-bold text-white" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; font-size: 0.74rem;">
                            <i class="fas fa-layer-group mr-1 text-info"></i> <?= $total_menu_sistem ?? 0 ?> Modul Menu
                        </span>
                        <a href="<?= BASE_URL ?>peran" class="btn btn-warning btn-sm font-weight-bold px-3 shadow-xs" style="border-radius: 8px; font-size: 0.76rem;">
                            <i class="fas fa-user-tag mr-1"></i> Master Peran
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEARCH & INSTRUCTION BAR -->
        <div class="row align-items-center mb-3">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" id="filter-role-input" class="form-control border-left-0" placeholder="Ketik nama peran untuk mencari..." style="border-radius: 0 8px 8px 0; border-color: #cbd5e1; font-size: 0.84rem;">
                </div>
            </div>
            <div class="col-md-6 col-12 text-md-right text-muted small">
                <i class="fas fa-info-circle text-primary mr-1"></i> Klik kartu peran di bawah untuk mengatur matriks izin menu.
            </div>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="role-grid-container" id="role-grid">
            <?php if (empty($list_peran)): ?>
                <div class="text-center p-5 w-100 bg-white rounded shadow-sm border" style="border-radius: 12px; grid-column: 1 / -1;">
                    <i class="fas fa-ghost fa-3x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Belum ada data peran yang didefinisikan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($list_peran as $p):
                    $id_p = $p['id_peran'];
                    $style = get_role_style($p['nama_peran']);
                    $active_menus = $menu_counts[$id_p] ?? 0;
                    $total_menus = max(1, $total_menu_sistem ?? 1);
                    $pct = min(100, round(($active_menus / $total_menus) * 100));
                ?>
                    <a href="<?= BASE_URL ?>hak_akses?id_peran=<?= $id_p ?>" class="role-card-modern role-item-card" data-name="<?= strtolower($p['nama_peran']) ?>">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="role-icon-box" style="background: <?= $style['bg'] ?>; color: <?= $style['color'] ?>; border: 1px solid <?= $style['border'] ?>;">
                                    <i class="fas fa-<?= $style['icon'] ?>"></i>
                                </div>
                                <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.65rem; border-radius: 6px; font-family: monospace;">
                                    ID: <?= $id_p ?>
                                </span>
                            </div>
                            <div class="role-title"><?= htmlspecialchars($p['nama_peran']) ?></div>
                            <div class="role-meta d-flex justify-content-between align-items-center mt-1">
                                <span>Akses Menu:</span>
                                <strong class="text-dark"><?= $active_menus ?> / <?= $total_menu_sistem ?? 0 ?> Menu</strong>
                            </div>
                            <div class="role-progress-bar">
                                <div class="role-progress-fill" style="width: <?= $pct ?>%; background: <?= $style['color'] ?>;"></div>
                            </div>
                        </div>
                        <div class="btn-kelola-akses mt-2">
                            <i class="fas fa-sliders-h"></i> Atur Hak Akses
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('filter-role-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.role-item-card');
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                if (name.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php
// INCLUDE FOOTER
$path_footer = __DIR__ . '/partials/footer.php';
if (file_exists($path_footer))
    include $path_footer;
?>