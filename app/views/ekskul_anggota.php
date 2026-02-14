<?php include __DIR__ . '/partials/header.php'; ?>
<style>
    .student-list {
        min-height: 400px;
        max-height: 600px;
        overflow-y: auto;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 5px;
        padding: 10px;
    }

    .student-item {
        cursor: grab;
        margin-bottom: 5px;
        padding: 8px 12px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9em;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.1s;
    }

    .student-item:hover {
        background: #e9ecef;
        transform: scale(1.01);
    }

    .student-item.ui-sortable-helper {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        background: #e3f2fd;
        border-color: #2196f3;
    }

    .kiri-style {
        background-color: #fff3e0 !important;
        border-color: #ffe0b2 !important;
    }

    .kanan-style {
        background-color: #e8f5e9 !important;
        border-color: #c8e6c9 !important;
    }

    .search-box {
        margin-bottom: 10px;
    }

    .count-badge {
        font-size: 0.8em;
        padding: 3px 8px;
        border-radius: 10px;
        background: #6c757d;
        color: white;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Anggota Ekskul: <strong><?= htmlspecialchars($ekskul['nama_ekskul']) ?></strong></h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="index.php?mod=ekskul" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i>
                    Kembali</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- KOLOM KIRI: LIST SISWA (CALON) -->
            <div class="col-md-6">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-graduate"></i> Daftar Siswa (Tersedia)</h3>
                        <div class="card-tools">
                            <span id="count-left" class="count-badge">0</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <select id="filter-kelas" class="form-control">
                                    <option value="">Semua Kelas</option>
                                    <?php foreach ($kelas_list as $k): ?>
                                        <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" id="search-left" class="form-control"
                                    placeholder="Cari Nama...">
                            </div>
                        </div>

                        <div id="list-left" class="student-list kiri-style connectedSortable">
                            <!-- Content loaded via AJAX -->
                            <?php foreach ($available_students as $s): ?>
                                <div class="student-item" data-id="<?= $s['id_siswa'] ?>">
                                    <div>
                                        <strong><?= $s['nama_siswa'] ?></strong><br>
                                        <small class="text-muted"><?= $s['nama_kelas'] ?? 'Tanpa Kelas' ?></small>
                                    </div>
                                    <i class="fas fa-arrows-alt text-muted"></i>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: ANGGOTA EKSKUL -->
            <div class="col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users"></i> Anggota Terdaftar</h3>
                        <div class="card-tools">
                            <span id="count-right" class="count-badge">0</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="search-box">
                            <input type="text" id="search-right" class="form-control" placeholder="Cari Anggota...">
                        </div>

                        <div id="list-right" class="student-list kanan-style connectedSortable">
                            <?php foreach ($anggota_list as $a): ?>
                                <div class="student-item" data-id="<?= $a['id_siswa'] ?>">
                                    <div>
                                        <strong><?= $a['nama_siswa'] ?></strong><br>
                                        <small class="text-muted"><?= $a['nama_kelas'] ?? 'Tanpa Kelas' ?></small>
                                    </div>
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($anggota_list)): ?>
                                <div class="text-center text-muted mt-5 empty-msg">Tarik siswa ke sini untuk menambahkan
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<!-- Masukkan jQuery UI jika belum ada -->
<?php if (!isset($has_jquery_ui)): ?>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<?php endif; ?>

<script>
    $(document).ready(function () {
        const id_ekskul = <?= $ekskul['id_ekskul'] ?>;

        // Update Counts
        function updateCounts() {
            $('#count-left').text($('#list-left .student-item').length);
            $('#count-right').text($('#list-right .student-item').length);

            if ($('#list-right .student-item').length > 0) {
                $('.empty-msg').hide();
            } else {
                $('.empty-msg').show();
            }
        }
        updateCounts();

        // Initialize Sortable
        $("#list-left, #list-right").sortable({
            connectWith: ".connectedSortable",
            placeholder: "ui-sortable-placeholder student-item",
            receive: function (event, ui) {
                const targetList = $(this).attr('id'); // list-left or list-right
                const studentId = ui.item.data('id');
                const studentIds = [studentId];

                let action = (targetList === 'list-right') ? 'add' : 'remove';

                // Show loading state on item
                ui.item.css('opacity', '0.5');

                $.ajax({
                    url: 'index.php?mod=ekskul&act=update_anggota',
                    type: 'POST',
                    data: {
                        action: action,
                        id_ekskul: id_ekskul,
                        student_ids: studentIds
                    },
                    dataType: 'json',
                    success: function (res) {
                        ui.item.css('opacity', '1');
                        if (res.status === 'success') {
                            // Success toast
                            if (window.Notify) window.Notify.success('Berhasil', res.message);

                            // Update Icon
                            if (action === 'add') {
                                ui.item.find('.fa-arrows-alt').attr('class', 'fas fa-check text-success');
                            } else {
                                ui.item.find('.fa-check').attr('class', 'fas fa-arrows-alt text-muted');
                            }
                            updateCounts();
                        } else {
                            // Revert if failed
                            $(ui.sender).sortable('cancel');
                            if (window.Notify) window.Notify.error('Gagal', res.message);
                            else alert(res.message);
                        }
                    },
                    error: function () {
                        ui.item.css('opacity', '1');
                        $(ui.sender).sortable('cancel');
                        if (window.Notify) window.Notify.error('Error', 'Terjadi kesalahan server.');
                        else alert('Server Error');
                    }
                });
            }
        }).disableSelection();

        // Function to load students
        function loadStudents() {
            var keyword = $('#search-left').val();
            var id_kelas = $('#filter-kelas').val();

            $.getJSON('index.php?mod=ekskul&act=search_students', { 
                id_ekskul: id_ekskul, 
                q: keyword,
                id_kelas: id_kelas // Send class filter
            }, function (res) {
                if (res.status === 'success') {
                    $('#list-left').empty();
                    res.data.forEach(function (s) {
                        $('#list-left').append(`
                        <div class="student-item" data-id="${s.id_siswa}">
                            <div>
                                <strong>${s.nama_siswa}</strong><br>
                                <small class="text-muted">${s.nama_kelas || 'Tanpa Kelas'}</small>
                            </div>
                            <i class="fas fa-arrows-alt text-muted"></i>
                        </div>
                        `);
                    });
                    updateCounts();
                }
            });
        }

        // Trigger search on typing (debounce) or select change
        var timeout = null;
        $('#search-left').on('keyup', function () {
            clearTimeout(timeout);
            timeout = setTimeout(loadStudents, 300);
        });

        $('#filter-kelas').on('change', function () {
            loadStudents();
        });

        $('#search-right').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $("#list-right .student-item").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>