<?php
// Ambil semua tahun ajaran untuk dropdown filter
$stmt_ta_all = $pdo->query("SELECT id_ta, nama_ta FROM tahun_ajaran ORDER BY id_ta DESC");
$semua_tahun_ajaran = $stmt_ta_all->fetchAll(PDO::FETCH_ASSOC);

// Tentukan ID TA yang sedang dilihat
$id_ta_yang_dilihat = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

// REVISI: Ambil nama sekolah (PATH DIPERBAIKI)
// Lokasi navbar.php: app/views/partials/
// Lokasi model: app/models/
// Path yang benar: ../../models/
if (file_exists(__DIR__ . '/../../models/ProfilSekolahModel.php')) {
    require_once __DIR__ . '/../../models/ProfilSekolahModel.php';
    // Cek apakah $pdo ada sebelum digunakan (Best practice)
    if (isset($pdo)) { 
        $profil_sekolah_nav = ProfilSekolahModel::getProfil($pdo);
        $nama_sekolah_nav = $profil_sekolah_nav['nama_sekolah'] ?? 'SIMAKS';
    } else {
        $nama_sekolah_nav = 'SIMAKS (DB Error)';
    }
} else {
    $nama_sekolah_nav = 'SIMAKS (Path Error)';
}

// Ambil data pengguna dari Sesi
$nama_pengguna_nav = $_SESSION['nama_pengguna'] ?? 'Pengguna';
$roles_pengguna_nav = implode(', ', user_roles()); 
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  
  <ul class="navbar-nav">
    <li class="nav-item d-none d-lg-block">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    
    <li class="nav-item d-none d-sm-inline-block">
      <a href="index.php?mod=dashboard" class="nav-link font-weight-bold" style="color: #001F3F; font-size: 1.1rem;">
        <?= htmlspecialchars($nama_sekolah_nav) ?>
      </a>
    </li>

    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#" title="Ganti Tampilan Tahun Ajaran">
        <i class="nav-icon fas fa-calendar-alt mr-1"></i> <span style="font-size: 0.8rem;">TA: <strong><?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? ($_SESSION['nama_ta_aktif'] ?? '...')) ?></strong>
        </span>
        <i class="fas fa-chevron-down ml-1" style="font-size: 0.7rem;"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-lg shadow-lg border-0" style="border-radius: 15px; overflow: hidden; min-width: 280px;">
        <div class="dropdown-header text-primary font-weight-bold" style="background: #f8fafc; border-bottom: 1px solid #edf2f7; font-size: 0.85rem; text-transform: none;">
            <i class="fas fa-exchange-alt mr-2"></i> Ganti Tampilan Tahun Ajaran
        </div>
        <div style="max-height: 250px; overflow-y: auto;">
            <?php foreach ($semua_tahun_ajaran as $ta): ?>
                <a href="javascript:void(0)" class="dropdown-item py-2 d-flex align-items-center <?= ($ta['id_ta'] == $id_ta_yang_dilihat) ? 'bg-primary text-white' : '' ?>" 
                   onclick="setYearView(<?= $ta['id_ta'] ?>)">
                    <i class="fas <?= ($ta['id_ta'] == $id_ta_yang_dilihat) ? 'fa-check-circle' : 'fa-calendar text-muted' ?> mr-3"></i>
                    <span style="font-weight: <?= ($ta['id_ta'] == $id_ta_yang_dilihat) ? '600' : '400' ?>;"><?= htmlspecialchars($ta['nama_ta']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="dropdown-divider m-0"></div>
        <div class="p-3 bg-light text-muted small" style="white-space: normal; line-height: 1.4;">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle mr-2 text-warning mt-1"></i>
                <span>Input data tetap masuk ke TA Aktif:<br><strong><?= htmlspecialchars($_SESSION['nama_ta_aktif'] ?? 'Belum Diatur') ?></strong></span>
            </div>
        </div>
        <!-- Hidden Form for Interaction -->
        <form action="index.php?mod=session_filter&act=set_ta" method="POST" id="formTaFilter" style="display:none;">
            <input type="hidden" name="id_ta_filter" id="id_ta_filter_input">
        </form>
        <script>
            function setYearView(id) {
                document.getElementById('id_ta_filter_input').value = id;
                document.getElementById('formTaFilter').submit();
            }
        </script>
      </div>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    
    <li class="nav-item d-none d-sm-inline-block">
      <a href="index.php?mod=dashboard" class="nav-link">Home</a>
    </li>

    <li class="nav-item dropdown user-menu">
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style="display: flex; align-items: center;">
        <img src="<?= get_user_photo($_SESSION['user_id'] ?? null) ?>" class="user-image img-circle elevation-2" alt="User Image" style="object-fit: cover;">
        <div class="d-none d-md-inline-block text-left" style="line-height: 1.2; vertical-align: middle;">
            <span class="d-block font-weight-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($nama_pengguna_nav) ?></span>
            <small class="d-block text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($roles_pengguna_nav) ?></small>
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <li class="user-header" style="background-color: var(--sidebar-bg-color, #001F3F);">
          <img src="<?= get_user_photo($_SESSION['user_id'] ?? null) ?>" class="img-circle elevation-2" alt="User Image" style="object-fit: cover;">
          <p style="color: white;">
            <?= htmlspecialchars($nama_pengguna_nav) ?>
            <small>Role: <?= htmlspecialchars($roles_pengguna_nav) ?></small>
          </p>
        </li>
        <li class="user-footer">
          <a href="index.php?mod=profil" class="btn btn-default btn-flat">Profil Saya</a>
          <a href="index.php?mod=auth&act=logout" class="btn btn-danger btn-flat float-right">Logout</a>
        </li>
      </ul>
    </li>

  </ul>
</nav>