<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Kelola Kokurikuler &amp; Projek P5
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold rounded-pill px-3" onclick="showAddModal()">
                    <i class="fas fa-plus mr-1"></i> Tambah Kegiatan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-info shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover" id="tableKokul">
                    <?php
                    // Mapping icon & warna untuk profil lulusan (digunakan tabel & modal)
                    $tbl_profil_icons = [
                        1 => ['icon' => 'fas fa-pray',       'color' => '#6f42c1', 'label' => 'Keimanan'],
                        2 => ['icon' => 'fas fa-flag',       'color' => '#dc3545', 'label' => 'Kewargaan'],
                        3 => ['icon' => 'fas fa-brain',      'color' => '#0dcaf0', 'label' => 'Penalaran Kritis'],
                        4 => ['icon' => 'fas fa-lightbulb',  'color' => '#ffc107', 'label' => 'Kreativitas'],
                        5 => ['icon' => 'fas fa-handshake',  'color' => '#198754', 'label' => 'Kolaborasi'],
                        6 => ['icon' => 'fas fa-user-check', 'color' => '#0d6efd', 'label' => 'Kemandirian'],
                        7 => ['icon' => 'fas fa-heartbeat',  'color' => '#d63384', 'label' => 'Kesehatan'],
                        8 => ['icon' => 'fas fa-comments',   'color' => '#fd7e14', 'label' => 'Komunikasi'],
                    ];
                    ?>
                    <thead class="table-light">
                        <tr>
                            <th width="4%">No</th>
                            <th>Nama Kegiatan</th>
                            <th>Tema</th>
                            <th>Koordinator</th>
                            <th>Jadwal</th>
                            <th>Profil Lulusan</th>
                            <th>Status</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kokul_list)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data kegiatan kokulikuler.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kokul_list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                    <td><?= htmlspecialchars($row['tema'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['nama_pembina'] ?? '-') ?></td>
                                    <td><?= $row['hari'] ? htmlspecialchars($row['hari']) . ' (' . substr($row['jam_mulai'], 0, 5) . '-' . substr($row['jam_selesai'], 0, 5) . ')' : '-' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['selected_profil'])): ?>
                                            <div class="d-flex flex-wrap" style="gap:4px;">
                                            <?php foreach ($row['selected_profil'] as $pid):
                                                $pi = $tbl_profil_icons[$pid] ?? null;
                                                if (!$pi) continue;
                                            ?>
                                                <span style="display:inline-flex;align-items:center;gap:4px;
                                                             padding:2px 8px 2px 6px;border-radius:999px;
                                                             background:<?= $pi['color'] ?>18;
                                                             border:1px solid <?= $pi['color'] ?>55;
                                                             color:<?= $pi['color'] ?>;font-size:0.72rem;
                                                             white-space:nowrap;">
                                                    <i class="<?= $pi['icon'] ?>" style="font-size:0.7rem;"></i>
                                                    <?= htmlspecialchars($pi['label']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $row['status'] == 'Aktif' ? 'success' : 'secondary' ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="<?= BASE_URL ?>kokulikuler/index/<?= $row['id_kokulikuler'] ?>/program" 
                                           class="btn btn-info btn-sm" title="Kelola Program & Penilaian">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" title="Edit" 
                                                onclick='editKokul(<?= json_encode($row) ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= BASE_URL ?>kokulikuler/delete?id=<?= $row['id_kokulikuler'] ?>"
                                            class="btn btn-danger btn-sm" onclick="return confirmDelete(event)" title="Hapus">
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

<!-- MODAL TAMBAH/EDIT -->
<div class="modal fade" id="modalKokul" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>kokulikuler/save" method="post" id="formKokul">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalTitle"><i class="fas fa-book-open mr-2"></i>Tambah Kokurikuler</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kokulikuler" id="id_kokulikuler">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label><i class="fas fa-tag mr-1 text-info"></i> Nama Kegiatan</label>
                                <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" 
                                       placeholder="Contoh: Pramuka, Rohis, PMR..." required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-toggle-on mr-1 text-info"></i> Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Non-Aktif">Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lightbulb mr-1 text-info"></i> Tema Kegiatan (Projek P5)</label>
                        <input type="text" name="tema" id="tema" class="form-control" placeholder="Contoh: Gaya Hidup Berkelanjutan">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-user-tie mr-1 text-info"></i> Koordinator (Guru)</label>
                        <select name="id_guru_pembina" id="id_guru_pembina" class="form-control">
                            <option value="">-- Pilih Koordinator --</option>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= $g['id_guru'] ?>"><?= htmlspecialchars($g['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-day mr-1 text-info"></i> Hari</label>
                                <select name="hari" id="hari" class="form-control">
                                    <option value="">-- Pilih Hari --</option>
                                    <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h): ?>
                                        <option value="<?= $h ?>"><?= $h ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                             <div class="form-group">
                                <label><i class="fas fa-clock mr-1 text-info"></i> Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                             <div class="form-group">
                                <label><i class="fas fa-clock mr-1 text-info"></i> Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="form-group">
                        <label class="d-block mb-3"><i class="fas fa-graduation-cap mr-1 text-info"></i> <strong>Target Profil Lulusan</strong> <small class="text-muted">(Bisa pilih lebih dari satu)</small></label>
                        <div class="row" id="profil-lulusan-grid">
                            <?php 
                            $profil_icons = [
                                1 => ['icon' => 'fas fa-pray',         'color' => '#6f42c1'],
                                2 => ['icon' => 'fas fa-flag',         'color' => '#dc3545'],
                                3 => ['icon' => 'fas fa-brain',        'color' => '#0dcaf0'],
                                4 => ['icon' => 'fas fa-lightbulb',    'color' => '#ffc107'],
                                5 => ['icon' => 'fas fa-handshake',    'color' => '#198754'],
                                6 => ['icon' => 'fas fa-user-check',   'color' => '#0d6efd'],
                                7 => ['icon' => 'fas fa-heartbeat',    'color' => '#d63384'],
                                8 => ['icon' => 'fas fa-comments',     'color' => '#fd7e14'],
                            ];
                            foreach ($profil_master as $p): 
                                $icon_data = $profil_icons[$p['id_profil']] ?? ['icon' => 'fas fa-star', 'color' => '#6c757d'];
                            ?>
                                <div class="col-6 col-md-3 mb-3">
                                    <!-- Gunakan div bukan label agar tidak terjadi double-toggle -->
                                    <div class="profil-card-item" onclick="toggleProfil(<?= $p['id_profil'] ?>)" 
                                         id="card_profil_<?= $p['id_profil'] ?>"
                                         style="cursor:pointer;">
                                        <input class="chk-profil d-none" type="checkbox" 
                                               name="id_profil[]" id="profil_<?= $p['id_profil'] ?>" 
                                               value="<?= $p['id_profil'] ?>">
                                        <div class="profil-card-inner border rounded text-center p-2 position-relative" 
                                             style="transition: all 0.2s ease; min-height: 105px; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8f9fa;">
                                            <div class="profil-check-badge d-none" style="position:absolute;top:5px;right:5px;color:#0d6efd;font-size:0.8rem;">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="profil-icon mb-1" style="color: <?= $icon_data['color'] ?>; font-size: 1.8rem;">
                                                <i class="<?= $icon_data['icon'] ?>"></i>
                                            </div>
                                            <div class="profil-name fw-bold" style="font-size: 0.72rem; line-height: 1.3;"><?= htmlspecialchars($p['nama_dimensi']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.profil-card-item .profil-card-inner {
    cursor: pointer;
    user-select: none;
}
.profil-card-item:hover .profil-card-inner {
    border-color: #6ea8fe !important;
    background: #f0f4ff !important;
}
.profil-card-item.is-selected .profil-card-inner {
    border-color: #0d6efd !important;
    background: #dce8ff !important;
    box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.3);
}
.profil-card-item.is-selected .profil-check-badge {
    display: block !important;
}
.profil-card-item.is-selected .profil-name {
    color: #0d6efd;
}
</style>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#tableKokul').DataTable({
             "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

    function showAddModal() {
        $('#formKokul')[0].reset();
        $('#id_kokulikuler').val('');
        resetProfilCards();
        $('#modalTitle').html('<i class="fas fa-plus-circle mr-2"></i>Tambah Kokurikuler');
        $('#modalKokul').modal('show');
    }

    function editKokul(data) {
        $('#formKokul')[0].reset();
        $('#id_kokulikuler').val(data.id_kokulikuler);
        $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Kokurikuler');

        $('#nama_kegiatan').val(data.nama_kegiatan);
        $('#tema').val(data.tema);
        $('#id_guru_pembina').val(data.id_guru_pembina);
        $('#hari').val(data.hari);
        $('#jam_mulai').val(data.jam_mulai);
        $('#jam_selesai').val(data.jam_selesai);
        $('#status').val(data.status);

        // Reset dulu semua profil
        resetProfilCards();

        // Set Profil Lulusan yang dipilih
        if (data.selected_profil && data.selected_profil.length > 0) {
            data.selected_profil.forEach(function(id) {
                var $card = $('#card_profil_' + id);
                $card.find('.chk-profil').prop('checked', true);
                $card.addClass('is-selected');
            });
        }

        $('#modalKokul').modal('show');
    }

    /**
     * Toggle satu profil card. Dipanggil via onclick="toggleProfil(id)"
     * Menggunakan div (bukan label) sehingga tidak ada double-toggle.
     */
    function toggleProfil(id) {
        var $card   = $('#card_profil_' + id);
        var $chk    = $card.find('.chk-profil');
        var checked = $chk.is(':checked');

        // Toggle state
        $chk.prop('checked', !checked);
        $card.toggleClass('is-selected', !checked);
    }

    /** Reset semua profil card ke unchecked */
    function resetProfilCards() {
        $('.chk-profil').prop('checked', false);
        $('.profil-card-item').removeClass('is-selected');
    }
</script>
