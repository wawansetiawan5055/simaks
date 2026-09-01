<?php
// app/views/laporan_kelas.php - Laporan Kelas with Dual-Tab Inline Paper Studio
include __DIR__ . '/partials/header.php'; 

$nama_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Tahun Ajaran Aktif';
$profil_sekolah_ttd = ProfilSekolahModel::getProfil($pdo);
$nama_kepsek = $profil_sekolah_ttd['nama_kepala_sekolah'] ?? $profil_sekolah_ttd['kepala_sekolah'] ?? 'Dadun Abdul Manaf, S.E., M.Pd.';
$nip_kepsek  = $profil_sekolah_ttd['nip_kepala_sekolah'] ?? '';
$kota_sekolah = $profil_sekolah_ttd['kota'] ?? 'Sukabumi';
?>

<style>
/* --- MODERN TAB & DOCUMENT STUDIO STYLING --- */
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

.simaks-paper-studio-container {
    background-color: #525659;
    border-radius: 12px;
    padding: 30px 20px;
    margin-top: 10px;
    min-height: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.2);
}

.simaks-paper-sheet {
    background: #ffffff;
    width: 100%;
    max-width: 860px;
    min-height: 1100px;
    padding: 35px 45px;
    margin: 0 auto;
    box-shadow: 0 8px 30px rgba(0,0,0,0.35);
    border-radius: 2px;
    box-sizing: border-box;
    font-family: 'Times New Roman', Times, serif;
    color: #000000;
}

.simaks-doc-title {
    text-align: center;
    font-size: 13pt;
    font-weight: bold;
    text-transform: uppercase;
    margin: 15px 0 5px 0;
    line-height: 1.3;
}
.simaks-doc-subtitle {
    text-align: center;
    font-size: 10pt;
    font-weight: bold;
    margin-bottom: 20px;
    color: #1e293b;
}

.simaks-doc-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
    font-size: 9.5pt;
}
.simaks-doc-table th, 
.simaks-doc-table td {
    border: 1px solid #000000;
    padding: 5px 8px;
    vertical-align: middle;
}
.simaks-doc-table th {
    background-color: #f1f5f9;
    font-weight: bold;
    text-align: center;
    text-transform: uppercase;
    font-size: 9pt;
}

.simaks-doc-signature {
    width: 100%;
    margin-top: 30px;
    border: none;
    font-size: 10pt;
    page-break-inside: avoid;
}
.simaks-doc-signature td {
    border: none;
    padding: 0;
    vertical-align: top;
    text-align: center;
}

@media print {
    body, html {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .main-header, .main-sidebar, .content-header, .nav-pills-custom, .filter-box-card, .btn-studio-toolbar, .main-footer, .no-print {
        display: none !important;
    }
    .content-wrapper, .content, .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        border: none !important;
    }
    .tab-content > .tab-pane {
        display: block !important;
        opacity: 1 !important;
    }
    #tab-tabel {
        display: none !important;
    }
    #tab-preview {
        display: block !important;
    }
    .simaks-paper-studio-container {
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
        min-height: auto !important;
    }
    .simaks-paper-sheet {
        max-width: 100% !important;
        width: 100% !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>

<div class="content-header pt-3 mb-2 no-print">
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
                    <p class="text-muted small m-0">Rekapitulasi rombongan belajar, wali kelas &amp; sebaran kuota siswa</p>
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
        
        <!-- DUAL TAB NAVIGATION SWITCHER -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap no-print">
            <ul class="nav nav-pills nav-pills-custom" id="laporanTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-tabel-link" data-toggle="pill" href="#tab-tabel" role="tab" aria-controls="tab-tabel" aria-selected="true">
                        <i class="fas fa-table"></i> 1. Tabel Data Interaktif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-preview-link" data-toggle="pill" href="#tab-preview" role="tab" aria-controls="tab-preview" aria-selected="false">
                        <i class="fas fa-file-invoice"></i> 2. Lembar Dokumen Cetak (A4 Live)
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                <span class="badge badge-light border px-3 py-2 text-dark font-weight-bold shadow-sm" style="font-size: 0.88rem;">
                    <i class="fas fa-door-open text-primary mr-1"></i> Total: <?= count($kelas_list) ?> Rombel (<?= htmlspecialchars($nama_ta) ?>)
                </span>
            </div>
        </div>

        <div class="tab-content" id="laporanTabContent">
            
            <!-- TAB 1: TABEL INTERAKTIF -->
            <div class="tab-pane fade show active" id="tab-tabel" role="tabpanel" aria-labelledby="tab-tabel-link">
                <div class="card shadow-sm border-0 mb-3 no-print">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap">
                        <div class="font-weight-bold text-dark">
                            <i class="fas fa-calendar-check text-primary mr-1"></i> Tahun Ajaran Aktif: <strong><?= htmlspecialchars($nama_ta) ?></strong>
                        </div>
                        <div class="btn-group shadow-sm mt-2 mt-md-0">
                            <a href="<?= BASE_URL ?>laporan/kelas_export_excel" class="btn btn-success font-weight-bold px-3">
                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                            </a>
                            <a href="<?= BASE_URL ?>laporan/kelas_export_pdf" class="btn btn-danger font-weight-bold px-3" target="_blank">
                                <i class="fas fa-file-pdf mr-1"></i> Download PDF
                            </a>
                            <button type="button" onclick="$('#tab-preview-link').tab('show')" class="btn btn-primary font-weight-bold px-3">
                                <i class="fas fa-eye mr-1"></i> Buka Lembar Cetak
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-list text-primary mr-1"></i> Rekapitulasi Rombel &amp; Wali Kelas
                        </h3>
                        <span class="badge badge-primary px-2 py-1"><?= count($kelas_list) ?> Kelas</span>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover table-bordered text-center m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 4%" rowspan="2" class="align-middle">No</th>
                                    <th style="width: 25%" rowspan="2" class="align-middle text-left">Nama Rombel / Kelas</th>
                                    <th style="width: 35%" rowspan="2" class="align-middle text-left">Wali Kelas</th>
                                    <th colspan="3" class="align-middle">Jumlah Siswa</th>
                                </tr>
                                <tr>
                                    <th style="width: 12%">Laki-Laki (L)</th>
                                    <th style="width: 12%">Perempuan (P)</th>
                                    <th style="width: 12%">Total</th>
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
                                    $no = 1;
                                    $tot_l = 0;
                                    $tot_p = 0;
                                    $tot_all = 0;
                                    foreach ($kelas_list as $d):
                                        $total_kelas = $d['jml_l'] + $d['jml_p'];
                                        $tot_l += $d['jml_l'];
                                        $tot_p += $d['jml_p'];
                                        $tot_all += $total_kelas;
                                    ?>
                                        <tr>
                                            <td class="font-weight-bold"><?= $no++ ?></td>
                                            <td class="text-left font-weight-bold text-dark">
                                                Kelas <?= htmlspecialchars($d['nama_kelas']) ?>
                                            </td>
                                            <td class="text-left">
                                                <?= htmlspecialchars($d['nama_walas'] ?? '- Belum Ditentukan -') ?>
                                            </td>
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

            <!-- TAB 2: LEMBAR DOKUMEN CETAK A4 -->
            <div class="tab-pane fade" id="tab-preview" role="tabpanel" aria-labelledby="tab-preview-link">
                <div class="card shadow-sm border-0 mb-3 bg-white btn-studio-toolbar no-print">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <button type="button" onclick="$('#tab-tabel-link').tab('show')" class="btn btn-outline-secondary font-weight-bold">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Tabel
                            </button>
                        </div>
                        <div class="btn-group shadow-sm mt-2 mt-md-0">
                            <button type="button" onclick="window.print()" class="btn btn-success btn-lg px-4 font-weight-bold">
                                <i class="fas fa-print mr-2"></i> Cetak Lembar Dokumen Sekarang
                            </button>
                            <a href="<?= BASE_URL ?>laporan/kelas_export_pdf" class="btn btn-danger font-weight-bold px-3 d-flex align-items-center" target="_blank">
                                <i class="fas fa-file-pdf mr-1"></i> Download PDF
                            </a>
                            <a href="<?= BASE_URL ?>laporan/kelas_export_excel" class="btn btn-success font-weight-bold px-3 d-flex align-items-center">
                                <i class="fas fa-file-excel mr-1"></i> Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="simaks-paper-studio-container">
                    <div class="simaks-paper-sheet">
                        <?php include __DIR__ . '/partials/kop_surat_universal.php'; ?>

                        <div class="simaks-doc-title">
                            LAPORAN REKAPITULASI ROMBONGAN BELAJAR &amp; KELAS
                        </div>
                        <div class="simaks-doc-subtitle">
                            Tahun Ajaran <?= htmlspecialchars($nama_ta) ?>
                        </div>

                        <table class="simaks-doc-table">
                            <thead>
                                <tr>
                                    <th width="5%" rowspan="2">No</th>
                                    <th rowspan="2">Nama Rombongan Belajar</th>
                                    <th rowspan="2">Wali Kelas</th>
                                    <th colspan="3">Jumlah Siswa</th>
                                </tr>
                                <tr>
                                    <th width="10%">L</th>
                                    <th width="10%">P</th>
                                    <th width="12%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($kelas_list)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 20px; font-style: italic;">
                                            Tidak ada data rombel untuk ditampilkan.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $no = 1;
                                    $tot_l = 0; $tot_p = 0; $tot_all = 0;
                                    foreach ($kelas_list as $d):
                                        $total_kelas = $d['jml_l'] + $d['jml_p'];
                                        $tot_l += $d['jml_l'];
                                        $tot_p += $d['jml_p'];
                                        $tot_all += $total_kelas;
                                    ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: bold;"><?= $no++ ?></td>
                                            <td style="font-weight: bold;">Kelas <?= htmlspecialchars($d['nama_kelas']) ?></td>
                                            <td><?= htmlspecialchars($d['nama_walas'] ?? '- Belum Ditentukan -') ?></td>
                                            <td style="text-align: center;"><?= $d['jml_l'] ?></td>
                                            <td style="text-align: center;"><?= $d['jml_p'] ?></td>
                                            <td style="text-align: center; font-weight: bold;"><?= $total_kelas ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                                        <td colspan="3" style="text-align: right; padding-right: 15px;">TOTAL KESELURUHAN</td>
                                        <td style="text-align: center;"><?= $tot_l ?></td>
                                        <td style="text-align: center;"><?= $tot_p ?></td>
                                        <td style="text-align: center; font-size: 10.5pt;"><?= $tot_all ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <table class="simaks-doc-signature">
                            <tr>
                                <td width="50%">
                                    Mengetahui,<br>
                                    Kepala Sekolah
                                    <br><br><br><br>
                                    <strong><u><?= htmlspecialchars($nama_kepsek) ?></u></strong>
                                    <?php if (!empty($nip_kepsek)): ?>
                                        <br>NIP. <?= htmlspecialchars($nip_kepsek) ?>
                                    <?php endif; ?>
                                </td>
                                <td width="50%">
                                    <?= htmlspecialchars($kota_sekolah) ?>, <?= tgl_indo() ?><br>
                                    Waka Kurikulum / Kesiswaan
                                    <br><br><br><br>
                                    <strong><u><?= htmlspecialchars($profil_sekolah_ttd['nama_waka_kurikulum'] ?? 'Pengelola Data') ?></u></strong><br>
                                    NIP. -
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>