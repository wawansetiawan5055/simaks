</div>
<footer class="main-footer">
    <strong>Copyright &copy; <?= date('Y') ?> <a href="#">SIMAKS</a>.</strong> All rights reserved.
    <div class="float-right d-none d-sm-inline-block"><b>Version</b> 2.3.5</div>
</footer>

</div>
<!-- END WRAPPER -->

<!-- BOTTOM NAVIGATION BAR (MOBILE ONLY) -->
<?php
$peran_aktif = $_SESSION['peran_aktif'] ?? '';
$is_siswa = ($peran_aktif === 'Siswa' || (function_exists('has_role') && has_role('Siswa') && !has_role('Guru') && !has_role('Admin')));

$current_mod = $_GET['mod'] ?? '';
$current_act = $_GET['act'] ?? '';
?>
<nav class="mobile-bottom-nav d-md-none">
    <?php if ($is_siswa): ?>
        <a href="<?= BASE_URL ?>siswa_portal/dashboard"
            class="nav-item <?= ($current_mod === 'siswa_portal' && ($current_act === 'dashboard' || empty($current_act))) ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?= BASE_URL ?>siswa_portal/materi"
            class="nav-item <?= ($current_mod === 'siswa_portal' && $current_act === 'materi') ? 'active' : '' ?>">
            <i class="fas fa-book-open"></i>
            <span>Materi</span>
        </a>
        <a href="#" data-widget="pushmenu" class="nav-item">
            <div class="center-btn">
                <i class="fas fa-bars"></i>
            </div>
            <span>Menu</span>
        </a>
        <a href="<?= BASE_URL ?>siswa_portal/tugas"
            class="nav-item <?= ($current_mod === 'siswa_portal' && $current_act === 'tugas') ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i>
            <span>Tugas</span>
        </a>
        <a href="<?= BASE_URL ?>siswa_portal/jadwal"
            class="nav-item <?= ($current_mod === 'siswa_portal' && $current_act === 'jadwal') ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i>
            <span>Jadwal</span>
        </a>
    <?php else: ?>
        <a href="<?= BASE_URL ?>dashboard"
            class="nav-item <?= (!isset($_GET['mod']) || $_GET['mod'] == 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?= BASE_URL ?>absensi_mapel"
            class="nav-item <?= (isset($_GET['mod']) && $_GET['mod'] == 'absensi_mapel') ? 'active' : '' ?>">
            <i class="fas fa-user-check"></i>
            <span>Absen</span>
        </a>
        <a href="#" data-widget="pushmenu" class="nav-item">
            <div class="center-btn">
                <i class="fas fa-bars"></i>
            </div>
            <span>Menu</span>
        </a>
        <a href="javascript:void(0);" onclick="$('#modal-jadwal-mengajar').modal('show');" class="nav-item">
            <i class="fas fa-calendar-check"></i>
            <span>Jadwal</span>
        </a>
        <a href="<?= BASE_URL ?>jurnal_kbm"
            class="nav-item <?= (isset($_GET['mod']) && $_GET['mod'] == 'jurnal_kbm') ? 'active' : '' ?>">
            <i class="fas fa-book"></i>
            <span>Jurnal</span>
        </a>
    <?php endif; ?>
</nav>

<style>
    /* BOTTOM NAV STYLES */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        display: flex;
        justify-content: space-around;
        align-items: center;
        box-shadow: 0 -3px 14px rgba(0, 0, 0, 0.08);
        z-index: 1060;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
    }

    .mobile-bottom-nav .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #64748b;
        text-decoration: none !important;
        font-size: 0.72rem;
        font-weight: 500;
        width: 20%;
        height: 100%;
        transition: all 0.2s ease;
    }

    .mobile-bottom-nav .nav-item i {
        font-size: 1.15rem;
        margin-bottom: 2px;
        transition: transform 0.2s, color 0.2s;
    }

    .mobile-bottom-nav .nav-item.active {
        color: #2563eb;
        font-weight: 700;
    }

    .mobile-bottom-nav .nav-item.active i {
        color: #2563eb;
        transform: translateY(-2px);
    }

    .mobile-bottom-nav .center-btn {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        margin-top: -14px;
        border: 3px solid #ffffff;
        transition: transform 0.2s;
    }

    .mobile-bottom-nav .center-btn:hover {
        transform: scale(1.08);
    }

    .mobile-bottom-nav .center-btn i {
        font-size: 1rem;
        color: #ffffff !important;
        margin-bottom: 0;
    }

    .mobile-bottom-nav span {
        margin-top: 1px;
        line-height: 1;
    }
</style>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>


<!-- Initialize Flatpickr for all time inputs (24-hour format) -->
<script>
    $(document).ready(function () {
        // Function to initialize flatpickr
        function initTimePicker() {
            $('input[type="time"]').each(function () {
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
        $(document).on('shown.bs.modal', function () {
            setTimeout(initTimePicker, 100);
        });
    });
</script>



<script>
    $(document).ready(function () {
        // VARIABEL PHP (Pastikan sudah didefinisikan di dashboard.php)
        const API_URL = '<?= $api_url ?? '' ?>';
        const currentTaId = <?= $current_ta_id ?? 0; ?>;

        // DEBUG: Log API URL untuk memastikan benar
        console.log('API_URL:', API_URL);
        console.log('currentTaId:', currentTaId);

        // === HELPER FUNCTION: Build API URL dengan routing index.php ===
        function buildApiUrl(apiType, action, params = {}) {
            let url = '<?= BASE_URL ?>index.php?mod=api&type=' + encodeURIComponent(apiType) + '&act=' + encodeURIComponent(action);
            for (const [key, value] of Object.entries(params)) {
                if (value !== null && value !== '') {
                    url += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(value);
                }
            }
            return url;
        }

        // Variabel Global untuk Data Mentah
        let rawRekapSiswaData = [];
        let rawAbsensiGuruData = [];
        let rawAbsensiSiswaData = [];

        // Variabel Chart
        let chartSiswa = null;
        let chartGuruAbsen = null;
        let chartSiswaAbsen = null;

        // --- 0. SUMMARY CARDS ---
        function loadSummary(id_ta = null) {
            let selected_id_ta = id_ta || $('#filter-ta').val() || currentTaId;
            const requestUrl = buildApiUrl('dashboard', 'summary', { id_ta: selected_id_ta });

            $.getJSON(requestUrl, function (res) {
                if (res.status == 'ok' && res.data) {
                    $('#summary-total-siswa').text(res.data.total_siswa.toLocaleString());
                    $('#summary-total-guru').text(res.data.total_guru.toLocaleString());
                    $('#summary-total-kelas').text(res.data.total_kelas.toLocaleString());
                    $('#summary-total-mapel').text(res.data.total_mapel.toLocaleString());
                }
            });
        }

        // --- 1. REKAP SISWA ---
        function loadRekapSiswa(id_ta = null) {
            let selected_id_ta = id_ta || $('#filter-ta').val() || currentTaId || 0;

            $('#rekap-siswa-table tbody').html('<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

            const requestUrl = buildApiUrl('dashboard', 'rekap_siswa', { id_ta: selected_id_ta });
            console.log('Loading rekap siswa from:', requestUrl, 'with id_ta:', selected_id_ta);

            $.ajax({
                url: requestUrl,
                type: 'GET',
                data: { id_ta: selected_id_ta },
                dataType: 'json',
                success: function (res) {
                    console.log('Rekap siswa response:', res);
                    if (res.status == 'ok') {
                        rawRekapSiswaData = res.data;
                        filterAndRenderRekapSiswa();
                    } else {
                        console.error('API returned error status:', res);
                        $('#rekap-siswa-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Error: ' + (res.msg || 'Unknown error') + '</td></tr>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error loading rekap siswa:', { jqXHR, textStatus, errorThrown });
                    let errorMsg = 'Gagal memuat data rekap siswa.';
                    if (jqXHR.status === 0) {
                        errorMsg = 'Network error - periksa koneksi internet atau CORS.';
                    } else if (jqXHR.status === 404) {
                        errorMsg = 'API endpoint tidak ditemukan (404). URL: ' + requestUrl;
                    } else if (jqXHR.status === 500) {
                        errorMsg = 'Server error (500). Response: ' + jqXHR.responseText;
                    }
                    $('#rekap-siswa-table tbody').html('<tr><td colspan="8" class="text-center text-danger">' + errorMsg + '</td></tr>');
                }
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
            let totalL = 0, totalP = 0, totalSiswa = 0, totalMasuk = 0, totalKeluar = 0;

            if (filteredData.length > 0) {
                filteredData.forEach((row, i) => {
                    let l = parseInt(row.laki) || 0;
                    let p = parseInt(row.perempuan) || 0;
                    let t = parseInt(row.total) || 0;
                    let m_in = parseInt(row.mutasi_masuk) || 0;
                    let m_out = parseInt(row.mutasi_keluar) || 0;

                    totalL += l;
                    totalP += p;
                    totalSiswa += t;
                    totalMasuk += m_in;
                    totalKeluar += m_out;

                    html += `<tr>
                    <td class="text-center" style="white-space: nowrap;">${i + 1}</td>
                    <td class="text-center font-weight-bold" style="white-space: nowrap;">${row.nama_kelas}</td>
                    <td class="d-none d-md-table-cell text-left" style="white-space: nowrap;">${row.nama_wali || '-'}</td>
                    <td class="text-center" style="white-space: nowrap;">${l}</td>
                    <td class="text-center" style="white-space: nowrap;">${p}</td>
                    <td class="text-center font-weight-bold" style="white-space: nowrap;">${t}</td>
                    <td class="text-center" style="white-space: nowrap;">${m_in}</td>
                    <td class="text-center" style="white-space: nowrap;">${m_out}</td>
                </tr>`;
                    labels.push(row.nama_kelas);
                    dataL.push(l);
                    dataP.push(p);
                });

                // Add Total Row
                html += `<tr class="total-row font-weight-bold">
                    <td colspan="2" class="text-center d-table-cell d-md-none">TOTAL</td>
                    <td colspan="3" class="text-center d-none d-md-table-cell">TOTAL</td>
                    <td class="text-center">${totalL}</td>
                    <td class="text-center">${totalP}</td>
                    <td class="text-center">${totalSiswa}</td>
                    <td class="text-center">${totalMasuk}</td>
                    <td class="text-center">${totalKeluar}</td>
                </tr>`;
            } else {
                html = '<tr><td colspan="8" class="text-center">Tidak ada data rekap siswa pada filter ini.</td></tr>';
            }

            $('#rekap-siswa-table tbody').html(html);
            renderBarChart('rekapSiswaChart', labels, dataL, dataP);
        }

        // --- 2. ABSENSI GURU ---
        function loadAbsensiGuru() {
            let params = getFilterParams('#filter-periode-guru', 'guru');
            params.id_ta = $('#filter-ta').val() || currentTaId || 0;
            params.mode_kbm = $('#filter-mode-guru').val() || 'tatap_muka';

            $('#rekap-absensi-guru-table tbody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

            $.getJSON(buildApiUrl('dashboard', 'absensi_guru', params), function (res) {
                if (res.status == 'ok') {
                    rawAbsensiGuruData = res.data;
                    filterAndRenderAbsensiGuru();
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                $('#rekap-absensi-guru-table tbody').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>');
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
                filteredData.forEach((d, i) => {
                    let total = parseInt(d.H) + parseInt(d.S) + parseInt(d.I) + parseInt(d.A);
                    let persen = total > 0 ? Math.round((d.H / total) * 100) : 0;
                    html += `<tr>
                    <td class="text-center">${i + 1}</td>
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
                html = '<tr><td colspan="7" class="text-center">Tidak ada data.</td></tr>';
            }

            $('#rekap-absensi-guru-table tbody').html(html);
            renderPieChart('absensiGuruChart', chartData, chartGuruAbsen, (c) => chartGuruAbsen = c);
        }

        // --- 3. ABSENSI SISWA ---
        function loadAbsensiSiswa() {
            let params = getFilterParams('#filter-periode-siswa', 'siswa');
            params.id_ta = $('#filter-ta').val() || currentTaId || 0;

            $('#rekap-absensi-siswa-table tbody').html('<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

            $.getJSON(buildApiUrl('dashboard', 'absensi_siswa', params), function (res) {
                if (res.status == 'ok') {
                    rawAbsensiSiswaData = res.data;
                    filterAndRenderAbsensiSiswa();
                }
            }).fail(function () {
                $('#rekap-absensi-siswa-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
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
                filteredData.forEach((d, i) => {
                    let total = parseInt(d.H) + parseInt(d.S) + parseInt(d.I) + parseInt(d.A);
                    let persen = total > 0 ? Math.round((d.H / total) * 100) : 0;
                    html += `<tr>
                    <td class="text-center">${i + 1}</td>
                    <td class="text-center font-weight-bold">${d.nama_kelas}</td>
                    <td class="text-center text-success">${d.H}</td>
                    <td class="text-center text-warning">${d.S}</td>
                    <td class="text-center text-info">${d.I}</td>
                    <td class="text-center text-danger">${d.A}</td>
                    <td class="font-weight-bold text-center">${persen}%</td>
                    <td class="text-center">
                        <button class="btn btn-xs btn-primary btn-detail-absen" data-id="${d.id_kelas}" data-nama="${d.nama_kelas}">
                            <i class="fas fa-search mr-1"></i> Detail
                        </button>
                    </td>
                </tr>`;

                    chartData.H += parseInt(d.H); chartData.S += parseInt(d.S);
                    chartData.I += parseInt(d.I); chartData.A += parseInt(d.A);
                });
            } else {
                html = '<tr><td colspan="8" class="text-center">Tidak ada data.</td></tr>';
            }

            $('#rekap-absensi-siswa-table tbody').html(html);
            renderPieChart('absensiSiswaChart', chartData, chartSiswaAbsen, (c) => chartSiswaAbsen = c);
        }

        // --- 4. DROPDOWNS ---
        function loadTahunAjaranFilter() {
            const taSelect = $('#filter-ta');
            $.getJSON(buildApiUrl('dashboard', 'list_ta'), function (res) {
                if (res.status == 'ok' && res.data) {
                    let options = '';
                    res.data.forEach(ta => {
                        const isSelected = ta.id_ta == currentTaId ? 'selected' : '';
                        options += `<option value="${ta.id_ta}" ${isSelected}>${ta.nama_ta}</option>`;
                    });
                    taSelect.html(options);
                    let selectedTaId = taSelect.val() || currentTaId || 0;
                    loadRekapSiswa(selectedTaId);
                    loadSummary(selectedTaId);
                } else {
                    taSelect.html('<option value="0">Gagal memuat TA</option>');
                    loadRekapSiswa(currentTaId);
                    loadSummary(currentTaId);
                }
            });
        }

        function loadGuruFilter() {
            const guruSelect = $('#filter-guru-absen');
            $.getJSON(buildApiUrl('dashboard', 'list_guru_aktif'), function (res) {
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
            $.getJSON(buildApiUrl('dashboard', 'list_kelas'), function (res) {
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
                                <option value="1" ${currentSmt == '1' ? 'selected' : ''}>Semester 1 (Ganjil)</option>
                                <option value="2" ${currentSmt == '2' ? 'selected' : ''}>Semester 2 (Genap)</option>
                            </select>`;
                }

                // Avoid flickering: only replace HTML if input group is empty or mode changed
                if ($(containerId).is(':empty') || $(containerId).data('mode') !== val) {
                    $(containerId).html(`<label>Pilih Waktu</label>` + html);
                    $(containerId).data('mode', val);
                }

                // Re-bind change events
                $(containerId).find('input, select').on('change', function () {
                    if (type.includes('guru')) loadAbsensiGuru();
                    else loadAbsensiSiswa();
                });

                if (type.includes('guru')) loadAbsensiGuru();
                else loadAbsensiSiswa();
            });
            // Initial Trigger
            $(filterId).trigger('change');
        }

        // --- DETAILED STUDENT ATTENDANCE LOGIC ---
        $(document).on('click', '.btn-detail-absen', function () {
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

            $.getJSON(buildApiUrl('dashboard', 'absensi_siswa_detail', params), function (res) {
                if (res.status === 'ok') {
                    let html = '';
                    if (res.data && res.data.length > 0) {
                        res.data.forEach((s, i) => {
                            let total = parseInt(s.H) + parseInt(s.S) + parseInt(s.I) + parseInt(s.A);
                            let persen = total > 0 ? Math.round((parseInt(s.H) / total) * 100) : 0;
                            html += `<tr>
                                <td class="text-center">${i + 1}</td>
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
            
            // Create gradients for a premium feel
            let gradientL = ctx.createLinearGradient(0, 0, 0, 350);
            gradientL.addColorStop(0, 'rgba(79, 70, 229, 0.85)'); // Indigo
            gradientL.addColorStop(1, 'rgba(99, 102, 241, 0.3)');

            let gradientP = ctx.createLinearGradient(0, 0, 0, 350);
            gradientP.addColorStop(0, 'rgba(236, 72, 153, 0.85)'); // Pink/Rose
            gradientP.addColorStop(1, 'rgba(244, 63, 94, 0.4)');

            chartSiswa = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { 
                            label: 'Laki-laki', 
                            data: dataL, 
                            backgroundColor: gradientL, 
                            borderColor: '#4f46e5',
                            borderWidth: 1.5,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        },
                        { 
                            label: 'Perempuan', 
                            data: dataP, 
                            backgroundColor: gradientP, 
                            borderColor: '#db2777',
                            borderWidth: 1.5,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'top',
                        labels: {
                            fontFamily: "'Poppins', sans-serif",
                            fontSize: 12,
                            fontColor: '#475569',
                            boxWidth: 12,
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltips: {
                        backgroundColor: '#1e293b',
                        titleFontFamily: "'Poppins', sans-serif",
                        titleFontSize: 13,
                        titleFontColor: '#fff',
                        bodyFontFamily: "'Poppins', sans-serif",
                        bodyFontSize: 12,
                        bodySpacing: 4,
                        xPadding: 12,
                        yPadding: 10,
                        cornerRadius: 8,
                        displayColors: true
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                fontFamily: "'Poppins', sans-serif",
                                fontSize: 11,
                                fontColor: '#64748b',
                                padding: 5
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                color: '#f1f5f9',
                                zeroLineColor: '#e2e8f0'
                            },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 2,
                                fontFamily: "'Poppins', sans-serif",
                                fontSize: 11,
                                fontColor: '#64748b',
                                padding: 10
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
        loadSummary();


        // EVENTS
        $('#filter-ta').on('change', function () {
            const val = $(this).val();
            loadRekapSiswa(val);
            loadSummary(val);
        });
        $('#filter-tingkat').on('change', filterAndRenderRekapSiswa);

        $('#apply-filter-absensi-guru').click(loadAbsensiGuru);
        $('#filter-guru-absen').on('change', filterAndRenderAbsensiGuru);
        $('#filter-mode-guru').on('change', loadAbsensiGuru);

        $('#apply-filter-absensi-siswa').click(loadAbsensiSiswa);
        $('#filter-kelas-absen').on('change', filterAndRenderAbsensiSiswa);

        // --- LOGIKA MODAL JADWAL (Paste kode ini di bagian bawah) ---
        // --- GLOBAL CHAT NOTIFICATION POLLING ---
        let prevUnreadCount = 0;
        const globalSoundReceived = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');

        function checkGlobalUnread() {
            $.get('<?= BASE_URL ?>api?type=chat&act=unread_count', function (res) {
                if (res.status == 'ok') {
                    let count = parseInt(res.count);
                    if (count > 0) {
                        $('#global-chat-badge').text(count).removeClass('d-none');
                        // Play sound only if count increases (new message)
                        // AND we are NOT on the chat page (chat page has its own sound logic)
                        if (count > prevUnreadCount && window.location.href.indexOf('mod=chat') === -1) {
                            globalSoundReceived.play().catch(e => console.log('Audio blocked'));
                        }
                    } else {
                        $('#global-chat-badge').addClass('d-none');
                    }
                    prevUnreadCount = count;
                }
            });
        }
        setInterval(checkGlobalUnread, 10000); // Check every 10 seconds
        checkGlobalUnread();
    });
</script>
<!-- 2. SUBMENU MODAL -->
<div class="modal fade" id="submenuModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 10500;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" id="submenuContent"></div>
    </div>
</div>

<!-- GLOBAL PREVIEW MODAL (Standardized Style 1: Dark Header + Iframe) -->
<div class="modal fade" id="modalGlobalPreview" tabindex="-1" role="dialog" aria-labelledby="modalGlobalPreviewLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 1150px; width: 95%;">
        <div class="modal-content" style="height: 90vh; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div class="modal-header bg-dark text-white p-2">
                <h5 class="modal-title" id="modalGlobalPreviewLabel" style="font-size: 1rem;">
                    <i class="fas fa-eye mr-2"></i> <span id="globalPreviewTitle">Pratinjau</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    onclick="$('#modalGlobalPreview').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="globalPreviewBody"
                style="background: #525659; height: 100%; width: 100%; overflow: hidden;">
                <!-- Content injected via JS -->
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Unified Global Preview Function
     * @param {string} url - URL or Path to the file/page
     * @param {string} type - 'image', 'pdf', 'iframe', 'print'
     * @param {string} title - Optional title for the modal header
     */
    function showGlobalPreview(url, type = 'iframe', title = 'Pratinjau') {
        $('#globalPreviewTitle').text(title);
        var content = '';
        var $body = $('#globalPreviewBody');

        // Clear previous content
        $body.empty();
        $body.css({ 'display': 'block', 'align-items': 'initial', 'justify-content': 'initial' });

        const ext = url.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(ext);
        const isWord = ['doc', 'docx'].includes(ext);
        const isPPT = ['ppt', 'pptx'].includes(ext);
        const isVideo = ['mp4', 'webm', 'ogg'].includes(ext);

        if (type === 'image' || isImage) {
            // Image Preview
            $body.css({ 'display': 'flex', 'align-items': 'center', 'justify-content': 'center' });
            content = '<img src="' + url + '" style="max-width: 100%; max-height: 100%; object-fit: contain; box-shadow: 0 0 20px rgba(0,0,0,0.5);">';
            $body.html(content);
        } else if (isVideo) {
            // Video Preview
            $body.css({ 'display': 'flex', 'align-items': 'center', 'justify-content': 'center' });
            content = `<video controls autoplay style="max-width: 100%; max-height: 100%; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.5);">
                        <source src="${url}" type="video/${ext}">
                        Browser Anda tidak mendukung pemutaran video.
                       </video>`;
            $body.html(content);
        } else {
            // Iframe Preview (HTML / PDF / Word / PPT)
            let finalUrl = url;
            if (isWord || isPPT) {
                let absoluteUrl = url;
                if (!url.startsWith('http')) {
                    absoluteUrl = window.location.origin + '/' + url.replace(/^\//, '');
                }
                finalUrl = 'https://docs.google.com/viewer?url=' + encodeURIComponent(absoluteUrl) + '&embedded=true';
            }

            var iframe = document.createElement('iframe');
            iframe.src = finalUrl;
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            iframe.style.border = 'none';
            iframe.setAttribute('allowfullscreen', 'true');

            $body.append(iframe);
        }

        $('#modalGlobalPreview').modal('show');
    }

    // Legacy Bridge (jika ada kode lama yang panggil showReportPreview)
    function showReportPreview(url, title = 'Pratinjau Laporan') {
        showGlobalPreview(url, 'iframe', title);
    }

    // Clear content on close to stop playing/loading
    $('#modalGlobalPreview').on('hidden.bs.modal', function () {
        $('#globalPreviewBody').empty();
    });
</script>

<!-- Toast Notification System -->
<!-- Modern Notification System Integration -->
<script src="<?= BASE_URL; ?>assets/js/notification.js"></script>
<script>
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
        // Clear session messages after passing to JavaScript layer
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
    $(document).ready(function () {
        // Delete confirmation for elements with .btn-delete-confirm class
        $(document).on('click', '.btn-delete-confirm', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
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
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#94a3b8',
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
    $(document).ready(function () {
        $('a[data-confirm="delete"], button[data-confirm="delete"]').on('click', function (e) {
            e.preventDefault();
            const message = $(this).data('message') || 'Data yang dihapus tidak dapat dikembalikan!';
            confirmDelete(e, message);
        });
    });
</script>

<!-- Summernote WYSIWYG Editor JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<!-- KaTeX & Summernote Math Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.js"></script>
<script src="<?= BASE_URL; ?>assets/js/summernote-math.js"></script>
<script>
    $(document).ready(function () {
        if ($('.summernote').length > 0) {
            $('.summernote').summernote({
                height: 300,
                placeholder: 'Tuliskan isi surat / template di sini...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'math']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }
    });
</script>

<!-- MathJax untuk render rumus matematika dari AI (Gemini, ChatGPT, dll) -->
<script>
    window.MathJax = {
        tex: {
            inlineMath: [['$', '$'], ['\\(', '\\)']],
            displayMath: [['$$', '$$'], ['\\[', '\\]']]
        },
        svg: {
            fontCache: 'global'
        }
    };
</script>
<script type="text/javascript" id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js">
</script>

<script>
window.renderMath = function(targetEl = null) {
    if (window.MathJax && window.MathJax.typesetPromise) {
        if (targetEl) {
            MathJax.typesetPromise([targetEl]).catch(function(err){ console.log('MathJax:', err); });
        } else {
            MathJax.typesetPromise().catch(function(err){ console.log('MathJax:', err); });
        }
    }
};
$(document).ready(function() {
    setTimeout(function() {
        window.renderMath();
    }, 300);
});
</script>
<?php include __DIR__ . '/mobile_modals.php'; ?>
</body>

</html>