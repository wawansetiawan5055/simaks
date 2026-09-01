<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-print"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Pusat Cetak Rapor &amp; Kelulusan Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Cetak Rapor</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Flash message -->
        <?php if (!empty($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?></div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <div class="card card-outline card-primary shadow-sm mb-3">
            <div class="card-body p-2">
                <form method="GET" id="filterForm" class="form-inline d-flex justify-content-center align-items-center flex-wrap">
                    <input type="hidden" name="mod" value="cetak_rapor">

                    <span class="mr-3 font-weight-bold"><i class="fas fa-filter text-primary mr-1"></i> Filter:</span>

                    <div class="form-group mx-2">
                        <label class="mr-2">Kelas:</label>
                        <select name="id_kelas" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width: 180px;">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($list_kelas as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= $id_kelas == $k['id_kelas'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mx-2">
                        <label class="mr-2">Semester:</label>
                        <select name="semester" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="1" <?= $semester == 1 ? 'selected' : '' ?>>Semester 1 (Ganjil)</option>
                            <option value="2" <?= $semester == 2 ? 'selected' : '' ?>>Semester 2 (Genap)</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($id_kelas && !empty($siswa)): ?>
        <!-- Action Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-users mr-2 text-primary"></i>
                Daftar Siswa — <strong><?= htmlspecialchars($nama_kelas) ?></strong>
                <span class="badge badge-secondary ml-2">Semester <?= $semester ?></span>
                <span class="badge badge-info ml-1"><?= count($siswa) ?> Siswa</span>
            </h5>
            <div>
                <a href="<?= BASE_URL ?>cetak_rapor/batch?id_kelas=<?= $id_kelas ?>&semester=<?= $semester ?>"
                   target="_blank" class="btn btn-success">
                    <i class="fas fa-print mr-1"></i> Cetak Semua (Batch)
                </a>
            </div>
        </div>

        <!-- Student Table -->
        <div class="card card-outline card-success shadow">
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width:50px;">No</th>
                            <th>Nama Siswa</th>
                            <th>NIS / NISN</th>
                            <th class="text-center">Status Catatan</th>
                            <th class="text-center" style="width:280px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $i => $s): ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td class="font-weight-bold"><?= htmlspecialchars($s['nama']) ?></td>
                            <td class="small text-muted"><?= htmlspecialchars($s['nipd']) ?> / <?= htmlspecialchars($s['nisn']) ?></td>
                            <td class="text-center">
                                <?php if ($s['has_catatan']): ?>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Sudah Ada</span>
                                    <?php if ($s['is_generated']): ?>
                                        <span class="badge badge-info ml-1">Auto</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i> Belum Ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-outline-info mr-1"
                                        onclick="openCatatanModal(<?= $s['id_penempatan'] ?>, '<?= htmlspecialchars(addslashes($s['nama'])) ?>', <?= $semester ?>)">
                                    <i class="fas fa-edit"></i> Catatan
                                </button>
                                <button class="btn btn-xs btn-outline-primary mr-1"
                                        onclick="openPreviewModal('<?= BASE_URL ?>cetak_rapor/preview?id_penempatan=<?= $s['id_penempatan'] ?>&semester=<?= $semester ?>')"
                                        title="Preview Rapor">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                                <a href="<?= BASE_URL ?>cetak_rapor/preview?id_penempatan=<?= $s['id_penempatan'] ?>&semester=<?= $semester ?>&print=1"
                                   target="_blank" class="btn btn-xs btn-success">
                                    <i class="fas fa-print"></i> Cetak
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($id_kelas && empty($siswa)): ?>
            <div class="alert alert-warning"><i class="fas fa-info-circle mr-2"></i> Tidak ada siswa aktif di kelas ini.</div>
        <?php else: ?>
            <div class="alert alert-info"><i class="fas fa-arrow-up mr-2"></i> Pilih kelas untuk menampilkan daftar siswa.</div>
        <?php endif; ?>

    </div>
</section>

<!-- Modal Preview Rapor -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 90%; width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-eye mr-2"></i> Preview Rapor</h5>
                <div class="ml-auto d-flex align-items-center">
                    <button type="button" id="btnPrintFromModal" class="btn btn-sm btn-success mr-2" onclick="printFromModal()">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                    <button type="button" class="close text-white ml-2" data-dismiss="modal"><span>&times;</span></button>
                </div>
            </div>
            <div class="modal-body p-0" style="height: 85vh;">
                <div id="previewLoading" class="d-flex justify-content-center align-items-center" style="height:100%;">
                    <div class="text-center text-muted">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <div>Memuat rapor...</div>
                    </div>
                </div>
                <iframe id="previewIframe" src="" style="width:100%; height:100%; border:none; display:none;" onload="iframeLoaded()"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catatan Wali Kelas -->
<div class="modal fade" id="catatanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Catatan Wali Kelas — <span id="modalNamaSiswa"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2">
                    <i class="fas fa-magic mr-1"></i>
                    Klik <strong>"Generate Otomatis"</strong> untuk membuat narasi berdasarkan data nilai, sikap, kehadiran, dan ekskul.
                    Anda bisa mengedit hasilnya sebelum disimpan.
                </div>
                <input type="hidden" id="catatanIdPenempatan">
                <input type="hidden" id="catatanSemester">
                <div class="form-group">
                    <label><strong>Narasi Catatan Wali Kelas:</strong></label>
                    <textarea id="catatanText" class="form-control" rows="5" placeholder="Isi catatan wali kelas..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info mr-auto" onclick="generateCatatan()" id="btnGenerate">
                    <i class="fas fa-magic mr-1"></i> Generate Otomatis
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveCatatan()">
                    <i class="fas fa-save mr-1"></i> Simpan Catatan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var currentPreviewUrl = '';

function openPreviewModal(url) {
    currentPreviewUrl = url;
    var iframe = document.getElementById('previewIframe');
    var loading = document.getElementById('previewLoading');
    iframe.style.display = 'none';
    loading.style.display = 'flex';
    iframe.src = url;
    $('#previewModal').modal('show');
}

function iframeLoaded() {
    document.getElementById('previewLoading').style.display = 'none';
    document.getElementById('previewIframe').style.display = 'block';
}

function printFromModal() {
    window.open(currentPreviewUrl + '&print=1', '_blank');
}

function openCatatanModal(idPenempatan, nama, semester) {
    document.getElementById('catatanIdPenempatan').value = idPenempatan;
    document.getElementById('catatanSemester').value = semester;
    document.getElementById('modalNamaSiswa').textContent = nama;
    document.getElementById('catatanText').value = '';
    $('#catatanModal').modal('show');

    // Load existing catatan via AJAX if exists
    fetch(`<?= BASE_URL ?>cetak_rapor/get_catatan?id_penempatan=${idPenempatan}&semester=${semester}`)
        .then(r => r.json())
        .then(d => {
            if (d.catatan) document.getElementById('catatanText').value = d.catatan;
        }).catch(() => {});
}

function generateCatatan() {
    const id = document.getElementById('catatanIdPenempatan').value;
    const sem = document.getElementById('catatanSemester').value;
    const btn = document.getElementById('btnGenerate');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Generating...';
    btn.disabled = true;

    fetch(`<?= BASE_URL ?>cetak_rapor/generate?id_penempatan=${id}&semester=${sem}`)
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('catatanText').value = d.catatan;
            } else {
                alert('Gagal generate catatan. Pastikan data nilai sudah lengkap.');
            }
        })
        .catch(() => alert('Terjadi kesalahan koneksi.'))
        .finally(() => {
            btn.innerHTML = '<i class="fas fa-magic mr-1"></i> Generate Otomatis';
            btn.disabled = false;
        });
}

function saveCatatan() {
    const id       = document.getElementById('catatanIdPenempatan').value;
    const sem      = document.getElementById('catatanSemester').value;
    const catatan  = document.getElementById('catatanText').value.trim();

    if (!catatan) { alert('Catatan tidak boleh kosong.'); return; }

    fetch('<?= BASE_URL ?>cetak_rapor/save_catatan', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_penempatan=${id}&semester=${sem}&catatan=${encodeURIComponent(catatan)}&is_generated=0`
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            $('#catatanModal').modal('hide');
            location.reload();
        } else {
            alert('Gagal menyimpan catatan.');
        }
    });
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
