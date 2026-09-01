<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .bank-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);
        flex-shrink: 0;
    }
    .bank-filter-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        padding: 12px 18px;
        margin-bottom: 20px;
    }
    .bank-card-master {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }
    .bank-card-master:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08) !important;
        border-color: #cbd5e1;
    }
    .bank-card-header {
        padding: 14px 16px 10px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
    }
    .bank-card-body {
        padding: 12px 16px;
        flex-grow: 1;
    }
    .bank-card-footer {
        padding: 10px 16px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        border-bottom-left-radius: 14px;
        border-bottom-right-radius: 14px;
    }
    .mini-grade-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        margin-top: 6px;
        margin-bottom: 6px;
    }
    .mini-grade-pill {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        padding: 6px 4px;
        text-align: center;
        text-decoration: none !important;
        color: #334155;
        font-size: 0.76rem;
        transition: all 0.15s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .mini-grade-pill:hover {
        border-color: #4f46e5;
        background: #eef2ff;
        color: #4f46e5;
    }
    .mini-grade-pill.has-soal {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }
    .mini-grade-pill.has-soal:hover {
        background: #dcfce7;
        border-color: #86efac;
    }
    .segmented-tab-nav {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
    }
    .segmented-tab-nav a {
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.84rem;
        color: #64748b;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .segmented-tab-nav a:hover {
        color: #1e293b;
    }
    .segmented-tab-nav a.active {
        background: #ffffff;
        color: #4f46e5;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    .search-mapel-input,
    .custom-filter-select,
    .bank-filter-card select,
    .bank-filter-card input,
    .bank-card-master,
    .btn {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    .custom-filter-select {
        height: 38px;
        border-radius: 50px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 0.86rem;
        font-weight: 500;
        color: #1e293b;
        padding: 0 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .custom-filter-select:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }
    .search-mapel-box {
        position: relative;
    }
    .search-mapel-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.88rem;
    }
    .search-mapel-input {
        padding-left: 36px;
        padding-right: 14px;
        height: 38px;
        border-radius: 50px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 0.86rem;
        font-weight: 500;
        color: #1e293b;
        transition: all 0.2s ease;
    }
    .search-mapel-input:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
</style>

<div class="content-header p-0 pt-3 mb-2">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4 flex-wrap" style="gap: 12px;">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #4f46e5, #3730a3); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h2 class="m-0 font-weight-bold text-dark" style="font-size: 1.65rem; letter-spacing: -0.5px;">
                        Master Bank Soal
                    </h2>
                    <p class="text-muted small mb-0 mt-0.5 font-weight-500">Kelola wadah naskah dan butir soal ujian per mata pelajaran.</p>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary btn-sm font-weight-bold rounded-pill px-4 shadow-sm" data-toggle="modal" data-target="#modalTambahBank">
                    <i class="fas fa-plus-circle mr-1"></i> Buat Bank Soal Baru
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- UNIFIED TOOLBAR & FILTER BAR -->
        <div class="bank-filter-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start" style="gap: 12px;">
                <!-- TAB NAVIGASI HAK AKSES (SEGMENTED PILLS) -->
                <div class="segmented-tab-nav">
                    <?php if (!$info['is_admin']): ?>
                        <a class="<?= ($active_tab === 'my_mapel') ? 'active' : '' ?>" href="index.php?mod=cbt_bank_soal&tab=my_mapel">
                            <i class="fas fa-chalkboard-teacher mr-1" style="margin-right: 6px;"></i> Mapel Saya
                        </a>
                    <?php endif; ?>
                    <a class="<?= ($active_tab === 'all_mapel') ? 'active' : '' ?>" href="index.php?mod=cbt_bank_soal&tab=all_mapel">
                        <i class="fas fa-globe mr-1" style="margin-right: 6px;"></i> <?= $info['is_admin'] ? 'Semua Bank Soal' : 'Seluruh Mapel Sekolah' ?>
                    </a>
                </div>

                <!-- SEARCH & MAPEL FILTER -->
                <div class="d-flex align-items-center flex-wrap flex-grow-1 justify-content-md-end" style="gap: 10px; max-width: 600px;">
                    <!-- INSTANT SEARCH BOX -->
                    <div class="search-mapel-box flex-grow-1" style="min-width: 220px;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="filter_search_mapel" class="form-control search-mapel-input" placeholder="Ketik nama mata pelajaran..." oninput="instantFilterBankCards(this.value)">
                    </div>

                    <!-- DROPDOWN FILTER -->
                    <form method="GET" class="m-0" id="formMapelSelect">
                        <input type="hidden" name="mod" value="cbt_bank_soal">
                        <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                        <select name="id_mapel" class="form-control custom-filter-select" onchange="this.form.submit()">
                            <option value="">Semua Mata Pelajaran (<?= count($all_mapel_list) ?>)</option>
                            <?php foreach ($mapel_list as $m): ?>
                                <option value="<?= $m['id_mapel'] ?>" <?= ($filter_mapel == $m['id_mapel']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nama_mapel']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>

                    <!-- BADGE COUNT -->
                    <span class="badge badge-light border text-muted px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.82rem; white-space: nowrap;">
                        <strong id="visible_bank_count" class="text-primary"><?= count($bank_list) ?></strong> Mapel
                    </span>
                </div>
            </div>
        </div>

        <!-- DAFTAR MASTER BANK SOAL COMPACT GRID -->
        <?php if (empty($bank_list)): ?>
            <div class="card p-5 text-center shadow-sm border-0" style="border-radius: 16px; background: #ffffff;">
                <i class="fas fa-folder-open fa-4x text-muted mb-3 opacity-50"></i>
                <h5 class="font-weight-bold text-dark">Belum Ada Bank Soal</h5>
                <p class="text-muted small mb-3">Klik tombol di bawah untuk membuat bank soal mata pelajaran pertama Anda.</p>
                <div>
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold rounded-pill px-4" data-toggle="modal" data-target="#modalTambahBank">
                        <i class="fas fa-plus-circle mr-1"></i> Buat Bank Soal Sekarang
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="row" id="bankGridContainer">
                <?php foreach ($bank_list as $b): ?>
                    <?php 
                        $is_my_bank = $info['is_admin'] || in_array((int)$b['id_mapel'], $info['mapel_ids']) || ($b['id_user'] == $info['user_id']);
                        $count_x   = (int)($b['jml_kelas_x'] ?? 0);
                        $count_xi  = (int)($b['jml_kelas_xi'] ?? 0);
                        $count_xii = (int)($b['jml_kelas_xii'] ?? 0);
                        $total_soal = (int)($b['total_soal'] ?? 0);
                        $search_keyword = strtolower(trim(($b['nama_mapel'] ?? '') . ' ' . ($b['nama_bank'] ?? '')));
                    ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 bank-card-col" data-search="<?= htmlspecialchars($search_keyword) ?>">
                        <div class="bank-card-master">
                            <!-- HEADER -->
                            <div class="bank-card-header d-flex justify-content-between align-items-center">
                                <span class="badge text-white font-weight-bold px-2 py-0.5 rounded text-truncate" style="background: #4f46e5; font-size: 0.72rem; max-width: 70%;">
                                    <i class="fas fa-book mr-1"></i> <?= htmlspecialchars($b['nama_mapel'] ?? 'Mapel') ?>
                                </span>
                                <div>
                                    <?php if ($is_my_bank): ?>
                                        <span class="badge badge-success px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.68rem;">
                                            <i class="fas fa-user-check mr-1"></i> Pengampu
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-light border text-muted px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                            <i class="fas fa-eye mr-1"></i> Jelajah
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- BODY -->
                            <div class="bank-card-body">
                                <h6 class="font-weight-bold text-dark mb-2 text-truncate" title="<?= htmlspecialchars($b['nama_bank']) ?>" style="font-size: 0.95rem;">
                                    <?= htmlspecialchars($b['nama_bank']) ?>
                                </h6>

                                <div class="mini-grade-grid">
                                    <!-- KELAS X -->
                                    <a href="index.php?mod=cbt_bank_soal&act=detail&id_bank=<?= $b['id_bank'] ?>&tingkat=X" class="mini-grade-pill <?= $count_x > 0 ? 'has-soal' : '' ?>" title="Buka Soal Kelas X (Fase E)">
                                        <strong class="font-weight-bold">Kelas X</strong>
                                        <span style="font-size: 0.72rem; font-weight: 600;"><?= $count_x ?> Soal</span>
                                    </a>

                                    <!-- KELAS XI -->
                                    <a href="index.php?mod=cbt_bank_soal&act=detail&id_bank=<?= $b['id_bank'] ?>&tingkat=XI" class="mini-grade-pill <?= $count_xi > 0 ? 'has-soal' : '' ?>" title="Buka Soal Kelas XI (Fase F)">
                                        <strong class="font-weight-bold">Kelas XI</strong>
                                        <span style="font-size: 0.72rem; font-weight: 600;"><?= $count_xi ?> Soal</span>
                                    </a>

                                    <!-- KELAS XII -->
                                    <a href="index.php?mod=cbt_bank_soal&act=detail&id_bank=<?= $b['id_bank'] ?>&tingkat=XII" class="mini-grade-pill <?= $count_xii > 0 ? 'has-soal' : '' ?>" title="Buka Soal Kelas XII (Fase F)">
                                        <strong class="font-weight-bold">Kelas XII</strong>
                                        <span style="font-size: 0.72rem; font-weight: 600;"><?= $count_xii ?> Soal</span>
                                    </a>
                                </div>
                            </div>

                            <!-- FOOTER -->
                            <div class="bank-card-footer d-flex justify-content-between align-items-center">
                                <span class="small font-weight-bold text-muted">
                                    Total: <strong class="<?= $total_soal > 0 ? 'text-primary' : 'text-secondary' ?>"><?= $total_soal ?></strong>
                                </span>

                                <div class="d-flex align-items-center" style="gap: 4px;">
                                    <a href="index.php?mod=cbt_bank_soal&act=detail&id_bank=<?= $b['id_bank'] ?>" class="btn btn-xs btn-primary rounded-pill px-2.5 font-weight-bold shadow-sm" title="Buka Semua Butir Soal">
                                        <i class="fas fa-folder-open mr-1"></i> Buka
                                    </a>

                                    <a href="index.php?mod=cbt_bank_soal&act=preview_siswa&id_bank=<?= $b['id_bank'] ?>" target="_blank" class="btn btn-xs btn-outline-info rounded-pill px-2 font-weight-bold shadow-sm" title="Simulasi CBT Siswa">
                                        <i class="fas fa-desktop"></i>
                                    </a>

                                    <?php if ($is_my_bank): ?>
                                        <a href="index.php?mod=cbt_bank_soal&act=delete_bank&id_bank=<?= $b['id_bank'] ?>" 
                                           class="btn btn-xs btn-outline-danger rounded-pill px-2 font-weight-bold"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus <?= htmlspecialchars($b['nama_bank']) ?> beserta butir soalnya?')"
                                           title="Hapus Bank Soal">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- MODAL TAMBAH BANK SOAL BARU -->
<div class="modal fade" id="modalTambahBank" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <form method="POST" action="index.php?mod=cbt_bank_soal&act=store_bank">
                <input type="hidden" name="id_bank" value="0">
                <div class="modal-header bg-primary text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-database mr-2"></i> Buat Bank Soal Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">Pilih Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="id_mapel" id="select_new_mapel" class="form-control rounded-pill" onchange="autoFillNamaBank(this)" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <?php foreach ($all_mapel_list as $m): ?>
                                <option value="<?= $m['id_mapel'] ?>" data-nama="<?= htmlspecialchars($m['nama_mapel']) ?>"><?= htmlspecialchars($m['nama_mapel']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">Nama Bank Soal <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bank" id="input_nama_bank" class="form-control rounded-pill" placeholder="Contoh: Bank Soal Matematika" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">Deskripsi / Catatan (Opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Catatan informasi bank soal..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold rounded-pill px-4">Simpan Bank Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function autoFillNamaBank(select) {
    const opt = select.options[select.selectedIndex];
    const nama = opt ? opt.getAttribute('data-nama') : '';
    if (nama) {
        $('#input_nama_bank').val('Bank Soal ' + nama);
    }
}

function instantFilterBankCards(kw) {
    kw = (kw || '').toLowerCase().trim();
    let visible = 0;
    $('.bank-card-col').each(function() {
        const text = $(this).data('search') || '';
        if (!kw || text.includes(kw)) {
            $(this).show();
            visible++;
        } else {
            $(this).hide();
        }
    });
    $('#visible_bank_count').text(visible);
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
