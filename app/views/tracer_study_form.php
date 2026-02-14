<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-route mr-2"></i> <?= $tracer_data ? 'Edit' : 'Tambah' ?> Data Tracer Study</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Data Tracer Study Alumni</h3>
            </div>

            <form action="index.php?mod=tracer_study&act=save" method="POST">
                <?php if ($tracer_data): ?>
                    <input type="hidden" name="id_tracer" value="<?= $tracer_data['id_tracer'] ?>">
                    <input type="hidden" name="id_siswa" value="<?= $tracer_data['id_siswa'] ?>">
                    <input type="hidden" name="tahun_lulus" value="<?= $tracer_data['tahun_lulus'] ?>">
                <?php endif; ?>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php if (!$tracer_data): ?>
                                <!-- Pilih Alumni (hanya untuk mode tambah) -->
                                <div class="form-group">
                                    <label>Pilih Alumni <span class="text-danger">*</span></label>
                                    <select name="id_siswa" id="id_siswa" class="form-control" required>
                                        <option value="">-- Pilih Alumni --</option>
                                        <?php foreach ($alumni_list as $alumni): ?>
                                            <option value="<?= $alumni['id_siswa'] ?>"
                                                data-tahun="<?= $alumni['tahun_lulus'] ?>">
                                                <?= htmlspecialchars($alumni['nama']) ?>
                                                (<?= $alumni['nisn'] ?>) -
                                                Lulus <?= $alumni['tahun_lulus'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">
                                        Hanya menampilkan alumni yang belum memiliki data tracer
                                    </small>
                                </div>
                            <?php else: ?>
                                <!-- Display Alumni Info (mode edit) -->
                                <div class="form-group">
                                    <label>Nama Alumni</label>
                                    <input type="text" class="form-control"
                                        value="<?= htmlspecialchars($tracer_data['nama']) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>NISN</label>
                                    <input type="text" class="form-control"
                                        value="<?= htmlspecialchars($tracer_data['nisn']) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Tahun Lulus</label>
                                    <input type="text" class="form-control" value="<?= $tracer_data['tahun_lulus'] ?>"
                                        readonly>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <!-- Status Setelah Lulus -->
                            <div class="form-group">
                                <label>Status Setelah Lulus <span class="text-danger">*</span></label>
                                <div>
                                    <?php
                                    $statuses = ['PTN/PTS', 'Bekerja', 'Wirausaha', 'Lain-lain'];
                                    $current_status = $tracer_data['status_setelah_lulus'] ?? '';
                                    ?>
                                    <?php foreach ($statuses as $status): ?>
                                        <div class="custom-control custom-radio">
                                            <input type="radio"
                                                id="status_<?= strtolower(str_replace('/', '_', $status)) ?>"
                                                name="status_setelah_lulus" value="<?= $status ?>"
                                                class="custom-control-input" <?= $current_status == $status ? 'checked' : '' ?>
                                                required>
                                            <label class="custom-control-label"
                                                for="status_<?= strtolower(str_replace('/', '_', $status)) ?>">
                                                <?= $status ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Nama Institusi/Perusahaan -->
                            <div class="form-group">
                                <label>Nama Institusi/Perusahaan/Usaha</label>
                                <input type="text" name="nama_institusi" class="form-control"
                                    value="<?= htmlspecialchars($tracer_data['nama_institusi'] ?? '') ?>"
                                    placeholder="Contoh: Universitas Indonesia, PT. Telkom, Toko Berkah">
                                <small class="form-text text-muted">
                                    Nama Perguruan Tinggi, Perusahaan, atau Usaha
                                </small>
                            </div>

                            <!-- Jurusan/Bidang Pekerjaan -->
                            <div class="form-group">
                                <label>Jurusan/Bidang Pekerjaan</label>
                                <input type="text" name="jurusan_pekerjaan" class="form-control"
                                    value="<?= htmlspecialchars($tracer_data['jurusan_pekerjaan'] ?? '') ?>"
                                    placeholder="Contoh: Teknik Informatika, Staff IT, Perdagangan">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Kota -->
                            <div class="form-group">
                                <label>Kota</label>
                                <input type="text" name="kota" class="form-control"
                                    value="<?= htmlspecialchars($tracer_data['kota'] ?? '') ?>"
                                    placeholder="Contoh: Jakarta, Bandung, Sukabumi">
                            </div>

                            <!-- Keterangan -->
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"
                                    placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($tracer_data['keterangan'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="index.php?mod=tracer_study" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    $(function () {
        // Auto-fill tahun lulus when selecting alumni (for add mode)
        $('#id_siswa').on('change', function () {
            const selectedOption = $(this).find('option:selected');
            const tahunLulus = selectedOption.data('tahun');

            // You can use this to auto-fill or validate
            console.log('Selected alumni, tahun lulus:', tahunLulus);
        });
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>