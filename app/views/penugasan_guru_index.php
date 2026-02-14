<?php include __DIR__ . '/partials/header.php'; ?>

<!-- Content Header -->
<!-- Content Header -->
<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 px-4">
      <div>
        <h2 class="m-0 font-weight-bold text-dark">
          <i class="fas fa-user-shield text-primary mr-2"></i> Penugasan GTK
        </h2>
        <p class="text-muted small mb-0">
          Kelola penugasan Guru dan Tenaga Kependidikan untuk TA: <?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') ?>
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<section class="content">
  <div class="container-fluid">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
      <div class="card-header p-0 bg-white" style="border-bottom: 2px solid #e9ecef;">
        <ul class="nav nav-tabs border-0" id="custom-tabs-three-tab" role="tablist" style="padding: 0 1rem;">
          <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'walas' ? 'active' : '' ?> font-weight-semibold" 
               id="tab-walas-tab" data-toggle="pill" href="#tab-walas" role="tab" 
               aria-controls="tab-walas" aria-selected="<?= $active_tab == 'walas' ? 'true' : 'false' ?>"
               style="border: none; border-bottom: 3px solid transparent; padding: 1rem 1.5rem;">
              <i class="fas fa-chalkboard-teacher mr-1"></i> Wali Kelas
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'mapel' ? 'active' : '' ?> font-weight-semibold" 
               id="tab-mapel-tab" data-toggle="pill" href="#tab-mapel" role="tab" 
               aria-controls="tab-mapel" aria-selected="<?= $active_tab == 'mapel' ? 'true' : 'false' ?>"
               style="border: none; border-bottom: 3px solid transparent; padding: 1rem 1.5rem;">
              <i class="fas fa-book mr-1"></i> Guru Mapel
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'jabatan_guru' ? 'active' : '' ?> font-weight-semibold" 
               id="tab-jabatan_guru-tab" data-toggle="pill" href="#tab-jabatan_guru" role="tab" 
               aria-controls="tab-jabatan_guru" aria-selected="<?= $active_tab == 'jabatan_guru' ? 'true' : 'false' ?>"
               style="border: none; border-bottom: 3px solid transparent; padding: 1rem 1.5rem;">
              <i class="fas fa-user-edit mr-1"></i> Tugas Tambahan (Guru)
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'jabatan_staff' ? 'active' : '' ?> font-weight-semibold" 
               id="tab-jabatan_staff-tab" data-toggle="pill" href="#tab-jabatan_staff" role="tab" 
               aria-controls="tab-jabatan_staff" aria-selected="<?= $active_tab == 'jabatan_staff' ? 'true' : 'false' ?>"
               style="border: none; border-bottom: 3px solid transparent; padding: 1rem 1.5rem;">
              <i class="fas fa-user-cog mr-1"></i> Jabatan Struktural (Staff)
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'pembina' ? 'active' : '' ?> font-weight-semibold" 
               id="tab-pembina-tab" data-toggle="pill" href="#tab-pembina" role="tab" 
               aria-controls="tab-pembina" aria-selected="<?= $active_tab == 'pembina' ? 'true' : 'false' ?>"
               style="border: none; border-bottom: 3px solid transparent; padding: 1rem 1.5rem;">
              <i class="fas fa-users mr-1"></i> Pembina Non-Akademik
            </a>
          </li>
        </ul>
      </div>
      <div class="card-body p-4">
        <div class="tab-content" id="custom-tabs-three-tabContent">

          <!-- TAB 1: WALI KELAS -->
          <div class="tab-pane fade <?= $active_tab == 'walas' ? 'show active' : '' ?>" id="tab-walas" role="tabpanel"
            aria-labelledby="tab-walas-tab">
            <!-- Content Walas -->
            <div class="row">
              <div class="col-md-4">
                <div class="card card-info">
                  <div class="card-header">
                    <h3 class="card-title">Form Penugasan Wali Kelas</h3>
                  </div>
                  <form action="index.php?mod=penugasan_guru&act=save_walas" method="POST">
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
              <div class="col-md-8">
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
                          <th style="width: 40px">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($walas_list as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                            <td><a
                                href="index.php?mod=penugasan_guru&act=delete_walas&id=<?= $row['id_penugasan_wali_kelas'] ?>"
                                class="btn btn-xs btn-danger btn-delete-confirm" onclick="return confirmDelete(event)"><i
                                  class="fa fa-trash"></i></a></td>
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
              <!-- ... (Form & Table Mapel) ... -->
              <div class="col-md-4">
                <div class="card card-warning">
                  <!-- ... content ... -->
                  <div class="card-header">
                    <h3 class="card-title">Form Penugasan Guru Mapel</h3>
                  </div>
                  <form action="index.php?mod=penugasan_guru&act=save_guru_mapel" method="POST">
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
                        <label>Mata Pelajaran</label>
                        <select name="id_mapel" class="form-control select2" required>
                          <option value="">-- Pilih Mapel --</option>
                          <?php foreach ($all_mapel as $m): ?>
                            <option value="<?= $m['id_mapel'] ?>"><?= $m['nama_mapel'] ?> (<?= $m['kategori_mapel'] ?>)
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-warning">Simpan Guru Mapel</button>
                    </div>
                  </form>
                </div>
              </div>
              <div class="col-md-8">
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
                          <th style="width: 40px">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($guru_mapel_list as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td><?= htmlspecialchars($row['nama_mapel']) ?></td>
                            <td><a
                                href="index.php?mod=penugasan_guru&act=delete_guru_mapel&id=<?= $row['id_guru_mapel'] ?>"
                                class="btn btn-xs btn-danger btn-delete-confirm" onclick="return confirmDelete(event)"><i
                                  class="fa fa-trash"></i></a></td>
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
              <div class="col-md-4">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Form Tugas Tambahan Guru</h3>
                  </div>
                  <form action="index.php?mod=penugasan_guru&act=save_jabatan" method="POST">
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
                        <select name="jenis_jabatan" class="form-control select2" required style="width: 100%;">
                          <option value="">-- Pilih Jabatan --</option>
                          <?php foreach ($master_jabatan_guru as $mj): ?>
                            <option value="<?= $mj['nama_jabatan'] ?>"><?= $mj['nama_jabatan'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-success">Simpan Tugas</button></div>
                  </form>
                </div>
              </div>
              <div class="col-md-8">
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
                          <th style="width: 40px">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $has_guru_data = false;
                        foreach ($jabatan_list as $row): 
                          // Cek apakah jenis_jabatan ini termasuk kategori GURU
                          $is_guru_role = false;
                          foreach($master_jabatan_guru as $mj) if($mj['nama_jabatan'] == $row['jenis_jabatan']) $is_guru_role = true;
                          if(!$is_guru_role) continue;
                          $has_guru_data = true;
                        ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td><span class="badge badge-success"><?= htmlspecialchars($row['jenis_jabatan']) ?></span></td>
                            <td><a href="index.php?mod=penugasan_guru&act=delete_jabatan&id=<?= $row['id_penugasan_jabatan'] ?>&tab=jabatan_guru" class="btn btn-xs btn-danger btn-delete-confirm"><i class="fa fa-trash"></i></a></td>
                          </tr>
                        <?php endforeach; ?>
                        <?php if (!$has_guru_data): ?>
                          <tr><td colspan="3" class="text-center p-3 text-muted">Belum ada data tugas tambahan guru.</td></tr>
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
              <div class="col-md-4">
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title">Form Jabatan Struktural Staff</h3>
                  </div>
                  <form action="index.php?mod=penugasan_guru&act=save_jabatan" method="POST">
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
                        <select name="jenis_jabatan" class="form-control select2" required style="width: 100%;">
                          <option value="">-- Pilih Jabatan --</option>
                          <?php foreach ($master_jabatan_staff as $mj): ?>
                            <option value="<?= $mj['nama_jabatan'] ?>"><?= $mj['nama_jabatan'] ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Jabatan</button></div>
                  </form>
                </div>
              </div>
              <div class="col-md-8">
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
                          <th style="width: 40px">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $has_staff_data = false;
                        foreach ($jabatan_list as $row): 
                          // Cek apakah jenis_jabatan ini termasuk kategori STAFF
                          $is_staff_role = false;
                          foreach($master_jabatan_staff as $mj) if($mj['nama_jabatan'] == $row['jenis_jabatan']) $is_staff_role = true;
                          if(!$is_staff_role) continue;
                          $has_staff_data = true;
                        ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($row['jenis_jabatan']) ?></span></td>
                            <td><a href="index.php?mod=penugasan_guru&act=delete_jabatan&id=<?= $row['id_penugasan_jabatan'] ?>&tab=jabatan_staff" class="btn btn-xs btn-danger btn-delete-confirm"><i class="fa fa-trash"></i></a></td>
                          </tr>
                        <?php endforeach; ?>
                        <?php if (!$has_staff_data): ?>
                          <tr><td colspan="3" class="text-center p-3 text-muted">Belum ada data jabatan staff.</td></tr>
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
              <div class="col-md-4">
                <div class="card card-purple">
                  <div class="card-header">
                    <h3 class="card-title">Form Pembina Kegiatan</h3>
                  </div>
                  <form action="index.php?mod=penugasan_guru&act=save_pembina" method="POST">
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
              <div class="col-md-8">
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
                          <th style="width: 40px">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($pembina_list)): ?>
                          <tr>
                            <td colspan="4" class="text-center">Belum ada data pembina.</td>
                          </tr>
                        <?php endif; ?>
                        <?php foreach ($pembina_list as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($row['jenis_kegiatan']) ?></span></td>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td><a
                                href="index.php?mod=penugasan_guru&act=delete_pembina&id=<?= $row['id_penugasan_pembina'] ?>"
                                class="btn btn-xs btn-danger btn-delete-confirm"><i class="fa fa-trash"></i></a></td>
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

<!-- Initialize Select2 if available -->
<script>
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