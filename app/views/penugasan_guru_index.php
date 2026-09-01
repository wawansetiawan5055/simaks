<?php include __DIR__ . '/partials/header.php'; ?>
<?php 
$is_manager = in_array(1, $_SESSION['role_ids'] ?? []) || in_array('Kurikulum', $_SESSION['roles'] ?? []); 
?>

<style>
  /* PENUGASAN GTK MODERN TABS & MOBILE OPTIMIZATION */
  .penugasan-header-icon {
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

  .nav-tabs-penugasan-wrapper {
    background: #ffffff;
    border-bottom: 2px solid #f1f5f9;
    padding: 0 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; /* Firefox */
  }
  .nav-tabs-penugasan-wrapper::-webkit-scrollbar {
    display: none; /* Safari and Chrome */
  }

  .nav-tabs-penugasan {
    display: flex !important;
    flex-wrap: nowrap !important;
    gap: 4px;
    border: none !important;
    margin: 0 !important;
    padding: 0.35rem 0.25rem 0 0.25rem !important;
    min-width: max-content;
  }

  .nav-tabs-penugasan .nav-item {
    flex-shrink: 0 !important;
  }

  .nav-tabs-penugasan .nav-link {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    border: none !important;
    border-bottom: 3px solid transparent !important;
    padding: 0.75rem 1.15rem !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    color: #64748b !important;
    background: transparent !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
    border-radius: 8px 8px 0 0 !important;
  }

  .nav-tabs-penugasan .nav-link:hover {
    color: #0284c7 !important;
    background: #f8fafc !important;
  }

  .nav-tabs-penugasan .nav-link.active {
    color: #0284c7 !important;
    font-weight: 700 !important;
    border-bottom-color: #0284c7 !important;
    background: rgba(2, 132, 199, 0.05) !important;
  }

  .nav-tabs-penugasan .nav-link i {
    font-size: 0.95rem;
  }

  @media (max-width: 768px) {
    .penugasan-header-icon {
      width: 36px !important;
      height: 36px !important;
      font-size: 1.05rem !important;
      border-radius: 8px !important;
      margin-right: 8px !important;
    }
    .content-header h4 {
      font-size: 0.95rem !important;
      line-height: 1.25 !important;
    }
    .nav-tabs-penugasan .nav-link {
      padding: 0.65rem 0.85rem !important;
      font-size: 0.75rem !important;
      gap: 5px !important;
    }
    .nav-tabs-penugasan .nav-link i {
      font-size: 0.85rem !important;
    }
    .card-body.p-4 {
      padding: 0.85rem !important;
    }
    .card-title {
      font-size: 0.85rem !important;
    }
    .table th, .table td {
      font-size: 0.74rem !important;
      padding: 6px 8px !important;
    }
  }
</style>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3 penugasan-header-icon">
          <i class="fas fa-user-shield"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Penugasan GTK (Mata Pelajaran)
          </h4>
          <small class="text-muted d-none d-sm-block">Distribusi wali kelas, guru mapel, jabatan, dan pembina</small>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <span class="badge badge-light border text-muted px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.76rem;">
          <i class="fas fa-calendar-check text-primary mr-1"></i> TA: <?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') ?>
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<section class="content">
  <div class="container-fluid">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
      <div class="card-header p-0 bg-white border-0">
        <!-- SCROLLABLE TABS WRAPPER (No overlap, smooth slide on mobile) -->
        <div class="nav-tabs-penugasan-wrapper">
          <ul class="nav nav-tabs nav-tabs-penugasan" id="custom-tabs-three-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link <?= $active_tab == 'walas' ? 'active' : '' ?>" 
                 id="tab-walas-tab" data-toggle="pill" href="#tab-walas" role="tab" 
                 aria-controls="tab-walas" aria-selected="<?= $active_tab == 'walas' ? 'true' : 'false' ?>">
                <i class="fas fa-chalkboard-teacher text-primary"></i> Wali Kelas
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $active_tab == 'mapel' ? 'active' : '' ?>" 
                 id="tab-mapel-tab" data-toggle="pill" href="#tab-mapel" role="tab" 
                 aria-controls="tab-mapel" aria-selected="<?= $active_tab == 'mapel' ? 'true' : 'false' ?>">
                <i class="fas fa-book text-warning"></i> Guru Mapel
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $active_tab == 'jabatan_guru' ? 'active' : '' ?>" 
                 id="tab-jabatan_guru-tab" data-toggle="pill" href="#tab-jabatan_guru" role="tab" 
                 aria-controls="tab-jabatan_guru" aria-selected="<?= $active_tab == 'jabatan_guru' ? 'true' : 'false' ?>">
                <i class="fas fa-user-edit text-info"></i> Tugas Tambahan
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $active_tab == 'jabatan_staff' ? 'active' : '' ?>" 
                 id="tab-jabatan_staff-tab" data-toggle="pill" href="#tab-jabatan_staff" role="tab" 
                 aria-controls="tab-jabatan_staff" aria-selected="<?= $active_tab == 'jabatan_staff' ? 'true' : 'false' ?>">
                <i class="fas fa-user-cog text-secondary"></i> Struktural Staff
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $active_tab == 'pembina' ? 'active' : '' ?>" 
                 id="tab-pembina-tab" data-toggle="pill" href="#tab-pembina" role="tab" 
                 aria-controls="tab-pembina" aria-selected="<?= $active_tab == 'pembina' ? 'true' : 'false' ?>">
                <i class="fas fa-users text-success"></i> Pembina Ekstra
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="card-body p-4">
        <div class="tab-content" id="custom-tabs-three-tabContent">

          <!-- TAB 1: WALI KELAS -->
          <div class="tab-pane fade <?= $active_tab == 'walas' ? 'show active' : '' ?>" id="tab-walas" role="tabpanel"
            aria-labelledby="tab-walas-tab">
            <!-- Content Walas -->
            <div class="row">
              <?php if ($is_manager): ?>
              <div class="col-md-4">
                <div class="card card-info">
                  <div class="card-header">
                    <h3 class="card-title">Form Penugasan Wali Kelas</h3>
                  </div>
                  <form action="<?= BASE_URL ?>penugasan_guru/save_walas" method="POST">
                    <div class="card-body">
                      <div class="form-group">
                        <label>Guru (yang belum jadi walas)</label>
                        <select name="id_guru" class="form-control select2" required>
                          <option value="">-- Guru Belum Jadi Walas --</option>
                          <?php foreach ($walas_available_guru as $g): ?>
                            <option value="<?= $g['id_guru'] ?>"><?= $g['nama'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Kelas (yang belum punya walas)</label>
                        <select name="id_kelas" class="form-control select2" required>
                          <option value="">-- Kelas Belum Ada Walas --</option>
                          <?php foreach ($walas_available_kelas as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-info">Simpan Walas</button></div>
                  </form>
                </div>
              </div>
              <?php endif; ?>
              <div class="<?= $is_manager ? 'col-md-8' : 'col-md-12' ?>">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Daftar Wali Kelas</h3>
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Nama Guru</th>
                          <th>Kelas</th>
                          <?php if ($is_manager): ?>
                          <th style="width: 40px">Aksi</th>
                          <?php endif; ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($walas_list as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                            <?php if ($is_manager): ?>
                            <td><a
                                href="<?= BASE_URL ?>penugasan_guru/delete_walas?id=<?= $row['id_penugasan_wali_kelas'] ?>"
                                class="btn btn-xs btn-danger btn-delete-confirm" onclick="return confirmDelete(event)"><i
                                  class="fa fa-trash"></i></a></td>
                            <?php endif; ?>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 2: GURU MAPEL -->
          <div class="tab-pane fade <?= $active_tab == 'mapel' ? 'show active' : '' ?>" id="tab-mapel" role="tabpanel"
            aria-labelledby="tab-mapel-tab">
            <!-- Content Mapel -->
            <div class="row">
              <?php if ($is_manager): ?>
              <div class="col-md-4">
                <div class="card card-warning">
                  <div class="card-header">
                    <h3 class="card-title">Form Penugasan Guru Mapel</h3>
                  </div>
                  <form action="<?= BASE_URL ?>penugasan_guru/save_guru_mapel" method="POST">
                    <div class="card-body">
                      <div class="form-group">
                        <label>Guru</label>
                        <select name="id_guru" class="form-control select2" required>
                          <option value="">-- Pilih Guru --</option>
                          <?php foreach ($all_guru as $g): ?>
                            <option value="<?= $g['id_guru'] ?>"><?= $g['nama'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Mata Pelajaran yang Diampu</label>
                        <div class="p-2 border rounded" style="max-height: 200px; overflow-y: auto; background: #fdfdfd;">
                          <?php foreach ($all_mapel as $m): ?>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="id_mapel[]" value="<?= $m['id_mapel'] ?>" id="mapel_<?= $m['id_mapel'] ?>">
                              <label class="form-check-label" for="mapel_<?= $m['id_mapel'] ?>">
                                <?= $m['nama_mapel'] ?> <small class="text-muted">(<?= $m['kategori_mapel'] ?>)</small>
                              </label>
                            </div>
                          <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Bisa pilih lebih dari satu mata pelajaran.</small>
                      </div>
                      <div class="form-group">
                        <label>Kelas yang Diampu</label>
                        <div class="row px-2">
                          <?php foreach ($all_kelas as $k): ?>
                            <div class="col-md-4 col-6">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="id_kelas[]" value="<?= $k['id_kelas'] ?>" id="kelas_<?= $k['id_kelas'] ?>">
                                <label class="form-check-label" for="kelas_<?= $k['id_kelas'] ?>">
                                  <?= $k['nama_kelas'] ?>
                                </label>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Bisa pilih lebih dari satu kelas sekaligus.</small>
                      </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-warning">Simpan Guru Mapel</button>
                    </div>
                  </form>
                </div>
              </div>
              <?php endif; ?>
              <div class="<?= $is_manager ? 'col-md-8' : 'col-md-12' ?>">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Daftar Guru Pengampu Mapel</h3>
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-striped" id="table-guru-mapel">
                      <thead>
                        <tr>
                          <th>Nama Guru</th>
                          <th>Mapel</th>
                          <th>Kelas</th>
                          <?php if ($is_manager): ?>
                          <th style="width: 40px">Aksi</th>
                          <?php endif; ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($guru_mapel_list as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td>
                              <?php 
                                $m_list = explode(' | ', $row['daftar_mapel'] ?? '');
                                foreach($m_list as $mn):
                              ?>
                                <span class="badge badge-warning mr-1"><?= htmlspecialchars($mn) ?></span>
                              <?php endforeach; ?>
                            </td>
                            <td>
                              <?php 
                                $k_list = explode(', ', $row['daftar_kelas'] ?? '');
                                foreach($k_list as $kn):
                              ?>
                                <span class="badge badge-info mr-1"><?= htmlspecialchars($kn) ?></span>
                              <?php endforeach; ?>
                            </td>
                            <?php if ($is_manager): ?>
                            <td class="text-nowrap" style="width: 80px;">
                              <button type="button" class="btn btn-xs btn-primary mr-1" 
                                onclick="editGuruMapel(<?= (int)$row['id_guru'] ?>, '<?= htmlspecialchars(addslashes($row['nama_guru'])) ?>', '<?= $row['mapel_ids'] ?? '' ?>', '<?= $row['kelas_ids'] ?? '' ?>')" 
                                title="Edit Penugasan">
                                <i class="fa fa-edit"></i> Edit
                              </button>
                              <a href="<?= BASE_URL ?>penugasan_guru/delete_guru_mapel?id=<?= $row['ids_guru_mapel'] ?>"
                                class="btn btn-xs btn-danger btn-delete-confirm" onclick="return confirmDelete(event)" title="Hapus Semua"><i
                                  class="fa fa-trash"></i></a>
                            </td>
                            <?php endif; ?>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 3: TUGAS TAMBAHAN GURU -->
          <div class="tab-pane fade <?= $active_tab == 'jabatan_guru' ? 'show active' : '' ?>" id="tab-jabatan_guru"
            role="tabpanel" aria-labelledby="tab-jabatan_guru-tab">
            <div class="row">
              <?php if ($is_manager): ?>
              <div class="col-md-4">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Form Tugas Tambahan Guru</h3>
                  </div>
                  <form action="<?= BASE_URL ?>penugasan_guru/save_jabatan" method="POST">
                    <input type="hidden" name="tab" value="jabatan_guru">
                    <div class="card-body">
                      <div class="form-group">
                        <label>Guru</label>
                        <select name="id_guru" class="form-control select2" required style="width: 100%;">
                          <option value="">-- Pilih Guru --</option>
                          <?php foreach ($all_guru as $g): ?>
                            <option value="<?= $g['id_guru'] ?>"><?= $g['nama'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Nama Jabatan / Tugas</label>
                        <div class="row px-2">
                          <?php foreach ($master_jabatan_guru as $mj): ?>
                            <div class="col-md-6 col-12">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenis_jabatan[]" value="<?= $mj['nama_jabatan'] ?>" id="jabatan_guru_<?= $mj['id_jabatan'] ?>">
                                <label class="form-check-label" for="jabatan_guru_<?= $mj['id_jabatan'] ?>">
                                  <?= $mj['nama_jabatan'] ?>
                                </label>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Bisa pilih lebih dari satu jabatan.</small>
                      </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-success">Simpan Tugas</button></div>
                  </form>
                </div>
              </div>
              <?php endif; ?>
              <div class="<?= $is_manager ? 'col-md-8' : 'col-md-12' ?>">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Daftar Tugas Tambahan Guru</h3>
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-sm table-striped">
                      <thead>
                        <tr>
                          <th>Nama Guru</th>
                          <th>Jabatan</th>
                          <?php if ($is_manager): ?>
                          <th style="width: 40px">Aksi</th>
                          <?php endif; ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $has_guru_data = false;
                        foreach ($jabatan_list as $row): 
                          // Pisahkan daftar jabatan
                          $j_list = explode(' | ', $row['daftar_jabatan'] ?? '');
                          $ids_list = explode(',', $row['ids_penugasan_jabatan'] ?? '');
                          
                          // Filter: hanya tampilkan jika ada jabatan berkategori GURU
                          $filtered_j = [];
                          $filtered_ids = [];
                          foreach($j_list as $idx => $jn) {
                            $is_match = false;
                            foreach($master_jabatan_guru as $mj) if($mj['nama_jabatan'] == $jn) $is_match = true;
                            if($is_match) {
                              $filtered_j[] = $jn;
                              $filtered_ids[] = $ids_list[$idx];
                            }
                          }
                          
                          if(empty($filtered_j)) continue;
                          $has_guru_data = true;
                        ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td>
                              <?php foreach($filtered_j as $fn): ?>
                                <span class="badge badge-success mr-1"><?= htmlspecialchars($fn) ?></span>
                              <?php endforeach; ?>
                            </td>
                            <?php if ($is_manager): ?>
                            <td><a href="<?= BASE_URL ?>penugasan_guru/delete_jabatan?id=<?= implode(',', $filtered_ids) ?>&tab=jabatan_guru" class="btn btn-xs btn-danger btn-delete-confirm"><i class="fa fa-trash"></i></a></td>
                            <?php endif; ?>
                          </tr>
                        <?php endforeach; ?>
                        <?php if (!$has_guru_data): ?>
                          <tr><td colspan="<?= $is_manager ? 3 : 2 ?>" class="text-center p-3 text-muted">Belum ada data tugas tambahan guru.</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 4: JABATAN STRUKTURAL STAFF -->
          <div class="tab-pane fade <?= $active_tab == 'jabatan_staff' ? 'show active' : '' ?>" id="tab-jabatan_staff"
            role="tabpanel" aria-labelledby="tab-jabatan_staff-tab">
            <div class="row">
              <?php if ($is_manager): ?>
              <div class="col-md-4">
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title">Form Jabatan Struktural Staff</h3>
                  </div>
                  <form action="<?= BASE_URL ?>penugasan_guru/save_jabatan" method="POST">
                    <input type="hidden" name="tab" value="jabatan_staff">
                    <div class="card-body">
                      <div class="form-group">
                        <label>Pegawai / Staff</label>
                        <select name="id_guru" class="form-control select2" required style="width: 100%;">
                          <option value="">-- Pilih Pegawai --</option>
                          <?php foreach ($all_guru as $g): ?>
                            <option value="<?= $g['id_guru'] ?>"><?= $g['nama'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Nama Jabatan</label>
                        <div class="row px-2">
                          <?php foreach ($master_jabatan_staff as $mj): ?>
                            <div class="col-md-6 col-12">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenis_jabatan[]" value="<?= $mj['nama_jabatan'] ?>" id="jabatan_staff_<?= $mj['id_jabatan'] ?>">
                                <label class="form-check-label" for="jabatan_staff_<?= $mj['id_jabatan'] ?>">
                                  <?= $mj['nama_jabatan'] ?>
                                </label>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Bisa pilih lebih dari satu jabatan.</small>
                      </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Jabatan</button></div>
                  </form>
                </div>
              </div>
              <?php endif; ?>
              <div class="<?= $is_manager ? 'col-md-8' : 'col-md-12' ?>">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Daftar Jabatan Struktural Staff</h3>
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-sm table-striped">
                      <thead>
                        <tr>
                          <th>Nama Pegawai</th>
                          <th>Jabatan</th>
                          <?php if ($is_manager): ?>
                          <th style="width: 40px">Aksi</th>
                          <?php endif; ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $has_staff_data = false;
                        foreach ($jabatan_list as $row): 
                          // Pisahkan daftar jabatan
                          $j_list = explode(' | ', $row['daftar_jabatan'] ?? '');
                          $ids_list = explode(',', $row['ids_penugasan_jabatan'] ?? '');
                          
                          // Filter: hanya tampilkan jika ada jabatan berkategori STAFF
                          $filtered_j = [];
                          $filtered_ids = [];
                          foreach($j_list as $idx => $jn) {
                            $is_match = false;
                            foreach($master_jabatan_staff as $mj) if($mj['nama_jabatan'] == $jn) $is_match = true;
                            if($is_match) {
                              $filtered_j[] = $jn;
                              $filtered_ids[] = $ids_list[$idx];
                            }
                          }
                          
                          if(empty($filtered_j)) continue;
                          $has_staff_data = true;
                        ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td>
                              <?php foreach($filtered_j as $fn): ?>
                                <span class="badge badge-primary mr-1"><?= htmlspecialchars($fn) ?></span>
                              <?php endforeach; ?>
                            </td>
                            <?php if ($is_manager): ?>
                            <td><a href="<?= BASE_URL ?>penugasan_guru/delete_jabatan?id=<?= implode(',', $filtered_ids) ?>&tab=jabatan_staff" class="btn btn-xs btn-danger btn-delete-confirm"><i class="fa fa-trash"></i></a></td>
                            <?php endif; ?>
                          </tr>
                        <?php endforeach; ?>
                        <?php if (!$has_staff_data): ?>
                          <tr><td colspan="<?= $is_manager ? 3 : 2 ?>" class="text-center p-3 text-muted">Belum ada data jabatan staff.</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- TAB 4: PEMBINA NON-AKADEMIK (NEW) -->
          <div class="tab-pane fade <?= $active_tab == 'pembina' ? 'show active' : '' ?>" id="tab-pembina"
            role="tabpanel" aria-labelledby="tab-pembina-tab">
            <div class="row">
              <?php if ($is_manager): ?>
              <div class="col-md-4">
                <div class="card card-purple">
                  <div class="card-header">
                    <h3 class="card-title">Form Pembina Kegiatan</h3>
                  </div>
                  <form action="<?= BASE_URL ?>penugasan_guru/save_pembina" method="POST">
                    <div class="card-body">
                      <div class="form-group">
                        <label>Kegiatan Non-Akademik</label>
                        <select name="id_kegiatan" class="form-control select2" required>
                          <option value="">-- Pilih Kegiatan (Master) --</option>
                          <?php foreach ($master_kegiatan_nona as $k): ?>
                            <option value="<?= $k['id_kegiatan'] ?>">
                              <?= htmlspecialchars($k['nama_kegiatan']) ?> (<?= $k['jenis_kegiatan'] ?>)
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Master Kegiatan Non-Akademik</small>
                      </div>
                      <div class="form-group">
                        <label>Guru Pembina</label>
                        <select name="id_guru" class="form-control select2" required>
                          <option value="">-- Pilih Guru --</option>
                          <?php foreach ($all_guru as $g): ?>
                            <option value="<?= $g['id_guru'] ?>"><?= $g['nama'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn bg-purple text-white">Simpan Pembina</button></div>
                  </form>
                </div>
              </div>
              <?php endif; ?>
              <div class="<?= $is_manager ? 'col-md-8' : 'col-md-12' ?>">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Daftar Pembina Non-Akademik</h3>
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Kegiatan</th>
                          <th>Jenis</th>
                          <th>Guru Pembina</th>
                          <?php if ($is_manager): ?>
                          <th style="width: 40px">Aksi</th>
                          <?php endif; ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($pembina_list)): ?>
                          <tr>
                            <td colspan="<?= $is_manager ? 4 : 3 ?>" class="text-center">Belum ada data pembina.</td>
                          </tr>
                        <?php endif; ?>
                        <?php foreach ($pembina_list as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($row['jenis_kegiatan']) ?></span></td>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <?php if ($is_manager): ?>
                            <td><a
                                href="<?= BASE_URL ?>penugasan_guru/delete_pembina?id=<?= $row['id_penugasan_pembina'] ?>"
                                class="btn btn-xs btn-danger btn-delete-confirm"><i class="fa fa-trash"></i></a></td>
                            <?php endif; ?>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- /.card -->
    </div>

  </div>
</section>

<!-- Custom Tab Styling -->
<style>
/* Modern Tab Styling */
.nav-tabs .nav-link {
  color: #6c757d;
  transition: all 0.3s;
}

.nav-tabs .nav-link:hover {
  color: #007bff;
  background: transparent;
}

.nav-tabs .nav-link.active {
  color: #007bff !important;
  background: transparent !important;
  border-bottom: 3px solid #007bff !important;
  font-weight: 600;
}

.nav-tabs .nav-link i {
  font-size: 0.9rem;
}

/* Card improvements */
.card {
  transition: box-shadow 0.2s;
}

/* Table styling */
.table thead th {
  background: #f8f9fa;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6c757d;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Button improvements */
.btn-xs {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  line-height: 1.5;
  border-radius: 0.375rem;
}
</style>

<!-- MODAL EDIT GURU MAPEL -->
<div class="modal fade" id="modalEditGuruMapel" tabindex="-1" role="dialog" aria-labelledby="modalEditGuruMapelLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
      <div class="modal-header bg-warning text-dark py-3">
        <h5 class="modal-title font-weight-bold" id="modalEditGuruMapelLabel">
          <i class="fas fa-edit mr-2"></i> Edit Penugasan Guru Mata Pelajaran
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= BASE_URL ?>penugasan_guru/update_guru_mapel" method="POST">
        <input type="hidden" name="id_guru" id="edit_gm_id_guru">
        <div class="modal-body p-4">
          <!-- INFO GURU -->
          <div class="alert alert-light border d-flex align-items-center mb-4 p-3 rounded-lg" style="background: #f8fafc;">
            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center mr-3" style="width: 44px; height: 44px; font-size: 1.2rem;">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
              <div class="text-muted small">Guru yang Ditugaskan:</div>
              <h5 class="font-weight-bold mb-0 text-dark" id="edit_gm_nama_guru">-</h5>
            </div>
          </div>

          <!-- CHECKBOX MAPEL -->
          <div class="form-group mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="font-weight-bold text-dark mb-0"><i class="fas fa-book mr-1 text-warning"></i> Mata Pelajaran yang Diampu:</label>
              <div>
                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill mr-1" onclick="toggleCheckboxes('#modalEditGuruMapel .edit-mapel-cb', true)">Pilih Semua</button>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill" onclick="toggleCheckboxes('#modalEditGuruMapel .edit-mapel-cb', false)">Hapus Semua</button>
              </div>
            </div>
            <div class="row px-2" style="max-height: 220px; overflow-y: auto; background: #fafbfc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
              <?php
              $current_kategori = '';
              foreach ($all_mapel as $m):
                if ($current_kategori !== ($m['kategori_mapel'] ?? '')):
                  $current_kategori = $m['kategori_mapel'] ?? '';
              ?>
                <div class="col-12 mt-2 mb-1">
                  <h6 class="font-weight-bold text-primary mb-1 border-bottom pb-1" style="font-size: 0.8rem;"><?= htmlspecialchars($current_kategori ?: 'Umum') ?></h6>
                </div>
              <?php endif; ?>
                <div class="col-md-4 col-6 mb-2">
                  <div class="custom-control custom-checkbox">
                    <input class="custom-control-input edit-mapel-cb" type="checkbox" name="id_mapel[]" value="<?= $m['id_mapel'] ?>" id="edit_mapel_<?= $m['id_mapel'] ?>">
                    <label class="custom-control-label small" for="edit_mapel_<?= $m['id_mapel'] ?>">
                      <?= htmlspecialchars($m['nama_mapel']) ?>
                    </label>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <small class="text-muted">Centang mapel yang akan diajarkan oleh guru ini.</small>
          </div>

          <!-- CHECKBOX KELAS -->
          <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="font-weight-bold text-dark mb-0"><i class="fas fa-chalkboard mr-1 text-info"></i> Kelas yang Diampu:</label>
              <div>
                <button type="button" class="btn btn-xs btn-outline-info rounded-pill mr-1" onclick="toggleCheckboxes('#modalEditGuruMapel .edit-kelas-cb', true)">Pilih Semua</button>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill" onclick="toggleCheckboxes('#modalEditGuruMapel .edit-kelas-cb', false)">Hapus Semua</button>
              </div>
            </div>
            <div class="row px-2" style="max-height: 200px; overflow-y: auto; background: #fafbfc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
              <?php foreach ($all_kelas as $k): ?>
                <div class="col-md-3 col-6 mb-2">
                  <div class="custom-control custom-checkbox">
                    <input class="custom-control-input edit-kelas-cb" type="checkbox" name="id_kelas[]" value="<?= $k['id_kelas'] ?>" id="edit_kelas_<?= $k['id_kelas'] ?>">
                    <label class="custom-control-label small" for="edit_kelas_<?= $k['id_kelas'] ?>">
                      <?= htmlspecialchars($k['nama_kelas']) ?>
                    </label>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <small class="text-muted">Centang kelas-kelas yang menjadi tanggung jawab guru ini.</small>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Initialize Select2 & Edit Handler -->
<script>
  function toggleCheckboxes(selector, state) {
    $(selector).prop('checked', state);
  }

  function editGuruMapel(idGuru, namaGuru, mapelIdsStr, kelasIdsStr) {
    $('#edit_gm_id_guru').val(idGuru);
    $('#edit_gm_nama_guru').text(namaGuru);

    // Reset semua checkbox
    $('#modalEditGuruMapel .edit-mapel-cb').prop('checked', false);
    $('#modalEditGuruMapel .edit-kelas-cb').prop('checked', false);

    // Check mapel-mapel yang terdaftar
    if (mapelIdsStr) {
      const mapelIds = mapelIdsStr.split(',');
      mapelIds.forEach(id => {
        $('#edit_mapel_' + id.trim()).prop('checked', true);
      });
    }

    // Check kelas-kelas yang terdaftar
    if (kelasIdsStr) {
      const kelasIds = kelasIdsStr.split(',');
      kelasIds.forEach(id => {
        $('#edit_kelas_' + id.trim()).prop('checked', true);
      });
    }

    $('#modalEditGuruMapel').modal('show');
  }

  $(document).ready(function () {
    // Initialize Select2
    if ($.fn.select2) {
      $('.select2').select2({ theme: 'bootstrap4' });
    }

    // Auto-switch Tab based on URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab) {
      // Use trigger click to ensure all bootstrap events fire correctly
      $('#tab-' + activeTab + '-tab').tab('show');
    }

    // Update URL when tab changes (so refresh keeps the tab)
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
      var tabName = $(e.target).attr('href').replace('#tab-', '');
      // Update URL without reloading
      var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?mod=penugasan_guru&tab=' + tabName;
      window.history.replaceState({path:newUrl}, '', newUrl);
    });
  });
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>