<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .jadwal-icon-box {
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
    .form-card-jadwal {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .table-jadwal-card {
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
    .table-jadwal-header th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px;
        border-top: none !important;
        border-bottom: 1.5px solid #e2e8f0 !important;
        padding: 12px 14px !important;
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
    .btn-soft-success {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        font-weight: 700;
    }
    .btn-soft-success:hover {
        background: #16a34a;
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
                <div class="mr-3" style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #4f46e5, #3730a3); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h2 class="m-0 font-weight-bold text-dark" style="font-size: 1.65rem; letter-spacing: -0.5px;">
                        Agenda &amp; Jadwal Ujian
                    </h2>
                    <p class="text-muted small mb-0 mt-0.5 font-weight-500">Aktivasi token, alokasi waktu, dan pengaturan rombel peserta asesmen.</p>
                </div>
            </div>
            <div>
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>cbt_dashboard" class="text-muted font-weight-500"><i class="fas fa-tachometer-alt mr-1"></i> CBT</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Agenda Ujian</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>
        <div class="row">
            <!-- 1. FORM BUAT JADWAL BARU (DI SEBELAH KIRI) -->
            <div class="col-lg-4 col-12 mb-4">
                <div class="card form-card-jadwal">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center">
                        <i class="fas fa-calendar-plus text-primary mr-2" style="font-size: 1.1rem;"></i>
                        <h6 class="font-weight-bold mb-0 text-dark" style="font-family: 'Poppins', sans-serif;">
                            Buat Agenda Ujian Baru
                        </h6>
                    </div>
                    <form method="POST" action="<?= BASE_URL ?>cbt_jadwal/store">
                        <div class="card-body p-4">
                            <!-- BADGE ROLE MODE -->
                            <div class="mb-3">
                                <?php if (!empty($info['is_admin'])): ?>
                                    <span class="badge badge-success px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 0.78rem;">
                                        <i class="fas fa-shield-alt mr-1"></i> Mode Admin (CBT Serentak / Skala Sekolah)
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-primary px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 0.78rem;">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i> Mode Guru (CBT Mandiri / Kelas Ampuan)
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Nama Ujian / Asesmen <span class="text-danger">*</span></label>
                                <input type="text" name="nama_ujian" class="form-control custom-form-input" placeholder="<?= !empty($info['is_admin']) ? 'Contoh: Sumatif Akhir Semester (SAS) Ganjil 2026/2027' : 'Contoh: Kuis Formatif Harian TP 1.1' ?>" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Pilih Paket Naskah Soal <span class="text-danger">*</span></label>
                                <select name="id_paket" class="form-control custom-form-select" required>
                                    <option value="">-- Pilih Paket Naskah Soal --</option>
                                    <?php foreach ($paket_list as $p): ?>
                                        <?php 
                                            $is_siap = !empty($p['is_siap_serentak']) || ($p['status_verifikasi'] ?? '') === 'siap';
                                            $badge_prefix = $is_siap ? '[SIAP SERENTAK] ' : '[DRAFT MANDIRI] ';
                                        ?>
                                        <option value="<?= $p['id_paket'] ?>">
                                            <?= $badge_prefix ?><?= htmlspecialchars($p['nama_paket']) ?> (<?= htmlspecialchars($p['nama_mapel'] ?? '') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- PILIH KELAS SASARAN (DENGAN DUKUNGAN MULTI-KELAS ADMIN) -->
                            <div class="form-group mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="font-weight-bold small text-dark mb-0">Kelas Sasaran <span class="text-danger">*</span></label>
                                    <?php if (!empty($info['is_admin'])): ?>
                                        <button type="button" class="btn btn-xs btn-link p-0 text-primary font-weight-bold" onclick="$('#multiKelasBox').toggle(); $('#singleKelasBox').toggle();">
                                            <i class="fas fa-layer-group mr-1"></i> Mode Multi-Kelas
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <div id="singleKelasBox">
                                    <select name="id_kelas" id="selectSingleKelas" class="form-control custom-form-select">
                                        <option value="">-- Pilih Kelas Sasaran --</option>
                                        <?php foreach ($kelas_list as $k): ?>
                                            <option value="<?= $k['id_kelas'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <?php if (!empty($info['is_admin'])): ?>
                                    <div id="multiKelasBox" style="display: none;" class="p-3 bg-light border rounded-lg">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small font-weight-bold text-muted">Pilih Beberapa Rombel Sekaligus:</span>
                                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2" onclick="$('.chk-multi-kelas').prop('checked', !$('.chk-multi-kelas').first().prop('checked'))">
                                                Pilih Semua
                                            </button>
                                        </div>
                                        <div class="row" style="max-height: 140px; overflow-y: auto; row-gap: 6px;">
                                            <?php foreach ($kelas_list as $k): ?>
                                                <div class="col-6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="id_kelas_multi[]" value="<?= $k['id_kelas'] ?>" class="custom-control-input chk-multi-kelas" id="chk_k_<?= $k['id_kelas'] ?>">
                                                        <label class="custom-control-label small font-weight-bold" for="chk_k_<?= $k['id_kelas'] ?>">
                                                            <?= htmlspecialchars($k['nama_kelas']) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">*Sistem otomatis membuat agenda ujian dan mem-plotting peserta untuk setiap kelas yang dicentang.</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row mb-3" style="row-gap: 10px;">
                                <div class="col-6">
                                    <label class="font-weight-bold small text-dark mb-1">Durasi (Menit)</label>
                                    <input type="number" name="durasi_menit" class="form-control custom-form-input" value="60" min="5" required>
                                </div>
                                <div class="col-6">
                                    <label class="font-weight-bold small text-dark mb-1">KKM / Passing</label>
                                    <input type="number" name="passing_grade" class="form-control custom-form-input" value="75" min="0" max="100">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Waktu Mulai Ujian</label>
                                <input type="datetime-local" name="tanggal_mulai" class="form-control custom-form-input" value="<?= date('Y-m-d\TH:i') ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Waktu Selesai Ujian</label>
                                <input type="datetime-local" name="tanggal_selesai" class="form-control custom-form-input" value="<?= date('Y-m-d\TH:i', strtotime('+2 hours')) ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Token / PIN Proktor (Opsional)</label>
                                <input type="text" name="pin_proktor" class="form-control custom-form-input font-weight-bold text-uppercase" placeholder="Otomatis acak (atau isi 6 digit)">
                                <small class="text-muted" style="font-size: 0.76rem;">Jika kosong, sistem otomatis membuat PIN 6 karakter acak.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Pengumuman Nilai ke Siswa</label>
                                <select name="tampilkan_nilai" class="form-control custom-form-select font-weight-bold">
                                    <option value="1" selected>&bull; Tampilkan Nilai (Siswa langsung lihat nilai)</option>
                                    <option value="0">&bull; Sembunyikan Nilai (Nilai ditutup oleh panitia/guru)</option>
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold small text-dark mb-1">Status Awal</label>
                                <select name="status" class="form-control custom-form-select">
                                    <option value="aktif" selected>Aktif (Bisa Dikerjakan Siswa Sesuai Jadwal)</option>
                                    <option value="draft">Draft (Disembunyikan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="card-footer bg-light p-3">
                            <button type="submit" class="btn btn-gradient-indigo btn-block font-weight-bold rounded-pill shadow-sm py-2">
                                <i class="fas fa-save mr-1"></i> Simpan Agenda Ujian
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. DAFTAR JADWAL UJIAN (DI SEBELAH KANAN) -->
            <div class="col-lg-8 col-12">
                <div class="card table-jadwal-card">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check text-primary mr-2" style="font-size: 1.1rem;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Daftar Agenda Ujian
                            </h6>
                        </div>
                        <span class="badge badge-light border text-muted px-3 py-1.5 rounded-pill font-weight-bold small">
                            Total: <strong class="text-primary font-weight-bold"><?= count($jadwal_list) ?></strong> Agenda
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-family: 'Poppins', sans-serif;">
                            <thead class="table-jadwal-header">
                                <tr>
                                    <th style="width: 35px;">#</th>
                                    <th>Nama Ujian &amp; Paket</th>
                                    <th>Kelas</th>
                                    <th>Waktu &amp; Durasi</th>
                                    <th class="text-center">PIN Proktor</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($jadwal_list)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="mb-3">
                                                <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.8rem;">
                                                    <i class="fas fa-calendar-times"></i>
                                                </div>
                                            </div>
                                            <h6 class="font-weight-bold text-dark mb-1">Belum Ada Agenda Ujian</h6>
                                            <p class="text-muted small mb-0">Gunakan formulir di sebelah kiri untuk membuat jadwal pelaksanaan ujian baru.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($jadwal_list as $i => $j): ?>
                                    <?php 
                                        $tampilkan = isset($j['tampilkan_nilai']) ? (int)$j['tampilkan_nilai'] : 1; 
                                    ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted align-middle"><?= $i + 1 ?></td>
                                        <td class="align-middle">
                                            <strong class="text-dark d-block" style="font-size: 0.92rem;"><?= htmlspecialchars($j['nama_ujian']) ?></strong>
                                            <span class="badge text-white font-weight-bold px-2 py-0.5 rounded mt-1" style="background: #4f46e5; font-size: 0.72rem;">
                                                <i class="fas fa-box-open mr-1"></i> <?= htmlspecialchars($j['nama_paket'] ?? '-') ?> (<?= htmlspecialchars($j['nama_mapel'] ?? '') ?>)
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-light border text-dark font-weight-bold px-2.5 py-1 rounded" style="font-size: 0.78rem;">
                                                <?= htmlspecialchars($j['nama_kelas'] ?? 'Semua Kelas') ?>
                                            </span>
                                        </td>
                                        <td class="small align-middle">
                                            <div><i class="fas fa-play text-success mr-1" style="font-size: 0.7rem;"></i> <?= $j['tanggal_mulai'] ? date('d M Y H:i', strtotime($j['tanggal_mulai'])) : '-' ?></div>
                                            <div><i class="fas fa-stop text-danger mr-1" style="font-size: 0.7rem;"></i> <?= $j['tanggal_selesai'] ? date('d M Y H:i', strtotime($j['tanggal_selesai'])) : '-' ?></div>
                                            <small class="text-muted font-weight-bold"><i class="fas fa-stopwatch mr-1"></i> <?= $j['durasi_menit'] ?? 60 ?> Mnt &bull; KKM: <?= $j['passing_grade'] ?? 75 ?></small>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-inline-flex align-items-center">
                                                <span class="badge badge-dark px-2 py-1 font-weight-bold" style="letter-spacing: 1.5px; font-family: monospace; font-size: 0.88rem; border-radius: 6px;">
                                                    <?= htmlspecialchars($j['pin_proktor'] ?? '-') ?>
                                                </span>
                                                <a href="<?= BASE_URL ?>cbt_jadwal/refresh_token?id_jadwal=<?= $j['id_jadwal'] ?>" class="btn btn-xs btn-light border ml-1 rounded-circle p-0" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center;" title="Rilis Token Baru" onclick="return confirm('Generate ulang token proktor untuk ujian ini?')">
                                                    <i class="fas fa-sync-alt fa-xs text-secondary"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="<?= BASE_URL ?>cbt_jadwal/toggle_nilai?id_jadwal=<?= $j['id_jadwal'] ?>" class="badge badge-<?= $tampilkan === 1 ? 'success' : 'secondary' ?> px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem; text-decoration: none;" title="Klik untuk mengubah apakah siswa dapat melihat nilainya">
                                                <i class="fas fa-<?= $tampilkan === 1 ? 'eye' : 'eye-slash' ?> mr-1"></i>
                                                <?= $tampilkan === 1 ? 'Terbuka' : 'Ditutup' ?>
                                            </a>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="<?= BASE_URL ?>cbt_jadwal/toggle?id_jadwal=<?= $j['id_jadwal'] ?>" class="badge badge-<?= $j['status']==='aktif'?'success':'secondary' ?> px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem; text-decoration: none;" title="Klik untuk mengubah status aktif/selesai">
                                                <?= ucfirst($j['status']) ?>
                                            </a>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex align-items-center justify-content-center flex-wrap" style="gap: 4px;">
                                                <a href="<?= BASE_URL ?>cbt_peserta?id_jadwal=<?= $j['id_jadwal'] ?>" class="btn btn-xs btn-soft-primary font-weight-bold rounded-pill px-2.5 py-1 shadow-sm" title="Kelola Peserta &amp; Auto Generate">
                                                    <i class="fas fa-users mr-1"></i> Peserta
                                                </a>
                                                <a href="<?= BASE_URL ?>cbt_hasil?id_jadwal=<?= $j['id_jadwal'] ?>" class="btn btn-xs btn-soft-success font-weight-bold rounded-pill px-2.5 py-1 shadow-sm" title="Lihat Hasil Nilai">
                                                    <i class="fas fa-chart-bar mr-1"></i> Hasil
                                                </a>
                                                <a href="<?= BASE_URL ?>cbt_jadwal/delete?id_jadwal=<?= $j['id_jadwal'] ?>" class="btn btn-xs btn-soft-danger rounded-circle p-0" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('Hapus agenda ujian ini?')" title="Hapus">
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

<?php include __DIR__ . '/partials/footer.php'; ?>
