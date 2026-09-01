<?php
// app/views/laporan_kelas.php - Unified Seamless In-Body PDF Preview Studio
include __DIR__ . '/partials/header.php'; 

$nama_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Tahun Ajaran Aktif';
$pdf_url = BASE_URL . 'laporan/kelas_export_pdf';
$excel_url = BASE_URL . 'laporan/kelas_export_excel';
?>

<style>
.preview-unified-card {
    border-radius: 12px;
    overflow: hidden;
    background-color: #323639;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    border: 1px solid #45494d;
    margin-bottom: 20px;
}
.preview-unified-header {
    background: #2a2e33;
    padding: 10px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #3f4448;
}
.preview-unified-title {
    color: #f1f5f9;
    font-size: 0.95rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-icon-studio {
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 0.88rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-icon-studio:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(255, 255, 255, 0.25);
}
.btn-icon-studio-danger {
    color: #fca5a5;
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 0.88rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-icon-studio-danger:hover {
    color: #ffffff;
    background: #ef4444;
    border-color: #ef4444;
}
.preview-unified-body {
    height: calc(100vh - 120px);
    min-height: 680px;
    width: 100%;
    position: relative;
    background-color: #525659;
}
.preview-unified-frame {
    width: 100%;
    height: 100%;
    border: none;
    display: block;
}
</style>

<div id="sectionMainData">
  <div class="content-header pt-3 mb-2">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm-6 col-12 d-flex align-items-center">
          <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
            <i class="fas fa-chalkboard"></i>
          </div>
          <div>
            <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
              Laporan Rekapitulasi Rombel &amp; Kelas
            </h4>
            <p class="text-muted small m-0">Tahun Ajaran <?= htmlspecialchars($nama_ta) ?></p>
          </div>
        </div>
        <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
          <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
            <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Kelas</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap">
          <div class="font-weight-bold text-dark">
            <i class="fas fa-calendar-check text-primary mr-1"></i> Rekapitulasi Rombel &amp; Wali Kelas
          </div>
          <div class="btn-group shadow-sm mt-2 mt-md-0">
            <a href="<?= $excel_url ?>" class="btn btn-success font-weight-bold px-3">
              <i class="fas fa-file-excel mr-1"></i> Excel
            </a>
            <button type="button" onclick="openFullscreenPreview()" class="btn btn-info font-weight-bold px-4">
              <i class="fas fa-print mr-1"></i> Cetak / Pratinjau
            </button>
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
          <h3 class="card-title font-weight-bold text-dark">
            <i class="fas fa-list text-primary mr-1"></i> Daftar Rombongan Belajar
          </h3>
          <span class="badge badge-primary px-2 py-1"><?= count($kelas_list) ?> Kelas</span>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-striped table-hover table-bordered text-center m-0">
            <thead class="bg-light">
              <tr>
                <th style="width: 4%" rowspan="2" class="align-middle">No</th>
                <th style="width: 28%" rowspan="2" class="align-middle text-left">Nama Rombel / Kelas</th>
                <th style="width: 36%" rowspan="2" class="align-middle text-left">Wali Kelas</th>
                <th colspan="3" class="align-middle">Jumlah Siswa</th>
              </tr>
              <tr>
                <th style="width: 11%">L</th>
                <th style="width: 11%">P</th>
                <th style="width: 10%">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($kelas_list)): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-5">
                    <i class="fas fa-info-circle fa-3x mb-3 text-muted"></i><br>
                    Data kelas belum tersedia.
                  </td>
                </tr>
              <?php else: ?>
                <?php
                $no = 1; $tot_l = 0; $tot_p = 0; $tot_all = 0;
                foreach ($kelas_list as $d):
                  $total_kelas = $d['jml_l'] + $d['jml_p'];
                  $tot_l += $d['jml_l'];
                  $tot_p += $d['jml_p'];
                  $tot_all += $total_kelas;
                ?>
                  <tr>
                    <td class="font-weight-bold"><?= $no++ ?></td>
                    <td class="text-left font-weight-bold text-dark">Kelas <?= htmlspecialchars($d['nama_kelas']) ?></td>
                    <td class="text-left"><?= htmlspecialchars($d['nama_walas'] ?? '- Belum Ditentukan -') ?></td>
                    <td><?= $d['jml_l'] ?></td>
                    <td><?= $d['jml_p'] ?></td>
                    <td class="font-weight-bold text-primary"><?= $total_kelas ?></td>
                  </tr>
                <?php endforeach; ?>

                <tr class="bg-light font-weight-bold text-dark" style="font-size: 1rem;">
                  <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                  <td class="text-info"><?= $tot_l ?></td>
                  <td class="text-pink"><?= $tot_p ?></td>
                  <td class="text-success"><?= $tot_all ?> Siswa</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<div id="sectionPreviewStudio" style="display: none;" class="pt-2">
  <div class="container-fluid">
    <div class="preview-unified-card">
      <div class="preview-unified-header">
        <div class="d-flex align-items-center">
          <button type="button" onclick="closeFullscreenPreview()" class="btn-icon-studio mr-2" title="Kembali ke Tabel Data">
            <i class="fas fa-arrow-left"></i> <span>Kembali</span>
          </button>
          <button type="button" onclick="closeFullscreenPreview()" class="btn-icon-studio-danger mr-3" title="Tutup Pratinjau">
            <i class="fas fa-times"></i> <span>Tutup</span>
          </button>
          <div class="preview-unified-title d-none d-md-flex">
            <i class="fas fa-file-pdf text-info"></i>
            <span>Pratinjau: Laporan Rekapitulasi Rombel &amp; Kelas</span>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" onclick="printPreviewFrame()" class="btn-icon-studio mr-2" title="Cetak Dokumen">
            <i class="fas fa-print text-success"></i> <span>Cetak</span>
          </button>
          <a href="<?= $pdf_url ?>" class="btn-icon-studio" target="_blank" title="Unduh File PDF">
            <i class="fas fa-download text-primary"></i> <span>Unduh PDF</span>
          </a>
        </div>
      </div>
      <div class="preview-unified-body">
        <iframe id="pdfStudioFrame" src="" class="preview-unified-frame" title="Pratinjau Cetak Lembar Dokumen"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
var pdfReportUrl = <?= json_encode($pdf_url) ?>;

function openFullscreenPreview() {
    var frame = document.getElementById('pdfStudioFrame');
    if (!frame.src || frame.src === 'about:blank' || frame.src === window.location.href) {
        frame.src = pdfReportUrl + (pdfReportUrl.indexOf('?') !== -1 ? '&' : '?') + '_t=' + new Date().getTime();
    }
    document.getElementById('sectionMainData').style.display = 'none';
    document.getElementById('sectionPreviewStudio').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function closeFullscreenPreview() {
    document.getElementById('sectionPreviewStudio').style.display = 'none';
    document.getElementById('sectionMainData').style.display = 'block';
}

function printPreviewFrame() {
    var iframe = document.getElementById('pdfStudioFrame');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    } else {
        window.open(pdfReportUrl, '_blank');
    }
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>