<?php include __DIR__.'/partials/header.php'; ?>

<style>
    /* (CSS Anda tetap sama) */
    .content-header h1 { font-size: 1.4rem !important; font-weight: 600 !important; }
    .card-primary .card-body { padding-bottom: 0.1rem !important; }
    .card-primary .card-header h4 { font-size: 1.1rem !important; }
    .card-outline .card-header h3 { font-size: 1.1rem !important; }
    .table-sm th, .table-sm td { padding: 0.4rem; }
    .btn-aksi-grup .btn { margin-left: 13px;}
</style>

<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 px-4">
      <div>
        <h2 class="m-0 font-weight-bold text-dark">Struktur Kurikulum (Alokasi JJM)</h2>
        <p class="text-muted small mb-0">Definisi jam mata pelajaran per minggu untuk setiap tingkat kelas.</p>
      </div>
      <div class="text-right">
        <button type="button" class="btn btn-warning shadow-sm px-3 font-weight-bold text-white" style="border-radius: 8px;" onclick="resetForm(); $('#modal-jjm').modal('show');">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Alokasi JJM
        </button>
      </div>
    </div>
  </div>
</div>

<section class="content">
<div class="container-fluid">
    
    <!-- Modal Form JJM -->
    <div class="modal fade" id="modal-jjm" tabindex="-1" role="dialog" aria-labelledby="modal-jjm-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="form-title">Tambah Alokasi JJM</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="index.php?mod=struktur_kurikulum&act=save" method="POST" id="form-struktur">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-sm">Mata Pelajaran</label>
                                <select name="id_mapel" id="id_mapel" class="form-control select2" style="width: 100%;" required>
                                    <option value="">-- Pilih Mapel --</option>
                                    <?php foreach($mapel_list as $m): ?>
                                        <option value="<?= $m['id_mapel'] ?>" data-kelompok="<?= htmlspecialchars($m['kategori_mapel'] ?? '') ?>"><?= htmlspecialchars($m['nama_mapel']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-sm">Tingkat Kelas</label>
                                <select name="tingkat" id="tingkat" class="form-control" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <?php foreach($tingkat_list as $t): ?>
                                        <option value="<?= $t ?>"><?= $t ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-sm">Kelompok Mapel</label>
                                <select name="kelompok" id="kelompok" class="form-control" required>
                                    <option value="">-- Pilih Kelompok --</option>
                                    <option value="Mata Pelajaran Wajib">Mata Pelajaran Wajib</option>
                                    <option value="Mata Pelajaran Pilihan">Mata Pelajaran Pilihan</option>
                                    <option value="Muatan Lokal">Muatan Lokal</option>
                                    <option value="Mulok Yayasan">Mulok Yayasan</option>
                                </select>
                                <small class="text-muted italic" style="font-size: 0.75rem;">*Terisi otomatis berdasarkan kategori mapel</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-sm">Alokasi JP/Mgg</label>
                                <input type="number" name="alokasi_jp_minggu" id="alokasi_jp_minggu" class="form-control" min="1" placeholder="Cth: 2" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary px-4 shadow">Simpan Struktur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php 
        $grouped_data = [];
        $total_jp = ['X' => 0, 'XI' => 0, 'XII' => 0]; 
        
        foreach ($list_struktur as $s) {
            $grouped_data[$s['tingkat']][] = $s;
            if (isset($total_jp[$s['tingkat']])) {
                $total_jp[$s['tingkat']] += $s['alokasi_jp_minggu']; 
            }
        }
        $tingkat_display = ['X', 'XI', 'XII']; 
        $colors = ['X' => '#3b82f6', 'XI' => '#10b981', 'XII' => '#f59e0b']; // Blue, Green, Orange
        $bg_colors = ['X' => '#eff6ff', 'XI' => '#f0fdf4', 'XII' => '#fffbeb']; // Light backgrounds
        
        foreach ($tingkat_display as $tingkat): 
            $data_tingkat = $grouped_data[$tingkat] ?? [];
            $kelompok_data = [];
            foreach ($data_tingkat as $d) {
                $kelompok_data[$d['kelompok']][] = $d;
            }
            $accent = $colors[$tingkat];
            $bg_header = $bg_colors[$tingkat];
        ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; overflow: hidden; border-top: 4px solid <?= $accent ?> !important;">
                <div class="card-header py-3" style="background: <?= $bg_header ?>; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div class="text-center">
                        <h6 class="font-weight-bold mb-1" style="color: <?= $accent ?>; letter-spacing: 1px;">KURIKULUM MERDEKA</h6>
                        <h4 class="font-weight-bold mb-0 text-dark">KELAS <?= htmlspecialchars($tingkat) ?></h4>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                <th class="text-center py-2 border-top-0" style="width: 40px;">NO</th>
                                <th class="py-2 border-top-0">MATA PELAJARAN</th>
                                <th class="text-center py-2 border-top-0" style="width: 60px;">JJM</th>
                                <th class="text-center py-2 border-top-0" style="width: 80px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no_urut = 1; 
                            if (!empty($kelompok_data)):
                                foreach ($kelompok_data as $kelompok => $mapel_list):
                            ?>
                                <tr>
                                    <td colspan="4" class="px-3 py-2 bg-light font-weight-bold text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <?= htmlspecialchars($kelompok) ?>
                                    </td>
                                </tr>
                                <?php foreach($mapel_list as $mapel): ?>
                                    <tr>
                                        <td class="text-center align-middle" style="font-size: 0.85rem; color: #64748b;"><?= $no_urut++ ?></td>
                                        <td class="align-middle font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($mapel['nama_mapel']) ?></td>
                                        <td class="text-center align-middle" style="font-size: 0.85rem;">
                                            <span class="badge badge-light border px-2"><?= $mapel['alokasi_jp_minggu'] ?> JP</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-xs btn-outline-warning border-0" 
                                                    style="background: #fffbeb; color: #d97706;"
                                                    title="Edit" 
                                                    onclick="editStruktur(this)"
                                                    data-id="<?= $mapel['id_struktur'] ?>"
                                                    data-id_mapel="<?= $mapel['id_mapel'] ?>"
                                                    data-tingkat="<?= $mapel['tingkat'] ?>"
                                                    data-kelompok="<?= htmlspecialchars($mapel['kelompok']) ?>"
                                                    data-jp="<?= $mapel['alokasi_jp_minggu'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="index.php?mod=struktur_kurikulum&act=delete&id=<?= $mapel['id_struktur'] ?>" 
                                                    class="btn btn-xs btn-outline-danger border-0 ml-1" 
                                                    style="background: #fef2f2; color: #ef4444;"
                                                    title="Hapus" 
                                                    onclick="return confirmDelete(event)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php 
                                endforeach;
                            else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4 small">Belum ada struktur data.</td></tr>
                            <?php endif; ?>
                            
                            <tr style="background: <?= $bg_header ?>;">
                                <td colspan="2" class="text-right font-weight-bold pr-3 py-2" style="font-size: 0.85rem; color: #334155;">TOTAL JAM PER MINGGU</td>
                                <td class="text-center font-weight-bold py-2" style="font-size: 0.9rem; color: <?= $accent ?>;"><?= $total_jp[$tingkat] ?></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</section>

<script>
// URL dasar untuk form (mode Tambah)
const formActionBase = 'index.php?mod=struktur_kurikulum&act=save';

function resetForm() {
    $('#form-title').text('Tambah Alokasi JJM');
    $('#form-struktur').attr('action', formActionBase);
    
    // Hapus hidden input id jika ada
    $('#form-struktur input[name="id"]').remove();
    
    // Hapus data dari form
    $('#id_mapel').val('').trigger('change');
    $('#tingkat').val('');
    $('#kelompok').val('');
    $('#alokasi_jp_minggu').val('');
    
    $('.alert-dismissible').remove();
}

// Fungsi Global untuk Edit (Dipanggil dari onclick)
function editStruktur(btn) {
    const $btn = $(btn);
    const idToEdit = $btn.data('id');
    
    // Reset any previous state
    resetForm();
    
    // Prepend hidden ID for save logic
    $('#form-struktur').prepend('<input type="hidden" name="id" value="' + idToEdit + '">');

    // Populate Fields
    $('#id_mapel').val($btn.data('id_mapel')).trigger('change');
    $('#tingkat').val($btn.data('tingkat'));
    $('#kelompok').val($btn.data('kelompok'));
    $('#alokasi_jp_minggu').val($btn.data('jp'));

    // UI Feedback
    $('#form-title').html('<i class="fas fa-edit"></i> Edit Alokasi JJM');
    
    // Show Modal
    $('#modal-jjm').modal('show');
}

$(document).ready(function() {
    // 1. Autofill Kelompok Mapel
    $('#id_mapel').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const kategori = selectedOption.data('kelompok');
        if (kategori) {
            $('#kelompok').val(kategori);
        } else {
            $('#kelompok').val('');
        }
    });

    // 2. Tombol Batal
    $('#btn-batal').on('click', resetForm);
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>