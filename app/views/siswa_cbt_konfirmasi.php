<?php
// app/views/siswa_cbt_konfirmasi.php
// Halaman Konfirmasi Data Peserta & Validasi Token Ujian (ANBK / TKA Pusmendik Style)

include __DIR__ . '/partials/header.php';

$nama_siswa  = htmlspecialchars($info['nama_siswa'] ?? 'Siswa');
$nisn        = htmlspecialchars($info['nisn'] ?? ($info['nipd'] ?? '-'));
$nama_kelas  = htmlspecialchars($info['nama_kelas'] ?? '-');
$jk          = $info['jk_siswa'] ?? '';
$foto        = $info['foto_siswa'] ?? '';
$avatar_url  = get_user_avatar($foto, $jk, 'siswa');

$nama_mapel  = htmlspecialchars($info['nama_mapel'] ?? 'Mata Pelajaran');
$nama_ujian  = htmlspecialchars($info['nama_ujian'] ?? 'Asesmen CBT');
$nama_paket  = htmlspecialchars($info['nama_paket'] ?? 'Naskah Soal');
$durasi      = (int)($info['durasi_menit'] ?? 60);
$jml_pg      = (int)($info['jml_soal_pg'] ?? 0);
$jml_essay   = (int)($info['jml_soal_essay'] ?? 0);
$has_pin     = !empty($info['pin_proktor']) || !empty($info['token']);
$token_val   = htmlspecialchars($info['token'] ?? ($info['pin_proktor'] ?? ''));
?>

<style>
    .cbt-icon-box {
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
    .anbk-confirm-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .anbk-header-banner {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
        color: #ffffff;
        padding: 24px 28px;
    }
    .avatar-student-frame {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        object-fit: cover;
        background: #f8fafc;
    }
    .token-input-box {
        font-family: 'Courier New', Courier, monospace;
        letter-spacing: 6px;
        font-size: 1.4rem;
        font-weight: 800;
        text-transform: uppercase;
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        transition: all 0.2s ease;
    }
    .token-input-box:focus {
        border-color: #4f46e5;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
        outline: none;
    }
    .spec-item {
        display: flex;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.90rem;
    }
    .spec-item:last-child {
        border-bottom: none;
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

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (KONFIRMASI CBT SISWA)              */
    /* ============================================================ */
    @media (max-width: 768px) {
        .container {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .content-header {
            padding: 8px 4px 2px !important;
        }
        .content-header h4 {
            font-size: 0.90rem !important;
        }
        .anbk-confirm-card {
            border-radius: 12px !important;
            margin-bottom: 10px !important;
        }
        .anbk-header-banner {
            padding: 12px 14px !important;
        }
        .anbk-header-banner h4 {
            font-size: 0.92rem !important;
        }
        .avatar-student-frame {
            width: 75px !important;
            height: 75px !important;
        }
        .token-input-box {
            font-size: 1.1rem !important;
            letter-spacing: 4px !important;
        }
        .spec-item {
            font-size: 0.74rem !important;
            padding: 6px 0 !important;
        }
        .card-body {
            padding: 12px 10px !important;
        }
        .btn-gradient-indigo, .btn {
            padding: 7px 12px !important;
            font-size: 0.74rem !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="cbt-icon-box mr-3">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                    Konfirmasi Peserta Asesmen
                </h4>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>siswa_portal/cbt" class="btn btn-sm btn-outline-secondary rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Ujian
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1 mb-5">
    <div class="container">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <div class="anbk-confirm-card mb-4">
            <!-- BANNER HEADER -->
            <div class="anbk-header-banner d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="badge text-white font-weight-bold px-2.5 py-1 mb-2 rounded-pill" style="background: rgba(255,255,255,0.15); font-size: 0.72rem;">
                        <i class="fas fa-id-card mr-1"></i> LEMBAR KONFIRMASI TES
                    </span>
                    <h4 class="font-weight-bold text-white mb-1" style="font-family: 'Poppins', sans-serif;"><?= $nama_ujian ?></h4>
                    <p class="text-light small mb-0" style="opacity: 0.9;">
                        Mata Pelajaran: <strong class="text-white"><?= $nama_mapel ?></strong> &bull; Paket: <strong class="text-white"><?= $nama_paket ?></strong>
                    </p>
                </div>
                <div class="text-right d-none d-md-block">
                    <div class="text-light small" style="opacity: 0.85; font-size: 0.78rem;">Alokasi Waktu Ujian</div>
                    <h3 class="font-weight-bold text-warning mb-0" style="font-family: 'Poppins', sans-serif;"><i class="fas fa-stopwatch mr-1"></i> <?= $durasi ?> Menit</h3>
                </div>
            </div>

            <!-- BODY CARD -->
            <div class="card-body p-4 p-md-5">
                <div class="row">
                    <!-- KOLOM KIRI: PROFIL PESERTA SISWA -->
                    <div class="col-lg-5 col-12 border-right-lg mb-4 mb-lg-0 pr-lg-4">
                        <div class="text-center mb-4">
                            <img src="<?= $avatar_url ?>" alt="Foto Siswa" class="avatar-student-frame mb-3">
                            <h5 class="font-weight-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;"><?= $nama_siswa ?></h5>
                            <span class="badge badge-light border text-muted px-3 py-1 rounded-pill font-weight-bold">
                                NISN / NIPD: <?= $nisn ?>
                            </span>
                        </div>

                        <div class="p-3 bg-light rounded-lg border">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2 small text-uppercase" style="font-family: 'Poppins', sans-serif;">
                                <i class="fas fa-user-graduate text-primary mr-1"></i> Biodata Terdaftar
                            </h6>
                            <div class="spec-item">
                                <span class="text-muted">Nama Lengkap</span>
                                <strong class="text-dark"><?= $nama_siswa ?></strong>
                            </div>
                            <div class="spec-item">
                                <span class="text-muted">Kelas / Rombel</span>
                                <strong class="text-primary"><?= $nama_kelas ?></strong>
                            </div>
                            <div class="spec-item">
                                <span class="text-muted">Jenis Kelamin</span>
                                <strong class="text-dark"><?= $jk ?: '-' ?></strong>
                            </div>
                            <div class="spec-item">
                                <span class="text-muted">Status Peserta</span>
                                <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">Siap Ujian</span>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN: RINCIAN TES & VALIDASI TOKEN -->
                    <div class="col-lg-7 col-12 pl-lg-4">
                        <div class="p-3 bg-light rounded-lg border mb-4">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2 small text-uppercase" style="font-family: 'Poppins', sans-serif;">
                                <i class="fas fa-file-alt text-primary mr-1"></i> Rincian Naskah Soal
                            </h6>
                            <div class="spec-item">
                                <span class="text-muted">Mata Pelajaran</span>
                                <strong class="text-dark"><?= $nama_mapel ?></strong>
                            </div>
                            <div class="spec-item">
                                <span class="text-muted">Komposisi Butir Soal</span>
                                <strong class="text-dark"><?= $jml_pg ?> Pilihan Ganda <?= $jml_essay > 0 ? '&bull; ' . $jml_essay . ' Esai' : '' ?></strong>
                            </div>
                            <div class="spec-item">
                                <span class="text-muted">Durasi Pengerjaan</span>
                                <strong class="text-primary font-weight-bold"><?= $durasi ?> Menit</strong>
                            </div>
                            <?php if (!empty($info['petunjuk_umum'])): ?>
                                <div class="mt-3 p-2 bg-white rounded border small text-muted">
                                    <strong class="d-block text-dark mb-1"><i class="fas fa-info-circle text-warning mr-1"></i> Petunjuk Ujian:</strong>
                                    <?= nl2br(htmlspecialchars($info['petunjuk_umum'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- FORM TOKEN / START -->
                        <form method="POST" action="<?= BASE_URL ?>siswa_portal/cbt_konfirmasi?id_peserta=<?= (int)$info['id_peserta'] ?>">
                            <input type="hidden" name="action" value="start_exam">
                            
                            <?php if ($has_pin): ?>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark d-flex justify-content-between align-items-center mb-2" style="font-family: 'Poppins', sans-serif;">
                                        <span><i class="fas fa-key text-warning mr-1"></i> Masukkan Token Ujian:</span>
                                        <span class="badge badge-success px-2.5 py-1 rounded-pill small font-weight-bold">Wajib Diisi</span>
                                    </label>
                                    <input type="text" 
                                           name="token" 
                                           class="form-control text-center token-input-box py-3" 
                                           placeholder="Ketikkan Token" 
                                           value="<?= $token_val ?>"
                                           maxlength="10" 
                                           autocomplete="off" 
                                           required 
                                           autofocus>
                                    <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap" style="gap: 4px;">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle text-primary mr-1"></i> Token Anda: <strong class="text-primary"><?= $token_val ?></strong>
                                        </small>
                                        <small class="text-muted">
                                            Atau masukkan PIN Proktor dari Pengawas.
                                        </small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info py-3 mb-4 rounded-lg shadow-sm border-0" style="background: #e0e7ff; color: #1e1b4b;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle fa-2x text-primary mr-3"></i>
                                        <div>
                                            <strong class="d-block font-weight-bold">Ujian Tanpa Token</strong>
                                            <span class="small">Asesmen ini dapat langsung Anda kerjakan. Pastikan koneksi internet Anda stabil sebelum menekan tombol mulai.</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-gradient-indigo btn-block font-weight-bold rounded-pill py-3 shadow-sm text-uppercase" style="font-size: 1rem; letter-spacing: 0.5px;">
                                <i class="fas fa-play-circle mr-2"></i> Mulai Mengerjakan Ujian Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ATURAN & ETIKA PENGERJAAN UJIAN -->
        <div class="card border-0 shadow-sm p-4" style="border-radius: 16px; background: #ffffff;">
            <h6 class="font-weight-bold text-dark mb-3" style="font-family: 'Poppins', sans-serif;">
                <i class="fas fa-gavel text-primary mr-2"></i> Peraturan &amp; Tata Tertib Peserta CBT
            </h6>
            <div class="row small text-muted" style="row-gap: 8px;">
                <div class="col-md-6 mb-2">
                    <div class="d-flex align-items-start mb-2">
                        <span class="badge badge-primary mr-2 mt-0.5 rounded-circle" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        <span>Dilarang berpindah tab browser, meminimalkan jendela, atau membuka aplikasi lain selama ujian berlangsung.</span>
                    </div>
                    <div class="d-flex align-items-start">
                        <span class="badge badge-primary mr-2 mt-0.5 rounded-circle" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                        <span>Sistem otomatis mendeteksi pelanggaran dan akan mengunci pengerjaan jika melanggar batas toleransi.</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start mb-2">
                        <span class="badge badge-primary mr-2 mt-0.5 rounded-circle" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                        <span>Jawaban Anda tersimpan otomatis secara realtime ke server pada setiap butir soal yang Anda pilih.</span>
                    </div>
                    <div class="d-flex align-items-start">
                        <span class="badge badge-primary mr-2 mt-0.5 rounded-circle" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;">4</span>
                        <span>Gunakan tombol keyboard <kbd>A</kbd>-<kbd>E</kbd> untuk menjawab dan <kbd>Panah</kbd> untuk berpindah soal.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
