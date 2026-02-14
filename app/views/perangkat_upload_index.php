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
                <div class="d-flex align-items-center">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <select class="form-control" id="filterJenis" onchange="applyFilter()">
                            <option value="">Semua Jenis</option>
                            <option value="ATP" <?= ($filter_jenis == 'ATP') ? 'selected' : '' ?>>ATP</option>
                            <option value="Modul Ajar" <?= ($filter_jenis == 'Modul Ajar') ? 'selected' : '' ?>>Modul Ajar
                            </option>
                            <option value="Prosem" <?= ($filter_jenis == 'Prosem') ? 'selected' : '' ?>>Prosem</option>
                            <option value="Prota" <?= ($filter_jenis == 'Prota') ? 'selected' : '' ?>>Prota</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted">
                            <th class="px-3" width="50">#</th>
                            <th>Judul Dokumen</th>
                            <th>Mapel / Kelas</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">File</th>
                            <th class="text-center" width="120">Aksi</th>
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
                                <tr>
                                    <td class="text-center px-3">
                                        <?= $i + 1 ?>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark">
                                            <?= htmlspecialchars($d['judul']) ?>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            <?= date('d M Y', strtotime($d['created_at'])) ?>
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
                                        elseif ($d['jenis'] == 'Modul Ajar')
                                            $badge_class = 'badge-primary';
                                        elseif ($d['jenis'] == 'Prosem')
                                            $badge_class = 'badge-warning';
                                        elseif ($d['jenis'] == 'Prota')
                                            $badge_class = 'badge-success';
                                        ?>
                                        <span class="badge <?= $badge_class ?> px-3 py-2" style="border-radius: 20px;">
                                            <?= $d['jenis'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($d['file_path']) && file_exists($d['file_path'])): ?>
                                            <a href="<?= $d['file_path'] ?>" target="_blank" class="btn btn-sm btn-light border"
                                                title="Download">
                                                <i class="fas fa-download text-primary"></i>
                                                <span class="d-none d-md-inline ml-1">
                                                    <?= strtoupper($d['tipe_file'] ?? 'FILE') ?>
                                                </span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">File Hilang</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning mb-1 btn-edit" data-id="<?= $d['id_perangkat'] ?>"
                                            data-judul="<?= htmlspecialchars($d['judul']) ?>" data-jenis="<?= $d['jenis'] ?>"
                                            data-mapel="<?= htmlspecialchars($d['mapel']) ?>"
                                            data-kelas="<?= htmlspecialchars($d['kelas']) ?>" data-toggle="modal"
                                            data-target="#modalEdit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="index.php?mod=perangkat_upload&act=delete&id=<?= $d['id_perangkat'] ?>"
                                            class="btn btn-sm btn-danger mb-1"
                                            onclick="return confirm('Yakin ingin menghapus dokumen ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
        <form action="index.php?mod=perangkat_upload&act=upload" method="POST" enctype="multipart/form-data"
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
                        <option value="Modul Ajar">Modul Ajar / RPP</option>
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
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
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
                    <small class="text-muted">Format: PDF, Word, Excel. Max 5MB.</small>
                </div>
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
        <form action="index.php?mod=perangkat_upload&act=update" method="POST" enctype="multipart/form-data"
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
                        <option value="Modul Ajar">Modul Ajar / RPP</option>
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
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
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

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    function applyFilter() {
        var jenis = document.getElementById('filterJenis').value;
        window.location.href = 'index.php?mod=perangkat_upload&jenis=' + jenis;
    }

    $(document).ready(function () {
        // Custom File Input
        $('.custom-file-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
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
    });
</script>