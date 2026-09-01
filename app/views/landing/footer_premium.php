<?php
// app/views/landing/footer_premium.php
$config = $config ?? ($data['config'] ?? []);
$identitas = $identitas ?? ($data['identitas'] ?? []);
?>

<!-- FOOTER BOXES -->
<section class="footer-boxes mt-5">
    <div class="container">
        <div class="row g-0 shadow-lg">
            <div class="col-md-6">
                <div class="footer-box-red p-4 p-lg-5" style="background-color: #f59e0b; color: #1a237e;">
                    <h3 class="fw-bold mb-3">
                        <?= htmlspecialchars($config['school_name'] ?? 'SMA Plus Al-Manshuriyah') ?></h3>
                    <p class="mb-0 fw-medium">Sistem Informasi Manajemen Akademik Sekolah (SIMAKS) - Mewujudkan
                        transparansi dan efisiensi pendidikan digital masa kini.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="footer-box-teal p-4 p-lg-5" style="background-color: #10b981; color: white;">
                    <h4 class="fw-bold mb-3"><i class="fas fa-map-marker-alt me-2"></i> LOKASI KAMI</h4>
                    <p class="mb-0 small line-height-base">
                        <?= htmlspecialchars($identitas['alamat'] ?? $config['school_address'] ?? 'Jl. Al-Manshuriyah No. 1, Kabupaten Sukabumi, Jawa Barat') ?>
                    </p>
                    <div class="mt-3 small pt-2 border-top border-white border-opacity-25">
                        <i class="fas fa-phone me-2"></i>
                        <?= htmlspecialchars($config['school_phone'] ?? '(021) 1234567') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer-main py-5" style="background-color: #0f172a; color: #94a3b8;">
    <div class="container py-lg-4">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="text-white fw-bold mb-4">PETA SITUS</h5>
                <div class="row">
                    <div class="col-6 footer-links">
                        <a href="<?= BASE_URL ?>landing/index"
                            class="d-block mb-2 text-decoration-none">Beranda</a>
                        <a href="<?= BASE_URL ?>landing/profil_sekolah"
                            class="d-block mb-2 text-decoration-none">Profil Sekolah</a>
                        <a href="<?= BASE_URL ?>landing/guru_list"
                            class="d-block mb-2 text-decoration-none">Profil GTK</a>
                    </div>
                    <div class="col-6 footer-links">
                        <a href="<?= BASE_URL ?>landing/berita_list"
                            class="d-block mb-2 text-decoration-none">Berita</a>
                        <a href="<?= BASE_URL ?>landing/gallery"
                            class="d-block mb-2 text-decoration-none">Galeri</a>
                        <a href="<?= BASE_URL ?>auth/login" class="d-block mb-2 text-decoration-none">Login
                            SIMAKS</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="text-white fw-bold mb-4 text-uppercase">Program & Kegiatan</h5>
                <div class="tag-cloud">
                    <span class="badge border border-secondary text-secondary px-3 py-2 mb-2 me-1">Kurikulum
                        Merdeka</span>
                    <span class="badge border border-secondary text-secondary px-3 py-2 mb-2 me-1">PPDB 2026</span>
                    <span class="badge border border-secondary text-secondary px-3 py-2 mb-2 me-1">Tahfidz Quran</span>
                    <span class="badge border border-secondary text-secondary px-3 py-2 mb-2 me-1">Moeslempreneur</span>
                </div>
            </div>
            <div class="col-lg-4">
                <h5 class="text-white fw-bold mb-4">HUBUNGI KAMI</h5>
                <div class="mb-3">
                    <div class="small mb-1"><i class="fas fa-envelope text-accent me-2"></i> Email Resmi</div>
                    <div class="text-white fw-semibold">
                        <?= htmlspecialchars($config['school_email'] ?? 'info@smaplus.sch.id') ?></div>
                </div>
                <div class="mb-4">
                    <div class="small mb-1"><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp</div>
                    <div class="text-white fw-semibold">
                        <?= htmlspecialchars($config['whatsapp_sekolah'] ?? '08886185500') ?></div>
                </div>
                <div class="d-flex gap-3">
                    <a href="<?= htmlspecialchars($config['facebook_url'] ?? '#') ?>"
                        class="social-circle bg-primary-subtle text-primary"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= htmlspecialchars($config['instagram_url'] ?? '#') ?>"
                        class="social-circle bg-danger-subtle text-danger"><i class="fab fa-instagram"></i></a>
                    <a href="<?= htmlspecialchars($config['youtube_url'] ?? '#') ?>"
                        class="social-circle bg-danger-subtle text-danger"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="footer-bottom py-3" style="background-color: #020617; border-top: 1px solid rgba(255,255,255,0.05);">
    <div class="container text-center">
        <p class="mb-0 small text-muted">
            &copy; <?= date('Y') ?> <span
                class="text-white fw-semibold"><?= htmlspecialchars($config['school_name'] ?? 'SMA Plus Al-Manshuriyah') ?></span>.
            All rights reserved. | Powered by <a href="#" class="text-accent text-decoration-none">SIMAKS</a>
        </p>
    </div>
</div>

<style>
    .footer-links a {
        color: #94a3b8;
        transition: all 0.3s;
        font-size: 0.9rem;
    }

    .footer-links a:hover {
        color: #f59e0b;
        padding-left: 5px;
    }

    .social-circle {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .social-circle:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .text-accent {
        color: #f59e0b;
    }

    .bg-primary-subtle {
        background-color: rgba(37, 99, 235, 0.1);
    }

    .bg-danger-subtle {
        background-color: rgba(220, 38, 38, 0.1);
    }

    /* ===== CRITICAL FLOATING BOTTOM NAV & WA (MOBILE) ===== */
    @media (max-width: 991.98px) {
        body {
            padding-bottom: 70px !important;
        }
        .mobile-bottom-nav {
            display: flex !important;
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 62px !important;
            background: rgba(0, 0, 51, 0.98) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-top: 2px solid #f59e0b !important;
            z-index: 999999 !important;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.35) !important;
            align-items: center !important;
            justify-content: space-around !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .mobile-nav-item {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            color: rgba(255, 255, 255, 0.75) !important;
            text-decoration: none !important;
            font-size: 0.65rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            padding: 4px 6px !important;
            flex: 1 !important;
            text-align: center !important;
        }
        .mobile-nav-item i {
            font-size: 1.15rem !important;
            margin-bottom: 2px !important;
        }
        .mobile-nav-item:hover,
        .mobile-nav-item:active,
        .mobile-nav-item.active {
            color: #fbbf24 !important;
        }
        .mobile-nav-spmb {
            position: relative !important;
            top: -14px !important;
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: #000033 !important;
            width: 52px !important;
            height: 52px !important;
            border-radius: 50% !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 800 !important;
            font-size: 0.62rem !important;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.5) !important;
            border: 3px solid #000033 !important;
            text-decoration: none !important;
            transition: transform 0.2s ease !important;
            flex-shrink: 0 !important;
        }
        .mobile-nav-spmb i {
            font-size: 1.25rem !important;
            margin-bottom: 1px !important;
        }
        .mobile-nav-spmb:hover,
        .mobile-nav-spmb:active,
        .mobile-nav-spmb.active {
            transform: scale(1.08) !important;
            color: #000033 !important;
        }
        .floating-wa-btn {
            bottom: 74px !important;
            right: 16px !important;
            width: 48px !important;
            height: 48px !important;
            font-size: 1.55rem !important;
        }
    }

    .mobile-bottom-nav {
        display: none;
    }

    .floating-wa-btn {
        position: fixed !important;
        bottom: 24px !important;
        right: 24px !important;
        width: 54px !important;
        height: 54px !important;
        background: linear-gradient(135deg, #25d366, #128c7e) !important;
        color: #fff !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 1.75rem !important;
        box-shadow: 0 4px 18px rgba(37, 211, 102, 0.45) !important;
        z-index: 99999 !important;
        text-decoration: none !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease !important;
    }
    .floating-wa-btn:hover {
        transform: scale(1.1) translateY(-3px) !important;
        box-shadow: 0 8px 24px rgba(37, 211, 102, 0.6) !important;
        color: #fff !important;
    }
</style>
<!-- FLOATING WHATSAPP BUTTON (DESKTOP & MOBILE) -->
<?php
$raw_phone = $config['school_phone'] ?? $config['whatsapp_sekolah'] ?? '08886185500';
$wa_phone = preg_replace('/[^0-9]/', '', $raw_phone);
if (strpos($wa_phone, '0') === 0) {
    $wa_phone = '62' . substr($wa_phone, 1);
}
$wa_url = "https://wa.me/{$wa_phone}?text=" . urlencode("Halo Admin SMA Plus Al-Manshuriyah, saya ingin bertanya tentang informasi sekolah / pendaftaran.");
$cur_act = $_GET['act'] ?? '';
?>
<a href="<?= $wa_url ?>" target="_blank" class="floating-wa-btn" title="Hubungi Kami via WhatsApp">
  <i class="fab fa-whatsapp"></i>
</a>

<!-- FLOATING BOTTOM NAVIGATION (MOBILE ONLY) -->
<nav class="mobile-bottom-nav">
  <a href="<?= BASE_URL ?>landing" class="mobile-nav-item <?= empty($cur_act) || $cur_act === 'index' ? 'active' : '' ?>">
    <i class="fas fa-home"></i>
    <span>Beranda</span>
  </a>
  <a href="<?= BASE_URL ?>landing/profil_sekolah" class="mobile-nav-item <?= $cur_act === 'profil_sekolah' ? 'active' : '' ?>">
    <i class="fas fa-school"></i>
    <span>Profil</span>
  </a>
  <a href="<?= BASE_URL ?>landing/ppdb_form" class="mobile-nav-spmb <?= $cur_act === 'ppdb_form' ? 'active' : '' ?>" title="Daftar SPMB Online">
    <i class="fas fa-user-plus"></i>
    <span>SPMB</span>
  </a>
  <a href="<?= BASE_URL ?>landing/informasi_list" class="mobile-nav-item <?= in_array($cur_act, ['informasi_list', 'informasi_detail', 'berita_list', 'berita_detail']) ? 'active' : '' ?>">
    <i class="fas fa-bullhorn"></i>
    <span>Info</span>
  </a>
  <a href="<?= BASE_URL ?>auth/login" class="mobile-nav-item">
    <i class="fas fa-lock"></i>
    <span>SIMAKS</span>
  </a>
</nav>