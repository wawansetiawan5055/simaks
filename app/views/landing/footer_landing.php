<?php
// app/views/landing/footer_landing.php
$config = $config ?? (isset($data['config']) ? $data['config'] : []);
$b_url = BASE_URL . "landing";
$current_act = $_GET['act'] ?? 'index';
$is_home = ($current_act == 'index' || $current_act == '' || !isset($_GET['act']));
?>

<!-- FOOTER RED & TEAL BOXES (CTA) -->
<section class="footer-boxes <?= $is_home ? '' : 'pt-5 mt-5' ?>">
    <div class="container">
        <div class="row g-0">
            <div class="col-md-6">
                <div class="footer-box-red">
                    <h3 class="fw-bold mb-3"><?= htmlspecialchars($config['school_name'] ?? '-') ?></h3>
                    <p class="mb-0">
                        <?= htmlspecialchars($config['footer_description'] ?? 'Aplikasi Sistem Informasi Manajemen Akademik Sekolah (SIMAKS) menghadirkan efisiensi dan transparansi dalam pengelolaan data pendidikan.') ?>
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="footer-box-teal">
                    <h4 class="fw-bold mb-3">LOKASI KAMI</h4>
                    <p class="mb-0 small"><i class="fas fa-map-marker-alt me-2"></i>
                        <?= htmlspecialchars($config['school_address'] ?? '-') ?>
                    </p>
                    <p class="mt-2 small"><i class="fas fa-phone me-2"></i>
                        <?= htmlspecialchars($config['school_phone'] ?? '-') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN FOOTER -->
<footer class="footer-main">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5>PETA SITUS</h5>
                <div class="row">
                    <div class="col-6 footer-links">
                        <a href="<?= $b_url ?>#home">Beranda</a>
                        <a href="<?= BASE_URL ?>landing/profil_sekolah">Profil Sekolah</a>
                        <a href="<?= BASE_URL ?>landing/profil_sekolah#visi-misi">Visi & Misi</a>
                        <a href="<?= BASE_URL ?>landing/guru_list">Profil GTK</a>
                    </div>
                    <div class="col-6 footer-links">
                        <a href="<?= $b_url ?>#program">Program</a>
                        <a href="<?= $b_url ?>#galeri">Galeri</a>
                        <a href="<?= $b_url ?>#informasi">Berita</a>
                        <a href="<?= BASE_URL ?>auth/login">Login SIMAKS</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <h5>TAGS KEGIATAN</h5>
                <div class="tag-cloud">
                    <span class="tag">Kurikulum Merdeka</span>
                    <span class="tag">PPDB 2025</span>
                    <span class="tag">Be Moeslempreneur</span>
                    <span class="tag">Tahfidz Quran</span>
                    <span class="tag">Pramuka</span>
                    <span class="tag">HUT Sekolah</span>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <h5>HUBUNGI KAMI</h5>
                <p class="small mb-2"><i class="fas fa-envelope me-2"></i>
                    <?= htmlspecialchars($config['school_email'] ?? '-') ?>
                </p>
                <p class="small mb-3"><i class="fab fa-whatsapp me-2"></i>
                    <?= htmlspecialchars($config['whatsapp_sekolah'] ?? '-') ?>
                </p>
                <h5>IKUTI KAMI</h5>
                <div class="d-flex gap-2">
                    <a href="<?= htmlspecialchars($config['facebook_url'] ?? '#') ?>"
                        class="social-circle text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= htmlspecialchars($config['twitter_url'] ?? '#') ?>" class="social-circle text-white"><i
                            class="fab fa-twitter"></i></a>
                    <a href="<?= htmlspecialchars($config['instagram_url'] ?? '#') ?>"
                        class="social-circle text-white"><i class="fab fa-instagram"></i></a>
                    <a href="<?= htmlspecialchars($config['youtube_url'] ?? '#') ?>" class="social-circle text-white"><i
                            class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="footer-bottom">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($config['school_name'] ?? '-') ?>. All rights
            reserved. | Powered by <SIMAKS-DEV></SIMAKS-DEV></p>
    </div>
</div>

<!-- SCROLL TOP -->
<button id="scrollTop" title="Ke Atas"><i class="fas fa-chevron-up"></i></button>

<!-- GLOBAL SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Stats Counter (if used on page)
    const counters = document.querySelectorAll('.num');
    if (counters.length > 0) {
        const speed = 200;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = +entry.target.getAttribute('data-target');
                    const updateCount = () => {
                        const current = +entry.target.innerText;
                        const inc = target / speed;
                        if (current < target) {
                            entry.target.innerText = Math.ceil(current + inc);
                            setTimeout(updateCount, 1);
                        } else {
                            entry.target.innerText = target;
                        }
                    };
                    updateCount();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 1 });
        counters.forEach(counter => observer.observe(counter));
    }

    // Scroll Top functionality
    const scrollBtn = document.getElementById('scrollTop');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.classList.toggle('show', window.scrollY > 500);
        });
        scrollBtn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
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