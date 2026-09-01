<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    .progress-compact {
        height: 10px;
        border-radius: 5px;
        background-color: #f1f5f9;
        margin-bottom: 0;
    }
    .stage-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #e2e8f0;
    }
    .stage-done { background-color: #10b981; box-shadow: 0 0 0 1px #10b981; }
    .stage-todo { background-color: #e2e8f0; }
    
    .student-card {
        transition: all 0.2s;
        border-radius: 12px;
    }
    .student-card:hover {
        background-color: #f8fafc;
    }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold"><i class="fas fa-chart-line mr-2 text-primary"></i> Detail Progres Tugas</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= BASE_URL ?>lms/tugas_list" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Info Tugas -->
        <div class="card shadow-sm mb-4" style="border-radius: 15px; border-top: 5px solid #6366f1 !important;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="font-weight-bold mb-1"><?= htmlspecialchars($tugas['judul_tugas']) ?></h4>
                        <p class="text-muted mb-0">
                            <i class="fas fa-bookmark mr-1"></i> <?= htmlspecialchars($tugas['nama_mapel']) ?> 
                            <span class="mx-2">•</span> 
                            <i class="fas fa-users mr-1"></i> Kelas <?= htmlspecialchars($tugas['nama_kelas'] ?? '-') ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <span class="badge badge-light border p-2 px-3" style="font-size: 0.9rem;">
                            <i class="far fa-clock mr-1 text-danger"></i> Deadline: <?= date('d M Y H:i', strtotime($tugas['deadline'])) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Siswa -->
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 font-weight-bold text-muted small"><i class="fas fa-users mr-2"></i> MONITORING PROGRES SISWA</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small">
                                <th class="px-4 border-0">NAMA SISWA</th>
                                <th class="border-0">TAHAPAN PROGRES</th>
                                <th class="border-0 text-center">STATUS AKHIR</th>
                                <th class="border-0 text-center">NILAI</th>
                                <th class="border-0 px-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Tentukan stage mana saja yang aktif untuk tugas ini
                            $active_stages = ['instruksi', 'materi']; // Selalu ada
                            if (!empty($tugas['tes_diagnostik_config'])) $active_stages[] = 'diagnostik';
                            if (!empty($tugas['essay_config'])) $active_stages[] = 'essay';
                            if (!empty($tugas['materi_questions']) && $tugas['materi_questions'] !== '[]') $active_stages[] = 'formatif';
                            if (!empty($tugas['refleksi_config'])) $active_stages[] = 'refleksi';
                            
                            $total_active = count($active_stages);

                            if(!empty($submissions)): ?>
                                <?php foreach($submissions as $s): 
                                    // Hitung progres hanya berdasarkan stage yang aktif
                                    $done_count = 0;
                                    foreach($active_stages as $st) {
                                        if (!empty($s['stage_'.$st])) $done_count++;
                                    }
                                    
                                    $percent = $total_active > 0 ? round(($done_count / $total_active) * 100) : 0;
                                    $status_class = $s['id_kumpul'] ? 'badge-success' : ($percent > 0 ? 'badge-warning' : 'badge-secondary');
                                    $status_text = $s['id_kumpul'] ? 'Selesai' : ($percent > 0 ? 'Sedang Mengerjakan' : 'Belum Mulai');
                                ?>
                                <tr class="student-card">
                                    <td class="px-4 py-3 align-middle">
                                        <span class="font-weight-bold d-block"><?= htmlspecialchars($s['nama']) ?></span>
                                        <small class="text-muted">Terakhir Aktif: <?= $s['last_active'] ? date('d/m H:i', strtotime($s['last_active'])) : '-' ?></small>
                                    </td>
                                    <td class="align-middle" style="min-width: 200px;">
                                        <div class="d-flex align-items-center mb-1">
                                            <!-- Selalu ada -->
                                            <div class="stage-dot <?= ($s['stage_instruksi'] ? 'stage-done' : 'stage-todo') ?>" title="Instruksi"></div>
                                            
                                            <!-- Dinamis -->
                                            <?php if(in_array('diagnostik', $active_stages)): ?>
                                                <div class="stage-dot <?= ($s['stage_diagnostik'] ? 'stage-done' : 'stage-todo') ?>" title="Diagnostik"></div>
                                            <?php endif; ?>
                                            
                                            <div class="stage-dot <?= ($s['stage_materi'] ? 'stage-done' : 'stage-todo') ?>" title="Materi"></div>
                                            
                                            <?php if(in_array('essay', $active_stages)): ?>
                                                <div class="stage-dot <?= ($s['stage_essay'] ? 'stage-done' : 'stage-todo') ?>" title="Essay"></div>
                                            <?php endif; ?>
                                            
                                            <?php if(in_array('formatif', $active_stages)): ?>
                                                <div class="stage-dot <?= ($s['stage_formatif'] ? 'stage-done' : 'stage-todo') ?>" title="Formatif"></div>
                                            <?php endif; ?>
                                            
                                            <?php if(in_array('refleksi', $active_stages)): ?>
                                                <div class="stage-dot <?= ($s['stage_refleksi'] ? 'stage-done' : 'stage-todo') ?>" title="Refleksi"></div>
                                            <?php endif; ?>

                                            <small class="ml-2 text-muted font-weight-bold"><?= $percent ?>%</small>
                                        </div>
                                        <div class="progress progress-compact">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percent ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge <?= $status_class ?> px-2 py-1"><?= $status_text ?></span>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold text-primary">
                                        <?= $s['nilai'] !== null ? $s['nilai'] : '-' ?>
                                    </td>
                                    <td class="px-4 text-center align-middle">
                                        <a href="<?= BASE_URL ?>lms/tugas_student_detail?id=<?= $tugas['id_tugas'] ?>&id_siswa=<?= $s['id_siswa'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted italic">Tidak ada siswa yang terdaftar di kelas ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
