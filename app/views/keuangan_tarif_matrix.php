<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<style>
    /* Premium Finance Dashboard Adjustments */
    .filter-card-premium {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        border-radius: 15px !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
    }

    .filter-icon-box {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    .form-select-premium {
        height: 42px !important;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding-left: 15px !important;
        background-color: #ffffff !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        transition: all 0.3s ease;
    }

    .form-select-premium:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
    }

    /* Style Box Nominal (Style Awal) */
    .cell-nominal {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 6px 2px;
        cursor: pointer;
        background-color: #ffffff;
        color: #212529;
        transition: all 0.2s ease;
        user-select: none; 
        font-weight: 600;
        min-height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }

    /* Style saat cell AKTIF (Tercentang) */
    .cell-nominal.active {
        background-color: #e7f1ff; /* Biru Muda */
        border-color: #0d6efd;     /* Biru Primary */
        color: #0d6efd;
        box-shadow: inset 0 0 0 1px #0d6efd;
    }

    .cell-nominal:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
    }

    .badge-warning-custom {
        position: absolute;
        top: -5px;
        right: -5px;
        font-size: 0.6rem;
        padding: 2px 5px;
        z-index: 2;
    }
</style>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="fas fa-th mr-2"></i> Matrix Aktivasi & Tarif</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>keuangan_tarif/index">Tarif Khusus</a></li>
                <li class="breadcrumb-item active">Matrix Kelas</li>
            </ol>
        </nav>
    </div>

        <div class="card filter-card-premium border-0 mb-4">
            <div class="card-body py-3 px-4">
                <form method="GET" action="index.php" class="row align-items-center">
                    <input type="hidden" name="mod" value="keuangan_tarif">
                    <input type="hidden" name="act" value="matrix">
                    
                    <div class="col-md-auto d-flex align-items-center mb-2 mb-md-0">
                        <div class="filter-icon-box mr-3">
                            <i class="fas fa-filter"></i>
                        </div>
                        <span class="h5 mb-0 font-weight-bold text-dark">Filter Kelas</span>
                    </div>
 
                    <div class="col-md-4">
                        <select class="form-select form-select-premium shadow-none w-100" name="id_kelas" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= $id_kelas == $k['id_kelas'] ? 'selected' : '' ?>>
                                    <?= $k['nama_kelas'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($id_kelas && !empty($students)): ?>
        <form action="<?= BASE_URL ?>keuangan_tarif/save_matrix" method="POST" id="formMatrix" onsubmit="return prepareSubmit()">
            <input type="hidden" name="id_kelas" value="<?= $id_kelas ?>">
            <input type="hidden" name="action_type" id="action_type" value="save_rule">
            <input type="hidden" name="matrix_data" id="matrix_data">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">Matrix Pembiayaan: <?= htmlspecialchars($selectedClassInfo['nama_kelas'] ?? 'Kelas Terpilih') ?></h5>
                        
                    </div>

                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-bordered table-sm table-hover align-middle head-fixed mb-0 text-center">
                            <thead class="bg-light small position-sticky top-0 shadow-sm" style="z-index: 10;">
                                <tr>
                                    <th rowspan="3" style="width: 50px;" class="align-middle table-light">NO</th>
                                    <th rowspan="3" style="min-width: 250px;" class="align-middle table-light text-start">NAMA SISWA</th>
                                    <th colspan="<?= count($jenisList) ?>" class="py-2 table-light">JENIS PEMBAYARAN</th>
                                </tr>
                                
                                <tr>
                                    <?php foreach ($jenisList as $jenis): ?>
                                    <th style="min-width: 150px;" class="align-middle pb-2 bg-white">
                                        <div class="text-uppercase mb-1" style="font-size: 0.75rem;"><?= $jenis['nama_jenis'] ?></div>
                                        <div class="badge bg-secondary fw-normal"><?= number_format($jenis['harga_default'],0,',','.') ?></div>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>

                                <tr>
                                    <?php foreach ($jenisList as $jenis): ?>
                                    <th class="p-0 bg-light text-center border-top" style="border-top: 2px solid #dee2e6 !important;">
                                        <div class="d-flex justify-content-center align-items-center w-100" style="height: 32px;">
                                            <input class="form-check-input m-0" type="checkbox" 
                                                   onchange="toggleColumn(this, <?= $jenis['id_jenis'] ?>)"
                                                   style="cursor: pointer; transform: scale(1.2); transform-origin: center; display: block;"
                                                   title="Pilih Semua">
                                        </div>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            
                            <tbody class="small bg-white">
                                <?php foreach ($students as $i => $s): ?>
                                <tr>
                                    <td class="text-center align-middle bg-light fw-bold"><?= $i + 1 ?></td>
                                    <td class="align-middle text-start">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($s['nama']) ?></div>
                                        <div class="text-muted" style="font-size:0.75rem"><?= $s['nisn'] ?? '-' ?></div>
                                    </td>
                                    <?php foreach ($jenisList as $j): 
                                        $jid = $j['id_jenis'];
                                        $sid = $s['id_siswa'];
                                        $existing = $existingTarifs[$sid][$jid] ?? null;
                                        $isActive = ($existing !== null); 
                                        $val = $existing['nominal'] ?? $j['harga_default'];
                                        $activeMonths = $existing['months'] ?? range(1, 12); 
                                        $isRecurring = ($j['is_recurring'] == 1);
                                    ?>
                                    <td class="p-2 position-relative">
                                        <input type="checkbox" id="chk_<?= $sid ?>_<?= $jid ?>"
                                               class="chk-col-<?= $jid ?> d-none matrix-active" 
                                               data-sid="<?= $sid ?>" data-jid="<?= $jid ?>"
                                               value="1" <?= $isActive ? 'checked' : '' ?>>

                                        <div id="box_<?= $sid ?>_<?= $jid ?>" 
                                             onclick="toggleItem('<?= $sid ?>', '<?= $jid ?>')"
                                             class="cell-nominal <?= $isActive ? 'active' : '' ?>"
                                             style="position: relative;">
                                            
                                            <span id="txt_<?= $sid ?>_<?= $jid ?>" 
                                                  onclick="event.stopPropagation(); openEditModal(<?= $sid ?>, <?= $jid ?>, '<?= htmlspecialchars(addslashes($s['nama']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($j['nama_jenis']), ENT_QUOTES) ?>', <?= $isRecurring ? 1 : 0 ?>)"
                                                  title="Klik untuk Edit Tarif"
                                                  style="z-index: 5; position: relative; padding: 5px;">
                                                <?= number_format($val, 0, ',', '.') ?>
                                            </span>

                                            <?php if ($isActive && $isRecurring && count($activeMonths) < 12): ?>
                                                <span class="badge rounded-pill bg-danger badge-warning-custom">!</span>
                                            <?php endif; ?>
                                        </div>

                                        <input type="hidden" class="matrix-nominal" 
                                               id="val_<?= $sid ?>_<?= $jid ?>" value="<?= number_format($val, 0, ',', '.') ?>">
                                        <div id="months_container_<?= $sid ?>_<?= $jid ?>" class="matrix-months-container">
                                            <?php foreach($activeMonths as $m): ?>
                                            <input type="hidden" class="month-val" value="<?= $m ?>">
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-end gap-2 sticky-bottom bg-white border-top shadow-sm p-3">
                    <button type="submit" class="btn btn-primary px-4" onclick="document.getElementById('action_type').value='save_rule'">
                        <i class="fas fa-save mr-2"></i> Simpan Matriks Pembayaran
                    </button>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </section>

    <div class="modal fade" id="modalEditNominal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white p-2 px-3">
                    <h5 class="modal-title fs-6">Edit Tarif Khusus</h5>
                    <button type="button" class="btn p-1 text-white" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg" style="font-size: 1rem;"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Siswa</label>
                        <input type="text" class="form-control-plaintext fw-bold" id="modal_student_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Jenis Pembayaran</label>
                        <input type="text" class="form-control-plaintext fw-bold" id="modal_fee_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="text" class="form-control text-end fs-4" id="modal_nominal_input" onkeyup="formatRupiah(this)">
                    </div>
                    
                    <div id="modal_month_helper" style="display:none;">
                        <label class="form-label fw-bold small">Bulan Aktif</label>
                        <div class="row g-2">
                            <?php 
                            $months = [7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des', 1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun'];
                            foreach($months as $k => $v): ?>
                            <div class="col-3">
                                <div class="form-check">
                                    <input class="form-check-input modal-month-chk" type="checkbox" value="<?= $k ?>" id="mchk_<?= $k ?>">
                                    <label class="form-check-label small" for="mchk_<?= $k ?>"><?= $v ?></label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <input type="hidden" id="modal_sid">
                    <input type="hidden" id="modal_jid">
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveModalNominal()">Set Tarif</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function toggleItem(sid, jid) {
    var chk = document.getElementById('chk_' + sid + '_' + jid);
    var box = document.getElementById('box_' + sid + '_' + jid);
    chk.checked = !chk.checked;
    box.classList.toggle('active', chk.checked);
}

function toggleColumn(headerChk, jid) {
    var checkboxes = document.querySelectorAll('.chk-col-' + jid);
    checkboxes.forEach(chk => {
        chk.checked = headerChk.checked;
        var parts = chk.id.split('_');
        var box = document.getElementById('box_' + parts[1] + '_' + parts[2]);
        if (box) {
            if (chk.checked) box.classList.add('active');
            else box.classList.remove('active');
        }
    });
}

let matrixModal;

function openEditModal(sid, jid, sName, jName, isRecurring) {
    var currentVal = document.getElementById('val_' + sid + '_' + jid).value;
    document.getElementById('modal_sid').value = sid;
    document.getElementById('modal_jid').value = jid;
    document.getElementById('modal_student_name').value = sName;
    document.getElementById('modal_fee_name').value = jName;
    document.getElementById('modal_nominal_input').value = currentVal;
    
    var monthContainer = document.getElementById('modal_month_helper');
    if (isRecurring) {
        monthContainer.style.display = 'block';
        var savedMonths = [];
        document.querySelectorAll(`#months_container_${sid}_${jid} .month-val`).forEach(inp => savedMonths.push(parseInt(inp.value)));
        document.querySelectorAll('.modal-month-chk').forEach(chk => {
            chk.checked = savedMonths.includes(parseInt(chk.value));
        });
    } else {
        monthContainer.style.display = 'none';
    }
    
    if (!matrixModal) {
        matrixModal = new bootstrap.Modal(document.getElementById('modalEditNominal'));
    }
    matrixModal.show();
}

function saveModalNominal() {
    var sid = document.getElementById('modal_sid').value;
    var jid = document.getElementById('modal_jid').value;
    var newVal = document.getElementById('modal_nominal_input').value;
    
    document.getElementById('val_' + sid + '_' + jid).value = newVal;
    document.getElementById('txt_' + sid + '_' + jid).innerText = newVal;
    
    var chk = document.getElementById('chk_' + sid + '_' + jid);
    var box = document.getElementById('box_' + sid + '_' + jid);
    chk.checked = true;
    box.classList.add('active');
    
    var container = document.getElementById(`months_container_${sid}_${jid}`);
    container.innerHTML = '';
    if (document.getElementById('modal_month_helper').style.display !== 'none') {
        document.querySelectorAll('.modal-month-chk:checked').forEach(m => {
            container.innerHTML += `<input type="hidden" class="month-val" value="${m.value}">`;
        });
    }
    
    if (matrixModal) {
        matrixModal.hide();
    }
}

function formatRupiah(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    input.value = new Intl.NumberFormat('id-ID').format(value);
}

function prepareSubmit() {
    const data = {};
    const cells = document.querySelectorAll('.matrix-active');
    
    cells.forEach(chk => {
        const sid = chk.getAttribute('data-sid');
        const jid = chk.getAttribute('data-jid');
        
        if (!data[sid]) data[sid] = {};
        
        const nominalInput = document.getElementById('val_' + sid + '_' + jid);
        const monthInputs = document.querySelectorAll(`#months_container_${sid}_${jid} .month-val`);
        const months = Array.from(monthInputs).map(inp => parseInt(inp.value));
        
        data[sid][jid] = {
            active: chk.checked ? 1 : 0,
            nominal: nominalInput.value,
            months: months
        };
    });
    
    document.getElementById('matrix_data').value = JSON.stringify(data);
    return true;
}
</script>

<?php include '../app/views/partials/footer.php'; ?>

<!-- External Flatpickr Plugins (MUST be after footer.php where flatpickr.js is loaded) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

<script>
$(document).ready(function() {
    // Indonesian Month Picker Initialization
    const monthsId = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
    if (typeof monthSelectPlugin !== "undefined") {
        flatpickr("#bulan_generate_picker", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: false,
                    dateFormat: "F Y",
                    altFormat: "F Y",
                    theme: "light"
                })
            ],
            locale: {
                months: {
                    shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                    longhand: monthsId
                }
            },
            defaultDate: "<?= date('Y-m') ?>",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const d = selectedDates[0];
                    const year = d.getFullYear();
                    const month = ("0" + (d.getMonth() + 1)).slice(-2);
                    document.getElementById('bulan_generate_val').value = year + "-" + month;
                }
            }
        });
        
        // Ensure Indonesian format on load
        const now = new Date();
        document.getElementById('bulan_generate_picker').value = monthsId[now.getMonth()] + " " + now.getFullYear();
    } else {
        console.warn("Flatpickr MonthSelect plugin could not be loaded. Falling back to native.");
        const picker = document.getElementById('bulan_generate_picker');
        picker.type = "month";
        picker.readOnly = false;
        picker.value = "<?= date('Y-m') ?>";
        picker.onchange = function() {
            document.getElementById('bulan_generate_val').value = this.value;
        };
    }
});
</script>