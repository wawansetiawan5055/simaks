<?php
// landing_sma.php - Premium Template (SMAN 8 JKT Style)
$config = $data['config'] ?? [];
$stats = $data['stats'] ?? ['total_siswa' => 0, 'total_gtk' => 0, 'tahun_berdiri' => 2010];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $config['school_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
  <style>
    /* ===== HERO FULL HEIGHT (EXACTLY VIEWPORT MINUS NAVBAR & MARGIN) ===== */
    .hero-section {
      height: calc(100vh - 95px);
      min-height: 440px;
      max-height: calc(100vh - 95px);
      overflow: hidden;
      position: relative;
    }
    .hero-section .container-fluid {
      height: 100%;
    }
    .hero-section .row.g-0 {
      height: 100%;
    }
    .hero-section .quote-slider-container {
      height: 100% !important;
      max-height: 100% !important;
      overflow: hidden;
    }
    .hero-section .center-slider-col {
      height: 100% !important;
      max-height: 100% !important;
      overflow: hidden;
      position: relative;
    }
    .hero-section #heroCarousel,
    .hero-section #heroCarousel .carousel-inner,
    .hero-section #heroCarousel .carousel-item {
      height: 100% !important;
      max-height: 100% !important;
      overflow: hidden;
    }
    .hero-section #heroCarousel .carousel-item img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      object-position: center !important;
      display: block;
    }
    .hero-section .pengumuman-sidebar {
      height: 100% !important;
      max-height: 100% !important;
      overflow: hidden;
    }

    /* Tablet / medium screens */
    @media (max-width: 991.98px) {
      body {
        padding-bottom: 68px !important; /* Space for Mobile Bottom Nav */
      }
      .hero-section {
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
      }
      .hero-section #heroCarousel,
      .hero-section #heroCarousel .carousel-inner,
      .hero-section #heroCarousel .carousel-item {
        height: 250px !important;
        max-height: 250px !important;
      }
      @media (min-width: 576px) and (max-width: 991.98px) {
        .hero-section #heroCarousel,
        .hero-section #heroCarousel .carousel-inner,
        .hero-section #heroCarousel .carousel-item {
          height: 330px !important;
          max-height: 330px !important;
        }
      }
      .hero-section .carousel-caption {
        padding: 10px 14px !important;
        background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 70%, transparent 100%) !important;
      }
      .hero-section .carousel-caption h2 {
        font-size: 1.15rem !important;
        margin-bottom: 2px !important;
        line-height: 1.25 !important;
      }
      .hero-section .carousel-caption p {
        font-size: 0.78rem !important;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }
      .section-pad {
        padding: 38px 0 !important;
      }
      .stats-bar {
        padding: 24px 0 !important;
      }
      .stat-item .num {
        font-size: 2.2rem !important;
      }
      .stat-item .label {
        font-size: 0.8rem !important;
      }
      .program-card img {
        height: 160px !important;
      }
      .ekskul-card {
        padding: 1rem 0.6rem !important;
      }
      .ekskul-card h5 {
        font-size: 0.82rem !important;
      }
    }

    /* Force Vertical Carousel Animation (Desktop Quote) */
    .carousel-vertical .carousel-inner {
      height: 100%;
    }

    .carousel-vertical .carousel-item {
      transition: transform 1.2s cubic-bezier(0.645, 0.045, 0.355, 1) !important;
      height: 100% !important;
      background-color: transparent !important;
      left: 0 !important;
      display: none;
      align-items: center;
      justify-content: center;
      width: 100%;
    }

    .carousel-vertical .carousel-item.active,
    .carousel-vertical .carousel-item-next,
    .carousel-vertical .carousel-item-prev {
      display: flex !important;
    }

    .carousel-vertical .carousel-item-next:not(.carousel-item-start),
    .carousel-vertical .active.carousel-item-end {
      transform: translateY(100%) !important;
    }

    .carousel-vertical .carousel-item-prev:not(.carousel-item-end),
    .carousel-vertical .active.carousel-item-start {
      transform: translateY(-100%) !important;
    }

    .carousel-vertical .carousel-item-next.carousel-item-start,
    .carousel-vertical .carousel-item-prev.carousel-item-end,
    .carousel-vertical .active {
      transform: translateY(0) !important;
    }

    /* Mobile Quick Announcement Ticker */
    .mobile-info-ticker {
      background: #0f172a;
      border-bottom: 2px solid #f59e0b;
      padding: 9px 14px;
      color: #fff;
    }
    .mobile-info-badge {
      background: #f59e0b;
      color: #0f172a;
      font-weight: 800;
      font-size: 0.68rem;
      padding: 3px 8px;
      border-radius: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      flex-shrink: 0;
    }
    .mobile-info-text {
      font-size: 0.78rem;
      color: #f1f5f9;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      text-decoration: none !important;
    }
    .mobile-info-text:hover {
      color: #fbbf24;
    }

    /* Mobile Quote Pill */
    .mobile-quote-pill {
      background: #1e293b;
      border-left: 3px solid #3b82f6;
      border-radius: 8px;
      padding: 10px 14px;
      margin: 12px 12px 0 12px;
      font-size: 0.8rem;
      color: #e2e8f0;
      font-style: italic;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Floating Bottom Navigation Bar (Mobile) */
    .mobile-bottom-nav {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      height: 62px;
      background: rgba(0, 0, 51, 0.96);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-top: 2px solid #f59e0b;
      z-index: 1040;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.25);
    }
    @media (max-width: 991.98px) {
      .mobile-bottom-nav {
        display: flex;
        align-items: center;
        justify-content: space-around;
      }
    }
    .mobile-nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none !important;
      font-size: 0.65rem;
      font-weight: 600;
      transition: all 0.2s ease;
      padding: 4px 6px;
      flex: 1;
      text-align: center;
    }
    .mobile-nav-item i {
      font-size: 1.15rem;
      margin-bottom: 2px;
    }
    .mobile-nav-item:hover,
    .mobile-nav-item:active,
    .mobile-nav-item.active {
      color: #fbbf24 !important;
    }
    .mobile-nav-spmb {
      position: relative;
      top: -12px;
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #000033 !important;
      width: 52px;
      height: 52px;
      border-radius: 50%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.62rem;
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.5);
      border: 3px solid #000033;
      text-decoration: none !important;
      transition: transform 0.2s ease;
      flex-shrink: 0;
    }
    .mobile-nav-spmb i {
      font-size: 1.25rem;
      margin-bottom: 1px;
    }
    .mobile-nav-spmb:hover,
    .mobile-nav-spmb:active {
      transform: scale(1.08);
      color: #000033 !important;
    }

    /* Floating WhatsApp Button */
    .floating-wa-btn {
      position: fixed;
      bottom: 24px;
      right: 24px;
      width: 54px;
      height: 54px;
      background: linear-gradient(135deg, #25d366, #128c7e);
      color: #fff !important;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.75rem;
      box-shadow: 0 4px 18px rgba(37, 211, 102, 0.45);
      z-index: 1030;
      text-decoration: none !important;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .floating-wa-btn:hover {
      transform: scale(1.1) translateY(-3px);
      box-shadow: 0 8px 24px rgba(37, 211, 102, 0.6);
      color: #fff !important;
    }
    @media (max-width: 991.98px) {
      .floating-wa-btn {
        bottom: 74px; /* Above bottom navigation */
        right: 16px;
        width: 48px;
        height: 48px;
        font-size: 1.55rem;
      }
    }
  </style>
</head>

<body>

  <?php $logoPath = !empty($config['school_logo']) ? BASE_URL . $config['school_logo'] : BASE_URL . 'assets/img/logo.png'; ?>

  <?php include __DIR__ . '/landing/navbar_landing.php'; ?>

  <!-- HERO: QUOTES + CAROUSEL + PENGUMUMAN SIDEBAR -->
  <section class="hero-section" id="home">
    <div class="container-fluid px-0 h-100">
      <div class="row g-0 h-100">
        <!-- Quotes Slider: ~16% (Left) -->
        <div
          class="col-lg-2 d-none d-lg-flex flex-column align-items-center justify-content-center p-3 text-center quote-slider-container"
          style="background: linear-gradient(135deg, #334155, #1e293b); color: white; border-right: 1px solid rgba(255,255,255,0.1);">


          <div id="quoteCarousel" class="carousel slide carousel-vertical h-100 w-100" data-bs-ride="carousel"
            data-bs-interval="5000">
            <div class="carousel-inner h-100">
              <?php
              $display_quotes = $data['quotes'] ?? [];
              if (empty($display_quotes) && !empty($config['landing_quote_text'])) {
                $display_quotes[] = [
                  'quote_text' => $config['landing_quote_text'],
                  'quote_source' => $config['landing_quote_source'] ?? ''
                ];
              }

              foreach ($display_quotes as $idx => $q): ?>
                <div class="carousel-item h-100 <?= $idx === 0 ? 'active' : '' ?>">
                  <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100">
                    <h6 class="fw-bold fst-italic mb-3 px-2" style="line-height:1.6; font-size:0.95rem;">
                      "<?= htmlspecialchars($q['quote_text']) ?>"
                    </h6>
                    <?php if (!empty($q['quote_source'])): ?>
                      <div class="small fw-bold text-warning"
                        style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                        — <?= htmlspecialchars($q['quote_source']) ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Carousel: ~66% (Center) -->
        <div class="col-lg-8 center-slider-col">
          <div id="heroCarousel" class="carousel slide h-100" data-bs-ride="carousel">
            <div class="carousel-inner h-100">
              <?php
              $slides = $data['hero_sliders'] ?? [];
              if (empty($slides)) {
                $slides = [
                  ['image_path' => 'assets/img/hero_1.jpg', 'title' => 'SELAMAT DATANG', 'description' => 'Di Official Website ' . ($config['school_name'] ?? 'SMA Plus Al-Manshuriyah')],
                  ['image_path' => 'assets/img/hero_2.jpg', 'title' => 'MENCETAK GENERASI QURANI', 'description' => 'Unggul dalam Imtaq, Terampil dalam Iptek'],
                ];
              }
              foreach ($slides as $i => $slide):
                ?>
                <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                  <img src="<?= BASE_URL . $slide['image_path'] ?>" alt="Slide" class="d-block w-100 h-100"
                    style="object-fit:cover; object-position:center;"
                    onerror="this.src='https://placehold.co/1200x600/2c3e50/fff?text=IMAGE+SCHOOL'">
                  <div class="carousel-caption">
                    <div class="container px-4">
                      <h2 style="font-size: 1.8rem;"><?= htmlspecialchars($slide['title']) ?></h2>
                      <p style="font-size: 0.95rem;"><?= htmlspecialchars($slide['description'] ?? '') ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
              data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
              data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
          </div>
        </div>

        <!-- Pengumuman Scroll: ~16% (Right) -->
        <div class="col-lg-2 d-none d-lg-flex flex-column pengumuman-sidebar"
          style="background:#1e293b;">
          <div class="px-3 pt-3 pb-2 border-bottom border-primary-subtle flex-shrink-0">
            <h6 class="text-white fw-bold mb-0" style="font-size:0.85rem;"><i
                class="fas fa-bullhorn me-2 text-warning"></i>PENGUMUMAN</h6>
          </div>
          <div class="flex-grow-1 overflow-y-auto px-2 py-2" style="overflow-y:auto;"
            id="pengumumanBox">
            <?php
            $infos = $data['informasi'] ?? [];
            if (empty($infos)):
              for ($j = 1; $j <= 5; $j++): ?>
                <div class="border-bottom border-white border-opacity-25 py-2">
                  <div class="small text-white-50" style="font-size:0.7rem;"><?= date('d M Y') ?></div>
                  <div class="text-white small fw-semibold" style="font-size:0.75rem;">Informasi Sekolah #<?= $j ?></div>
                </div>
              <?php endfor;
            else:
              foreach ($infos as $info): ?>
                <a href="<?= BASE_URL ?>landing/informasi_list"
                  class="d-block text-decoration-none border-bottom border-white border-opacity-25 py-2">
                  <div class="small text-white-50" style="font-size:0.7rem;">
                    <?= date('d M Y', strtotime($info['tanggal_publikasi'] ?? 'now')) ?>
                  </div>
                  <div class="text-white small fw-semibold" style="line-height:1.2; font-size:0.78rem;">
                    <?= htmlspecialchars($info['judul']) ?>
                  </div>
                </a>
              <?php endforeach;
            endif;
            ?>
          </div>
          <div class="px-2 py-2 flex-shrink-0">
            <a href="<?= BASE_URL ?>landing/informasi_list"
              class="btn btn-warning btn-sm w-100 rounded-0 fw-bold" style="font-size:0.75rem;">Lihat Semua &rsaquo;</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MOBILE ONLY: QUICK ANNOUNCEMENT TICKER & QUOTE -->
  <?php if (!empty($infos)): ?>
    <div class="d-block d-lg-none mobile-info-ticker">
      <div class="d-flex align-items-center gap-2">
        <span class="mobile-info-badge"><i class="fas fa-bullhorn me-1"></i>INFO</span>
        <a href="<?= BASE_URL ?>landing/informasi_list" class="mobile-info-text flex-grow-1">
          <?= htmlspecialchars($infos[0]['judul']) ?>
        </a>
        <a href="<?= BASE_URL ?>landing/informasi_list" class="text-warning text-decoration-none fw-bold" style="font-size:0.75rem; flex-shrink:0;">
          Lihat &rsaquo;
        </a>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($display_quotes)): ?>
    <div class="d-block d-lg-none mobile-quote-pill shadow-sm">
      <i class="fas fa-quote-left text-warning me-1"></i>
      <div>
        "<?= htmlspecialchars($display_quotes[0]['quote_text']) ?>"
        <?php if (!empty($display_quotes[0]['quote_source'])): ?>
          <span class="text-warning fw-bold d-block" style="font-size:0.7rem;">— <?= htmlspecialchars($display_quotes[0]['quote_source']) ?></span>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- LAYOUT 70/30 (Utama & Sidebar) -->
  <section class="section-pad bg-light">
    <div class="container">
      <!-- BARIS 1: PROFIL & SAMBUTAN (EQUAL HEIGHT) -->
      <div class="row d-flex align-items-stretch g-4">
        <!-- KOLOM PROFIL (70%) -->
        <div class="col-lg-8 d-flex flex-column mb-4 mb-lg-0">
          <div class="section-title mb-3">
            <h2>PROFIL SEKOLAH</h2>
          </div>
          <div class="bg-white border shadow-sm flex-grow-1 d-flex flex-column overflow-hidden"
            style="border-radius: 12px;">
            <?php
            $highlightPhoto = !empty($config['landing_school_profile_image']) ? BASE_URL . $config['landing_school_profile_image'] : $logoPath;
            $highlightExcerpt = !empty($config['landing_school_profile_excerpt']) ? $config['landing_school_profile_excerpt'] : ($config['school_description'] ?? 'Portal informasi resmi sekolah kami.');
            ?>
            <!-- Photo Wide at Top -->
            <div class="profile-banner"
              style="height: 240px; width: 100%; overflow: hidden; border-bottom: 4px solid #f8f9fa;">
              <img src="<?= $highlightPhoto ?>" alt="Highlight Profil" class="w-100 h-100" style="object-fit: cover;"
                onerror="this.src='<?= $logoPath ?>'">
            </div>

            <!-- Content Section -->
            <div class="p-4 d-flex flex-column flex-grow-1">
              <div class="text-muted mb-4 overflow-hidden"
                style="line-height: 1.8; font-size: 0.95rem; display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical;">
                <?= nl2br(htmlspecialchars($highlightExcerpt)) ?>
              </div>

              <!-- Footer Stats & Link -->
              <div class="mt-auto pt-4 border-top">
                <div class="row align-items-center g-3">
                  <div class="col-md-9">
                    <div class="row g-2 text-start">
                      <div class="col-sm-4">
                        <div class="d-flex gap-2 align-items-center">
                          <div class="bg-light p-2 border rounded-circle text-primary"
                            style="width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-size:0.8rem;">
                            <i class="fas fa-award"></i>
                          </div>
                          <div style="line-height: 1.1;">
                            <h6 class="fw-bold mb-0 text-muted" style="font-size:0.6rem; text-transform: uppercase;">
                              AKREDITASI</h6>
                            <span class="badge bg-primary"
                              style="font-size:0.65rem;"><?= htmlspecialchars($config['school_accreditation'] ?? 'A (Unggul)') ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="d-flex gap-2 align-items-center">
                          <div class="bg-light p-2 border rounded-circle text-success"
                            style="width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-size:0.8rem;">
                            <i class="fas fa-calendar-alt"></i>
                          </div>
                          <div style="line-height: 1.1;">
                            <h6 class="fw-bold mb-0 text-muted" style="font-size:0.6rem; text-transform: uppercase;">
                              BERDIRI</h6>
                            <span class="text-dark fw-bold"
                              style="font-size:0.75rem;"><?= htmlspecialchars($config['tahun_berdiri'] ?? '2010') ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="d-flex gap-2 align-items-center">
                          <div class="bg-light p-2 border rounded-circle text-warning"
                            style="width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-size:0.8rem;">
                            <i class="fas fa-edit"></i>
                          </div>
                          <div style="line-height: 1.1;">
                            <h6 class="fw-bold mb-0 text-muted" style="font-size:0.6rem; text-transform: uppercase;">
                              PERUBAHAN</h6>
                            <span class="text-dark fw-bold"
                              style="font-size:0.75rem;"><?= htmlspecialchars($config['tahun_perubahan'] ?? '2024') ?></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 text-end">
                    <a href="<?= BASE_URL ?>landing/profil_sekolah"
                      class="btn btn-outline-primary btn-sm rounded-pill px-4 shadow-sm" style="font-size: 0.75rem;">
                      Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- KOLOM SAMBUTAN (30%) -->
        <div class="col-lg-4 d-flex flex-column">
          <div class="sidebar-title mb-3">SAMBUTAN KEPALA SEKOLAH</div>
          <div class="card p-4 border shadow-sm text-center flex-grow-1 d-flex flex-column"
            style="border-radius: 12px; transition: transform 0.3s;">
            <?php $kepsekPath = !empty($config['headmaster_photo']) ? BASE_URL . $config['headmaster_photo'] : BASE_URL . 'assets/img/kepsek.jpg'; ?>
            <div class="mx-auto mb-3" style="width:130px; height:130px;">
              <img src="<?= $kepsekPath ?>" alt="Kepala Sekolah" class="img-fluid w-100 h-100 shadow-sm"
                style="object-fit:cover; border-radius:50%; border:4px solid #f8f9fa"
                onerror="this.src='https://placehold.co/300x400/2c3e50/fff?text=KEPSEK'">
            </div>
            <h6 class="fw-bold mb-1" style="font-size: 1.1rem; color: #1a237e;">
              <?= htmlspecialchars($config['headmaster_name'] ?? 'NAMA KEPALA SEKOLAH') ?>
            </h6>
            <small class="text-accent mb-3 d-block fw-bold text-uppercase"
              style="letter-spacing: 1px; font-size: 0.75rem;">Kepala
              <?= htmlspecialchars($config['school_name'] ?? 'SMA Plus Al-Manshuriyah') ?></small>

            <div class="flex-grow-1 d-flex align-items-center justify-content-center">
              <div class="small text-muted mb-0 fst-italic overflow-hidden"
                style="line-height: 1.6; font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical;">
                <?php
                $msg = $config['headmaster_message'] ?? 'Bersama-sama kita membangun generasi yang tidak hanya cerdas berilmu, tapi juga teguh dalam iman dan mulia dalam akhlaq.';
                echo '"' . nl2br(htmlspecialchars($msg)) . '"';
                ?>
              </div>
            </div>

            <div class="mt-4 pt-3 border-top">
              <a href="<?= BASE_URL ?>landing/profil_sekolah"
                class="btn btn-link btn-sm text-primary text-decoration-none fw-bold p-0">
                Lanjutkan Membaca <i class="fas fa-chevron-right ms-1" style="font-size: 0.8rem;"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- SPACER BARIS -->
      <div class="py-4"></div>

      <!-- BARIS 2: BERITA & SIDEBAR LAINNYA -->
      <div class="row g-4 mt-2">
        <!-- KOLOM BERITA (70%) -->
        <div class="col-lg-8 d-flex flex-column">
          <div id="informasi">
            <div class="section-title mb-4">
              <h2>INFORMASI TERBARU</h2>
            </div>
            <div class="d-flex flex-column gap-1">
              <?php
              $infos = $data['informasi'] ?? [];
              if (empty($infos)):
                for ($j = 1; $j <= 3; $j++): ?>
                  <div class="news-item-horizontal bg-white p-2 border shadow-sm rounded-3 d-flex gap-3 align-items-center"
                    style="transition: transform 0.3s, box-shadow 0.3s;">
                    <div class="news-img"
                      style="width: 140px; height: 90px; flex-shrink: 0; overflow: hidden; border-radius: 6px;">
                      <img src="https://placehold.co/400x300/f1f5f9/64748b?text=Informasi" alt="News" class="w-100 h-100"
                        style="object-fit: cover;">
                    </div>
                    <div class="news-content">
                      <div class="meta text-muted mb-1" style="font-size: 0.75rem;">
                        <i class="far fa-calendar-alt me-1 text-accent"></i> <?= date('d M Y') ?>
                      </div>
                      <h5 class="fw-bold mb-1" style="font-size: 0.9rem;"><a href="#"
                          class="text-dark text-decoration-none">Update Informasi Sekolah</a></h5>
                      <p class="small text-muted mb-0" style="font-size: 0.8rem; line-height: 1.4;">Informasi terkini
                        kegiatan belajar mengajar...</p>
                    </div>
                  </div>
                <?php endfor;
              else:
                foreach (array_slice($infos, 0, 4) as $info): ?>
                  <div
                    class="news-item-horizontal bg-white p-2 border shadow-sm rounded-3 d-flex gap-3 align-items-center mb-1"
                    style="transition: transform 0.3s; cursor: pointer;">
                    <div class="news-img"
                      style="width: 140px; height: 90px; flex-shrink: 0; overflow: hidden; border-radius: 6px;">
                      <?php
                      $newsImg = !empty($info['gambar']) ? BASE_URL . $info['gambar'] : 'https://placehold.co/400x300/f1f5f9/64748b?text=SIMAKS';
                      ?>
                      <img src="<?= $newsImg ?>" alt="News" class="w-100 h-100" style="object-fit: cover;"
                        onerror="this.src='https://placehold.co/400x300/f1f5f9/64748b?text=Informasi'">
                    </div>
                    <div class="news-content">
                      <div class="meta text-muted mb-1" style="font-size: 0.75rem;">
                        <i class="far fa-calendar-alt me-1 text-accent"></i>
                        <?= date('d M Y', strtotime($info['tanggal_publikasi'] ?? 'now')) ?>
                      </div>
                      <h5 class="fw-bold mb-1" style="font-size: 0.95rem;">
                        <a href="<?= BASE_URL ?>landing/informasi_detail?id=<?= $info['id'] ?>"
                          class="text-dark text-decoration-none hover-accent">
                          <?= htmlspecialchars($info['judul']) ?>
                        </a>
                      </h5>
                      <p class="small text-muted mb-0"
                        style="font-size: 0.8rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <?= htmlspecialchars(strip_tags($info['konten'])) ?>
                      </p>
                    </div>
                  </div>
                <?php endforeach;
              endif;
              ?>
            </div>
            <div class="text-end mt-4">
              <a href="<?= BASE_URL ?>landing/informasi_list"
                class="btn btn-outline-primary btn-sm rounded-pill px-4" style="font-size: 0.8rem;">LIHAT SEMUA
                BERITA</a>
            </div>
          </div>
        </div>

        <!-- KOLOM SIDEBAR LANJUTAN (30%) -->
        <div class="col-lg-4 d-flex flex-column">
          <!-- Tautan Cepat -->
          <div class="sidebar-title mb-4">TAUTAN PENTING</div>
          <div class="card mb-5 border-0 shadow-sm rounded-3 overflow-hidden flex-grow-1">
            <div class="list-group list-group-flush">
              <?php
              $quick_links = $data['tautan_penting'] ?? [];
              foreach ($quick_links as $link):
                ?>
                <a href="<?= htmlspecialchars($link['url']) ?>"
                  class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                  <div
                    class="bg-light p-2 rounded-circle me-3 text-primary d-flex align-items-center justify-content-center"
                    style="width: 35px; height: 35px; flex-shrink: 0;">
                    <i class="<?= htmlspecialchars($link['icon']) ?> fa-sm"></i>
                  </div>
                  <span class="fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars($link['title']) ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Medsos -->
          <div class="sidebar-title mb-4">IKUTI KAMI</div>
          <div class="card p-4 border shadow-sm rounded-3 bg-white">
            <div class="d-flex justify-content-center gap-3">
              <?php
              $socials = [
                'facebook' => ['icon' => 'fab fa-facebook-f', 'url' => $config['facebook_url'] ?? ''],
                'instagram' => ['icon' => 'fab fa-instagram', 'url' => $config['instagram_url'] ?? ''],
                'youtube' => ['icon' => 'fab fa-youtube', 'url' => $config['youtube_url'] ?? ''],
                'whatsapp' => ['icon' => 'fab fa-whatsapp', 'url' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $config['whatsapp_sekolah'] ?? '')]
              ];

              foreach ($socials as $key => $social):
                if (empty($social['url']))
                  continue;
                $link = $social['url'];
                if (strpos($link, 'http') !== 0 && $key != 'whatsapp') {
                  $link = 'https://' . ltrim($link, '/');
                }
                ?>
                <a href="<?= $link ?>" target="_blank" class="social-circle shadow-sm"
                  style="background: #f8f9fa; color: #1a237e; border: 1px solid #e2e8f0; transition: all 0.3s; width: 45px; height: 45px; display: flex; align-items:center; justify-content:center; border-radius: 50%;">
                  <i class="<?= $social['icon'] ?>"></i>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- STATS BAR -->
  <section class="stats-bar">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="stat-item">
            <span class="num" data-target="<?= $stats['total_siswa'] ?? 0 ?>">0</span>
            <span class="label">Total Siswa Aktif</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-item">
            <span class="num" data-target="<?= $stats['total_gtk'] ?? 0 ?>">0</span>
            <span class="label">Guru & Staff</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-item">
            <span class="num" data-target="2">0</span>
            <span class="label">Program Keahlian</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- PROGRAM UNGGULAN -->
  <section class="section-pad bg-light" id="program">
    <div class="container">
      <div class="section-title center">
        <h2>PROGRAM UNGGULAN</h2>
      </div>
      <div class="row g-4">
        <?php foreach ($data['programs'] ?? [] as $prog): ?>
          <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm program-card" style="transition: transform 0.3s; cursor: pointer;"
              onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
              <a href="<?= BASE_URL ?>landing/program_detail?id=<?= $prog['id'] ?>"
                class="text-decoration-none text-dark d-flex flex-column h-100">
                <?php if (!empty($prog['image'])): ?>
                  <img src="<?= BASE_URL . $prog['image'] ?>" class="card-img-top"
                    alt="<?= htmlspecialchars($prog['title']) ?>" style="height: 200px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body text-center p-4 d-flex flex-column flex-grow-1">
                  <?php if (!empty($prog['icon'])): ?>
                    <div class="mb-3 text-primary">
                      <i class="<?= htmlspecialchars($prog['icon']) ?> fa-3x"></i>
                    </div>
                  <?php endif; ?>
                  <h4 class="fw-bold mb-3 hover-accent"><?= htmlspecialchars($prog['title']) ?></h4>
                  <p class="text-muted flex-grow-1 mb-0"><?= nl2br(htmlspecialchars($prog['description'] ?? '')) ?></p>
                </div>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($data['programs'])): ?>
          <div class="col-12 text-center text-muted">Belum ada data program unggulan.</div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- EKSTRAKURIKULER -->
  <section class="section-pad" id="ekskul">
    <div class="container">
      <div class="section-title center">
        <h2>EKSTRAKURIKULER</h2>
      </div>
      <div class="row g-4 justify-content-center">
        <?php foreach ($data['ekskul'] ?? [] as $eks): ?>
          <div class="col-lg-3 col-md-4 col-6 text-center">
            <div class="card border-0 shadow-sm h-100 p-4 ekskul-card">
              <a href="<?= BASE_URL ?>landing/ekstrakurikuler_detail?id=<?= $eks['id'] ?>"
                class="text-decoration-none text-dark d-block h-100">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                  style="width: 80px; height: 80px; overflow: hidden; transition: transform 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                  <?php if (!empty($eks['image_path'])): ?>
                    <img src="<?= BASE_URL . $eks['image_path'] ?>" alt="<?= htmlspecialchars($eks['nama']) ?>"
                      style="width: 100%; height: 100%; object-fit: cover;">
                  <?php else: ?>
                    <i class="<?= htmlspecialchars($eks['icon_class'] ?? 'fas fa-star') ?> fa-2x text-accent"></i>
                  <?php endif; ?>
                </div>
                <h5 class="fw-bold fs-6 hover-accent"><?= htmlspecialchars($eks['nama']) ?></h5>
                <?php if (!empty($eks['jadwal'])): ?>
                  <small class="text-muted d-block mt-2"><i class="far fa-clock me-1"></i>
                    <?= htmlspecialchars($eks['jadwal']) ?></small>
                <?php endif; ?>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($data['ekskul'])): ?>
          <div class="col-12 text-center text-muted">Belum ada data ekstrakurikuler.</div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- GALERI & VIDEO (Grid) -->
  <section class="section-pad" id="galeri">
    <div class="container">
      <div class="section-title center">
        <h2>GALERI & VIDEO KEGIATAN</h2>
      </div>
      <div class="row g-3">
        <?php foreach (array_slice($data['gallery'] ?? [], 0, 3) as $gal): ?>
          <div class="col-lg-4 col-md-6 col-12">
            <div class="card border-0 overflow-hidden" style="height:200px;">
              <a href="<?= BASE_URL . $gal['image_path'] ?>" target="_blank" class="d-block w-100 h-100">
                <img src="<?= BASE_URL . $gal['image_path'] ?>" alt="<?= htmlspecialchars($gal['title']) ?>"
                  class="w-100 h-100 object-fit-cover shadow-sm hover-zoom">
              </a>
            </div>
          </div>
        <?php endforeach; ?>
        <?php foreach (array_slice($data['videos'] ?? [], 0, 3) as $vid): ?>
          <div class="col-lg-4 col-md-6 col-12">
            <div class="card border-0 overflow-hidden bg-dark" style="height:200px;">
              <a href="<?= htmlspecialchars($vid['video_url']) ?>" target="_blank"
                class="d-block w-100 h-100 position-relative">
                <?php if (!empty($vid['thumbnail'])): ?>
                  <img src="<?= htmlspecialchars($vid['thumbnail']) ?>" alt="<?= htmlspecialchars($vid['judul']) ?>"
                    class="w-100 h-100 object-fit-cover shadow-sm opacity-75 hover-zoom">
                <?php endif; ?>
                <div class="position-absolute top-50 start-50 translate-middle">
                  <i class="fas fa-play-circle fa-4x text-white mb-2 shadow-sm"
                    style="filter: drop-shadow(0 0 10px rgba(0,0,0,0.5));"></i>
                </div>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($data['gallery']) && empty($data['videos'])): ?>
          <div class="col-12 text-center text-muted">Belum ada data galeri maupun video.</div>
        <?php endif; ?>
      </div>
      <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>landing/gallery" class="btn btn-outline-primary btn-sm px-4">LIHAT SEMUA FOTO</a>
        <a href="<?= BASE_URL ?>landing/video_list" class="btn btn-outline-accent btn-sm px-4 ms-2">LIHAT
          VIDEO</a>
      </div>
    </div>
  </section>

  

  <?php include __DIR__ . '/landing/footer_premium.php'; ?>

  <button id="scrollTop" title="Ke Atas"><i class="fas fa-chevron-up"></i></button>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // 1. Touch Swipe Gesture Support for Carousel (Mobile & Tablet)
    const heroCarousel = document.getElementById('heroCarousel');
    if (heroCarousel) {
      let touchStartX = 0;
      let touchEndX = 0;
      heroCarousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
      }, { passive: true });
      heroCarousel.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleHeroSwipe();
      }, { passive: true });

      function handleHeroSwipe() {
        const threshold = 45;
        const bsCarousel = bootstrap.Carousel.getOrCreateInstance(heroCarousel);
        if (touchEndX < touchStartX - threshold) {
          bsCarousel.next(); // swipe left -> next slide
        }
        if (touchEndX > touchStartX + threshold) {
          bsCarousel.prev(); // swipe right -> prev slide
        }
      }
    }

    // 2. Stats Counter
    const counters = document.querySelectorAll('.num');
    const speed = 200;
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const target = +entry.target.getAttribute('data-target');
          const count = +entry.target.innerText;
          const inc = target / speed;
          const updateCount = () => {
            const current = +entry.target.innerText;
            if (current < target) {
              entry.target.innerText = Math.ceil(current + inc);
              setTimeout(updateCount, 1);
            } else { entry.target.innerText = target; }
          };
          updateCount(); observer.unobserve(entry.target);
        }
      });
    }, { threshold: 1 });
    counters.forEach(counter => observer.observe(counter));

    // 3. Scroll Top
    window.addEventListener('scroll', () => {
      document.getElementById('scrollTop').classList.toggle('show', window.scrollY > 500);
    });
    document.getElementById('scrollTop').onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
  </script>
</body>

</html>