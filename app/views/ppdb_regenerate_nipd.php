<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .nipd-card {
        border-radius: 14px;
        border: none;
        box-shadow: 0 4px 18px rgba(79,70,229,0.09);
        transition: box-shadow 0.2s;
    }
    .nipd-card:hover { box-shadow: 0 8px 28px rgba(79,70,229,0.16); }

    .nipd-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-radius: 14px 14px 0 0;
        padding: 22px 28px;
    }
    .nipd-header h4 { font-weight: 700; margin: 0; }
    .nipd-header p  { margin: 0; opacity: 0.85; font-size: 0.93rem; }

    .badge-tingkat-10 { background: #dbeafe; color: #1e40af; font-size: 0.75rem; font-weight: 600; }
    .badge-tingkat-11 { background: #d1fae5; color: #065f46; font-size: 0.75rem; font-weight: 600; }
    .badge-tingkat-12 { background: #fef3c7; color: #92400e; font-size: 0.75rem; font-weight: 600; }

    .nipd-old { color: #94a3b8; font-size: 0.85rem; text-decoration: line-through; }
    .nipd-new { color: #059669; font-weight: 700; font-size: 0.95rem; }
    .nipd-same { color: #64748b; font-size: 0.9rem; }
    .row-changed { background: #f0fdf4 !important; }
    .row-same    { background: #fff; }

    .stat-box {
        border-radius: 12px;
        padding: 18px 22px;
        text-align: center;
        font-weight: 600;
    }
    .stat-box .stat-num { font-size: 2rem; font-weight: 800; line-height: 1.1; }
    .stat-box .stat-lbl { font-size: 0.8rem; text-transform: uppercase; letter-spacing: .5px; opacity: .8; margin-top: 4px; }

    .preview-table { font-size: 0.85rem; }
    .preview-table thead th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.76rem;
        letter-spacing: .4px;
        padding: 10px 12px;
        border-bottom: 2px solid #e2e8f0;
    }
    .preview-table tbody td { padding: 8px 12px; vertical-align: middle; }

    .arrow-icon { color: #94a3b8; margin: 0 5px; }
    
    .confirm-box {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 2px solid #f59e0b;
        border-radius: 12px;
        padding: 20px 24px;
    }

    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #ede9fe;
        color: #5b21b6;
        border-radius: 30px;
        padding: 5px 14px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .search-filter { border-radius: 8px; border: 1.5px solid #e2e8f0; }
    .search-filter:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.12); outline: none; }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-sync-alt mr-2 text-indigo"></i> Re-Generate NIPD Massal</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>ppdb">PPDB</a></li>
                    <li class="breadcrumb-item active">Re-Generate NIPD</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <?php if (isset($_SESSION['pesan_sukses'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($_SESSION['pesan_sukses']); unset($_SESSION['pesan_sukses']); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['pesan_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($_SESSION['pesan_error']); unset($_SESSION['pesan_error']); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php endif; ?>

        <!-- INFO BOX -->
        <div class="alert alert-info border-0 shadow-sm rounded-lg mb-4">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle fa-2x mr-3 mt-1 text-info"></i>
                <div>
                    <h6 class="font-weight-bold mb-1">Cara Kerja Re-Generate NIPD</h6>
                    <ul class="mb-0 pl-4 small">
                        <li>Pilih <strong>Tahun Ajaran Masuk</strong> yang ingin di-regenerate NIPD-nya</li>
                        <li>Sistem akan menampilkan <strong>preview perubahan NIPD</strong> sebelum disimpan</li>
                        <li>NIPD di-generate berdasarkan urutan <strong>alfabet nama siswa</strong></li>
                        <li>Tingkat <strong>X → XI → XII</strong> diurutkan berurutan, nomor urut <em>dilanjutkan</em> antar tingkat</li>
                        <li>Siswa dari TA lain <strong>tidak terpengaruh</strong></li>
                        <li>Username login (NISN) <strong>tidak berubah</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FORM PILIH TA -->
        <div class="card nipd-card mb-4">
            <div class="nipd-header">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-alt fa-2x mr-3" style="opacity:.7"></i>
                    <div>
                        <h4>Pilih Tahun Ajaran</h4>
                        <p>NIPD akan di-generate ulang hanya untuk siswa yang masuk pada TA yang dipilih</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="<?= BASE_URL ?>ppdb/regenerate_nipd" class="form-row align-items-end">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="font-weight-bold text-muted small mb-1">TAHUN AJARAN MASUK SISWA</label>
                        <select name="id_ta" id="id_ta" class="form-control custom-select" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php foreach ($list_ta as $ta): ?>
                            <option value="<?= $ta['id_ta'] ?>" 
                                    <?= ($id_ta_dipilih == $ta['id_ta']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ta['nama_ta']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                            <i class="fas fa-eye mr-2"></i> Tampilkan Preview
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>ppdb" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke PPDB
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($error_preview): ?>
        <div class="alert alert-warning shadow-sm">
            <i class="fas fa-exclamation-triangle mr-2"></i> <?= htmlspecialchars($error_preview) ?>
        </div>
        <?php endif; ?>

        <?php if ($preview_data): 
            $jumlah_berubah = count(array_filter($preview_data['preview'], fn($p) => $p['berubah']));
            $jumlah_sama    = count($preview_data['preview']) - $jumlah_berubah;
            $by_tingkat = [];
            foreach ($preview_data['preview'] as $p) {
                $by_tingkat[$p['tingkat']] = ($by_tingkat[$p['tingkat']] ?? 0) + 1;
            }
        ?>

        <!-- STAT BOXES -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-box shadow-sm" style="background:#ede9fe;color:#5b21b6;">
                    <div class="stat-num"><?= $preview_data['jumlah'] ?></div>
                    <div class="stat-lbl">Total Siswa</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-box shadow-sm" style="background:#d1fae5;color:#065f46;">
                    <div class="stat-num"><?= $jumlah_berubah ?></div>
                    <div class="stat-lbl">NIPD Berubah</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-box shadow-sm" style="background:#f1f5f9;color:#475569;">
                    <div class="stat-num"><?= $jumlah_sama ?></div>
                    <div class="stat-lbl">NIPD Tetap Sama</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-box shadow-sm" style="background:#fef3c7;color:#92400e;">
                    <div class="stat-num"><?= count($by_tingkat) ?></div>
                    <div class="stat-lbl">Tingkat (X/XI/XII)</div>
                </div>
            </div>
        </div>

        <!-- RINGKASAN PER TINGKAT -->
        <div class="row mb-4">
            <?php foreach ($by_tingkat as $tingkat => $cnt): 
                $label = ($tingkat == 10) ? 'Kelas X' : (($tingkat == 11) ? 'Kelas XI' : 'Kelas XII');
                $icon  = ($tingkat == 10) ? 'fas fa-user-graduate' : (($tingkat == 11) ? 'fas fa-users' : 'fas fa-user-tie');
                $colors = [
                    10 => ['bg' => '#dbeafe', 'clr' => '#1e40af'],
                    11 => ['bg' => '#d1fae5', 'clr' => '#065f46'],
                    12 => ['bg' => '#fef3c7', 'clr' => '#92400e'],
                ];
                $c = $colors[$tingkat] ?? ['bg' => '#f1f5f9', 'clr' => '#475569'];
            ?>
            <div class="col-md-4 mb-3">
                <div class="d-flex align-items-center p-3 rounded-lg shadow-sm" 
                     style="background:<?= $c['bg'] ?>;color:<?= $c['clr'] ?>">
                    <i class="<?= $icon ?> fa-2x mr-3" style="opacity:.7"></i>
                    <div>
                        <div style="font-size:1.4rem;font-weight:800;"><?= $cnt ?> Siswa</div>
                        <div style="font-size:0.82rem;font-weight:600;opacity:.8;"><?= $label ?> (Tingkat <?= $tingkat ?>)</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CONFIRM BOX -->
        <?php if ($jumlah_berubah > 0 && !isset($_GET['done'])): ?>
        <div class="confirm-box mb-4">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-exclamation-triangle fa-lg mr-2 text-warning"></i>
                <strong>Konfirmasi Eksekusi</strong>
            </div>
            <p class="mb-3 small">
                Sebanyak <strong><?= $jumlah_berubah ?> NIPD akan diubah</strong> untuk TA <strong><?= htmlspecialchars($preview_data['nama_ta']) ?></strong>.
                Preview di bawah menunjukkan perubahan secara detail. Pastikan sudah benar sebelum mengeksekusi.
            </p>
            <form method="POST" action="<?= BASE_URL ?>ppdb/regenerate_nipd_exec" 
                  onsubmit="return confirm('⚠️ PERHATIAN!\n\nAnda akan men-update NIPD <?= $preview_data['jumlah'] ?> siswa TA <?= htmlspecialchars($preview_data['nama_ta']) ?>.\n\nData lain (nama, NISN, username login) TIDAK berubah.\n\nLanjutkan?')">
                <input type="hidden" name="id_ta" value="<?= $id_ta_dipilih ?>">
                <div class="d-flex gap-3 flex-wrap">
                    <button type="submit" class="btn btn-warning text-dark shadow mr-2 px-4">
                        <i class="fas fa-play-circle mr-2"></i> <strong>Eksekusi Re-Generate NIPD</strong>
                        <span class="badge badge-dark ml-1"><?= $preview_data['jumlah'] ?> siswa</span>
                    </button>
                    <a href="<?= BASE_URL ?>ppdb" class="btn btn-outline-secondary">
                        <i class="fas fa-times mr-1"></i> Batalkan
                    </a>
                </div>
            </form>
        </div>
        <?php elseif (isset($_GET['done'])): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle mr-2"></i> <strong>Re-Generate selesai!</strong> 
            NIPD <?= $preview_data['jumlah'] ?> siswa berhasil diperbarui. Tabel di bawah menampilkan hasil akhir.
        </div>
        <?php else: ?>
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <i class="fas fa-info-circle mr-2"></i> Semua NIPD sudah sesuai — tidak ada perubahan yang diperlukan.
        </div>
        <?php endif; ?>

        <!-- PREVIEW TABLE -->
        <div class="card nipd-card">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4">
                <div>
                    <h5 class="font-weight-bold mb-0">
                        <i class="fas fa-list-alt mr-2 text-indigo"></i>
                        Preview Perubahan NIPD — TA: <?= htmlspecialchars($preview_data['nama_ta']) ?>
                    </h5>
                    <small class="text-muted">Baris hijau = NIPD berubah | Baris putih = NIPD tetap</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="searchPreview" class="form-control search-filter" 
                           placeholder="Cari nama siswa..." style="width:220px;">
                    <select id="filterTingkat" class="form-control search-filter ml-2" style="width:130px;">
                        <option value="">Semua Tingkat</option>
                        <option value="10">Kelas X</option>
                        <option value="11">Kelas XI</option>
                        <option value="12">Kelas XII</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table preview-table mb-0" id="previewTable">
                        <thead>
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Nama Siswa</th>
                                <th style="width:90px">Kelas Masuk</th>
                                <th style="width:80px">Tingkat</th>
                                <th>NIPD Lama</th>
                                <th></th>
                                <th>NIPD Baru</th>
                                <th style="width:90px">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach ($preview_data['preview'] as $p): $no++; ?>
                            <tr class="<?= $p['berubah'] ? 'row-changed' : 'row-same' ?>"
                                data-tingkat="<?= $p['tingkat'] ?>"
                                data-nama="<?= strtolower(htmlspecialchars($p['nama'])) ?>">
                                <td class="text-muted"><?= $no ?></td>
                                <td><strong><?= htmlspecialchars($p['nama']) ?></strong></td>
                                <td>
                                    <span class="badge badge-secondary"><?= htmlspecialchars($p['nama_kelas'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $t = $p['tingkat'];
                                    $badge_class = "badge-tingkat-$t";
                                    $label_t = ($t == 10) ? 'Kelas X' : (($t == 11) ? 'Kelas XI' : 'Kelas XII');
                                    ?>
                                    <span class="badge <?= $badge_class ?> px-2 py-1"><?= $label_t ?></span>
                                </td>
                                <td>
                                    <?php if ($p['berubah']): ?>
                                    <span class="nipd-old"><?= htmlspecialchars($p['nipd_lama'] ?? '-') ?></span>
                                    <?php else: ?>
                                    <span class="nipd-same"><?= htmlspecialchars($p['nipd_lama'] ?? '-') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['berubah']): ?>
                                    <i class="fas fa-long-arrow-alt-right arrow-icon text-success"></i>
                                    <?php else: ?>
                                    <i class="fas fa-equals arrow-icon"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['berubah']): ?>
                                    <span class="nipd-new"><?= htmlspecialchars($p['nipd_baru']) ?></span>
                                    <?php else: ?>
                                    <span class="nipd-same"><?= htmlspecialchars($p['nipd_baru']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['berubah']): ?>
                                    <span class="badge badge-success">Berubah</span>
                                    <?php else: ?>
                                    <span class="badge badge-light text-muted">Tetap</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-4">
                <small class="text-muted">
                    Total: <strong><?= $preview_data['jumlah'] ?></strong> siswa | 
                    Berubah: <strong class="text-success"><?= $jumlah_berubah ?></strong> | 
                    Tetap: <strong class="text-muted"><?= $jumlah_sama ?></strong>
                </small>
                <small class="text-muted">TA: <?= htmlspecialchars($preview_data['nama_ta']) ?></small>
            </div>
        </div>

        <?php endif; // end if preview_data ?>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
$(document).ready(function() {
    // Filter preview table
    function filterTable() {
        var cari = $('#searchPreview').val().toLowerCase().trim();
        var tingkat = $('#filterTingkat').val();
        
        $('#previewTable tbody tr').each(function() {
            var nama = $(this).data('nama') || '';
            var t = String($(this).data('tingkat') || '');
            
            var matchNama = !cari || nama.includes(cari);
            var matchTingkat = !tingkat || t === tingkat;
            
            $(this).toggle(matchNama && matchTingkat);
        });
    }
    
    $('#searchPreview').on('input', filterTable);
    $('#filterTingkat').on('change', filterTable);
    
    // Auto pilih TA jika hanya ada 1 (tidak terpakai tapi defensif)
    if ($('#id_ta option').length === 2) {
        $('#id_ta option:last').prop('selected', true);
    }
});
</script>
