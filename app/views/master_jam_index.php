<?php include __DIR__.'/partials/header.php'; ?>
<style>
    .drag-handle { cursor: move; color: #cbd5e1; transition: color 0.2s; }
    .drag-handle:hover { color: #64748b; }
    .ui-sortable-placeholder { height: 45px; background: #f8fafc; border: 1px dashed #cbd5e1; visibility: visible !important; border-radius: 8px; }
    .ui-sortable-helper { background: #fff !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-radius: 8px; display: table !important; }
</style>

<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="m-0 font-weight-bold text-dark"><i class="fas fa-clock text-primary mr-2"></i> Master Jam Pelajaran</h4>
            <p class="text-muted small mb-0">Atur slot waktu, durasi, dan jenis kegiatan KBM atau Non-KBM.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-none" style="border-radius: 8px;" data-toggle="modal" data-target="#modalTambahJam">
            <i class="fas fa-plus mr-1"></i> Tambah Slot Jam
        </button>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
      <div class="card-header bg-white py-3 border-bottom">
        <h6 class="mb-0 font-weight-bold text-muted small"><i class="fas fa-arrows-alt-v mr-2 text-primary"></i> DRAG & DROP UNTUK MENGURUTKAN JADWAL</h6>
      </div>
      <div class="card-body p-0">
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead style="background: #f8fafc;">
            <tr class="text-muted">
              <th style="width: 40px;" class="border-bottom"></th>
              <th class="text-center py-2 border-bottom" style="width: 70px; font-size: 0.7rem; letter-spacing: 1px;">URUTAN</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">JAM KE-</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 150px;">WAKTU</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">DURASI</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">JENIS</th>
              <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA KEGIATAN</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">AKSI</th>
            </tr>
          </thead>
          <tbody id="sortable-jam">
            <?php if (empty($jam_list)): ?>
                <tr><td colspan="8" class="text-center py-5 text-muted small"><em>Belum ada data jam pelajaran.</em></td></tr>
            <?php endif; ?>
            <?php foreach($jam_list as $j): ?>
            <tr data-id="<?= $j['id_jam'] ?>" 
                data-label="<?= htmlspecialchars($j['label_jam_ke']) ?>"
                data-mulai="<?= htmlspecialchars($j['jam_mulai']) ?>"
                data-selesai="<?= htmlspecialchars($j['jam_selesai']) ?>"
                data-jenis="<?= htmlspecialchars($j['jenis_kegiatan']) ?>"
                data-id_kegiatan="<?= htmlspecialchars($j['id_kegiatan']) ?>"
                data-kegiatan_custom="<?= htmlspecialchars($j['nama_kegiatan_custom']) ?>"
                class="bg-white">
                
              <td class="text-center align-middle"><i class="fas fa-grip-vertical drag-handle"></i></td>
              <td class="text-center align-middle">
                  <span class="badge badge-light border font-weight-bold" style="font-size: 0.75rem; border-radius: 6px;"><?= $j['urutan'] ?></span>
              </td>
              <td class="text-center align-middle">
                  <span class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($j['label_jam_ke']) ?></span>
              </td>
              <td class="text-center align-middle">
                  <code class="text-primary small"><?= htmlspecialchars(substr($j['jam_mulai'], 0, 5)) ?> - <?= htmlspecialchars(substr($j['jam_selesai'], 0, 5)) ?></code>
              </td>
              <td class="text-center align-middle small text-muted"><?= htmlspecialchars($j['durasi_menit']) ?> mnt</td>
              <td class="text-center align-middle">
                  <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.65rem; border-radius: 4px;"><?= htmlspecialchars($j['jenis_kegiatan']) ?></span>
              </td>
              <td class="align-middle">
                  <span class="font-weight-bold text-dark small"><?= htmlspecialchars($j['nama_kegiatan_custom'] ?: $j['nama_kegiatan']) ?></span>
              </td>
              <td class="text-center align-middle">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-outline-warning border-0 p-1 mr-1 btn-edit" 
                            style="background: #fffbeb; width: 28px; height: 28px; border-radius: 8px; color: #d97706;" 
                            title="Edit"><i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i></button>
                    <a href="index.php?mod=master_jam&act=delete&id=<?= $j['id_jam'] ?>" 
                       class="btn btn-xs btn-outline-danger border-0 p-1" 
                       style="background: #fef2f2; width: 28px; height: 28px; border-radius: 8px; color: #dc2626;" 
                       title="Hapus" onclick="return confirmDelete(event)">
                        <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                    </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Modal Tambah/Edit Jam -->
<div class="modal fade" id="modalTambahJam" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="index.php?mod=master_jam&act=save" method="POST" id="form-jam">
        <input type="hidden" name="id_jam" id="id_jam" value="">
        
        <div class="modal-header">
          <h5 class="modal-title" id="form-title">Tambah Slot Jam Baru</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-4">
              <label>Label Jam Ke-</label>
              <input type="text" name="label_jam_ke" id="label_jam_ke" class="form-control" placeholder="Cth: 1, 2, Istirahat, -">
            </div>
            <div class="form-group col-md-4">
              <label>Jam Mulai</label>
              <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
            </div>
            <div class="form-group col-md-4">
              <label>Jam Selesai</label>
              <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Jenis Kegiatan</label>
              <select name="jenis_kegiatan" id="jenis_kegiatan" class="form-control" required>
                <option value="KBM">KBM (Pelajaran)</option>
                <option value="Istirahat">Istirahat</option>
                <option value="Pembiasaan">Pembiasaan (Upacara, Tadarus)</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="form-group col-md-6" id="container_kegiatan">
              <label>Kegiatan (dari Master)</label>
              <select name="id_kegiatan" id="id_kegiatan" class="form-control">
                <option value="">-- (Hanya Non-KBM) --</option>
                <?php foreach($kegiatan_list as $k): ?>
                  <option value="<?= $k['id_kegiatan'] ?>" data-durasi="<?= $k['durasi_menit'] ?>">
                    <?= $k['nama_kegiatan'] ?> (<?= $k['jenis_kegiatan'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group" id="container_kegiatan_custom">
            <label>Nama Kustom</label>
            <input type="text" name="nama_kegiatan_custom" id="nama_kegiatan_custom" class="form-control" placeholder="Cth: Sholat Dzuhur">
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/partials/footer.php'; ?>

<script>
$(document).ready(function() {
    if (typeof $.ui === 'undefined') { return; }

    const formJam = $('#form-jam');
    const formTitle = $('#form-title');
    const btnBatal = $('#btn-batal');
    
    const jamMulaiInput = $('#jam_mulai');
    const jamSelesaiInput = $('#jam_selesai');
    const kegiatanSelect = $('#id_kegiatan');
    const jenisSelect = $('#jenis_kegiatan');

    // Buat Peta Durasi (dari data PHP)
    const durasiMap = {
        <?php foreach($kegiatan_list as $k): ?>
            '<?= $k['id_kegiatan'] ?>': '<?= $k['durasi_menit'] ?>',
        <?php endforeach; ?>
    };

    function hitungJamSelesai() {
        const jamMulai = jamMulaiInput.val();
        const jenis = jenisSelect.val();
        const idKegiatan = kegiatanSelect.val();
        
        // Hanya hitung jika jam mulai ada, dan (jenis BUKAN KBM DAN idKegiatan dipilih)
        if (jamMulai && jenis !== 'KBM' && idKegiatan && durasiMap[idKegiatan]) {
            const durasi = parseInt(durasiMap[idKegiatan], 10);
            
            if (isNaN(durasi)) {
                jamSelesaiInput.val(''); // Kosongkan jika durasi tidak valid
                return;
            }
            
            try {
                let [jam, menit] = jamMulai.split(':').map(Number);
                
                let date = new Date();
                date.setHours(jam);
                date.setMinutes(menit + durasi); // Tambah durasi
                
                let jamBaru = String(date.getHours()).padStart(2, '0');
                let menitBaru = String(date.getMinutes()).padStart(2, '0');
                
                jamSelesaiInput.val(`${jamBaru}:${menitBaru}`);
                jamSelesaiInput.prop('readonly', true); // Kunci jam selesai
            } catch (e) {
                console.error("Gagal menghitung jam selesai.");
                jamSelesaiInput.prop('readonly', false);
            }
        } else {
             jamSelesaiInput.prop('readonly', false); // Buka kunci jika KBM
        }
    }

    // Event listener
    jamMulaiInput.on('change', hitungJamSelesai);
    kegiatanSelect.on('change', hitungJamSelesai);
    jenisSelect.on('change', function() {
        if ($(this).val() === 'KBM') {
            jamSelesaiInput.prop('readonly', false); // Buka kunci jika KBM
            // Kosongkan jam selesai jika KBM (sesuai permintaan awal Anda)
            // jamSelesaiInput.val(''); 
        } else {
            hitungJamSelesai();
        }
    });


    // 1. Logika Edit - Open Modal
    $(document).on('click', '.btn-edit', function() {
        const row = $(this).closest('tr');
        
        formJam.find('#id_jam').val(row.data('id'));
        formJam.find('#label_jam_ke').val(row.data('label'));
        formJam.find('#jam_mulai').val(row.data('mulai'));
        formJam.find('#jam_selesai').val(row.data('selesai'));
        formJam.find('#jenis_kegiatan').val(row.data('jenis'));
        formJam.find('#id_kegiatan').val(row.data('id_kegiatan'));
        formJam.find('#nama_kegiatan_custom').val(row.data('kegiatan_custom'));

        formTitle.text('Edit Slot Jam (Urutan: ' + row.find('td:nth-child(2)').text() + ')');
        $('#modalTambahJam').modal('show');
        hitungJamSelesai(); // Hitung ulang saat edit
    });

    // Reset form when modal is closed
    $('#modalTambahJam').on('hidden.bs.modal', function() {
        formJam[0].reset();
        formJam.find('#id_jam').val('');
        formTitle.text('Tambah Slot Jam Baru');
        jamSelesaiInput.prop('readonly', false);
    });

    // 2. Logika Drag & Drop
    $("#sortable-jam").sortable({
        handle: ".drag-handle", 
        placeholder: "ui-sortable-placeholder", 
        axis: "y", 
        update: function(event, ui) {
            var urutanIds = [];
            $(this).children('tr').each(function(index, element) {
                urutanIds.push($(element).attr('data-id'));
            });

            $.ajax({
                url: 'index.php?mod=master_jam&act=update_urutan',
                type: 'POST',
                data: { urutan: urutanIds },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        if (window.Notify) {
                            window.Notify.success('Berhasil!', response.message + ' Halaman akan dimuat ulang.');
                        } else {
                            alert(response.message);
                        }
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        if (window.Notify) {
                            window.Notify.error('Error!', response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                },
                error: function() {
                    if (window.Notify) {
                        window.Notify.error('Error!', 'Terjadi kesalahan koneksi.');
                    } else {
                        alert('Terjadi kesalahan koneksi.');
                    }
                }
            });
        }
    });
});
</script>