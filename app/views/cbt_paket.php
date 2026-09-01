<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .paket-icon-box {
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
    .custom-form-input,
    .custom-form-select {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 0.86rem;
        font-weight: 500;
        color: #1e293b;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        height: 38px;
        transition: all 0.2s ease;
    }
    .custom-form-input:focus,
    .custom-form-select:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }
    .form-card-rakit {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .table-paket-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .btn-gradient-indigo {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff !important;
        border: none;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }
    .btn-gradient-indigo:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }
    .table-paket-header th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px;
        border-top: none !important;
        border-bottom: 1.5px solid #e2e8f0 !important;
        padding: 12px 16px !important;
    }
    .btn-soft-primary {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        font-weight: 700;
    }
    .btn-soft-primary:hover {
        background: #2563eb;
        color: #ffffff;
    }
    .btn-soft-info {
        background: #f0fdfa;
        color: #0d9488;
        border: 1.5px solid #99f6e4;
        font-weight: 700;
    }
    .btn-soft-info:hover {
        background: #0d9488;
        color: #ffffff;
    }
    .btn-soft-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        font-weight: 700;
    }
    .btn-soft-danger:hover {
        background: #dc2626;
        color: #ffffff;
    }
</style>

<div class="content-header p-0 pt-3 mb-2">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4 flex-wrap" style="gap: 12px;">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25);">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h2 class="m-0 font-weight-bold text-dark" style="font-size: 1.65rem; letter-spacing: -0.5px;">
                        Paket Soal Ujian
                    </h2>
                    <p class="text-muted small mb-0 mt-0.5 font-weight-500">Perakitan paket naskah butir soal dari bank soal untuk persiapan asesmen.</p>
                </div>
            </div>
            <div>
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>cbt_dashboard" class="text-muted font-weight-500"><i class="fas fa-tachometer-alt mr-1"></i> CBT</a></li>
                    <li class="breadcrumb-item active text-warning font-weight-bold">Paket Soal</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>
        <div class="row">
            <!-- 1. FORM INPUT BUAT PAKET SOAL (DI SEBELAH KIRI) -->
            <div class="col-lg-4 col-12 mb-4">
                <div class="card form-card-rakit">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center">
                        <i class="fas fa-magic text-primary mr-2" style="font-size: 1.1rem;"></i>
                        <h6 class="font-weight-bold mb-0 text-dark" style="font-family: 'Poppins', sans-serif;">
                            Rakit Paket Naskah Baru
                        </h6>
                    </div>
                    <form method="POST" action="<?= BASE_URL ?>cbt_paket/store">
                        <div class="card-body p-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Nama Paket Naskah Soal <span class="text-danger">*</span></label>
                                <input type="text" name="nama_paket" class="form-control custom-form-input" placeholder="Contoh: SAS Ganjil 2026/2027 Ekonomi X" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Pilih Bank Soal Sumber <span class="text-danger">*</span></label>
                                <select name="id_bank" id="selectBankPaket" class="form-control custom-form-select" required onchange="syncBankMapel(this)">
                                    <option value="">-- Pilih Bank Soal Sumber --</option>
                                    <?php foreach ($bank_list as $b): ?>
                                        <option value="<?= $b['id_bank'] ?>" data-mapel="<?= $b['id_mapel'] ?>" data-tingkat="<?= htmlspecialchars($b['tingkat'] ?? '') ?>">
                                            <?= htmlspecialchars($b['nama_bank']) ?> (<?= htmlspecialchars($b['nama_mapel'] ?? '-') ?> &bull; <?= htmlspecialchars($b['tingkat'] ?? 'Semua') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <input type="hidden" name="id_mapel" id="inputMapelPaket" value="">

                            <div class="row mb-3" style="row-gap: 10px;">
                                <div class="col-6">
                                    <label class="font-weight-bold small text-dark mb-1">Jenis Asesmen</label>
                                    <select name="jenis_asesmen" class="form-control custom-form-select">
                                        <option value="Sumatif Akhir Semester (SAS)" selected>SAS (Akhir Semester)</option>
                                        <option value="Sumatif Tengah Semester (STS)">STS (Tengah Semester)</option>
                                        <option value="Asesmen Sumatif TP">Sumatif Lingkup TP</option>
                                        <option value="Asesmen Formatif">Formatif / Latihan</option>
                                        <option value="Try Out Ujian">Try Out / Ujian Sekolah</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="font-weight-bold small text-dark mb-1">Tingkat / Kelas</label>
                                    <input type="text" name="tingkat" id="inputTingkatPaket" class="form-control custom-form-input" value="X" placeholder="Contoh: X, XI, XII">
                                </div>
                            </div>

                            <div class="row mb-3" style="row-gap: 10px;">
                                <div class="col-6">
                                    <label class="font-weight-bold small text-dark mb-1">Semester</label>
                                    <select name="semester" class="form-control custom-form-select">
                                        <option value="Ganjil" selected>Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="font-weight-bold small text-dark mb-1">Alokasi Waktu</label>
                                    <input type="text" name="alokasi_waktu" class="form-control custom-form-input" value="90 Menit" placeholder="Contoh: 90 Menit">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Nama Guru Penyusun</label>
                                <input type="text" name="penyusun" class="form-control custom-form-input" placeholder="Nama Guru Pembuat Naskah">
                            </div>

                            <div class="row mb-3" style="row-gap: 10px;">
                                <div class="col-6">
                                    <label class="font-weight-bold small text-dark mb-1">Target Soal PG</label>
                                    <input type="number" name="jml_soal_pg" class="form-control custom-form-input" value="30" min="0">
                                </div>
                                <div class="col-6">
                                    <label class="font-weight-bold small text-dark mb-1">Target Soal Esai</label>
                                    <input type="number" name="jml_soal_essay" class="form-control custom-form-input" value="5" min="0">
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-lg border mb-3">
                                <label class="font-weight-bold small text-dark mb-2 d-block">
                                    <i class="fas fa-random text-primary mr-1"></i> Opsi Ujian Online
                                </label>
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="acakSoal" name="acak_soal" checked>
                                    <label class="custom-control-label small font-weight-bold" for="acakSoal">Acak Urutan Soal Siswa</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="acakOpsi" name="acak_opsi" checked>
                                    <label class="custom-control-label small font-weight-bold" for="acakOpsi">Acak Opsi Pilihan Jawaban</label>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold small text-dark mb-1">Keterangan / Catatan</label>
                                <textarea name="keterangan" class="form-control" rows="2" style="font-family: 'Poppins', sans-serif; font-size: 0.86rem; border-color: #cbd5e1; background: #f8fafc; border-radius: 8px;" placeholder="Catatan perakitan soal..."></textarea>
                            </div>
                        </div>

                        <div class="card-footer bg-light p-3">
                            <button type="submit" class="btn btn-gradient-indigo btn-block font-weight-bold rounded-pill shadow-sm py-2">
                                <i class="fas fa-magic mr-1"></i> Buat &amp; Buka Studio Rakit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. DAFTAR PAKET SOAL (DI SEBELAH KANAN) -->
            <div class="col-lg-8 col-12">
                <!-- SEGMENTED TABS FILTER PAKET -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 10px;">
                    <div class="btn-group btn-group-sm p-1 rounded-pill shadow-sm" style="background: #f1f5f9; border: 1px solid #e2e8f0;">
                        <a href="<?= BASE_URL ?>cbt_paket?tab=all" class="btn btn-sm font-weight-bold rounded-pill px-3 <?= (empty($_GET['tab']) || $_GET['tab'] === 'all') ? 'btn-white text-primary shadow-sm bg-white' : 'text-muted' ?>">
                            <i class="fas fa-boxes mr-1"></i> Semua Paket (<?= count($paket_list) ?>)
                        </a>
                        <a href="<?= BASE_URL ?>cbt_paket?tab=serentak" class="btn btn-sm font-weight-bold rounded-pill px-3 <?= (($_GET['tab'] ?? '') === 'serentak') ? 'btn-white text-success shadow-sm bg-white' : 'text-muted' ?>">
                            <i class="fas fa-check-double mr-1"></i> Siap Serentak
                        </a>
                        <a href="<?= BASE_URL ?>cbt_paket?tab=mandiri" class="btn btn-sm font-weight-bold rounded-pill px-3 <?= (($_GET['tab'] ?? '') === 'mandiri') ? 'btn-white text-dark shadow-sm bg-white' : 'text-muted' ?>">
                            <i class="fas fa-user-edit mr-1"></i> Draft Mandiri
                        </a>
                    </div>

                    <span class="badge badge-light border text-muted px-3 py-1.5 rounded-pill font-weight-bold small">
                        Total: <strong class="text-primary font-weight-bold"><?= count($paket_list) ?></strong> Paket
                    </span>
                </div>

                <div class="card table-paket-card">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-layer-group text-primary mr-2" style="font-size: 1.1rem;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Daftar Paket Asesmen Naskah
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-family: 'Poppins', sans-serif;">
                            <thead class="table-paket-header">
                                <tr>
                                    <th style="width: 35px;">#</th>
                                    <th>Nama Paket &amp; Mapel</th>
                                    <th>Jenis &amp; Tingkat</th>
                                    <th class="text-center">Status Naskah</th>
                                    <th class="text-center" style="width: 240px;">Aksi &amp; Administrasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($paket_list)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="mb-3">
                                                <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.8rem;">
                                                    <i class="fas fa-boxes"></i>
                                                </div>
                                            </div>
                                            <h6 class="font-weight-bold text-dark mb-1">Belum Ada Paket Naskah Soal</h6>
                                            <p class="text-muted small mb-0">Gunakan formulir di sebelah kiri untuk mulai merakit paket naskah soal baru.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($paket_list as $i => $p): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted align-middle"><?= $i + 1 ?></td>
                                        <td class="align-middle">
                                            <strong class="text-dark d-block" style="font-size: 0.95rem;"><?= htmlspecialchars($p['nama_paket']) ?></strong>
                                            <div class="d-flex align-items-center flex-wrap mt-1" style="gap: 4px;">
                                                <span class="badge text-white font-weight-bold px-2 py-0.5 rounded" style="background: #4f46e5; font-size: 0.72rem;">
                                                    <i class="fas fa-book mr-1"></i> <?= htmlspecialchars($p['nama_mapel'] ?? '-') ?>
                                                </span>
                                                <span class="badge badge-light border text-muted px-2 py-0.5 rounded" style="font-size: 0.72rem;">
                                                    Sumber: <?= htmlspecialchars($p['nama_bank'] ?? '-') ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-info px-2 py-1 mb-1 d-inline-block font-weight-bold" style="font-size: 0.75rem;">
                                                <?= htmlspecialchars($p['jenis_asesmen'] ?? 'SAS') ?>
                                            </span>
                                            <div class="small text-muted font-weight-bold">
                                                Kelas <?= htmlspecialchars($p['tingkat'] ?? '-') ?> &bull; <?= htmlspecialchars($p['semester'] ?? 'Ganjil') ?>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-primary px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.75rem;">
                                                <i class="fas fa-puzzle-piece mr-1"></i> <?= (int)($p['total_dirakit'] ?? 0) ?> Butir
                                            </span>
                                            <div class="small text-muted font-weight-bold mt-1" style="font-size: 0.72rem;">
                                                PG: <?= (int)$p['jml_soal_pg'] ?> | Esai: <?= (int)$p['jml_soal_essay'] ?>
                                            </div>
                                            <?php if (!empty($p['is_siap_serentak']) || ($p['status_verifikasi'] ?? '') === 'siap' || ($p['status_verifikasi'] ?? '') === 'terverifikasi'): ?>
                                                <span class="badge badge-success px-2 py-0.5 rounded-pill font-weight-bold d-inline-block mt-1" style="font-size: 0.7rem;">
                                                    <i class="fas fa-check-circle mr-1"></i> Siap Serentak
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary px-2 py-0.5 rounded-pill font-weight-bold d-inline-block mt-1" style="font-size: 0.7rem;">
                                                    <i class="fas fa-user-edit mr-1"></i> Draft Mandiri
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex align-items-center justify-content-center flex-wrap" style="gap: 4px;">
                                                <a href="<?= BASE_URL ?>cbt_paket/builder?id_paket=<?= $p['id_paket'] ?>" class="btn btn-xs btn-gradient-indigo font-weight-bold rounded-pill px-2.5 py-1 shadow-sm" title="Buka Studio Rakit Soal">
                                                    <i class="fas fa-puzzle-piece mr-1 text-warning"></i> Studio
                                                </a>

                                                <?php if (!empty($p['is_siap_serentak'])): ?>
                                                    <a href="<?= BASE_URL ?>cbt_paket/toggle_serentak?id_paket=<?= $p['id_paket'] ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2 py-1" onclick="return confirm('Kembalikan status naskah paket ini menjadi Draft Mandiri Guru?')" title="Kembalikan ke Draft Mandiri">
                                                        <i class="fas fa-undo"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>cbt_paket/toggle_serentak?id_paket=<?= $p['id_paket'] ?>" class="btn btn-xs btn-soft-success font-weight-bold rounded-pill px-2 py-1 shadow-sm" onclick="return confirm('Tandai naskah paket ini SIAP DIGUNAKAN UJIAN SERENTAK SEKOLAH (SAS/SAT/STS)?')" title="Serahkan ke Panitia Ujian Serentak">
                                                        <i class="fas fa-paper-plane mr-1"></i> Siap Serentak
                                                    </a>
                                                <?php endif; ?>

                                                <a href="<?= BASE_URL ?>cbt_paket/preview_siswa?id_paket=<?= $p['id_paket'] ?>" target="_blank" class="btn btn-xs btn-soft-info font-weight-bold rounded-pill px-2 py-1 shadow-sm" title="Simulasi Tampilan Siswa">
                                                    <i class="fas fa-desktop"></i>
                                                </a>
                                                
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-xs btn-light border font-weight-bold rounded-pill px-2 py-1 dropdown-toggle shadow-sm text-muted" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-print mr-1 text-secondary"></i> Cetak
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 12px; font-family: 'Poppins', sans-serif;">
                                                        <a class="dropdown-item py-2 small" href="<?= BASE_URL ?>cbt_paket/print_naskah?id_paket=<?= $p['id_paket'] ?>" target="_blank">
                                                            <i class="fas fa-file-alt text-primary mr-2"></i> 📄 Naskah Soal Ujian
                                                        </a>
                                                        <a class="dropdown-item py-2 small" href="<?= BASE_URL ?>cbt_paket/print_kisi_kisi?id_paket=<?= $p['id_paket'] ?>" target="_blank">
                                                            <i class="fas fa-th-list text-info mr-2"></i> 📊 Format Kisi-Kisi Standar
                                                        </a>
                                                        <a class="dropdown-item py-2 small" href="<?= BASE_URL ?>cbt_paket/print_kartu_soal?id_paket=<?= $p['id_paket'] ?>" target="_blank">
                                                            <i class="fas fa-id-card text-warning mr-2"></i> 🗂️ Kartu Soal Akreditasi
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item py-2 small" href="<?= BASE_URL ?>cbt_paket/print_kunci?id_paket=<?= $p['id_paket'] ?>" target="_blank">
                                                            <i class="fas fa-key text-success mr-2"></i> 🔑 Kunci &amp; Rubrik Penskoran
                                                        </a>
                                                    </div>
                                                </div>

                                                <a href="<?= BASE_URL ?>cbt_paket/delete?id_paket=<?= $p['id_paket'] ?>" class="btn btn-xs btn-soft-danger rounded-circle p-0" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('Hapus paket soal ini beserta naskahnya?')" title="Hapus Paket">
                                                    <i class="fas fa-trash-alt"></i>
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
        </div>
    </div>
</section>

<script>
function syncBankMapel(sel) {
    const selectedOpt = sel.options[sel.selectedIndex];
    const mapelId = selectedOpt.getAttribute('data-mapel') || '';
    const tingkat = selectedOpt.getAttribute('data-tingkat') || '';
    document.getElementById('inputMapelPaket').value = mapelId;
    if (tingkat) {
        document.getElementById('inputTingkatPaket').value = tingkat;
    }
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
