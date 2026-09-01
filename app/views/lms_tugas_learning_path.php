<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    .lp-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .lp-header { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border-radius: 20px; padding: 40px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2); }
    .lp-title { font-size: 2rem; font-weight: 800; margin-bottom: 10px; }
    
    .lp-stages { display: flex; flex-direction: column; gap: 20px; }
    .lp-stage-card { background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden; transition: all 0.3s; position: relative; }
    .lp-stage-card.locked { opacity: 0.6; pointer-events: none; }
    .lp-stage-card.locked .lp-stage-header { background: #f8fafc; }
    .lp-stage-card.locked .lp-stage-icon { background: #e2e8f0; color: #94a3b8; }
    
    .lp-stage-header { padding: 20px 30px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; cursor: pointer; }
    .lp-stage-info { display: flex; align-items: center; gap: 20px; }
    .lp-stage-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; background: #eef2ff; color: #6366f1; font-weight: bold; }
    .lp-stage-title { font-size: 1.2rem; font-weight: 700; color: #1e293b; margin: 0; }
    .lp-stage-desc { font-size: 0.9rem; color: #64748b; margin: 0; }
    
    .lp-stage-status { display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem; }
    .status-done { color: #10b981; }
    .status-locked { color: #94a3b8; }
    .status-active { color: #f59e0b; }
    
    .lp-stage-body { padding: 30px; display: none; }
    .lp-stage-card.active .lp-stage-body { display: block; }
    .lp-stage-card.active { border-color: #6366f1; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1); }
    
    /* Content Styles */
    .content-box { font-size: 1.1rem; line-height: 1.8; color: #334155; }
    .video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; border-radius: 15px; overflow: hidden; background: #000; margin-top: 20px; }
    .video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
    
    .btn-complete { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2); transition: all 0.3s; }
    .btn-complete:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(16, 185, 129, 0.3); }
    .btn-complete:disabled { background: #94a3b8; transform: none; box-shadow: none; cursor: not-allowed; }
    
    .pdf-viewer { width: 100%; height: 80vh; min-height: 600px; border: none; border-radius: 15px; background: #f8fafc; margin-top: 20px; }
    
    @media (max-width: 767.98px) {
        .lp-container { padding: 6px 4px !important; }
        .lp-header { padding: 12px 14px !important; border-radius: 10px !important; margin-bottom: 10px; }
        .lp-title { font-size: 0.95rem !important; line-height: 1.25; margin-bottom: 4px !important; }
        .lp-header p { font-size: 0.70rem !important; }
        .lp-header .badge { font-size: 0.65rem !important; padding: 3px 8px !important; margin-bottom: 6px !important; }
        
        .lp-stages { gap: 8px !important; }
        .lp-stage-card { border-radius: 8px !important; }
        .lp-stage-header { padding: 8px 10px !important; }
        .lp-stage-info { gap: 8px !important; }
        .lp-stage-icon { width: 26px !important; height: 26px !important; font-size: 0.75rem !important; border-radius: 6px !important; }
        .lp-stage-title { font-size: 0.78rem !important; }
        .lp-stage-desc { font-size: 0.65rem !important; }
        .lp-stage-status { font-size: 0.68rem !important; gap: 4px !important; }
        .lp-stage-body { padding: 8px 8px !important; }
        
        .content-box { font-size: 0.72rem !important; line-height: 1.45; word-break: break-word; }
        .content-box h1, .content-box h2, .content-box h3 { font-size: 0.80rem !important; margin-top: 6px !important; margin-bottom: 3px !important; }
        .content-box h4, .content-box h5, .content-box h6 { font-size: 0.74rem !important; margin-top: 4px !important; margin-bottom: 2px !important; }
        .content-box p, .content-box li, .content-box td, .content-box th, .content-box span { font-size: 0.70rem !important; }
        .content-box table { width: 100% !important; max-width: 100%; display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .btn-complete { width: 100% !important; text-align: center; padding: 6px 16px !important; font-size: 0.72rem !important; }
    }
</style>

<section class="content">
    <div class="lp-container">
        <div class="lp-header">

            <div class="badge badge-light px-3 py-2 rounded-pill mb-3 text-primary font-weight-bold">Learning Path</div>
            <h1 class="lp-title"><?php echo htmlspecialchars($materi['judul_materi']); ?></h1>
            <p class="mb-0 opacity-75"><i class="fas fa-book mr-2"></i> <?php echo htmlspecialchars($materi['nama_mapel']); ?> | Batas Waktu: <?php echo date('d M Y H:i', strtotime($tugas['deadline'])); ?></p>
        </div>

        <div class="lp-stages">
            
            <!-- STAGE 1: IDENTITAS, CP, TP, LANGKAH & DIAGNOSTIK -->
            <?php 
                // Di backend, ketika stage 1 disubmit, stage_instruksi dan stage_diagnostik akan bernilai 1.
                $s1_done = $progress['stage_diagnostik'] == 1;
                $s1_active = !$s1_done;
                $s1_class = $s1_done ? '' : 'active';
                
                $diag_questions_raw = json_decode($materi['tes_diagnostik_config'] ?? '[]', true);
                $diag_questions = [];
                if (is_array($diag_questions_raw)) {
                    foreach ($diag_questions_raw as $dq) {
                        if (is_array($dq) && isset($dq['q'])) { $diag_questions[] = $dq['q']; } 
                        elseif (is_string($dq) && trim($dq) !== '') { $diag_questions[] = $dq; }
                    }
                }
            ?>
            <div class="lp-stage-card <?php echo $s1_class; ?>" id="stage-1">
                <div class="lp-stage-header" onclick="toggleStage(1)">
                    <div class="lp-stage-info">
                        <div class="lp-stage-icon"><i class="fas fa-id-card"></i></div>
                        <div>
                            <h3 class="lp-stage-title">1. Identitas, Instruksi & Diagnostik</h3>
                            <p class="lp-stage-desc">Pahami target pembelajaran & cek pemahaman awal</p>
                        </div>
                    </div>
                    <div class="lp-stage-status">
                        <?php if($s1_done): ?><span class="status-done"><i class="fas fa-check-circle fa-lg"></i> Selesai</span>
                        <?php else: ?><span class="status-active"><i class="fas fa-play-circle fa-lg"></i> Sedang Dikerjakan</span><?php endif; ?>
                    </div>
                </div>
                <div class="lp-stage-body">
                    <div class="content-box">
                        <!-- Identitas Modul -->
                        <div class="p-3 mb-4 rounded-lg" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-info-circle text-info mr-2"></i> Identitas Modul</h5>
                            <div class="row">
                                <div class="col-md-6 mb-2"><span class="text-muted d-block" style="font-size: 0.85rem;">Mata Pelajaran</span><strong><?php echo htmlspecialchars($materi['nama_mapel'] ?? '-'); ?></strong></div>
                                <div class="col-md-6 mb-2"><span class="text-muted d-block" style="font-size: 0.85rem;">Guru Pengampu</span><strong><?php echo htmlspecialchars($tugas['nama_guru'] ?? $materi['nama_guru'] ?? 'Administrator'); ?></strong></div>
                                <div class="col-md-6 mb-2"><span class="text-muted d-block" style="font-size: 0.85rem;">Kelas / Tingkat</span><strong><?php echo htmlspecialchars($materi['tingkat'] ?? 'Umum'); ?></strong></div>
                                <div class="col-md-6 mb-2"><span class="text-muted d-block" style="font-size: 0.85rem;">Topik Materi</span><strong><?php echo htmlspecialchars($materi['judul_materi'] ?? '-'); ?></strong></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h5><i class="fas fa-bullseye text-primary mr-2"></i> Capaian Pembelajaran</h5>
                                <?php if ($cp_data || !empty($materi['cp_manual'])): ?>
                                <div class="p-3 bg-light rounded-lg border-left border-primary" style="border-width: 4px !important;">
                                    <?php echo nl2br(htmlspecialchars($cp_data['deskripsi_cp'] ?? $materi['cp_manual'])); ?>
                                </div>
                                <?php else: ?>
                                <div class="p-3 bg-light rounded-lg border-left border-secondary text-muted" style="border-width: 4px !important;"><em>Belum ada Capaian Pembelajaran yang diatur.</em></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h5><i class="fas fa-tasks text-success mr-2"></i> Tujuan Pembelajaran</h5>
                                <?php if (!empty($tp_data) || !empty($materi['tp_manual'])): ?>
                                <div class="p-3 bg-light rounded-lg border-left border-success" style="border-width: 4px !important;">
                                    <ul class="pl-3 mb-0">
                                    <?php foreach ($tp_data as $tp): ?><li><strong><?php echo $tp['kode_tp']; ?></strong>: <?php echo htmlspecialchars($tp['deskripsi_tp']); ?></li><?php endforeach; ?>
                                    <?php if (!empty($materi['tp_manual'])) echo "<li>" . nl2br(htmlspecialchars($materi['tp_manual'])) . "</li>"; ?>
                                    </ul>
                                </div>
                                <?php else: ?>
                                <div class="p-3 bg-light rounded-lg border-left border-secondary text-muted" style="border-width: 4px !important;"><em>Belum ada Tujuan Pembelajaran yang diatur.</em></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($materi['instruksi']): ?>
                        <hr>
                        <h5 class="mb-3"><i class="fas fa-chalkboard-teacher text-warning mr-2"></i> Instruksi Guru / Langkah Pembelajaran</h5>
                        <div class="p-4 mb-4 rounded-lg" style="background:#fffbeb; border: 1px solid #fef3c7;">
                            <?php echo $materi['instruksi']; ?>
                        </div>
                        <?php endif; ?>

                        <?php if(is_array($diag_questions) && !empty($diag_questions)): ?>
                        <hr>
                        <h5 class="mb-4"><i class="fas fa-stethoscope text-info mr-2"></i> Tes Diagnostik Awal</h5>
                        <?php if(!$s1_done): ?>
                        <form id="form-diagnostik" onsubmit="event.preventDefault(); submitEssay(1, 'diagnostik');">
                            <?php foreach ($diag_questions as $idx => $q): ?>
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark small"><?php echo ($idx+1) . ". " . htmlspecialchars($q); ?></label>
                                <input type="hidden" name="pertanyaan[]" value="<?php echo htmlspecialchars($q); ?>">
                                <textarea name="jawaban[]" class="form-control" rows="2" required></textarea>
                            </div>
                            <?php endforeach; ?>
                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-complete"><i class="fas fa-paper-plane mr-2"></i> Kirim Jawaban & Lanjutkan</button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-success"><i class="fas fa-check mr-2"></i> Anda telah menyelesaikan tahap diagnostik ini.</div>
                        <?php endif; ?>
                        
                        <?php elseif(!empty($materi['tes_diagnostik_config'])): ?>
                        <hr>
                        <h5 class="mb-4"><i class="fas fa-stethoscope text-info mr-2"></i> Tes Diagnostik</h5>
                        <div class="p-3 bg-light rounded border mb-4">
                            <?php echo $materi['tes_diagnostik_config']; ?>
                        </div>
                        <?php if(!$s1_done): ?>
                        <div class="text-right mt-4">
                            <button class="btn btn-complete" onclick="markComplete(1)"><i class="fas fa-check mr-2"></i> Selesai, Lanjutkan</button>
                        </div>
                        <?php endif; ?>
                        
                        <?php else: ?>
                        <?php if(!$s1_done): ?>
                        <div class="text-right mt-4">
                            <button class="btn btn-complete" onclick="markComplete(1)"><i class="fas fa-check mr-2"></i> Saya Mengerti, Lanjutkan</button>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- STAGE 2: MATERI VIDEO & Q (Sebelumnya Stage 3) -->
            <?php 
                $video_questions_raw = json_decode($materi['video_questions'] ?? '[]', true);
                $video_questions = [];
                if (is_array($video_questions_raw)) {
                    foreach ($video_questions_raw as $vq) {
                        if (is_array($vq) && isset($vq['q'])) { $video_questions[] = $vq['q']; } 
                        elseif (is_string($vq) && trim($vq) !== '') { $video_questions[] = $vq; }
                    }
                }
                $s2_locked = !$progress['stage_diagnostik'];
                $s2_done = $progress['stage_materi'] == 1;
                $s2_active = !$s2_locked && !$s2_done;
                $s2_class = $s2_locked ? 'locked' : ($s2_active ? 'active' : '');
            ?>
            <div class="lp-stage-card <?php echo $s2_class; ?>" id="stage-2">
                <div class="lp-stage-header" onclick="toggleStage(2)">
                    <div class="lp-stage-info">
                        <div class="lp-stage-icon"><i class="fab fa-youtube"></i></div>
                        <div>
                            <h3 class="lp-stage-title">2. Materi Video & Pertanyaan</h3>
                            <p class="lp-stage-desc">Simak video dan jawab pertanyaan pemantik</p>
                        </div>
                    </div>
                    <div class="lp-stage-status">
                        <?php if($s2_locked): ?><span class="status-locked"><i class="fas fa-lock fa-lg"></i> Terkunci</span>
                        <?php elseif($s2_done): ?><span class="status-done"><i class="fas fa-check-circle fa-lg"></i> Selesai</span>
                        <?php else: ?><span class="status-active"><i class="fas fa-play-circle fa-lg"></i> Sedang Dikerjakan</span><?php endif; ?>
                    </div>
                </div>
                <div class="lp-stage-body">
                    <div class="content-box">
                        <?php if ($materi['video_url']): ?>
                            <?php 
                            $video_id = '';
                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $materi['video_url'], $match)) {
                                $video_id = $match[1];
                            }
                            ?>
                            <?php if ($video_id): ?>
                                <div class="video-wrapper shadow mb-4"><div id="ytplayer"></div></div>
                                <?php if(!$s2_done): ?>
                                <div class="alert alert-warning mb-4" id="video-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> Anda wajib menonton video sampai selesai untuk memunculkan pertanyaan.
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-secondary py-5 text-center"><i class="fas fa-video-slash fa-3x mb-3 opacity-50"></i><br>Tidak ada materi video untuk bagian ini.</div>
                        <?php endif; ?>
                        
                        <div id="video-questions-area" <?php echo ($materi['video_url'] && $video_id && !$s2_done) ? 'style="display:none;"' : ''; ?>>
                            <?php if(!empty($video_questions)): ?>
                            <hr>
                            <h5 class="mb-4"><i class="fas fa-question-circle text-primary mr-2"></i> Pertanyaan Video</h5>
                            <?php if(!$s2_done): ?>
                            <form id="form-video" onsubmit="event.preventDefault(); submitEssay(2, 'video');">
                                <?php foreach ($video_questions as $idx => $q): ?>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark small"><?php echo ($idx+1) . ". " . htmlspecialchars($q); ?></label>
                                    <input type="hidden" name="pertanyaan[]" value="<?php echo htmlspecialchars($q); ?>">
                                    <textarea name="jawaban[]" class="form-control" rows="2" required></textarea>
                                </div>
                                <?php endforeach; ?>
                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-complete"><i class="fas fa-paper-plane mr-2"></i> Kirim Jawaban Video</button>
                                </div>
                            </form>
                            <?php else: ?>
                            <div class="alert alert-success"><i class="fas fa-check mr-2"></i> Jawaban video Anda telah tersimpan.</div>
                            <?php endif; ?>
                            <?php else: ?>
                            <div class="text-right mt-4">
                                <button class="btn btn-complete" id="btn-video-continue" <?php echo ($materi['video_url'] && $video_id && !$s2_done) ? 'disabled' : ''; ?> onclick="markComplete(2)"><i class="fas fa-check mr-2"></i> Lanjutkan</button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAGE 3: MATERI TERTULIS & Q (Sebelumnya Stage 4) -->
            <?php 
                $materi_questions_raw = json_decode($materi['materi_questions'] ?? '[]', true);
                $materi_questions = [];
                if (is_array($materi_questions_raw)) {
                    foreach ($materi_questions_raw as $mq) {
                        if (is_array($mq) && isset($mq['q'])) { $materi_questions[] = $mq['q']; } 
                        elseif (is_string($mq) && trim($mq) !== '') { $materi_questions[] = $mq; }
                    }
                }
                $s3_locked = !$progress['stage_materi'];
                $s3_done = $progress['stage_essay'] == 1;
                $s3_active = !$s3_locked && !$s3_done;
                $s3_class = $s3_locked ? 'locked' : ($s3_active ? 'active' : '');
            ?>
            <div class="lp-stage-card <?php echo $s3_class; ?>" id="stage-3">
                <div class="lp-stage-header" onclick="toggleStage(3)">
                    <div class="lp-stage-info">
                        <div class="lp-stage-icon"><i class="fas fa-book-open"></i></div>
                        <div>
                            <h3 class="lp-stage-title">3. Materi Tertulis & Pertanyaan</h3>
                            <p class="lp-stage-desc">Baca teks/file dan jawab poin pentingnya</p>
                        </div>
                    </div>
                    <div class="lp-stage-status">
                        <?php if($s3_locked): ?><span class="status-locked"><i class="fas fa-lock fa-lg"></i> Terkunci</span>
                        <?php elseif($s3_done): ?><span class="status-done"><i class="fas fa-check-circle fa-lg"></i> Selesai</span>
                        <?php else: ?><span class="status-active"><i class="fas fa-play-circle fa-lg"></i> Sedang Dikerjakan</span><?php endif; ?>
                    </div>
                </div>
                <div class="lp-stage-body">
                    <div class="content-box">
                        <div class="mb-4"><?php echo $materi['deskripsi']; ?></div>
                        
                        <?php if ($materi['file_path']): ?>
                            <?php 
                            $ext = strtolower(pathinfo($materi['file_path'], PATHINFO_EXTENSION));
                            $file_url = BASE_URL . 'uploads/lms_materi/' . $materi['file_path'];
                            $is_pdf = ($ext == 'pdf');
                            $is_doc = in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);
                            ?>
                            <?php if ($is_pdf): ?>
                                <div class="embed-responsive embed-responsive-16by9 shadow-sm rounded-lg overflow-hidden mb-4" style="height: 70vh;">
                                    <iframe src="<?php echo $file_url; ?>" class="embed-responsive-item" allowfullscreen></iframe>
                                </div>
                            <?php elseif ($is_doc): ?>
                                <div class="embed-responsive embed-responsive-16by9 shadow-sm rounded-lg overflow-hidden mb-4" style="height: 70vh;">
                                    <iframe src="https://docs.google.com/viewer?url=<?php echo urlencode(BASE_URL . 'uploads/lms_materi/' . $materi['file_path']); ?>&embedded=true" class="embed-responsive-item" allowfullscreen></iframe>
                                </div>
                            <?php endif; ?>
                            <div class="p-3 bg-light rounded border mt-4 d-flex align-items-center justify-content-between">
                                <div><i class="fas fa-file-alt text-primary fa-2x mr-3"></i> <strong>File Materi / Modul (<?php echo strtoupper($ext); ?>)</strong></div>
                                <a href="<?php echo $file_url; ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-4">Download</a>
                            </div>
                        <?php endif; ?>

                        <?php if(!empty($materi_questions)): ?>
                        <hr class="my-5">
                        <h5 class="mb-4"><i class="fas fa-pen-fancy text-success mr-2"></i> Pertanyaan Materi</h5>
                        <?php if(!$s3_done): ?>
                        <form id="form-materi" onsubmit="event.preventDefault(); submitEssay(3, 'materi');" enctype="multipart/form-data">
                            <?php foreach ($materi_questions as $idx => $q): ?>
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark small"><?php echo ($idx+1) . ". " . htmlspecialchars($q); ?></label>
                                <input type="hidden" name="pertanyaan[]" value="<?php echo htmlspecialchars($q); ?>">
                                <textarea name="jawaban[]" class="form-control" rows="2" required></textarea>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark small"><i class="fas fa-camera mr-2"></i> Upload Foto Catatan (Opsional)</label>
                                <p class="text-muted small">Jika Anda mengerjakan di buku tulis, silakan foto dan unggah di sini.</p>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_materi_siswa" name="file_materi_siswa" accept="image/*">
                                    <label class="custom-file-label" for="file_materi_siswa">Pilih foto...</label>
                                </div>
                            </div>

                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-complete"><i class="fas fa-paper-plane mr-2"></i> Kirim Jawaban Materi</button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-success"><i class="fas fa-check mr-2"></i> Jawaban materi Anda telah tersimpan.</div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="text-right mt-4">
                            <button class="btn btn-complete" onclick="markComplete(3)"><i class="fas fa-check mr-2"></i> Lanjutkan</button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- STAGE 4: TES FORMATIF (Sebelumnya Stage 5) -->
            <?php 
                $formatif_soal = array_filter($soal_list, function($s) { return ($s['kategori_soal'] ?? 'Latihan') == 'Latihan'; });
                $has_formatif = !empty($formatif_soal);
                if (!$has_formatif) { $progress['stage_formatif'] = 1; }
                
                $s4_locked = !$progress['stage_essay'];
                $s4_done = $progress['stage_formatif'] == 1;
                $s4_active = !$s4_locked && !$s4_done && $has_formatif;
                $s4_class = $s4_locked ? 'locked' : ($s4_active ? 'active' : '');
            ?>
            <?php if ($has_formatif): ?>
            <div class="lp-stage-card <?php echo $s4_class; ?>" id="stage-4">
                <div class="lp-stage-header" onclick="toggleStage(4)">
                    <div class="lp-stage-info">
                        <div class="lp-stage-icon"><i class="fas fa-tasks"></i></div>
                        <div>
                            <h3 class="lp-stage-title">4. Tes Formatif Akhir</h3>
                            <p class="lp-stage-desc">Uji kompetensi objektif / pilihan ganda</p>
                        </div>
                    </div>
                    <div class="lp-stage-status">
                        <?php if($s4_locked): ?><span class="status-locked"><i class="fas fa-lock fa-lg"></i> Terkunci</span>
                        <?php elseif($s4_done): ?><span class="status-done"><i class="fas fa-check-circle fa-lg"></i> Selesai</span>
                        <?php else: ?><span class="status-active"><i class="fas fa-play-circle fa-lg"></i> Sedang Dikerjakan</span><?php endif; ?>
                    </div>
                </div>
                <div class="lp-stage-body">
                    <div class="content-box">
                        <?php if(!$s4_done): ?>
                        <form id="form-formatif" onsubmit="event.preventDefault(); submitFormatif(4);">
                            <?php foreach ($formatif_soal as $i => $s): ?>
                                <div class="card shadow-sm mb-4 border">
                                    <div class="card-body p-4">
                                        <div class="d-flex mb-3">
                                            <span class="badge badge-primary mr-3" style="height:fit-content; width:30px;"><?php echo array_search($s, array_values($formatif_soal)) + 1; ?></span>
                                            <div class="font-weight-bold text-dark mb-0"><?php echo nl2br(htmlspecialchars($s['pertanyaan'])); ?></div>
                                        </div>
                                        <?php if ($s['tipe'] == 'PG'): ?>
                                            <div class="row mt-3">
                                                <?php foreach (['a', 'b', 'c', 'd', 'e'] as $opt): ?>
                                                    <?php if ($s['opsi_'.$opt]): ?>
                                                        <div class="col-md-6 mb-2">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="f_<?php echo $s['id_soal']; ?>_<?php echo $opt; ?>" name="formatif[<?php echo $s['id_soal']; ?>]" class="custom-control-input" value="<?php echo strtoupper($opt); ?>" required>
                                                                <label class="custom-control-label font-weight-normal" for="f_<?php echo $s['id_soal']; ?>_<?php echo $opt; ?>"><span class="font-weight-bold mr-1"><?php echo strtoupper($opt); ?>.</span> <?php echo htmlspecialchars($s['opsi_'.$opt]); ?></label>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-complete"><i class="fas fa-paper-plane mr-2"></i> Kirim Jawaban Kuis</button>
                            </div>
                        </form>
                        <?php else: ?>
                            <?php 
                            $has_essay = false;
                            foreach($formatif_soal as $soal_f) { if($soal_f['tipe'] == 'Essay') { $has_essay = true; break; } }
                            ?>
                            <div class="alert alert-success shadow-sm" style="border-radius: 12px; border-left: 5px solid #10b981;">
                                <h6 class="font-weight-bold mb-2"><i class="fas fa-check-circle mr-2"></i> Tes Formatif Selesai</h6>
                                <?php if($has_essay): ?>
                                    <p class="mb-0">Jawaban Anda telah terkirim. Karena terdapat soal **Essay**, nilai akan muncul setelah diperiksa oleh Guru.</p>
                                <?php else: ?>
                                    <p class="mb-0">Nilai tes formatif Anda telah tersimpan. **Harap dicatat:** Ini baru nilai kuis, bukan nilai keseluruhan tugas. Nilai final akan diberikan setelah pemeriksaan seluruh tahapan oleh Guru.</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- STAGE 5: REFLEKSI (Sebelumnya Stage 6) -->
            <?php 
                $ref_questions_raw = json_decode($materi['refleksi_config'] ?? '[]', true);
                $ref_questions = [];
                if (is_array($ref_questions_raw)) {
                    foreach ($ref_questions_raw as $rq) {
                        if (is_array($rq) && isset($rq['q'])) { $ref_questions[] = $rq['q']; } 
                        elseif (is_string($rq) && trim($rq) !== '') { $ref_questions[] = $rq; }
                    }
                }
                $has_refleksi = !empty($ref_questions);
                if (!$has_refleksi) { $progress['stage_refleksi'] = 1; }
                
                $s5_locked = !$progress['stage_formatif'];
                $s5_done = $progress['stage_refleksi'] == 1;
                $s5_active = !$s5_locked && !$s5_done && $has_refleksi;
                $s5_class = $s5_locked ? 'locked' : ($s5_active ? 'active' : '');
            ?>
            <?php if ($has_refleksi): ?>
            <div class="lp-stage-card <?php echo $s5_class; ?>" id="stage-5">
                <div class="lp-stage-header" onclick="toggleStage(5)">
                    <div class="lp-stage-info">
                        <div class="lp-stage-icon"><i class="fas fa-comments"></i></div>
                        <div>
                            <h3 class="lp-stage-title">5. Refleksi Pembelajaran</h3>
                            <p class="lp-stage-desc">Beri umpan balik mengenai proses belajar</p>
                        </div>
                    </div>
                    <div class="lp-stage-status">
                        <?php if($s5_locked): ?><span class="status-locked"><i class="fas fa-lock fa-lg"></i> Terkunci</span>
                        <?php elseif($s5_done): ?><span class="status-done"><i class="fas fa-check-circle fa-lg"></i> Selesai</span>
                        <?php else: ?><span class="status-active"><i class="fas fa-play-circle fa-lg"></i> Sedang Dikerjakan</span><?php endif; ?>
                    </div>
                </div>
                <div class="lp-stage-body">
                    <div class="content-box">
                        <?php if(!$s5_done): ?>
                        <form id="form-refleksi" onsubmit="event.preventDefault(); submitRefleksi(5);">
                            <?php foreach ($ref_questions as $r_idx => $q_text): ?>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark small"><?php echo $q_text; ?></label>
                                    <textarea name="refleksi[<?php echo $r_idx; ?>]" class="form-control" rows="2" required></textarea>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-complete"><i class="fas fa-flag-checkered mr-2"></i> Selesaikan Seluruh Modul</button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-success"><i class="fas fa-check mr-2"></i> Refleksi selesai. Selamat, Anda telah menyelesaikan seluruh tahapan modul ini!</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- API YouTube iframe Player -->
<?php if ($materi['video_url'] && isset($video_id) && $video_id): ?>
<script src="https://www.youtube.com/iframe_api"></script>
<script>
    var player;
    var maxTimeWatched = 0;
    var checkInterval;

    function onYouTubeIframeAPIReady() {
        player = new YT.Player('ytplayer', {
            videoId: '<?php echo $video_id; ?>',
            playerVars: { 'controls': 1, 'disablekb': 1, 'rel': 0 },
            events: {
                'onStateChange': onPlayerStateChange
            }
        });
    }

    var isVideoCompleted = <?php echo $s2_done ? 'true' : 'false'; ?>;
    
    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING && !isVideoCompleted) {
            checkInterval = setInterval(checkTime, 1000);
        } else {
            clearInterval(checkInterval);
        }

        if (event.data == YT.PlayerState.ENDED) {
            isVideoCompleted = true;
            $('#btn-video-continue').prop('disabled', false);
            $('#video-questions-area').slideDown();
            $('#video-warning').removeClass('alert-warning').addClass('alert-success')
                .html('<i class="fas fa-check-circle mr-2"></i> Video selesai ditonton. Silakan jawab pertanyaan di bawah.');
        }
    }

    function checkTime() {
        if (!player || isVideoCompleted) return;
        var currentTime = player.getCurrentTime();
        
        // Jika mencoba skip lebih dari 2 detik ke depan
        if (currentTime > maxTimeWatched + 2) {
            player.seekTo(maxTimeWatched);
        } else {
            if (currentTime > maxTimeWatched) {
                maxTimeWatched = currentTime;
            }
        }
    }
</script>
<?php endif; ?>

<script>
    function toggleStage(stageNum) {
        if ($('#stage-' + stageNum).hasClass('locked')) return;
        $('.lp-stage-card').removeClass('active');
        $('#stage-' + stageNum).addClass('active');
    }

    function markComplete(stageNum) {
        $.post('<?= BASE_URL ?>lms/lp_mark_stage', {
            id_tugas: <?php echo $id_tugas; ?>,
            stage: stageNum
        }, function(res) {
            location.reload();
        });
    }

    function submitEssay(stageNum, type) {
        const form = document.getElementById('form-' + type);
        const formData = new FormData(form);
        formData.append('id_tugas', '<?php echo $id_tugas; ?>');
        formData.append('stage', stageNum);
        formData.append('type', type);
        
        $.ajax({
            url: '<?= BASE_URL ?>lms/lp_submit_essay',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                markComplete(stageNum);
            }
        });
    }

    function submitFormatif(stageNum) {
        const data = $('#form-formatif').serialize() + '&id_tugas=<?php echo $id_tugas; ?>&stage=' + stageNum;
        $.post('<?= BASE_URL ?>lms/lp_submit_formatif', data, function(res) {
            markComplete(stageNum);
        });
    }

    function submitRefleksi(stageNum) {
        const data = $('#form-refleksi').serialize() + '&id_tugas=<?php echo $id_tugas; ?>&stage=' + stageNum;
        $.post('<?= BASE_URL ?>lms/lp_submit_refleksi', data, function(res) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Seluruh tahapan telah selesai. Silakan menunggu pemeriksaan dari Guru untuk nilai final.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        });
    }

    // Update label custom file
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
