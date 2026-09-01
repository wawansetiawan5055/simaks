<?php
/**
 * View: Input Soal
 */
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-edit text-primary mr-2"></i>Kelola Butir Soal</h1>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-success shadow-sm mr-2" data-toggle="modal" data-target="#modalImport">
                    <i class="fas fa-file-excel mr-1"></i> Import Excel
                </button>
                <button class="btn btn-info shadow-sm mr-2" data-toggle="modal" data-target="#modalZip">
                    <i class="fas fa-file-archive mr-1"></i> ZIP Media
                </button>
                <a href="index.php?mod=bank_soal" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Bank Soal
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal ZIP Upload Media -->
<div class="modal fade" id="modalZip" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="index.php?mod=input_soal&act=upload_media&id_bank=<?= $bank['id_bank'] ?>"
            enctype="multipart/form-data">
            <input type="hidden" name="id_bank" value="<?= $bank['id_bank'] ?>">
            <div class="modal-content">
                <div class="modal-header bg-info text-white py-3">
                    <h5 class="modal-title"><i class="fas fa-file-archive mr-2"></i>Upload ZIP Berisi File Media</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Masukkan semua file gambar (JPG, PNG), audio (MP3), dan video (MP4) ke dalam satu file ZIP
                        lalu upload di sini. Nama file harus sama persis dengan yang terisi di kolom FILEJAWAB / GAMBAR
                        / AUDIO / VIDEO di Excel.
                    </p>
                    <div class="input-group">
                        <div class="custom-file border">
                            <input type="file" name="file_zip" class="custom-file-input" id="zipFileInput" accept=".zip"
                                required>
                            <label class="custom-file-label" for="zipFileInput">Pilih file .zip...</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-secondary px-4 shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info px-4 shadow-sm font-weight-bold">
                        <i class="fas fa-upload mr-1"></i> Upload Media
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>




<section class="content">
    <div class="container-fluid">
        <!-- Info Bank Soal -->
        <div class="card card-outline card-primary shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1 font-weight-bold">
                            <?= htmlspecialchars($bank['nama_bank']) ?>
                        </h5>
                        <p class="mb-0 text-muted">
                            <span class="mr-3"><i class="fas fa-book mr-1"></i> Mapel: <strong>
                                    <?= htmlspecialchars($bank['nama_mapel']) ?>
                                </strong></span>
                            <span><i class="fas fa-list-ol mr-1"></i> Total: <strong>
                                    <?= count($soal_list) ?> Soal
                                </strong></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Input -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 10;">
                    <form action="index.php?mod=input_soal&act=save_soal&id_bank=<?= $bank['id_bank'] ?>" method="POST"
                        id="formSoal">
                        <input type="hidden" name="id_bank" value="<?= $bank['id_bank'] ?>">
                        <input type="hidden" name="id_soal" id="id_soal" value="0">

                        <div class="card-header bg-white py-3 border-bottom">
                            <h3 class="card-title font-weight-bold" id="formTitle"><i
                                    class="fas fa-plus-circle mr-2 text-success"></i>Tambah Soal Baru</h3>
                        </div>
                        <div class="card-body py-2 px-3 bg-light border-bottom">
                            <div class="row small">
                                <div class="col-md-3"><b>Kode:</b> <?= htmlspecialchars($bank['kode_bank'] ?: '-') ?>
                                </div>
                                <div class="col-md-3"><b>Tingkat:</b>
                                    <?= htmlspecialchars($bank['tingkat'] ?: 'Semua') ?></div>
                                <div class="col-md-3"><b>Jurusan:</b>
                                    <?= htmlspecialchars($bank['id_jurusan'] ?: 'Semua') ?></div>
                                <div class="col-md-3 text-right text-primary font-weight-bold"><?= $bank['opsi_pg'] ?>
                                    Opsi PG</div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-group mb-3">
                                <select name="tipe_soal" id="tipe_soal" class="form-control" required>
                                    <option value="pg">Pilihan Ganda (A-<?= $bank['opsi_pg'] == 4 ? 'D' : 'E' ?>)
                                    </option>
                                    <option value="essay">Essay / Uraian</option>
                                    <option value="tf">Benar / Salah (TF)</option>
                                    <option value="matching">Menjodohkan (Matching)</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1">Pertanyaan <span
                                        class="text-danger">*</span></label>
                                <textarea name="pertanyaan" id="pertanyaan" class="form-control" rows="4"
                                    placeholder="Ketikkan pertanyaan di sini..." required></textarea>
                            </div>

                            <div class="form-row mb-3">
                                <div class="col-md-6">
                                    <label class="font-weight-bold mb-1 d-block">Gambar Pertanyaan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="fas fa-image"></i></span></div>
                                        <input type="file" name="gambar_soal" id="gambar_soal"
                                            class="form-control form-control-file" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-bold mb-1 d-block">Acak Soal?</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" class="custom-control-input" id="is_acak_soal"
                                            name="is_acak_soal" value="1" checked>
                                        <label class="custom-control-label" for="is_acak_soal">Ya</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-bold mb-1 d-block">Acak Opsi?</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" class="custom-control-input" id="is_acak_opsi"
                                            name="is_acak_opsi" value="1" checked>
                                        <label class="custom-control-label" for="is_acak_opsi">Ya</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi Pilihan Ganda -->
                            <div id="section-pg">
                                <hr class="my-3">
                                <label class="font-weight-bold mb-2">Opsi Jawaban & Kunci</label>
                                <?php foreach (['A', 'B', 'C', 'D', 'E'] as $l):
                                    $hide_e = ($l == 'E' && $bank['opsi_pg'] == 4);
                                    ?>
                                    <div class="mb-3 <?= $hide_e ? 'd-none' : '' ?>">
                                        <div class="input-group shadow-sm">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-light font-weight-bold">
                                                    <?= $l ?>
                                                </div>
                                            </div>
                                            <input type="text" name="opsi_<?= $l ?>" id="opsi_<?= $l ?>"
                                                class="form-control" placeholder="Isi opsi <?= $l ?>">
                                            <div class="input-group-append">
                                                <div class="input-group-text bg-white border-left-0">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="kunci_<?= $l ?>" name="kunci_jawaban"
                                                            value="<?= $l ?>" class="custom-control-input">
                                                        <label class="custom-control-label" for="kunci_<?= $l ?>"
                                                            title="Set sebagai kunci"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group input-group-sm mt-1">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light text-muted"><i
                                                        class="fas fa-image fa-xs"></i></span>
                                            </div>
                                            <input type="file" name="gambar_opsi_<?= $l ?>"
                                                class="form-control form-control-file form-control-sm" accept="image/*"
                                                style="font-size:0.75rem;">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Opsi TF -->
                            <div id="section-tf" style="display:none;">
                                <hr class="my-3">
                                <label class="font-weight-bold mb-2">Kunci Jawaban (TF)</label>
                                <div class="d-flex border rounded p-2 bg-light shadow-sm">
                                    <div class="custom-control custom-radio mr-4 ml-2">
                                        <input type="radio" id="tf_b" name="kunci_tf" value="B"
                                            class="custom-control-input">
                                        <label class="custom-control-label font-weight-bold text-success"
                                            for="tf_b">BENAR</label>
                                    </div>
                                    <div class="custom-control custom-radio ml-4">
                                        <input type="radio" id="tf_s" name="kunci_tf" value="S"
                                            class="custom-control-input">
                                        <label class="custom-control-label font-weight-bold text-danger"
                                            for="tf_s">SALAH</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi Matching -->
                            <div id="section-matching" style="display:none;">
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="font-weight-bold mb-0">Pasangan Menjodohkan</label>
                                    <button type="button" class="btn btn-xs btn-primary font-weight-bold"
                                        id="btnAddPair">
                                        <i class="fas fa-plus mr-1"></i> Tambah Baris
                                    </button>
                                </div>
                                <div id="matching-container">
                                    <div class="row no-gutters mb-2 matching-row">
                                        <div class="col-5 mr-1">
                                            <input type="text" name="match_p[]" class="form-control form-control-sm"
                                                placeholder="Item Kiri (Premis)">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" name="match_r[]" class="form-control form-control-sm"
                                                placeholder="Item Kanan (Respon)">
                                        </div>
                                        <div class="col-1 ml-auto text-right">
                                            <button type="button" class="btn btn-xs btn-outline-danger btnRemovePair"><i
                                                    class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted italic">* Premis dan Respon di atas adalah pasangan yang
                                    benar.</small>
                            </div>

                            <div class="row mt-4">
                                <div class="col-6">
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold mb-1">Bobot Nilai</label>
                                        <input type="number" name="bobot" id="bobot"
                                            class="form-control form-control-sm" value="1" min="1">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold mb-1">Kesulitan</label>
                                        <select name="tingkat_kesulitan" id="kesulitan"
                                            class="form-control form-control-sm">
                                            <option value="mudah">Mudah</option>
                                            <option value="sedang" selected>Sedang</option>
                                            <option value="sulit">Sulit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-3 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary shadow-sm" id="btnBatal"
                                style="display:none;">Batal Edit</button>
                            <button type="submit" class="btn btn-success shadow-sm ml-auto font-weight-bold px-4">
                                <i class="fas fa-save mr-1"></i> Simpan Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Soal -->
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-list mr-2 text-primary"></i>Daftar Soal
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($soal_list)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-pencil-alt fa-3x opacity-25 mb-3"></i>
                                <p>Belum ada soal di dalam bank ini. Silakan input soal pertama Anda.</p>
                            </div>
                        <?php else:
                            foreach ($soal_list as $s): ?>
                                <div class="p-3 border-bottom hover-bg-light">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <span class="badge badge-outline-dark border px-2 py-1 mr-2">
                                                <?= $s['nomor_urut'] ?>.
                                            </span>
                                            <span class="badge badge-info text-uppercase font-size-10 px-2">
                                                <?= $s['tipe_soal'] ?>
                                            </span>
                                            <?php if (!empty($s['is_acak_soal'])): ?>
                                                <span class="badge badge-light border text-muted font-size-10 px-2 ml-1"
                                                    title="Soal diacak">
                                                    <i class="fas fa-random mr-1"></i>Acak
                                                </span>
                                            <?php endif; ?>
                                            <span
                                                class="badge badge-<?= $s['tingkat_kesulitan'] == 'mudah' ? 'success' : ($s['tingkat_kesulitan'] == 'sulit' ? 'danger' : 'warning') ?> text-uppercase font-size-10 px-2">
                                                <?= $s['tingkat_kesulitan'] ?>
                                            </span>
                                            <span class="badge badge-secondary font-size-10 px-2 ml-1">Bobot:
                                                <?= $s['bobot'] ?>
                                            </span>
                                        </div>
                                        <div>
                                            <button class="btn btn-xs btn-warning btn-edit-soal shadow-sm"
                                                data-json='<?= json_encode($s) ?>'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-xs btn-danger btn-delete-soal shadow-sm"
                                                data-id="<?= $s['id_soal'] ?>" data-no="<?= $s['nomor_urut'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="soal-text font-weight-bold mb-2">
                                        <?= nl2br(htmlspecialchars($s['pertanyaan'])) ?>
                                    </div>

                                    <?php if (!empty($s['media'])): ?>
                                        <div class="soal-media mb-3">
                                            <?php foreach ($s['media'] as $m): ?>
                                                <div class="mb-2">
                                                    <?php if ($m['tipe_media'] == 'gambar'): ?>
                                                        <img src="/simaks/cbt/<?= $m['path_file'] ?>"
                                                            class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
                                                    <?php elseif ($m['tipe_media'] == 'audio'): ?>
                                                        <audio controls class="w-100 mt-1" style="height: 30px;">
                                                            <source src="/simaks/cbt/<?= $m['path_file'] ?>" type="audio/mpeg">
                                                        </audio>
                                                    <?php elseif ($m['tipe_media'] == 'video'): ?>
                                                        <video controls class="w-100 rounded border shadow-sm" style="max-height: 250px;">
                                                            <source src="/simaks/cbt/<?= $m['path_file'] ?>" type="video/mp4">
                                                        </video>
                                                    <?php endif; ?>
                                                    <div class="small text-muted mt-1 font-size-10">
                                                        <i class="fas fa-paperclip mr-1"></i> <?= htmlspecialchars($m['nama_file']) ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($s['tipe_soal'] == 'pg' || $s['tipe_soal'] == 'tf'): ?>
                                        <div class="row small no-gutters">
                                            <?php
                                            foreach ($s['opsi'] as $o):
                                                $cls = $o['is_benar'] ? 'text-success font-weight-bold' : 'text-muted';
                                                $icon = $o['is_benar'] ? '<i class="fas fa-check-circle mr-1"></i>' : '<i class="far fa-circle mr-1 opacity-50"></i>';
                                                ?>
                                                <div class="col-md-6 mb-1 <?= $cls ?>">
                                                    <div class="d-flex align-items-top">
                                                        <span class="mr-1"><?= $icon ?><?= $o['label'] ?>.</span>
                                                        <span><?= htmlspecialchars($o['isi_opsi']) ?></span>
                                                    </div>
                                                    <?php if (!empty($o['gambar'])): ?>
                                                        <img src="/simaks/cbt/uploads/soal/bank_<?= $s['id_bank'] ?>/<?= htmlspecialchars($o['gambar']) ?>"
                                                            class="img-fluid rounded border mt-1 ml-3" style="max-height:80px;">
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif ($s['tipe_soal'] == 'matching'): ?>
                                        <div class="matching-pairs small">
                                            <div class="text-muted mb-1 font-italic">Pasangan (Menjodohkan):</div>
                                            <div class="row no-gutters">
                                                <?php foreach ($s['opsi'] as $o): ?>
                                                    <div class="col-12 mb-1">
                                                        <span
                                                            class="badge badge-light border"><?= htmlspecialchars($o['label']) ?></span>
                                                        <i class="fas fa-long-arrow-alt-right mx-2 text-primary"></i>
                                                        <span class="badge badge-info"><?= htmlspecialchars($o['isi_opsi']) ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .hover-bg-light:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .font-size-10 {
        font-size: 10px;
    }

    .btn-xs {
        padding: 0.125rem 0.25rem;
        font-size: 0.75rem;
    }
</style>

<script>
    $(document).ready(function () {
        const sectionPG = $('#section-pg');
        const sectionTF = $('#section-tf');

        // Handle Tipe Soal Change
        $('#tipe_soal').on('change', function () {
            const val = $(this).val();
            sectionPG.hide();
            sectionTF.hide();
            $('#section-matching').hide();

            if (val === 'pg') {
                sectionPG.slideDown();
            } else if (val === 'tf') {
                sectionTF.slideDown();
            } else if (val === 'matching') {
                $('#section-matching').slideDown();
            }
        });

        // Add/Remove Matching Pairs
        $('#btnAddPair').on('click', function () {
            const html = `
                <div class="row no-gutters mb-2 matching-row">
                    <div class="col-5 mr-1">
                        <input type="text" name="match_p[]" class="form-control form-control-sm" placeholder="Item Kiri (Premis)">
                    </div>
                    <div class="col-5">
                        <input type="text" name="match_r[]" class="form-control form-control-sm" placeholder="Item Kanan (Respon)">
                    </div>
                    <div class="col-1 ml-auto text-right">
                        <button type="button" class="btn btn-xs btn-outline-danger btnRemovePair"><i class="fas fa-times"></i></button>
                    </div>
                </div>`;
            $('#matching-container').append(html);
        });

        $(document).on('click', '.btnRemovePair', function () {
            $(this).closest('.matching-row').remove();
        });

        // Edit Soal Handler
        $(document).on('click', '.btn-edit-soal', function () {
            const data = $(this).data('json');

            $('#formTitle').html('<i class="fas fa-edit mr-2 text-warning"></i>Edit Soal #' + data.nomor_urut);
            $('#id_soal').val(data.id_soal);
            $('#tipe_soal').val(data.tipe_soal).trigger('change');
            $('#pertanyaan').val(data.pertanyaan);
            $('#bobot').val(data.bobot);
            $('#kesulitan').val(data.tingkat_kesulitan);
            $('#btnBatal').show();

            // Fill Options
            if (data.tipe_soal === 'pg') {
                data.opsi.forEach(o => {
                    if (o.label === 'E' && <?= $bank['opsi_pg'] ?> == 4) return;
                    $('#opsi_' + o.label).val(o.isi_opsi);
                    if (o.is_benar == 1) $('#kunci_' + o.label).prop('checked', true);
                });
            } else if (data.tipe_soal === 'tf') {
                data.opsi.forEach(o => {
                    if (o.label === 'B') $('#tf_b').prop('checked', true);
                    else $('#tf_s').prop('checked', true);
                });
            } else if (data.tipe_soal === 'matching') {
                $('#matching-container').empty();
                data.opsi.forEach(o => {
                    const row = `
                        <div class="row no-gutters mb-2 matching-row">
                            <div class="col-5 mr-1">
                                <input type="text" name="match_p[]" class="form-control form-control-sm" value="${o.label}">
                            </div>
                            <div class="col-5">
                                <input type="text" name="match_r[]" class="form-control form-control-sm" value="${o.isi_opsi}">
                            </div>
                            <div class="col-1 ml-auto text-right">
                                <button type="button" class="btn btn-xs btn-outline-danger btnRemovePair"><i class="fas fa-times"></i></button>
                            </div>
                        </div>`;
                    $('#matching-container').append(row);
                });
                if (data.opsi.length === 0) $('#btnAddPair').trigger('click');
            }
        });

        // Batal Edit
        $('#btnBatal').on('click', function () {
            $('#formTitle').html('<i class="fas fa-plus-circle mr-2 text-success"></i>Tambah Soal Baru');
            $('#id_soal').val(0);
            $('#formSoal')[0].reset();
            $('#tipe_soal').trigger('change');
            $(this).hide();
        });

        // Delete Soal
        $(document).on('click', '.btn-delete-soal', function () {
            const id = $(this).data('id');
            const no = $(this).data('no');

            Swal.fire({
                title: 'Hapus Soal #' + no + '?',
                text: "Aksi ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?mod=input_soal&act=delete_soal&id_bank=<?= $bank['id_bank'] ?>&id=' + id;
                }
            });
        });

        // Notifications
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('ok')) {
            let msg = 'Data soal telah disimpan.';
            if (urlParams.has('imported')) {
                msg = urlParams.get('imported') + ' butir soal berhasil diimport.';
            } else if (urlParams.has('media_uploaded')) {
                msg = urlParams.get('media_uploaded') + ' file media berhasil diunggah.';
            }
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: msg, timer: 2500, showConfirmButton: false });
        } else if (urlParams.has('del')) {
            Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Soal telah berhasil dihapus.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.has('err')) {
            const errType = urlParams.get('err');
            let errMsg = 'Terjadi kesalahan sistem.';
            if (errType === 'empty') errMsg = 'Pastikan seluruh input wajib terisi.';
            if (errType === 'upload') errMsg = 'Gagal upload file.';
            if (errType === 'zip') errMsg = 'Gagal mengekstrak file ZIP. Pastikan file valid.';
            if (errType === 'excel_parse') errMsg = 'Gagal membaca isi file excel. Pastikan format sesuai template.';
            Swal.fire({ icon: 'error', title: 'Gagal!', text: errMsg });
        }

        // Filename display for custom file input
        $('.custom-file-input').on('change', function () {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=input_soal&act=import&id_bank=<?= $bank['id_bank'] ?>" method="POST"
            enctype="multipart/form-data">
            <input type="hidden" name="id_bank" value="<?= $bank['id_bank'] ?>">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i>Import Soal dari Excel</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-info-circle mr-1"></i> Instruksi:</h6>
                        <ul class="pl-3 mb-0 small">
                            <li>Gunakan template yang telah disediakan agar format sesuai.</li>
                            <li>Kolom <b>Tipe</b> diisi dengan: <code>pg</code>, <code>essay</code>, atau
                                <code>tf</code>.
                            </li>
                            <li>Kolom <b>Jawaban</b> untuk PG diisi: <code>A/B/C/D/E</code>. Untuk TF diisi:
                                <code>B/S</code>.
                            </li>
                        </ul>
                        <hr class="my-2 opacity-25">
                        <a href="index.php?mod=input_soal&act=download_template&id_bank=<?= $bank['id_bank'] ?>"
                            class="btn btn-dark btn-sm font-weight-bold">
                            <i class="fas fa-download mr-1"></i> Unduh Template Soal (.xlsx)
                        </a>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold mb-2">Pilih File Excel</label>
                        <div class="input-group">
                            <div class="custom-file border">
                                <input type="file" name="file_excel" class="custom-file-input" id="inputGroupFile01"
                                    accept=".xlsx, .xls" required>
                                <label class="custom-file-label" for="inputGroupFile01 text-muted">Klik untuk memilih
                                    file...</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-secondary px-4 shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm font-weight-bold">Mulai Import</button>
                </div>
            </div>
        </form>
    </div>
</div>