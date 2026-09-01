<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">LMS System</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card lms-card shadow-lg">
            <div class="lms-card-header d-flex justify-content-between align-items-center">
                <h3 class="lms-card-title">
                    <i class="fas fa-edit text-primary mr-2"></i> Koreksi Tugas Siswa
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= BASE_URL ?>lms/koreksi_list" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if ($pengumpulan): ?>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
                                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                                    <h5 class="font-weight-bold"><i class="fas fa-info-circle text-info mr-2"></i>Detail Pengumpulan</h5>
                                </div>
                                <div class="card-body px-4 pb-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="small text-muted mb-1">Siswa</div>
                                            <div class="font-weight-bold"><?php echo htmlspecialchars($pengumpulan['nama_siswa']); ?></div>
                                            <div class="small text-muted">NIS: <?php echo htmlspecialchars($pengumpulan['nis']); ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="small text-muted mb-1">Mata Pelajaran</div>
                                            <div class="font-weight-bold text-primary"><?php echo htmlspecialchars($pengumpulan['nama_mapel']); ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="small text-muted mb-1">Judul Tugas</div>
                                            <div class="font-weight-bold"><?php echo htmlspecialchars($pengumpulan['judul_tugas']); ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="small text-muted mb-1">Tanggal Upload</div>
                                            <div class="font-weight-bold"><?php echo date('d/m/Y H:i', strtotime($pengumpulan['tgl_upload'])); ?></div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <h6 class="font-weight-bold small text-uppercase text-muted">Instruksi Tugas:</h6>
                                        <div class="bg-white p-3 rounded border" style="font-size: 0.9rem; line-height: 1.6;">
                                            <?php echo nl2br(htmlspecialchars($pengumpulan['instruksi'])); ?>
                                        </div>
                                    </div>

                                    <?php if ($pengumpulan['file_siswa'] && $pengumpulan['file_siswa'] !== 'Learning Path Completed'): ?>
                                        <div class="mt-4">
                                            <h6 class="font-weight-bold small text-uppercase text-muted">File Siswa:</h6>
                                            <a href="<?php echo BASE_URL . $pengumpulan['file_siswa']; ?>" target="_blank" class="btn btn-info btn-sm px-4 py-2 rounded-pill shadow-sm">
                                                <i class="fas fa-file-download mr-2"></i> Download File Siswa
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <!-- DETAIL PEKERJAAN SISWA (LEARNING PATH) -->
                                    <div class="mt-5">
                                        <h5 class="font-weight-bold mb-4"><i class="fas fa-tasks text-success mr-2"></i>Hasil Pengerjaan Tahapan</h5>
                                        
                                        <div class="accordion" id="workDetailAccordion">
                                            
                                            <!-- DIAGNOSTIK -->
                                            <?php if (!empty($student_work['diagnostik'])): ?>
                                            <div class="card mb-2 border">
                                                <div class="card-header bg-white py-2" id="headingDiag">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block text-left font-weight-bold text-dark" type="button" data-toggle="collapse" data-target="#collapseDiag">
                                                            <i class="fas fa-stethoscope mr-2 text-info"></i> Tes Diagnostik
                                                        </button>
                                                    </h2>
                                                </div>
                                                <div id="collapseDiag" class="collapse show" data-parent="#workDetailAccordion">
                                                    <div class="card-body bg-light">
                                                        <?php foreach ($student_work['diagnostik'] as $diag): ?>
                                                            <div class="mb-2 p-2 bg-white rounded border-left border-info">
                                                                <div class="small text-muted">Jawaban:</div>
                                                                <div><?= nl2br(htmlspecialchars($diag['jawaban'])) ?></div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- ESSAY -->
                                            <?php if (!empty($student_work['essay'])): ?>
                                            <div class="card mb-2 border">
                                                <div class="card-header bg-white py-2" id="headingEssay">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block text-left font-weight-bold text-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseEssay">
                                                            <i class="fas fa-pen-nib mr-2 text-warning"></i> Tugas Essay / Terbuka
                                                        </button>
                                                    </h2>
                                                </div>
                                                <div id="collapseEssay" class="collapse" data-parent="#workDetailAccordion">
                                                    <div class="card-body bg-light">
                                                        <?php foreach ($student_work['essay'] as $es): ?>
                                                            <div class="mb-3 p-3 bg-white rounded border-left border-warning">
                                                                <div class="font-weight-bold mb-1"><?= htmlspecialchars($es['pertanyaan']) ?></div>
                                                                <div class="text-primary italic">"<?= nl2br(htmlspecialchars($es['jawaban'])) ?>"</div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- FORMATIF -->
                                            <?php if (!empty($student_work['formatif'])): ?>
                                            <div class="card mb-2 border">
                                                <div class="card-header bg-white py-2" id="headingFormatif">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block text-left font-weight-bold text-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseFormatif">
                                                            <i class="fas fa-check-double mr-2 text-success"></i> Tes Formatif (Pilihan Ganda)
                                                        </button>
                                                    </h2>
                                                </div>
                                                <div id="collapseFormatif" class="collapse" data-parent="#workDetailAccordion">
                                                    <div class="card-body bg-light">
                                                        <?php 
                                                        $correct_count = 0;
                                                        $total_pg = 0;
                                                        foreach ($student_work['formatif'] as $f): 
                                                            if ($f['tipe'] == 'PG') {
                                                                $total_pg++;
                                                                if ($f['is_correct']) $correct_count++;
                                                            }
                                                        ?>
                                                            <div class="mb-2 p-2 bg-white rounded d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="small"><?= htmlspecialchars($f['pertanyaan']) ?></div>
                                                                    <div class="font-weight-bold">Jawaban: <?= htmlspecialchars($f['jawaban']) ?></div>
                                                                </div>
                                                                <div>
                                                                    <?php if ($f['tipe'] == 'PG'): ?>
                                                                        <?php if ($f['is_correct']): ?>
                                                                            <span class="badge badge-success"><i class="fas fa-check"></i> Benar</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-danger"><i class="fas fa-times"></i> Salah (Kunci: <?= $f['kunci_jawaban'] ?>)</span>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        
                                                        <?php if ($total_pg > 0): 
                                                            $suggested_score = round(($correct_count / $total_pg) * 100);
                                                        ?>
                                                            <div class="alert alert-info mt-3 mb-0">
                                                                <i class="fas fa-lightbulb mr-2"></i> <strong>Saran Nilai Kuis: <?= $suggested_score ?></strong> (<?= $correct_count ?> dari <?= $total_pg ?> soal benar)
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- REFLEKSI -->
                                            <?php if (!empty($student_work['refleksi'])): ?>
                                            <div class="card mb-2 border">
                                                <div class="card-header bg-white py-2" id="headingRef">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block text-left font-weight-bold text-dark collapsed d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseRef">
                                                            <span><i class="fas fa-comment-dots mr-2 text-primary"></i> Refleksi &amp; Presensi Belajar Siswa</span>
                                                            <?php 
                                                            $fotoPr = '';
                                                            foreach ($student_work['refleksi'] as $rf) {
                                                                if (!empty($rf['foto_presensi'])) { $fotoPr = $rf['foto_presensi']; break; }
                                                            }
                                                            if (!$fotoPr && !empty($detail['foto_presensi'])) {
                                                                $fotoPr = $detail['foto_presensi'];
                                                            }
                                                            ?>
                                                            <?php if ($fotoPr): ?>
                                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-camera mr-1"></i> Foto Terverifikasi</span>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                </div>
                                                <div id="collapseRef" class="collapse" data-parent="#workDetailAccordion">
                                                    <div class="card-body bg-light">
                                                        <?php if ($fotoPr): ?>
                                                            <div class="mb-3 p-3 bg-white rounded border shadow-sm text-center">
                                                                <div class="small font-weight-bold text-muted mb-2 text-left">
                                                                    <i class="fas fa-camera text-primary mr-1"></i> Bukti Foto Presensi / Kehadiran Belajar Siswa:
                                                                </div>
                                                                <div class="d-inline-block position-relative border rounded p-1 bg-light shadow-sm" style="max-width: 280px;">
                                                                    <a href="<?= BASE_URL ?>uploads/presensi_materi/<?= htmlspecialchars($fotoPr) ?>" target="_blank" title="Klik untuk memperbesar foto">
                                                                        <img src="<?= BASE_URL ?>uploads/presensi_materi/<?= htmlspecialchars($fotoPr) ?>" alt="Foto Presensi" class="img-fluid rounded" style="max-height: 220px; object-fit: cover;">
                                                                    </a>
                                                                    <div class="small text-muted mt-1 font-weight-bold"><i class="fas fa-search-plus mr-1"></i> Klik untuk memperbesar</div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php foreach ($student_work['refleksi'] as $ref): ?>
                                                            <div class="mb-3 p-3 bg-white rounded border-left border-primary shadow-sm">
                                                                <div class="font-weight-bold mb-1 text-dark"><?= htmlspecialchars($ref['pertanyaan']) ?></div>
                                                                <div class="italic text-secondary">"<?= nl2br(htmlspecialchars($ref['jawaban'])) ?>"</div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #fff;">
                                <div class="card-header bg-gradient-primary text-white border-0 pt-4 px-4" style="border-radius: 12px 12px 0 0;">
                                    <h5 class="font-weight-bold mb-0">Penilaian Tahapan</h5>
                                </div>
                                <div class="card-body p-4">
                                    <form method="POST" action="" id="gradingForm">
                                        <input type="hidden" name="detailed_grading" value="1">
                                        
                                        <!-- Score Diagnostik -->
                                        <div class="form-group mb-3">
                                            <label class="small font-weight-bold">1. Tes Diagnostik (Max: 10)</label>
                                            <input type="number" name="score_diagnostik" class="form-control score-input" value="<?= $detail['score_diagnostik'] ?? 0 ?>" min="0" max="10" oninput="calculateFinal()">
                                        </div>

                                        <!-- Score Menyimak -->
                                        <div class="form-group mb-3">
                                            <label class="small font-weight-bold">2. Menyimak Materi (Max: 20)</label>
                                            <input type="number" name="score_materi" class="form-control score-input" value="<?= $detail['score_materi'] ?? 0 ?>" min="0" max="20" oninput="calculateFinal()">
                                        </div>

                                        <!-- Score Merangkum -->
                                        <div class="form-group mb-3">
                                            <label class="small font-weight-bold">3. Merangkum/Tugas (Max: 60)</label>
                                            <?php if($detail['file_materi_siswa']): ?>
                                                <div class="mb-2">
                                                    <a href="<?= BASE_URL . $detail['file_materi_siswa'] ?>" target="_blank" class="btn btn-xs btn-outline-info"><i class="fas fa-image mr-1"></i> Lihat Foto Buku</a>
                                                </div>
                                            <?php endif; ?>
                                            <input type="number" name="score_tugas_materi" class="form-control score-input" value="<?= $detail['score_tugas_materi'] ?? 0 ?>" min="0" max="60" oninput="calculateFinal()">
                                        </div>

                                        <!-- Score Formatif (Input Manual atau dari Kuis) -->
                                        <?php 
                                            // Hitung saran nilai kuis PG jika ada
                                            $pg_score = 0;
                                            if (!empty($student_work['formatif'])) {
                                                $correct = 0; $total = 0;
                                                foreach($student_work['formatif'] as $f) { if($f['tipe'] == 'PG') { $total++; if($f['is_correct']) $correct++; } }
                                                if($total > 0) $pg_score = round(($correct/$total)*100);
                                            }
                                        ?>
                                        <div class="form-group mb-3">
                                            <label class="small font-weight-bold">4. Tes Formatif PG/Essay (0-100)</label>
                                            <div class="input-group">
                                                <input type="number" name="score_formatif" class="form-control score-input" value="<?= $detail['score_formatif'] ?: $pg_score ?>" min="0" max="100" oninput="calculateFinal()">
                                                <?php if($pg_score > 0): ?>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFormatif(<?= $pg_score ?>)">Gunakan Kuis</button>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">Nilai kuis PG: <?= $pg_score ?></small>
                                        </div>

                                        <!-- Score Refleksi -->
                                        <div class="form-group mb-4">
                                            <label class="small font-weight-bold">5. Refleksi (Max: 10)</label>
                                            <input type="number" name="score_refleksi" class="form-control score-input" value="<?= $detail['score_refleksi'] ?? 0 ?>" min="0" max="10" oninput="calculateFinal()">
                                        </div>

                                        <hr>

                                        <!-- HASIL AKHIR -->
                                        <div class="bg-light p-3 rounded mb-4 text-center border">
                                            <div class="small text-muted mb-1 text-uppercase font-weight-bold">Nilai Akhir (Weighted)</div>
                                            <div class="h2 font-weight-bold text-primary mb-0" id="finalDisplay"><?= $pengumpulan['nilai'] ?: 0 ?></div>
                                            <input type="hidden" name="nilai" id="nilaiInput" value="<?= $pengumpulan['nilai'] ?: 0 ?>">
                                            <div class="small text-muted mt-1">(60% Tugas + 40% Formatif)</div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label for="catatan_guru" class="font-weight-bold">Catatan Guru</label>
                                            <textarea class="form-control" id="catatan_guru" name="catatan_guru" rows="3" placeholder="Saran/apresiasi..."><?= htmlspecialchars($pengumpulan['catatan_guru'] ?? '') ?></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold rounded-lg">
                                            <i class="fas fa-check-circle mr-2"></i> Simpan Penilaian
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

<script>
function calculateFinal() {
    let diag = parseInt($('input[name="score_diagnostik"]').val()) || 0;
    let materi = parseInt($('input[name="score_materi"]').val()) || 0;
    let tugas = parseInt($('input[name="score_tugas_materi"]').val()) || 0;
    let formatif = parseInt($('input[name="score_formatif"]').val()) || 0;
    let ref = parseInt($('input[name="score_refleksi"]').val()) || 0;

    // Nilai Tugas (Diag + Materi + Tugas + Ref) -> Max 100
    let totalTugas = diag + materi + tugas + ref;
    
    // Bobot: 60% Tugas + 40% Formatif
    let final = (totalTugas * 0.6) + (formatif * 0.4);
    final = Math.round(final);

    $('#finalDisplay').text(final);
    $('#nilaiInput').val(final);
}

function setFormatif(val) {
    $('input[name="score_formatif"]').val(val);
    calculateFinal();
}
</script>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                        <h5 class="text-muted">Data pengumpulan tidak ditemukan.</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
