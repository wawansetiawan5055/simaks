<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-calendar-check mr-2"></i> Formulir Absensi Mapel
                    <?php if (!empty($has_existing_data)): ?>
                        <span class="badge badge-warning ml-2" style="font-size:0.65rem;vertical-align:middle;">MODE EDIT</span>
                    <?php elseif (!empty($is_past_date)): ?>
                        <span class="badge badge-warning ml-2" style="font-size:0.65rem;vertical-align:middle;">MODE EDIT (TANGGAL LALU)</span>
                    <?php endif; ?>
                </h1>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">

        <?php if (!empty($has_existing_data)): ?>
        <!-- Banner Edit Mode - Data Tersimpan -->
        <div class="callout callout-warning mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-edit fa-2x text-warning mr-3"></i>
                <div>
                    <strong>Mode Edit — Data Sudah Tersimpan</strong><br>
                    <small class="text-muted">Data absensi kelas ini pada tanggal ini sudah pernah disimpan. Menyimpan ulang akan <strong>memperbarui</strong> data yang lama.</small>
                </div>
            </div>
        </div>
        <?php elseif (!empty($is_past_date)): ?>
        <!-- Banner Edit Mode - Tanggal Lalu -->
        <div class="callout callout-warning mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-history fa-2x text-warning mr-3"></i>
                <div>
                    <strong>Mode Edit — Absensi Tanggal Lalu</strong><br>
                    <small class="text-muted">Anda sedang menginput/mengubah absensi untuk tanggal yang telah lalu (<?= DateHelper::formatTanggal($tanggal, 'long') ?>).</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- [REVISI] Pesan sukses dengan link jurnal -->
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="callout callout-success d-flex justify-content-between align-items-center mb-3">
                <div class="text-success">
                    <i class="fas fa-check-circle mr-2"></i> Data berhasil disimpan.
                </div>
                <a href="<?= BASE_URL ?>jurnal_kbm?id_kelas=<?= $id_kelas ?>&tanggal=<?= $tanggal ?>"
                    class="btn btn-success font-weight-bold">
                    Lanjut ke Jurnal KBM <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>absensi_mapel/save" method="POST">
            <input type="hidden" name="id_kelas" id="id_kelas_hidden" value="<?= $id_kelas ?>">
            <input type="hidden" name="tanggal" id="tanggal_hidden" value="<?= $tanggal ?>">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelas: <strong><?= $kelas['nama_kelas'] ?></strong> | Tanggal:
                        <strong><?= DateHelper::formatTanggal($tanggal, 'long') ?></strong>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Jam Mengajar (Pilih satu atau lebih)</label>
                                <div id="jam_mengajar_container">
                                    <p class="text-muted">-- Memuat jadwal... --</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="overflow-x:auto;">
                        <table class="table table-bordered table-hover w-100" style="min-width:100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kehadiran</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-absensi">
                            <?php $no = 1; $row_idx = 0;
                            foreach ($siswa_list as $s):
                                // Ambil data existing jika ada (mode edit)
                                $existing = $absensi_existing[$s['id_siswa']] ?? null;
                                $status_saved = $existing['status'] ?? 'Hadir';
                                $ket_saved = $existing['keterangan'] ?? '';
                                $row_hl = ($existing && $status_saved !== 'Hadir') ? 'table-warning' : '';
                            ?>
                                <tr data-row="<?= $row_idx ?>" class="<?= $row_hl ?>">
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td>
                                        <span class="font-weight-bold"><?= htmlspecialchars($s['nama']) ?></span><br>
                                        <small class="text-muted">NISN: <?= $s['nisn'] ?></small>
                                    </td>

                                    <td>
                                        <div class="btn-group btn-group-toggle d-flex flex-wrap absensi-btn-group" data-toggle="buttons" data-row="<?= $row_idx ?>">
                                            <label class="btn btn-outline-success active mb-1 btn-absensi <?= $status_saved === 'Hadir' ? 'active' : '' ?>" tabindex="0" data-key="H">
                                                <input type="radio" name="absensi[<?= $s['id_siswa'] ?>][status]" value="Hadir" <?= $status_saved === 'Hadir' ? 'checked' : '' ?>> Hadir
                                            </label>
                                            <label class="btn btn-outline-warning mb-1 btn-absensi <?= $status_saved === 'Sakit' ? 'active' : '' ?>" tabindex="0" data-key="S">
                                                <input type="radio" name="absensi[<?= $s['id_siswa'] ?>][status]" value="Sakit" <?= $status_saved === 'Sakit' ? 'checked' : '' ?>> Sakit
                                            </label>
                                            <label class="btn btn-outline-info mb-1 btn-absensi <?= $status_saved === 'Izin' ? 'active' : '' ?>" tabindex="0" data-key="I">
                                                <input type="radio" name="absensi[<?= $s['id_siswa'] ?>][status]" value="Izin" <?= $status_saved === 'Izin' ? 'checked' : '' ?>> Izin
                                            </label>
                                            <label class="btn btn-outline-danger mb-1 btn-absensi <?= $status_saved === 'Alpa' ? 'active' : '' ?>" tabindex="0" data-key="A">
                                                <input type="radio" name="absensi[<?= $s['id_siswa'] ?>][status]" value="Alpa" <?= $status_saved === 'Alpa' ? 'checked' : '' ?>> Alpa
                                            </label>
                                        </div>
                                    </td>

                                    <td><input type="text" name="absensi[<?= $s['id_siswa'] ?>][keterangan]"
                                            class="form-control form-control-sm input-keterangan" data-row="<?= $row_idx ?>"
                                            value="<?= htmlspecialchars($ket_saved) ?>"
                                            placeholder="Opsional..."></td>
                                </tr>
                            <?php $row_idx++; endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-<?= !empty($has_existing_data) ? 'warning' : 'success' ?> px-4">
                        <i class="fas fa-<?= !empty($has_existing_data) ? 'sync-alt' : 'save' ?> mr-1"></i>
                        <?= !empty($has_existing_data) ? 'Update Absensi' : 'Simpan Absensi' ?>
                    </button>
                    <a href="<?= BASE_URL ?>absensi_mapel" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <?php if (!empty($has_existing_data)): ?>
                    <small class="text-muted ml-3">
                        <i class="fas fa-info-circle"></i> Menyimpan ulang akan memperbarui data absensi sebelumnya.
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</section>

<style>
    tr.row-focused {
        background-color: rgba(0, 123, 255, 0.05) !important;
        border-left: 3px solid #007bff;
    }
    .input-keterangan:focus {
        background-color: #fff8e1;
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const idKelas = document.getElementById('id_kelas_hidden').value;
        const tanggal = document.getElementById('tanggal_hidden').value;
        const jamContainer = document.getElementById('jam_mengajar_container');

        function fetchJadwal() {
            if (!idKelas || !tanggal) return;

            // REVISI: Routing public/api.php directly
            fetch(`api.php?mod=jadwal&act=get_by_kelas_dan_tanggal&id_kelas=${idKelas}&tanggal=${tanggal}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'ok' && result.data.length > 0) {
                        jamContainer.innerHTML = ''; // Kosongkan container
                        result.data.forEach(item => {
                            const jamMulai = item.jam_mulai.substring(0, 5);
                            const jamSelesai = item.jam_selesai.substring(0, 5);
                            const optionText = `${jamMulai} - ${jamSelesai} | ${item.nama_mapel}`;

                            // Buat elemen checkbox
                            const checkboxDiv = document.createElement('div');
                            checkboxDiv.className = 'form-check';
                            checkboxDiv.innerHTML = `
                            <input class="form-check-input" type="checkbox" name="jam_mengajar[]" value="${item.id_jadwal_mengajar}" id="jam_${item.id_jadwal_mengajar}">
                            <label class="form-check-label" for="jam_${item.id_jadwal_mengajar}">${optionText}</label>
                        `;
                            jamContainer.appendChild(checkboxDiv);
                        });
                    } else {
                        jamContainer.innerHTML = '<p class="text-danger font-italic">-- Tidak ada jadwal di hari ini --</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching schedule:', error);
                    jamContainer.innerHTML = '<p class="text-danger font-italic">-- Gagal memuat jadwal --</p>';
                });
        }
        fetchJadwal();

        // KEYBOARD NAVIGATION LOGIC
        let focusedRow = 0;
        const totalRows = document.querySelectorAll('#tabel-absensi tr').length;

        function focusRow(idx) {
            document.querySelectorAll('#tabel-absensi tr').forEach(tr => tr.classList.remove('row-focused'));
            const tr = document.querySelector(`#tabel-absensi tr[data-row="${idx}"]`);
            if (tr) {
                tr.classList.add('row-focused');
                focusedRow = idx;
            }
        }

        if (totalRows > 0) {
            focusRow(0);
        }

        document.addEventListener('keydown', function(e) {
            const activeEl = document.activeElement;
            
            // Allow typing normally if inside Keterangan input
            if (activeEl && activeEl.classList.contains('input-keterangan')) {
                if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    e.preventDefault();
                    const nextRow = focusedRow + 1;
                    const nextInput = document.querySelector(`.input-keterangan[data-row="${nextRow}"]`);
                    if (nextInput) {
                        focusRow(nextRow);
                        nextInput.focus();
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevRow = focusedRow - 1;
                    const prevInput = document.querySelector(`.input-keterangan[data-row="${prevRow}"]`);
                    if (prevInput) {
                        focusRow(prevRow);
                        prevInput.focus();
                    }
                }
                return; // Don't process H/S/I/A if in Keterangan text input
            }
            
            // Navigation
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (focusedRow + 1 < totalRows) focusRow(focusedRow + 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (focusedRow - 1 >= 0) focusRow(focusedRow - 1);
            }

            // Absensi Shortcuts
            const key = e.key.toUpperCase();
            if (['H', 'S', 'I', 'A'].includes(key)) {
                e.preventDefault();
                const currentTr = document.querySelector(`#tabel-absensi tr[data-row="${focusedRow}"]`);
                if (currentTr) {
                    const btnLabel = currentTr.querySelector(`.btn-absensi[data-key="${key}"]`);
                    if (btnLabel) {
                        btnLabel.click();
                        
                        // Auto move to next row
                        if (focusedRow + 1 < totalRows) focusRow(focusedRow + 1);
                    }
                }
            }
        });

        // Update focusedRow if user manually clicks somewhere in the row
        document.querySelectorAll('#tabel-absensi tr').forEach(tr => {
            tr.addEventListener('click', function() {
                focusRow(parseInt(this.getAttribute('data-row')));
            });
        });
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>