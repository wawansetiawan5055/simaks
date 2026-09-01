<?php include __DIR__.'/partials/header.php'; ?>

<style>
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

  /* MOBILE DAY SWITCHER */
  .mobile-day-tabs-wrapper {
    background: #ffffff;
    border-radius: 12px;
    padding: 6px 8px;
    border: 1px solid #e2e8f0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  }
  .mobile-day-tabs-wrapper::-webkit-scrollbar {
    display: none;
  }
  .mobile-day-pills {
    display: flex;
    flex-wrap: nowrap;
    gap: 6px;
    margin: 0;
    min-width: max-content;
  }
  .mobile-day-pill {
    font-size: 0.74rem !important;
    font-weight: 700 !important;
    padding: 0.35rem 0.75rem !important;
    border-radius: 8px !important;
    white-space: nowrap !important;
    transition: all 0.2s ease !important;
    color: #64748b;
  }
  .mobile-day-pill.active {
    background-color: #0284c7 !important;
    color: #ffffff !important;
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
    /* 3 Program Buttons: 3-column equal grid di HP */
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
    .card-header h6 {
      font-size: 0.80rem !important;
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

    <!-- FILTER PER KELAS / PER GURU MAPEL (Opsi Keseluruhan Dihapus) -->
    <div class="col-md-12 mb-3">
        <div class="card shadow-sm border-0" style="border-radius: 12px; background: #ffffff;">
            <div class="card-body py-2 px-3">
                <form id="formJadwalFilter" method="GET" class="row align-items-center" style="gap: 6px 0;">
                    <input type="hidden" name="mod" value="jadwal">
                    <input type="hidden" name="program" id="input_program" value="<?= htmlspecialchars($program) ?>">
                    
                    <div class="col-md-auto col-12 d-flex align-items-center mb-1 mb-md-0">
                        <span class="badge badge-primary mr-2 px-2 py-1"><i class="fas fa-filter"></i></span>
                        <span class="font-weight-bold text-dark" style="font-size: 0.80rem;">FILTER:</span>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="view" class="form-control form-control-sm border bg-light font-weight-bold" style="border-radius: 8px; font-size: 0.78rem;" onchange="this.form.submit()">
                            <option value="kelas" <?= ($view_type == 'kelas') ? 'selected' : ''; ?>>Per Rombel / Kelas</option>
                            <option value="guru" <?= ($view_type == 'guru') ? 'selected' : ''; ?>>Per Guru Pengampu</option>
                        </select>
                    </div>
                    <?php if ($view_type == 'guru'): ?>
                        <div class="col-md-4 col-6">
                            <select name="id_guru" class="form-control form-control-sm border bg-light font-weight-bold" style="border-radius: 8px; font-size: 0.78rem;" onchange="this.form.submit()">
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach ($guru_list as $g): ?>
                                    <option value="<?= $g['id_guru'] ?>" <?= ($id_guru_filter == $g['id_guru']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($g['nama_guru']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="col-md-4 col-6">
                            <select name="id_kelas" class="form-control form-control-sm border bg-light font-weight-bold" style="border-radius: 8px; font-size: 0.78rem;" onchange="this.form.submit()">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($filtered_kelas_list as $k): ?>
                                    <?php $jk_suffix = ($k['jenis_kelas'] ?? '') === 'pjj' ? ' (TERBUKA)' : (($k['jenis_kelas'] ?? '') === 'menginduk' ? ' (MENGINDUK)' : ''); ?>
                                    <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($k['nama_kelas']) ?><?= $jk_suffix ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- MOBILE DAY SWITCHER TABS (Tampil di Layar HP) -->
    <div class="col-12 d-block d-lg-none mb-3">
        <div class="mobile-day-tabs-wrapper">
            <ul class="nav nav-pills mobile-day-pills">
                <li class="nav-item"><a class="nav-link mobile-day-pill active text-white" href="javascript:void(0)" onclick="switchMobileDay('all', this)">Semua Hari</a></li>
                <li class="nav-item"><a class="nav-link mobile-day-pill bg-light text-muted" href="javascript:void(0)" onclick="switchMobileDay('Senin', this)">Senin</a></li>
                <li class="nav-item"><a class="nav-link mobile-day-pill bg-light text-muted" href="javascript:void(0)" onclick="switchMobileDay('Selasa', this)">Selasa</a></li>
                <li class="nav-item"><a class="nav-link mobile-day-pill bg-light text-muted" href="javascript:void(0)" onclick="switchMobileDay('Rabu', this)">Rabu</a></li>
                <li class="nav-item"><a class="nav-link mobile-day-pill bg-light text-muted" href="javascript:void(0)" onclick="switchMobileDay('Kamis', this)">Kamis</a></li>
                <li class="nav-item"><a class="nav-link mobile-day-pill bg-light text-muted" href="javascript:void(0)" onclick="switchMobileDay('Jumat', this)">Jumat</a></li>
                <li class="nav-item"><a class="nav-link mobile-day-pill bg-light text-muted" href="javascript:void(0)" onclick="switchMobileDay('Sabtu', this)">Sabtu</a></li>
                <li class="nav-item"><a class="nav-link mobile-day-pill bg-light text-muted" href="javascript:void(0)" onclick="switchMobileDay('Minggu', this)">Minggu</a></li>
            </ul>
        </div>
    </div>

    <!-- TABEL JADWAL SPLIT (SENIN S.D. MINGGU DENGAN HARI DIMERGER) -->
    <?php 
    $day_palette = [
        'Senin'  => ['accent' => '#0284c7', 'bg' => '#f0f9ff'],
        'Selasa' => ['accent' => '#059669', 'bg' => '#f0fdf4'],
        'Rabu'   => ['accent' => '#d97706', 'bg' => '#fffbeb'],
        'Kamis'  => ['accent' => '#7c3aed', 'bg' => '#f5f3ff'],
        'Jumat'  => ['accent' => '#e11d48', 'bg' => '#fff1f2'],
        'Sabtu'  => ['accent' => '#0891b2', 'bg' => '#ecfeff'],
        'Minggu' => ['accent' => '#ea580c', 'bg' => '#fff7ed'],
    ];

    $groups = [
        0 => ['Senin', 'Selasa', 'Rabu'],
        1 => ['Kamis', 'Jumat', 'Sabtu', 'Minggu']
    ];

    $group_border_colors = [0 => '#0284c7', 1 => '#10b981']; 
    ?>
    <div class="col-md-12">
        <div class="row">
            <?php foreach($groups as $idx => $days): 
                $accent_card = $group_border_colors[$idx];
            ?>
            <div class="col-lg-6 mb-4 jadwal-card-group" data-group="<?= $idx ?>">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; overflow: hidden; border-top: 4px solid <?= $accent_card ?> !important;">
                    <div class="card-header py-2.5 px-3" style="background: #ffffff; border-bottom: 1px solid rgba(0,0,0,0.05);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="font-weight-bold mb-0" style="color: <?= $accent_card ?>; letter-spacing: 0.5px; text-transform: uppercase;">
                                <i class="fas fa-calendar-week mr-1.5"></i> 
                                <?php 
                                if($view_type == 'guru' && $id_guru_filter) {
                                    $find = array_filter($guru_list, function($x) use($id_guru_filter) { return $x['id_guru'] == $id_guru_filter; });
                                    $label = !empty($find) ? reset($find) : null;
                                    echo $label ? 'GURU: ' . htmlspecialchars($label['nama_guru']) : 'JADWAL GURU';
                                } elseif($view_type == 'kelas' && $id_kelas_filter) {
                                    $find = array_filter($kelas_list, function($x) use($id_kelas_filter) { return $x['id_kelas'] == $id_kelas_filter; });
                                    $label = !empty($find) ? reset($find) : null;
                                    $jk_suffix = $label && ($label['jenis_kelas'] ?? '') === 'pjj' ? ' (TERBUKA)' : ($label && ($label['jenis_kelas'] ?? '') === 'menginduk' ? ' (MENGINDUK)' : '');
                                    echo $label ? 'KELAS: ' . htmlspecialchars($label['nama_kelas']) . $jk_suffix : 'JADWAL KELAS';
                                } else {
                                    echo 'HARI ' . strtoupper(implode(' • ', $days));
                                }
                                ?>
                            </h6>
                            <span class="badge badge-pill badge-light border" style="font-size: 0.68rem; color: #64748b;"><?= count($days) ?> HARI</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="border-collapse: collapse;">
                                <thead class="bg-light">
                                    <tr class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <th class="text-center align-middle py-2 border-top-0" style="width: 65px;">HARI</th>
                                        <th class="text-center align-middle border-top-0" style="width: 105px;">WAKTU</th>
                                        <th class="text-center align-middle border-top-0" style="width: 75px;">KELAS</th>
                                        <th class="text-left pl-3 align-middle border-top-0">MAPEL / GURU</th>
                                        <?php if(has_role(['Admin', 'TU', 'Kurikulum'])): ?>
                                            <th class="text-center align-middle border-top-0" style="width: 38px;"><i class="fas fa-cog"></i></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $has_data = false;
                                    foreach($days as $hari_ini): 
                                        $style = $day_palette[$hari_ini] ?? ['accent' => '#64748b', 'bg' => '#f8fafc'];
                                        if (isset($result[$hari_ini]) && !empty($result[$hari_ini])): 
                                            $has_data = true;
                                            $day_rows = $result[$hari_ini];
                                            $total_slots_in_day = count($day_rows);
                                            foreach($day_rows as $index => $row): 
                                                $jk_row = $row['jenis_kelas'] ?? 'reguler';
                                                $mode_row = $row['mode_kbm'] ?? 'offline';
                                                $display_name = $row['nama_mapel'] ?: ($row['nama_kegiatan_custom'] ?: ($row['nama_kegiatan'] ?? null));
                                                $is_orphan = empty($display_name) && !empty($row['id_jadwal_mengajar']);
                                            ?>
                                            <tr class="jadwal-slot-row" data-hari="<?= $hari_ini ?>" data-jenis="<?= $jk_row ?>" data-mode="<?= $mode_row ?>" style="background-color: <?= $style['bg'] ?>;">
                                                <!-- KOLOM 1: HARI (DIMERGER VERTICAL / ROWSPAN) -->
                                                <?php if ($index === 0): ?>
                                                <td rowspan="<?= $total_slots_in_day ?>" class="align-middle text-center p-1 font-weight-bold border-right jadwal-day-cell" style="background-color: <?= $style['bg'] ?>; border-right: 2px solid <?= $style['accent'] ?> !important; width: 65px;">
                                                    <span class="badge px-1.5 py-1 font-weight-bold" style="font-size: 0.68rem; color: #fff; background-color: <?= $style['accent'] ?>; border-radius: 6px; letter-spacing: 0.5px;">
                                                        <?= strtoupper($hari_ini) ?>
                                                    </span>
                                                </td>
                                                <?php endif; ?>
                                                
                                                <!-- KOLOM 2: WAKTU -->
                                                <td class="align-middle text-center p-1.5" style="font-size: 0.74rem; white-space: nowrap; color: #475569; background: #fafafa; border-right: 1px dashed #f1f5f9; width: 105px;">
                                                    <span class="font-weight-bold text-dark"><?= substr($row['jam_mulai'],0,5) ?></span>
                                                    <span class="small text-muted mx-0.5">-</span>
                                                    <span class="font-weight-bold text-muted"><?= substr($row['jam_selesai'],0,5) ?></span>
                                                    <?php if (($row['jp_count'] ?? 1) > 1): ?>
                                                        <div><span class="badge badge-light border text-primary font-weight-bold" style="font-size: 0.62rem;"><?= $row['jp_count'] ?> JP</span></div>
                                                    <?php endif; ?>
                                                    <?php if ($mode_row === 'online'): ?>
                                                        <div><span class="badge badge-info px-1 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.58rem;">🌐 ONLINE</span></div>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <!-- KOLOM 3: KELAS -->
                                                <td class="align-middle text-center small font-weight-bold" style="color: #334155; width: 85px;">
                                                    <?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
                                                    <?php if ($jk_row === 'pjj'): ?>
                                                        <span class="badge badge-success d-block mt-0.5" style="font-size: 0.58rem;">(TERBUKA)</span>
                                                    <?php elseif ($jk_row === 'menginduk'): ?>
                                                        <span class="badge badge-warning text-dark d-block mt-0.5" style="font-size: 0.58rem;">(MENGINDUK)</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- KOLOM 4: MAPEL / GURU -->
                                                <td class="align-middle pl-3 py-2">
                                                    <?php if ($is_orphan): ?>
                                                        <div class="text-danger font-weight-bold" style="font-size: 0.80rem;">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i> Penugasan Diubah di GTK
                                                        </div>
                                                        <div class="text-muted" style="font-size: 0.70rem;">Hapus slot ini dan tambahkan mapel baru</div>
                                                    <?php else: ?>
                                                        <div class="text-dark font-weight-bold mb-0" style="font-size: 0.84rem; line-height: 1.25;">
                                                            <?= htmlspecialchars($display_name ?? '-') ?>
                                                        </div>
                                                        <?php if (!empty($row['nama_guru'])): ?>
                                                            <div class="text-muted" style="font-size: 0.74rem; margin-top: 1px;">
                                                                <i class="fas fa-chalkboard-teacher mr-1 text-primary opacity-50"></i><?= htmlspecialchars($row['nama_guru']) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- KOLOM 5: AKSI (HAPUS) -->
                                                <?php if (can_do($pdo, 'jadwal', 'delete')): ?>
                                                <td class="text-center align-middle p-1" style="width: 38px;">
                                                    <?php 
                                                        $del_ids = !empty($row['ids_jadwal_mengajar']) ? implode(',', array_filter($row['ids_jadwal_mengajar'])) : ($row['id_jadwal_mengajar'] ?? '');
                                                    ?>
                                                    <?php if(!empty($del_ids)): ?>
                                                        <a href="<?= BASE_URL ?>jadwal/delete?id=<?= $del_ids ?>" onclick="return confirmDelete(event)" class="btn btn-xs btn-outline-danger border-0 rounded-circle" style="width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center;" title="Hapus Jadwal"><i class="fas fa-times"></i></a>
                                                    <?php endif; ?>
                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if(!$has_data): ?>
                                        <tr><td colspan="5" class="text-center font-italic text-muted py-5 small bg-light">
                                            <i class="fas fa-calendar-times text-muted mb-2" style="font-size: 2rem; opacity: 0.4;"></i><br>
                                            Tidak ada jadwal untuk <?= implode(', ', $days) ?>
                                        </td></tr>
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
                            <!-- CHECKBOX BOX DIRECT CONTAINER (Tidak macet/hilang) -->
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

<script>
function selectProgramTab(program) {
    document.getElementById('input_program').value = program;
    // Clear selected id_kelas so it selects the first class of the new program
    var selectKelas = document.querySelector('select[name="id_kelas"]');
    if (selectKelas) {
        selectKelas.value = "";
    }
    document.getElementById('formJadwalFilter').submit();
}

function switchMobileDay(hari, el) {
    $('.mobile-day-pill').removeClass('active text-white').addClass('bg-light text-muted');
    $(el).addClass('active text-white').removeClass('bg-light text-muted');

    if (hari === 'all') {
        $('.jadwal-slot-row').show();
        $('.jadwal-card-group').show();
    } else {
        $('.jadwal-slot-row').each(function() {
            if ($(this).data('hari') === hari) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('.jadwal-card-group').each(function() {
            var hasVisible = $(this).find('.jadwal-slot-row:visible').length > 0;
            if (hasVisible) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function(){
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

    // FITUR SAMAR & FILTER JAM SESUAI HARI
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

        let visibleCount = 0;
        jamCheckboxes.forEach(cb => {
            const row = cb.closest('.jam-row');
            const label = row ? row.querySelector('label') : null;
            const hariPelaksanaan = row ? (row.getAttribute('data-hari') || "") : "";
            const activeArr = hariPelaksanaan.split(',').map(s => s.trim());

            // 1. Filter jam yang aktif di hari ini
            if (!activeArr.includes(hariKbm) && hariPelaksanaan !== "") {
                if (row) row.style.display = 'none';
                cb.checked = false; 
            } else {
                if (row) row.style.display = 'block';
                visibleCount++;
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

    // FITUR FILTER GURU BERDASARKAN KELAS
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
            // Trigger immediately if class is already selected
            if (selectKelas.value) {
                filterGuruMapel();
            }
        }
    }

    // Auto-adjust Moda KBM based on PJJ Class & Day
    const selectModeKbm = document.getElementById('modal_mode_kbm');
    function autoAdjustModeKbm() {
        if (!selectKelas || !selectModeKbm) return;
        const selectedOption = selectKelas.options[selectKelas.selectedIndex];
        const jenisKelas = selectedOption ? selectedOption.getAttribute('data-jenis') : '';
        const hari = selectHari ? selectHari.value : '';

        if (jenisKelas === 'pjj') {
            if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(hari) || !hari) {
                selectModeKbm.value = 'online';
            } else if (['Sabtu', 'Minggu'].includes(hari)) {
                selectModeKbm.value = 'offline';
            }
        }
    }

    if (selectKelas && selectModeKbm) {
        selectKelas.addEventListener('change', autoAdjustModeKbm);
    }
    if (selectHari && selectModeKbm) {
        selectHari.addEventListener('change', autoAdjustModeKbm);
    }
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
</script>

<?php include __DIR__.'/partials/footer.php'; ?>