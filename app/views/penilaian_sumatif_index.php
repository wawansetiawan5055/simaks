<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>

<style>
    .page-sumatif .lms-card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
        background: #ffffff;
        margin-bottom: 20px;
    }
    
    .page-sumatif .custom-filter-select {
        height: 42px !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        border: 1.5px solid #cbd5e1 !important;
        background-color: #f8fafc !important;
        padding: 6px 14px !important;
        box-shadow: none !important;
        border-radius: 10px !important;
        transition: all 0.2s ease;
    }
    .page-sumatif .custom-filter-select:focus {
        border-color: #4f46e5 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }

    .btn-create-agenda {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-radius: 50px !important;
        padding: 8px 18px !important;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25) !important;
        transition: all 0.2s ease !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-create-agenda:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35) !important;
        color: #ffffff !important;
    }

    /* Agenda Card for Mobile */
    .agenda-sumatif-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }
    .agenda-sumatif-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .badge-jenis-sumatif {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 50px;
    }

    @media (max-width: 768px) {
        .page-sumatif .content-header h1 {
            font-size: 1.15rem !important;
        }
        .page-sumatif .lms-card {
            border-radius: 12px !important;
            padding: 12px 14px !important;
        }
        .page-sumatif .custom-filter-select {
            height: 38px !important;
            font-size: 0.82rem !important;
            padding: 4px 10px !important;
        }
        .btn-create-agenda {
            width: 100% !important;
            justify-content: center;
            font-size: 0.85rem !important;
            margin-top: 8px;
        }
        .desktop-table-container {
            display: none !important;
        }
        .mobile-cards-container {
            display: block !important;
        }
    }

    @media (min-width: 769px) {
        .desktop-table-container {
            display: block !important;
        }
        .mobile-cards-container {
            display: none !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2 page-sumatif">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-poll"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Penilaian &amp; Input Nilai Sumatif
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>penilaian_sumatif/form_agenda<?= $id_kelas_filter ? '&id_kelas='.$id_kelas_filter : '' ?><?= $id_guru_mapel_filter ? '&id_guru_mapel='.$id_guru_mapel_filter : '' ?>"
                    class="btn btn-primary btn-sm rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Buat Agenda Baru
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content page-sumatif">
    <div class="container-fluid">

        <!-- FILTER CARD -->
        <div class="card lms-card p-3 mb-3 shadow-sm">
            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                <div class="bg-light p-2 rounded mr-2 text-primary font-weight-bold" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-filter"></i>
                </div>
                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.95rem;">Filter Data Agenda Penilaian</h6>
            </div>
            <form method="GET" id="filterFormIndex">
                <input type="hidden" name="mod" value="penilaian_sumatif">
                <div class="row" style="row-gap: 12px;">
                    <div class="form-group col-md-6 col-12 mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                            <i class="fas fa-chalkboard text-primary mr-1"></i> Kelas
                        </label>
                        <select name="id_kelas" class="form-control custom-filter-select" onchange="this.form.submit()">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelas_diajar as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6 col-12 mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                            <i class="fas fa-book text-primary mr-1"></i> Mata Pelajaran
                        </label>
                        <select name="id_guru_mapel" class="form-control custom-filter-select" onchange="this.form.submit()">
                            <option value="">-- Semua Mapel --</option>
                            <?php if ($id_kelas_filter && !empty($mapel_diajar)): ?>
                                <?php foreach ($mapel_diajar as $m): ?>
                                    <option value="<?= $m['id_guru_mapel'] ?>" <?= ($id_guru_mapel_filter == $m['id_guru_mapel']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['nama_mapel']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- DAFTAR AGENDA PENILAIAN SUMATIF -->
        <div class="card lms-card shadow-sm">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.05rem;">
                    <i class="fas fa-list-alt text-primary mr-1"></i> Daftar Agenda Penilaian Sumatif
                </h5>
                <span class="badge badge-light border text-muted px-3 py-1 font-weight-bold" style="border-radius: 50px;">
                    <?= count($agenda_list) ?> Agenda
                </span>
            </div>

            <div class="card-body p-3 p-md-4">
                <?php if (empty($agenda_list)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-poll fa-3x mb-3 d-block opacity-50"></i>
                        <h6 class="font-weight-bold text-dark">Belum Ada Agenda Penilaian Sumatif</h6>
                        <p class="small text-muted mb-3">Buat agenda penilaian sumatif baru untuk mulai menginput skor dan capaian siswa.</p>
                        <a href="<?= BASE_URL ?>penilaian_sumatif/form_agenda<?= $id_kelas_filter ? '&id_kelas='.$id_kelas_filter : '' ?><?= $id_guru_mapel_filter ? '&id_guru_mapel='.$id_guru_mapel_filter : '' ?>"
                            class="btn btn-sm btn-primary rounded-pill px-4 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                            <i class="fas fa-plus mr-1"></i> Buat Agenda Pertama
                        </a>
                    </div>
                <?php else: ?>

                    <!-- 1. DESKTOP VIEW (TABLE) -->
                    <div class="table-responsive desktop-table-container">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="thead-light" style="font-size: 0.85rem;">
                                <tr>
                                    <th width="12%">Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Nama Penilaian</th>
                                    <th width="20%">Jenis Penilaian</th>
                                    <th width="12%">Tanggal</th>
                                    <th width="16%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agenda_list as $agenda): ?>
                                    <?php
                                    $jenis = $agenda['jenis_sumatif'];
                                    $badge_class = 'badge-primary';
                                    if (strpos($jenis, 'Lingkup Materi') !== false) $badge_class = 'badge-info';
                                    elseif (strpos($jenis, 'Tengah Semester') !== false) $badge_class = 'badge-warning text-dark';
                                    elseif (strpos($jenis, 'Akhir Semester') !== false) $badge_class = 'badge-success';
                                    elseif (strpos($jenis, 'Akhir Tahun') !== false) $badge_class = 'badge-danger';
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="font-size: 0.8rem;">
                                                <?= htmlspecialchars($agenda['nama_kelas']) ?>
                                            </span>
                                        </td>
                                        <td><strong class="text-dark"><?= htmlspecialchars($agenda['nama_mapel']) ?></strong></td>
                                        <td>
                                            <span class="font-weight-bold text-primary"><?= htmlspecialchars($agenda['nama_penilaian']) ?></span>
                                            <?php if (!empty($agenda['keterangan'])): ?>
                                                <small class="text-muted d-block font-italic"><?= htmlspecialchars($agenda['keterangan']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $badge_class ?> badge-jenis-sumatif">
                                                <?= htmlspecialchars($agenda['jenis_sumatif']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                <?= $agenda['tanggal_penilaian'] ? DateHelper::formatTanggal($agenda['tanggal_penilaian'], 'short') : '-' ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                                <a href="<?= BASE_URL ?>penilaian_sumatif/form_nilai?id_sumatif=<?= $agenda['id_sumatif'] ?>"
                                                    class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;" title="Input Nilai">
                                                    <i class="fa fa-edit mr-1"></i> Nilai
                                                </a>
                                                <a href="<?= BASE_URL ?>penilaian_sumatif/form_agenda?id=<?= $agenda['id_sumatif'] ?>"
                                                    class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Edit Agenda">
                                                    <i class="fa fa-cog"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>penilaian_sumatif/delete_agenda?id=<?= $agenda['id_sumatif'] ?>"
                                                    class="btn btn-sm btn-outline-danger rounded-circle btn-delete-confirm" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Hapus Agenda">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. MOBILE VIEW (AGENDA CARDS) -->
                    <div class="mobile-cards-container">
                        <?php foreach ($agenda_list as $agenda): ?>
                            <?php
                            $jenis = $agenda['jenis_sumatif'];
                            $badge_class = 'badge-primary';
                            if (strpos($jenis, 'Lingkup Materi') !== false) $badge_class = 'badge-info';
                            elseif (strpos($jenis, 'Tengah Semester') !== false) $badge_class = 'badge-warning text-dark';
                            elseif (strpos($jenis, 'Akhir Semester') !== false) $badge_class = 'badge-success';
                            elseif (strpos($jenis, 'Akhir Tahun') !== false) $badge_class = 'badge-danger';
                            ?>
                            <div class="agenda-sumatif-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge <?= $badge_class ?> badge-jenis-sumatif">
                                        <?= htmlspecialchars($agenda['jenis_sumatif']) ?>
                                    </span>
                                    <span class="badge badge-light border text-muted" style="font-size: 0.72rem;">
                                        <?= htmlspecialchars($agenda['nama_kelas']) ?>
                                    </span>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.95rem;">
                                    <?= htmlspecialchars($agenda['nama_penilaian']) ?>
                                </h6>
                                <div class="small text-muted mb-2">
                                    <i class="fas fa-book mr-1 text-primary"></i> <?= htmlspecialchars($agenda['nama_mapel']) ?>
                                    <span class="mx-1">•</span>
                                    <i class="far fa-calendar-alt mr-1"></i> <?= $agenda['tanggal_penilaian'] ? DateHelper::formatTanggal($agenda['tanggal_penilaian'], 'short') : '-' ?>
                                </div>
                                <?php if (!empty($agenda['keterangan'])): ?>
                                    <p class="small text-muted font-italic mb-2" style="font-size: 0.76rem;"><?= htmlspecialchars($agenda['keterangan']) ?></p>
                                <?php endif; ?>

                                <div class="d-flex align-items-center mt-2 pt-2 border-top" style="gap: 8px;">
                                    <a href="<?= BASE_URL ?>penilaian_sumatif/form_nilai?id_sumatif=<?= $agenda['id_sumatif'] ?>"
                                        class="btn btn-sm btn-primary rounded-pill font-weight-bold flex-grow-1 text-center shadow-sm" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none; padding: 7px 12px; font-size: 0.82rem;">
                                        <i class="fa fa-edit mr-1"></i> Input Nilai
                                    </a>
                                    <a href="<?= BASE_URL ?>penilaian_sumatif/form_agenda?id=<?= $agenda['id_sumatif'] ?>"
                                        class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.8rem;" title="Edit Agenda">
                                        <i class="fa fa-cog"></i> Edit
                                    </a>
                                    <a href="<?= BASE_URL ?>penilaian_sumatif/delete_agenda?id=<?= $agenda['id_sumatif'] ?>"
                                        class="btn btn-sm btn-outline-danger rounded-circle btn-delete-confirm" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;" title="Hapus Agenda">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>