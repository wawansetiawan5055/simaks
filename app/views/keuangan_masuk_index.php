<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<!-- Flatpickr Month Select Plugin -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Transaksi Pemasukan &amp; Pendapatan
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm font-weight-bold" onclick="showAddModal()">
                    <i class="fas fa-plus-circle mr-1"></i> Input Pemasukan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-success shadow">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-1"></i> Riwayat Pemasukan</h3>
                <div class="card-tools">
                    <form action="" method="get" class="form-inline">
                        <input type="hidden" name="mod" value="keuangan_transaksi_masuk">
                        <div class="input-group input-group-sm">
                            <input type="text" id="filter_bulan_picker" class="form-control form-control-sm bg-white"
                                readonly placeholder="Pilih Bulan"
                                value="<?= DateHelper::getNamaBulan((int) ($bulan ?? date('m'))) . ' ' . ($tahun ?? date('Y')) ?>">
                            <input type="hidden" name="bulan" id="filter_bulan" value="<?= $bulan ?? date('m') ?>">
                            <input type="hidden" name="tahun" id="filter_tahun" value="<?= $tahun ?? date('Y') ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-filter"></i>
                                    Filter</button>
                                <button type="button" class="btn btn-secondary ml-1" onclick="printGrouped()"
                                    title="Cetak Gabungan (Pilih Baris)">
                                    <i class="fas fa-print mr-2"></i> Cetak Gabungan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped table-hover text-nowrap" id="table-transaksi"
                    style="width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th width="30px" class="text-center">
                                <input type="checkbox" id="check-all" onclick="toggleSelectAll()">
                            </th>
                            <th width="5%" class="text-center">No</th>
                            <th class="text-center">No Bukti</th>
                            <th>Tanggal</th>
                            <th width="10%" class="text-center">Kode</th>
                            <th>Jenis Pembayaran</th>
                            <th>Nama (Siswa/Payer)</th>
                            <th>Masuk Ke</th>
                            <th>Jumlah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transaksi)): ?>
                            <?php $no = 1;
                            foreach ($transaksi as $row): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="check-item" value="<?= $row['id_transaksi'] ?>"
                                            data-id_siswa="<?= $row['id_siswa'] ?>">
                                    </td>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="font-weight-bold text-primary"><?= $row['no_bukti'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="text-center font-weight-bold"><?= $row['kode_akun'] ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= $row['nama_jenis'] ?></span>
                                        <?php if (!empty($row['periode']) && $row['is_recurring'] == 1):
                                            // Format periode (YYYY-MM) to month name
                                            $bulanMap = [
                                                '01' => 'Januari',
                                                '02' => 'Februari',
                                                '03' => 'Maret',
                                                '04' => 'April',
                                                '05' => 'Mei',
                                                '06' => 'Juni',
                                                '07' => 'Juli',
                                                '08' => 'Agustus',
                                                '09' => 'September',
                                                '10' => 'Oktober',
                                                '11' => 'November',
                                                '12' => 'Desember'
                                            ];
                                            $bulanCode = substr($row['periode'], 5, 2);
                                            $namaBulan = $bulanMap[$bulanCode] ?? $bulanCode;
                                            ?>
                                            <br><small class="text-muted font-weight-bold"><?= strtoupper($namaBulan) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">
                                            <?php if ($row['nama_siswa']): ?>
                                                <i class="fas fa-user-graduate text-success"></i> <?= $row['nama_siswa'] ?>
                                            <?php elseif ($row['referensi']): ?>
                                                <i class="fas fa-user text-muted"></i> <?= $row['referensi'] ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= $row['nama_rekening'] ?></td>
                                    <td class="text-success font-weight-bold">Rp
                                        <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-default mb-1" title="Cetak Kwitansi"
                                            onclick="printKwitansi(<?= $row['id_transaksi'] ?>)"><i
                                                class="fas fa-print"></i></button>
                                        <button class="btn btn-xs btn-warning mb-1" title="Edit Transaksi"
                                            onclick='editTransaksi(<?= json_encode($row) ?>)'><i
                                                class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-3">Belum ada data periode ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- MODAL INPUT PEMASUKAN -->
<div class="modal fade" id="modal-transaksi" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Input Pemasukan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="form-transaksi">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_transaksi">
                    <input type="hidden" name="tipe" value="MASUK">
                    <input type="hidden" name="id_transaksi" id="id_transaksi">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Tanggal Transaksi <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Masuk ke Rekening <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" name="id_rekening" required>
                                    <?php foreach ($rekening_list as $r): ?>
                                        <option value="<?= $r['id_rekening'] ?>"><?= $r['nama_rekening'] ?>
                                            (<?= $r['tipe'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Pilih Kelas</label>
                                <select class="form-control" id="filter_id_kelas">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($kelas_list as $k): ?>
                                        <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Pilih Siswa (Opsional)</label>
                                <select class="form-control" name="id_siswa" id="id_siswa">
                                    <option value="">-- Pilih Kelas Dahulu --</option>
                                </select>
                                <small class="text-muted" style="font-size: 0.7rem;">Kosongkan jika pemasukan
                                    umum</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Jenis Pemasukan <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_jenis" id="id_jenis" required>
                                    <option value="">-- Pilih Siswa Dahulu --</option>
                                    <?php
                                    $current_cat = "";
                                    foreach ($jenis_list as $j):
                                        if ($current_cat != $j['nama_kategori']) {
                                            if ($current_cat != "")
                                                echo "</optgroup>";
                                            echo "<optgroup label='" . $j['nama_kategori'] . "'>";
                                            $current_cat = $j['nama_kategori'];
                                        }
                                        ?>
                                        <option value="<?= $j['id_jenis'] ?>"
                                            data-harga="<?= intval($j['harga_default']) ?>"
                                            data-recurring="<?= $j['is_recurring'] ?? 0 ?>">
                                            [<?= $j['kode_akun'] ?>] <?= $j['nama_jenis'] ?>
                                        </option>
                                    <?php endforeach;
                                    if ($current_cat != "")
                                        echo "</optgroup>"; ?>
                                </select>
                            </div>

                            <div class="form-group shadow-none mb-3" id="group_bulan"
                                style="display:none; background: #fff3cd; padding: 12px; border-radius: 8px; border: 1px solid #ffeeba;">
                                <label class="small fw-bold">Periode Pembayaran (Range Bulan)</label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="text" class="form-control" id="bulan_awal_picker"
                                                placeholder="Dari..." readonly>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fas fa-arrow-right"></i></span>
                                            <input type="text" class="form-control" id="bulan_akhir_picker"
                                                placeholder="Ke..." readonly>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="bulan_awal" id="bulan_awal">
                                <input type="hidden" name="tahun_awal" id="tahun_awal">
                                <input type="hidden" name="bulan_akhir" id="bulan_akhir">
                                <input type="hidden" name="tahun_akhir" id="tahun_akhir">
                                <small class="text-muted" style="font-size: 0.7rem;">Pilih range bulan untuk pembayaran
                                    multi-periode.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Jumlah (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control font-weight-bold text-success" name="jumlah"
                                    id="jumlah" min="0" required placeholder="0" style="font-size: 1.2rem;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Metode Pembayaran</label>
                                <select class="form-control" name="metode_pembayaran">
                                    <option value="TUNAI">Tunai / Cash</option>
                                    <option value="TRANSFER">Transfer Bank</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Keterangan / Catatan</label>
                                <textarea class="form-control" name="keterangan" rows="1"
                                    placeholder="Catatan singkat (Opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Pemasukan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include '../app/views/partials/footer.php'; ?>

<!-- Flatpickr Locale -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    $(document).ready(function () {
        var filterKelas = document.getElementById('filter_id_kelas');
        var siswaSelect = document.getElementById('id_siswa');
        var jenisSelect = document.getElementById('id_jenis');
        var jumlahInput = document.getElementById('jumlah');
        var form = document.getElementById('form-transaksi');
        var keteranganInput = form.querySelector('textarea[name="keterangan"]');

        var matrixData = []; // Store active matrix for selected student

        if (filterKelas) {
            filterKelas.addEventListener('change', function () {
                var id_kelas = this.value;
                siswaSelect.innerHTML = '<option value="">Loading...</option>';
                matrixData = []; // Reset matrix

                if (id_kelas) {
                    fetch('<?= BASE_URL ?>keuangan_get_siswa?id_kelas=' + id_kelas)
                        .then(function (response) { return response.json(); })
                        .then(function (res) {
                            var html = '<option value="">-- Pilih Siswa --</option>';
                            if (res.status === 'ok' && res.data && res.data.length > 0) {
                                res.data.forEach(function (siswa) {
                                    html += '<option value="' + siswa.id_siswa + '">' + (siswa.nama || 'No Name') + ' (' + (siswa.nisn || '-') + ')</option>';
                                });
                            } else {
                                html += '<option value="">Tidak ada siswa di kelas ini (' + (res.ta_nama || '') + ')</option>';
                            }
                            siswaSelect.innerHTML = html;
                            // Trigger reset of payment types if student is cleared
                            updateJenisDropdown();
                        })
                        .catch(function (error) {
                            console.error('Fetch Error:', error);
                            siswaSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                        });
                } else {
                    siswaSelect.innerHTML = '<option value="">-- Pilih Kelas Terlebih Dahulu --</option>';
                    updateJenisDropdown();
                }
            });
        }

        // Capture original options for restoration
        var allJenisOptionsHtml = jenisSelect.innerHTML;

        function updateJenisDropdown() {
            if (matrixData.length > 0) {
                var html = '<option value="">-- Pilih Jenis (Matrix Aktif) --</option>';
                var current_cat = "";
                matrixData.forEach(function (item) {
                    if (current_cat != item.nama_kategori) {
                        if (current_cat != "") html += "</optgroup>";
                        html += "<optgroup label='" + item.nama_kategori + "'>";
                        current_cat = item.nama_kategori;
                    }
                    html += '<option value="' + item.id_jenis + '" ' +
                        'data-harga="' + item.nominal + '" ' +
                        'data-recurring="' + item.is_recurring + '" ' +
                        'data-matrix="1">' +
                        '[' + item.kode_kategori + '] ' + item.nama_jenis + (item.nominal != item.harga_default ? ' (Tarif Khusus)' : '') +
                        '</option>';
                });
                if (current_cat != "") html += "</optgroup>";
                jenisSelect.innerHTML = html;
            } else {
                // Restore all if no student/matrix
                jenisSelect.innerHTML = allJenisOptionsHtml;
            }
        }

        if (siswaSelect) {
            siswaSelect.addEventListener('change', function () {
                var id_siswa = this.value;
                if (id_siswa) {
                    fetch('<?= BASE_URL ?>keuangan_get_siswa_matrix?id_siswa=' + id_siswa)
                        .then(function (response) { return response.json(); })
                        .then(function (res) {
                            if (res.status === 'ok') {
                                matrixData = res.data;
                                updateJenisDropdown();
                            }
                        });
                } else {
                    matrixData = [];
                    updateJenisDropdown();
                }
            });
        }


        if (jenisSelect) {
            jenisSelect.addEventListener('change', function () {
                var selected = this.options[this.selectedIndex];
                var harga = selected.getAttribute('data-harga');
                var isRecurring = selected.getAttribute('data-recurring');
                var groupBulan = document.getElementById('group_bulan');
                var bulanInput = document.getElementById('bulan');

                // Show/Hide Bulan field for recurring payments (SPP)
                if (isRecurring == '1') {
                    groupBulan.style.display = 'block';
                    document.getElementById('bulan_awal').setAttribute('required', 'required');
                } else {
                    groupBulan.style.display = 'none';
                    document.getElementById('bulan_awal').removeAttribute('required');
                    document.getElementById('bulan_awal').value = '';
                    document.getElementById('bulan_akhir').value = '';
                }

                // Auto-fill price
                calculateAmount();
            });
        }

        function calculateAmount() {
            var selected = jenisSelect.options[jenisSelect.selectedIndex];
            if (!selected || !selected.value) return;

            var baseHarga = parseInt(selected.getAttribute('data-harga') || 0);
            var isRecurring = selected.getAttribute('data-recurring');
            var isMatrix = selected.getAttribute('data-matrix');

            var m_start = document.getElementById('bulan_awal').value;
            var y_start = document.getElementById('tahun_awal').value;
            var m_end = document.getElementById('bulan_akhir').value || m_start;
            var y_end = document.getElementById('tahun_akhir').value || y_start;

            if (isRecurring != '1') {
                jumlahInput.value = baseHarga;
                return;
            }

            if (!m_start || !y_start) {
                jumlahInput.value = 0;
                return;
            }

            // Generate months in range
            var start = new Date(y_start, parseInt(m_start) - 1, 1);
            var end = new Date(y_end, parseInt(m_end) - 1, 1);

            if (end < start) end = start; // Guard against end < start

            var totalHarga = 0;
            var listFree = [];
            var countMonths = 0;

            var current = new Date(start);
            while (current <= end) {
                countMonths++;
                var m_current = current.getMonth() + 1;
                var monthHarga = baseHarga; // RESET to default/base for EACH month (FIX BUG)

                if (isMatrix == '1' && matrixData.length > 0) {
                    var rule = matrixData.find(function (m) { return m.id_jenis == selected.value; });
                    if (rule && rule.keterangan) {
                        try {
                            var config = JSON.parse(rule.keterangan);
                            // The matrix rule stores active months in 'months' property
                            var activeMonths = (config.months || config.active_months || []).map(Number);

                            if (!activeMonths.includes(Number(m_current))) {
                                monthHarga = 0; // Free month
                                listFree.push(current.toLocaleString('id-ID', { month: 'long' }));
                            } else {
                                // If active, use matrix nominal (might be discount)
                                monthHarga = parseInt(rule.nominal);
                            }
                        } catch (e) {
                            console.error("Matrix parse error:", e);
                        }
                    }
                }

                totalHarga += monthHarga;
                current.setMonth(current.getMonth() + 1);
                if (countMonths > 24) break; // Infinite loop safety
            }

            jumlahInput.value = totalHarga;

            // Auto-fill Keterangan
            var ket = "";
            if (listFree.length > 0) {
                ket += "Pembebasan Biaya (Bulan Gratis: " + listFree.join(', ') + "). ";
            }

            if (countMonths > 1) {
                var startTxt = document.getElementById('bulan_awal_picker').value;
                var endTxt = document.getElementById('bulan_akhir_picker').value;
                ket += "Pembayaran Multi-Periode (" + startTxt + " - " + endTxt + ")";
            }

            keteranganInput.value = ket;
        }

        // Initialize Flatpickr for Month Picker
        initMonthPicker();

        function initMonthPicker() {
            // Start Month
            flatpickr("#bulan_awal_picker", {
                plugins: [new monthSelectPlugin({ shorthand: true, dateFormat: "F Y", altFormat: "F Y", theme: "light" })],
                locale: "id",
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        var d = selectedDates[0];
                        document.getElementById('bulan_awal').value = (d.getMonth() + 1).toString().padStart(2, '0');
                        document.getElementById('tahun_awal').value = d.getFullYear();
                        calculateAmount();
                    }
                }
            });

            // End Month
            flatpickr("#bulan_akhir_picker", {
                plugins: [new monthSelectPlugin({ shorthand: true, dateFormat: "F Y", altFormat: "F Y", theme: "light" })],
                locale: "id",
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        var d = selectedDates[0];
                        document.getElementById('bulan_akhir').value = (d.getMonth() + 1).toString().padStart(2, '0');
                        document.getElementById('tahun_akhir').value = d.getFullYear();
                        calculateAmount();
                    }
                }
            });
        }

        // Simplified: Removal of auto-fill on hidden ID change as it's handled in calculateAmount

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                var btn = form.querySelector('button[type="submit"]');
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                var formData = new FormData(form);

                fetch('<?= BASE_URL ?>keuangan_masuk/save', {
                    method: 'POST',
                    body: formData
                })
                    .then(function (response) { return response.json(); })
                    .then(function (res) {
                        if (res.success) {
                            alert('Berhasil: ' + res.message);
                            location.reload();
                        } else {
                            alert('Gagal: ' + res.message);
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    })
                    .catch(function (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan sistem.');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
            });
        }
    });

    function showAddModal() {
        document.getElementById('form-transaksi').reset();
        document.getElementById('id_transaksi').value = '';
        document.getElementById('id_siswa').innerHTML = '<option value="">-- Pilih Kelas Terlebih Dahulu --</option>';
        document.getElementById('filter_id_kelas').value = '';
        $('.modal-title').html('<i class="fas fa-plus-circle"></i> Input Pemasukan');
        if (typeof $ !== 'undefined') {
            $('#modal-transaksi').modal('show');
        }
    }

    function editTransaksi(data) {
        $('#form-transaksi')[0].reset();
        $('#id_transaksi').val(data.id_transaksi);
        $('input[name="tanggal"]').val(data.tanggal);
        $('#id_jenis').val(data.id_jenis);
        $('select[name="id_rekening"]').val(data.id_rekening);
        $('textarea[name="keterangan"]').val(data.keterangan);
        $('#jumlah').val(parseInt(data.jumlah));
        $('select[name="metode_pembayaran"]').val(data.metode_pembayaran);

        // Handle Siswa
        if (data.id_siswa) {
            var siswaSelect = $('#id_siswa');
            // Create option manually to bypass filter load
            siswaSelect.html('<option value="' + data.id_siswa + '" selected>' + (data.nama_siswa || 'Siswa Terpilih') + '</option>');
        } else {
            $('#id_siswa').html('<option value="">-- Pilih Kelas Terlebih Dahulu --</option>');
        }

        // Handle Period (if recurring)
        if (data.periode) {
            var parts = data.periode.split('-');
            var y = parts[0];
            var m = parts[1];

            document.getElementById('bulan_awal').value = m;
            document.getElementById('tahun_awal').value = y;

            // Format for picker display
            var bulanNames = {
                '01': 'Januari', '02': 'Februari', '03': 'Maret', '04': 'April',
                '05': 'Mei', '06': 'Juni', '07': 'Juli', '08': 'Agustus',
                '09': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
            };
            document.getElementById('bulan_awal_picker').value = bulanNames[m] + ' ' + y;

            // Also set bulan_akhir to same to avoid range issues on single edit
            document.getElementById('bulan_akhir').value = m;
            document.getElementById('tahun_akhir').value = y;
            document.getElementById('bulan_akhir_picker').value = bulanNames[m] + ' ' + y;
        }

        // Reset Filter Kelas because we bypassed it
        $('#filter_id_kelas').val('');

        $('.modal-title').html('<i class="fas fa-edit"></i> Edit Pemasukan');

        // Trigger display logic for recurring group
        var selectedJ = document.getElementById('id_jenis').options[document.getElementById('id_jenis').selectedIndex];
        if (selectedJ) {
            var isRecurring = selectedJ.getAttribute('data-recurring');
            document.getElementById('group_bulan').style.display = (isRecurring == '1') ? 'block' : 'none';
        }

        $('#modal-transaksi').modal('show');

        // Re-init month picker on shown to ensure z-index and interaction work after modal animation
        $('#modal-transaksi').one('shown.bs.modal', function () {
            initMonthPicker();
        });
    }


    function printKwitansi(id) {
        if (typeof showGlobalPreview === 'function') {
            showGlobalPreview('<?= BASE_URL ?>keuangan_masuk_print?id=' + id, 'iframe', 'Cetak Kwitansi');
        } else {
            window.open('<?= BASE_URL ?>keuangan_masuk_print?id=' + id, '_blank');
        }
    }

    function toggleSelectAll() {
        var checkAll = document.getElementById('check-all');
        var items = document.querySelectorAll('.check-item');
        items.forEach(function (item) {
            item.checked = checkAll.checked;
        });
    }

    function printGrouped() {
        var checked = document.querySelectorAll('.check-item:checked');
        if (checked.length === 0) {
            toast_warning('Silakan pilih minimal satu transaksi untuk dicetak.');
            return;
        }

        var ids = [];
        var firstSiswa = null;
        var isConsistent = true;

        checked.forEach(function (item) {
            var id_siswa = item.getAttribute('data-id_siswa');
            if (firstSiswa === null) firstSiswa = id_siswa;
            if (id_siswa !== firstSiswa) isConsistent = false;
            ids.push(item.value);
        });

        if (!isConsistent) {
            toast_error('Kwitansi gabungan hanya bisa mencetak transaksi milik satu siswa yang sama.');
            return;
        }

        if (typeof showGlobalPreview === 'function') {
            showGlobalPreview('<?= BASE_URL ?>keuangan_masuk_print?id=' + ids.join(','), 'iframe', 'Cetak Kwitansi Gabungan');
        } else {
            window.open('<?= BASE_URL ?>keuangan_masuk_print?id=' + ids.join(','), '_blank');
        }
    }

    // Initializer for Filter Month Picker
    $(document).ready(function () {
        flatpickr("#filter_bulan_picker", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "F Y",
                    altFormat: "F Y",
                    theme: "light"
                })
            ],
            locale: "id",
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    var d = selectedDates[0];
                    var m = (d.getMonth() + 1).toString().padStart(2, '0');
                    var y = d.getFullYear();

                    document.getElementById('filter_bulan').value = m;
                    document.getElementById('filter_tahun').value = y;
                }
            }
        });
    });
</script>