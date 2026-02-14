<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />

<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 px-4">
      <div>
        <h1 class="m-0"><i class="fas fa-calendar-alt mr-2"></i> Kalender Pendidikan</h1>
        <p class="text-muted small mb-0">Kelola agenda kegiatan sekolah, hari libur, dan jadwal akademik lainnya.</p>
      </div>
      <div class="text-end">
        <?php if ($can_create): ?>
          <button type="button" class="btn btn-warning shadow-sm px-3 font-weight-bold text-white"
            style="border-radius: 8px;" data-toggle="modal" data-target="#modalTambahKegiatan">
            <i class="fas fa-plus mr-1"></i> Tambah Kegiatan
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    <!-- Filter Section -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
      <div class="card-header bg-white py-3 border-bottom">
        <h6 class="mb-0 font-weight-bold text-muted"><i class="fas fa-filter mr-2 text-primary"></i> FILTER KEGIATAN
        </h6>
      </div>
      <div class="card-body">
        <form method="GET" id="filterForm">
          <input type="hidden" name="mod" value="kalender_akademik">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-0">
                <label class="small font-weight-bold text-muted">Tahun Ajaran</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0"><i
                        class="fas fa-calendar-alt text-muted"></i></span>
                  </div>
                  <select name="id_ta" class="form-control border-left-0" onchange="this.form.submit()">
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    <?php foreach ($ta_list as $ta): ?>
                      <option value="<?= $ta['id_ta'] ?>" <?= ($filter_ta == $ta['id_ta']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ta['nama_ta']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-0">
                <label class="small font-weight-bold text-muted">Kategori</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-tags text-muted"></i></span>
                  </div>
                  <select name="kategori" id="filterKategori" class="form-control border-left-0">
                    <option value="">Semua Kategori</option>
                    <option value="Libur">🟥 Libur</option>
                    <option value="Ujian">🟧 Ujian</option>
                    <option value="Kegiatan Sekolah">🟦 Kegiatan Sekolah</option>
                    <option value="Rapat">🟩 Rapat</option>
                    <option value="Lainnya">⬜ Lainnya</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button type="button" class="btn btn-light border text-primary font-weight-bold btn-block"
                onclick="$('#calendar').fullCalendar('refetchEvents')" style="border-radius: 8px;">
                <i class="fas fa-sync-alt mr-2"></i> Refresh Kalender
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Calendar Card -->
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
      <div class="card-body p-4">
        <div id="calendar"></div>
      </div>
      <div class="card-footer bg-light border-top">
        <div class="d-flex align-items-center flex-wrap">
          <span class="font-weight-bold text-muted mr-3 small text-uppercase">Keterangan:</span>
          <span class="badge badge-pill badge-light border mr-2 mb-1 px-3 py-2"><span style="color: #dc3545;">●</span>
            Libur</span>
          <span class="badge badge-pill badge-light border mr-2 mb-1 px-3 py-2"><span style="color: #fd7e14;">●</span>
            Ujian</span>
          <span class="badge badge-pill badge-light border mr-2 mb-1 px-3 py-2"><span style="color: #007bff;">●</span>
            Kegiatan</span>
          <span class="badge badge-pill badge-light border mr-2 mb-1 px-3 py-2"><span style="color: #28a745;">●</span>
            Rapat</span>
          <span class="badge badge-pill badge-light border mr-2 mb-1 px-3 py-2"><span style="color: #6c757d;">●</span>
            Lainnya</span>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Modal Tambah/Edit Kegiatan -->
<div class="modal fade" id="modalTambahKegiatan" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="index.php?mod=kalender_akademik&act=save" method="POST" id="formKegiatan">
        <input type="hidden" name="id_kalender" id="id_kalender" value="">
        <input type="hidden" name="id_ta" id="id_ta" value="<?= $filter_ta ?>">

        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Tambah Kegiatan Baru</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Judul Kegiatan <span class="text-danger">*</span></label>
            <input type="text" name="judul_kegiatan" id="judul_kegiatan" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal Selesai <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kategori <span class="text-danger">*</span></label>
                <select name="kategori" id="kategori" class="form-control" required>
                  <option value="Libur">Libur</option>
                  <option value="Ujian">Ujian</option>
                  <option value="Kegiatan Sekolah" selected>Kegiatan Sekolah</option>
                  <option value="Rapat">Rapat</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Warna</label>
                <input type="color" name="warna" id="warna" class="form-control" value="#007bff">
                <small class="text-muted">Warna akan otomatis disesuaikan dengan kategori</small>
              </div>
            </div>
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

<!-- Modal Detail Kegiatan -->
<div class="modal fade" id="modalDetailKegiatan" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailTitle"></h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Kategori:</strong> <span id="detailKategori"></span></p>
        <p><strong>Tanggal:</strong> <span id="detailTanggal"></span></p>
        <p><strong>Deskripsi:</strong></p>
        <p id="detailDeskripsi" class="text-muted"></p>
      </div>
      <div class="modal-footer">
        <?php if ($can_update): ?>
          <button type="button" class="btn btn-warning btn-sm" id="btnEdit">
            <i class="fas fa-edit"></i> Edit
          </button>
        <?php endif; ?>
        <?php if ($can_delete): ?>
          <button type="button" class="btn btn-danger btn-sm" id="btnDelete">
            <i class="fas fa-trash"></i> Hapus
          </button>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/id.js'></script>

<script>
  $(document).ready(function () {
    const idTa = <?= json_encode($filter_ta) ?>;
    const categoryColors = <?= json_encode($kategori_colors) ?>;
    const canCreate = <?= json_encode($can_create) ?>;
    const canUpdate = <?= json_encode($can_update) ?>;
    const canDelete = <?= json_encode($can_delete) ?>;

    // Initialize FullCalendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'id',
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listMonth'
      },
      buttonText: {
        today: 'Hari Ini',
        month: 'Bulan',
        week: 'Minggu',
        list: 'Daftar'
      },
      height: 'auto',
      events: function (info, successCallback, failureCallback) {
        const filterKategori = $('#filterKategori').val();

        $.ajax({
          url: 'index.php?mod=kalender_akademik&act=api',
          data: {
            start: info.startStr,
            end: info.endStr,
            id_ta: idTa,
            kategori: filterKategori
          },
          success: function (data) {
            successCallback(data);
          },
          error: function () {
            failureCallback();
          }
        });
      },
      eventClick: function (info) {
        const event = info.event;
        $('#detailTitle').text(event.title);
        $('#detailKategori').html('<span class="badge" style="background-color: ' + event.backgroundColor + '">' + event.extendedProps.kategori + '</span>');
        $('#detailTanggal').text(formatDateRange(event.start, event.end));
        $('#detailDeskripsi').text(event.extendedProps.deskripsi || '-');

        // Set edit and delete buttons
        $('#btnEdit').off('click').on('click', function () {
          $('#modalDetailKegiatan').modal('hide');
          editEvent(event.id);
        });

        $('#btnDelete').off('click').on('click', function () {
          if (confirm('Yakin ingin menghapus kegiatan ini?')) {
            window.location.href = 'index.php?mod=kalender_akademik&act=delete&id=' + event.id + '&id_ta=' + idTa;
          }
        });

        $('#modalDetailKegiatan').modal('show');
      },
      dateClick: function (info) {
        if (!canCreate) return;
        // Quick add - open modal with pre-filled date
        $('#id_kalender').val('');
        $('#judul_kegiatan').val('');
        $('#deskripsi').val('');
        $('#tanggal_mulai').val(info.dateStr);
        $('#tanggal_selesai').val(info.dateStr);
        $('#kategori').val('Kegiatan Sekolah');
        updateColorByCategory();
        $('#modalTitle').text('Tambah Kegiatan Baru');
        $('#modalTambahKegiatan').modal('show');
      }
    });

    calendar.render();

    // Auto-update color when category changes
    $('#kategori').on('change', updateColorByCategory);

    function updateColorByCategory() {
      const kategori = $('#kategori').val();
      const color = categoryColors[kategori] || '#3788d8';
      $('#warna').val(color);
    }

    // Filter by category
    $('#filterKategori').on('change', function () {
      calendar.refetchEvents();
    });

    // Reset form when modal closes
    $('#modalTambahKegiatan').on('hidden.bs.modal', function () {
      $('#formKegiatan')[0].reset();
      $('#id_kalender').val('');
      $('#modalTitle').text('Tambah Kegiatan Baru');
      updateColorByCategory();
    });

    // Format date range for display
    function formatDateRange(start, end) {
      const startDate = new Date(start);
      const endDate = new Date(end);
      endDate.setDate(endDate.getDate() - 1); // Adjust for FullCalendar exclusive end

      const options = { day: 'numeric', month: 'long', year: 'numeric' };
      const startStr = startDate.toLocaleDateString('id-ID', options);

      if (startDate.toDateString() === endDate.toDateString()) {
        return startStr;
      } else {
        const endStr = endDate.toLocaleDateString('id-ID', options);
        return startStr + ' - ' + endStr;
      }
    }

    // Edit event function
    function editEvent(eventId) {
      // Fetch event data via AJAX or from calendar
      $.ajax({
        url: 'index.php?mod=kalender_akademik&act=api',
        data: {
          start: '2000-01-01',
          end: '2099-12-31',
          id_ta: idTa
        },
        success: function (events) {
          const event = events.find(e => e.id == eventId);
          if (event) {
            $('#id_kalender').val(event.id);
            $('#judul_kegiatan').val(event.title);
            $('#deskripsi').val(event.extendedProps.deskripsi || '');
            $('#tanggal_mulai').val(event.start);

            // Adjust end date (FullCalendar end is exclusive)
            const endDate = new Date(event.end);
            endDate.setDate(endDate.getDate() - 1);
            $('#tanggal_selesai').val(endDate.toISOString().split('T')[0]);

            $('#kategori').val(event.extendedProps.kategori);
            $('#warna').val(event.backgroundColor);
            $('#modalTitle').text('Edit Kegiatan');
            $('#modalTambahKegiatan').modal('show');
          }
        }
      });
    }
  });
</script>