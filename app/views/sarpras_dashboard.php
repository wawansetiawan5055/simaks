<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        <?= htmlspecialchars($title ?? 'Dashboard Sarana & Prasarana') ?>
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Sarpras</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $total_gedung ?></h3>
                        <p>Total Gedung & Prasarana</p>
                    </div>
                    <div class="icon">
                        <i class="far fa-building"></i>
                    </div>
                    <a href="<?= BASE_URL ?>sarpras_gedung" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $total_ruang ?></h3>
                        <p>Total Ruangan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <a href="<?= BASE_URL ?>sarpras_ruang" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $total_barang ?></h3>
                        <p>Total Jenis Barang</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <a href="<?= BASE_URL ?>sarpras_barang" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $sum_barang ?></h3>
                        <p>Total Item Inventaris</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chair"></i>
                    </div>
                    <a href="<?= BASE_URL ?>sarpras_barang" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Sistem Sarana dan Prasarana</h3>
                    </div>
                    <div class="card-body">
                        <p>Selamat datang di modul Manajemen Sarana dan Prasarana. Gunakan menu di samping untuk mengelola data fisik sekolah secara hierarkis:</p>
                        <ul>
                            <li><strong>Data Gedung & Prasarana:</strong> Manajemen gedung utama, lapangan olahraga, area parkir, dll.</li>
                            <li><strong>Data Ruangan:</strong> Pemetaan ruang kelas, ruang guru, lab, dll yang berada di dalam sebuah gedung.</li>
                            <li><strong>Inventaris Barang:</strong> Pendataan aset fisik sekolah (meja, kursi, lemari, dll) beserta kondisinya di masing-masing ruangan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
