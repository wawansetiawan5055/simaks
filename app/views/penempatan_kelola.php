<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    /* Kontainer utama untuk 2 kolom */
    .drag-container {
        display: flex;
        gap: 20px;
    }

    /* Kolom (Daftar Siswa) */
    .drag-column {
        flex: 1;
        min-width: 300px;
    }

    /* Daftar UL */
    .student-list {
        background: #fdfdfd;
        border: 1px solid #ddd;
        border-radius: 5px;
        min-height: 400px;
        padding: 10px;
        list-style-type: none;
    }

    /* Item Siswa (LI) */
    .student-item {
        background: #fff;
        border: 1px solid #eee;
        padding: 8px 12px;
        margin-bottom: 5px;
        border-radius: 4px;
        cursor: move;
        /* Kursor pindah */
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .student-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }

    .student-item.dragging {
        opacity: 0.8;
        transform: rotate(5deg);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .student-item .student-name {
        font-weight: 600;
    }

    .student-item .student-nisn {
        font-size: 0.85rem;
        color: #777;
    }

    /* Placeholder saat menyeret */
    .ui-sortable-placeholder {
        height: 50px;
        background-color: #e3f2fd !important;
        border: 2px dashed #2196f3 !important;
        visibility: visible !important;
        border-radius: 4px;
        margin-bottom: 5px;
    }

    /* Tampilan item saat diseret */
    .ui-sortable-helper {
        background-color: #fff !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transform: rotate(2deg);
        z-index: 1000;
    }

    /* Grip handle */
    .student-item .fa-grip-vertical {
        color: #ccc;
        font-size: 0.9rem;
    }

    .student-item:hover .fa-grip-vertical {
        color: #666;
    }

    /* Status badges */
    .student-item .badge {
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="fw-bold">Kelola Anggota Rombel</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= BASE_URL ?>penempatan" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali
                    ke Daftar Kelas</a>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?php // Session messages now handled by toast notifications in footer.php ?>

        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Pilih Kelas Tujuan</h3>
            </div>
            <form method="GET">
                <input type="hidden" name="mod" value="penempatan">
                <input type="hidden" name="act" value="kelola"> <!-- ACTION KELOLA -->
                <div class="card-body">
                    <label>Pilih Kelas untuk Mengisi Rombel</label>
                    <select name="id_kelas" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : '' ?>
                                >
                                <?= htmlspecialchars($k['nama_kelas']) ?> (Wali:
                                <?= htmlspecialchars($k['nama_walas'] ?? 'Belum ada') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if ($id_kelas_filter): ?>
            <div class="drag-container">

                <div class="drag-column">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users"></i> Anggota Rombel:
                                <strong>
                                    <?= htmlspecialchars($info_kelas['nama_kelas']) ?>
                                </strong>
                            </h3>
                            <span class="badge badge-light float-right" id="count-assigned">
                                <?php 
                                    $active_count = 0;
                                    foreach($assigned_students as $st) if(($st['status_aktif'] ?? 'Aktif') == 'Aktif') $active_count++;
                                    $total_count = count($assigned_students);
                                    echo ($active_count == $total_count) ? "$total_count Siswa" : "$active_count Aktif ($total_count Total)";
                                ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted small"></i></span>
                                </div>
                                <input type="text" id="search-assigned" class="form-control form-control-sm border-left-0" placeholder="Cari nama anggota...">
                            </div>
                            <ul id="assigned-list" class="student-list connected-lists"
                                data-id-kelas="<?= $id_kelas_filter ?>">
                                <?php if (empty($assigned_students)): ?>
                                    <li class="text-muted text-center no-drag">Belum ada siswa di kelas ini.</li>
                                <?php endif; ?>
                                <?php foreach ($assigned_students as $s): ?>
                                    <li class="student-item" data-id-siswa="<?= $s['id_siswa'] ?>" data-status="<?= $s['status_aktif'] ?? 'Aktif' ?>">
                                        <div>
                                            <span class="student-name">
                                                <?= htmlspecialchars($s['nama']) ?>
                                                <?php if(($s['status_aktif'] ?? 'Aktif') != 'Aktif'): ?>
                                                    <span class="badge badge-danger ml-1" style="font-size: 0.7rem;"><?= $s['status_aktif'] ?></span>
                                                <?php endif; ?>
                                            </span>
                                            <span class="student-nisn d-block">NISN:
                                                <?= htmlspecialchars($s['nisn']) ?> | <?= ($s['jk'] ?? '') == 'Laki-laki' ? 'L' : 'P' ?>
                                            </span>
                                        </div>
                                        <i class="fas fa-grip-vertical text-muted"></i>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="drag-column">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-plus"></i>
                                <?php if ($id_ta_sumber && $id_kelas_sumber): ?>
                                    Siswa dari Sumber
                                <?php else: ?>
                                    Siswa Belum Ditempatkan
                                <?php endif; ?>
                            </h3>
                            <span class="badge badge-light float-right" id="count-unassigned">
                                <?php 
                                    $display_list = ($id_ta_sumber && $id_kelas_sumber) ? $source_students : $unassigned_students;
                                    $active_count_u = 0;
                                    foreach($display_list as $st) if(($st['status_aktif'] ?? 'Aktif') == 'Aktif') $active_count_u++;
                                    $total_count_u = count($display_list);
                                    echo ($active_count_u == $total_count_u) ? "$total_count_u Siswa" : "$active_count_u Aktif ($total_count_u Total)";
                                ?>
                            </span>
                        </div>
                        <div class="card-body">

                            <!-- FILTER SUMBER (COPY ROMBEL) -->
                            <div class="mb-3 p-2 bg-light border rounded">
                                <form method="GET" id="form-source-filter">
                                    <input type="hidden" name="mod" value="penempatan">
                                    <input type="hidden" name="act" value="kelola"> <!-- ACTION KELOLA -->
                                    <input type="hidden" name="id_kelas" value="<?= $id_kelas_filter ?>">

                                    <h6 class="text-muted mb-2"><i class="fas fa-filter"></i> Ambil Siswa Dari:</h6>
                                    <div class="row">
                                        <div class="col-md-5 mb-2">
                                            <select name="source_ta" class="form-control form-control-sm"
                                                onchange="this.form.submit()">
                                                <option value="">-- Pilih TA Sumber --</option>
                                                <?php foreach ($ta_list as $t): ?>
                                                    <option value="<?= $t['id_ta'] ?>" <?= ($id_ta_sumber == $t['id_ta']) ? 'selected' : '' ?>>
                                                        <?= $t['nama_ta'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5 mb-2">
                                            <select name="source_kelas" class="form-control form-control-sm"
                                                onchange="this.form.submit()">
                                                <option value="">-- Pilih Kelas Sumber --</option>
                                                <?php foreach ($source_kelas_list as $sk): ?>
                                                    <option value="<?= $sk['id_kelas'] ?>"
                                                        <?= ($id_kelas_sumber == $sk['id_kelas']) ? 'selected' : '' ?>>
                                                        <?= $sk['nama_kelas'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <?php if ($id_ta_sumber || $id_kelas_sumber): ?>
                                                <a href="<?= BASE_URL ?>penempatan/kelola?id_kelas=<?= $id_kelas_filter ?>"
                                                    class="btn btn-sm btn-outline-danger btn-block" title="Reset Filter">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>

                                <?php if ($id_ta_sumber && $id_kelas_sumber && !empty($source_students)): ?>
                                    <button type="button" id="btn-salin-semua" 
                                        class="btn btn-sm btn-primary btn-block mt-1"
                                        data-target-kelas="<?= $id_kelas_filter ?>"
                                        data-source-ta="<?= $id_ta_sumber ?>"
                                        data-source-kelas="<?= $id_kelas_sumber ?>"
                                        data-jumlah="<?= count($source_students) ?>">
                                        <i class="fas fa-copy"></i> Salin Semua Siswa (<?= count($source_students) ?>)
                                    </button>
                                <?php endif; ?>


                            </div>
                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted small"></i></span>
                                </div>
                                <input type="text" id="search-unassigned" class="form-control form-control-sm border-left-0" placeholder="Cari nama siswa...">
                            </div>

                            <ul id="unassigned-list" class="student-list connected-lists">
                                <?php
                                // Tentukan list mana yang dipakai
                                $display_list = ($id_ta_sumber && $id_kelas_sumber) ? $source_students : $unassigned_students;
                                ?>

                                <?php if (empty($display_list)): ?>
                                    <li class="text-muted text-center no-drag">Tidak ada siswa ditemukan.</li>
                                <?php endif; ?>

                                <?php foreach ($display_list as $s): ?>
                                    <li class="student-item" data-id-siswa="<?= $s['id_siswa'] ?>" data-status="<?= $s['status_aktif'] ?? 'Aktif' ?>">
                                        <div>
                                            <span class="student-name">
                                                <?= htmlspecialchars($s['nama']) ?>
                                                <?php if(($s['status_aktif'] ?? 'Aktif') != 'Aktif'): ?>
                                                    <span class="badge badge-danger ml-1" style="font-size: 0.7rem;"><?= $s['status_aktif'] ?></span>
                                                <?php endif; ?>
                                            </span>
                                            <span class="student-nisn d-block">NISN:
                                                <?= htmlspecialchars($s['nisn']) ?> | <?= ($s['jk'] ?? '') == 'Laki-laki' ? 'L' : 'P' ?>
                                            </span>
                                        </div>
                                        <i class="fas fa-grip-vertical text-muted"></i>
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
        // Check if jQuery UI is loaded
        if (typeof $.ui === 'undefined' || typeof $.ui.sortable === 'undefined') {
            console.error('jQuery UI Sortable not loaded');
            if (window.Notify) {
                Notify.error('System Error', 'jQuery UI Sortable tidak dimuat. Fitur drag & drop tidak akan berfungsi.');
            } else {
                alert('System Error: jQuery UI Sortable tidak dimuat. Fitur drag & drop tidak akan berfungsi.');
            }
            return;
        }

        // Pastikan Notify tersedia, jika tidak fallback ke alert
        const safeNotify = {
            success: (title, msg) => {
                if (window.Notify) window.Notify.success(title, msg);
                else alert(title + ': ' + msg);
            },
            error: (title, msg) => {
                if (window.Notify) window.Notify.error(title, msg);
                else alert(title + ': ' + msg);
            }
        };

        const idTa = <?= $id_ta_tampil ?>;
        const idKelas = <?= $id_kelas_filter ?? 0 ?>;

        // Initialize counts on page load
        function updateCounts() {
            try {
                const assignedItems = $('#assigned-list li.student-item');
                const assignedActive = assignedItems.filter(function() { return $(this).data('status') === 'Aktif'; }).length;
                const assignedTotal = assignedItems.length;
                const assignedText = (assignedActive === assignedTotal) ? assignedTotal + ' Siswa' : assignedActive + ' Aktif (' + assignedTotal + ' Total)';
                $('#count-assigned').text(assignedText);

                const unassignedItems = $('#unassigned-list li.student-item');
                const unassignedActive = unassignedItems.filter(function() { return $(this).data('status') === 'Aktif'; }).length;
                const unassignedTotal = unassignedItems.length;
                const unassignedText = (unassignedActive === unassignedTotal) ? unassignedTotal + ' Siswa' : unassignedActive + ' Aktif (' + unassignedTotal + ' Total)';
                $('#count-unassigned').text(unassignedText);
            } catch (e) {
                console.error('Error updating counts:', e);
            }
        }

        // Call updateCounts initially
        updateCounts();

        // --- SEARCH FUNCTIONALITY ---
        function liveSearch(inputId, listId) {
            $(inputId).on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(listId + " li.student-item").filter(function() {
                    $(this).toggle($(this).find('.student-name').text().toLowerCase().indexOf(value) > -1)
                });
            });
        }

        liveSearch("#search-assigned", "#assigned-list");
        liveSearch("#search-unassigned", "#unassigned-list");

        // Initialize sortable with error handling
        try {
            $("#assigned-list, #unassigned-list").sortable({
                connectWith: ".connected-lists",
                placeholder: "ui-sortable-placeholder",
                items: "li.student-item",
                cursor: "move",
                opacity: 0.8,
                revert: true,
                tolerance: "pointer",

                /**
                 * Event 'receive' terjadi ketika item DIPINDAHKAN DARI list LAIN
                 */
                receive: function (event, ui) {
                    try {
                        const idSiswa = ui.item.data('id-siswa');
                        const targetList = $(this).attr('id');

                        if (!idSiswa) {
                            console.error('No student ID found');
                            $(ui.sender).sortable('cancel');
                            return;
                        }

                        if (targetList === 'assigned-list') {
                            // --- PROSES ASSIGN (MASUK KE KELAS) ---
                            $.ajax({
                                url: '<?= BASE_URL ?>penempatan/save',
                                type: 'POST',
                                data: {
                                    id_siswa: idSiswa,
                                    id_kelas: idKelas,
                                    id_ta: idTa
                                },
                                dataType: 'json',
                                timeout: 10000,
                                success: function (response) {
                                    if (response.status === 'success') {
                                        safeNotify.success('Berhasil', response.message || 'Siswa berhasil ditempatkan.');
                                        updateCounts();
                                    } else {
                                        safeNotify.error('Gagal', response.message || 'Gagal menempatkan siswa.');
                                        $(ui.sender).sortable('cancel');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('AJAX Error:', xhr, status, error);
                                    let msg = 'Error koneksi saat menyimpan.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        msg = xhr.responseJSON.message;
                                    } else if (status === 'timeout') {
                                        msg = 'Request timeout. Coba lagi.';
                                    }
                                    safeNotify.error('Gagal', msg);
                                    $(ui.sender).sortable('cancel');
                                }
                            });

                        } else if (targetList === 'unassigned-list') {
                            // --- PROSES UNASSIGN (DIKELUARKAN DARI KELAS) ---
                            $.ajax({
                                url: '<?= BASE_URL ?>penempatan/delete?id=' + idSiswa + '&id_ta=' + idTa,
                                type: 'GET',
                                dataType: 'json',
                                timeout: 10000,
                                success: function (response) {
                                    if (response.status === 'success') {
                                        safeNotify.success('Berhasil', response.message || 'Siswa berhasil dikeluarkan dari kelas.');
                                        updateCounts();
                                    } else {
                                        safeNotify.error('Gagal', response.message || 'Gagal mengeluarkan siswa dari kelas.');
                                        $(ui.sender).sortable('cancel');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('AJAX Error:', xhr, status, error);
                                    let msg = 'Error koneksi saat menghapus.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        msg = xhr.responseJSON.message;
                                    } else if (status === 'timeout') {
                                        msg = 'Request timeout. Coba lagi.';
                                    }
                                    safeNotify.error('Gagal', msg);
                                    $(ui.sender).sortable('cancel');
                                }
                            });
                        }
                    } catch (e) {
                        console.error('Error in receive event:', e);
                        $(ui.sender).sortable('cancel');
                        safeNotify.error('Error', 'Terjadi kesalahan sistem.');
                    }
                },

                /**
                 * Event 'start' - saat mulai drag
                 */
                start: function (event, ui) {
                    ui.item.addClass('dragging');
                },

                /**
                 * Event 'stop' - saat selesai drag
                 */
                stop: function (event, ui) {
                    ui.item.removeClass('dragging');
                    updateCounts();
                },

                /**
                 * Event 'update' - saat urutan berubah dalam list yang sama
                 */
                update: function (event, ui) {
                    // Jika ini adalah move dalam list yang sama, tidak perlu AJAX
                    if (!ui.sender) {
                        // Item hanya di-reorder dalam list yang sama, tidak ada perubahan data
                        return;
                    }
                }
            }).disableSelection();

            console.log('Drag & drop initialized successfully');

        } catch (e) {
            console.error('Error initializing sortable:', e);
            safeNotify.error('System Error', 'Gagal menginisialisasi fitur drag & drop.');
        }
    });

    // --- SALIN SEMUA SISWA (AJAX + SweetAlert2) ---
    $(document).on('click', '#btn-salin-semua', function () {
        var btn = $(this);
        var targetKelas = btn.data('target-kelas');
        var sourceTa = btn.data('source-ta');
        var sourceKelas = btn.data('source-kelas');
        var jumlah = btn.data('jumlah');

        Swal.fire({
            title: 'Salin Semua Siswa?',
            html: 'Proses ini akan menyalin <strong>' + jumlah + ' siswa</strong> dari kelas sumber ke rombel ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Salin Sekarang',
            cancelButtonText: 'Batal',
        }).then(function(result) {
            if (!result.isConfirmed) return;
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyalin...');
            $.ajax({
                url: '<?= BASE_URL ?>penempatan/copy_rombel',
                method: 'POST',
                dataType: 'json',
                data: { target_kelas: targetKelas, source_ta: sourceTa, source_kelas: sourceKelas, ajax: 1 },
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({ title: 'Berhasil!', text: response.message, icon: 'success', confirmButtonText: 'OK' })
                            .then(function() { location.reload(); });
                    } else {
                        var icon = (response.status === 'warning') ? 'warning' : 'error';
                        Swal.fire('Perhatian', response.message || 'Terjadi kesalahan.', icon);
                        btn.prop('disabled', false).html('<i class="fas fa-copy"></i> Salin Semua Siswa (' + jumlah + ')');
                    }
                },
                error: function (xhr) {
                    var msg = 'Error koneksi saat menyalin.';
                    try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
                    Swal.fire('Gagal', msg, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-copy"></i> Salin Semua Siswa (' + jumlah + ')');
                }
            });
        });
    });
</script>