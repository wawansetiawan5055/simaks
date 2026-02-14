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
        height: 40px;
        background-color: #f0f8ff !important;
        border: 1px dashed #007bff;
        visibility: visible !important;
    }

    /* Tampilan item saat diseret */
    .ui-sortable-helper {
        background-color: #fff !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="fw-bold">Kelola Anggota Rombel</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="index.php?mod=penempatan" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali
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
                                                <a href="index.php?mod=penempatan&act=kelola&id_kelas=<?= $id_kelas_filter ?>"
                                                    class="btn btn-sm btn-outline-danger btn-block" title="Reset Filter">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>

                                <?php if ($id_ta_sumber && $id_kelas_sumber && !empty($source_students)): ?>
                                    <form method="POST" action="index.php?mod=penempatan&act=copy_rombel"
                                        onsubmit="return confirm('Salin semua siswa ini ke kelas tujuan?');">
                                        <input type="hidden" name="target_kelas" value="<?= $id_kelas_filter ?>">
                                        <input type="hidden" name="source_ta" value="<?= $id_ta_sumber ?>">
                                        <input type="hidden" name="source_kelas" value="<?= $id_kelas_sumber ?>">
                                        <button type="submit" class="btn btn-sm btn-primary btn-block mt-1">
                                            <i class="fas fa-copy"></i> Salin Semua Siswa (
                                            <?= count($source_students) ?>)
                                        </button>
                                    </form>
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
        if (typeof $.ui === 'undefined') {
            Notify.error('System Error', 'jQuery UI tidak dimuat.');
            return;
        }

        // Pastikan Notify tersedia, jika tidak fallback ke alert
        const safeNotify = {
            success: (title, msg) => {
                if (window.Notify) window.Notify.success(title, msg);
                else alert(title + ': ' + msg); // Fallback
            },
            error: (title, msg) => {
                if (window.Notify) window.Notify.error(title, msg);
                else alert(title + ': ' + msg); // Fallback
            }
        };

        const idTa = <?= $id_ta_tampil ?>;
        const idKelas = <?= $id_kelas_filter ?? 0 ?>;

        function updateCounts() {
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
        }

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

        $("#assigned-list, #unassigned-list").sortable({
            connectWith: ".connected-lists", // Menghubungkan kedua list
            placeholder: "ui-sortable-placeholder",
            items: "li.student-item", // Hanya item siswa yang bisa diseret

            /**
             * Event 'receive' terjadi ketika item DIPINDAHKAN DARI list LAIN
             */
            receive: function (event, ui) {
                const idSiswa = ui.item.data('id-siswa');

                // Tentukan apakah ini 'assign' (masuk ke assigned-list)
                // atau 'unassign' (masuk ke unassigned-list)

                if ($(this).attr('id') === 'assigned-list') {
                    // --- PROSES ASSIGN (MASUK KE KELAS) ---
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
                                safeNotify.success('Berhasil', response.message);
                            } else {
                                safeNotify.error('Gagal', response.message);
                                $(ui.sender).sortable('cancel');
                            }
                            updateCounts();
                        },
                        error: function (xhr) {
                            let msg = 'Error koneksi saat menyimpan.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            safeNotify.error('Gagal', msg);
                            $(ui.sender).sortable('cancel');
                            updateCounts();
                        }
                    });

                } else if ($(this).attr('id') === 'unassigned-list') {
                    // --- PROSES UNASSIGN (DIKELUARKAN DARI KELAS) ---
                    $.ajax({
                        url: 'index.php?mod=penempatan&act=delete&id=' + idSiswa, // Menggunakan 'delete'
                        type: 'GET', // Sesuai router (act=delete&id=)
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                safeNotify.success('Berhasil', response.message);
                            } else {
                                safeNotify.error('Gagal', response.message);
                                $(ui.sender).sortable('cancel');
                            }
                            updateCounts();
                        },
                        error: function (xhr) {
                            let msg = 'Error koneksi saat menghapus.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            safeNotify.error('Gagal', msg);
                            $(ui.sender).sortable('cancel');
                            updateCounts();
                        }
                    });
                }
            },
            /**
             * Event 'stop' terjadi setelah drop selesai
             */
            stop: function (event, ui) {
                updateCounts();
            }
        }).disableSelection();
    });
</script>