<?php
// tahfidz_jurnal_form.php
include __DIR__ . '/partials/header.php';
include __DIR__ . '/partials/sidebar.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-quran mr-2"></i> Jurnal & Absensi Tahfidz</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>tahfidz?id=<?= $id_tah ?>">Detail</a></li>
                    <li class="breadcrumb-item active">Jurnal</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="<?= BASE_URL ?>tahfidz/jurnal_save" method="post">
            <input type="hidden" name="id_tahfidz" value="<?= $id_tah ?>">
            <?php if ($jurnal): ?>
                <input type="hidden" name="id_jurnal" value="<?= $jurnal['id_jurnal'] ?>">
            <?php endif; ?>

            <div class="row">
                <!-- FORM JURNAL (LEFT COL) -->
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Data Pertemuan</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info py-2">
                                <small><strong>Kelompok:</strong> <?= htmlspecialchars($tahfidz['nama_kelompok']) ?></small>
                            </div>
                            
                            <div class="form-group">
                                <label>Tanggal</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="tanggal" class="form-control" required value="<?= $jurnal['tanggal'] ?? date('Y-m-d') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Materi Global / Kegiatan</label>
                                <textarea name="materi" class="form-control" rows="4" required placeholder="Misal: Murajaah Surat An-Naba bersama..."><?= htmlspecialchars($jurnal['materi'] ?? '') ?></textarea>
                                <small class="text-muted">Untuk setoran individu, gunakan tab <strong>Setoran Hafalan</strong>.</small>
                            </div>

                            <div class="form-group">
                                <label>Keterangan / Catatan</label>
                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional..."><?= htmlspecialchars($jurnal['keterangan'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORM ABSENSI (RIGHT COL) -->
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Absensi Anggota</h3>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center" width="30%">Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($anggota)): ?>
                                        <tr><td colspan="3" class="text-center p-3">Belum ada anggota terdaftar di kelompok ini.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($anggota as $i => $s): 
                                            $status = $presensi[$s['id_siswa']] ?? 'H';
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= $i + 1 ?></td>
                                            <td class="align-middle">
                                                <strong><?= htmlspecialchars($s['nama_siswa']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($s['nama_kelas']) ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-outline-success btn-sm <?= $status=='H'?'active':'' ?>">
                                                        <input type="radio" name="presensi[<?= $s['id_siswa'] ?>]" value="H" autocomplete="off" <?= $status=='H'?'checked':'' ?>> H
                                                    </label>
                                                    <label class="btn btn-outline-warning btn-sm <?= $status=='S'?'active':'' ?>">
                                                        <input type="radio" name="presensi[<?= $s['id_siswa'] ?>]" value="S" autocomplete="off" <?= $status=='S'?'checked':'' ?>> S
                                                    </label>
                                                    <label class="btn btn-outline-info btn-sm <?= $status=='I'?'active':'' ?>">
                                                        <input type="radio" name="presensi[<?= $s['id_siswa'] ?>]" value="I" autocomplete="off" <?= $status=='I'?'checked':'' ?>> I
                                                    </label>
                                                    <label class="btn btn-outline-danger btn-sm <?= $status=='A'?'active':'' ?>">
                                                        <input type="radio" name="presensi[<?= $s['id_siswa'] ?>]" value="A" autocomplete="off" <?= $status=='A'?'checked':'' ?>> A
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-right">
                             <a href="<?= BASE_URL ?>tahfidz/index?id=<?= $id_tah ?>&tab=jurnal" class="btn btn-secondary">Batal</a>
                             <button type="submit" class="btn btn-primary">Simpan Jurnal & Absensi</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
