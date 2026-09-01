<?php include __DIR__.'/partials/header.php'; ?>

<<style>
  /* JADWAL PELAJARAN MODERN UI & MOBILE RESPONSIVE */
  .jadwal-header-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0284c7, #0369a1);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);
  }

  /* 3 PROGRAM PILLS (DESKTOP & MOBILE) */
  .custom-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .custom-pills .nav-link {
    font-size: 0.82rem;
    font-weight: 600;
    padding: 0.5rem 1.1rem;
    border-radius: 50rem;
    transition: all 0.2s ease;
  }
  .custom-pills .nav-link.active {
    background-color: #0284c7 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25) !important;
  }

  /* DAY FILTER BUTTONS IN CARD FILTER */
  .day-filter-btn {
    font-size: 0.76rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.85rem !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
  }
  .day-filter-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
  }
  .day-filter-btn.active {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 3px 8px rgba(2, 132, 199, 0.3) !important;
  }

  /* DAY CARDS STYLING */
  .card-jadwal-hari {
    border-radius: 14px !important;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.04) !important;
  }
  .card-jadwal-hari:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
  }

  .slot-kbm-row:hover {
    background-color: rgba(2, 132, 199, 0.04) !important;
  }

  /* RESPONSIVE MOBILE VIEW (MAX-WIDTH 768px) */
  @media (max-width: 768px) {
    .jadwal-header-icon {
      width: 36px !important;
      height: 36px !important;
      font-size: 1.05rem !important;
      border-radius: 8px !important;
      margin-right: 8px !important;
    }
    .content-header h4 {
      font-size: 0.95rem !important;
    }
    .custom-pills {
      display: grid !important;
      grid-template-columns: 1fr 1fr 1fr !important;
      gap: 6px !important;
      width: 100% !important;
    }
    .custom-pills .nav-link {
      font-size: 0.75rem !important;
      padding: 0.45rem 0.4rem !important;
      text-align: center !important;
      width: 100% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 4px !important;
    }
    .day-filter-btn {
      font-size: 0.70rem !important;
      padding: 0.30rem 0.55rem !important;
    }
    .table thead th {
      font-size: 0.68rem !important;
      padding: 6px 4px !important;
    }
    .table tbody td {
      font-size: 0.74rem !important;
      padding: 6px 4px !important;
    }
  }
</style>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3 jadwal-header-icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Jadwal Pelajaran KBM
          </h4>
          <small class="text-muted d-none d-sm-block">Matriks jadwal mingguan kelas, mapel, dan guru pengampu</small>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <?php if (can_do($pdo, 'jadwal', 'create')): ?>
          <button type="button" class="btn btn-primary btn-sm font-weight-bold px-3 py-1.5 shadow-sm" style="border-radius: 8px;" data-toggle="modal" data-target="#modalTambahJadwal">
            <i class="fas fa-plus mr-1"></i> Tambah Jadwal
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<section class="content">
<div class="container-fluid">

<div class="row">
    <!-- 3 PROGRAM SEGMENTED FILTER TABS (Reguler, Terbuka, Menginduk) -->
    <div class="col-md-12 mb-3">
        <ul class="nav nav-pills custom-pills" id="programPills">
            <li class="nav-item">
                <a class="nav-link <?= ($program === 'reguler') ? 'active text-white' : 'text-dark bg-white border'; ?> font-weight-bold shadow-xs" href="javascript:void(0)" onclick="selectProgramTab('reguler')">
                    <i class="fas fa-school mr-1"></i> Reguler
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($program === 'pjj') ? 'active text-white' : 'text-dark bg-white border'; ?> font-weight-bold shadow-xs" href="javascript:void(0)" onclick="selectProgramTab('pjj')">
                    <i class="fas fa-globe text-success mr-1"></i> Terbuka
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($program === 'menginduk') ? 'active text-white' : 'text-dark bg-white border'; ?> font-weight-bold shadow-xs" href="javascript:void(0)" onclick="selectProgramTab('menginduk')">
                    <i class="fas fa-handshake text-warning mr-1"></i> Menginduk
                </a>
            </li>
        </ul>
    </div>

    <!-- FILTER PER KELAS / PER GURU + TOMBOL FILTER PER 2 HARI (DIBAGI 50:50 SEIMBANG) -->
    <div class="col-md-12 mb-3">
        <div class="card shadow-sm border-0" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body py-2.5 px-3">
                <div class="row align-items-center">
                    
                    <!-- 50% KIRI: FORM DROPDOWN FILTER KELAS / GURU -->
                    <div class="col-lg-6 col-12 mb-2 mb-lg-0">
                        <form id="formJadwalFilter" method="GET" class="d-flex align-items-center flex-nowrap m-0" style="gap: 8px;">
                            <input type="hidden" name="mod" value="jadwal">
                            <input type="hidden" name="program" id="input_program" value="<?= htmlspecialchars($program) ?>">
                            
                            <div class="d-flex align-items-center flex-shrink-0">
                                <span class="badge badge-primary mr-2 px-2 py-1"><i class="fas fa-filter"></i></span>
                                <span class="font-weight-bold text-dark d-none d-sm-inline" style="font-size: 0.82rem;">FILTER:</span>
                            </div>

                            <select name="view" class="form-control form-control-sm border bg-light font-weight-bold" style="border-radius: 8px; font-size: 0.78rem; flex: 1; min-width: 130px;" onchange="this.form.submit()">
                                <option value="kelas" <?= ($view_type == 'kelas') ? 'selected' : ''; ?>>Per Rombel / Kelas</option>
                                <option value="guru" <?= ($view_type == 'guru') ? 'selected' : ''; ?>>Per Guru Pengampu</option>
                            </select>

                            <?php if ($view_type == 'guru'): ?>
                                <select name="id_guru" class="form-control form-control-sm border bg-light font-weight-bold" style="border-radius: 8px; font-size: 0.78rem; flex: 1.3; min-width: 150px;" onchange="this.form.submit()">
                                    <?php foreach ($guru_list as $g): ?>
                                        <option value="<?= $g['id_guru'] ?>" <?= ($id_guru_filter == $g['id_guru']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($g['nama_guru']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <select name="id_kelas" class="form-control form-control-sm border bg-light font-weight-bold" style="border-radius: 8px; font-size: 0.78rem; flex: 1.3; min-width: 140px;" onchange="this.form.submit()">
                                    <?php foreach ($filtered_kelas_list as $k): ?>
                                        <?php $jk_suffix = ($k['jenis_kelas'] ?? '') === 'pjj' ? ' (TERBUKA)' : (($k['jenis_kelas'] ?? '') === 'menginduk' ? ' (MENGINDUK)' : ''); ?>
                                        <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($k['nama_kelas']) ?><?= $jk_suffix ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- 50% KANAN: TOMBOL FILTER HARI (PROPORSIONAL MENGISI SETENGAH AREA) -->
                    <div class="col-lg-6 col-12 d-flex align-items-center justify-content-start mt-2 mt-lg-0">
                        <span class="font-weight-bold text-dark mr-3 small flex-shrink-0 d-none d-sm-inline" style="font-size: 0.82rem;">
                            <i class="fas fa-calendar-day text-primary mr-1"></i>Hari:
                        </span>
                        <div class="d-flex align-items-center flex-grow-1 flex-wrap" style="gap: 6px;">
                            <button type="button" class="btn btn-sm day-filter-btn flex-fill text-center active" data-target-days="all" onclick="filterJadwalHari('all', this)" style="padding: 0.35rem 0.45rem;">
                                Semua
                            </button>
                            <button type="button" class="btn btn-sm day-filter-btn flex-fill text-center" data-target-days="Senin,Selasa" onclick="filterJadwalHari(['Senin', 'Selasa'], this)" style="padding: 0.35rem 0.45rem;">
                                Senin - Selasa
                            </button>
                            <button type="button" class="btn btn-sm day-filter-btn flex-fill text-center" data-target-days="Rabu,Kamis" onclick="filterJadwalHari(['Rabu', 'Kamis'], this)" style="padding: 0.35rem 0.45rem;">
                                Rabu - Kamis
                            </button>
                            <button type="button" class="btn btn-sm day-filter-btn flex-fill text-center" data-target-days="Jumat,Sabtu" onclick="filterJadwalHari(['Jumat', 'Sabtu'], this)" style="padding: 0.35rem 0.45rem;">
                                Jumat - Sabtu
                            </button>
                            <button type="button" class="btn btn-sm day-filter-btn flex-fill text-center" data-target-days="Minggu" onclick="filterJadwalHari(['Minggu'], this)" style="padding: 0.35rem 0.45rem;">
                                Minggu
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- TABEL JADWAL: 1 HARI = 1 CARD TABEL (2 KOLOM DESKTOP GRID) -->
    <?php 
    $day_colors = [
        'Senin'  => ['color' => '#0284c7', 'bg_header' => '#f0f9ff', 'icon' => 'fas fa-book-reader'],
        'Selasa' => ['color' => '#059669', 'bg_header' => '#f0fdf4', 'icon' => 'fas fa-pen-nib'],
        'Rabu'   => ['color' => '#d97706', 'bg_header' => '#fffbeb', 'icon' => 'fas fa-calculator'],
        'Kamis'  => ['color' => '#7c3aed', 'bg_header' => '#f5f3ff', 'icon' => 'fas fa-flask'],
        'Jumat'  => ['color' => '#e11d48', 'bg_header' => '#fff1f2', 'icon' => 'fas fa-praying-hands'],
        'Sabtu'  => ['color' => '#0891b2', 'bg_header' => '#ecfeff', 'icon' => 'fas fa-laptop-code'],
        'Minggu' => ['color' => '#ea580c', 'bg_header' => '#fff7ed', 'icon' => 'fas fa-sun'],
    ];

    $all_days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    ?>

    <div class="col-md-12">
        <div class="row" id="jadwalCardsContainer">
            <?php foreach ($all_days as $hari_ini): 
                $theme = $day_colors[$hari_ini] ?? ['color' => '#0284c7', 'bg_header' => '#f8fafc', 'icon' => 'fas fa-calendar'];
                $raw_day_rows = $result[$hari_ini] ?? [];
                
                // Jika Filter Per Guru: Hanya tampilkan slot KBM mengajar aktif (tanpa slot kegiatan non-KBM / istirahat)
                if ($view_type === 'guru') {
                    $day_rows = array_values(array_filter($raw_day_rows, function($r) {
                        $is_kbm = ($r['jenis_jam_pelajaran'] == 'KBM' || $r['jenis_master_kegiatan'] == 'KBM');
                        return $is_kbm && !empty($r['id_guru']);
                    }));
                } else {
                    $day_rows = $raw_day_rows;
                }

                $total_slots = count($day_rows);
                $total_jp = 0;
                foreach ($day_rows as $r) {
                    if ($r['jenis_jam_pelajaran'] == 'KBM' || $r['jenis_master_kegiatan'] == 'KBM') {
                        $total_jp += ($r['jp_count'] ?? 1);
                    }
                }
            ?>
            <div class="col-lg-6 mb-4 card-day-wrapper" data-hari="<?= $hari_ini ?>">
                <div class="card card-jadwal-hari border-0 h-100" style="border-top: 4px solid <?= $theme['color'] ?> !important;">
                    <!-- CARD HEADER PER HARI -->
                    <div class="card-header py-2.5 px-3" style="background-color: <?= $theme['bg_header'] ?>; border-bottom: 1px solid rgba(0,0,0,0.06);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge mr-2 px-2.5 py-1 text-white font-weight-bold" style="background-color: <?= $theme['color'] ?>; font-size: 0.78rem; border-radius: 6px; letter-spacing: 0.5px;">
                                    <i class="<?= $theme['icon'] ?> mr-1"></i> <?= strtoupper($hari_ini) ?>
                                </span>
                                <?php if ($view_type === 'kelas' && $id_kelas_filter): 
                                    $find_k = array_filter($kelas_list, function($x) use($id_kelas_filter) { return $x['id_kelas'] == $id_kelas_filter; });
                                    $cur_k = !empty($find_k) ? reset($find_k) : null;
                                ?>
                                    <span class="font-weight-bold text-dark small">
                                        Kelas <?= htmlspecialchars($cur_k['nama_kelas'] ?? '') ?>
                                    </span>
                                <?php elseif ($view_type === 'guru' && $id_guru_filter): 
                                    $find_g = array_filter($guru_list, function($x) use($id_guru_filter) { return $x['id_guru'] == $id_guru_filter; });
                                    $cur_g = !empty($find_g) ? reset($find_g) : null;
                                ?>
                                    <span class="font-weight-bold text-dark small">
                                        <?= htmlspecialchars($cur_g['nama_guru'] ?? '') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if ($total_jp > 0): ?>
                                    <span class="badge badge-pill badge-light border font-weight-bold" style="font-size: 0.70rem; color: <?= $theme['color'] ?>;">
                                        <?= $total_jp ?> JP
                                    </span>
                                <?php endif; ?>
                                <span class="badge badge-pill badge-light border text-muted" style="font-size: 0.68rem;">
                                    <?= $total_slots ?> <?= ($view_type === 'guru') ? 'Kelas' : 'Sesi' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD BODY TABLE PER HARI -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="border-collapse: collapse;">
                                <thead class="bg-light">
                                    <tr class="text-muted" style="font-size: 0.70rem; letter-spacing: 0.5px;">
                                        <th class="text-center align-middle py-2 border-top-0" style="width: 115px;">JAM / WAKTU</th>
                                        <?php if ($view_type === 'guru'): ?>
                                            <th class="text-left pl-3 align-middle border-top-0" style="width: 145px;">KELAS / ROMBEL</th>
                                            <th class="text-left pl-3 align-middle border-top-0">MATA PELAJARAN</th>
                                        <?php else: ?>
                                            <th class="text-left pl-3 align-middle border-top-0">MATA PELAJARAN</th>
                                            <th class="text-left pl-3 align-middle border-top-0">GURU PENGAMPU</th>
                                        <?php endif; ?>
                                        <?php if (can_do($pdo, 'jadwal', 'update') || can_do($pdo, 'jadwal', 'delete')): ?>
                                            <th class="text-center align-middle border-top-0" style="width: 70px;">AKSI</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($day_rows)): ?>
                                        <?php foreach ($day_rows as $row): 
                                            $is_kbm = ($row['jenis_jam_pelajaran'] == 'KBM' || $row['jenis_master_kegiatan'] == 'KBM');
                                            $jk_row = $row['jenis_kelas'] ?? 'reguler';
                                            $mode_row = $row['mode_kbm'] ?? 'offline';
                                            $display_name = $row['nama_mapel'] ?: ($row['nama_kegiatan_custom'] ?: ($row['nama_kegiatan'] ?? null));
                                            $is_orphan = empty($display_name) && !empty($row['id_jadwal_mengajar']);
                                            $del_ids = !empty($row['ids_jadwal_mengajar']) ? implode(',', array_filter($row['ids_jadwal_mengajar'])) : ($row['id_jadwal_mengajar'] ?? '');
                                            $jam_ids_str = !empty($row['jam_ids']) ? implode(',', array_filter($row['jam_ids'])) : ($row['id_jam'] ?? '');
                                        ?>
                                        <tr class="slot-kbm-row">
                                            <!-- KOLOM 1: WAKTU -->
                                            <td class="align-middle text-center p-2" style="font-size: 0.75rem; white-space: nowrap; color: #475569; border-right: 1px dashed #e2e8f0; width: 115px; background: #fafafa;">
                                                <div class="font-weight-bold text-dark" style="font-size: 0.78rem;">
                                                    <?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?>
                                                </div>
                                                <div class="mt-0.5">
                                                    <?php if (($row['jp_count'] ?? 1) > 1 && $is_kbm): ?>
                                                        <span class="badge badge-light border text-primary font-weight-bold px-1.5" style="font-size: 0.62rem;">
                                                            <?= $row['jp_count'] ?> JP
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($mode_row === 'online'): ?>
                                                        <span class="badge badge-info px-1.5 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.58rem;">
                                                            🌐 ONLINE
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <?php if ($is_kbm): ?>
                                                <!-- ROW KBM -->
                                                <?php if ($view_type === 'guru'): ?>
                                                    <!-- URUTAN FILTER PER GURU: KELAS DULU LALU MAPEL -->
                                                    <!-- KOLOM 2: KELAS / ROMBEL -->
                                                    <td class="align-middle pl-3 py-2" style="width: 145px;">
                                                        <?php if (!empty($row['nama_kelas'])): ?>
                                                            <span class="badge badge-primary px-2 py-1 font-weight-bold shadow-xs" style="font-size: 0.74rem; border-radius: 6px;">
                                                                <i class="fas fa-users mr-1"></i>Kelas <?= htmlspecialchars($row['nama_kelas']) ?>
                                                            </span>
                                                            <?php if ($jk_row === 'pjj'): ?>
                                                                <span class="badge badge-success px-1.5 py-0.5 d-block mt-0.5 text-left" style="font-size: 0.58rem; width: max-content;">TERBUKA</span>
                                                            <?php elseif ($jk_row === 'menginduk'): ?>
                                                                <span class="badge badge-warning text-dark px-1.5 py-0.5 d-block mt-0.5 text-left" style="font-size: 0.58rem; width: max-content;">MENGINDUK</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted small">-</span>
                                                        <?php endif; ?>
                                                    </td>

                                                    <!-- KOLOM 3: MATA PELAJARAN -->
                                                    <td class="align-middle pl-3 py-2">
                                                        <?php if ($is_orphan): ?>
                                                            <div class="text-danger font-weight-bold" style="font-size: 0.80rem;">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i> Penugasan Diubah di GTK
                                                            </div>
                                                            <div class="text-muted small" style="font-size: 0.70rem;">Hapus/edit slot ini dan pilih guru mapel baru</div>
                                                        <?php else: ?>
                                                            <div class="font-weight-bold text-dark" style="font-size: 0.84rem; line-height: 1.3;">
                                                                <?= htmlspecialchars($display_name ?? '-') ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php else: ?>
                                                    <!-- URUTAN FILTER PER ROMBEL: MAPEL DULU LALU GURU -->
                                                    <!-- KOLOM 2: MATA PELAJARAN -->
                                                    <td class="align-middle pl-3 py-2">
                                                        <?php if ($is_orphan): ?>
                                                            <div class="text-danger font-weight-bold" style="font-size: 0.80rem;">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i> Penugasan Diubah di GTK
                                                            </div>
                                                            <div class="text-muted small" style="font-size: 0.70rem;">Hapus/edit slot ini dan pilih guru mapel baru</div>
                                                        <?php else: ?>
                                                            <div class="font-weight-bold text-dark" style="font-size: 0.84rem; line-height: 1.3;">
                                                                <?= htmlspecialchars($display_name ?? '-') ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>

                                                    <!-- KOLOM 3: GURU PENGAMPU -->
                                                    <td class="align-middle pl-3 py-2">
                                                        <?php if (!empty($row['nama_guru'])): ?>
                                                            <div class="text-dark font-weight-500" style="font-size: 0.78rem;">
                                                                <i class="fas fa-chalkboard-teacher mr-1 text-primary opacity-75"></i><?= htmlspecialchars($row['nama_guru']) ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted small">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>

                                                <!-- KOLOM 4: AKSI (EDIT & HAPUS) -->
                                                <?php if (can_do($pdo, 'jadwal', 'update') || can_do($pdo, 'jadwal', 'delete')): ?>
                                                <td class="text-center align-middle p-1" style="width: 70px; white-space: nowrap;">
                                                    <?php if (!empty($del_ids)): ?>
                                                        <?php if (can_do($pdo, 'jadwal', 'update')): ?>
                                                            <button type="button" class="btn btn-xs btn-outline-warning border-0 rounded-circle btn-edit-jadwal mr-1" 
                                                                style="width: 26px; height: 26px; display: inline-flex; justify-content: center; align-items: center;" 
                                                                data-id="<?= $del_ids ?>"
                                                                data-id-kelas="<?= $row['id_kelas'] ?? '' ?>"
                                                                data-id-guru-mapel="<?= $row['id_guru_mapel'] ?? '' ?>"
                                                                data-hari="<?= $hari_ini ?>"
                                                                data-mode="<?= $mode_row ?>"
                                                                data-jam-ids="<?= $jam_ids_str ?>"
                                                                title="Edit Jadwal">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if (can_do($pdo, 'jadwal', 'delete')): ?>
                                                            <a href="<?= BASE_URL ?>jadwal/delete?id=<?= $del_ids ?>" onclick="return confirmDelete(event)" class="btn btn-xs btn-outline-danger border-0 rounded-circle" style="width: 26px; height: 26px; display: inline-flex; justify-content: center; align-items: center;" title="Hapus Jadwal">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <?php endif; ?>

                                            <?php else: ?>
                                                <!-- ROW NON-KBM: HANYA TAMPIL DI FILTER PER KELAS (COLSPAN BANNER SESI KEGIATAN BERSAMA) -->
                                                <?php 
                                                $name_lower = strtolower($display_name ?? '');
                                                $banner_bg = '#f8fafc';
                                                $banner_badge_class = 'badge-secondary';
                                                $banner_icon = 'fas fa-school';
                                                $banner_subtitle = 'Kegiatan Sekolah / Pembiasaan';

                                                if (strpos($name_lower, 'istirahat') !== false) {
                                                    $banner_bg = '#fffbeb';
                                                    $banner_badge_class = 'badge-warning text-dark';
                                                    $banner_icon = 'fas fa-mug-hot';
                                                    $banner_subtitle = 'Jeda Pembelajaran & Istirahat Siswa';
                                                } elseif (strpos($name_lower, 'upacara') !== false || strpos($name_lower, 'apel') !== false) {
                                                    $banner_bg = '#fff1f2';
                                                    $banner_badge_class = 'badge-danger text-white';
                                                    $banner_icon = 'fas fa-flag';
                                                    $banner_subtitle = 'Diikuti Seluruh Guru, Tenaga Kependidikan & Siswa';
                                                } elseif (strpos($name_lower, 'tadarus') !== false || strpos($name_lower, 'dhuha') !== false || strpos($name_lower, 'sholat') !== false || strpos($name_lower, 'dzuhur') !== false || strpos($name_lower, 'kajian') !== false || strpos($name_lower, 'jumat') !== false) {
                                                    $banner_bg = '#f0fdf4';
                                                    $banner_badge_class = 'badge-success text-white';
                                                    $banner_icon = 'fas fa-praying-hands';
                                                    $banner_subtitle = 'Pembiasaan Ibadah Bersama (Didampingi Guru Piket & Wali Kelas)';
                                                } elseif (strpos($name_lower, 'literasi') !== false || strpos($name_lower, 'senam') !== false) {
                                                    $banner_bg = '#ecfeff';
                                                    $banner_badge_class = 'badge-info text-white';
                                                    $banner_icon = 'fas fa-book-reader';
                                                    $banner_subtitle = 'Kegiatan Pembiasaan Sekolah';
                                                }

                                                $colspan_count = (can_do($pdo, 'jadwal', 'update') || can_do($pdo, 'jadwal', 'delete')) ? 3 : 2;
                                                ?>
                                                <td colspan="<?= $colspan_count ?>" class="align-middle pl-3 py-2" style="background-color: <?= $banner_bg ?>;">
                                                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                                        <span class="badge badge-pill <?= $banner_badge_class ?> px-2.5 py-1 font-weight-bold shadow-xs" style="font-size: 0.70rem; letter-spacing: 0.3px;">
                                                            <i class="<?= $banner_icon ?> mr-1"></i> <?= strtoupper(htmlspecialchars($display_name ?? 'NON-KBM')) ?>
                                                        </span>
                                                        <span class="text-muted small font-italic" style="font-size: 0.73rem;">
                                                            <?= $banner_subtitle ?>
                                                        </span>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center font-italic text-muted py-4 small bg-light">
                                                <i class="fas fa-mug-hot text-muted mb-1 d-block" style="font-size: 1.5rem; opacity: 0.35;"></i>
                                                <?= ($view_type === 'guru') ? 'Tidak ada jadwal mengajar pada hari ' . $hari_ini : 'Tidak ada jadwal kegiatan untuk hari ' . $hari_ini ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>
</section>

<!-- MODAL TAMBAH JADWAL -->
<?php if (can_do($pdo, 'jadwal', 'create')): ?>
    <div class="modal fade" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalTambahJadwalLabel"><i class="fas fa-calendar-plus mr-2"></i>Tambah Jadwal Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= BASE_URL ?>jadwal/save" method="POST">
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark">Pilih Kelas / Rombel</label>
                            <select name="id_kelas" id="modal_id_kelas" class="form-control" required onchange="onModalKelasChange(this)">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_list as $k): ?>
                                    <?php $jk_tag = ($k['jenis_kelas'] ?? '') === 'pjj' ? ' (TERBUKA)' : (($k['jenis_kelas'] ?? '') === 'menginduk' ? ' (MENGINDUK)' : ''); ?>
                                    <option value="<?= $k['id_kelas'] ?>" data-jenis="<?= $k['jenis_kelas'] ?? 'reguler' ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($k['nama_kelas']) ?><?= $jk_tag ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark">Moda Pembelajaran (Online vs Offline)</label>
                            <select name="mode_kbm" id="modal_mode_kbm" class="form-control" required>
                                <option value="offline">🏫 Tatap Muka (Offline di Lokasi)</option>
                                <option value="online">🌐 Daring / Online LMS (Asinkronus / Mandiri)</option>
                            </select>
                            <small class="text-muted" style="font-size: 0.72rem;">*Jadwal Online LMS tidak akan diblokir oleh bentrok jam guru lain.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark">Guru &amp; Mapel (Sesuai Penugasan Kelas)</label>
                            <select name="id_guru_mapel" id="modal_id_guru_mapel" class="form-control" required title="Pilih kelas terlebih dahulu">
                                <option value="">-- Pilih Guru & Mapel --</option>
                                <?php foreach ($guru_mapel_list as $gm): ?>
                                    <option value="<?= $gm['id_guru_mapel'] ?>" data-kelas="<?= $gm['id_kelas'] ?>"><?= $gm['nama_guru'] ?> (<?= $gm['nama_mapel'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted" id="guru_filter_info">Pilih kelas untuk memfilter daftar guru.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark">Hari</label>
                            <select name="hari_kbm" id="modal_hari_kbm" class="form-control" required>
                                <option value="">-- Pilih Hari --</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark d-flex justify-content-between align-items-center mb-1">
                                <span>Jam Pelajaran (Bisa multi-slot / Jam Ganda)</span>
                                <span class="small font-weight-normal">
                                    <a href="#" class="select-all-jam mr-1 text-primary">Pilih Semua</a> | 
                                    <a href="#" class="clear-all-jam text-danger ml-1">Reset</a>
                                </span>
                            </label>
                            <!-- CHECKBOX BOX DIRECT CONTAINER -->
                            <div class="border rounded p-2.5 bg-light" style="max-height: 200px; overflow-y: auto; border-color: #cbd5e1 !important;" id="jamCheckboxContainer">
                                <p class="text-muted small mb-0 font-italic" id="jamPlaceholderInfo">Pilih hari terlebih dahulu untuk melihat slot jam aktif.</p>
                                <?php foreach ($jam_list as $jam): ?>
                                    <div class="custom-control custom-checkbox jam-row mb-1" style="display: none;" data-hari="<?= htmlspecialchars($jam['hari_pelaksanaan'] ?? '') ?>">
                                        <input type="checkbox" class="custom-control-input jam-checkbox" id="jam_<?= $jam['id_jam'] ?>" data-value="<?= $jam['id_jam'] ?>">
                                        <label class="custom-control-label small font-weight-bold" for="jam_<?= $jam['id_jam'] ?>">
                                            <?= htmlspecialchars($jam['label_jam_ke'] ?? $jam['nama_sesi']) ?> (<?= substr($jam['jam_mulai'],0,5) ?> - <?= substr($jam['jam_selesai'],0,5) ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div id="selected-jam-inputs"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="border-radius: 8px;"><i class="fas fa-save mr-1"></i> Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- MODAL EDIT JADWAL -->
<?php if (can_do($pdo, 'jadwal', 'update')): ?>
    <div class="modal fade" id="modalEditJadwal" tabindex="-1" role="dialog" aria-labelledby="modalEditJadwalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title font-weight-bold" id="modalEditJadwalLabel"><i class="fas fa-pencil-alt mr-2"></i>Edit Jadwal KBM</h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= BASE_URL ?>jadwal/update" method="POST">
                    <input type="hidden" name="old_ids" id="edit_old_ids">
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark">Pilih Kelas / Rombel</label>
                            <select name="id_kelas" id="edit_id_kelas" class="form-control" required onchange="onEditModalKelasChange(this)">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_list as $k): ?>
                                    <?php $jk_tag = ($k['jenis_kelas'] ?? '') === 'pjj' ? ' (TERBUKA)' : (($k['jenis_kelas'] ?? '') === 'menginduk' ? ' (MENGINDUK)' : ''); ?>
                                    <option value="<?= $k['id_kelas'] ?>" data-jenis="<?= $k['jenis_kelas'] ?? 'reguler' ?>">
                                        <?= htmlspecialchars($k['nama_kelas']) ?><?= $jk_tag ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark">Moda Pembelajaran (Online vs Offline)</label>
                            <select name="mode_kbm" id="edit_mode_kbm" class="form-control" required>
                                <option value="offline">🏫 Tatap Muka (Offline di Lokasi)</option>
                                <option value="online">🌐 Daring / Online LMS (Asinkronus / Mandiri)</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark">Guru &amp; Mapel (Sesuai Penugasan Kelas)</label>
                            <select name="id_guru_mapel" id="edit_id_guru_mapel" class="form-control" required>
                                <option value="">-- Pilih Guru & Mapel --</option>
                                <?php foreach ($guru_mapel_list as $gm): ?>
                                    <option value="<?= $gm['id_guru_mapel'] ?>" data-kelas="<?= $gm['id_kelas'] ?>"><?= $gm['nama_guru'] ?> (<?= $gm['nama_mapel'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted" id="edit_guru_filter_info">Pilih kelas untuk memfilter daftar guru.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-dark">Hari</label>
                            <select name="hari_kbm" id="edit_hari_kbm" class="form-control" required>
                                <option value="">-- Pilih Hari --</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark d-flex justify-content-between align-items-center mb-1">
                                <span>Jam Pelajaran</span>
                                <span class="small font-weight-normal">
                                    <a href="#" class="edit-select-all-jam mr-1 text-primary">Pilih Semua</a> | 
                                    <a href="#" class="edit-clear-all-jam text-danger ml-1">Reset</a>
                                </span>
                            </label>
                            <div class="border rounded p-2.5 bg-light" style="max-height: 200px; overflow-y: auto; border-color: #cbd5e1 !important;" id="editJamCheckboxContainer">
                                <p class="text-muted small mb-0 font-italic" id="editJamPlaceholderInfo">Pilih hari terlebih dahulu untuk melihat slot jam aktif.</p>
                                <?php foreach ($jam_list as $jam): ?>
                                    <div class="custom-control custom-checkbox edit-jam-row mb-1" style="display: none;" data-hari="<?= htmlspecialchars($jam['hari_pelaksanaan'] ?? '') ?>">
                                        <input type="checkbox" class="custom-control-input edit-jam-checkbox" id="edit_jam_<?= $jam['id_jam'] ?>" data-value="<?= $jam['id_jam'] ?>">
                                        <label class="custom-control-label small font-weight-bold" for="edit_jam_<?= $jam['id_jam'] ?>">
                                            <?= htmlspecialchars($jam['label_jam_ke'] ?? $jam['nama_sesi']) ?> (<?= substr($jam['jam_mulai'],0,5) ?> - <?= substr($jam['jam_selesai'],0,5) ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div id="edit-selected-jam-inputs"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-3" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
                        <button type="submit" class="btn btn-warning px-4 font-weight-bold text-dark" style="border-radius: 8px;"><i class="fas fa-save mr-1"></i> Update Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function selectProgramTab(program) {
    document.getElementById('input_program').value = program;
    var selectKelas = document.querySelector('select[name="id_kelas"]');
    if (selectKelas) {
        selectKelas.value = "";
    }
    document.getElementById('formJadwalFilter').submit();
}

// FILTER TOMBOL HARI (SEMUA HARI / SENIN-SELASA / RABU-KAMIS / JUMAT-SABTU / MINGGU)
function filterJadwalHari(days, el) {
    $('.day-filter-btn').removeClass('active');
    $(el).addClass('active');

    if (days === 'all') {
        $('.card-day-wrapper').fadeIn(150);
    } else {
        var dayArr = Array.isArray(days) ? days : [days];
        $('.card-day-wrapper').each(function() {
            var cardDay = $(this).data('hari');
            if (dayArr.includes(cardDay)) {
                $(this).fadeIn(150);
            } else {
                $(this).hide();
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function(){
    // --- CREATE MODAL SYNC HIDDEN INPUTS ---
    function syncHiddenInputs() {
        var container = document.getElementById('selected-jam-inputs');
        if (!container) return;
        container.innerHTML = '';
        document.querySelectorAll('.jam-checkbox:checked').forEach(function(cb){
            var val = cb.getAttribute('data-value');
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'jam[]';
            inp.value = val;
            container.appendChild(inp);
        });
    }

    document.addEventListener('change', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('jam-checkbox')) {
            syncHiddenInputs();
        }
    });

    document.addEventListener('click', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('select-all-jam')) {
            e.preventDefault();
            document.querySelectorAll('.jam-row:not([style*="display: none"]) .jam-checkbox:not(:disabled)').forEach(function(cb){ 
                cb.checked = true; 
            });
            syncHiddenInputs();
        }
        if (e.target && e.target.classList && e.target.classList.contains('clear-all-jam')) {
            e.preventDefault();
            document.querySelectorAll('.jam-checkbox').forEach(function(cb){ cb.checked = false; });
            syncHiddenInputs();
        }
    });

    syncHiddenInputs();

    // --- EDIT MODAL SYNC HIDDEN INPUTS ---
    function syncEditHiddenInputs() {
        var container = document.getElementById('edit-selected-jam-inputs');
        if (!container) return;
        container.innerHTML = '';
        document.querySelectorAll('.edit-jam-checkbox:checked').forEach(function(cb){
            var val = cb.getAttribute('data-value');
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'jam[]';
            inp.value = val;
            container.appendChild(inp);
        });
    }

    document.addEventListener('change', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('edit-jam-checkbox')) {
            syncEditHiddenInputs();
        }
    });

    document.addEventListener('click', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('edit-select-all-jam')) {
            e.preventDefault();
            document.querySelectorAll('.edit-jam-row:not([style*="display: none"]) .edit-jam-checkbox:not(:disabled)').forEach(function(cb){ 
                cb.checked = true; 
            });
            syncEditHiddenInputs();
        }
        if (e.target && e.target.classList && e.target.classList.contains('edit-clear-all-jam')) {
            e.preventDefault();
            document.querySelectorAll('.edit-jam-checkbox').forEach(function(cb){ cb.checked = false; });
            syncEditHiddenInputs();
        }
    });

    // --- MODAL TAMBAH: FITUR SAMAR & FILTER JAM SESUAI HARI ---
    const selectKelas = document.getElementById('modal_id_kelas');
    const selectHari = document.getElementById('modal_hari_kbm');
    const jamCheckboxes = document.querySelectorAll('.jam-checkbox');
    const jamPlaceholder = document.getElementById('jamPlaceholderInfo');

    function checkOccupiedSlots() {
        const idKelas = selectKelas ? selectKelas.value : '';
        const hariKbm = selectHari ? selectHari.value : '';

        if (!hariKbm) {
            if (jamPlaceholder) jamPlaceholder.style.display = 'block';
            jamCheckboxes.forEach(cb => {
                const row = cb.closest('.jam-row');
                if (row) row.style.display = 'none';
            });
            return;
        }

        if (jamPlaceholder) jamPlaceholder.style.display = 'none';

        jamCheckboxes.forEach(cb => {
            const row = cb.closest('.jam-row');
            const label = row ? row.querySelector('label') : null;
            const hariPelaksanaan = row ? (row.getAttribute('data-hari') || "") : "";
            const activeArr = hariPelaksanaan.split(',').map(s => s.trim());

            if (!activeArr.includes(hariKbm) && hariPelaksanaan !== "") {
                if (row) row.style.display = 'none';
                cb.checked = false; 
            } else {
                if (row) row.style.display = 'block';
            }

            cb.disabled = false;
            if (row) row.style.opacity = '1';
            if (row) row.title = '';
            if (label) label.style.textDecoration = 'none';
        });

        if (!idKelas) {
            syncHiddenInputs();
            return;
        }

        fetch(`<?= BASE_URL ?>api?type=jadwal&act=get_occupied&id_kelas=${idKelas}&hari_kbm=${hariKbm}`)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    const occupiedIds = res.data.map(id => id.toString());
                    jamCheckboxes.forEach(cb => {
                        const val = cb.getAttribute('data-value');
                        if (occupiedIds.includes(val)) {
                            const row = cb.closest('.jam-row');
                            const label = row ? row.querySelector('label') : null;
                            cb.disabled = true;
                            cb.checked = false;
                            if (row) row.style.opacity = '0.45';
                            if (row) row.title = 'Sudah terisi untuk kelas ini';
                            if (label) label.style.textDecoration = 'line-through';
                        }
                    });
                    syncHiddenInputs();
                }
            })
            .catch(err => console.error('Gagal mengecek slot jam:', err));
    }

    if (selectKelas && selectHari) {
        selectKelas.addEventListener('change', checkOccupiedSlots);
        selectHari.addEventListener('change', checkOccupiedSlots);
    }

    // --- MODAL TAMBAH: FILTER GURU BERDASARKAN KELAS ---
    const selectGuruMapel = document.getElementById('modal_id_guru_mapel');
    const guruFilterInfo = document.getElementById('guru_filter_info');
    
    if (selectGuruMapel) {
        const allGuruOptions = Array.from(selectGuruMapel.options);

        function filterGuruMapel() {
            const idKelas = selectKelas ? selectKelas.value : '';
            
            if (!idKelas) {
                selectGuruMapel.disabled = true;
                selectGuruMapel.value = "";
                selectGuruMapel.title = "Pilih kelas terlebih dahulu";
                if (guruFilterInfo) guruFilterInfo.textContent = "Pilih kelas untuk memfilter daftar guru.";
                return;
            }

            selectGuruMapel.disabled = false;
            selectGuruMapel.title = "";
            selectGuruMapel.innerHTML = "";
            selectGuruMapel.appendChild(allGuruOptions[0]);
            
            let found = 0;
            allGuruOptions.forEach((opt, idx) => {
                if (idx === 0) return;
                const kelasId = opt.getAttribute('data-kelas');
                if (kelasId == idKelas) {
                    selectGuruMapel.appendChild(opt.cloneNode(true));
                    found++;
                }
            });

            if (guruFilterInfo) {
                if (found === 0) {
                    guruFilterInfo.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada penugasan guru untuk kelas ini.</span>';
                } else {
                    guruFilterInfo.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i> Menampilkan ' + found + ' guru pengampu.</span>';
                }
            }
        }

        if (selectKelas) {
            selectKelas.addEventListener('change', filterGuruMapel);
            if (selectKelas.value) {
                filterGuruMapel();
            }
        }
    }

    // --- MODAL EDIT: LOGIKA POPULATE & EVENT LISTENER ---
    const editSelectKelas = document.getElementById('edit_id_kelas');
    const editSelectHari = document.getElementById('edit_hari_kbm');
    const editSelectGuruMapel = document.getElementById('edit_id_guru_mapel');
    const editSelectModeKbm = document.getElementById('edit_mode_kbm');
    const editJamCheckboxes = document.querySelectorAll('.edit-jam-checkbox');
    const editJamPlaceholder = document.getElementById('editJamPlaceholderInfo');
    const editGuruFilterInfo = document.getElementById('edit_guru_filter_info');

    let allEditGuruOptions = [];
    if (editSelectGuruMapel) {
        allEditGuruOptions = Array.from(editSelectGuruMapel.options);
    }

    function filterEditGuruMapel(selectedId = '') {
        if (!editSelectGuruMapel) return;
        const idKelas = editSelectKelas ? editSelectKelas.value : '';
        
        editSelectGuruMapel.innerHTML = "";
        editSelectGuruMapel.appendChild(allEditGuruOptions[0].cloneNode(true));
        
        let found = 0;
        allEditGuruOptions.forEach((opt, idx) => {
            if (idx === 0) return;
            const kelasId = opt.getAttribute('data-kelas');
            if (kelasId == idKelas) {
                const clone = opt.cloneNode(true);
                if (clone.value == selectedId) {
                    clone.selected = true;
                }
                editSelectGuruMapel.appendChild(clone);
                found++;
            }
        });

        if (editGuruFilterInfo) {
            if (found === 0) {
                editGuruFilterInfo.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada penugasan guru untuk kelas ini.</span>';
            } else {
                editGuruFilterInfo.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i> Menampilkan ' + found + ' guru pengampu.</span>';
            }
        }
    }

    function renderEditJamSlots(currentActiveJamIds = []) {
        const hariKbm = editSelectHari ? editSelectHari.value : '';
        if (!hariKbm) {
            if (editJamPlaceholder) editJamPlaceholder.style.display = 'block';
            editJamCheckboxes.forEach(cb => {
                const row = cb.closest('.edit-jam-row');
                if (row) row.style.display = 'none';
            });
            return;
        }

        if (editJamPlaceholder) editJamPlaceholder.style.display = 'none';

        editJamCheckboxes.forEach(cb => {
            const row = cb.closest('.edit-jam-row');
            const label = row ? row.querySelector('label') : null;
            const hariPelaksanaan = row ? (row.getAttribute('data-hari') || "") : "";
            const activeArr = hariPelaksanaan.split(',').map(s => s.trim());
            const val = cb.getAttribute('data-value');

            if (!activeArr.includes(hariKbm) && hariPelaksanaan !== "") {
                if (row) row.style.display = 'none';
                cb.checked = false;
            } else {
                if (row) row.style.display = 'block';
            }

            cb.disabled = false;
            if (row) row.style.opacity = '1';
            if (row) row.title = '';
            if (label) label.style.textDecoration = 'none';

            if (currentActiveJamIds.includes(val) || currentActiveJamIds.includes(parseInt(val))) {
                cb.checked = true;
            }
        });

        syncEditHiddenInputs();
    }

    if (editSelectKelas) {
        editSelectKelas.addEventListener('change', function() {
            filterEditGuruMapel();
        });
    }

    if (editSelectHari) {
        editSelectHari.addEventListener('change', function() {
            renderEditJamSlots([]);
        });
    }

    // Tombol Edit Jadwal Click Handler
    $(document).on('click', '.btn-edit-jadwal', function() {
        const oldIds = $(this).data('id');
        const idKelas = $(this).data('id-kelas');
        const idGuruMapel = $(this).data('id-guru-mapel');
        const hari = $(this).data('hari');
        const mode = $(this).data('mode') || 'offline';
        const jamIdsStr = $(this).data('jam-ids') ? $(this).data('jam-ids').toString() : '';
        const jamIds = jamIdsStr ? jamIdsStr.split(',').map(s => s.trim()) : [];

        $('#edit_old_ids').val(oldIds);
        if (editSelectKelas) $(editSelectKelas).val(idKelas);
        if (editSelectModeKbm) $(editSelectModeKbm).val(mode);
        if (editSelectHari) $(editSelectHari).val(hari);

        filterEditGuruMapel(idGuruMapel);
        renderEditJamSlots(jamIds);

        $('#modalEditJadwal').modal('show');
    });
});

function onModalKelasChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    const jenis = opt ? opt.getAttribute('data-jenis') : 'reguler';
    const modeSel = document.getElementById('modal_mode_kbm');

    if (jenis === 'pjj') {
        if (modeSel) modeSel.value = 'online';
    } else {
        if (modeSel) modeSel.value = 'offline';
    }
}

function onEditModalKelasChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    const jenis = opt ? opt.getAttribute('data-jenis') : 'reguler';
    const modeSel = document.getElementById('edit_mode_kbm');

    if (jenis === 'pjj') {
        if (modeSel) modeSel.value = 'online';
    } else {
        if (modeSel) modeSel.value = 'offline';
    }
}
</script>

<?php include __DIR__.'/partials/footer.php'; ?>