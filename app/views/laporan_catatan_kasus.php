<?php
// app/views/laporan_catatan_kasus.php - Clean, Fast & Standard Laporan Catatan Kasus
include __DIR__ . '/partials/header.php'; 

$nama_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Tahun Ajaran Aktif';
$query_string_custom = "kelas=" . urlencode($kelas ?? '') . "&tanggal1=" . urlencode($tanggal1 ?? '') . "&tanggal2=" . urlencode($tanggal2 ?? '');
$pdf_url = BASE_URL . 'laporan/catatan_kasus_export_pdf?' . $query_string_custom;
$excel_url = BASE_URL . 'laporan/catatan_kasus_export_excel?' . $query_string_custom;
?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Laporan Catatan Kasus Siswa (BK)
                    </h4>
                    <p class="text-muted small m-0">Tahun Ajaran <?= htmlspecialchars($nama_ta) ?></p>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Kasus BK</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <form method="GET" class="m-0">
                    <input type="hidden" name="mod" value="laporan">
                    <input type="hidden" name="act" value="catatan_kasus">

                    <div class="row align-items-end">
                        <div class="col-md-3 form-group mb-2 mb-md-0">
                            <label class="font-weight-bold small text-muted text-uppercase mb-1">Pilih Kelas</label>
                            <select name="kelas" class="form-control font-weight-bold" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_list as $k): ?>
                                    <option value="<?= $k['id_kelas'] ?>" <?= ($kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                                        Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-2 mb-md-0">
                            <label class="font-weight-bold small text-muted text-uppercase mb-1">Tanggal Mulai</label>
                            <input type="date" name="tanggal1" class="form-control" value="<?= htmlspecialchars($tanggal1 ?? '') ?>" required>
                        </div>
                        <div class="col-md-3 form-group mb-2 mb-md-0">
                            <label class="font-weight-bold small text-muted text-uppercase mb-1">Tanggal Selesai</label>
                            <input type="date" name="tanggal2" class="form-control" value="<?= htmlspecialchars($tanggal2 ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-search mr-1"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title font-weight-bold text-dark mb-2 mb-md-0">
                    <i class="fas fa-list text-primary mr-1"></i> Hasil Laporan Kasus Siswa
                </h3>
                <div class="btn-group shadow-sm">
                    <a href="<?= $excel_url ?>" class="btn btn-success btn-sm font-weight-bold px-3">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href="<?= $pdf_url ?>" class="btn btn-danger btn-sm font-weight-bold px-3" target="_blank">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                    <a href="<?= $pdf_url ?>" class="btn btn-info btn-sm font-weight-bold px-3" target="_blank">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </a>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <?php if (!empty($list)): ?>
                    <table class="table table-striped table-hover m-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="4%" class="text-center">No</th>
                                <th width="12%">Tanggal</th>
                                <th width="12%">Kelas</th>
                                <th width="20%">Nama Siswa</th>
                                <th>Kasus / Catatan Pembinaan</th>
                                <th>Tindak Lanjut</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($list as $l): ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td><?= tgl_indo($l['tanggal']) ?></td>
                                    <td><span class="badge badge-light border font-weight-bold">Kelas <?= htmlspecialchars($l['nama_kelas']) ?></span></td>
                                    <td class="font-weight-bold text-dark"><?= htmlspecialchars($l['nama']) ?></td>
                                    <td><?= htmlspecialchars($l['catatan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($l['tindak_lanjut'] ?? '-') ?></td>
                                    <td><span class="badge badge-info px-2 py-1"><?= htmlspecialchars($l['keterangan'] ?? 'Tercatat') ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-clipboard-check fa-3x mb-3 text-muted"></i><br>
                        Silakan pilih filter kelas dan tanggal, lalu klik <strong>Tampilkan</strong>.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>