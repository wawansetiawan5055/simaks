</div>
<footer class="main-footer">
    <strong>Copyright &copy; <?= date('Y') ?> <a href="#">SIMAKS</a>.</strong> All rights reserved.
    <div class="float-right d-none d-sm-inline-block"><b>Version</b> 2.3.5</div>
    </footer>

</div> 
<!-- END WRAPPER -->

 <!-- Moved to end of file to ensure scripts are loaded -->

<!-- BOTTOM NAVIGATION BAR (MOBILE ONLY) -->
<nav class="mobile-bottom-nav d-md-none">
    <a href="index.php?mod=dashboard" class="nav-item <?= (!isset($_GET['mod']) || $_GET['mod']=='dashboard') ? 'active' : '' ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="index.php?mod=absensi_mapel" class="nav-item <?= (isset($_GET['mod']) && $_GET['mod']=='absensi_mapel') ? 'active' : '' ?>">
        <i class="fas fa-user-check"></i>
        <span>Absen</span>
    </a>
    <a href="javascript:void(0);" onclick="$('#mobileGridModal').modal('show');" class="nav-item">
        <div class="center-btn">
            <i class="fas fa-plus"></i>
        </div>
        <span>Menu</span>
    </a>
    <a href="javascript:void(0);" onclick="$('#modal-jadwal-mengajar').modal('show');" class="nav-item">
        <i class="fas fa-calendar-check"></i>
        <span>Jadwal</span>
    </a>
    <a href="index.php?mod=jurnal_kbm" class="nav-item <?= (isset($_GET['mod']) && $_GET['mod']=='jurnal_kbm') ? 'active' : '' ?>">
        <i class="fas fa-book"></i>
        <span>Jurnal</span>
    </a>
</nav>

<style>
/* BOTTOM NAV STYLES */
.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 65px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 1040;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.mobile-bottom-nav .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #64748b; /* Muted slate */
    text-decoration: none;
    font-size: 0.75rem;
    width: 20%;
    height: 100%;
    transition: all 0.2s ease;
}

.mobile-bottom-nav .nav-item i {
    font-size: 1.4rem;
    margin-bottom: 4px;
    transition: all 0.2s ease;
}

.mobile-bottom-nav .nav-item.active {
    color: var(--theme-accent, #3b82f6);
    font-weight: 600;
}

.mobile-bottom-nav .nav-item:active {
    transform: scale(0.9);
}

/* CENTER BUTTON (PLUS ICON) STYLING */
.center-btn {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-top: -30px; /* Float effect */
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
    border: 3px solid #fff;
    transition: all 0.2s ease;
}

.center-btn i {
    font-size: 1.4rem !important;
    margin-bottom: 0 !important;
}

.nav-item:active .center-btn {
    transform: scale(0.9) translateY(5px);
}

.mobile-bottom-nav span {
    margin-top: 2px;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<!-- Flatpickr Time Picker -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Initialize Flatpickr for all time inputs (24-hour format) -->
<script>
$(document).ready(function() {
    // Function to initialize flatpickr
    function initTimePicker() {
        $('input[type="time"]').each(function() {
            var $input = $(this);
            
            // Skip if already initialized
            if ($input.data('flatpickr-initialized')) {
                return;
            }
            
            // Get current value
            var currentValue = $input.val();
            
            // Change type to text
            $input.attr('type', 'text');
            $input.attr('placeholder', 'HH:mm');
            
            // Initialize Flatpickr
            flatpickr($input[0], {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,  // Force 24-hour format
                defaultDate: currentValue || null,
                allowInput: true,  // Allow manual typing
                minuteIncrement: 1
            });
            
            // Mark as initialized
            $input.data('flatpickr-initialized', true);
        });
    }
    
    // Initial call
    initTimePicker();
    
    // Re-initialize when modals are shown
    $(document).on('shown.bs.modal', function() {
        setTimeout(initTimePicker, 100);
    });
});
</script>



<script>
    $(document).ready(function () {
        // VARIABEL PHP (Pastikan sudah didefinisikan di dashboard.php)
        const API_URL = '<?= $api_url ?>';
        const currentTaId = <?= $current_ta_id; ?>;

        // Variabel Global untuk Data Mentah
        let rawRekapSiswaData = [];
        let rawAbsensiGuruData = [];
        let rawAbsensiSiswaData = [];

        // Variabel Chart
        let chartSiswa = null;
        let chartGuruAbsen = null;
        let chartSiswaAbsen = null;

        // --- 1. REKAP SISWA ---
        function loadRekapSiswa(id_ta = null) {
            let selected_id_ta = id_ta || $('#filter-ta').val() || currentTaId;
            if (!selected_id_ta || selected_id_ta == 0) {
                $('#rekap-siswa-table tbody').html('<tr><td colspan="7" class="text-center text-danger">Tahun Ajaran belum dipilih.</td></tr>');
                return;
            }

            $('#rekap-siswa-table tbody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

            $.getJSON(API_URL + '?mod=dashboard&act=rekap_siswa', { id_ta: selected_id_ta }, function (res) {
                if (res.status == 'ok') {
                    rawRekapSiswaData = res.data;
                    filterAndRenderRekapSiswa();
                }
            }).fail(function () {
                $('#rekap-siswa-table tbody').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data rekap siswa.</td></tr>');
            });
        }

        function filterAndRenderRekapSiswa() {
            const tingkatFilter = $('#filter-tingkat').val();
            let filteredData = rawRekapSiswaData;

            // Filter Tingkat (String Comparison agar aman untuk 'X', '10', dll)
            if (tingkatFilter !== 'all') {
                filteredData = rawRekapSiswaData.filter(row => row.tingkat.toString() == tingkatFilter);
            }

            let html = '';
            let labels = [], dataL = [], dataP = [];

            if (filteredData.length > 0) {
                filteredData.forEach((row, i) => {
                    html += `<tr>
                    <td>${i + 1}</td>
                    <td>${row.nama_kelas}</td>
                    <td class="text-center">${row.laki}</td>
                    <td class="text-center">${row.perempuan}</td>
                    <td class="text-center"><b>${row.total}</b></td>
                    <td class="text-center">${row.mutasi_masuk || 0}</td>
                    <td class="text-center">${row.mutasi_keluar || 0}</td>
                </tr>`;
                    labels.push(row.nama_kelas);
                    // Pastikan dikonversi ke Integer agar Chart membacanya sebagai angka
                    dataL.push(parseInt(row.laki));
                    dataP.push(parseInt(row.perempuan));
                });
            } else {
                html = '<tr><td colspan="7" class="text-center">Tidak ada data rekap siswa pada filter ini.</td></tr>';
            }

            $('#rekap-siswa-table tbody').html(html);
            renderBarChart('rekapSiswaChart', labels, dataL, dataP);
        }

        // --- 2. ABSENSI GURU ---
        function loadAbsensiGuru() {
            let params = getFilterParams('#filter-periode-guru', 'guru');
            params.id_ta = currentTaId;
            
            $('#rekap-absensi-guru-table tbody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

            $.getJSON(API_URL + '?mod=dashboard&act=absensi_guru', params, function (res) {
                if (res.status == 'ok') {
                    rawAbsensiGuruData = res.data;
                    filterAndRenderAbsensiGuru();
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                $('#rekap-absensi-guru-table tbody').html('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>');
            });
        }

        function filterAndRenderAbsensiGuru() {
            const guruFilter = $('#filter-guru-absen').val();
            let filteredData = rawAbsensiGuruData;
            let chartData = { H: 0, S: 0, I: 0, A: 0 };

            if (guruFilter !== 'all') {
                filteredData = rawAbsensiGuruData.filter(row => row.nama === guruFilter);
            }

            let html = '';
            if (filteredData && filteredData.length > 0) {
                filteredData.forEach(d => {
                    let total = parseInt(d.H) + parseInt(d.S) + parseInt(d.I) + parseInt(d.A);
                    let persen = total > 0 ? Math.round((d.H / total) * 100) : 0;
                    html += `<tr>
                    <td>${d.nama}</td> 
                    <td class="text-center text-success">${d.H}</td>
                    <td class="text-center text-warning">${d.S}</td>
                    <td class="text-center text-info">${d.I}</td>
                    <td class="text-center text-danger">${d.A}</td>
                    <td class="font-weight-bold text-center">${persen}%</td>
                </tr>`;

                    chartData.H += parseInt(d.H); chartData.S += parseInt(d.S);
                    chartData.I += parseInt(d.I); chartData.A += parseInt(d.A);
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>';
            }

            $('#rekap-absensi-guru-table tbody').html(html);
            renderPieChart('absensiGuruChart', chartData, chartGuruAbsen, (c) => chartGuruAbsen = c);
        }

        // --- 3. ABSENSI SISWA ---
        function loadAbsensiSiswa() {
            let params = getFilterParams('#filter-periode-siswa', 'siswa');
            params.id_ta = currentTaId;

            $('#rekap-absensi-siswa-table tbody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

            $.getJSON(API_URL + '?mod=dashboard&act=absensi_siswa', params, function (res) {
                if (res.status == 'ok') {
                    rawAbsensiSiswaData = res.data;
                    filterAndRenderAbsensiSiswa();
                }
            }).fail(function () {
                $('#rekap-absensi-siswa-table tbody').html('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>');
            });
        }

        function filterAndRenderAbsensiSiswa() {
            const kelasFilter = $('#filter-kelas-absen').val();
            let filteredData = rawAbsensiSiswaData;
            let chartData = { H: 0, S: 0, I: 0, A: 0 };

            if (kelasFilter !== 'all') {
                filteredData = rawAbsensiSiswaData.filter(row => row.nama_kelas === kelasFilter);
            }

            let html = '';
            if (filteredData && filteredData.length > 0) {
                filteredData.forEach(d => {
                    let total = parseInt(d.H) + parseInt(d.S) + parseInt(d.I) + parseInt(d.A);
                    let persen = total > 0 ? Math.round((d.H / total) * 100) : 0;
                    html += `<tr>
                    <td>${d.nama_kelas}</td>
                    <td class="text-center text-success">${d.H}</td>
                    <td class="text-center text-warning">${d.S}</td>
                    <td class="text-center text-info">${d.I}</td>
                    <td class="text-center text-danger">${d.A}</td>
                    <td class="font-weight-bold text-center">${persen}%</td>
                    <td class="text-center">
                        <button class="btn btn-xs btn-primary btn-detail-absen" data-id="${d.id_kelas}" data-nama="${d.nama_kelas}">
                            <i class="fas fa-search"></i> Detail
                        </button>
                    </td>
                </tr>`;

                    chartData.H += parseInt(d.H); chartData.S += parseInt(d.S);
                    chartData.I += parseInt(d.I); chartData.A += parseInt(d.A);
                });
            } else {
                html = '<tr><td colspan="7" class="text-center">Tidak ada data.</td></tr>';
            }

            $('#rekap-absensi-siswa-table tbody').html(html);
            renderPieChart('absensiSiswaChart', chartData, chartSiswaAbsen, (c) => chartSiswaAbsen = c);
        }

        // --- 4. DROPDOWNS ---
        function loadTahunAjaranFilter() {
            const taSelect = $('#filter-ta');
            $.getJSON(API_URL + '?mod=dashboard&act=list_ta', function (res) {
                if (res.status == 'ok' && res.data) {
                    let options = '';
                    res.data.forEach(ta => {
                        const isSelected = ta.id_ta == currentTaId ? 'selected' : '';
                        options += `<option value="${ta.id_ta}" ${isSelected}>${ta.nama_ta}</option>`;
                    });
                    taSelect.html(options);
                    loadRekapSiswa(currentTaId);
                } else {
                    taSelect.html('<option value="0">Gagal memuat TA</option>');
                    loadRekapSiswa(currentTaId);
                }
            });
        }

        function loadGuruFilter() {
            const guruSelect = $('#filter-guru-absen');
            $.getJSON(API_URL + '?mod=dashboard&act=list_guru_aktif', function (res) {
                if (res.status == 'ok' && res.data) {
                    let options = '<option value="all">-- Semua Guru --</option>';
                    res.data.forEach(guru => {
                        options += `<option value="${guru.nama}">${guru.nama}</option>`;
                    });
                    guruSelect.html(options);
                }
            });
        }

        function loadKelasFilter() {
            const kelasSelect = $('#filter-kelas-absen');
            $.getJSON(API_URL + '?mod=dashboard&act=list_kelas', function (res) {
                if (res.status == 'ok' && res.data) {
                    let options = '<option value="all">-- Semua Kelas --</option>';
                    res.data.forEach(kelas => {
                        options += `<option value="${kelas.nama_kelas}">${kelas.nama_kelas}</option>`;
                    });
                    kelasSelect.html(options);
                }
            });
        }

        // --- NEW HELPER: Get Dates from Filter UI ---
        function getFilterParams(filterIdSelector, type) {
            let mode = $(filterIdSelector).val();
            let startDate = '', endDate = '', semester = '';
            
            if (mode === 'daily') {
                startDate = $(`#date-input-${type}-daily`).val();
            } 
            else if (mode === 'monthly') {
                startDate = $(`#date-input-${type}-month`).val(); // YYYY-MM
            } 
            else if (mode === 'semester') {
                semester = $(`#date-input-${type}-semester`).val();
            }

            return { periode: mode, tanggal: startDate, semester: semester };
        }

        // --- 5. HELPER LAINNYA (UPDATED) ---
        function setupDateFilter(filterId, containerId, type) {
            $(filterId).change(function () {
                let val = $(this).val();
                let html = '';
                let today = new Date();
                let y = today.getFullYear();
                let m = String(today.getMonth() + 1).padStart(2, '0');
                let d = String(today.getDate()).padStart(2, '0');
                let currentDate = `${y}-${m}-${d}`;
                let currentMonth = `${y}-${m}`;

                if (val === 'daily') {
                    html = `<input type="date" id="date-input-${type}-daily" class="form-control form-control-sm" value="${currentDate}">`;
                } 
                else if (val === 'monthly') {
                    html = `<input type="month" id="date-input-${type}-month" class="form-control form-control-sm" value="${currentMonth}">`;
                } 
                else if (val === 'semester') {
                    let currentSmt = (parseInt(m) >= 7) ? '1' : '2';
                    html = `<select id="date-input-${type}-semester" class="form-control form-control-sm">
                                <option value="1" ${currentSmt=='1'?'selected':''}>Semester 1 (Ganjil)</option>
                                <option value="2" ${currentSmt=='2'?'selected':''}>Semester 2 (Genap)</option>
                            </select>`;
                }
                
                // Avoid flickering: only replace HTML if input group is empty or mode changed
                if ($(containerId).is(':empty') || $(containerId).data('mode') !== val) {
                    $(containerId).html(`<label>Pilih Waktu</label>` + html);
                    $(containerId).data('mode', val);
                }

                // Re-bind change events
                $(containerId).find('input, select').on('change', function() {
                    if(type.includes('guru')) loadAbsensiGuru();
                    else loadAbsensiSiswa();
                });

                if(type.includes('guru')) loadAbsensiGuru();
                else loadAbsensiSiswa();
            });
            // Initial Trigger
            $(filterId).trigger('change');
        }

        // --- DETAILED STUDENT ATTENDANCE LOGIC ---
        $(document).on('click', '.btn-detail-absen', function() {
            const idKelas = $(this).data('id');
            const namaKelas = $(this).data('nama');
            const params = getFilterParams('#filter-periode-siswa', 'siswa');
            params.id_kelas = idKelas;
            params.id_ta = currentTaId;
            
            $('#detail-kelas-nama').text(namaKelas);
            let periodText = params.periode.charAt(0).toUpperCase() + params.periode.slice(1);
            if (params.periode === 'daily') periodText += ': ' + params.tanggal;
            else if (params.periode === 'monthly') periodText += ': ' + params.tanggal;
            else if (params.periode === 'semester') periodText += ': Semester ' + params.semester;
            
            $('#detail-periode-text').text(periodText);
            $('#table-detail-siswa tbody').html('<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>');
            $('#modal-detail-absensi-siswa').modal('show');

            $.getJSON(API_URL + '?mod=dashboard&act=absensi_siswa_detail', params, function(res) {
                if (res.status === 'ok') {
                    let html = '';
                    if (res.data && res.data.length > 0) {
                        res.data.forEach((s, i) => {
                            let total = parseInt(s.H) + parseInt(s.S) + parseInt(s.I) + parseInt(s.A);
                            let persen = total > 0 ? Math.round((parseInt(s.H) / total) * 100) : 0;
                            html += `<tr>
                                <td class="text-center">${i+1}</td>
                                <td class="text-center">${s.nipd || '-'}</td>
                                <td>${s.nama}</td>
                                <td class="text-center text-success">${s.H}</td>
                                <td class="text-center text-warning">${s.S}</td>
                                <td class="text-center text-info">${s.I}</td>
                                <td class="text-center text-danger">${s.A}</td>
                                <td class="text-center font-weight-bold text-primary">${persen}%</td>
                            </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="8" class="text-center">Tidak ada data kehadiran siswa.</td></tr>';
                    }
                    $('#table-detail-siswa tbody').html(html);
                } else {
                    $('#table-detail-siswa tbody').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
                }
            });
        });

        // --- CHART CONFIGURATION (REVISI: BEGIN AT ZERO) ---
        function renderBarChart(canvasId, labels, dataL, dataP) {
            var canvas = document.getElementById(canvasId);
            if (!canvas) return; // Skip if canvas doesn't exist on this page
            if (chartSiswa) chartSiswa.destroy();
            let ctx = canvas.getContext('2d');
            chartSiswa = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Laki-laki', data: dataL, backgroundColor: '#007bff' },
                        { label: 'Perempuan', data: dataP, backgroundColor: '#dc3545' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true, // WAJIB: Agar grafik selalu mulai dari 0
                                stepSize: 1 // Agar sumbu Y selalu bilangan bulat
                            }
                        }]
                    }
                }
            });
        }

        function renderPieChart(canvasId, data, chartObj, setChartInstance) {
            var canvas = document.getElementById(canvasId);
            if (!canvas) return; // Skip if canvas doesn't exist on this page
            if (chartObj) chartObj.destroy();
            let ctx = canvas.getContext('2d');
            let newChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
                    datasets: [{
                        data: [data.H, data.S, data.I, data.A],
                        backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
            setChartInstance(newChart);
        }

        // --- INIT ---
        setupDateFilter('#filter-periode-guru', '#date-input-group-guru', 'guru');
        setupDateFilter('#filter-periode-siswa', '#date-input-group-siswa', 'siswa');

        loadTahunAjaranFilter();
        loadGuruFilter();
        loadKelasFilter();


        // EVENTS
        $('#filter-ta').on('change', function () { loadRekapSiswa($(this).val()); });
        $('#filter-tingkat').on('change', filterAndRenderRekapSiswa);

        $('#apply-filter-absensi-guru').click(loadAbsensiGuru);
        $('#filter-guru-absen').on('change', filterAndRenderAbsensiGuru);

        $('#apply-filter-absensi-siswa').click(loadAbsensiSiswa);
        $('#filter-kelas-absen').on('change', filterAndRenderAbsensiSiswa);

        // --- LOGIKA MODAL JADWAL (Paste kode ini di bagian bawah) ---
        // --- LOGIKA MODAL JADWAL (KONFLIK: Sudah ditangani di dashboard.php) ---
        // $('#modal-jadwal-mengajar').on('show.bs.modal', function (e) {
        //     ...
        // });
        // Dibatalkan agar tidak bentrok dengan logic baru di dashboard.php
    });
</script>
<!-- GLOBAL REPORT PRINT MODAL -->
<div class="modal fade" id="modalGlobalPrint" tabindex="-1" role="dialog" aria-labelledby="modalGlobalPrintLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 90%; margin: 1.75rem auto;">
        <div class="modal-content" style="height: 90vh;">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGlobalPrintLabel"><i class="fas fa-print"></i> Pratinjau Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="background: #525659;">
                <iframe id="globalPrintFrame" name="globalPrintFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Global function to show report in modal
     * @param {string} url - URL of the report to print
     * @param {string} title - Optional title for the modal
     */
    function showReportPreview(url, title = 'Pratinjau Laporan') {
        $('#modalGlobalPrintLabel').html('<i class="fas fa-print"></i> ' + title);
        $('#globalPrintFrame').attr('src', url);
        $('#modalGlobalPrint').modal('show');
    }

    // Clear iframe src when modal is closed to stop processing/playing
    $('#modalGlobalPrint').on('hidden.bs.modal', function () {
        $('#globalPrintFrame').attr('src', '');
    });
</script>

<!-- Toast Notification System -->
<script src="<?= BASE_URL; ?>assets/js/notification.js"></script>
<script>
// Pass PHP session messages to JavaScript for toast notifications
<?php
$hasMessages = isset($_SESSION['pesan_sukses']) || isset($_SESSION['pesan_error']) 
    || isset($_SESSION['pesan_warning']) || isset($_SESSION['pesan_info']);

if ($hasMessages):
?>
window.phpSessionMessages = {
    success: <?= isset($_SESSION['pesan_sukses']) ? json_encode($_SESSION['pesan_sukses']) : 'null' ?>,
    error: <?= isset($_SESSION['pesan_error']) ? json_encode($_SESSION['pesan_error']) : 'null' ?>,
    warning: <?= isset($_SESSION['pesan_warning']) ? json_encode($_SESSION['pesan_warning']) : 'null' ?>,
    info: <?= isset($_SESSION['pesan_info']) ? json_encode($_SESSION['pesan_info']) : 'null' ?>
};
<?php
    // Clear session messages after passing to JavaScript
    unset($_SESSION['pesan_sukses']);
    unset($_SESSION['pesan_error']);
    unset($_SESSION['pesan_warning']);
    unset($_SESSION['pesan_info']);
endif;
?>
</script>

<!-- SweetAlert2 for delete confirmation (still needed) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Global Delete Confirmation using SweetAlert2 (Keep this functionality) -->
<script>
$(document).ready(function() {
    // Delete confirmation for elements with .btn-delete-confirm class
    $(document).on('click', '.btn-delete-confirm', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>

<!-- Global Delete Confirmation with SweetAlert2 -->
<script>
/**
 * Global function to show SweetAlert2 delete confirmation
 * Usage from any view: 
 * <a href="delete_url" onclick="return confirmDelete(event)">Delete</a>
 */
function confirmDelete(event, customMessage = null) {
    event.preventDefault(); // Stop default navigation
    
    const deleteUrl = event.currentTarget.href || event.currentTarget.getAttribute('data-href');
    const message = customMessage || 'Data yang dihapus tidak dapat dikembalikan!';
    
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = deleteUrl;
        }
    });
    
    return false; // Prevent default link behavior
}

// Auto-attach to all links with data-confirm attribute
$(document).ready(function() {
    $('a[data-confirm="delete"], button[data-confirm="delete"]').on('click', function(e) {
        e.preventDefault();
        const message = $(this).data('message') || 'Data yang dihapus tidak dapat dikembalikan!';
        confirmDelete(e, message);
    });
});
</script>

<?php include __DIR__ . '/mobile_modals.php'; ?>
</body>
</html>