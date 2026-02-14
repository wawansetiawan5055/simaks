<?php include __DIR__ . '/partials/header.php'; ?>

<!-- Custom Styles for Drag and Drop -->
<style>
    .drag-container {
        display: flex;
        gap: 24px;
        align-items: flex-start;
    }

    .drag-column {
        flex: 1;
        min-width: 0; /* Prevent flex overflow */
    }

    .student-list {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        min-height: 500px;
        padding: 16px;
        list-style-type: none;
        margin: 0;
        transition: background 0.2s, border-color 0.2s;
    }

    .student-list.highlight-hover {
        background: #eff6ff;
        border-color: #3b82f6;
    }

    .student-item {
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 12px 16px;
        margin-bottom: 12px;
        border-radius: 10px;
        cursor: grab;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .student-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    .student-item:active {
        cursor: grabbing;
    }

    .student-item .student-avatar {
        width: 36px;
        height: 36px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
        margin-right: 12px;
    }

    .student-item .student-info {
        flex: 1;
        min-width: 0;
    }

    .student-item .student-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    .student-item .student-nisn {
        font-size: 0.8rem;
        color: #64748b;
        display: block;
    }

    .ui-sortable-placeholder {
        height: 60px;
        background-color: #f1f5f9 !important;
        border: 2px dashed #94a3b8;
        border-radius: 10px;
        margin-bottom: 12px;
        visibility: visible !important;
    }

    .ui-sortable-helper {
        background-color: #fff !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        transform: scale(1.02);
        opacity: 0.95;
    }
</style>

<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="m-0 font-weight-bold text-dark">
                    <a href="index.php?mod=penempatan" class="text-dark hover-primary" style="text-decoration: none;">
                        <i class="fas fa-layer-group text-primary mr-2"></i> Penempatan Siswa
                    </a>
                </h4>
                <p class="text-muted small mb-0">
                    Kelola anggota rombel dengan drag & drop (TA: <?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') ?>)
                </p>
            </div>
            <a href="index.php?mod=penempatan" class="btn btn-outline-secondary shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php // Session messages handled via toast/js ?>

        <!-- PILIH KELAS TARGET (Sticky-like) -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
            <div class="card-body p-3">
                <form method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="mod" value="penempatan">
                    <input type="hidden" name="act" value="kelola"> <!-- Ensure we stay on kelola -->
                    <label class="mb-0 mr-3 font-weight-bold text-muted"><i class="fas fa-chalkboard-teacher mr-2"></i> Pilih Kelas Tujuan:</label>
                    <select name="id_kelas" class="form-control form-control-lg border-0 bg-light" style="flex: 1; border-radius: 8px;" onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?> 
                                (Wali: <?= htmlspecialchars($k['nama_walas'] ?? 'Kosong') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <?php if ($id_kelas_filter): ?>
            <div class="drag-container">

                <!-- KOLOM KIRI: SISWA DI KELAS -->
                <div class="drag-column">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                        <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 font-weight-bold text-success">
                                <i class="fas fa-users mr-2"></i> ANGGOTA: <?= htmlspecialchars($info_kelas['nama_kelas']) ?>
                            </h6>
                            <span class="badge badge-success px-3 py-2 shadow-sm rounded-pill" id="count-assigned">
                                <?= count($assigned_students) ?> Siswa
                            </span>
                        </div>
                        <div class="card-body p-3 bg-light">
                            <ul id="assigned-list" class="student-list connected-lists" data-id-kelas="<?= $id_kelas_filter ?>">
                                <?php if (empty($assigned_students)): ?>
                                    <li class="text-muted text-center no-drag py-5">
                                        <i class="fas fa-user-friends fa-2x mb-3 text-muted opacity-25"></i><br>
                                        Belum ada siswa di kelas ini.<br>
                                        <small>Tarik siswa dari kolom kanan.</small>
                                    </li>
                                <?php endif; ?>
                                <?php foreach ($assigned_students as $s): ?>
                                    <li class="student-item" data-id-siswa="<?= $s['id_siswa'] ?>">
                                        <div class="student-avatar">
                                            <?= strtoupper(substr($s['nama'], 0, 1)) ?>
                                        </div>
                                        <div class="student-info">
                                            <span class="student-name" title="<?= htmlspecialchars($s['nama']) ?>"><?= htmlspecialchars($s['nama']) ?></span>
                                            <span class="student-nisn">NISN: <?= htmlspecialchars($s['nisn']) ?></span>
                                        </div>
                                        <i class="fas fa-grip-vertical text-muted opacity-50 ml-2"></i>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: SISWA BELUM DITEMPATKAN -->
                <div class="drag-column">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                        <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 font-weight-bold text-warning">
                                <i class="fas fa-user-plus mr-2"></i> 
                                <?= ($id_ta_sumber && $id_kelas_sumber) ? 'DARI SUMBER' : 'BELUM DITEMPATKAN' ?>
                            </h6>
                            <span class="badge badge-warning px-3 py-2 shadow-sm rounded-pill text-white" id="count-unassigned">
                                <?= ($id_ta_sumber && $id_kelas_sumber) ? count($source_students) : count($unassigned_students) ?> Siswa
                            </span>
                        </div>
                        
                        <!-- FILTER / SUMBER SECTION -->
                        <div class="px-3 pb-3">
                            <div class="bg-white p-3 rounded shadow-sm border" style="border-radius: 12px;">
                                <form method="GET" id="form-source-filter">
                                    <input type="hidden" name="mod" value="penempatan">
                                    <?php if(isset($_GET['act']) && $_GET['act'] == 'kelola'): ?>
                                        <input type="hidden" name="act" value="kelola">
                                    <?php endif; ?>
                                    <input type="hidden" name="id_kelas" value="<?= $id_kelas_filter ?>">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="small text-muted font-weight-bold text-uppercase mb-0">Sumber Data Siswa</label>
                                        <?php if ($id_ta_sumber || $id_kelas_sumber): ?>
                                            <a href="index.php?mod=penempatan&act=kelola&id_kelas=<?= $id_kelas_filter ?>"
                                                class="btn btn-xs btn-danger shadow-none rounded-pill px-2" title="Reset Filter">
                                                <i class="fas fa-times mr-1"></i> Reset
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="row no-gutters">
                                        <div class="col-6 pr-1">
                                            <select name="source_ta" class="form-control form-control-sm bg-light border-0" onchange="this.form.submit()" style="border-radius: 6px;">
                                                <option value="">-- Semua TA --</option>
                                                <?php foreach ($ta_list as $t): ?>
                                                    <option value="<?= $t['id_ta'] ?>" <?= ($id_ta_sumber == $t['id_ta']) ? 'selected' : '' ?>>
                                                        <?= $t['nama_ta'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-6 pl-1">
                                            <select name="source_kelas" class="form-control form-control-sm bg-light border-0" onchange="this.form.submit()" style="border-radius: 6px;">
                                                <option value="">-- Semua Kelas --</option>
                                                <?php foreach ($source_kelas_list as $sk): ?>
                                                    <option value="<?= $sk['id_kelas'] ?>" <?= ($id_kelas_sumber == $sk['id_kelas']) ? 'selected' : '' ?>>
                                                        <?= $sk['nama_kelas'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>

                                <?php if ($id_ta_sumber && $id_kelas_sumber && !empty($source_students)): ?>
                                    <form method="POST" action="index.php?mod=penempatan&act=copy_rombel" class="mt-2"
                                        onsubmit="return confirm('Salin semua siswa ini ke kelas tujuan?');">
                                        <input type="hidden" name="target_kelas" value="<?= $id_kelas_filter ?>">
                                        <input type="hidden" name="source_ta" value="<?= $id_ta_sumber ?>">
                                        <input type="hidden" name="source_kelas" value="<?= $id_kelas_sumber ?>">
                                        <button type="submit" class="btn btn-sm btn-primary btn-block shadow-sm" style="border-radius: 8px;">
                                            <i class="fas fa-copy mr-1"></i> Salin Semua (<?= count($source_students) ?>)
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body p-3 bg-light border-top">
                            <ul id="unassigned-list" class="student-list connected-lists">
                                <?php
                                $display_list = ($id_ta_sumber && $id_kelas_sumber) ? $source_students : $unassigned_students;
                                ?>

                                <?php if (empty($display_list)): ?>
                                    <li class="text-muted text-center no-drag py-5">
                                        <i class="fas fa-search fa-2x mb-3 text-muted opacity-25"></i><br>
                                        Tidak ada siswa.<br>
                                        <small>Coba ubah filter sumber.</small>
                                    </li>
                                <?php endif; ?>

                                <?php foreach ($display_list as $s): ?>
                                    <li class="student-item" data-id-siswa="<?= $s['id_siswa'] ?>">
                                        <div class="student-avatar bg-white border text-secondary">
                                            <?= strtoupper(substr($s['nama'], 0, 1)) ?>
                                        </div>
                                        <div class="student-info">
                                            <span class="student-name" title="<?= htmlspecialchars($s['nama']) ?>"><?= htmlspecialchars($s['nama']) ?></span>
                                            <span class="student-nisn">NISN: <?= htmlspecialchars($s['nisn']) ?></span>
                                        </div>
                                        <i class="fas fa-grip-vertical text-muted opacity-50 ml-2"></i>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    $(document).ready(function () {
        if (typeof $.ui === 'undefined') {
            if(window.Notify) Notify.error('System Error', 'jQuery UI tidak dimuat.');
            else alert('jQuery UI tidak dimuat.');
            return;
        }

        const safeNotify = {
            success: (title, msg) => {
                if (window.Notify) window.Notify.success(title, msg);
                else console.log('Success:', title, msg);
            },
            error: (title, msg) => {
                if (window.Notify) window.Notify.error(title, msg);
                else alert(title + ': ' + msg);
            }
        };

        const idTa = "<?= $id_ta_tampil ?? '' ?>";
        const idKelas = "<?= $id_kelas_filter ?? 0 ?>";

        function updateCounts() {
            $('#count-assigned').text($('#assigned-list li.student-item').length + ' Siswa');
            $('#count-unassigned').text($('#unassigned-list li.student-item').length + ' Siswa');
        }

        $("#assigned-list, #unassigned-list").sortable({
            connectWith: ".connected-lists",
            placeholder: "ui-sortable-placeholder",
            items: "li.student-item",
            cursor: "grabbing", // Better cursor
            
            receive: function (event, ui) {
                const idSiswa = ui.item.data('id-siswa');
                const targetListId = $(this).attr('id');

                if (targetListId === 'assigned-list') {
                    // Assign to Class
                    $.ajax({
                        url: 'index.php?mod=penempatan&act=save',
                        type: 'POST',
                        data: {
                            id_siswa: idSiswa,
                            id_kelas: idKelas,
                            id_ta: idTa
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                safeNotify.success('Tersimpan', response.message);
                                // Optional visual feedback
                            } else {
                                safeNotify.error('Gagal', response.message);
                                $(ui.sender).sortable('cancel');
                            }
                            updateCounts();
                        },
                        error: function (xhr) {
                            safeNotify.error('Error', 'Gagal menyimpan data.');
                            $(ui.sender).sortable('cancel');
                            updateCounts();
                        }
                    });

                } else if (targetListId === 'unassigned-list') {
                    // Unassign
                    $.ajax({
                        url: 'index.php?mod=penempatan&act=delete&id=' + idSiswa,
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                safeNotify.success('Terhapus', response.message);
                            } else {
                                safeNotify.error('Gagal', response.message);
                                $(ui.sender).sortable('cancel');
                            }
                            updateCounts();
                        },
                        error: function (xhr) {
                            safeNotify.error('Error', 'Gagal menghapus data.');
                            $(ui.sender).sortable('cancel');
                            updateCounts();
                        }
                    });
                }
            },
            stop: function (event, ui) {
                updateCounts();
            }
        }).disableSelection();
    });
</script>