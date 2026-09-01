<?php
// app/views/laporan_siswa.php - Laporan Data Siswa with Embedded In-Body Native PDF Viewer
include __DIR__ . '/partials/header.php'; 

$nama_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Tahun Ajaran Aktif';
$selected_kelas_name = 'Semua Kelas';
if (!empty($_GET['kelas'])) {
    foreach ($kelas_list as $k) {
        if ($k['id_kelas'] == $_GET['kelas']) {
            $selected_kelas_name = 'Kelas ' . $k['nama_kelas'];
            break;
        }
    }
}

$query_params = $_GET;
unset($query_params['mod'], $query_params['act']);
$new_query_string = http_build_query($query_params);
$pdf_url = BASE_URL . 'laporan/siswa_export_pdf?' . $new_query_string;
?>

<style>
.nav-pills-custom .nav-link {
    font-weight: 600;
    font-size: 0.95rem;
    color: #475569;
    padding: 10px 22px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-right: 8px;
}
.nav-pills-custom .nav-link:hover {
    color: #0284c7;
    background: #f0f9ff;
    border-color: #bae6fd;
}
.nav-pills-custom .nav-link.active {
    color: #ffffff !important;
    background: linear-gradient(135deg, #0284c7, #0369a1) !important;
    border-color: #0284c7 !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
}

.pdf-inbody-card {
    border-radius: 14px;
    overflow: hidden;
    height: 820px;
    background-color: #525659;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    border: 1px solid #cbd5e1;
}
.pdf-inbody-frame {
    width: 100%;
    height: 100%;
    border: none;
    display: block;
}
</style>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-user-graduate"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Laporan Data Peserta Didik
          </h4>
          <p class="text-muted small m-0">Rekapitulasi data siswa aktif per kelas &amp; pratinjau lembar cetak resmi</p>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Siswa</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    
    <!-- DUAL TAB NAVIGATION SWITCHER -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
      <ul class="nav nav-pills nav-pills-custom" id="laporanTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="tab-tabel-link" data-toggle="pill" href="#tab-tabel" role="tab" aria-controls="tab-tabel" aria-selected="true">
            <i class="fas fa-table"></i> 1. Tabel Data Interaktif
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="tab-preview-link" data-toggle="pill" href="#tab-preview" role="tab" aria-controls="tab-preview" aria-selected="false">
            <i class="fas fa-file-pdf"></i> 2. Pratinjau Dokumen Cetak (PDF Live)
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
        <span class="badge badge-light border px-3 py-2 text-dark font-weight-bold shadow-sm" style="font-size: 0.88rem;">
          <i class="fas fa-users text-primary mr-1"></i> Total: <?= count($siswa_list) ?> Siswa (<?= htmlspecialchars($selected_kelas_name) ?>)
        </span>
      </div>
    </div>

    <div class="tab-content" id="laporanTabContent">
      
      <!-- ============================================================= -->
      <!-- TAB 1: TABEL DATA INTERAKTIF & FILTER -->
      <!-- ============================================================= -->
      <div class="tab-pane fade show active" id="tab-tabel" role="tabpanel" aria-labelledby="tab-tabel-link">
        
        <div class="card shadow-sm border-0 mb-3">
          <div class="card-body p-3">
            <form method="get" class="m-0">
              <input type="hidden" name="mod" value="laporan">
              <input type="hidden" name="act" value="siswa">

              <div class="row align-items-end">
                <div class="col-md-4 form-group mb-2 mb-md-0">
                  <label for="kelasFilter" class="font-weight-bold small text-muted text-uppercase mb-1">
                    <i class="fas fa-filter text-primary mr-1"></i> Filter Rombongan Belajar (Kelas)
                  </label>
                  <select name="kelas" id="kelasFilter" class="form-control font-weight-bold" onchange="this.form.submit()">
                    <option value="">-- Tampilkan Semua Kelas --</option>
                    <?php foreach ($kelas_list as $k): ?>
                      <option value="<?= $k['id_kelas'] ?>" <?= isset($_GET['kelas']) && $_GET['kelas'] == $k['id_kelas'] ? 'selected' : ''; ?>>
                        Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-8 text-md-right">
                  <div class="btn-group shadow-sm">
                    <a href="<?= BASE_URL ?>laporan/siswa_export_excel?<?= $new_query_string ?>" class="btn btn-success font-weight-bold px-3">
                      <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <button type="button" onclick="$('#tab-preview-link').tab('show')" class="btn btn-primary font-weight-bold px-3">
                      <i class="fas fa-print mr-1"></i> Pratinjau Lembar Cetak
                    </button>
                    <a href="<?= $pdf_url ?>" class="btn btn-danger font-weight-bold px-3" target="_blank">
                      <i class="fas fa-external-link-alt mr-1"></i> Buka Full PDF
                    </a>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="card shadow-sm border-0">
          <div class="card-header bg-white border-bottom-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold text-dark">
              <i class="fas fa-list text-primary mr-1"></i> Daftar Data Siswa (<?= htmlspecialchars($selected_kelas_name) ?>)
            </h3>
            <span class="badge badge-primary px-2 py-1"><?= count($siswa_list) ?> Siswa</span>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped text-nowrap m-0">
              <thead class="bg-light">
                <tr>
                  <th width="4%" class="text-center">No</th>
                  <th>Nama Lengkap Siswa</th>
                  <th width="12%">NISN</th>
                  <th width="12%">NIPD</th>
                  <th width="8%" class="text-center">L/P</th>
                  <th width="12%" class="text-center">Kelas</th>
                  <th width="10%" class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($siswa_list)): ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                      <i class="fas fa-user-slash fa-3x mb-3 text-muted"></i><br>
                      <strong>Tidak ada data siswa ditemukan untuk filter ini.</strong>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php $no = 1; foreach ($siswa_list as $s): ?>
                    <tr>
                      <td class="text-center font-weight-bold"><?= $no++ ?></td>
                      <td class="font-weight-bold text-dark"><?= htmlspecialchars($s['nama']) ?></td>
                      <td><code><?= htmlspecialchars($s['nisn'] ?: '-') ?></code></td>
                      <td><code><?= htmlspecialchars($s['nipd'] ?: '-') ?></code></td>
                      <td class="text-center"><?= htmlspecialchars($s['jk']) ?></td>
                      <td class="text-center">
                        <span class="badge badge-light border px-2 py-1 font-weight-bold">
                          Kelas <?= htmlspecialchars($s['nama_kelas'] ?? '-') ?>
                        </span>
                      </td>
                      <td class="text-center">
                        <?php if ($s['status_aktif'] == 'Aktif'): ?>
                          <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                        <?php else: ?>
                          <span class="badge badge-secondary px-2 py-1"><?= htmlspecialchars($s['status_aktif']) ?></span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- ============================================================= -->
      <!-- TAB 2: PRATINJAU DOKUMEN CETAK (NATIVE IN-BODY PDF VIEWER) -->
      <!-- ============================================================= -->
      <div class="tab-pane fade" id="tab-preview" role="tabpanel" aria-labelledby="tab-preview-link">
        
        <div class="card shadow-sm border-0 mb-3 bg-white">
          <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap">
            <div>
              <button type="button" onclick="$('#tab-tabel-link').tab('show')" class="btn btn-outline-secondary font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Tabel Data
              </button>
            </div>
            <div class="btn-group shadow-sm mt-2 mt-md-0">
              <a href="<?= $pdf_url ?>" class="btn btn-danger font-weight-bold px-3" target="_blank">
                <i class="fas fa-external-link-alt mr-1"></i> Buka di Tab Penuh
              </a>
              <a href="<?= BASE_URL ?>laporan/siswa_export_excel?<?= $new_query_string ?>" class="btn btn-success font-weight-bold px-3">
                <i class="fas fa-file-excel mr-1"></i> Download Excel
              </a>
            </div>
          </div>
        </div>

        <!-- Embedded Native PDF Stream Card -->
        <div class="card shadow-sm border-0 pdf-inbody-card mb-4">
          <iframe id="pdfPreviewFrame" src="<?= $pdf_url ?>" class="pdf-inbody-frame" title="Pratinjau Lembar Cetak PDF"></iframe>
        </div>

      </div>

    </div>

  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>