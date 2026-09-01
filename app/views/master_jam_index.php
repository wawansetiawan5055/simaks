<?php include __DIR__.'/partials/header.php'; ?>
<style>
    .drag-handle { cursor: move; color: #cbd5e1; transition: color 0.2s; }
    .drag-handle:hover { color: #64748b; }
    .ui-sortable-placeholder { height: 45px; background: #f8fafc; border: 1px dashed #cbd5e1; visibility: visible !important; border-radius: 8px; }
    .ui-sortable-helper { background: #fff !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-radius: 8px; display: table !important; }
</style>

<?php 
    // Deteksi jam selesai terakhir untuk fitur Auto-Chain
    $last_end_time = '';
    if (!empty($jam_list)) {
        $last_row = end($jam_list);
        $last_end_time = substr($last_row['jam_selesai'], 0, 5); // Ambil format HH:mm
    }
?>
<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="m-0 font-weight-bold text-dark"><i class="fas fa-clock text-primary mr-2"></i> Master Jam Pelajaran</h4>
            <p class="text-muted small mb-0">Atur slot waktu, durasi, dan jenis kegiatan KBM atau Non-KBM.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-none" style="border-radius: 8px;" 
                data-toggle="modal" data-target="#modalTambahJam" 
                data-next-start="<?= $last_end_time ?>">
            <i class="fas fa-plus mr-1"></i> Tambah Slot Jam
        </button>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
      <div class="card-header bg-white py-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;" id="day-filter-pills">
                <?php foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $index => $h): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-day-pill <?= $index === 0 ? 'active' : '' ?>" data-day="<?= $h ?>"><?= $h ?></button>
                <?php endforeach; ?>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-info shadow-none d-none" id="btn-copy-day" style="border-radius: 8px;">
                    <i class="fas fa-copy mr-1"></i> Salin Jadwal
                </button>
                <span class="text-muted small ml-2 d-none d-md-inline" id="hint-drag"><i class="fas fa-arrows-alt-v mr-1 text-primary"></i> Geser untuk urutan</span>
            </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-sm align-middle mb-0">
            <thead style="background: #f8fafc;">
              <tr class="text-muted">
                <th style="width: 40px;" class="border-bottom d-none d-sm-table-cell"></th>
                <th class="text-center py-2 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">#</th>
                <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">JAM KE-</th>
                <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 150px;">WAKTU</th>
                <th class="text-center py-2 border-bottom d-none d-sm-table-cell" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">DURASI</th>
                <th class="text-center py-2 border-bottom d-none d-sm-table-cell" style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">JENIS</th>
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
                  data-hari="<?= htmlspecialchars($j['hari_pelaksanaan']) ?>"
                  class="bg-white">
                  
                <td class="text-center align-middle d-none d-sm-table-cell"><i class="fas fa-grip-vertical drag-handle"></i></td>
                <td class="text-center align-middle row-number-cell">
                    <span class="badge badge-light border font-weight-bold row-number" style="font-size: 0.75rem; border-radius: 6px; color: #94a3b8;">-</span>
                </td>
                <td class="text-center align-middle">
                    <span class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($j['label_jam_ke']) ?></span>
                </td>
                <td class="text-center align-middle">
                    <code class="text-primary small"><?= htmlspecialchars(substr($j['jam_mulai'], 0, 5)) ?> - <?= htmlspecialchars(substr($j['jam_selesai'], 0, 5)) ?></code>
                </td>
                <td class="text-center align-middle small text-muted d-none d-sm-table-cell"><?= htmlspecialchars($j['durasi_menit']) ?> mnt</td>
                <td class="text-center align-middle d-none d-sm-table-cell">
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
                      <a href="<?= BASE_URL ?>master_jam/delete?id=<?= $j['id_jam'] ?>" 
                         class="btn btn-xs btn-outline-danger border-0 p-1 btn-delete-jam" 
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
  </div>
</section>

<!-- Modal Salin Jadwal -->
<div class="modal fade" id="modalCopyDay" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white border-0 py-3">
        <h5 class="modal-title font-weight-bold"><i class="fas fa-sync mr-2"></i> Salin Jadwal Antar Hari</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body px-4">
        <div class="alert alert-info border-0 small" style="background: #e0f2fe; color: #0369a1; border-radius: 10px;">
            <i class="fas fa-info-circle mr-2"></i> Fitur ini akan **menyalin (kloning)** daftar jam dari hari asal ke hari tujuan. Data hasil salinan akan berdiri sendiri (independen).
        </div>
        <div class="form-group">
            <label class="small font-weight-bold text-muted">SALIN DARI HARI (ASAL):</label>
            <select id="copy_from" class="form-control" style="border-radius: 8px;">
                <?php foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h): ?>
                    <option value="<?= $h ?>"><?= $h ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label class="small font-weight-bold text-muted">KE HARI (TUJUAN):</label>
            <input type="text" id="copy_to_label" class="form-control bg-light" readonly style="border-radius: 8px; font-weight: bold;">
            <input type="hidden" id="copy_to_val">
        </div>
        <div class="custom-control custom-checkbox mt-4">
            <input type="checkbox" class="custom-control-input" id="confirm_sync">
            <label class="custom-control-label small text-danger font-weight-bold" for="confirm_sync">Saya mengerti bahwa data lama di hari tujuan akan tertimpa.</label>
        </div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4">
        <button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-info btn-sm px-4 shadow-none" id="btn-do-copy" disabled style="border-radius: 8px;">Eksekusi Sinkronisasi</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah/Edit Jam -->
<div class="modal fade" id="modalTambahJam" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="<?= BASE_URL ?>master_jam/save" method="POST" id="form-jam">
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
                <option value="">-- Pilih (Abaikan Jika KBM) --</option>
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

          <!-- Hari Aktif otomatis sesuai Tab yang dibuka -->
          <input type="hidden" name="hari_pelaksanaan[]" id="hari_pelaksanaan_hidden" value="Senin">
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
    const kegiatanMetadata = [
        <?php foreach($kegiatan_list as $k): ?>
        {
            id: '<?= $k['id_kegiatan'] ?>',
            jenis: '<?= $k['jenis_kegiatan'] ?>',
            durasi: parseInt('<?= $k['durasi_menit'] ?>', 10),
            hari: '<?= $k['hari_pelaksanaan'] ?>'.split(',')
        },
        <?php endforeach; ?>
    ];

    function hitungJamSelesai() {
        const jamMulai = jamMulaiInput.val();
        const jenis = jenisSelect.val();
        const idKegiatan = kegiatanSelect.val();
        
        if (!jamMulai) return;

        let durasi = 0;
        
        // LOGIKA BARU: Cari durasi yang cocok dengan JENIS dan HARI yang sedang aktif
        const matchByDay = kegiatanMetadata.find(k => k.jenis === jenis && k.hari.includes(currentDay));
        const matchById = kegiatanMetadata.find(k => k.id === idKegiatan);

        if (jenis === 'KBM' && matchByDay) {
            durasi = matchByDay.durasi;
        } else if (idKegiatan && matchById) {
            // Jika memilih kegiatan spesifik dari dropdown (Istirahat/Pembiasaan), gunakan durasi kegiatan itu
            durasi = matchById.durasi;
        } else if (matchByDay) {
            // Fallback: Jika tidak pilih kegiatan spesifik, ambil durasi umum jenis tersebut untuk hari ini
            durasi = matchByDay.durasi;
        }

        if (durasi && !isNaN(durasi)) {
            try {
                let [jam, menit] = jamMulai.split(':').map(Number);
                let date = new Date();
                date.setHours(jam, menit, 0, 0); 
                date.setMinutes(date.getMinutes() + durasi); 
                
                let jamBaru = String(date.getHours()).padStart(2, '0');
                let menitBaru = String(date.getMinutes()).padStart(2, '0');
                
                jamSelesaiInput.val(`${jamBaru}:${menitBaru}`);
                jamSelesaiInput.prop('readonly', true); 
            } catch (e) {
                console.error("Gagal menghitung jam selesai.");
                jamSelesaiInput.prop('readonly', false);
            }
        } else {
             jamSelesaiInput.prop('readonly', false);
        }
    }

    // Event listener
    jamMulaiInput.on('input change', hitungJamSelesai);
    kegiatanSelect.on('change', hitungJamSelesai);
    jenisSelect.on('change', function() {
        if ($(this).val() === 'KBM') {
            kegiatanSelect.val(''); // Reset pilihan kegiatan krn KBM ga butuh milih opsi spesifik ini
        }
        hitungJamSelesai();
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

        // Handle Day (Set hidden input to currentDay context if on a specific tab)
        if (currentDay && currentDay !== 'Semua') {
            $('#hari_pelaksanaan_hidden').val(currentDay);
        }

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

    // Handle initial value for new entry (Auto-Chain)
    $('#modalTambahJam').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const isEdit = button.hasClass('btn-edit');
        
        if (!isEdit) {
            const nextStart = button.data('next-start');
            if (nextStart) {
                jamMulaiInput.val(nextStart);
                // Trigger calculation if a jenis_kegiatan is already selected (usually KBM by default)
                setTimeout(() => hitungJamSelesai(), 100);
            }
        }
    });

    // --- FITUR BARU: FILTER HARI & SALIN JADWAL ---
    const btnCopy = $('#btn-copy-day');
    const dayPills = $('.btn-day-pill');
    let currentDay = 'Semua';

    // Hitung ulang nomor baris berdasarkan baris yang terlihat
    function renumberRows() {
        let no = 1;
        $('#sortable-jam tr:visible').each(function() {
            $(this).find('.row-number').text(no++).css('color', '#374151');
        });
    }

    function filterTable(day) {
        currentDay = day;
        $('#sortable-jam tr').each(function() {
            const row = $(this);
            const days = row.data('hari') || "";
            if (day === 'Semua' || days.split(',').includes(day)) {
                row.show();
            } else {
                row.hide();
            }
        });

        // Perbarui nomor urut yang tampil
        renumberRows();

        // Update hidden input di modal & Toggle Copy Button
        if (day !== 'Semua') {
            $('#hari_pelaksanaan_hidden').val(day);
            btnCopy.removeClass('d-none');
            $('#copy_to_label').val(day);
            $('#copy_to_val').val(day);
        } else {
            btnCopy.addClass('d-none');
        }

        // Update semua link hapus agar menyertakan konteks hari
        $('.btn-delete-jam').each(function() {
            const btn = $(this);
            const baseHref = btn.attr('href').split('&day=')[0];
            btn.attr('href', baseHref + '&day=' + (day === 'Semua' ? '' : day));
        });
    }

    dayPills.on('click', function() {
        dayPills.removeClass('active');
        $(this).addClass('active');
        filterTable($(this).data('day'));
    });

    // Set default filter to Senin on load
    filterTable('Senin');

    btnCopy.on('click', function() {
        $('#confirm_sync').prop('checked', false);
        $('#btn-do-copy').prop('disabled', true);
        $('#modalCopyDay').modal('show');
    });

    $('#confirm_sync').on('change', function() {
        $('#btn-do-copy').prop('disabled', !$(this).is(':checked'));
    });

    $('#btn-do-copy').on('click', function() {
        const from = $('#copy_from').val();
        const to = $('#copy_to_val').val();

        if (from === to) {
            alert('Hari asal dan tujuan tidak boleh sama.');
            return;
        }

        $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...').prop('disabled', true);

        $.ajax({
            url: '<?= BASE_URL ?>master_jam/copy_day',
            type: 'POST',
            data: { from: from, to: to },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    if (window.Notify) {
                        window.Notify.success('Berhasil!', res.message);
                    } else {
                        alert(res.message);
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error: ' + res.message);
                    $('#btn-do-copy').html('Eksekusi Sinkronisasi').prop('disabled', false);
                }
            },
            error: function() {
                alert('Terjadi kesalahan koneksi.');
                $('#btn-do-copy').html('Eksekusi Sinkronisasi').prop('disabled', false);
            }
        });
    });

    // 2. Logika Drag & Drop
    $("#sortable-jam").sortable({
        handle: ".drag-handle", 
        placeholder: "ui-sortable-placeholder", 
        axis: "y", 
        update: function(event, ui) {
            if (currentDay !== 'Semua') {
                if (window.Notify) window.Notify.warning('Perhatian', 'Urutan global mungkin terpengaruh saat drag dalam mode filter.');
            }
            // Perbarui nomor baris setelah drag
            renumberRows();

            var urutanIds = [];
            $(this).children('tr').each(function(index, element) {
                urutanIds.push($(element).attr('data-id'));
            });

            $.ajax({
                url: '<?= BASE_URL ?>master_jam/update_urutan',
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