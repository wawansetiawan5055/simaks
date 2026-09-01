<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .soal-form-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .opsi-input-card {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 12px;
        transition: border-color 0.2s ease;
    }
    .opsi-input-card:focus-within {
        border-color: #6366f1;
        background: #ffffff;
    }
    .opsi-badge-radio {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.95rem;
        background: #e2e8f0;
        color: #334155;
    }
</style>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-8">
                <a href="<?= BASE_URL ?>cbt_bank_soal/detail?id_bank=<?= $bank['id_bank'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 mb-2 font-weight-bold shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Butir Soal
                </a>
                <h4 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-edit text-danger mr-2"></i> <?= $title ?>
                </h4>
                <p class="text-muted small mb-0">
                    Bank Soal: <strong><?= htmlspecialchars($bank['nama_bank']) ?></strong> &bull;
                    Mapel: <strong><?= htmlspecialchars($bank['nama_mapel'] ?? '-') ?></strong> &bull;
                    Kelas: <strong><?= htmlspecialchars($bank['tingkat'] ?? '-') ?></strong>
                </p>
            </div>
        </div>
    </div>
</div>

<section class="content mt-3">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <div class="card soal-form-card">
            <form method="POST" action="<?= BASE_URL ?>cbt_bank_soal/<?= isset($soal) ? 'update_soal' : 'store_soal' ?>" enctype="multipart/form-data">
                <input type="hidden" name="id_bank" value="<?= $bank['id_bank'] ?>">
                <?php if (isset($soal)): ?>
                    <input type="hidden" name="id_soal" value="<?= $soal['id_soal'] ?>">
                <?php endif; ?>

                <div class="card-body p-4">
                    <!-- BARIS 1: CP & TP SELECTOR -->
                    <div class="p-3 bg-light rounded-lg border mb-3">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-graduation-cap text-primary mr-1"></i> Penyelarasan Kurikulum Merdeka (CP, TP &amp; Kisi-Kisi)
                        </h6>
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3">
                                <label class="font-weight-bold small text-dark">Capaian Pembelajaran (CP)</label>
                                <select name="id_cp" id="form_id_cp" class="form-control rounded-pill" onchange="loadTpForForm(this.value)">
                                    <option value="">-- Pilih Capaian Pembelajaran (CP) --</option>
                                    <?php if (!empty($cp_list)): ?>
                                        <?php foreach ($cp_list as $cp): ?>
                                            <option value="<?= $cp['id_cp'] ?>" <?= (isset($soal) && $soal['id_cp'] == $cp['id_cp']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((!empty($cp['fase']) ? '[Fase ' . $cp['fase'] . '] ' : '') . mb_strimwidth($cp['deskripsi_cp'], 0, 70, '...')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6 col-12 mb-3">
                                <label class="font-weight-bold small text-dark">Tujuan Pembelajaran (TP)</label>
                                <select name="id_tp" id="form_id_tp" class="form-control rounded-pill" onchange="applyTpToForm(this)">
                                    <option value="">-- Pilih Tujuan Pembelajaran (TP) --</option>
                                    <?php if (!empty($tp_list)): ?>
                                        <?php foreach ($tp_list as $tp): ?>
                                            <option value="<?= $tp['id_tp'] ?>" data-materi="<?= htmlspecialchars($tp['materi'] ?? '') ?>" <?= (isset($soal) && $soal['id_tp'] == $tp['id_tp']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars(($tp['kode_tp'] ? $tp['kode_tp'] . ' - ' : '') . $tp['deskripsi_tp']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6 col-12 mb-3">
                                <label class="font-weight-bold small text-dark">Lingkup Materi / Topik</label>
                                <input type="text" name="lingkup_materi" id="form_lingkup_materi" class="form-control rounded-pill" value="<?= htmlspecialchars($soal['lingkup_materi'] ?? '') ?>" placeholder="Contoh: Eksponen dan Logaritma">
                            </div>

                            <div class="col-md-6 col-12 mb-3">
                                <label class="font-weight-bold small text-dark">Level Kognitif <span class="text-danger">*</span></label>
                                <select name="level_kognitif" class="form-control rounded-pill">
                                    <option value="L1" <?= (isset($soal) && $soal['level_kognitif']==='L1') ? 'selected' : '' ?>>L1 - Pengetahuan / Pemahaman (C1-C2)</option>
                                    <option value="L2" <?= (!isset($soal) || $soal['level_kognitif']==='L2') ? 'selected' : '' ?>>L2 - Aplikasi / Penerapan Konsep (C3-C4)</option>
                                    <option value="L3" <?= (isset($soal) && ($soal['level_kognitif']==='L3' || strpos($soal['level_kognitif'],'HOTS')!==false)) ? 'selected' : '' ?>>L3 - Penalaran / Analisis Kritis (HOTS / C5-C6)</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="font-weight-bold small text-dark">
                                    <i class="fas fa-bullseye text-indigo mr-1"></i> Kalimat Indikator Soal (Untuk Format Kisi-Kisi Asesmen)
                                </label>
                                <input type="text" name="indikator_soal" class="form-control rounded-pill" value="<?= htmlspecialchars($soal['indikator_soal'] ?? '') ?>" placeholder="Contoh: Disajikan permasalahan kontekstual pertumbuhan bakteri, peserta didik dapat menentukan...">
                            </div>
                        </div>
                    </div>

                    <!-- BARIS 2: TIPE SOAL, BOBOT & KESULITAN -->
                    <div class="row mb-3">
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <label class="font-weight-bold small text-dark">Bentuk / Tipe Soal <span class="text-danger">*</span></label>
                            <select name="tipe_soal" id="tipeSoalSelect" class="form-control rounded-pill" required>
                                <option value="pg" <?= (isset($soal) && $soal['tipe_soal']==='pg') ? 'selected' : '' ?>>Pilihan Ganda (PG)</option>
                                <option value="essay" <?= (isset($soal) && $soal['tipe_soal']==='essay') ? 'selected' : '' ?>>Esai / Uraian</option>
                                <option value="tf" <?= (isset($soal) && $soal['tipe_soal']==='tf') ? 'selected' : '' ?>>Benar / Salah (True-False)</option>
                                <option value="matching" <?= (isset($soal) && $soal['tipe_soal']==='matching') ? 'selected' : '' ?>>Menjodohkan (Matching)</option>
                            </select>
                        </div>

                        <div class="col-md-4 col-6">
                            <label class="font-weight-bold small text-dark">Bobot Skor Soal <span class="text-danger">*</span></label>
                            <input type="number" name="bobot" class="form-control rounded-pill" value="<?= $soal['bobot'] ?? 1 ?>" min="1" max="100" required>
                        </div>

                        <div class="col-md-4 col-6">
                            <label class="font-weight-bold small text-dark">Tingkat Kesulitan</label>
                            <select name="tingkat_kesulitan" class="form-control rounded-pill">
                                <option value="mudah" <?= (isset($soal) && $soal['tingkat_kesulitan']==='mudah') ? 'selected' : '' ?>>Mudah (LOTS)</option>
                                <option value="sedang" <?= (!isset($soal) || $soal['tingkat_kesulitan']==='sedang') ? 'selected' : '' ?>>Sedang (MOTS)</option>
                                <option value="sulit" <?= (isset($soal) && $soal['tingkat_kesulitan']==='sulit') ? 'selected' : '' ?>>Sulit / HOTS (HOTS)</option>
                            </select>
                        </div>
                    </div>

                    <!-- WACANA / STIMULUS TEKS (OPSIONAL) -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">
                            <i class="fas fa-quote-left text-info mr-1"></i> Teks Wacana / Narasi Stimulus Soal (Opsional)
                        </label>
                        <textarea name="stimulus" class="form-control" rows="3" placeholder="Tuliskan teks cerita, data studi kasus, atau konteks wacana soal jika ada..."><?= htmlspecialchars($soal['stimulus'] ?? '') ?></textarea>
                    </div>

                    <!-- MEDIA STIMULUS SOAL -->
                    <div class="p-3 bg-light rounded-lg border mb-3">
                        <label class="font-weight-bold small text-dark mb-1">
                            <i class="fas fa-photo-video text-primary mr-1"></i> Lampiran Media Stimulus (Gambar, Audio Listening, Video)
                        </label>
                        <div class="row">
                            <div class="col-md-3 col-12 mb-2">
                                <select name="media_tipe" id="mediaTipeSelect" class="form-control form-control-sm rounded-pill">
                                    <option value="none" <?= (!isset($soal) || empty($soal['media_tipe']) || $soal['media_tipe']==='none') ? 'selected' : '' ?>>Tanpa Media Stimulus</option>
                                    <option value="gambar" <?= (isset($soal) && ($soal['media_tipe'] ?? '')==='gambar') ? 'selected' : '' ?>>Gambar Stimulus</option>
                                    <option value="audio" <?= (isset($soal) && ($soal['media_tipe'] ?? '')==='audio') ? 'selected' : '' ?>>Audio Listening</option>
                                    <option value="video" <?= (isset($soal) && ($soal['media_tipe'] ?? '')==='video') ? 'selected' : '' ?>>Video Embed</option>
                                </select>
                            </div>
                            <div class="col-md-9 col-12" id="mediaInputWrapper">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    </div>
                                    <input type="text" name="media_url" class="form-control rounded-right" placeholder="Masukkan URL Gambar/Audio/Video (atau upload file di bawah)" value="<?= htmlspecialchars($soal['media_url'] ?? '') ?>">
                                </div>
                                <div class="mt-2">
                                    <input type="file" name="media_file" class="form-control-file small" accept="image/*,audio/*,video/*">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TEKS PERTANYAAN -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold small text-dark">
                            Rumusan Butir Pertanyaan / Soal <span class="text-danger">*</span>
                        </label>
                        <textarea name="pertanyaan" class="form-control" rows="4" placeholder="Tuliskan butir pertanyaan di sini..." required><?= htmlspecialchars($soal['pertanyaan'] ?? '') ?></textarea>
                    </div>

                    <!-- BAGIAN OPSI JAWABAN PILIHAN GANDA (PG) -->
                    <div id="sectionPG" class="mb-4">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-list-ol text-primary mr-1"></i> Pilihan Jawaban &amp; Kunci Jawaban Benar
                        </h6>
                        
                        <?php 
                            $opsi_keys = ['A', 'B', 'C', 'D', 'E'];
                            $kunci_current = $soal['kunci_pg'] ?? 'A';
                            $map_opsi = [];
                            if (!empty($opsi_list)) {
                                foreach ($opsi_list as $o) {
                                    $map_opsi[$o['label']] = $o;
                                    if (!empty($o['is_benar'])) $kunci_current = $o['label'];
                                }
                            }
                        ?>

                        <?php foreach ($opsi_keys as $k): ?>
                            <?php $curr_o = $map_opsi[$k] ?? null; ?>
                            <div class="opsi-input-card">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="custom-control custom-radio mr-3">
                                        <input type="radio" id="kunci_<?= $k ?>" name="kunci_pg" value="<?= $k ?>" class="custom-control-input" <?= $kunci_current === $k ? 'checked' : '' ?>>
                                        <label class="custom-control-label font-weight-bold" for="kunci_<?= $k ?>">
                                            <span class="opsi-badge-radio"><?= $k ?></span> Kunci Benar
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group mb-1">
                                    <textarea name="opsi[<?= $k ?>]" class="form-control" rows="2" placeholder="Teks pilihan jawaban <?= $k ?>"><?= htmlspecialchars($curr_o['isi_opsi'] ?? '') ?></textarea>
                                </div>
                                <div class="small text-muted mt-1">
                                    <label class="font-weight-bold mr-2"><i class="fas fa-image mr-1"></i> Gambar Opsi <?= $k ?> (Opsional):</label>
                                    <input type="file" name="gambar_opsi_<?= $k ?>" class="small" accept="image/*">
                                    <?php if (!empty($curr_o['gambar'])): ?>
                                        <div class="mt-1">
                                            <img src="<?= htmlspecialchars($curr_o['gambar']) ?>" style="max-height: 60px;" class="img-thumbnail">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- BAGIAN BENAR / SALAH (TF) -->
                    <div id="sectionTF" class="mb-4" style="display: none;">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-check-double text-info mr-1"></i> Kunci Jawaban Benar / Salah
                        </h6>
                        <div class="p-3 bg-light rounded-lg border">
                            <label class="small font-weight-bold text-dark d-block mb-2">Pernyataan ini bernilai:</label>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="tf_b" name="kunci_tf" value="B" class="custom-control-input" <?= (!isset($soal['kunci_pg']) || $soal['kunci_pg']==='B') ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-bold text-success" for="tf_b">BENAR (TRUE)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="tf_s" name="kunci_tf" value="S" class="custom-control-input" <?= (isset($soal['kunci_pg']) && $soal['kunci_pg']==='S') ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-bold text-danger" for="tf_s">SALAH (FALSE)</label>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN MENJODOHKAN (MATCHING) -->
                    <div id="sectionMatching" class="mb-4" style="display: none;">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-arrows-alt-h text-success mr-1"></i> Pasangan Menjodohkan (Premis &amp; Pasangan Jawaban)
                        </h6>
                        <p class="small text-muted mb-2">Tuliskan pasangan premis (kiri) dan jawaban yang cocok (kanan):</p>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="row mb-2">
                            <div class="col-md-6 col-12 mb-2 mb-md-0">
                                <input type="text" name="matching_kiri[]" class="form-control form-control-sm rounded-pill" placeholder="Premis <?= $i ?> (Contoh: Inflasi)">
                            </div>
                            <div class="col-md-6 col-12">
                                <input type="text" name="matching_kanan[]" class="form-control form-control-sm rounded-pill" placeholder="Pasangan <?= $i ?> (Contoh: Kenaikan harga umum)">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>

                    <!-- PEMBAHASAN / RUBRIK ESAY -->
                    <div class="row">
                        <div class="col-md-6 col-12 form-group mb-3">
                            <label class="font-weight-bold small text-dark">
                                <i class="fas fa-lightbulb text-warning mr-1"></i> Pembahasan &amp; Solusi Soal
                            </label>
                            <textarea name="pembahasan" class="form-control" rows="3" placeholder="Tuliskan pembahasan langkah penyelesaian butir soal"><?= htmlspecialchars($soal['pembahasan'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 col-12 form-group mb-3">
                            <label class="font-weight-bold small text-dark">
                                <i class="fas fa-clipboard-check text-success mr-1"></i> Pedoman Rubrik Penskoran
                            </label>
                            <textarea name="rubrik_penilaian" class="form-control" rows="3" placeholder="Pedoman kriteria skor"><?= htmlspecialchars($soal['rubrik_penilaian'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center">
                    <a href="<?= BASE_URL ?>cbt_bank_soal/detail?id_bank=<?= $bank['id_bank'] ?>" class="btn btn-secondary btn-sm rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Butir Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
function loadTpForForm(idCp) {
    const tpSelect = $('#form_id_tp');
    tpSelect.html('<option value="">Memuat TP...</option>');

    if (!idCp) {
        tpSelect.html('<option value="">-- Pilih Tujuan Pembelajaran (TP) --</option>');
        return;
    }

    $.getJSON('api.php?mod=cptp&act=get_tp_by_cp&id_cp=' + idCp, function(res) {
        let opts = '<option value="">-- Pilih Tujuan Pembelajaran (TP) --</option>';
        if (res.status === 'ok' && res.data && res.data.length > 0) {
            res.data.forEach(tp => {
                const desc = tp.kode_tp ? (tp.kode_tp + ' - ' + tp.deskripsi_tp) : tp.deskripsi_tp;
                opts += `<option value="${tp.id_tp}" data-materi="${tp.materi || ''}">${desc}</option>`;
            });
        } else {
            opts = '<option value="">(Belum ada TP pada CP ini)</option>';
        }
        tpSelect.html(opts);
    }).fail(function() {
        tpSelect.html('<option value="">Gagal memuat TP</option>');
    });
}

function applyTpToForm(selectEl) {
    const opt = $(selectEl).find(':selected');
    const materi = opt.data('materi');
    if (materi) {
        $('#form_lingkup_materi').val(materi);
    }
}

function syncTipeSoal() {
    const tipe = $('#tipeSoalSelect').val();
    $('#sectionPG').hide();
    $('#sectionTF').hide();
    $('#sectionMatching').hide();

    if (tipe === 'pg') {
        $('#sectionPG').fadeIn(150);
    } else if (tipe === 'tf') {
        $('#sectionTF').fadeIn(150);
    } else if (tipe === 'matching') {
        $('#sectionMatching').fadeIn(150);
    }
}

$('#tipeSoalSelect').on('change', syncTipeSoal);
syncTipeSoal();
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
