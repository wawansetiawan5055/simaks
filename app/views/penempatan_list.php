<?php include __DIR__ . '/partials/header.php'; ?>

<!-- Content Header -->
<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4">
            <div>
                <h2 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-layer-group text-primary mr-2"></i> Daftar Rombel (Kelas)
                </h2>
                <p class="text-muted small mb-0">
                    Kelola data rombongan belajar dan penempatan siswa TA: <?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') ?>
                </p>
            </div>
            <?php if (can_do($pdo, 'penempatan', 'create')): ?>
                <button type="button" class="btn btn-warning shadow-sm px-4 font-weight-bold text-white" style="border-radius: 8px;" data-toggle="modal" data-target="#modalTambahRombel">
                    <i class="fas fa-plus mr-2"></i> Tambah Rombel
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 font-weight-bold text-muted"><i class="fas fa-list-ul mr-2 text-primary"></i> DAFTAR KELAS AKTIF</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (isset($_SESSION['pesan_sukses'])): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-0 m-0 border-0" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> <?= $_SESSION['pesan_sukses']; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['pesan_sukses']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['pesan_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-0 m-0 border-0" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i> <?= $_SESSION['pesan_error']; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['pesan_error']); ?>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8fafc;">
                            <tr class="text-muted">
                                <th class="text-center py-3 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                                <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA KELAS</th>
                                <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">WALI KELAS</th>
                                <th class="text-center py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 80px;">L</th>
                                <th class="text-center py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 80px;">P</th>
                                <th class="text-center py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 80px;">TOTAL</th>
                                <th class="text-center py-3 border-bottom" style="width: 150px; font-size: 0.7rem; letter-spacing: 1px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_total_l = 0;
                            $grand_total_p = 0;
                            $grand_total_all = 0;
                            
                            if (empty($rekap_kelas)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-20" style="color: #cbd5e1;"></i>
                                        <em>Belum ada data kelas untuk Tahun Ajaran ini.</em>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($rekap_kelas as $row): 
                                    $grand_total_l += $row['laki'];
                                    $grand_total_p += $row['perempuan'];
                                    $grand_total_all += $row['total'];
                                ?>
                                    <tr>
                                        <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++ ?></td>
                                        <td class="align-middle">
                                            <a href="index.php?mod=penempatan&act=kelola&id_kelas=<?= $row['id_kelas'] ?>" class="font-weight-bold text-dark" style="font-size: 0.95rem; text-decoration: none;">
                                                <?= htmlspecialchars($row['nama_kelas']) ?>
                                            </a>
                                        </td>
                                        <td class="align-middle">
                                            <?php if($row['nama_walas']): ?>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-2 text-primary" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-user-tie"></i>
                                                    </div>
                                                    <span class="text-dark small font-weight-bold"><?= htmlspecialchars($row['nama_walas']) ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge badge-light text-muted border px-2 py-1 footer-badge">Belum ada walas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle font-weight-bold text-info"><?= $row['laki'] ?></td>
                                        <td class="text-center align-middle font-weight-bold text-danger"><?= $row['perempuan'] ?></td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-light border px-3 py-2" style="font-size: 0.85rem; border-radius: 6px;"><?= $row['total'] ?></span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group">
                                                <a href="index.php?mod=penempatan&act=kelola&id_kelas=<?= $row['id_kelas'] ?>"
                                                    class="btn btn-sm btn-outline-primary border-0 px-2 mr-1" 
                                                    style="background: #eff6ff; color: #3b82f6; border-radius: 6px;" title="Kelola Anggota">
                                                    <i class="fas fa-users-cog"></i>
                                                </a>
                                                <?php if (can_do($pdo, 'penempatan', 'delete')): ?>
                                                    <a href="index.php?mod=penempatan&act=hapus_rombel&id_kelas=<?= $row['id_kelas'] ?>"
                                                        class="btn btn-sm btn-outline-danger border-0 px-2"
                                                        style="background: #fef2f2; color: #ef4444; border-radius: 6px;"
                                                        onclick="return confirm('⚠️ Hapus rombel ini?\nSeluruh data penempatan siswa, jadwal, dan wali kelas di dalamnya akan dihapus. Lanjutkan?')"
                                                        title="Hapus Rombel">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($rekap_kelas)): ?>
                        <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                            <tr>
                                <td colspan="3" class="text-right py-3 pr-4 font-weight-bold text-muted small uppercase" style="letter-spacing: 1px;">TOTAL KESELURUHAN</td>
                                <td class="text-center py-3 font-weight-bold text-info"><?= $grand_total_l ?></td>
                                <td class="text-center py-3 font-weight-bold text-danger"><?= $grand_total_p ?></td>
                                <td class="text-center py-3 font-weight-bold text-dark" style="font-size: 1rem;"><?= $grand_total_all ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL TAMBAH ROMBEL -->
<div class="modal fade" id="modalTambahRombel" tabindex="-1" role="dialog" aria-labelledby="modalTambahRombelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold" id="modalTambahRombelLabel"><i class="fas fa-plus-circle mr-2"></i> Tambah Rombel Baru</h5>
                <button type="button" class="close text-white opacity-1" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php?mod=penempatan&act=tambah_rombel" method="POST">
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Tingkat / Kelas Master</label>
                        <select name="tingkat" class="form-control form-control-lg bg-light border-0" required style="border-radius: 8px;">
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="X">Tingkat X</option>
                            <option value="XI">Tingkat XI</option>
                            <option value="XII">Tingkat XII</option>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Nama Rombel / Kelas</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0 pl-3" style="border-radius: 8px 0 0 8px;"><i class="fas fa-signature text-muted"></i></span>
                            </div>
                            <input type="text" name="nama_kelas" class="form-control form-control-lg bg-light border-0" list="class_suggestions"
                                placeholder="Contoh: XII.1 atau XI-IPA-1" required style="border-radius: 0 8px 8px 0;">
                        </div>
                        <datalist id="class_suggestions">
                            <?php foreach ($standard_classes as $sc): ?>
                                <option value="<?= $sc ?>">
                            <?php endforeach; ?>
                        </datalist>
                        <small class="form-text text-muted mt-2">Pilih dari saran atau ketik nama kelas baru.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Wali Kelas (Opsional)</label>
                        <select name="id_guru" class="form-control select2" style="width: 100%;">
                            <option value="0">-- Pilih Wali Kelas --</option>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= $g['id_guru'] ?>"><?= htmlspecialchars($g['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted mt-2">Otomatis mendaftarkan wali kelas untuk rombel ini.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3" style="border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-save mr-2"></i> Simpan Rombel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 with Bootstrap 4 theme
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih Wali Kelas --",
            allowClear: true,
            dropdownParent: $('#modalTambahRombel')
        });

        // Add hover effect to table rows
        $('.table-hover tbody tr').hover(
            function() { $(this).addClass('bg-light'); },
            function() { $(this).removeClass('bg-light'); }
        );
    });
</script>

<style>
/* Custom modern styles */
.input-group-text {
    color: #64748b;
}
.modal-backdrop.show {
    opacity: 0.5;
    backdrop-filter: blur(2px);
}
.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.875rem + 2px) !important;
    background-color: #f8f9fa !important;
    border: none !important;
    border-radius: 8px !important;
    padding-top: 0.5rem;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
    color: #6c757d;
    line-height: 2.875rem;
}
</style>

<?php include __DIR__ . '/partials/footer.php'; ?>
