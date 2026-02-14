<?php 
include '../app/views/partials/header.php'; 
include '../app/views/partials/sidebar.php'; 

// Role Mapping for Assignments
$roleMap = [
    'tunj_kepsek' => 'Kepala Sekolah',
    'tunj_tas' => 'Tenaga Administrasi',
    'tunj_plk' => 'Petugas Layanan Khusus',
    'tunj_penjaga' => 'Penjaga Sekolah',
    'tunj_satpam' => 'Satpam',
    'tunj_sopir' => 'Sopir',
    'tunj_kurikulum' => 'Waka Kurikulum',
    'tunj_kesiswaan' => 'Waka Kesiswaan',
    'tunj_sarpras' => 'Waka Sarpras',
    'tunj_humas' => 'Waka Humas',
    'tunj_kepala_lab' => 'Kepala Laboratorium',
    'tunj_kepala_perpus' => 'Kepala Perpustakaan',
    'tunj_operator' => 'Operator',
    'tunj_pembina_keagamaan' => 'Pembina Keagamaan',
    'tunj_pengelola_smater' => 'Pengelola Smater',
    'tunj_walas' => 'Wali Kelas'
];

function checkAssignment($id_guru, $field, $assignments, $roleMap) {
    if (!isset($roleMap[$field])) return true; // Not a mapped position field (e.g. JJM, Transport)
    $requiredRole = $roleMap[$field];
    $teacherAssignments = $assignments[$id_guru] ?? [];
    return in_array($requiredRole, $teacherAssignments);
}
?>

<style>
    .matrix-container {
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 15px rgba(0,0,-0.03);
    }
    .head-fixed thead tr:nth-child(1) th { position: sticky; top: 0; z-index: 20; }
    .head-fixed thead tr:nth-child(2) th { position: sticky; top: 35px; z-index: 20; }
    
    .sticky-col {
        position: sticky;
        left: 0;
        background-color: #f8f9fa !important;
        z-index: 10;
        border-right: 2px solid #dee2e6 !important;
    }
    
    .matrix-table th {
        vertical-align: middle !important;
        font-size: 10px;
        letter-spacing: 0.5px;
        border-bottom-width: 2px !important;
    }
    .matrix-table td {
        vertical-align: middle !important;
        padding: 4px !important;
    }
    .currency-input {
        border: 1px solid transparent !important;
        background: transparent !important;
        transition: all 0.2s;
        border-radius: 4px;
        font-weight: 600;
        width: 100% !important;
        min-width: 85px;
        font-size: 0.85rem;
    }
    .currency-input:hover, .currency-input:focus {
        background: #fff !important;
        border-color: #007bff !important;
        box-shadow: 0 0 5px rgba(0,123,255,0.2) !important;
        outline: none;
    }
    .bg-honor { background-color: #e3f2fd !important; color: #0d47a1 !important; border-top: 3px solid #0d47a1 !important; }
    .bg-jabatan { background-color: #e8f5e9 !important; color: #1b5e20 !important; border-top: 3px solid #1b5e20 !important; }
    .bg-tugas { background-color: #fff3e0 !important; color: #e65100 !important; border-top: 3px solid #e65100 !important; }
    .bg-potongan { background-color: #ffebee !important; color: #b71c1c !important; border-top: 3px solid #b71c1c !important; }
    
    .group-header {
        font-weight: 800;
        text-transform: uppercase;
        font-size: 11px;
        padding: 10px !important;
    }
    
    .modal-xl { max-width: 95% !important; }
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(231, 231, 231, 1);
        border-radius: 15px;
    }
</style>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4">
            <div>
                <h2 class="fw-bold m-0 text-dark"><i class="fas fa-th-list text-primary mr-2"></i> Matrix Konfigurasi Payroll</h2>
                <p class="text-muted small mb-0">Atur nominal spesifik per pegawai. Kosongkan untuk menggunakan nilai global.</p>
            </div>
            <div class="text-end">
                <a href="index.php?mod=keuangan_gaji" class="btn btn-default shadow-sm border mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <button type="button" class="btn btn-warning shadow-sm border mr-2" data-toggle="modal" data-target="#modalConfig">
                    <i class="fas fa-cogs mr-1"></i> Pengaturan Nominal
                </button>
                <button type="submit" form="formMatrix" class="btn btn-success shadow-sm px-4 border-0" style="background: #28a745;">
                    <i class="fas fa-save mr-1"></i> Simpan Data Matrix
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content px-3">
    <div class="container-fluid">
        
        <?php if(isset($_GET['status'])): ?>
            <?php if($_GET['status'] == 'success'): ?>
            <div class="alert alert-success shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <i class="fas fa-check-circle mr-1"></i> Berhasil memperbarui <?= $_GET['count'] ?? '' ?> data matrix payroll.
            </div>
            <?php elseif($_GET['status'] == 'config_saved'): ?>
            <div class="alert alert-primary shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <i class="fas fa-check-circle mr-1"></i> Pengaturan Nominal Global berhasil diperbarui.
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="matrix-container glass-card overflow-hidden">
            <form action="index.php?mod=keuangan_gaji&act=save_setting" method="post" id="formMatrix">
                <div class="table-responsive" style="max-height: 75vh;">
                    <table class="table table-bordered table-hover mb-0 text-nowrap matrix-table head-fixed">
                        <thead class="text-center text-uppercase font-weight-bold">
                            <tr>
                                <th rowspan="2" class="align-middle bg-light sticky-col" style="z-index: 30; left: 0; min-width: 250px; text-align:left;">NAMA PEGAWAI</th>
                                
                                <th colspan="4" class="group-header bg-honor">HONOR (VARIABEL)</th>
                                <th colspan="6" class="group-header bg-jabatan">TUNJANGAN JABATAN</th>
                                <th colspan="10" class="group-header bg-tugas">TUGAS TAMBAHAN & WALAS</th>
                                <th colspan="4" class="group-header bg-potongan">POTONGAN</th>
                            </tr>
                            <tr class="small" style="background:#fcfcfc;">
                                <!-- Honor -->
                                <th width="90" class="text-primary border-bottom">HONOR/JAM</th>
                                <th width="90" class="text-primary border-bottom">TRANSPORT</th>
                                <th width="90" class="text-primary border-bottom">INST. KBM</th>
                                <th width="90" class="text-primary border-bottom">EKSKUL</th>
                                
                                <!-- Jabatan -->
                                <th width="90" class="text-success border-bottom">KEPSEK</th>
                                <th width="90" class="text-success border-bottom">TAS</th>
                                <th width="90" class="text-success border-bottom">PLK</th>
                                <th width="90" class="text-success border-bottom">PENJAGA</th>
                                <th width="90" class="text-success border-bottom">SATPAM</th>
                                <th width="90" class="text-success border-bottom">SOPIR</th>
                                
                                <!-- Tugas Tambahan -->
                                <th width="90" class="text-warning border-bottom">KURIKULUM</th>
                                <th width="90" class="text-warning border-bottom">KESISWAAN</th>
                                <th width="90" class="text-warning border-bottom">SARPRAS</th>
                                <th width="90" class="text-warning border-bottom">HUMAS</th>
                                <th width="90" class="text-warning border-bottom">WALAS</th>
                                <th width="90" class="text-warning border-bottom">K. PERPUS</th>
                                <th width="90" class="text-warning border-bottom">K. LAB</th>
                                <th width="90" class="text-warning border-bottom">OPERATOR</th>
                                <th width="90" class="text-warning border-bottom">PEMBINA KG.</th>
                                <th width="90" class="text-warning border-bottom">SMATER</th>
                                
                                <!-- Potongan -->
                                <th width="90" class="text-danger border-left border-bottom">BPJS KES</th>
                                <th width="90" class="text-danger border-bottom">BPJS TK</th>
                                <th width="90" class="text-danger border-bottom">PINJAMAN</th>
                                <th width="90" class="text-danger border-bottom">LAINNYA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($matrix)): 
                                foreach($matrix as $row):
                                    $gid = $row['id_guru'];
                            ?>
                            <tr>
                                <td class="sticky-col font-weight-bold text-dark border-right shadow-sm" style="text-align:left;">
                                    <?= htmlspecialchars($row['nama']) ?>
                                </td>
                                
                                <!-- HONOR -->
                                <td><input type="text" name="rules[<?= $gid ?>][tarif_jjm]" class="form-control text-right currency-input" value="<?= ($row['tarif_jjm'] ?? 0) > 0 ? number_format($row['tarif_jjm'], 0, ',', '.') : '' ?>" placeholder="<?= number_format($config['tarif_jjm'] ?? 0, 0, ',', '.') ?>"></td>
                                <td><input type="text" name="rules[<?= $gid ?>][tarif_transport]" class="form-control text-right currency-input" value="<?= ($row['tarif_transport'] ?? 0) > 0 ? number_format($row['tarif_transport'], 0, ',', '.') : '' ?>" placeholder="<?= number_format($config['tarif_transport'] ?? 0, 0, ',', '.') ?>"></td>
                                <td><input type="text" name="rules[<?= $gid ?>][tarif_kinerja]" class="form-control text-right currency-input" value="<?= ($row['tarif_kinerja'] ?? 0) > 0 ? number_format($row['tarif_kinerja'], 0, ',', '.') : '' ?>" placeholder="<?= number_format($config['tarif_kinerja'] ?? 0, 0, ',', '.') ?>"></td>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_ekskul]" class="form-control text-right currency-input" value="<?= ($row['tunj_ekskul'] ?? 0) > 0 ? number_format($row['tunj_ekskul'], 0, ',', '.') : '' ?>" placeholder="<?= number_format($config['tarif_ekskul_global'] ?? 0, 0, ',', '.') ?>"></td>

                                <!-- JABATAN -->
                                <?php $assigned = checkAssignment($gid, 'tunj_kepsek', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_kepsek]" class="form-control text-right currency-input" value="<?= ($row['tunj_kepsek'] ?? 0) > 0 ? number_format($row['tunj_kepsek'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_kepsek'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_tas', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_tas]" class="form-control text-right currency-input" value="<?= ($row['tunj_tas'] ?? 0) > 0 ? number_format($row['tunj_tas'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_tas'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_plk', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_plk]" class="form-control text-right currency-input" value="<?= ($row['tunj_plk'] ?? 0) > 0 ? number_format($row['tunj_plk'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_plk'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_penjaga', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_penjaga]" class="form-control text-right currency-input" value="<?= ($row['tunj_penjaga'] ?? 0) > 0 ? number_format($row['tunj_penjaga'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_penjaga'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_satpam', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_satpam]" class="form-control text-right currency-input" value="<?= ($row['tunj_satpam'] ?? 0) > 0 ? number_format($row['tunj_satpam'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_satpam'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_sopir', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_sopir]" class="form-control text-right currency-input" value="<?= ($row['tunj_sopir'] ?? 0) > 0 ? number_format($row['tunj_sopir'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_sopir'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>

                                <!-- TUGAS -->
                                <?php $assigned = checkAssignment($gid, 'tunj_kurikulum', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_kurikulum]" class="form-control text-right currency-input" value="<?= ($row['tunj_kurikulum'] ?? 0) > 0 ? number_format($row['tunj_kurikulum'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_waka_kurikulum'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_kesiswaan', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_kesiswaan]" class="form-control text-right currency-input" value="<?= ($row['tunj_kesiswaan'] ?? 0) > 0 ? number_format($row['tunj_kesiswaan'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_waka_kesiswaan'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_sarpras', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_sarpras]" class="form-control text-right currency-input" value="<?= ($row['tunj_sarpras'] ?? 0) > 0 ? number_format($row['tunj_sarpras'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_sarpras'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_humas', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_humas]" class="form-control text-right currency-input" value="<?= ($row['tunj_humas'] ?? 0) > 0 ? number_format($row['tunj_humas'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_waka_humas'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_walas', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_walas]" class="form-control text-right currency-input" value="<?= ($row['tunj_walas'] ?? 0) > 0 ? number_format($row['tunj_walas'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_walas'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_kepala_perpus', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_kepala_perpus]" class="form-control text-right currency-input" value="<?= ($row['tunj_kepala_perpus'] ?? 0) > 0 ? number_format($row['tunj_kepala_perpus'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_kepala_perpus'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_kepala_lab', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_kepala_lab]" class="form-control text-right currency-input" value="<?= ($row['tunj_kepala_lab'] ?? 0) > 0 ? number_format($row['tunj_kepala_lab'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_kepala_lab'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_operator', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_operator]" class="form-control text-right currency-input" value="<?= ($row['tunj_operator'] ?? 0) > 0 ? number_format($row['tunj_operator'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_operator'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_pembina_keagamaan', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_pembina_keagamaan]" class="form-control text-right currency-input" value="<?= ($row['tunj_pembina_keagamaan'] ?? 0) > 0 ? number_format($row['tunj_pembina_keagamaan'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_pembina_keagamaan'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>
                                <?php $assigned = checkAssignment($gid, 'tunj_pengelola_smater', $assignments, $roleMap); ?>
                                <td><input type="text" name="rules[<?= $gid ?>][tunj_pengelola_smater]" class="form-control text-right currency-input" value="<?= ($row['tunj_pengelola_smater'] ?? 0) > 0 ? number_format($row['tunj_pengelola_smater'], 0, ',', '.') : '' ?>" placeholder="<?= $assigned ? number_format($config['tunj_pengelola_smater'] ?? 0, 0, ',', '.') : '-' ?>" <?= !$assigned ? 'disabled' : '' ?>></td>

                                <!-- POTONGAN -->
                                <td class="border-left"><input type="text" name="rules[<?= $gid ?>][potongan_bpjs_kes]" class="form-control text-right currency-input text-danger" value="<?= ($row['potongan_bpjs_kes'] ?? 0) > 0 ? number_format($row['potongan_bpjs_kes'], 0, ',', '.') : '' ?>"></td>
                                <td><input type="text" name="rules[<?= $gid ?>][potongan_bpjs_tk]" class="form-control text-right currency-input text-danger" value="<?= ($row['potongan_bpjs_tk'] ?? 0) > 0 ? number_format($row['potongan_bpjs_tk'], 0, ',', '.') : '' ?>"></td>
                                <td><input type="text" name="rules[<?= $gid ?>][potongan_kasbon]" class="form-control text-right currency-input text-danger" value="<?= ($row['potongan_kasbon'] ?? 0) > 0 ? number_format($row['potongan_kasbon'], 0, ',', '.') : '' ?>"></td>
                                <td><input type="text" name="rules[<?= $gid ?>][potongan_lain]" class="form-control text-right currency-input text-danger" value="<?= ($row['potongan_lain'] ?? 0) > 0 ? number_format($row['potongan_lain'], 0, ',', '.') : '' ?>"></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="30" class="text-center py-5">Belum ada data guru aktif.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- MODAL CONFIG - REFINED -->
<div class="modal fade" id="modalConfig" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content glass-card shadow-lg border-0" style="background: #f8f9fa;">
            <div class="modal-header border-bottom-0 pb-0 bg-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-sliders-h mr-2 text-primary"></i> Pengaturan Nominal Global</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted small mb-3 ml-1">Nilai di bawah ini menjadi acuan utama jika data pada matrix di atas kosong (Rp 0).</p>
                <form action="index.php?mod=keuangan_gaji&act=save_config" method="POST" id="formConfigModal">
                    <div class="row">
                        <!-- HONORARIUM -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-none border-0" style="border-radius: 10px; overflow:hidden;">
                                <div class="card-header bg-primary text-white py-1 font-weight-bold" style="font-size:12px;">1. HONORARIUM MENGAJAR (VARIABEL)</div>
                                <div class="card-body p-3 bg-white">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="small font-weight-bold mb-1">HONOR PER JAM (MINGGUAN)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Rp</span>
                                                <input type="text" name="tarif_jjm" class="form-control currency-input text-right font-weight-bold" value="<?= number_format($config['tarif_jjm'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small font-weight-bold mb-1">TRANSPORT / KEHADIRAN</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Rp</span>
                                                <input type="text" name="tarif_transport" class="form-control currency-input text-right font-weight-bold" value="<?= number_format($config['tarif_transport'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small font-weight-bold mb-1">INSENTIF KBM (PER JURNAL)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Rp</span>
                                                <input type="text" name="tarif_kinerja" class="form-control currency-input text-right font-weight-bold" value="<?= number_format($config['tarif_kinerja'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- JABATAN -->
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-none border-0 h-100" style="border-radius: 10px; overflow:hidden;">
                                <div class="card-header bg-success text-white py-1 font-weight-bold text-uppercase" style="font-size:12px;">2. TUNJANGAN JABATAN</div>
                                <div class="card-body p-3 bg-white">
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Kepala Sekolah</label>
                                            <input type="text" name="tunj_kepsek" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_kepsek'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Tenaga Adm. (TAS)</label>
                                            <input type="text" name="tunj_tas" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_tas'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Petugas Khusus (PLK)</label>
                                            <input type="text" name="tunj_plk" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_plk'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Penjaga Sekolah</label>
                                            <input type="text" name="tunj_penjaga" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_penjaga'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Satpam</label>
                                            <input type="text" name="tunj_satpam" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_satpam'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Sopir</label>
                                            <input type="text" name="tunj_sopir" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_sopir'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TUGAS TAMBAHAN -->
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-none border-0 h-100" style="border-radius: 10px; overflow:hidden;">
                                <div class="card-header bg-warning text-dark py-1 font-weight-bold text-uppercase" style="font-size:12px;">3. Tunjangan Tugas Tambahan</div>
                                <div class="card-body p-3 bg-white">
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Waka Kurikulum</label>
                                            <input type="text" name="tunj_waka_kurikulum" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_waka_kurikulum'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Waka Kesiswaan</label>
                                            <input type="text" name="tunj_waka_kesiswaan" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_waka_kesiswaan'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Waka Humas</label>
                                            <input type="text" name="tunj_waka_humas" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_waka_humas'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Waka Sarpras</label>
                                            <input type="text" name="tunj_sarpras" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_sarpras'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Kepala Perpus</label>
                                            <input type="text" name="tunj_kepala_perpus" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_kepala_perpus'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Kepala Lab</label>
                                            <input type="text" name="tunj_kepala_lab" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_kepala_lab'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Operator Sekolah</label>
                                            <input type="text" name="tunj_operator" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_operator'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Pembina Keagamaan</label>
                                            <input type="text" name="tunj_pembina_keagamaan" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_pembina_keagamaan'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0">Pengelola SMATER</label>
                                            <input type="text" name="tunj_pengelola_smater" class="form-control form-control-sm currency-input text-right" value="<?= number_format($config['tunj_pengelola_smater'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="small mb-0 text-primary font-weight-bold">Wali Kelas</label>
                                            <input type="text" name="tunj_walas" class="form-control form-control-sm currency-input text-right border-primary" value="<?= number_format($config['tunj_walas'] ?? 0, 0, ',', '.') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EKSKUL -->
                        <div class="col-md-12">
                            <div class="card shadow-none border-0" style="border-radius: 10px; overflow:hidden;">
                                <div class="card-header bg-dark text-white py-1 font-weight-bold" style="font-size:12px;">4. HONOR EKSTRAKURIKULER (PER KEHADIRAN)</div>
                                <div class="card-body p-3 bg-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 border-right mb-3">
                                            <label class="small font-weight-bold mb-1">TARIF GLOBAL (DEFAULT)</label>
                                            <input type="text" name="tarif_ekskul_global" class="form-control currency-input text-right font-weight-bold" value="<?= number_format($config['tarif_ekskul_global'] ?? 0, 0, ',', '.') ?>">
                                            <small class="text-muted">Digunakan jika tarif per-ekskul di sebelah kanan kosong.</small>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <?php foreach($ekskulList as $eks): 
                                                    $val = $ekskulRates[$eks['id_kegiatan']] ?? 0; ?>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="mb-0 small text-muted font-weight-bold" style="font-size: 10px;"><?= $eks['nama_kegiatan'] ?></label>
                                                        <input type="text" name="ekskul_rates[<?= $eks['id_kegiatan'] ?>]" class="form-control form-control-sm currency-input text-right" value="<?= number_format($val, 0, ',', '.') ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 bg-white" style="border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                <button type="submit" form="formConfigModal" class="btn btn-primary shadow-sm px-4 font-weight-bold">SIMPAN KONFIGURASI GLOBAL</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function formatCurrency(val) {
        val = val.replace(/[^0-9]/g, '');
        if(val === '') return '';
        return new Intl.NumberFormat('id-ID').format(val);
    }

    $(document).on('keyup', '.currency-input', function() {
        $(this).val(formatCurrency($(this).val()));
    });

    $(document).on('focus', '.currency-input', function() {
        $(this).select();
    });
});
</script>

<?php include '../app/views/partials/footer.php'; ?>
