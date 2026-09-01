<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (VALIDASI PERUBAHAN DATA SISWA)     */
    /* ============================================================ */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .content-header {
            padding: 8px 4px 2px !important;
        }
        .content-header h4 {
            font-size: 0.90rem !important;
        }
        .card {
            border-radius: 10px !important;
            margin-bottom: 8px !important;
        }
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border: none;
        }
        .table th {
            padding: 6px 8px !important;
            font-size: 0.65rem !important;
            white-space: nowrap;
        }
        .table td {
            padding: 6px 8px !important;
            font-size: 0.70rem !important;
        }
        .btn-group .btn {
            padding: 3px 6px !important;
            font-size: 0.65rem !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-clipboard-check"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Validasi Perubahan Data Siswa
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <a href="<?= BASE_URL ?>siswa" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3 shadow-sm">
          <i class="fas fa-arrow-left mr-1"></i> Kembali ke Data Siswa
        </a>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <?php if (isset($_SESSION['pesan_sukses'])): ?>
        <div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['pesan_error'])): ?>
        <div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm align-middle mb-0" style="border-collapse: collapse;">
                    <thead style="background: #f8fafc;">
                        <tr class="text-muted">
                            <th class="text-center py-2 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                            <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 150px;">TANGGAL</th>
                            <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">SISWA</th>
                            <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">KATEGORI</th>
                            <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">DETAIL PENGAJUAN</th>
                            <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 180px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($pengajuan_menunggu)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted small"><em>Tidak ada pengajuan yang menunggu validasi.</em></td>
                        </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($pengajuan_menunggu as $p): ?>
                            <tr>
                                <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++ ?></td>
                                <td class="align-middle small"><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
                                <td class="align-middle">
                                    <span class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($p['nama']) ?></span><br>
                                    <code class="text-muted small"><?= htmlspecialchars($p['nisn']) ?></code>
                                </td>
                                <td class="align-middle small">
                                    <span class="badge badge-info px-2 py-1"><?= htmlspecialchars($p['kategori']) ?></span>
                                </td>
                                <td class="align-middle small">
                                    <?php 
                                        $data_ubah = json_decode($p['data_perubahan'], true);
                                        if(is_array($data_ubah)) {
                                            if (isset($data_ubah['jenis_berkas']) && isset($data_ubah['file_temp'])) {
                                                echo "<div class='small mb-1'><b>Berkas:</b> " . str_replace('file_', '', $data_ubah['jenis_berkas']) . "</div>";
                                                echo "<button type='button' class='btn btn-xs btn-info mt-1' onclick=\"previewFile('" . BASE_URL . "uploads/siswa/" . htmlspecialchars($data_ubah['file_temp']) . "')\"><i class='fas fa-eye'></i> Lihat File</button>";
                                            } else {
                                                echo "<ul class='mb-0 pl-3'>";
                                                foreach($data_ubah as $k => $v) {
                                                    if(!empty($v)) {
                                                        echo "<li><span class='text-primary font-weight-bold'>".str_replace('_', ' ', ucfirst($k))."</span>: <br> <i class='fas fa-arrow-right text-success mr-1'></i>".htmlspecialchars($v)."</li>";
                                                    }
                                                }
                                                echo "</ul>";
                                            }
                                        }
                                    ?>
                                </td>
                                <td class="text-center align-middle">
                                    <form action="<?= BASE_URL ?>siswa/acc_pengajuan" method="post" class="d-inline">
                                        <input type="hidden" name="id_pengajuan" value="<?= $p['id_pengajuan'] ?>">
                                        
                                        <button type="button" class="btn btn-xs btn-success px-2 py-1 shadow-sm font-weight-bold" onclick="showCatatan(this, 'setuju')" title="Setujui Perubahan">
                                            <i class="fas fa-check"></i> ACC
                                        </button>
                                        <button type="button" class="btn btn-xs btn-danger px-2 py-1 shadow-sm font-weight-bold" onclick="showCatatan(this, 'tolak')" title="Tolak Perubahan">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>

                                        <!-- Hidden Input for Form Submission -->
                                        <input type="hidden" name="action" class="action-input">
                                        <input type="hidden" name="catatan_admin" class="catatan-input">
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
</section>

<!-- Modal Catatan -->
<div class="modal fade" id="modalCatatan" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCatatanTitle">Catatan Validasi</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
          <p id="modalCatatanDesc" class="small text-muted"></p>
          <textarea id="catatanText" class="form-control" rows="3" placeholder="Tulis catatan (opsional)..."></textarea>
      </div>
      <div class="modal-footer border-0">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="btnProses">Proses</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Preview Berkas -->
<div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl" style="max-width:90%; height:90%;">
        <div class="modal-content" style="height:100%;">
            <div class="modal-header">
                <h5 class="modal-title">Preview Berkas</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0" style="height:calc(100% - 60px);">
                <iframe src="" id="filePreviewFrame" style="width:100%; height:100%; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    let currentForm = null;
    let currentAction = null;

    function previewFile(url) {
        $('#filePreviewFrame').attr('src', url);
        $('#filePreviewModal').modal('show');
    }

    function showCatatan(btn, action) {
        currentForm = $(btn).closest('form');
        currentAction = action;
        
        if (action === 'setuju') {
            $('#modalCatatanTitle').text('Setujui Pengajuan');
            $('#modalCatatanDesc').text('Data ini akan secara otomatis memperbarui database profil siswa.');
            $('#btnProses').removeClass('btn-danger').addClass('btn-success').text('Setujui Sekarang');
        } else {
            $('#modalCatatanTitle').text('Tolak Pengajuan');
            $('#modalCatatanDesc').text('Berikan alasan penolakan agar siswa dapat memperbaikinya.');
            $('#btnProses').removeClass('btn-success').addClass('btn-danger').text('Tolak Pengajuan');
        }
        
        $('#catatanText').val('');
        $('#modalCatatan').modal('show');
    }

    $('#btnProses').click(function() {
        if (currentForm) {
            currentForm.find('.action-input').val(currentAction);
            currentForm.find('.catatan-input').val($('#catatanText').val());
            currentForm.submit();
        }
    });
</script>
