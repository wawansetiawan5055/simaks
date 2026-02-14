<?php 
// views/utilitas_db_index.php - IMPROVED LAYOUT VERSION
include __DIR__.'/partials/header.php'; 
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-database"></i> Utilitas Database</h1>
            </div>
        </div>
    </div>
</section>

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

        <!-- ROW 0: 3 BLOCKS (Backup, Insert, Restore) -->
        <div class="row mb-3">
             <!-- BLOCK 1: Backup -->
             <div class="col-md-4">
                <div class="card card-primary card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-download"></i> Backup Data</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Download database sql file.</p>
                        <div class="btn-group-vertical d-block">
                            <a href="index.php?mod=utilitas_db&act=backup&type=full" class="btn btn-primary mb-2 btn-block text-left shadow-sm">
                                <i class="fas fa-database mr-2"></i> Backup Full (Struktur + Data)
                            </a>
                            <a href="index.php?mod=utilitas_db&act=backup&type=structure" class="btn btn-info mb-2 btn-block text-left shadow-sm text-white">
                                <i class="fas fa-project-diagram mr-2"></i> Backup Struktur Saja
                            </a>
                            <a href="index.php?mod=utilitas_db&act=backup&type=data" class="btn btn-secondary btn-block text-left shadow-sm">
                                <i class="fas fa-file-alt mr-2"></i> Backup Data Saja
                            </a>
                        </div>
                    </div>
                </div>
             </div>

             <!-- BLOCK 2: Insert Data (SQL Runner) -->
             <div class="col-md-4">
                <div class="card card-navy card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-terminal"></i> Insert Data (SQL Dump)</h3>
                    </div>
                    <div class="card-body">
                        <form action="index.php?mod=utilitas_db&act=run_sql" method="POST">
                            <div class="form-group pb-0 mb-2">
                                <textarea class="form-control form-control-sm" name="sql_query" rows="5" placeholder="Paste SQL Insert/Dump here..." style="font-family: monospace; font-size: 12px;"><?= $_SESSION['last_query'] ?? '' ?></textarea>
                                <?php unset($_SESSION['last_query']); ?>
                            </div>
                            <button type="submit" class="btn btn-success btn-block shadow-sm font-weight-bold">
                                <i class="fas fa-play mr-2"></i> Jalankan / Insert
                            </button>
                        </form>
                    </div>
                </div>
             </div>

             <!-- BLOCK 3: Restore Data -->
             <div class="col-md-4">
                <div class="card card-warning card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-upload"></i> Restore Data</h3>
                    </div>
                    <div class="card-body">
                        <form action="index.php?mod=utilitas_db&act=restore" method="POST" enctype="multipart/form-data" onsubmit="return confirm('APAKAH ANDA YAKIN? Ini akan menimpa seluruh database Anda saat ini.')">
                            <p class="text-muted small">Upload file .sql untuk restore.</p>
                            <div class="form-group">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="backup_file" name="backup_file" accept=".sql" required>
                                    <label class="custom-file-label" for="backup_file">Pilih file...</label>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-block shadow-sm font-weight-bold text-white" type="submit">
                                <i class="fas fa-upload mr-2"></i> Restore Database
                            </button>
                        </form>
                    </div>
                </div>
             </div>
        </div>

        <!-- ROW 1: Master & Setup (Balanced) -->
        <div class="row">
            <?php
            // Group tables
            $tables_by_category = [];
            foreach ($tables as $tbl) {
                $tables_by_category[$tbl['category']][] = $tbl;
            }

            function renderTableCard($categories, $tables_by_category) {
                foreach ($categories as $cat):
                    if (empty($tables_by_category[$cat])) continue;
                    $cat_tables = $tables_by_category[$cat];
                    
                    // Colors
                    $card_color = 'primary';
                    if ($cat == 'Data Konfigurasi/Setup') $card_color = 'info';
                    if ($cat == 'Data Histori/Log') $card_color = 'warning';
                    if ($cat == 'Data Master') $card_color = 'success';
            ?>
            <div class="card card-<?= $card_color ?> card-outline h-100">
                <div class="card-header">
                    <h3 class="card-title"><?= $cat ?></h3>
                </div>
                <div class="card-body table-responsive p-0" style="max-height: 400px;">
                    <form action="index.php?mod=utilitas_db" method="POST">
                        <table class="table table-hover table-sm table-head-fixed text-nowrap">
                            <thead class="">
                                <tr>
                                    <th class="pl-3 bg-primary text-white border-0" style="z-index: 10;">Nama Data</th>
                                    <th style="width: 80px; z-index: 10;" class="text-right bg-primary text-white border-0">Jml Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cat_tables as $tbl): ?>
                                <tr>
                                    <td class="align-middle pl-3">
                                        <div class="custom-control custom-checkbox custom-checkbox-aligned">
                                            <input class="custom-control-input checkbox-table" type="checkbox" id="tbl_<?= $tbl['name'] ?>" name="selected_tables[]" value="<?= $tbl['name'] ?>">
                                            <label for="tbl_<?= $tbl['name'] ?>" class="custom-control-label" style="font-weight: normal; cursor: pointer; padding-top: 4px;">
                                                <?= $tbl['label'] ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-right align-middle font-weight-bold text-muted"><?= number_format($tbl['rows']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="card-footer p-2 bg-white border-top-0">
                             <div class="row no-gutters">
                                <div class="col-6 pr-1">
                                    <button type="submit" name="backup_selected" value="1" onclick="this.form.action='index.php?mod=utilitas_db&act=backup'" class="btn btn-primary btn-sm btn-block shadow-sm">
                                        <i class="fas fa-download mr-1"></i> Backup
                                    </button>
                                </div>
                                <div class="col-6 pl-1">
                                    <button type="submit" name="truncate_selected" value="1" onclick="this.form.action='index.php?mod=utilitas_db&act=truncate_selected'; return confirm('Yakin ingin kosongkan/hapus data terpilih?');" class="btn btn-danger btn-sm btn-block shadow-sm">
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

            <!-- LEFT: Master -->
            <div class="col-lg-6 mb-3">
                <?php renderTableCard(['Data Master'], $tables_by_category); ?>
            </div>

            <!-- RIGHT: Setup -->
            <div class="col-lg-6 mb-3">
                <?php renderTableCard(['Data Konfigurasi/Setup'], $tables_by_category); ?>
            </div>
        </div>

        <style>
            /* Correction for vertical alignment of checkbox with padded text */
            .custom-control.custom-checkbox-aligned .custom-control-label::before,
            .custom-control.custom-checkbox-aligned .custom-control-label::after {
                top: 0.5rem; /* Shift down 4px from default 0.25rem to match text padding */
            }
        </style>

        <!-- ROW 2: History & Reset -->
        <div class="row">
            <!-- LEFT: History -->
            <div class="col-lg-6 mb-3">
                 <?php renderTableCard(['Data Histori/Log'], $tables_by_category); ?>
                 <?php renderTableCard(['Lainnya'], $tables_by_category); ?>
            </div>

            <!-- RIGHT: Reset Aplikasi -->
            <div class="col-lg-6 mb-3">
                <div class="card card-danger card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Reset Aplikasi TOTAL (TRUNCATE)</h3>
                    </div>
                    <form action="index.php?mod=utilitas_db&act=reset_aplikasi" method="POST" onsubmit="return confirm('ANDA SANGAT YAKIN INGIN MELAKUKAN RESET APLIKASI TOTAL? SEMUA DATA AKAN HILANG PERMANEN!')">
                        <div class="card-body">
                            <p class="text-danger"><strong>PERINGATAN PALING SERIUS:</strong> Aksi ini akan menghapus **SEMUA DATA** di database. Digunakan untuk mengembalikan aplikasi ke "Setelan Pabrik".</p>
                            
                            <div class="form-group mb-0">
                                <label for="confirm_text">Ketik: <strong class="text-danger">RESET APLIKASI</strong></label>
                                <input type="text" id="confirm_text" class="form-control" name="confirm_text" placeholder="Ketik teks konfirmasi..." autocomplete="off">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="btn-reset" class="btn btn-danger btn-block" disabled>
                                <i class="fas fa-skull-crossbones"></i> Hapus Total & Reset Aplikasi
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