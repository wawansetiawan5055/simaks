<?php include __DIR__.'/partials/header.php'; ?>

<style>
.table-nilai th, .table-nilai td { vertical-align: middle !important; }
.select-predikat { 
    border-radius: 4px; 
    padding: 2px 5px; 
    font-weight: bold; 
    border: 1px solid #ddd;
    cursor: pointer;
    transition: all 0.2s;
}
.select-predikat:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.4);
    border-color: #6366f1;
}
.select-predikat.val-A { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
.select-predikat.val-B { background-color: #cce5ff; color: #004085; border-color: #b8daff; }
.select-predikat.val-C { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
.select-predikat.val-D { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }

.nav-hint { font-size: 0.85rem; color: #64748b; }
.nav-hint kbd { background: #f1f5f9; color: #1e293b; border: 1px solid #cbd5e1; box-shadow: 0 1px 0 rgba(0,0,0,0.1); }
</style>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-edit"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Input Nilai Sikap: <?= htmlspecialchars($agenda['nama_kelas']) ?>
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <a href="<?= BASE_URL ?>penilaian_sikap" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-weight-bold shadow-sm">
          <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    <div class="card card-outline card-info shadow-sm mb-3">
      <div class="card-body py-2 px-3">
        <div class="row align-items-center">
          <div class="col-md-4">
            <label class="mb-0 mr-3 text-sm font-weight-bold">Arah Input:</label>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-sm btn-outline-info active">
                <input type="radio" name="nav-mode" value="horizontal" checked> <i class="fas fa-arrows-alt-h mr-1"></i> Horizontal
              </label>
              <label class="btn btn-sm btn-outline-info">
                <input type="radio" name="nav-mode" value="vertical"> <i class="fas fa-arrows-alt-v mr-1"></i> Vertikal
              </label>
            </div>
          </div>
          <div class="col-md-8 nav-hint text-right">
            <span>Navigasi: <kbd>Panah</kbd> atau <kbd>Enter</kbd></span> | 
            <span>Input Cepat: Tekan tombol <kbd>A</kbd>, <kbd>B</kbd>, <kbd>C</kbd>, atau <kbd>D</kbd></span>
          </div>
        </div>
      </div>
    </div>

    <form action="<?= BASE_URL ?>penilaian_sikap/save_nilai" method="POST">
      <input type="hidden" name="id_agenda" value="<?= $agenda['id_agenda'] ?>">

      <div class="card card-outline card-primary shadow">
        <div class="card-header bg-white">
          <h3 class="card-title font-weight-bold"><i class="fas fa-tasks mr-1 text-primary"></i> Rubrik Penilaian Siswa</h3>
          <div class="card-tools">
            <span class="badge badge-info p-2">Keterangan: A=4, B=3, C=2, D=1</span>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-nilai mb-0">
              <thead class="bg-light">
                <tr>
                  <th width="50" class="text-center">No</th>
                  <th width="250">Nama Siswa</th>
                  <?php 
                  $col_idx = 0;
                  foreach($komponen_list as $k): 
                    $col_idx++;
                  ?>
                    <th class="text-center" title="<?= htmlspecialchars($k['deskripsi']) ?>">
                      <?= htmlspecialchars($k['nama_komponen']) ?>
                    </th>
                  <?php endforeach; ?>
                  <th width="120" class="text-center bg-dark">Rata-rata / Predikat</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $row_idx = 0;
                foreach($siswa_list as $s): 
                  $row_idx++;
                ?>
                <tr>
                  <td class="text-center"><?= $row_idx ?></td>
                  <td>
                    <div class="font-weight-bold"><?= htmlspecialchars($s['nama']) ?></div>
                    <small class="text-muted"><?= $s['nisn'] ?></small>
                  </td>
                  <?php 
                  $current_col = 0;
                  foreach($komponen_list as $k): 
                    $current_col++;
                    $val = $s['details'][$k['id_komponen']]['nilai_predikat'] ?? '';
                  ?>
                    <td class="text-center">
                      <select name="nilai[<?= $s['id_penempatan'] ?>][<?= $k['id_komponen'] ?>]" 
                              class="select-predikat val-<?= $val ?>"
                              data-row="<?= $row_idx ?>"
                              data-col="<?= $current_col ?>">
                        <option value="" <?= $val == '' ? 'selected' : '' ?>>-</option>
                        <option value="A" <?= $val == 'A' ? 'selected' : '' ?>>A</option>
                        <option value="B" <?= $val == 'B' ? 'selected' : '' ?>>B</option>
                        <option value="C" <?= $val == 'C' ? 'selected' : '' ?>>C</option>
                        <option value="D" <?= $val == 'D' ? 'selected' : '' ?>>D</option>
                      </select>
                    </td>
                  <?php endforeach; ?>
                  <td class="text-center bg-light">
                    <?php if ($s['rata_rata']): ?>
                        <div class="font-weight-bold h5 mb-0"><?= $s['predikat'] ?></div>
                        <small class="text-muted">(<?= number_format($s['rata_rata'], 2) ?>)</small>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer bg-white text-right">
          <button type="submit" class="btn btn-primary btn-lg elevation-2 px-5 font-weight-bold">
            <i class="fas fa-save mr-2"></i> Simpan Penilaian
          </button>
        </div>
      </div>
    </form>
  </div>
</section>

<script>
$(function(){
  const totalRows = <?= count($siswa_list) ?>;
  const totalCols = <?= count($komponen_list) ?>;

  // Ubah warna select predikat secara dinamis
  $('.select-predikat').on('change', function(){
    const val = $(this).val();
    $(this).removeClass('val- val-A val-B val-C val-D').addClass('val-' + val);
  });

  // Handle Keyboard Shortcuts & Navigation
  $('.select-predikat').on('keydown', function(e){
    const row = parseInt($(this).data('row'));
    const col = parseInt($(this).data('col'));
    const mode = $('input[name="nav-mode"]:checked').val();
    const key = e.key.toUpperCase();

    // 1. Quick Entry (A/B/C/D atau 1/2/3/4)
    const valMap = {'A':'A', 'B':'B', 'C':'C', 'D':'D', '1':'A', '2':'B', '3':'C', '4':'D'};
    if (valMap[key]) {
      e.preventDefault();
      $(this).val(valMap[key]).trigger('change');
      moveFocus(row, col, mode, 1);
      return;
    }

    // 2. Navigation Keys
    if (e.key === 'ArrowRight') { e.preventDefault(); moveFocus(row, col, 'horizontal', 1); }
    if (e.key === 'ArrowLeft')  { e.preventDefault(); moveFocus(row, col, 'horizontal', -1); }
    if (e.key === 'ArrowDown')  { e.preventDefault(); moveFocus(row, col, 'vertical', 1); }
    if (e.key === 'ArrowUp')    { e.preventDefault(); moveFocus(row, col, 'vertical', -1); }
    
    // 3. Enter Key
    if (e.key === 'Enter') {
      e.preventDefault();
      moveFocus(row, col, mode, 1);
    }
  });

  function moveFocus(row, col, mode, delta) {
    let nextRow = row;
    let nextCol = col;

    if (mode === 'horizontal') {
      nextCol += delta;
      if (nextCol > totalCols) {
        nextCol = 1;
        nextRow += 1;
      } else if (nextCol < 1) {
        nextCol = totalCols;
        nextRow -= 1;
      }
    } else {
      nextRow += delta;
      if (nextRow > totalRows) {
        nextRow = 1;
        nextCol += 1;
      } else if (nextRow < 1) {
        nextRow = totalRows;
        nextCol -= 1;
      }
    }

    // Boundary check
    if (nextRow >= 1 && nextRow <= totalRows && nextCol >= 1 && nextCol <= totalCols) {
      $(`.select-predikat[data-row="${nextRow}"][data-col="${nextCol}"]`).focus();
    }
  }
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>
