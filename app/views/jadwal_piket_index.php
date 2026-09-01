<?php
// app/views/jadwal_piket_index.php
include __DIR__ . '/partials/header.php';

$days_meta = [
    'Senin'  => ['color' => '#4f46e5', 'bg' => '#eef2ff', 'border' => '#c7d2fe', 'icon' => 'fa-calendar-day'],
    'Selasa' => ['color' => '#0284c7', 'bg' => '#f0f9ff', 'border' => '#bae6fd', 'icon' => 'fa-calendar-day'],
    'Rabu'   => ['color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0', 'icon' => 'fa-calendar-day'],
    'Kamis'  => ['color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a', 'icon' => 'fa-calendar-day'],
    'Jumat'  => ['color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca', 'icon' => 'fa-calendar-day']
];

$active_tab = $active_tab ?? 'piket';
?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Jadwal Piket &amp; Non-KBM GTK
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <?php if ($active_tab === 'piket'): ?>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold rounded-pill px-3" data-toggle="modal" data-target="#modalTambahPiket">
                        <i class="fas fa-plus mr-1"></i> Tambah Guru Piket
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>
        <!-- Filter Bar & Navigation Tabs -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: #fff;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-pills" id="piketNonKbmTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold <?= ($active_tab === 'piket') ? 'active' : '' ?>" id="piket-tab" data-toggle="pill" href="#tab-piket" role="tab" style="border-radius: 8px;">
                                <i class="fas fa-user-shield mr-1"></i> Jadwal Guru Piket
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold <?= ($active_tab === 'non_kbm') ? 'active' : '' ?>" id="non-kbm-tab" data-toggle="pill" href="#tab-non-kbm" role="tab" style="border-radius: 8px;">
                                <i class="fas fa-calendar-alt mr-1"></i> Hari Ngantor / Non-KBM GTK
                            </a>
                        </li>
                    </ul>

                    <!-- Year Selector -->
                    <form method="GET" action="<?= BASE_URL ?>index.php" class="form-inline">
                        <input type="hidden" name="mod" value="jadwal_piket">
                        <input type="hidden" name="tab" id="filter_active_tab" value="<?= htmlspecialchars($active_tab) ?>">
                        <label class="mr-2 font-weight-bold text-secondary text-sm">Tahun Ajaran:</label>
                        <select name="id_ta" class="form-control form-control-sm custom-select" onchange="this.form.submit()" style="min-width: 220px; border-radius: 8px;">
                            <?php foreach ($ta_list as $ta): ?>
                                <option value="<?= $ta['id_ta'] ?>" <?= ($ta['id_ta'] == $id_ta_filter) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ta['nama_ta']) ?> <?= ($ta['status'] === 'Aktif') ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-content" id="piketNonKbmTabContent">
            <!-- ========================================== -->
            <!-- TAB 1: JADWAL GURU PIKET (Senin - Jumat)  -->
            <!-- ========================================== -->
            <div class="tab-pane fade <?= ($active_tab === 'piket') ? 'show active' : '' ?>" id="tab-piket" role="tabpanel">
                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" style="border-radius: 10px; font-size: 0.85rem;">
                    <i class="fas fa-info-circle fa-lg mr-2"></i>
                    <div>
                        Guru piket bertugas memantau keterlaksanaan KBM, merekap presensi harian siswa rombel, dan presensi guru di sekolah (Senin s.d. Jumat).
                    </div>
                </div>

                <!-- Grid 5 Hari (Senin - Jumat) -->
                <div class="row">
                    <?php foreach ($days_meta as $day => $meta): 
                        $list = $jadwal_weekly[$day] ?? [];
                        $count = count($list);
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 14px; overflow: hidden; border-top: 4px solid <?= $meta['color'] ?> !important;">
                            <div class="card-header d-flex align-items-center justify-content-between py-3" style="background: <?= $meta['bg'] ?>; border-bottom: 1px solid <?= $meta['border'] ?>;">
                                <div class="d-flex align-items-center">
                                    <span class="d-inline-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px; border-radius: 8px; background: <?= $meta['color'] ?>; color: #fff;">
                                        <i class="fas <?= $meta['icon'] ?> text-sm"></i>
                                    </span>
                                    <h6 class="m-0 font-weight-bold" style="color: <?= $meta['color'] ?>; font-size: 1.05rem;">
                                        Hari <?= $day ?>
                                    </h6>
                                </div>
                                <span class="badge badge-pill shadow-none px-2 py-1" style="background: <?= $meta['color'] ?>; color: #fff; font-size: 0.75rem;">
                                    <?= $count ?> Guru
                                </span>
                            </div>

                            <div class="card-body p-3 d-flex flex-column">
                                <?php if (empty($list)): ?>
                                    <div class="text-center py-4 w-100 my-auto">
                                        <i class="fas fa-user-clock text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="text-muted text-sm mb-0">Belum ada guru piket dijadwalkan.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush w-100">
                                        <?php foreach ($list as $item): ?>
                                        <div class="list-group-item px-2 py-2 mb-2 d-flex align-items-center justify-content-between border rounded" style="background: #ffffff; border-color: #f1f5f9 !important;">
                                            <div class="d-flex align-items-center" style="min-width: 0;">
                                                <div class="mr-2 flex-shrink-0">
                                                    <?php if (!empty($item['foto'])): ?>
                                                        <img src="<?= BASE_URL ?>assets/img/profil/<?= htmlspecialchars($item['foto']) ?>" class="img-circle" style="width: 36px; height: 36px; object-fit: cover;" alt="Foto">
                                                    <?php else: ?>
                                                        <div class="img-circle bg-light d-flex align-items-center justify-content-center text-secondary font-weight-bold" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                                            <?= strtoupper(substr($item['nama_guru'], 0, 1)) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div style="min-width: 0;">
                                                    <div class="font-weight-bold text-dark text-truncate text-sm" style="max-width: 170px;" title="<?= htmlspecialchars($item['nama_guru']) ?>">
                                                        <?= htmlspecialchars($item['nama_guru']) ?>
                                                    </div>
                                                    <small class="text-muted d-block">
                                                        <?= htmlspecialchars($item['keterangan'] ?: 'Piket Harian') ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <a href="<?= BASE_URL ?>jadwal_piket/delete?id=<?= $item['id_jadwal_piket'] ?>&id_ta=<?= $id_ta_filter ?>" 
                                               class="btn btn-sm btn-outline-danger btn-delete-confirm ml-2 flex-shrink-0" 
                                               title="Hapus dari jadwal hari <?= $day ?>"
                                               style="border-radius: 6px; padding: 2px 8px;">
                                                <i class="fas fa-trash-alt fa-xs"></i>
                                            </a>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-footer bg-white border-top-0 pt-0 pb-3 px-3">
                                <button type="button" class="btn btn-block btn-sm btn-outline-secondary" onclick="openAddModalForDay('<?= $day ?>')" style="border-radius: 8px; border-style: dashed;">
                                    <i class="fas fa-plus mr-1"></i> Tambah ke <?= $day ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- TAB 2: MATRIKS HARI KERJA & HARI NGANTOR / NON-KBM GTK      -->
            <!-- ============================================================ -->
            <div class="tab-pane fade <?= ($active_tab === 'non_kbm') ? 'show active' : '' ?>" id="tab-non-kbm" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-th mr-2 text-primary"></i> Matriks Hari Kerja & Penugasan Non-KBM GTK (Senin - Jumat)
                            </h5>
                            <small class="text-muted">
                                Guru otomatis wajib hadir di hari <strong>KBM</strong> atau <strong>Piket</strong>. Centang hari tambahan untuk <strong>Hari Ngantor / Standby / Tugas Khusus</strong>.
                            </small>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <span class="badge badge-light border text-dark mr-1"><i class="fas fa-chalkboard-teacher text-primary mr-1"></i> KBM (Otomatis)</span>
                            <span class="badge badge-light border text-dark mr-1"><i class="fas fa-user-shield text-danger mr-1"></i> Piket (Otomatis)</span>
                            <span class="badge badge-success text-white"><i class="fas fa-check mr-1"></i> Ngantor / Standby</span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <form action="<?= BASE_URL ?>jadwal_piket/save_non_kbm" method="POST">
                            <input type="hidden" name="id_ta" value="<?= $id_ta_filter ?>">
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0" style="font-size: 0.85rem;">
                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th style="width: 40px;">No</th>
                                            <th style="min-width: 220px;" class="text-left">Nama GTK & Peran</th>
                                            <th style="width: 125px;">Senin</th>
                                            <th style="width: 125px;">Selasa</th>
                                            <th style="width: 125px;">Rabu</th>
                                            <th style="width: 125px;">Kamis</th>
                                            <th style="width: 125px;">Jumat</th>
                                            <th style="width: 100px;">Total Wajib</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($gtk_matrix)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data GTK aktif.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php 
                                            $no = 1;
                                            foreach ($gtk_matrix as $row): 
                                                $id_g = $row['id_guru'];
                                                $total_wajib_hari = 0;
                                            ?>
                                            <tr>
                                                <td class="text-center align-middle font-weight-bold text-muted"><?= $no++ ?></td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold text-dark"><?= htmlspecialchars($row['nama']) ?></div>
                                                    <div class="d-flex align-items-center mt-1" style="gap: 4px;">
                                                        <span class="badge badge-light border text-secondary" style="font-size: 0.68rem;"><?= htmlspecialchars($row['peran']) ?></span>
                                                        <?php if (!empty($row['kode_guru'])): ?>
                                                            <span class="badge badge-secondary" style="font-size: 0.65rem;"><?= htmlspecialchars($row['kode_guru']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day): 
                                                    $cell = $row['days'][$day] ?? [];
                                                    $is_kbm = !empty($cell['kbm']);
                                                    $is_piket = !empty($cell['piket']);
                                                    $is_non_kbm = !empty($cell['non_kbm']);
                                                    $is_tendik = !empty($row['is_tendik']);

                                                    if ($is_kbm || $is_piket || $is_non_kbm || $is_tendik) {
                                                        $total_wajib_hari++;
                                                    }
                                                ?>
                                                <td class="text-center align-middle <?= ($is_kbm || $is_piket || $is_non_kbm) ? 'bg-light' : '' ?>" style="padding: 6px;">
                                                    <?php if ($is_tendik): ?>
                                                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.70rem;" title="Staf TU / Tendik">
                                                            <i class="fas fa-building mr-1"></i> TU / Tendik
                                                        </span>
                                                    <?php else: ?>
                                                        <div class="d-flex flex-column align-items-center" style="gap: 3px;">
                                                            <?php if ($is_kbm): ?>
                                                                <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 0.68rem;" title="<?= htmlspecialchars($cell['kbm']['mapel_list'] ?? 'Mengajar KBM') ?>">
                                                                    <i class="fas fa-chalkboard mr-1"></i> KBM (<?= $cell['kbm']['total_kelas'] ?> Kls)
                                                                </span>
                                                            <?php endif; ?>

                                                            <?php if ($is_piket): ?>
                                                                <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 0.68rem;" title="Petugas Piket">
                                                                    <i class="fas fa-user-shield mr-1"></i> Piket
                                                                </span>
                                                            <?php endif; ?>

                                                            <!-- Checkbox Hari Ngantor / Non-KBM Tambahan -->
                                                            <div class="custom-control custom-checkbox mt-1">
                                                                <input type="checkbox" 
                                                                       class="custom-control-input checkbox-non-kbm" 
                                                                       id="chk_<?= $id_g ?>_<?= $day ?>" 
                                                                       name="non_kbm[<?= $id_g ?>][<?= $day ?>]" 
                                                                       value="1" 
                                                                       <?= $is_non_kbm ? 'checked' : '' ?>>
                                                                <label class="custom-control-label font-weight-normal text-muted" for="chk_<?= $id_g ?>_<?= $day ?>" style="font-size: 0.72rem; cursor: pointer;">
                                                                    Ngantor
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <?php endforeach; ?>

                                                <td class="text-center align-middle font-weight-bold">
                                                    <span class="badge badge-pill <?= ($total_wajib_hari > 0) ? 'badge-dark' : 'badge-light text-muted' ?> px-2 py-1" style="font-size: 0.75rem;">
                                                        <?= $total_wajib_hari ?> Hari
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted text-sm">
                                    <i class="fas fa-save text-primary mr-1"></i> Klik simpan setelah melakukan perubahan jadwal hari ngantor GTK.
                                </span>
                                <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 8px;">
                                    <i class="fas fa-check-circle mr-1"></i> Simpan Jadwal Hari Ngantor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah Guru Piket -->
<div class="modal fade" id="modalTambahPiket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Penugasan Guru Piket
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>jadwal_piket/save" method="POST">
                <input type="hidden" name="id_ta" value="<?= $id_ta_filter ?>">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Pilih Hari Piket <span class="text-danger">*</span></label>
                        <select name="hari" id="modal_select_hari" class="form-control custom-select" required style="border-radius: 8px;">
                            <option value="">-- Pilih Hari --</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Pilih Guru <span class="text-danger">*</span></label>
                        <select name="id_guru" class="form-control custom-select select2" required style="width: 100%; border-radius: 8px;">
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= $g['id_guru'] ?>">
                                    <?= htmlspecialchars($g['nama']) ?> <?= !empty($g['kode_guru']) ? '('.$g['kode_guru'].')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark text-sm">Keterangan / Peran (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Piket Utama, Koordinator, Piket Pagi" style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer bg-light py-3 px-4">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                        <i class="fas fa-save mr-1"></i> Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModalForDay(day) {
    document.getElementById('modal_select_hari').value = day;
    $('#modalTambahPiket').modal('show');
}

// Update active tab hidden input when switching tabs
$('#piketNonKbmTab a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
    let target = $(e.target).attr("id");
    let tabVal = (target === 'non-kbm-tab') ? 'non_kbm' : 'piket';
    $('#filter_active_tab').val(tabVal);
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>

