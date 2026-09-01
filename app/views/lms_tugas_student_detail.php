<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold"><i class="fas fa-user-graduate mr-2 text-primary"></i> Detail Progres Siswa</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= BASE_URL ?>lms/tugas_detail?id=<?= $detail['id_tugas'] ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Rekap Progres
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="font-weight-bold mb-2"><?= htmlspecialchars($detail['nama']) ?></h4>
                                <p class="text-muted mb-1"><strong>NIS:</strong> <?= htmlspecialchars($detail['nis'] ?? '-') ?></p>
                                <p class="text-muted mb-0"><strong>Kelas:</strong> <?= htmlspecialchars($detail['nama_kelas']) ?> — <strong>Mapel:</strong> <?= htmlspecialchars($detail['nama_mapel']) ?></p>
                            </div>
                            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                <span class="badge badge-info px-3 py-2" style="font-size: 0.95rem;">Tugas: <?= htmlspecialchars($detail['judul_tugas']) ?></span>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="small text-muted">Deadline</div>
                                <div class="font-weight-bold"><?= date('d/m/Y H:i', strtotime($detail['deadline'])) ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="small text-muted">Bobot Nilai</div>
                                <div class="font-weight-bold"><?= htmlspecialchars($detail['bobot_nilai']) ?>%</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5 class="font-weight-bold">Ringkasan Progres</h5>
                            <?php
                            $stages = [
                                'instruksi' => 'Instruksi',
                                'diagnostik' => 'Diagnostik',
                                'materi' => 'Materi',
                                'essay' => 'Essay',
                                'formatif' => 'Formatif',
                                'refleksi' => 'Refleksi',
                            ];

                            $progress_items = ['instruksi' => 'Instruksi'];
                            if (!empty($detail['tes_diagnostik_config'])) {
                                $progress_items['diagnostik'] = 'Diagnostik';
                            }
                            $progress_items['materi'] = 'Materi';
                            if (!empty($detail['essay_config'])) {
                                $progress_items['essay'] = 'Essay';
                            }
                            if (!empty($detail['materi_questions']) && $detail['materi_questions'] !== '[]') {
                                $progress_items['formatif'] = 'Formatif';
                            }
                            if (!empty($detail['refleksi_config'])) {
                                $progress_items['refleksi'] = 'Refleksi';
                            }

                            $done_count = 0;
                            foreach ($progress_items as $key => $label) {
                                if (!empty($detail['stage_'.$key])) {
                                    $done_count++;
                                }
                            }
                            $total_stages = count($progress_items) > 0 ? count($progress_items) : 1;
                            $percent = round(($done_count / $total_stages) * 100);
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="font-weight-bold text-secondary">Tahapan diselesaikan</div>
                                    <div class="font-weight-bold"><?= $percent ?>%</div>
                                </div>
                                <div class="progress" style="height: 12px; border-radius: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%;"></div>
                                </div>
                            </div>

                            <div class="row">
                                <?php foreach ($progress_items as $key => $label):
                                    $done = !empty($detail['stage_'.$key]);
                                ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="font-weight-bold"><?= $label ?></div>
                                                <span class="badge <?= $done ? 'badge-success' : 'badge-secondary' ?> px-2 py-1"><?= $done ? 'Selesai' : 'Belum' ?></span>
                                            </div>
                                            <div class="text-muted small">
                                                <?= $done ? 'Telah diselesaikan oleh siswa.' : 'Belum diselesaikan.' ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Detail Pengumpulan dan Penilaian</h5>
                        <?php if ($detail['id_kumpul']): ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="small text-muted">Tanggal Upload</div>
                                    <div class="font-weight-bold"><?= date('d/m/Y H:i', strtotime($detail['tgl_upload'])) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Status Penilaian</div>
                                    <div class="font-weight-bold"><?= $detail['nilai'] !== null ? 'Dinilai' : 'Menunggu Penilaian' ?></div>
                                </div>
                            </div>

                            <?php if ($detail['file_siswa']): ?>
                                <div class="mb-3">
                                    <a href="<?= BASE_URL . $detail['file_siswa'] ?>" target="_blank" class="btn btn-info btn-sm rounded-pill px-4 py-2 shadow-sm">
                                        <i class="fas fa-file-download mr-2"></i> Unduh File Siswa
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="small text-muted">Nilai</div>
                                    <div class="font-weight-bold"><?= $detail['nilai'] !== null ? htmlspecialchars($detail['nilai']) : '-' ?></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="small text-muted">Nilai Diberikan</div>
                                    <div class="font-weight-bold"><?= $detail['tgl_nilai'] ? date('d/m/Y H:i', strtotime($detail['tgl_nilai'])) : '-' ?></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="small text-muted">Terakhir Aktif</div>
                                    <div class="font-weight-bold"><?= $detail['last_active'] ? date('d/m/Y H:i', strtotime($detail['last_active'])) : '-' ?></div>
                                </div>
                            </div>

                            <?php if (!empty($detail['catatan_guru'])): ?>
                                <div class="mb-3">
                                    <div class="small text-muted mb-1">Komentar Guru</div>
                                    <div class="border rounded p-3 bg-light" style="line-height: 1.6;">
                                        <?= nl2br(htmlspecialchars($detail['catatan_guru'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <a href="<?= BASE_URL ?>lms/koreksi_detail?id=<?= $detail['id_kumpul'] ?>" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                    <i class="fas fa-edit mr-2"></i> Periksa / Ubah Penilaian
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0" role="alert">
                                <strong>Belum ada pengumpulan tugas.</strong> Siswa bisa mengumpulkan tugas setelah menyelesaikan tahap yang diperlukan.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Informasi Tugas</h5>
                        <div class="small text-muted">Judul Tugas</div>
                        <div class="font-weight-bold mb-3"><?= htmlspecialchars($detail['judul_tugas']) ?></div>
                        <div class="small text-muted">Instruksi</div>
                        <div class="bg-light border rounded p-3 mb-3" style="line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($detail['instruksi'])) ?>
                        </div>
                        <div class="small text-muted">Total Tahap</div>
                        <div class="font-weight-bold mb-3"><?= $total_stages ?></div>
                        <div class="small text-muted">Progres Siswa</div>
                        <div class="font-weight-bold"><?= $done_count ?> dari <?= $total_stages ?> Tahap</div>
                        <div class="small text-muted">Persentase</div>
                        <div class="font-weight-bold"><?= $percent ?>%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
