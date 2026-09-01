<?php
// Dashboard View
$page_title = 'Dashboard CBT';
require_once CBT_ROOT . '/app/views/partials/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-tachometer-alt text-danger mr-2"></i>Dashboard CBT</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Stat Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box"
                    style="background:linear-gradient(135deg,#e94560,#c0392b); color:#fff; border-radius:12px;">
                    <div class="inner">
                        <h3>
                            <?= $stats['total_soal'] ?>
                        </h3>
                        <p>Total Soal</p>
                    </div>
                    <div class="icon"><i class="fas fa-question-circle"></i></div>
                    <a href="<?= CBT_BASE_URL ?>?mod=bank_soal"
                        class="small-box-footer text-white-50">Kelola <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box"
                    style="background:linear-gradient(135deg,#4a90e2,#2471a3); color:#fff; border-radius:12px;">
                    <div class="inner">
                        <h3>
                            <?= $stats['total_ujian'] ?>
                        </h3>
                        <p>Total Ujian</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                    <a href="<?= CBT_BASE_URL ?>?mod=kelola_ujian"
                        class="small-box-footer text-white-50">Kelola <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box"
                    style="background:linear-gradient(135deg,#27ae60,#1e8449); color:#fff; border-radius:12px;">
                    <div class="inner">
                        <h3>
                            <?= $stats['ujian_aktif'] ?>
                        </h3>
                        <p>Ujian Sedang Berjalan</p>
                    </div>
                    <div class="icon"><i class="fas fa-play-circle"></i></div>
                    <a href="<?= CBT_BASE_URL ?>?mod=peserta"
                        class="small-box-footer text-white-50">Monitor <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box"
                    style="background:linear-gradient(135deg,#f39c12,#d68910); color:#fff; border-radius:12px;">
                    <div class="inner">
                        <h3>
                            <?= $stats['total_siswa'] ?>
                        </h3>
                        <p>Total Siswa Terdaftar</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <a href="<?= CBT_BASE_URL ?>?mod=kelola_siswa"
                        class="small-box-footer text-white-50">Kelola <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Quick Access & Jadwal Terbaru -->
        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header" style="background:linear-gradient(135deg,#1a1a2e,#16213e); color:#fff;">
                        <h3 class="card-title"><i class="fas fa-bolt mr-2 text-warning"></i>Akses Cepat</h3>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <?php
                            $shortcuts = [
                                ['icon' => 'fas fa-plus', 'label' => 'Tambah Soal Baru', 'url' => 'bank_soal&act=input', 'color' => 'danger'],
                                ['icon' => 'fas fa-calendar-plus', 'label' => 'Buat Jadwal Ujian', 'url' => 'kelola_ujian&act=create', 'color' => 'primary'],
                                ['icon' => 'fas fa-user-plus', 'label' => 'Import Siswa', 'url' => 'kelola_siswa', 'color' => 'success'],
                                ['icon' => 'fas fa-chart-line', 'label' => 'Lihat Hasil Ujian', 'url' => 'hasil_ujian', 'color' => 'warning'],
                            ];
                            foreach ($shortcuts as $s): ?>
                                <div class="col-6 mb-2">
                                    <a href="<?= CBT_BASE_URL ?>?mod=<?= $s['url'] ?>"
                                        class="btn btn-<?= $s['color'] ?> btn-block text-left py-3">
                                        <i class="<?= $s['icon'] ?> mr-2"></i>
                                        <?= $s['label'] ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-alt mr-2 text-primary"></i>Jadwal Ujian Terbaru
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Ujian</th>
                                    <th>Tanggal</th>
                                    <th>Peserta</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($jadwal_terbaru)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted"><i
                                                class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada jadwal ujian.</td>
                                    </tr>
                                <?php else:
                                    foreach ($jadwal_terbaru as $j):
                                        $badge = ['draft' => 'secondary', 'aktif' => 'success', 'selesai' => 'dark'][$j['status']] ?? 'secondary';
                                        ?>
                                        <tr>
                                            <td class="font-weight-bold">
                                                <?= htmlspecialchars($j['nama_ujian']) ?>
                                            </td>
                                            <td>
                                                <?= date('d M Y', strtotime($j['tanggal_mulai'])) ?>
                                            </td>
                                            <td><span class="badge badge-primary">
                                                    <?= $j['jml_peserta'] ?> siswa
                                                </span></td>
                                            <td><span class="badge badge-<?= $badge ?>">
                                                    <?= strtoupper($j['status']) ?>
                                                </span></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once CBT_ROOT . '/app/views/partials/footer.php'; ?>