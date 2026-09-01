<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="m-0 font-weight-bold text-dark"><i class="fas fa-folder-open text-primary mr-2"></i>
                    Perangkat KBM</h4>
                <p class="text-muted small mb-0">Upload dan kelola dokumen ATP, Modul Ajar, Prosem, dan Prota.</p>
            </div>

            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalUpload">
                <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Perangkat
            </button>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Bulk Action Bar (Hidden by default) -->
        <div id="bulkActionBar" class="alert alert-info shadow-sm mb-3 d-none animate__animated animate__fadeInDown" 
             style="border-radius: 10px; border-left: 5px solid #0dcaf0; position: sticky; top: 10px; z-index: 1000;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-check-double mr-2"></i>
                    <strong id="selectedCount">0</strong> dokumen terpilih
                </div>
                <div class="d-flex" style="gap: 10px;">
                    <button class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#modalBulkClone">
                        <i class="fas fa-copy mr-1"></i> Salin Terpilih
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <?php if (!empty($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                <?= $_SESSION['pesan_sukses'];
                unset($_SESSION['pesan_sukses']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= $_SESSION['pesan_error'];
                unset($_SESSION['pesan_error']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <select class="form-control" id="filterTA" onchange="applyFilter()">
                            <?php foreach ($all_ta as $ta): ?>
                                <option value="<?= $ta['id_ta'] ?>" <?= ($filter_ta == $ta['id_ta']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ta['nama_ta']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group input-group-sm" style="width: 200px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-book"></i></span>
                        </div>
                        <select class="form-control" id="filterMapel" onchange="applyFilter()">
                            <option value="">Semua Mapel</option>
                            <?php 
                            // Get unique mapel from dokumen_list to help filtering historical data
                            // or use mapel_list if it's broad enough
                            foreach ($mapel_list as $m): ?>
                                <option value="<?= htmlspecialchars($m['nama_mapel']) ?>" <?= ($filter_mapel == $m['nama_mapel']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nama_mapel']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group input-group-sm" style="width: 180px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-filter"></i></span>
                        </div>
                        <select class="form-control" id="filterJenis" onchange="applyFilter()">
                            <option value="">Semua Jenis</option>
                            <option value="ATP" <?= ($filter_jenis == 'ATP') ? 'selected' : '' ?>>ATP</option>
                            <option value="KKTP" <?= ($filter_jenis == 'KKTP') ? 'selected' : '' ?>>KKTP</option>
                            <option value="Modul Ajar" <?= ($filter_jenis == 'Modul Ajar') ? 'selected' : '' ?>>Modul Ajar</option>
                            <option value="Media Ajar" <?= ($filter_jenis == 'Media Ajar') ? 'selected' : '' ?>>Media Ajar</option>
                            <option value="Buku" <?= ($filter_jenis == 'Buku') ? 'selected' : '' ?>>Buku</option>
                            <option value="Prosem" <?= ($filter_jenis == 'Prosem') ? 'selected' : '' ?>>Prosem</option>
                            <option value="Prota" <?= ($filter_jenis == 'Prota') ? 'selected' : '' ?>>Prota</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase">
                            <th class="text-center" width="40">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th class="px-3" width="50">#</th>
                            <th>Judul Dokumen</th>
                            <th>Mapel / Kelas</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">File</th>
                            <th class="text-center" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dokumen_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <img src="assets/img/empty_state.svg" alt="Empty" style="height: 100px; opacity: 0.5;"
                                        class="mb-3 d-block mx-auto">
                                    Belum ada dokumen yang diupload.
                                </td>
                            </tr>
                        <?php else: ?>
                                <?php foreach ($dokumen_list as $i => $d): ?>
                                    <tr class="row-document" data-id="<?= $d['id_perangkat'] ?>">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input check-item" id="check_<?= $d['id_perangkat'] ?>" value="<?= $d['id_perangkat'] ?>">
                                                <label class="custom-control-label" for="check_<?= $d['id_perangkat'] ?>"></label>
                                            </div>
                                        </td>
                                        <td class="text-center px-3">
                                            <?= $i + 1 ?>
                                        </td>
                                    <td>
                                        <div class="font-weight-bold text-dark">
                                            <?= htmlspecialchars($d['judul']) ?>
                                        </div>
                                        <div class="small text-muted">
                                            <?= date('d M Y', strtotime($d['created_at'])) ?>
                                            <?php if ($d['is_reused']): ?>
                                                <span class="badge badge-light text-muted border ml-1" title="Disalin dari semester lain">
                                                    <i class="fas fa-sync-alt mr-1"></i> Reused
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark font-weight-bold">
                                            <?= htmlspecialchars($d['mapel']) ?>
                                        </div>
                                        <div class="badge badge-light border">
                                            <?= htmlspecialchars($d['kelas']) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badge_class = 'badge-secondary';
                                        if ($d['jenis'] == 'ATP')
                                            $badge_class = 'badge-info';
                                        elseif ($d['jenis'] == 'KKTP')
                                            $badge_class = 'badge-teal';
                                        elseif ($d['jenis'] == 'Modul Ajar')
                                            $badge_class = 'badge-primary';
                                        elseif ($d['jenis'] == 'Media Ajar')
                                            $badge_class = 'badge-indigo';
                                        elseif ($d['jenis'] == 'Buku')
                                            $badge_class = 'badge-purple';
                                        elseif ($d['jenis'] == 'Prosem')
                                            $badge_class = 'badge-warning';
                                        elseif ($d['jenis'] == 'Prota')
                                            $badge_class = 'badge-success';
                                        ?>
                                        <style>.badge-teal { background-color: #20c997; color: white; } .badge-indigo { background-color: #6610f2; color: white; } .badge-purple { background-color: #6f42c1; color: white; }</style>
                                        <span class="badge <?= $badge_class ?> px-3 py-2" style="border-radius: 20px;">
                                            <?= $d['jenis'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($d['file_path']) && file_exists($d['file_path'])): ?>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info" onclick="showGlobalPreview('<?= $d['file_path'] ?>', 'iframe', 'Pratinjau <?= htmlspecialchars($d['judul'] ?? $d['jenis']) ?>')">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                                <a href="<?= $d['file_path'] ?>" target="_blank" class="btn btn-light border border-left-0" title="Download">
                                                    <i class="fas fa-download text-primary"></i> <span class="d-none d-md-inline"><?= strtoupper($d['tipe_file'] ?? 'FILE') ?></span>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small">File Hilang</span>
                                        <?php endif; ?>
                                    </td>
                                        <td class="text-center py-3">
                                            <div class="d-flex justify-content-center" style="gap: 5px;">
                                                <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $d['id_perangkat'] ?>"
                                                    data-judul="<?= htmlspecialchars($d['judul']) ?>" data-jenis="<?= $d['jenis'] ?>"
                                                    data-mapel="<?= htmlspecialchars($d['mapel']) ?>"
                                                    data-kelas="<?= htmlspecialchars($d['kelas']) ?>" data-toggle="modal"
                                                    data-target="#modalEdit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-info btn-clone" 
                                                    data-id="<?= $d['id_perangkat'] ?>"
                                                    data-judul="<?= htmlspecialchars($d['judul']) ?>"
                                                    data-toggle="modal" data-target="#modalClone" title="Salin">
                                                    <i class="fas fa-copy"></i>
                                                </button>

                                                <a href="<?= BASE_URL ?>perangkat_upload/delete?id=<?= $d['id_perangkat'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus dokumen ini?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Upload -->
<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>perangkat_upload/upload" method="POST" enctype="multipart/form-data"
            class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-cloud-upload-alt mr-2"></i> Upload Dokumen Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Jenis Perangkat <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="ATP">ATP (Alur Tujuan Pembelajaran)</option>
                        <option value="KKTP">KKTP (Kriteria Ketercapaian Tujuan Pembelajaran)</option>
                        <option value="Modul Ajar">Modul Ajar / RPP</option>
                        <option value="Media Ajar">Media Ajar</option>
                        <option value="Buku">Buku</option>
                        <option value="Prosem">Program Semester</option>
                        <option value="Prota">Program Tahunan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="mapel" class="form-control" required>
                        <option value="">-- Pilih Mapel --</option>
                        <?php foreach ($mapel_list as $m): ?>
                            <option value="<?= htmlspecialchars($m['nama_mapel']) ?>">
                                <?= htmlspecialchars($m['nama_mapel']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kelas <span class="text-danger">*</span></label>
                    <select name="kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Judul Dokumen (Opsional)</label>
                    <input type="text" name="judul" class="form-control"
                        placeholder="Biarkan kosong untuk gunakan nama file">
                </div>

                <div class="form-group">
                    <label>File Dokumen <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file" name="file_perangkat" class="custom-file-input" id="customFile" required>
                        <label class="custom-file-label" for="customFile">Pilih file...</label>
                    </div>
                    <small class="text-muted">Format: PDF, Word, Excel, PPT, Video. Max 50MB.</small>
                </div>

                <?php if (!empty($related_ta)): ?>
                    <div class="custom-control custom-checkbox mt-3 p-3 bg-light border" style="border-radius: 10px;">
                        <input type="checkbox" name="apply_all_semester" class="custom-control-input" id="checkApplyAll" value="1" checked>
                        <label class="custom-control-label font-weight-bold text-primary" for="checkApplyAll">
                            Gunakan di seluruh semester Tahun Pelajaran ini
                        </label>
                        <p class="small text-muted mb-0 mt-1">
                            Centang ini agar dokumen otomatis muncul di Ganjil & Genap (jika tersedia).
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>perangkat_upload/update" method="POST" enctype="multipart/form-data"
            class="modal-content">
            <input type="hidden" name="id_perangkat" id="edit_id">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Edit Dokumen</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Jenis Perangkat</label>
                    <select name="jenis" id="edit_jenis" class="form-control" required>
                        <option value="ATP">ATP (Alur Tujuan Pembelajaran)</option>
                        <option value="KKTP">KKTP (Kriteria Ketercapaian Tujuan Pembelajaran)</option>
                        <option value="Modul Ajar">Modul Ajar / RPP</option>
                        <option value="Media Ajar">Media Ajar</option>
                        <option value="Buku">Buku</option>
                        <option value="Prosem">Program Semester</option>
                        <option value="Prota">Program Tahunan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <select name="mapel" id="edit_mapel" class="form-control" required>
                        <?php foreach ($mapel_list as $m): ?>
                            <option value="<?= htmlspecialchars($m['nama_mapel']) ?>">
                                <?= htmlspecialchars($m['nama_mapel']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kelas</label>
                    <select name="kelas" id="edit_kelas" class="form-control" required>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Judul Dokumen</label>
                    <input type="text" name="judul" id="edit_judul" class="form-control" required>
                </div>

                <div class="alert alert-info small">
                    <i class="fas fa-info-circle"></i> Upload file baru di bawah ini JIKA INGIN MENGGANTI file. Jika
                    tidak, biarkan kosong.
                </div>

                <div class="form-group">
                    <label>Ganti File (Opsional)</label>
                    <div class="custom-file">
                        <input type="file" name="file_perangkat" class="custom-file-input" id="editFile">
                        <label class="custom-file-label" for="editFile">Pilih file baru...</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Clone -->
<div class="modal fade" id="modalClone" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form action="<?= BASE_URL ?>perangkat_upload/clone" method="POST" class="modal-content">
            <input type="hidden" name="id_perangkat" id="clone_id">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title small font-weight-bold"><i class="fas fa-copy mr-2"></i> Salin Perangkat</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="small mb-3">Salin dokumen <strong id="clone_judul"></strong> ke semester lain di Tahun Pelajaran yang sama:</p>
                <div class="form-group">
                    <label class="small font-weight-bold">Pilih Semester Target:</label>
                    <select name="target_id_ta" class="form-control" required>
                        <?php 
                        // Logic for target TA in clone modal
                        $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
                        $shown_ta = [];

                        // 1. Add Active TA if it's not the one we are currently viewing
                        if ($filter_ta != $id_ta_aktif) {
                            $shown_ta[] = $id_ta_aktif;
                            echo '<option value="'.$id_ta_aktif.'" class="font-weight-bold text-primary">Target: '.$_SESSION['nama_ta_aktif'].' (AKTIF)</option>';
                        }

                        // 2. Add related TA (other semesters in SAME YEAR as viewed TA)
                        foreach ($related_ta as $ta) {
                            if (!in_array($ta['id_ta'], $shown_ta)) {
                                $shown_ta[] = $ta['id_ta'];
                                echo '<option value="'.$ta['id_ta'].'">Salin ke: '.htmlspecialchars($ta['nama_ta']).'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light p-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-info">Salin Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Bulk Clone -->
<div class="modal fade" id="modalBulkClone" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form action="<?= BASE_URL ?>perangkat_upload/bulk_clone" method="POST" id="formBulkClone" class="modal-content">
            <!-- Selected IDs will be injected here -->
            <div id="bulkIdsContainer"></div>
            
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title small font-weight-bold"><i class="fas fa-copy mr-2"></i> Salin Massal</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="small mb-3 text-center">Salin <strong class="text-primary" id="bulkSelectedText"></strong> dokumen terpilih ke semester tujuan:</p>
                <div class="form-group">
                    <label class="small font-weight-bold">Pilih Semester Target:</label>
                    <select name="target_id_ta" class="form-control" required>
                        <?php 
                        $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
                        $shown_ta_bulk = [];

                        if ($filter_ta != $id_ta_aktif) {
                            $shown_ta_bulk[] = $id_ta_aktif;
                            echo '<option value="'.$id_ta_aktif.'" class="font-weight-bold text-primary">Target: '.$_SESSION['nama_ta_aktif'].' (AKTIF)</option>';
                        }

                        foreach ($related_ta as $ta) {
                            if (!in_array($ta['id_ta'], $shown_ta_bulk)) {
                                $shown_ta_bulk[] = $ta['id_ta'];
                                echo '<option value="'.$ta['id_ta'].'">Salin ke: '.htmlspecialchars($ta['nama_ta']).'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="alert alert-warning small mb-0">
                    <i class="fas fa-info-circle mr-1"></i> Proses ini akan memakan waktu tergantung jumlah dokumen.
                </div>
            </div>
            <div class="modal-footer bg-light p-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-info">Salin Sekarang</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    function applyFilter() {
        var ta = document.getElementById('filterTA').value;
        var mapel = document.getElementById('filterMapel').value;
        var jenis = document.getElementById('filterJenis').value;
        window.location.href = '<?= BASE_URL ?>perangkat_upload?ta=' + ta + '&mapel=' + encodeURIComponent(mapel) + '&jenis=' + jenis;
    }

    $(document).ready(function () {
        // Custom File Input
        $('.custom-file-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Popup Confirmation for Upload & Edit Forms
        $('#modalUpload form, #modalEdit form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var isEdit = $(form).closest('.modal').attr('id') === 'modalEdit';
            
            Swal.fire({
                title: isEdit ? 'Simpan Perubahan?' : 'Upload Perangkat?',
                text: isEdit ? 'Update dokumen ini akan disimpan.' : 'Dokumen Anda akan diunggah ke server.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fas fa-save mr-1"></i> Ya, Simpan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Berkas...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });

        // Edit Modal Handler
        $('.btn-edit').on('click', function () {
            var id = $(this).data('id');
            var judul = $(this).data('judul');
            var jenis = $(this).data('jenis');
            var mapel = $(this).data('mapel');
            var kelas = $(this).data('kelas');

            $('#edit_id').val(id);
            $('#edit_judul').val(judul);
            $('#edit_jenis').val(jenis);
            $('#edit_mapel').val(mapel);
            $('#edit_kelas').val(kelas);

            // Reset file input
            $('#editFile').val('');
            $('#editFile').next('.custom-file-label').html('Pilih file baru...');
        });

        // Clone Modal Handler
        $('.btn-clone').on('click', function () {
            var id = $(this).data('id');
            var judul = $(this).data('judul');
            $('#clone_id').val(id);
            $('#clone_judul').text(judul);
        });

        // --- Bulk Action Logic ---
        function updateBulkUI() {
            var checkedCount = $('.check-item:checked').length;
            if (checkedCount > 0) {
                $('#selectedCount').text(checkedCount);
                $('#bulkActionBar').removeClass('d-none');
            } else {
                $('#bulkActionBar').addClass('d-none');
            }
        }

        $('#selectAll').on('change', function() {
            $('.check-item').prop('checked', $(this).is(':checked'));
            updateBulkUI();
        });

        $('.check-item').on('change', function() {
            updateBulkUI();
            if ($('.check-item:checked').length === $('.check-item').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        window.clearSelection = function() {
            $('.check-item, #selectAll').prop('checked', false);
            updateBulkUI();
        };

        $('#modalBulkClone').on('show.bs.modal', function () {
            var selectedIds = [];
            $('.check-item:checked').each(function() {
                selectedIds.push($(this).val());
            });
            $('#bulkSelectedText').text(selectedIds.length);
            var container = $('#bulkIdsContainer');
            container.empty();
            selectedIds.forEach(function(id) {
                container.append('<input type="hidden" name="ids[]" value="' + id + '">');
            });
        });

        $('#formBulkClone').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Salin Massal?',
                text: 'Dokumen terpilih akan disalin ke semester target.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                confirmButtonText: 'Ya, Salin!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyalin Dokumen...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        });
    });
</script>