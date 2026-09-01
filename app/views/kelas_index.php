<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-school"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Data Master Kelas (Rombel)
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <div class="d-inline-flex flex-wrap justify-content-end" style="gap: 8px;">
                    <a href="<?= BASE_URL ?>kelas/form"
                        class="btn btn-warning btn-sm px-3 shadow-sm font-weight-bold text-white rounded-pill">
                        <i class="fas fa-plus mr-1"></i> Tambah Kelas
                    </a>
                    <?php if (!empty($previous_ta)): ?>
                        <?php if ($can_import_previous): ?>
                            <a href="<?= BASE_URL ?>kelas/import_previous"
                                class="btn btn-info btn-sm px-3 shadow-sm font-weight-bold text-white rounded-pill"
                                onclick="return confirm('Tarik daftar kelas dari <?= htmlspecialchars(addslashes($previous_ta['nama_ta'])) ?> ke TA saat ini?');">
                                <i class="fas fa-download mr-1"></i> Tarik dari <?= htmlspecialchars($previous_ta['nama_ta']) ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php
            $grouped_kelas = [];
            $levels = ['X', 'XI', 'XII'];
            $accent_colors = ['X' => '#3b82f6', 'XI' => '#10b981', 'XII' => '#f59e0b'];
            $bg_colors = ['X' => '#eff6ff', 'XI' => '#f0fdf4', 'XII' => '#fffbeb'];

            foreach ($kelas_list as $k) {
                $grouped_kelas[$k['tingkat']][] = $k;
            }

            foreach ($levels as $tingkat):
                $data_kelas = $grouped_kelas[$tingkat] ?? [];
                $color = $accent_colors[$tingkat];
                $bg = $bg_colors[$tingkat];
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0"
                        style="border-radius: 15px; border-top: 4px solid <?= $color ?> !important;">
                        <div class="card-header border-bottom py-3" style="background: <?= $bg ?>; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                            <h6 class="mb-0 font-weight-bold" style="color: <?= $color ?>; letter-spacing: 1px;">
                                TINGKAT <?= htmlspecialchars($tingkat) ?>
                            </h6>
                        </div>
                        <div class="card-body p-0" style="overflow: visible;">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="bg-light">
                                    <tr class="text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                        <th class="text-center py-2" style="width: 50px;">NO</th>
                                        <th class="py-2">NAMA KELAS</th>
                                        <th class="text-center py-2" style="width: 90px;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($data_kelas)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted small"><em>Belum ada data.</em>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                            $no = 1; 
                                            $total_rows = count($data_kelas);
                                        ?>
                                        <?php foreach ($data_kelas as $idx => $k): ?>
                                            <?php 
                                                $jk = $k['jenis_kelas'] ?? 'reguler'; 
                                                // Jika baris berada di bagian bawah (2 baris terakhir), gunakan dropup agar membuka ke atas
                                                $is_bottom_row = ($total_rows > 2 && ($total_rows - $idx) <= 2);
                                            ?>
                                            <tr>
                                                <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++; ?>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 4px;">
                                                        <span class="font-weight-bold text-dark" style="font-size: 0.85rem;">
                                                            <?= htmlspecialchars($k['nama_kelas']) ?>
                                                        </span>
                                                        
                                                        <!-- Dynamic Status Badge Dropdown (Auto Dropup on bottom rows) -->
                                                        <div class="btn-group <?= $is_bottom_row ? 'dropup' : 'dropdown' ?> d-inline-block">
                                                            <button class="btn btn-xs dropdown-toggle font-weight-bold border-0 px-2 py-0.5 rounded-pill shadow-xs" 
                                                                    type="button" data-toggle="dropdown" data-boundary="window"
                                                                    id="badge-jk-<?= $k['id_kelas'] ?>" 
                                                                    style="font-size: 0.65rem; <?= $jk === 'pjj' ? 'background:#dcfce7; color:#166534;' : ($jk === 'menginduk' ? 'background:#fef3c7; color:#92400e;' : 'background:#e0e7ff; color:#4338ca;') ?>">
                                                                <?= $jk === 'pjj' ? '🌐 PJJ / Terbuka' : ($jk === 'menginduk' ? '🤝 Menginduk' : '🏫 Reguler') ?>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right shadow border-0 py-1" style="font-size: 0.75rem; border-radius: 8px; z-index: 1060;">
                                                                <a class="dropdown-item py-1 <?= $jk==='reguler'?'active font-weight-bold':'' ?>" href="javascript:void(0)" onclick="setJenisKelas(<?= $k['id_kelas'] ?>, 'reguler')">🏫 Reguler (5 Hari)</a>
                                                                <a class="dropdown-item py-1 <?= $jk==='pjj'?'active font-weight-bold':'' ?>" href="javascript:void(0)" onclick="setJenisKelas(<?= $k['id_kelas'] ?>, 'pjj')">🌐 PJJ / Terbuka (Hybrid)</a>
                                                                <a class="dropdown-item py-1 <?= $jk==='menginduk'?'active font-weight-bold':'' ?>" href="javascript:void(0)" onclick="setJenisKelas(<?= $k['id_kelas'] ?>, 'menginduk')">🤝 Sekolah Menginduk (6 Hari)</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group">
                                                        <a href="<?= BASE_URL ?>kelas/form?id=<?= $k['id_kelas'] ?>"
                                                            class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                                                            style="background: #fffbeb; width: 26px; height: 26px; border-radius: 6px; color: #d97706;"
                                                            title="Edit"><i class="fas fa-pencil-alt"
                                                                style="font-size: 0.75rem;"></i></a>
                                                        <a href="<?= BASE_URL ?>kelas/delete?id=<?= $k['id_kelas'] ?>"
                                                            class="btn btn-xs btn-outline-danger border-0 p-1"
                                                            style="background: #fef2f2; width: 26px; height: 26px; border-radius: 6px; color: #dc2626;"
                                                            title="Hapus"
                                                            onclick="return confirmDelete(event, 'Hapus kelas ini?')"><i
                                                                class="fas fa-trash-alt" style="font-size: 0.75rem;"></i></a>
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
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
function setJenisKelas(idKelas, jenis) {
    $.ajax({
        url: '<?= BASE_URL ?>index.php?mod=kelas&act=toggle_jenis',
        type: 'POST',
        data: { id_kelas: idKelas, jenis_kelas: jenis },
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(res) {
            if (res.status === 'success') {
                const btn = $('#badge-jk-' + idKelas);
                if (jenis === 'pjj') {
                    btn.attr('style', 'font-size: 0.65rem; background:#dcfce7; color:#166534;').html('🌐 PJJ / Terbuka');
                } else if (jenis === 'menginduk') {
                    btn.attr('style', 'font-size: 0.65rem; background:#fef3c7; color:#92400e;').html('🤝 Menginduk');
                } else {
                    btn.attr('style', 'font-size: 0.65rem; background:#e0e7ff; color:#4338ca;').html('🏫 Reguler');
                }
            } else {
                alert(res.message || 'Gagal memperbarui jenis kelas.');
            }
        },
        error: function() {
            window.location.reload();
        }
    });
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>