<?php 
// views/utilitas_db_index.php - IMPROVED LAYOUT VERSION
include __DIR__.'/partials/header.php'; 
?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Utilitas &amp; Manajemen Database
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Utilitas DB</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Alerts handled by toast -->
        <?php if(isset($_SESSION['sql_result'])): ?>
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-check"></i> Hasil SQL Query</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="max-height: 300px;">
                    <table class="table table-head-fixed table-hover text-nowrap table-sm">
                        <?php if(!empty($_SESSION['sql_result'])): ?>
                            <thead>
                                <tr>
                                    <?php foreach(array_keys($_SESSION['sql_result'][0]) as $col): ?>
                                        <th><?= htmlspecialchars($col) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($_SESSION['sql_result'] as $row): ?>
                                    <tr>
                                        <?php foreach($row as $val): ?>
                                            <td><?= htmlspecialchars($val) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        <?php else: ?>
                            <tbody><tr><td class="p-3">Query berhasil namun tidak ada data yang dikembalikan.</td></tr></tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <?php unset($_SESSION['sql_result']); ?>
        <?php endif; ?>

        <!-- ROW 0: 3 BLOCKS (Backup & Optimasi, Insert, Restore) -->
        <div class="row mb-3">
             <!-- BLOCK 1: Backup & Optimasi -->
             <div class="col-md-4">
                <div class="card card-primary card-outline h-100 shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-download mr-1"></i> Backup &amp; Optimasi Database</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">Unduh berkas cadangan SQL atau jalankan pembersihan indeks.</p>
                        <div class="btn-group-vertical d-block">
                            <a href="<?= BASE_URL ?>utilitas_db/backup?type=full" class="btn btn-primary mb-2 btn-block text-left shadow-sm font-weight-bold">
                                <i class="fas fa-database mr-2"></i> Backup Full (Struktur + Data)
                            </a>
                            <a href="<?= BASE_URL ?>utilitas_db/backup?type=structure" class="btn btn-info mb-2 btn-block text-left shadow-sm text-white font-weight-bold">
                                <i class="fas fa-project-diagram mr-2"></i> Backup Struktur Saja
                            </a>
                            <a href="<?= BASE_URL ?>utilitas_db/backup?type=data" class="btn btn-secondary mb-2 btn-block text-left shadow-sm font-weight-bold">
                                <i class="fas fa-file-alt mr-2"></i> Backup Data Saja
                            </a>
                            <a href="<?= BASE_URL ?>utilitas_db/optimize" class="btn btn-success btn-block text-left shadow-sm font-weight-bold" onclick="return confirm('Jalankan optimasi dan defragmentasi pada seluruh 180 tabel database?')">
                                <i class="fas fa-tachometer-alt mr-2"></i> Optimasi &amp; Defrag Database
                            </a>
                        </div>
                    </div>
                </div>
             </div>

             <!-- BLOCK 2: Insert Data (SQL Runner) -->
             <div class="col-md-4">
                <div class="card card-navy card-outline h-100 shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-terminal mr-1"></i> Raw SQL Query Runner</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>utilitas_db/run_sql" method="POST">
                            <div class="form-group pb-0 mb-2">
                                <textarea class="form-control form-control-sm" name="sql_query" rows="5" placeholder="Ketik query SQL (SELECT, INSERT, UPDATE, ALTER)..." style="font-family: monospace; font-size: 12px;"><?= $_SESSION['last_query'] ?? '' ?></textarea>
                                <?php unset($_SESSION['last_query']); ?>
                            </div>
                            <button type="submit" class="btn btn-success btn-block shadow-sm font-weight-bold">
                                <i class="fas fa-play mr-2"></i> Jalankan Query SQL
                            </button>
                        </form>
                    </div>
                </div>
             </div>

             <!-- BLOCK 3: Restore Data -->
             <div class="col-md-4">
                <div class="card card-warning card-outline h-100 shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-upload mr-1"></i> Restore Database</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>utilitas_db/restore" method="POST" enctype="multipart/form-data" onsubmit="return confirm('APAKAH ANDA YAKIN? Ini akan menimpa data pada tabel yang ada di dalam berkas backup .sql.')">
                            <p class="text-muted small mb-2">Pilih berkas <code>.sql</code> untuk memulihkan database.</p>
                            <div class="form-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="backup_file" name="backup_file" accept=".sql" required>
                                    <label class="custom-file-label" for="backup_file">Pilih file .sql...</label>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-block shadow-sm font-weight-bold text-dark" type="submit">
                                <i class="fas fa-upload mr-2"></i> Restore Database
                            </button>
                        </form>
                    </div>
                </div>
             </div>
        </div>

        <!-- ROW PATCH RUNNER: DATABASE MIGRATION & PATCHES -->
        <div class="row mb-3" id="patch-section">
            <div class="col-12">
                <div class="card card-indigo card-outline shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold text-indigo">
                            <i class="fas fa-magic mr-2"></i> Patch &amp; Migrasi Database (Database Patch Runner)
                        </h3>
                        <div class="card-tools ml-auto">
                            <span class="badge badge-light border px-2 py-1">
                                <i class="fas fa-folder-open text-primary mr-1"></i> <?= count($available_patches ?? []) ?> File Patch Tersedia
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Nama Berkas Patch</th>
                                        <th>Lokasi Folder</th>
                                        <th>Ukuran</th>
                                        <th>Status Eksekusi</th>
                                        <th>Terakhir Dijalankan</th>
                                        <th style="width: 140px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($available_patches)): ?>
                                        <?php 
                                        $applied_map = [];
                                        foreach ($applied_patches as $ap) {
                                            $applied_map[$ap['patch_name']] = $ap;
                                        }
                                        $no = 1;
                                        foreach ($available_patches as $patch): 
                                            $is_applied = isset($applied_map[$patch['filename']]);
                                            $applied_info = $applied_map[$patch['filename']] ?? null;
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle font-weight-bold text-muted"><?= $no++ ?></td>
                                            <td class="align-middle">
                                                <code class="text-indigo font-weight-bold"><?= htmlspecialchars($patch['filename']) ?></code>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge badge-secondary"><?= htmlspecialchars($patch['dir_name']) ?>/</span>
                                            </td>
                                            <td class="align-middle text-muted small">
                                                <?= number_format($patch['size'] / 1024, 1) ?> KB
                                            </td>
                                            <td class="align-middle">
                                                <?php if ($is_applied): ?>
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sudah Diterapkan</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Dijalankan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-muted small">
                                                <?= $is_applied ? date('d M Y H:i', strtotime($applied_info['executed_at'])) . ' (' . htmlspecialchars($applied_info['executed_by']) . ')' : '-' ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <form action="<?= BASE_URL ?>utilitas_db/run_patch" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menjalankan file patch \'<?= htmlspecialchars($patch['filename']) ?>\'?');">
                                                    <input type="hidden" name="patch_filename" value="<?= htmlspecialchars($patch['filename']) ?>">
                                                    <button type="submit" class="btn <?= $is_applied ? 'btn-outline-secondary' : 'btn-indigo' ?> btn-xs shadow-sm font-weight-bold px-2 py-1">
                                                        <i class="fas <?= $is_applied ? 'fa-redo' : 'fa-play' ?> mr-1"></i>
                                                        <?= $is_applied ? 'Jalankan Ulang' : 'Jalankan Patch' ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-folder-open fa-2x mb-2 d-block text-gray"></i>
                                                Tidak ada berkas <code>.sql</code> ditemukan di folder <code>sql/</code> atau <code>patch/</code>.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW TABEL PER KATEGORI (Mendukung 180+ Tabel Dinamis) -->
        <div class="row">
            <?php
            // Group tables by category
            $tables_by_category = [];
            foreach ($tables as $tbl) {
                $tables_by_category[$tbl['category']][] = $tbl;
            }

            $all_categories = array_keys($tables_by_category);
            $left_categories = [];
            $right_categories = [];
            
            foreach ($all_categories as $idx => $cat_name) {
                if ($idx % 2 == 0) {
                    $left_categories[] = $cat_name;
                } else {
                    $right_categories[] = $cat_name;
                }
            }

            function renderTableCard($categories, $tables_by_category) {
                foreach ($categories as $cat):
                    if (empty($tables_by_category[$cat])) continue;
                    $cat_tables = $tables_by_category[$cat];
                    
                    // Dynamic Colors per Category
                    $card_color = 'primary';
                    if (strpos($cat, 'Master') !== false) $card_color = 'success';
                    elseif (strpos($cat, 'CBT') !== false) $card_color = 'indigo';
                    elseif (strpos($cat, 'LMS') !== false) $card_color = 'teal';
                    elseif (strpos($cat, 'Keuangan') !== false) $card_color = 'navy';
                    elseif (strpos($cat, 'Kesiswaan') !== false) $card_color = 'purple';
                    elseif (strpos($cat, 'Histori') !== false) $card_color = 'warning';
                    elseif (strpos($cat, 'Persuratan') !== false) $card_color = 'maroon';
                    elseif (strpos($cat, 'UKS') !== false || strpos($cat, 'Perpus') !== false || strpos($cat, 'Sarana') !== false) $card_color = 'info';
            ?>
            <div class="card card-<?= $card_color ?> card-outline mb-3 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold"><?= htmlspecialchars($cat) ?></h3>
                    <span class="badge badge-light border ml-auto font-weight-bold"><?= count($cat_tables) ?> Tabel</span>
                </div>
                <div class="card-body table-responsive p-0" style="max-height: 380px;">
                    <form action="<?= BASE_URL ?>utilitas_db" method="POST">
                        <table class="table table-hover table-sm table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th class="pl-3 bg-light border-bottom" style="z-index: 10;">Nama Tabel Data</th>
                                    <th style="width: 90px; z-index: 10;" class="text-right pr-3 bg-light border-bottom">Jml Baris</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cat_tables as $tbl): ?>
                                <tr>
                                    <td class="align-middle pl-3">
                                        <div class="custom-control custom-checkbox custom-checkbox-aligned">
                                            <input class="custom-control-input checkbox-table" type="checkbox" id="tbl_<?= $tbl['name'] ?>" name="selected_tables[]" value="<?= $tbl['name'] ?>">
                                            <label for="tbl_<?= $tbl['name'] ?>" class="custom-control-label" style="font-weight: 500; cursor: pointer; padding-top: 2px;">
                                                <?= htmlspecialchars($tbl['label']) ?>
                                                <small class="text-muted d-block font-weight-normal font-italic" style="font-size: 0.72rem;">`<?= $tbl['name'] ?>`</small>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-right align-middle pr-3 font-weight-bold text-muted"><?= number_format($tbl['rows']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="card-footer p-2 bg-white border-top">
                             <div class="row no-gutters">
                                <div class="col-6 pr-1">
                                    <button type="submit" name="backup_selected" value="1" onclick="this.form.action='<?= BASE_URL ?>utilitas_db/backup'" class="btn btn-primary btn-xs btn-block shadow-sm py-1 font-weight-bold">
                                        <i class="fas fa-download mr-1"></i> Backup
                                    </button>
                                </div>
                                <div class="col-6 pl-1">
                                    <button type="submit" name="truncate_selected" value="1" onclick="this.form.action='<?= BASE_URL ?>utilitas_db/truncate_selected'; return confirm('Yakin ingin mengosongkan tabel terpilih?');" class="btn btn-danger btn-xs btn-block shadow-sm py-1 font-weight-bold">
                                        <i class="fas fa-trash mr-1"></i> Kosongkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php 
                endforeach;
            }
            ?>

            <!-- LEFT COLUMN -->
            <div class="col-lg-6">
                <?php renderTableCard($left_categories, $tables_by_category); ?>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-6">
                <?php renderTableCard($right_categories, $tables_by_category); ?>
                
                <!-- Reset Total Aplikasi Card -->
                <div class="card card-danger card-outline shadow-sm mb-3">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Reset Aplikasi TOTAL (Setelan Pabrik)</h3>
                    </div>
                    <form action="<?= BASE_URL ?>utilitas_db/reset_aplikasi" method="POST" onsubmit="return confirm('ANDA SANGAT YAKIN INGIN MELAKUKAN RESET APLIKASI TOTAL? SEMUA DATA AKAN HILANG PERMANEN!')">
                        <div class="card-body">
                            <p class="text-danger small mb-2"><strong>PERINGATAN PALING SERIUS:</strong> Aksi ini akan mengosongkan seluruh data transaksi dan mengembalikan aplikasi ke kondisi awal pabrik.</p>
                            
                            <div class="form-group mb-0">
                                <label for="confirm_text" class="small">Ketik persis: <strong class="text-danger">RESET APLIKASI</strong></label>
                                <input type="text" id="confirm_text" class="form-control form-control-sm" name="confirm_text" placeholder="Ketik teks konfirmasi..." autocomplete="off">
                            </div>
                        </div>
                        <div class="card-footer p-2 bg-white">
                            <button type="submit" id="btn-reset" class="btn btn-danger btn-sm btn-block font-weight-bold" disabled>
                                <i class="fas fa-skull-crossbones mr-1"></i> Hapus Total &amp; Reset Aplikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirm text validation for reset
    const confirmInput = document.getElementById('confirm_text');
    const resetButton = document.getElementById('btn-reset');
    const requiredText = 'RESET APLIKASI';

    if (confirmInput && resetButton) {
        confirmInput.addEventListener('input', function() {
            if (this.value.trim().toUpperCase() === requiredText) {
                resetButton.disabled = false;
            } else {
                resetButton.disabled = true;
            }
        });
    }

    // File input label update
    const backupFile = document.getElementById('backup_file');
    if (backupFile) {
        backupFile.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
            let label = this.closest('.custom-file').querySelector('.custom-file-label');
            if (label) {
                label.innerText = fileName;
            }
        });
    }

    // Check All functionality
    const checkAll = document.getElementById('check_all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.checkbox-table').forEach(cb => {
                cb.checked = isChecked;
            });
        });
    }
});
</script>
<?php include __DIR__.'/partials/footer.php'; ?>