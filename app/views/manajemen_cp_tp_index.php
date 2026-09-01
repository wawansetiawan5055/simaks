<?php include __DIR__ . '/partials/header.php'; ?>
<style>
  .card-header-actions .btn-tool {
    padding: 0.25rem 0.5rem;
    margin-left: 0.25rem;
  }
  .card-header-actions .btn-tool:hover {
    background-color: rgba(0,0,0,0.05);
  }
  .table-tp th {
    background-color: #f8f9fa;
    border-top: none;
  }
  .form-inline-tp .form-control {
    width: 100%;
  }
  .btn-gradient-ai {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
    border: none;
    color: #fff !important;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(139, 92, 246, 0.35);
  }
  .btn-gradient-ai:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #c026d3 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.5);
    color: #fff !important;
  }
  .bloom-option-card {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
    height: 100%;
    position: relative;
  }
  .bloom-option-card:hover {
    border-color: #a855f7;
    background: #faf5ff;
    transform: translateY(-2px);
  }
  .bloom-option-card.selected {
    border-color: #7c3aed;
    background: #f5f3ff;
    box-shadow: 0 0 0 1px #7c3aed;
  }
  .bloom-badge {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .badge-c1-c2 { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
  .badge-c3-c4 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
  .badge-c5-c6 { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
  .badge-berjenjang { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
  .ai-pulse {
    animation: pulse 1.8s infinite ease-in-out;
  }
  @keyframes pulse {
    0% { transform: scale(1); opacity: 0.9; }
    50% { transform: scale(1.04); opacity: 1; }
    100% { transform: scale(1); opacity: 0.9; }
  }
</style>
<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-list-alt"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Manajemen Capaian &amp; Tujuan Pembelajaran (CP &amp; TP)
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">CP &amp; TP</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm mb-4">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Filter Data Mata Pelajaran & Fase</h3>
      </div>
      <form method="GET">
        <input type="hidden" name="mod" value="manajemen_cp_tp">
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Mata Pelajaran</label>
              <select name="id_mapel" class="form-control" onchange="this.form.submit()">
                <?php foreach ($mapel_list as $m): ?>
                  <option value="<?= $m['id_mapel'] ?>" <?= ($id_mapel_filter == $m['id_mapel']) ? 'selected' : '' ?>>
                    <?= $m['nama_mapel'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Fase</label>
              <select name="fase" class="form-control" onchange="this.form.submit()">
                <option value="E" <?= ($fase_filter == 'E') ? 'selected' : '' ?>>E (Kelas X)</option>
                <option value="F" <?= ($fase_filter == 'F') ? 'selected' : '' ?>>F (Kelas XI & XII)</option>
              </select>
            </div>
          </div>
        </div>
      </form>
    </div>

    <?php if ($id_mapel_filter): ?>
      <div class="row mb-4">
        <div class="col-md-8">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
              <h3 class="card-title font-weight-bold text-success"><i class="fas fa-plus-circle mr-1"></i> Tambah Capaian Pembelajaran (CP)</h3>
            </div>
            <form action="<?= BASE_URL ?>manajemen_cp_tp/cp_save" method="POST">
              <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
              <input type="hidden" name="fase" value="<?= $fase_filter ?>">
              <div class="card-body py-3">
                <div class="form-group mb-0">
                  <textarea name="deskripsi_cp" class="form-control" rows="3" placeholder="Masukkan deskripsi Capaian Pembelajaran secara lengkap di sini..." required></textarea>
                </div>
              </div>
              <div class="card-footer bg-white border-0 text-right pb-3">
                <button type="submit" class="btn btn-success px-4"><i class="fas fa-save mr-1"></i> Simpan CP</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100 bg-light">
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
              <i class="fas fa-file-excel text-success mb-3" style="font-size: 3rem;"></i>
              <h5 class="font-weight-bold text-dark">Impor Data Excel</h5>
              <p class="text-muted small mb-3">Tambahkan CP dan TP secara masal menggunakan template Excel.</p>
              <button type="button" class="btn btn-secondary btn-block" data-toggle="modal" data-target="#modalImporCp">
                <i class="fa fa-upload mr-1"></i> Buka Form Impor
              </button>
            </div>
          </div>
        </div>
      </div>

      <?php if (empty($cp_list)): ?>
        <div class="alert alert-info shadow-sm">
          <i class="fas fa-info-circle mr-2"></i> Belum ada data Capaian Pembelajaran (CP) untuk Mata Pelajaran dan Fase yang dipilih.
        </div>
      <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
          <h4 class="m-0 font-weight-bold text-dark"><i class="fas fa-layer-group text-primary mr-2"></i> Daftar Capaian & Tujuan Pembelajaran</h4>
          <button type="button" class="btn btn-danger btn-sm shadow-sm" id="btn-bulk-delete-tp" style="display:none;" onclick="submitBulkDelete()">
            <i class="fa fa-trash mr-1"></i> Hapus TP Terpilih
          </button>
        </div>
        <form id="form-bulk-delete" action="<?= BASE_URL ?>manajemen_cp_tp/tp_delete_bulk" method="POST">
          <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
          <input type="hidden" name="fase" value="<?= $fase_filter ?>">
        </form>

        <?php foreach ($cp_list as $cp): ?>
          <div class="card card-outline card-secondary shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 pr-3">
                <h5 class="mb-0 text-dark font-weight-bold mb-2">CP:</h5>
                <p class="mb-0 text-dark" style="line-height: 1.6; font-size: 0.95rem;">
                  <?= nl2br(htmlspecialchars($cp['deskripsi_cp'])) ?>
                </p>
              </div>
              <div class="card-header-actions d-flex align-items-center mt-1 flex-shrink-0">
                <button type="button" class="btn btn-sm btn-gradient-ai mr-2 shadow-sm font-weight-bold px-3" 
                        onclick='openAiGenerateModal(<?= $cp['id_cp'] ?>, <?= json_encode($cp['deskripsi_cp']) ?>, <?= count($tp_data[$cp['id_cp']] ?? []) ?>)' 
                        title="Generate TP Otomatis dari CP ini menggunakan AI">
                  <i class="fas fa-magic mr-1"></i> Buat TP (AI)
                </button>
                <button type="button" class="btn btn-tool" onclick="editCp(<?= $cp['id_cp'] ?>, `<?= htmlspecialchars($cp['deskripsi_cp'], ENT_QUOTES) ?>`)" title="Edit CP">
                  <i class="fas fa-edit text-warning" style="font-size: 1.1rem;"></i>
                </button>
                <a href="<?= BASE_URL ?>manajemen_cp_tp/cp_delete?id=<?= $cp['id_cp'] ?>" class="btn btn-tool" onclick="return confirmDelete(event)" title="Hapus CP">
                  <i class="fas fa-trash-alt text-danger" style="font-size: 1.1rem;"></i>
                </a>
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus text-secondary" style="font-size: 1.1rem;"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="p-3 bg-white">
                <?php
                  $has_empty_topic = false;
                  if (!empty($tp_data[$cp['id_cp']])) {
                      foreach ($tp_data[$cp['id_cp']] as $tp_chk) {
                          if (empty(trim($tp_chk['materi'] ?? ''))) {
                              $has_empty_topic = true;
                              break;
                          }
                      }
                  }
                ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-bullseye mr-2"></i>Tujuan Pembelajaran (TP):</h6>
                  <?php if ($has_empty_topic): ?>
                    <button type="button" class="btn btn-xs btn-outline-primary shadow-sm font-weight-bold px-2 py-1" 
                            onclick="autoFillMissingTopics(<?= $cp['id_cp'] ?>, this)" 
                            title="Lengkapi nama Topik Materi untuk TP yang masih kosong secara otomatis dengan AI">
                      <i class="fas fa-magic mr-1"></i> Lengkapi Topik Materi (AI)
                    </button>
                  <?php endif; ?>
                </div>

                <?php if (!empty($tp_data[$cp['id_cp']])): ?>
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered table-tp mb-0">
                      <thead>
                        <tr>
                          <th width="40px" class="text-center align-middle">
                            <div class="icheck-danger d-inline">
                              <input type="checkbox" id="checkall_tp_<?= $cp['id_cp'] ?>" class="checkall-tp" data-idcp="<?= $cp['id_cp'] ?>">
                              <label for="checkall_tp_<?= $cp['id_cp'] ?>"></label>
                            </div>
                          </th>
                          <th width="110px" class="align-middle">Kode TP</th>
                          <th class="align-middle">Deskripsi Tujuan Pembelajaran</th>
                          <th width="220px" class="align-middle">Topik / Lingkup Materi</th>
                          <th width="90px" class="text-center align-middle">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($tp_data[$cp['id_cp']] as $tp): ?>
                          <tr>
                            <td class="text-center align-middle">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" value="<?= $tp['id_tp'] ?>" id="tp_checkbox_<?= $tp['id_tp'] ?>" class="tp-checkbox form-bulk-delete-checkbox" onchange="toggleBulkDeleteBtn()">
                                <label for="tp_checkbox_<?= $tp['id_tp'] ?>"></label>
                              </div>
                            </td>
                            <td class="align-middle font-weight-bold text-center">
                              <span class="badge badge-light border border-secondary p-2 w-100"><?= htmlspecialchars($tp['kode_tp']) ?></span>
                            </td>
                            <td class="align-middle"><?= nl2br(htmlspecialchars($tp['deskripsi_tp'])) ?></td>
                            <td class="align-middle">
                              <?php if (!empty($tp['materi'])): ?>
                                <span class="badge badge-info px-2 py-1 font-weight-normal text-wrap" style="font-size: 0.85rem; line-height: 1.3;">
                                  <i class="fas fa-tag mr-1"></i> <?= htmlspecialchars($tp['materi']) ?>
                                </span>
                              <?php else: ?>
                                <span class="text-muted small font-italic">- Belum diset -</span>
                              <?php endif; ?>
                            </td>
                            <td class="text-center align-middle">
                              <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                        onclick="editTp(<?= $tp['id_tp'] ?>, '<?= htmlspecialchars($tp['kode_tp'], ENT_QUOTES) ?>', `<?= htmlspecialchars($tp['materi'] ?? '', ENT_QUOTES) ?>`, `<?= htmlspecialchars($tp['deskripsi_tp'], ENT_QUOTES) ?>`)" 
                                        title="Edit TP">
                                  <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= BASE_URL ?>manajemen_cp_tp/tp_delete?id=<?= $tp['id_tp'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete(event)" title="Hapus TP">
                                  <i class="fa fa-trash"></i>
                                </a>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="alert alert-light border text-center text-muted mb-0">
                    Belum ada Tujuan Pembelajaran untuk CP ini.
                  </div>
                <?php endif; ?>
              </div>
              
              <div class="bg-light p-3 border-top">
                <form action="<?= BASE_URL ?>manajemen_cp_tp/tp_save" method="POST" class="form-inline-tp">
                  <input type="hidden" name="id_cp" value="<?= $cp['id_cp'] ?>">
                  <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
                  <input type="hidden" name="fase" value="<?= $fase_filter ?>">
                  <div class="row w-100 align-items-center m-0">
                    <div class="col-md-2 px-1 mb-2 mb-md-0">
                      <input type="text" name="kode_tp" class="form-control form-control-sm border-primary" placeholder="Kode TP (Cth: E.1.1)" required>
                    </div>
                    <div class="col-md-5 px-1 mb-2 mb-md-0">
                      <input type="text" name="deskripsi_tp" class="form-control form-control-sm border-primary" placeholder="Masukkan deskripsi TP baru..." required>
                    </div>
                    <div class="col-md-3 px-1 mb-2 mb-md-0">
                      <input type="text" name="materi" class="form-control form-control-sm border-primary" placeholder="Topik Materi (Opsional)">
                    </div>
                    <div class="col-md-2 px-1">
                      <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fas fa-plus mr-1"></i> Tambah TP</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<!-- MODAL EDIT CP -->
<div class="modal fade" id="modalEditCp">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h4 class="modal-title font-weight-bold text-dark"><i class="fas fa-edit mr-2"></i>Edit Capaian Pembelajaran (CP)</h4>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <form action="<?= BASE_URL ?>manajemen_cp_tp/cp_update" method="POST">
        <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
        <input type="hidden" name="fase" value="<?= $fase_filter ?>">
        <input type="hidden" name="id_cp" id="edit_id_cp">
        <div class="modal-body">
          <div class="form-group">
            <label>Deskripsi CP</label>
            <textarea name="deskripsi_cp" id="edit_deskripsi_cp" class="form-control" rows="5" required></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDIT TP -->
<div class="modal fade" id="modalEditTp">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h4 class="modal-title font-weight-bold text-dark"><i class="fas fa-edit mr-2"></i>Edit Tujuan Pembelajaran (TP)</h4>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <form action="<?= BASE_URL ?>manajemen_cp_tp/tp_update" method="POST">
        <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
        <input type="hidden" name="fase" value="<?= $fase_filter ?>">
        <input type="hidden" name="id_tp" id="edit_id_tp">
        <div class="modal-body">
          <div class="form-group">
            <label>Kode TP</label>
            <input type="text" name="kode_tp" id="edit_kode_tp" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Topik / Lingkup Materi</label>
            <input type="text" name="materi" id="edit_materi_tp" class="form-control" placeholder="Contoh: Pendapatan Nasional">
          </div>
          <div class="form-group">
            <label>Deskripsi TP</label>
            <textarea name="deskripsi_tp" id="edit_deskripsi_tp_val" class="form-control" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL IMPOR CP & TP -->
<div class="modal fade" id="modalImporCp">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-light">
        <h4 class="modal-title font-weight-bold text-dark"><i class="fas fa-upload text-success mr-2"></i>Impor CP & TP dari Excel</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="<?= BASE_URL ?>manajemen_cp_tp/cp_import" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
        <input type="hidden" name="fase" value="<?= $fase_filter ?>">
        <div class="modal-body p-4">
          <div class="d-flex justify-content-between align-items-center p-3 bg-white border rounded mb-4 shadow-sm">
            <div>
              <label class="font-weight-bold text-secondary mb-2">Upload File Excel (.xls, .xlsx)</label>
              <input type="file" name="file_excel" class="form-control-file" accept=".xls,.xlsx" required>
            </div>
            <a href="<?= BASE_URL ?>manajemen_cp_tp/download_template" class="btn btn-outline-success font-weight-bold">
              <i class="fas fa-download mr-1"></i> Unduh Template
            </a>
          </div>

          <div class="alert alert-info border-info bg-light text-dark">
            <h6 class="font-weight-bold"><i class="fa fa-info-circle text-info mr-1"></i> Panduan Format Excel</h6>
            <hr>
            <div class="table-responsive bg-white mt-2 rounded border">
              <table class="table table-sm table-bordered m-0 text-sm">
                <thead class="bg-light text-secondary">
                  <tr>
                    <th class="text-center">KOLOM A<br><small>DESKRIPSI CP</small></th>
                    <th class="text-center">KOLOM B<br><small>KODE TP</small></th>
                    <th class="text-center">KOLOM C<br><small>DESKRIPSI TP</small></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Deskripsi CP 1</td>
                    <td class="text-center">E.1.1</td>
                    <td>Deskripsi TP 1.1</td>
                  </tr>
                  <tr>
                    <td class="text-center text-muted"><em>(Kosongkan)</em></td>
                    <td class="text-center">E.1.2</td>
                    <td>Deskripsi TP 1.2</td>
                  </tr>
                  <tr>
                    <td>Deskripsi CP 2</td>
                    <td class="text-center">E.2.1</td>
                    <td>Deskripsi TP 2.1</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-3 text-sm">
              <p class="mb-1"><strong>Aturan Pengisian:</strong></p>
              <ul class="pl-3 mb-0">
                <li>Kolom <strong>A</strong> diisi dengan deskripsi CP. Jika kosong, berarti TP tersebut milik CP di atasnya.</li>
                <li>Kolom <strong>B</strong> diisi dengan Kode TP (contoh: E.1.1)</li>
                <li>Kolom <strong>C</strong> diisi dengan Deskripsi TP</li>
                <li>Baris header pada baris pertama Excel akan diabaikan otomatis.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-success px-4"><i class="fa fa-cloud-upload-alt mr-1"></i> Mulai Impor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL GENERATE TP DENGAN AI (GEMINI) -->
<div class="modal fade" id="modalAiGenerateTp" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
      
      <!-- Modal Header -->
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #9333ea 100%); padding: 16px 24px;">
        <div class="d-flex align-items-center">
          <div class="mr-3 bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-size: 1.3rem;">
            <i class="fas fa-robot text-purple"></i>
          </div>
          <div>
            <h5 class="modal-title font-weight-bold mb-0">Asisten AI: Perumusan Tujuan Pembelajaran (TP)</h5>
            <small class="text-light opacity-90">Otomatisasi perumusan TP dari Capaian Pembelajaran berbasis Taksonomi Bloom & Kurikulum Merdeka</small>
          </div>
        </div>
        <button type="button" class="close text-white opacity-90" data-dismiss="modal" aria-label="Close" style="font-size: 1.6rem;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-4 bg-light">
        
        <!-- Info Konteks CP -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
          <div class="card-body p-3 bg-white" style="border-left: 5px solid #6366f1; border-radius: 10px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="badge badge-primary px-2 py-1" id="ai_modal_mapel_badge">
                <i class="fas fa-book mr-1"></i> <span id="ai_modal_mapel_text">Mata Pelajaran</span> - Fase <?= htmlspecialchars($fase_filter) ?>
              </span>
              <span class="badge badge-light border text-muted px-2 py-1" id="ai_modal_cp_id_badge">
                ID CP: #<span id="ai_modal_cp_id_text">0</span>
              </span>
            </div>
            <h6 class="font-weight-bold text-dark mb-1">Capaian Pembelajaran (CP) yang Dipilih:</h6>
            <p class="mb-0 text-secondary" id="ai_modal_cp_desc" style="font-size: 0.92rem; line-height: 1.5;"></p>
          </div>
        </div>

        <!-- Form Konfigurasi Kriteria AI -->
        <div id="ai_form_section" class="card shadow-sm border-0 mb-3" style="border-radius: 10px;">
          <div class="card-body p-4 bg-white" style="border-radius: 10px;">
            
            <input type="hidden" id="ai_target_id_cp" value="0">
            <input type="hidden" id="ai_target_id_mapel" value="<?= $id_mapel_filter ?>">
            <input type="hidden" id="ai_target_fase" value="<?= $fase_filter ?>">

            <!-- 1. Kriteria Taksonomi Bloom -->
            <label class="font-weight-bold text-dark mb-2">
              <i class="fas fa-layer-group text-primary mr-1"></i> 1. Kriteria Tingkat Kognitif (Taksonomi Bloom):
            </label>
            <div class="row mb-4">
              
              <!-- Pilihan Berjenjang (Default) -->
              <div class="col-md-6 col-lg-3 mb-2 mb-lg-0">
                <div class="bloom-option-card selected" onclick="selectBloomOption(this, 'berjenjang')">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="bloom-badge badge-berjenjang"><i class="fas fa-star mr-1"></i> Berjenjang</span>
                    <input type="radio" name="ai_kriteria_bloom" value="berjenjang" checked style="accent-color: #7c3aed;">
                  </div>
                  <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.88rem;">C1 s.d C6 (Bertahap)</h6>
                  <p class="text-muted small mb-0" style="line-height: 1.3;">Progresif dari dasar pemahaman hingga aplikasi & kreasi HOTS.</p>
                </div>
              </div>

              <!-- Pilihan C1 - C2 -->
              <div class="col-md-6 col-lg-3 mb-2 mb-lg-0">
                <div class="bloom-option-card" onclick="selectBloomOption(this, 'c1_c2')">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="bloom-badge badge-c1-c2">LOTS</span>
                    <input type="radio" name="ai_kriteria_bloom" value="c1_c2" style="accent-color: #0284c7;">
                  </div>
                  <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.88rem;">C1 – C2 (Mengingat & Memahami)</h6>
                  <p class="text-muted small mb-0" style="line-height: 1.3;">KKO: Mengidentifikasi, menjelaskan, menyebutkan, menguraikan.</p>
                </div>
              </div>

              <!-- Pilihan C3 - C4 -->
              <div class="col-md-6 col-lg-3 mb-2 mb-lg-0">
                <div class="bloom-option-card" onclick="selectBloomOption(this, 'c3_c4')">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="bloom-badge badge-c3-c4">MOTS</span>
                    <input type="radio" name="ai_kriteria_bloom" value="c3_c4" style="accent-color: #d97706;">
                  </div>
                  <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.88rem;">C3 – C4 (Menerapkan & Analisis)</h6>
                  <p class="text-muted small mb-0" style="line-height: 1.3;">KKO: Menerapkan, mengimplementasikan, menganalisis, membedakan.</p>
                </div>
              </div>

              <!-- Pilihan C5 - C6 -->
              <div class="col-md-6 col-lg-3 mb-2 mb-lg-0">
                <div class="bloom-option-card" onclick="selectBloomOption(this, 'c5_c6')">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="bloom-badge badge-c5-c6">HOTS</span>
                    <input type="radio" name="ai_kriteria_bloom" value="c5_c6" style="accent-color: #dc2626;">
                  </div>
                  <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.88rem;">C5 – C6 (Evaluasi & Mencipta)</h6>
                  <p class="text-muted small mb-0" style="line-height: 1.3;">KKO: Mengevaluasi, menilai, merancang, mengkonstruksi, memproduksi.</p>
                </div>
              </div>

            </div>

            <!-- 2. Jumlah TP & Prefix Penomoran -->
            <div class="row align-items-end">
              <div class="col-md-4 mb-3 mb-md-0">
                <label class="font-weight-bold text-dark mb-1">
                  <i class="fas fa-sort-numeric-down text-primary mr-1"></i> 2. Jumlah TP yang Ingin Dibuat:
                </label>
                <select id="ai_jumlah_tp" class="form-control font-weight-bold text-primary">
                  <option value="2">2 Butir Tujuan Pembelajaran</option>
                  <option value="3" selected>3 Butir Tujuan Pembelajaran (Standar)</option>
                  <option value="4">4 Butir Tujuan Pembelajaran</option>
                  <option value="5">5 Butir Tujuan Pembelajaran</option>
                  <option value="6">6 Butir Tujuan Pembelajaran (Lengkap)</option>
                </select>
              </div>

              <div class="col-md-4 mb-3 mb-md-0">
                <label class="font-weight-bold text-dark mb-1">
                  <i class="fas fa-tag text-primary mr-1"></i> 3. Format / Prefix Kode TP:
                </label>
                <input type="text" id="ai_prefix_kode" class="form-control" placeholder="Cth: <?= htmlspecialchars($fase_filter) ?>.1.">
                <small class="text-muted">Otomatis melanjutkan nomor TP yang sudah ada.</small>
              </div>

              <div class="col-md-4">
                <button type="button" class="btn btn-gradient-ai btn-block py-2 font-weight-bold shadow" id="btn_trigger_ai" onclick="submitGenerateAiTp()">
                  <i class="fas fa-magic mr-2"></i> Rumuskan TP dengan AI
                </button>
              </div>
            </div>

          </div>
        </div>

        <!-- State Loading AI -->
        <div id="ai_loading_section" class="card shadow-sm border-0 mb-3 text-center p-5 bg-white" style="display:none; border-radius: 10px;">
          <div class="py-3">
            <div class="spinner-border text-primary ai-pulse" style="width: 3.5rem; height: 3.5rem;" role="status">
              <span class="sr-only">Loading...</span>
            </div>
            <h5 class="font-weight-bold text-dark mt-4 mb-2">Google Gemini AI Sedang Menganalisis...</h5>
            <p class="text-secondary small mb-0" id="ai_loading_status_text">
              Sedang membedah Capaian Pembelajaran dan merumuskan Tujuan Pembelajaran operasional sesuai Taksonomi Bloom...
            </p>
          </div>
        </div>

        <!-- Hasil Generator (Preview Table) -->
        <div id="ai_result_section" class="card shadow-sm border-0 mb-0" style="display:none; border-radius: 10px;">
          <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <div>
              <h6 class="font-weight-bold text-success mb-0">
                <i class="fas fa-check-circle mr-1"></i> Hasil Rumusan AI - Preview & Verifikasi
              </h6>
              <small class="text-muted">Anda dapat mencentang TP yang ingin disimpan dan mengedit langsung teks deskripsi bila diperlukan.</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetAiForm()">
              <i class="fas fa-redo mr-1"></i> Generate Ulang
            </button>
          </div>
          
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover table-bordered mb-0" id="ai_tp_preview_table">
                <thead class="bg-light text-secondary">
                  <tr>
                    <th width="45px" class="text-center align-middle">
                      <div class="icheck-primary d-inline">
                        <input type="checkbox" id="checkall_ai_tp" checked onchange="toggleCheckAllAiTp(this)">
                        <label for="checkall_ai_tp"></label>
                      </div>
                    </th>
                    <th width="110px" class="align-middle">Kode TP</th>
                    <th class="align-middle">Deskripsi Tujuan Pembelajaran (Dapat Diedit)</th>
                    <th width="220px" class="align-middle">Topik / Lingkup Materi</th>
                  </tr>
                </thead>
                <tbody id="ai_tp_preview_tbody">
                  <!-- Dynamic rows will be inserted here -->
                </tbody>
              </table>
            </div>
          </div>

          <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
              <span id="ai_selected_count_text">0</span> TP dipilih untuk disimpan.
            </div>
            <button type="button" class="btn btn-success px-4 font-weight-bold shadow-sm" id="btn_save_bulk_ai" onclick="saveBulkAiTp()">
              <i class="fas fa-save mr-1"></i> Simpan TP Terpilih ke Database
            </button>
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>

<script>
  function editCp(id, deskripsi) {
    document.getElementById('edit_id_cp').value = id;
    document.getElementById('edit_deskripsi_cp').value = deskripsi;
    $('#modalEditCp').modal('show');
  }

  function editTp(id, kode, materi, deskripsi) {
    document.getElementById('edit_id_tp').value = id;
    document.getElementById('edit_kode_tp').value = kode;
    document.getElementById('edit_materi_tp').value = materi || '';
    document.getElementById('edit_deskripsi_tp_val').value = deskripsi;
    $('#modalEditTp').modal('show');
  }

  function toggleBulkDeleteBtn() {
    const checkedBoxes = document.querySelectorAll('.tp-checkbox:checked');
    const bulkBtn = document.getElementById('btn-bulk-delete-tp');
    if (checkedBoxes.length > 0) {
      bulkBtn.style.display = 'inline-block';
    } else {
      bulkBtn.style.display = 'none';
    }
  }

  document.querySelectorAll('.checkall-tp').forEach(checkall => {
    checkall.addEventListener('change', function () {
      const table = this.closest('table');
      const checkboxes = table.querySelectorAll('.tp-checkbox');
      checkboxes.forEach(cb => {
        cb.checked = this.checked;
      });
      toggleBulkDeleteBtn();
    });
  });

  function submitBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.tp-checkbox:checked');
    if (checkedBoxes.length === 0) return;

    if (confirm('Apakah Anda yakin ingin menghapus ' + checkedBoxes.length + ' Tujuan Pembelajaran (TP) yang dipilih?')) {
      const form = document.getElementById('form-bulk-delete');
      checkedBoxes.forEach(cb => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'tp_ids[]';
        hidden.value = cb.value;
        form.appendChild(hidden);
      });
      form.submit();
    }
  }

  // =========================================================
  // AI GENERATOR TP JAVASCRIPT HANDLERS
  // =========================================================

  function selectBloomOption(cardElement, value) {
    document.querySelectorAll('.bloom-option-card').forEach(el => el.classList.remove('selected'));
    cardElement.classList.add('selected');
    const radio = cardElement.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
  }

  function openAiGenerateModal(id_cp, deskripsi_cp, existing_count) {
    document.getElementById('ai_target_id_cp').value = id_cp;
    document.getElementById('ai_modal_cp_id_text').innerText = id_cp;
    document.getElementById('ai_modal_cp_desc').innerText = deskripsi_cp;
    
    // Set mapel text
    const mapelSelect = document.querySelector('select[name="id_mapel"]');
    const mapelName = mapelSelect ? mapelSelect.options[mapelSelect.selectedIndex].text : 'Mata Pelajaran';
    document.getElementById('ai_modal_mapel_text').innerText = mapelName;

    // Suggest default prefix kode (e.g. E.1.)
    const fase = "<?= htmlspecialchars($fase_filter) ?>";
    document.getElementById('ai_prefix_kode').value = fase + '.1.';

    // Reset view
    resetAiForm();

    $('#modalAiGenerateTp').modal('show');
  }

  function resetAiForm() {
    document.getElementById('ai_form_section').style.display = 'block';
    document.getElementById('ai_loading_section').style.display = 'none';
    document.getElementById('ai_result_section').style.display = 'none';
    document.getElementById('btn_trigger_ai').disabled = false;
  }

  function submitGenerateAiTp() {
    const id_cp = document.getElementById('ai_target_id_cp').value;
    const id_mapel = document.getElementById('ai_target_id_mapel').value;
    const fase = document.getElementById('ai_target_fase').value;
    const kriteriaRadio = document.querySelector('input[name="ai_kriteria_bloom"]:checked');
    const kriteria_bloom = kriteriaRadio ? kriteriaRadio.value : 'berjenjang';
    const jumlah_tp = document.getElementById('ai_jumlah_tp').value;
    const prefix_kode = document.getElementById('ai_prefix_kode').value;

    if (!id_cp || id_cp === '0') {
      alert('Silakan pilih CP terlebih dahulu.');
      return;
    }

    // Switch to loading
    document.getElementById('ai_form_section').style.display = 'none';
    document.getElementById('ai_loading_section').style.display = 'block';
    document.getElementById('ai_result_section').style.display = 'none';

    const formData = new FormData();
    formData.append('id_cp', id_cp);
    formData.append('id_mapel', id_mapel);
    formData.append('fase', fase);
    formData.append('kriteria_bloom', kriteria_bloom);
    formData.append('jumlah_tp', jumlah_tp);
    formData.append('prefix_kode', prefix_kode);

    fetch('<?= BASE_URL ?>index.php?mod=manajemen_cp_tp&act=ai_generate_tp', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      document.getElementById('ai_loading_section').style.display = 'none';

      if (!data.success) {
        alert('Gagal: ' + (data.message || 'Terjadi kesalahan saat memproses ke AI.'));
        document.getElementById('ai_form_section').style.display = 'block';
        return;
      }

      renderAiTpResult(data.tp_list);
    })
    .catch(err => {
      document.getElementById('ai_loading_section').style.display = 'none';
      document.getElementById('ai_form_section').style.display = 'block';
      alert('Koneksi ke server gagal: ' + err);
    });
  }

  function renderAiTpResult(tpList) {
    const tbody = document.getElementById('ai_tp_preview_tbody');
    tbody.innerHTML = '';

    if (!tpList || tpList.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada TP yang dihasilkan. Silakan coba lagi.</td></tr>';
      document.getElementById('ai_result_section').style.display = 'block';
      return;
    }

    tpList.forEach((tp, idx) => {
      const row = document.createElement('tr');

      row.innerHTML = `
        <td class="text-center align-middle">
          <div class="icheck-primary d-inline">
            <input type="checkbox" id="ai_tp_check_${idx}" class="ai-tp-check" value="${idx}" checked onchange="updateSelectedAiCount()">
            <label for="ai_tp_check_${idx}"></label>
          </div>
        </td>
        <td class="align-middle">
          <input type="text" class="form-control form-control-sm font-weight-bold ai-tp-kode text-center" value="${escapeHtml(tp.kode_tp)}" required>
        </td>
        <td class="align-middle">
          <textarea class="form-control form-control-sm ai-tp-desc" rows="2" required>${escapeHtml(tp.deskripsi_tp)}</textarea>
        </td>
        <td class="align-middle">
          <input type="text" class="form-control form-control-sm font-weight-bold text-primary ai-tp-materi" value="${escapeHtml(tp.materi || '')}" placeholder="Topik Materi (Cth: Inflasi)">
        </td>
      `;
      tbody.appendChild(row);
    });

    document.getElementById('ai_result_section').style.display = 'block';
    updateSelectedAiCount();
  }

  function toggleCheckAllAiTp(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.ai-tp-check');
    checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
    updateSelectedAiCount();
  }

  function updateSelectedAiCount() {
    const count = document.querySelectorAll('.ai-tp-check:checked').length;
    document.getElementById('ai_selected_count_text').innerText = count;
    document.getElementById('btn_save_bulk_ai').disabled = (count === 0);
  }

  function saveBulkAiTp() {
    const id_cp = document.getElementById('ai_target_id_cp').value;
    const id_mapel = document.getElementById('ai_target_id_mapel').value;
    const rows = document.querySelectorAll('#ai_tp_preview_tbody tr');

    const tp_items = [];
    rows.forEach(row => {
      const checkbox = row.querySelector('.ai-tp-check');
      if (checkbox && checkbox.checked) {
        const kode = row.querySelector('.ai-tp-kode').value.trim();
        const materi = row.querySelector('.ai-tp-materi').value.trim();
        const desc = row.querySelector('.ai-tp-desc').value.trim();
        if (kode && desc) {
          tp_items.push({
            kode_tp: kode,
            materi: materi,
            deskripsi_tp: desc
          });
        }
      }
    });

    if (tp_items.length === 0) {
      alert('Pilih setidaknya 1 Tujuan Pembelajaran untuk disimpan.');
      return;
    }

    const saveBtn = document.getElementById('btn_save_bulk_ai');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

    const formData = new FormData();
    formData.append('id_cp', id_cp);
    formData.append('id_mapel', id_mapel);
    tp_items.forEach((item, i) => {
      formData.append(`tp_items[${i}][kode_tp]`, item.kode_tp);
      formData.append(`tp_items[${i}][materi]`, item.materi);
      formData.append(`tp_items[${i}][deskripsi_tp]`, item.deskripsi_tp);
    });

    fetch('<?= BASE_URL ?>index.php?mod=manajemen_cp_tp&act=ai_save_bulk', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Sukses! Reload halaman untuk menampilkan data baru
        window.location.reload();
      } else {
        alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan.'));
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan TP Terpilih ke Database';
      }
    })
    .catch(err => {
      alert('Koneksi gagal: ' + err);
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan TP Terpilih ke Database';
    });
  }

  // Lengkapi otomatis topik materi untuk TP lama yang belum memiliki topik
  function autoFillMissingTopics(id_cp, btnElement) {
    if (!confirm('Apakah Anda ingin AI menganalisis dan mengisi nama Topik Materi untuk semua TP yang masih kosong pada CP ini?')) {
      return;
    }

    const originalHtml = btnElement.innerHTML;
    btnElement.disabled = true;
    btnElement.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> AI Sedang Menganalisis...';

    const formData = new FormData();
    formData.append('id_cp', id_cp);

    fetch('<?= BASE_URL ?>index.php?mod=manajemen_cp_tp&act=ai_generate_missing_topics', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert('Info: ' + (data.message || 'Gagal melengkapi topik materi.'));
        btnElement.disabled = false;
        btnElement.innerHTML = originalHtml;
      }
    })
    .catch(err => {
      alert('Koneksi gagal: ' + err);
      btnElement.disabled = false;
      btnElement.innerHTML = originalHtml;
    });
  }

  function escapeHtml(text) {
    if (!text) return '';
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>