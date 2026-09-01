<?php include __DIR__ . "/partials/header.php";

$siswa              = $data["siswa"]              ?? [];
$kelas              = $data["kelas"]              ?? [];
$absensi            = $data["absensi"]            ?? ["hadir" => 0, "sakit" => 0, "izin" => 0, "alpa" => 0];
$wali_kelas         = $data["wali_kelas"]         ?? "-";
$hari_ini           = $data["hari_ini"]           ?? "Senin";
$jadwal_hari_ini    = $data["jadwal_hari_ini"]    ?? [];
$tugas_pending_list = $data["tugas_pending_list"] ?? [];
$materi_terbaru     = $data["materi_terbaru"]     ?? [];

// Hitung status KBM Real-time hari ini
$now_time = date('H:i:s');
$current_kbm = null;
$next_kbm = null;
$kbm_status_type = 'no_kbm'; // 'active_kbm', 'active_non_kbm', 'break_between', 'upcoming', 'finished', 'no_kbm'

if (!empty($jadwal_hari_ini)) {
    $first_session = $jadwal_hari_ini[0];
    $last_session = end($jadwal_hari_ini);
    
    if ($now_time < $first_session['jam_mulai']) {
        $kbm_status_type = 'upcoming';
        $next_kbm = $first_session;
    } elseif ($now_time > $last_session['jam_selesai']) {
        $kbm_status_type = 'finished';
    } else {
        foreach ($jadwal_hari_ini as $sesi) {
            if ($now_time >= $sesi['jam_mulai'] && $now_time <= $sesi['jam_selesai']) {
                $current_kbm = $sesi;
                $is_kbm = (($sesi['jenis_jam'] ?? '') === 'KBM' || ($sesi['jenis_kegiatan'] ?? '') === 'KBM');
                $kbm_status_type = $is_kbm ? 'active_kbm' : 'active_non_kbm';
                break;
            } elseif ($now_time < $sesi['jam_mulai']) {
                if ($next_kbm === null) {
                    $next_kbm = $sesi;
                }
            }
        }
        if ($current_kbm === null && $next_kbm !== null) {
            $kbm_status_type = 'break_between';
        }
    }
}

$hari_id = ["Sunday"=>"Minggu","Monday"=>"Senin","Tuesday"=>"Selasa","Wednesday"=>"Rabu","Thursday"=>"Kamis","Friday"=>"Jumat","Saturday"=>"Sabtu"];
$bln_id  = [1=>"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
?>
<style>
/* ============ LAYOUT ============ */
.portal-container { padding-top: 14px; padding-bottom: 28px; padding-left: 15px; padding-right: 15px; }

/* ============ HERO CAROUSEL SLIDER ============ */
.hero-slider-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0,0,0,0.12);
    border: 1px solid rgba(255,255,255,0.12);
    margin-bottom: 18px;
    background: #0f172a;
}
.hero-slider-card .carousel-inner {
    border-radius: 16px;
}
.hero-slider-card .carousel-item {
    min-height: 135px;
}
.hero-slider-card .carousel-indicators {
    bottom: 3px;
    margin-bottom: 0;
}
.hero-slider-card .carousel-indicators li {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin: 0 3px;
    background-color: rgba(255,255,255,0.4);
    border: none;
    transition: all 0.2s ease;
}
.hero-slider-card .carousel-indicators li.active {
    width: 20px;
    border-radius: 8px;
    background-color: #ffffff;
}
.hero-slide-content {
    background: linear-gradient(135deg, #091e3a 0%, #173b6c 50%, #1f4068 100%);
    color: #ffffff;
    padding: 1.15rem 1.6rem;
    min-height: 135px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
}
.hero-slide-kbm {
    background: linear-gradient(135deg, #16193b 0%, #252850 50%, #1e293b 100%);
}
.hero-title {
    font-size: 1.22rem;
    font-weight: 800;
    letter-spacing: -0.3px;
    line-height: 1.3;
    color: #ffffff !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.4);
}
.hero-desc {
    font-size: 0.83rem;
    color: #e2e8f0;
    line-height: 1.45;
}
.hero-clock-box {
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 12px;
    padding: 8px 14px;
    text-align: center;
    backdrop-filter: blur(10px);
    min-width: 175px;
    flex-shrink: 0;
}
.hero-clock-time {
    font-size: 1.55rem;
    font-weight: 800;
    line-height: 1;
    color: #ffffff;
    letter-spacing: -0.5px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
.hero-clock-date {
    font-size: 0.72rem;
    color: #f1f5f9;
    margin-top: 3px;
    font-weight: 600;
}
.hero-clock-label {
    font-size: 0.62rem;
    color: #fde047;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-top: 2px;
}

/* ============ 8 STAT CARDS ============ */
.stat-card-col { margin-bottom: 12px; }
.stat-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 118px;
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
    color: inherit;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.08);
    text-decoration: none;
    color: inherit;
}
.stat-card-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.9rem 1rem 0.7rem;
    flex: 1;
    gap: 8px;
}
.stat-number {
    font-size: 1.6rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 3px;
}
.stat-label {
    font-size: 0.70rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin: 0;
}
.stat-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #fff;
    flex-shrink: 0;
}
.stat-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.4rem 1rem;
    font-size: 0.68rem;
    font-weight: 600;
    border-top: 1px solid #f1f5f9;
    background: #fafbfc;
    text-decoration: none;
    transition: background .2s;
}
.stat-card-footer:hover { background:#f1f5f9; text-decoration:none; }

.sc--mapel { border-top:4px solid #3b82f6; }
.sc--tugas { border-top:4px solid #f97316; }
.sc--done  { border-top:4px solid #10b981; }
.sc--nilai { border-top:4px solid #8b5cf6; }
.sc--hadir { border-top:4px solid #10b981; }
.sc--sakit { border-top:4px solid #06b6d4; }
.sc--izin  { border-top:4px solid #f59e0b; }
.sc--alpa  { border-top:4px solid #ef4444; }

.sc--mapel .stat-card-icon { background:linear-gradient(135deg,#3b82f6,#1d4ed8); }
.sc--tugas .stat-card-icon { background:linear-gradient(135deg,#f97316,#c2410c); }
.sc--done  .stat-card-icon { background:linear-gradient(135deg,#10b981,#047857); }
.sc--nilai .stat-card-icon { background:linear-gradient(135deg,#8b5cf6,#6d28d9); }
.sc--hadir .stat-card-icon { background:linear-gradient(135deg,#10b981,#047857); }
.sc--sakit .stat-card-icon { background:linear-gradient(135deg,#06b6d4,#0e7490); }
.sc--izin  .stat-card-icon { background:linear-gradient(135deg,#f59e0b,#b45309); }
.sc--alpa  .stat-card-icon { background:linear-gradient(135deg,#ef4444,#b91c1c); }

.sc--mapel .stat-number { color:#2563eb; } .sc--mapel .stat-card-footer { color:#2563eb; }
.sc--tugas .stat-number { color:#ea580c; } .sc--tugas .stat-card-footer { color:#ea580c; }
.sc--done  .stat-number { color:#059669; } .sc--done  .stat-card-footer { color:#059669; }
.sc--nilai .stat-number { color:#7c3aed; } .sc--nilai .stat-card-footer { color:#7c3aed; }
.sc--hadir .stat-number { color:#059669; } .sc--hadir .stat-card-footer { color:#059669; }
.sc--sakit .stat-number { color:#0891b2; } .sc--sakit .stat-card-footer { color:#0891b2; }
.sc--izin  .stat-number { color:#d97706; } .sc--izin  .stat-card-footer { color:#d97706; }
.sc--alpa  .stat-number { color:#dc2626; } .sc--alpa  .stat-card-footer { color:#dc2626; }

/* ============ 3 BOTTOM CARDS — EQUAL WIDTH (col-4) + EQUAL HEIGHT ============ */
.bottom-row { display:flex; align-items:stretch; margin-bottom:20px; }

.bottom-col {
    flex:0 0 33.333%;
    max-width:33.333%;
    padding-left:7px;
    padding-right:7px;
    display:flex;
    flex-direction:column;
}
.bottom-col:first-child { padding-left:0; }
.bottom-col:last-child  { padding-right:0; }

@media (max-width:991.98px) {
    .bottom-col { flex:0 0 100%; max-width:100%; padding:0; margin-bottom:14px; }
}

/* MOBILE RESPONSIVENESS */
@media (max-width: 768px) {
    .portal-container { padding-left: 4px !important; padding-right: 4px !important; padding-top: 6px !important; }
    .hero-slider-card { border-radius: 12px !important; margin-bottom: 10px !important; }
    .hero-slider-card .carousel-item { min-height: 135px !important; height: auto !important; }
    .hero-slide-content { padding: 0.75rem 0.85rem !important; min-height: 135px !important; }
    .hero-title { font-size: 0.95rem !important; margin-bottom: 2px !important; }
    .hero-desc { font-size: 0.70rem !important; }
    .hero-clock-box {
        min-width: 100% !important;
        padding: 4px 10px !important;
        margin-top: 4px !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 8px !important;
    }
    .hero-clock-time { font-size: 1.15rem !important; }
    .hero-clock-date { margin-top: 0 !important; font-size: 0.65rem !important; text-align: right; }
    .hero-clock-label { display: none; }
    
    .stat-card-col {
        flex: 0 0 50% !important;
        max-width: 50% !important;
        padding-left: 3px !important;
        padding-right: 3px !important;
        margin-bottom: 6px !important;
    }
    .stat-card {
        min-height: 80px !important;
        border-radius: 8px !important;
    }
    .stat-card-body {
        padding: 6px 8px 4px !important;
    }
    .stat-number {
        font-size: 1.15rem !important;
        margin-bottom: 1px !important;
    }
    .stat-label {
        font-size: 0.58rem !important;
        letter-spacing: 0.1px !important;
    }
    .stat-card-icon {
        width: 26px !important;
        height: 26px !important;
        font-size: 0.75rem !important;
        border-radius: 6px !important;
    }
    .stat-card-footer {
        padding: 2px 6px !important;
        font-size: 0.58rem !important;
    }
    .bottom-col {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin-bottom: 10px !important;
    }
    .profil-card, .akses-card, .jadwal-card {
        border-radius: 10px !important;
    }
    .profil-card-banner {
        padding: 0.9rem 0.8rem !important;
    }
    .profil-avatar-wrap {
        width: 54px !important;
        height: 54px !important;
    }
    .profil-avatar-wrap img {
        width: 48px !important;
        height: 48px !important;
    }
    .profil-card-name {
        font-size: 0.88rem !important;
    }
    .profil-card-body {
        padding: 0.7rem 0.8rem !important;
    }
    .profil-val {
        font-size: 0.76rem !important;
    }
    .akses-card-hdr, .jadwal-card-hdr {
        padding: 0.7rem 0.9rem !important;
    }
    .jadwal-table thead th {
        padding: 6px 8px !important;
        font-size: 0.65rem !important;
    }
    .jadwal-table tbody td {
        padding: 6px 8px !important;
        font-size: 0.72rem !important;
    }
}

/* --- Profil card --- */
.profil-card {
    background:#fff; border-radius:14px;
    box-shadow:0 4px 16px rgba(0,0,0,.06);
    border:1px solid rgba(0,0,0,.04);
    overflow:hidden; display:flex; flex-direction:column; flex:1;
}
.profil-card-banner {
    background:linear-gradient(135deg,#0f2027 0%,#203a43 50%,#2c5364 100%);
    padding:1.4rem 1rem; text-align:center; flex-shrink:0;
}
.profil-avatar-wrap {
    display:inline-flex; align-items:center; justify-content:center;
    width:70px; height:70px; border-radius:50%;
    background:rgba(255,255,255,.15); border:3px solid rgba(255,255,255,.35);
    margin-bottom:.5rem; overflow:hidden;
}
.profil-avatar-wrap img { width:64px; height:64px; object-fit:cover; border-radius:50%; }
.profil-card-name  { color:#fff; font-weight:700; font-size:.98rem; margin-bottom:2px; }
.profil-card-nisn  { color:rgba(255,255,255,.72); font-size:.76rem; margin:0; }
.profil-card-body  { padding:1rem 1.1rem; flex:1; display:flex; flex-direction:column; justify-content:space-around; }
.profil-row {
    display:flex; align-items:center; gap:10px;
    padding:.48rem 0; border-bottom:1px solid #f1f5f9;
}
.profil-row:last-child { border-bottom:none; }
.profil-icon {
    width:30px; height:30px; border-radius:8px;
    display:flex; align-items:center; justify-content:center;
    font-size:.77rem; color:#fff; flex-shrink:0;
}
.profil-lbl  { font-size:.67rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.4px; line-height:1; margin-bottom:2px; }
.profil-val  { font-size:.84rem; color:#1e293b; font-weight:600; }

/* --- Akses Cepat card --- */
.akses-card {
    background:#fff; border-radius:14px;
    box-shadow:0 4px 16px rgba(0,0,0,.06);
    border:1px solid rgba(0,0,0,.04);
    overflow:hidden; display:flex; flex-direction:column; flex:1;
}
.akses-card-hdr {
    background:linear-gradient(135deg,#1e293b,#0f172a);
    padding:.9rem 1.2rem; display:flex; align-items:center; gap:10px; flex-shrink:0;
}
.akses-hdr-ico {
    width:30px; height:30px; background:rgba(255,255,255,.14);
    border-radius:8px; display:flex; align-items:center;
    justify-content:center; font-size:.88rem; color:#fff;
}
.akses-card-hdr h6 { color:#fff; font-weight:700; margin:0; font-size:.86rem; letter-spacing:.3px; }
/* 3-baris x 2-kolom grid untuk akses cepat */
.akses-card-body {
    padding:1rem 1.1rem;
    flex:1;
    display:grid;
    grid-template-columns:1fr 1fr;
    grid-template-rows:repeat(3,1fr);
    gap:9px;
}
.akses-btn {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:7px;
    padding:12px 8px;
    border-radius:11px;
    text-decoration:none !important;
    font-weight:600;
    font-size:.76rem;
    text-align:center;
    transition:transform .15s, box-shadow .15s;
    line-height:1.2;
}
.akses-btn:hover { transform:translateY(-2px); box-shadow:0 5px 14px rgba(0,0,0,.15); }
.akses-btn i { font-size:1.35rem; }
.akses-btn--jadwal  { background:linear-gradient(135deg,#6366f1,#4f46e5); color:#fff; }
.akses-btn--materi  { background:linear-gradient(135deg,#0ea5e9,#0284c7); color:#fff; }
.akses-btn--tugas   { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; }
.akses-btn--nilai   { background:linear-gradient(135deg,#8b5cf6,#7c3aed); color:#fff; }
.akses-btn--absensi { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }
.akses-btn--izin    { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; }

/* --- Jadwal card --- */
.jadwal-card {
    background:#fff; border-radius:14px;
    box-shadow:0 4px 16px rgba(0,0,0,.06);
    border:1px solid rgba(0,0,0,.04);
    overflow:hidden; display:flex; flex-direction:column; flex:1;
}
.jadwal-card-hdr {
    background:#fff; border-bottom:1px solid #f1f5f9;
    padding:.95rem 1.2rem; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;
}
.jadwal-card-title { font-size:.93rem; font-weight:700; color:#1e293b; margin:0; }
.jadwal-card-body  { padding:0; flex:1; overflow-y:auto; }

.jadwal-table { margin:0; }
.jadwal-table thead th {
    background:#1e293b; color:#fff;
    font-weight:600; text-transform:uppercase;
    font-size:.72rem; letter-spacing:.5px;
    padding:10px 12px; border:1px solid rgba(255,255,255,.08);
    vertical-align:middle; white-space:nowrap;
}
.jadwal-table tbody td {
    padding:11px 12px; vertical-align:middle;
    color:#334155; border:1px solid #f1f5f9; font-size:.82rem;
}
.jadwal-table tbody tr:nth-of-type(odd)  { background:#fafbfc; }
.jadwal-table tbody tr:hover { background:#eef2fb; transition:background .15s; }

.jam-badge {
    display:inline-flex; align-items:center; justify-content:center;
    width:30px; height:30px; border-radius:8px;
    background:#1e293b; color:#fff; font-size:.78rem; font-weight:700;
}
.waktu-badge {
    display:inline-block; background:#f1f5f9; color:#475569;
    font-size:.74rem; font-weight:600; border-radius:6px;
    padding:3px 7px; white-space:nowrap;
}
.guru-badge {
    display:inline-block; margin-top:4px;
    background:#e0f2fe; color:#0369a1;
    font-size:.72rem; font-weight:600; border-radius:5px;
    padding:2px 7px;
}
.guru-badge i { font-size:.68rem; margin-right:3px; }

.jadwal-empty {
    display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    padding:3rem 1rem; text-align:center; flex:1;
}
</style>

<div class="portal-container container-fluid">

  <!-- ===== HERO SLIDER CAROUSEL (SELAMAT DATANG + JAM SERVER + LIVE KBM) ===== -->
  <div id="heroDashboardCarousel" class="carousel slide hero-slider-card" data-ride="carousel" data-interval="7000">
    <ol class="carousel-indicators">
      <li data-target="#heroDashboardCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#heroDashboardCarousel" data-slide-to="1"></li>
    </ol>
    <div class="carousel-inner">

      <!-- SLIDE 1: SELAMAT DATANG & JAM SERVER LIVE -->
      <div class="carousel-item active">
        <div class="hero-slide-content">
          <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between h-100" style="gap: 12px;">
            <div class="hero-text-left">
              <span class="badge badge-warning px-2 py-1 font-weight-bold text-uppercase mb-2 d-inline-block shadow-sm" style="font-size: 0.68rem; background: #fbbf24; color: #78350f; border-radius: 6px;">
                <i class="fas fa-graduation-cap mr-1"></i> SIMAKS PORTAL SISWA
              </span>
              <h3 class="hero-title mb-1">
                Selamat Datang, <?= htmlspecialchars($siswa["nama"] ?? $_SESSION["nama_pengguna"] ?? "Siswa"); ?>! 👋
              </h3>
              <p class="hero-desc mb-0">
                Kelas <span class="badge badge-light text-dark font-weight-bold px-2 py-0" style="font-size: 0.72rem;"><?= htmlspecialchars($kelas["nama_kelas"] ?? "-"); ?></span> &bull; 
                TA <strong><?= htmlspecialchars($_SESSION["nama_ta_aktif"] ?? "2026/2027 Ganjil"); ?></strong> &bull; 
                Wali Kelas: <strong class="text-white"><?= htmlspecialchars($wali_kelas); ?></strong>
              </p>
            </div>
            
            <div class="hero-clock-box">
              <div class="hero-clock-time" id="digitalClock"><?= date("H:i:s"); ?></div>
              <div class="hero-clock-date">
                <?= $hari_id[date("l")].", ".date("d")." ".$bln_id[(int)date("m")]." ".date("Y"); ?>
              </div>
              <div class="hero-clock-label">
                <i class="fas fa-clock text-warning mr-1"></i> Waktu Server SIMAKS
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SLIDE 2: JADWAL & STATUS KBM REAL-TIME -->
      <div class="carousel-item">
        <div class="hero-slide-content hero-slide-kbm">
          <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between h-100" style="gap: 12px;">
            <div class="hero-text-left flex-grow-1" style="min-width: 0;">
              <div class="d-flex align-items-center justify-content-between flex-wrap mb-2" style="gap: 6px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                  <span class="badge badge-light px-2 py-1 font-weight-bold text-dark" style="font-size: 0.68rem; border-radius: 6px;">
                    <i class="fas fa-calendar-day mr-1 text-primary"></i> Hari <?= $hari_ini ?>
                  </span>
                  <?php if ($kbm_status_type === 'active_kbm'): ?>
                    <span class="badge badge-success px-2 py-1 font-weight-bold shadow-sm" style="font-size: 0.68rem; border-radius: 6px;">
                      <i class="fas fa-dot-circle mr-1 text-white"></i> Sedang KBM
                    </span>
                  <?php elseif ($kbm_status_type === 'active_non_kbm'): ?>
                    <span class="badge badge-warning px-2 py-1 font-weight-bold text-dark shadow-sm" style="font-size: 0.68rem; border-radius: 6px;">
                      <i class="fas fa-coffee mr-1"></i> <?= htmlspecialchars($current_kbm['nama_kegiatan'] ?? 'Non-KBM') ?>
                    </span>
                  <?php elseif ($kbm_status_type === 'break_between'): ?>
                    <span class="badge badge-info px-2 py-1 font-weight-bold shadow-sm" style="font-size: 0.68rem; border-radius: 6px;">
                      <i class="fas fa-exchange-alt mr-1"></i> Jeda Jam
                    </span>
                  <?php elseif ($kbm_status_type === 'upcoming'): ?>
                    <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 0.68rem; border-radius: 6px;">
                      <i class="fas fa-clock mr-1"></i> Belum Mulai
                    </span>
                  <?php elseif ($kbm_status_type === 'finished'): ?>
                    <span class="badge badge-light px-2 py-1 font-weight-bold text-dark" style="font-size: 0.68rem; border-radius: 6px;">
                      <i class="fas fa-check-circle mr-1 text-success"></i> KBM Selesai
                    </span>
                  <?php else: ?>
                    <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 0.68rem; border-radius: 6px;">
                      <i class="fas fa-mug-hot mr-1"></i> Belajar Mandiri
                    </span>
                  <?php endif; ?>
                </div>

                <a href="<?= BASE_URL ?>siswa_portal/jadwal" class="badge badge-light-glass px-2 py-1 font-weight-bold text-white text-decoration-none shadow-sm" style="font-size: 0.68rem; border-radius: 20px; border: 1px solid rgba(255,255,255,0.3);">
                  Lihat Jadwal <i class="fas fa-arrow-right ml-1 text-warning"></i>
                </a>
              </div>

              <?php if ($kbm_status_type === 'active_kbm'): ?>
                <h3 class="hero-title mb-1 text-warning">
                  <?= htmlspecialchars($current_kbm['nama_mapel'] ?? 'Mata Pelajaran') ?>
                </h3>
                <p class="hero-desc mb-0">
                  <i class="fas fa-clock mr-1"></i> Pukul <?= substr($current_kbm['jam_mulai'], 0, 5) ?> &ndash; <?= substr($current_kbm['jam_selesai'], 0, 5) ?> &bull; 
                  <i class="fas fa-chalkboard-teacher mr-1"></i> Guru: <strong><?= htmlspecialchars($current_kbm['nama_guru'] ?? '-') ?></strong>
                </p>
              <?php elseif ($kbm_status_type === 'active_non_kbm'): ?>
                <h3 class="hero-title mb-1 text-warning">
                  <?= htmlspecialchars($current_kbm['nama_kegiatan'] ?? 'Istirahat') ?>
                </h3>
                <p class="hero-desc mb-0">
                  <i class="fas fa-clock mr-1"></i> Pukul <?= substr($current_kbm['jam_mulai'], 0, 5) ?> &ndash; <?= substr($current_kbm['jam_selesai'], 0, 5) ?> &bull; Silakan istirahat sejenak sebelum sesi berikutnya.
                </p>
              <?php elseif ($kbm_status_type === 'break_between' && $next_kbm): ?>
                <h3 class="hero-title mb-1 text-info">
                  Persiapan: <?= htmlspecialchars($next_kbm['nama_mapel'] ?? $next_kbm['nama_kegiatan'] ?? '') ?>
                </h3>
                <p class="hero-desc mb-0">
                  <i class="fas fa-clock mr-1"></i> Dimulai pukul <?= substr($next_kbm['jam_mulai'], 0, 5) ?> &bull; Guru: <strong><?= htmlspecialchars($next_kbm['nama_guru'] ?? '-') ?></strong>
                </p>
              <?php elseif ($kbm_status_type === 'upcoming' && $next_kbm): ?>
                <h3 class="hero-title mb-1 text-white">
                  Sesi Pertama: <?= htmlspecialchars($next_kbm['nama_mapel'] ?? $next_kbm['nama_kegiatan'] ?? '') ?>
                </h3>
                <p class="hero-desc mb-0">
                  <i class="fas fa-clock mr-1"></i> Dimulai pukul <?= substr($next_kbm['jam_mulai'], 0, 5) ?> &ndash; <?= substr($next_kbm['jam_selesai'], 0, 5) ?> &bull; Guru: <strong><?= htmlspecialchars($next_kbm['nama_guru'] ?? '-') ?></strong>
                </p>
              <?php elseif ($kbm_status_type === 'finished'): ?>
                <h3 class="hero-title mb-1 text-white">
                  Pembelajaran Hari Ini Telah Tuntas!
                </h3>
                <p class="hero-desc mb-0">
                  Total <?= count($jadwal_hari_ini) ?> sesi pelajaran selesai. Selamat mengulang materi &amp; istirahat!
                </p>
              <?php else: ?>
                <h3 class="hero-title mb-1 text-white">
                  Hari Libur / Belajar Mandiri
                </h3>
                <p class="hero-desc mb-0">
                  Tidak ada agenda KBM tatap muka hari ini. Anda dapat mengeksplorasi modul mandiri di LMS.
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- ===== 8 STAT CARDS (DESKTOP 4 KOLOM, MOBILE GRID 2 KOLOM) ===== -->
  <div class="row mb-1">
    <div class="col-xl-3 col-lg-3 col-md-6 col-6 stat-card-col">
      <a href="<?php echo BASE_URL; ?>siswa_portal/materi" class="stat-card sc--mapel">
        <div class="stat-card-body"><div class="stat-card-info"><div class="stat-number"><?php echo (int)($data["mapel_count"]??0); ?></div><p class="stat-label">Mata Pelajaran</p></div><div class="stat-card-icon"><i class="fas fa-book-open"></i></div></div>
        <div class="stat-card-footer"><span>Lihat Materi</span><i class="fas fa-arrow-right"></i></div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-6 stat-card-col">
      <a href="<?php echo BASE_URL; ?>siswa_portal/tugas" class="stat-card sc--tugas">
        <div class="stat-card-body"><div class="stat-card-info"><div class="stat-number"><?php echo (int)($data["tugas_pending_count"]??0); ?></div><p class="stat-label">Tugas Tersedia</p></div><div class="stat-card-icon"><i class="fas fa-clipboard-list"></i></div></div>
        <div class="stat-card-footer"><span>Kerjakan Tugas</span><i class="fas fa-arrow-right"></i></div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-6 stat-card-col">
      <a href="<?php echo BASE_URL; ?>siswa_portal/tugas" class="stat-card sc--done">
        <div class="stat-card-body"><div class="stat-card-info"><div class="stat-number"><?php echo (int)($data["tugas_done_count"]??0); ?></div><p class="stat-label">Tugas Selesai</p></div><div class="stat-card-icon"><i class="fas fa-check-double"></i></div></div>
        <div class="stat-card-footer"><span>Riwayat Tugas</span><i class="fas fa-arrow-right"></i></div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-6 stat-card-col">
      <a href="<?php echo BASE_URL; ?>siswa_portal/nilai" class="stat-card sc--nilai">
        <div class="stat-card-body"><div class="stat-card-info"><div class="stat-number"><?php echo number_format((float)($data["rata_nilai"]??0),1); ?></div><p class="stat-label">Nilai Rata-Rata</p></div><div class="stat-card-icon"><i class="fas fa-award"></i></div></div>
        <div class="stat-card-footer"><span>Nilai Rapor</span><i class="fas fa-arrow-right"></i></div>
      </a>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-xl-3 col-lg-3 col-md-6 col-6 stat-card-col">
      <a href="<?php echo BASE_URL; ?>siswa_portal/absensi" class="stat-card sc--hadir">
        <div class="stat-card-body"><div class="stat-card-info"><div class="stat-number"><?php echo (int)($absensi["hadir"]??0); ?></div><p class="stat-label">Total Hadir</p></div><div class="stat-card-icon"><i class="fas fa-user-check"></i></div></div>
        <div class="stat-card-footer"><span>Presensi Hadir</span><i class="fas fa-arrow-right"></i></div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-6 stat-card-col">
      <a href="<?php echo BASE_URL; ?>siswa_portal/absensi" class="stat-card sc--sakit">
        <div class="stat-card-body"><div class="stat-card-info"><div class="stat-number"><?php echo (int)($absensi["sakit"]??0); ?></div><p class="stat-label">Total Sakit</p></div><div class="stat-card-icon"><i class="fas fa-clinic-medical"></i></div></div>
        <div class="stat-card-footer"><span>Izin Sakit</span><i class="fas fa-arrow-right"></i></div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-6 stat-card-col">
      <a href="<?php echo BASE_URL; ?>siswa_portal/permohonan" class="stat-card sc--izin">
        <div class="stat-card-body"><div class="stat-card-info"><div class="stat-number"><?php echo (int)($absensi["izin"]??0); ?></div><p class="stat-label">Total Izin</p></div><div class="stat-card-icon"><i class="fas fa-envelope-open-text"></i></div></div>
        <div class="stat-card-footer"><span>Pengajuan Izin</span><i class="fas fa-arrow-right"></i></div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-6 stat-card-col">
      <a href="<?php echo BASE_URL; ?>siswa_portal/absensi" class="stat-card sc--alpa">
        <div class="stat-card-body"><div class="stat-card-info"><div class="stat-number"><?php echo (int)($absensi["alpa"]??0); ?></div><p class="stat-label">Total Alpa</p></div><div class="stat-card-icon"><i class="fas fa-user-times"></i></div></div>
        <div class="stat-card-footer"><span>Tanpa Keterangan</span><i class="fas fa-arrow-right"></i></div>
      </a>
    </div>
  </div>

  <!-- ===== 3 KARTU BAWAH — LEBAR SAMA (masing-masing 1/3) ===== -->
  <div class="row mx-0 bottom-row">

    <!-- 1. PROFIL SISWA -->
    <div class="bottom-col">
      <div class="profil-card">
        <div class="profil-card-banner">
          <div class="profil-avatar-wrap">
            <?php $foto = !empty($siswa["foto_pengguna"]) ? BASE_URL.$siswa["foto_pengguna"] : ""; ?>
            <img src="<?php echo $foto; ?>" alt="Foto"
                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($siswa["nama"]??"S"); ?>&background=007bff&color=fff&size=128'">
          </div>
          <h5 class="profil-card-name"><?php echo htmlspecialchars($siswa["nama"]??"Siswa"); ?></h5>
          <p class="profil-card-nisn">NISN: <?php echo htmlspecialchars($siswa["nisn"]?:($siswa["nipd"]??"-")); ?></p>
        </div>
        <div class="profil-card-body">
          <div class="profil-row">
            <div class="profil-icon" style="background:#3b82f6"><i class="fas fa-chalkboard"></i></div>
            <div><div class="profil-lbl">Kelas / Rombel</div><div class="profil-val"><?php echo htmlspecialchars($kelas["nama_kelas"]??"Belum Ditentukan"); ?></div></div>
          </div>
          <div class="profil-row">
            <div class="profil-icon" style="background:#10b981"><i class="fas fa-user-tie"></i></div>
            <div><div class="profil-lbl">Wali Kelas</div><div class="profil-val"><?php echo htmlspecialchars($wali_kelas); ?></div></div>
          </div>
          <div class="profil-row">
            <div class="profil-icon" style="background:#f97316"><i class="fas fa-venus-mars"></i></div>
            <div><div class="profil-lbl">Jenis Kelamin</div><div class="profil-val"><?php echo htmlspecialchars($siswa["jk"]??"-"); ?></div></div>
          </div>
          <div class="profil-row">
            <div class="profil-icon" style="background:#8b5cf6"><i class="fas fa-school"></i></div>
            <div><div class="profil-lbl">Sekolah Asal</div><div class="profil-val"><?php echo htmlspecialchars($siswa["sekolah_asal"]??"-"); ?></div></div>
          </div>
          <div class="profil-row">
            <div class="profil-icon" style="background:#06b6d4"><i class="fas fa-check-circle"></i></div>
            <div><div class="profil-lbl">Status Siswa</div><div class="profil-val text-success font-weight-bold"><?php echo htmlspecialchars($siswa["status_aktif"]??"Aktif"); ?></div></div>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. AKSES CEPAT PORTAL (TENGAH) -->
    <div class="bottom-col">
      <div class="akses-card">
        <div class="akses-card-hdr">
          <div class="akses-hdr-ico"><i class="fas fa-th-large"></i></div>
          <h6>AKSES CEPAT PORTAL</h6>
        </div>
        <div class="akses-card-body">
          <a href="<?php echo BASE_URL; ?>siswa_portal/jadwal"     class="akses-btn akses-btn--jadwal"><i class="fas fa-calendar-alt"></i><span>Jadwal</span></a>
          <a href="<?php echo BASE_URL; ?>siswa_portal/materi"     class="akses-btn akses-btn--materi"><i class="fas fa-book-open"></i><span>Materi</span></a>
          <a href="<?php echo BASE_URL; ?>siswa_portal/tugas"      class="akses-btn akses-btn--tugas"><i class="fas fa-clipboard-list"></i><span>Tugas</span></a>
          <a href="<?php echo BASE_URL; ?>siswa_portal/nilai"      class="akses-btn akses-btn--nilai"><i class="fas fa-award"></i><span>Nilai</span></a>
          <a href="<?php echo BASE_URL; ?>siswa_portal/absensi"    class="akses-btn akses-btn--absensi"><i class="fas fa-user-check"></i><span>Presensi</span></a>
          <a href="<?php echo BASE_URL; ?>siswa_portal/permohonan" class="akses-btn akses-btn--izin"><i class="fas fa-file-medical"></i><span>Izin/Sakit</span></a>
        </div>
      </div>
    </div>

    <!-- 3. JADWAL PELAJARAN HARI INI (KANAN) -->
    <div class="bottom-col">
      <div class="jadwal-card shadow-sm" style="border-radius: 14px;">
        <div class="jadwal-card-hdr" style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
          <h5 class="jadwal-card-title font-weight-bold" style="font-size: 0.95rem;">
            <i class="fas fa-calendar-day mr-1 text-primary"></i>
            Jadwal Hari Ini <span class="text-primary">(<?php echo htmlspecialchars($hari_ini); ?>)</span>
          </h5>
          <a href="<?php echo BASE_URL; ?>siswa_portal/jadwal" class="btn btn-xs btn-outline-primary font-weight-bold px-3 py-1 rounded-pill">
            Lengkap &rarr;
          </a>
        </div>
        <div class="jadwal-card-body p-3" style="max-height: 380px; overflow-y: auto;">
          <?php if (empty($jadwal_hari_ini)): ?>
            <div class="jadwal-empty text-muted py-4">
              <i class="fas fa-mug-hot fa-3x mb-3 text-warning"></i>
              <p class="mb-1 font-weight-bold" style="font-size:.97rem">Tidak ada sesi untuk hari <?php echo htmlspecialchars($hari_ini); ?>.</p>
              <small>Selamat belajar mandiri!</small>
            </div>
          <?php else: ?>
            <div class="d-flex flex-column" style="gap: 8px;">
              <?php foreach ($jadwal_hari_ini as $j): 
                $is_kbm = (($j['jenis_jam'] ?? '') === 'KBM' || ($j['jenis_kegiatan'] ?? '') === 'KBM');
                $jenis_raw = strtolower($j['jenis_kegiatan'] ?? '');
                
                $border_color = '#3b82f6';
                $badge_bg = 'background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;';
                $badge_label = 'KBM';

                if (!$is_kbm) {
                    if (strpos($jenis_raw, 'istirahat') !== false) {
                        $border_color = '#f59e0b';
                        $badge_bg = 'background: #fef3c7; color: #d97706; border: 1px solid #fde68a;';
                        $badge_label = 'Istirahat';
                    } elseif (strpos($jenis_raw, 'pembiasaan') !== false || strpos($jenis_raw, 'upacara') !== false || strpos($jenis_raw, 'tadarus') !== false) {
                        $border_color = '#10b981';
                        $badge_bg = 'background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;';
                        $badge_label = $j['jenis_kegiatan'] ?? 'Pembiasaan';
                    } else {
                        $border_color = '#8b5cf6';
                        $badge_bg = 'background: #f3e8ff; color: #9333ea; border: 1px solid #e9d5ff;';
                        $badge_label = $j['jenis_kegiatan'] ?? 'Kegiatan';
                    }
                }

                $display_nama = $is_kbm ? ($j['nama_mapel'] ?: ($j['nama_kegiatan'] ?: 'KBM')) : ($j['nama_kegiatan'] ?: '-');
                $jp = (int)($j['jp_count'] ?? 1);
              ?>
              <div class="d-flex align-items-center p-2 rounded-lg" style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid <?= $border_color ?>; gap: 10px;">
                <!-- Time & JP -->
                <div class="text-center pr-2" style="min-width: 90px; border-right: 1px dashed #e2e8f0;">
                  <span class="font-weight-bold text-dark d-block" style="font-size: 0.76rem; letter-spacing: -0.2px;">
                    <?= substr($j['jam_mulai'] ?? '', 0, 5) ?> &ndash; <?= substr($j['jam_selesai'] ?? '', 0, 5) ?>
                  </span>
                  <?php if ($jp > 1): ?>
                    <span class="badge badge-light border text-primary px-1 font-weight-bold" style="font-size: 0.62rem;"><?= $jp ?> JP</span>
                  <?php endif; ?>
                </div>

                <!-- Subject & Teacher -->
                <div class="flex-grow-1" style="min-width: 0;">
                  <span class="badge px-1 py-0 font-weight-bold mb-1" style="<?= $badge_bg ?> font-size: 0.62rem;"><?= htmlspecialchars($badge_label) ?></span>
                  <div class="font-weight-bold text-dark text-truncate" style="font-size: 0.84rem;" title="<?= htmlspecialchars($display_nama) ?>">
                    <?= htmlspecialchars($display_nama) ?>
                  </div>
                  <?php if (!empty($j['nama_guru']) && $j['nama_guru'] !== '-'): ?>
                    <div class="small text-muted text-truncate" style="font-size: 0.72rem;">
                      <i class="fas fa-chalkboard-teacher mr-1 text-primary"></i> <?= htmlspecialchars($j['nama_guru']) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div><!-- /bottom-row -->

</div><!-- /portal-container -->

<?php include __DIR__ . "/partials/footer.php"; ?>

<script>
function updateClock(){
  const n=new Date(), pad=v=>String(v).padStart(2,"0");
  const el=document.getElementById("digitalClock");
  if(el) el.innerText=pad(n.getHours())+":"+pad(n.getMinutes())+":"+pad(n.getSeconds());
}
setInterval(updateClock,1000);
</script>