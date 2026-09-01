<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4 flex-wrap" style="gap: 12px;">
            <div>
                <h2 class="m-0 font-weight-bold text-dark"><i class="fas fa-user-minus text-danger mr-2"></i> Mutasi Siswa Keluar</h2>
                <p class="text-muted small mb-0">Pencatatan dan arsip data siswa mutasi keluar, pindah sekolah, atau drop out.</p>
            </div>
            <div>
                <button type="button" class="btn btn-danger btn-sm px-3 shadow-sm font-weight-bold" style="border-radius: 8px;" onclick="openModalAdd()">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Mutasi Keluar
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filter TA -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-body p-2">
                <form action="index.php" method="GET" class="form-inline">
                    <input type="hidden" name="mod" value="mutasi_siswa">
                    <div class="form-group mx-sm-3 mb-0">
                        <label class="mr-2">Tampilkan Riwayat TA:</label>
                        <select name="id_ta" class="form-control form-control-sm" onchange="this.form.submit()">
                            <?php foreach ($ta_list as $ta): ?>
                                <option value="<?= $ta['id_ta'] ?>" <?= ($id_ta_filter == $ta['id_ta']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ta['nama_ta']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Siswa Mutasi Keluar</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>Jenis</th>
                            <th>Alasan</th>
                            <th style="width: 100px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($riwayat_mutasi as $r): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="text-nowrap"><?= DateHelper::formatTanggal($r['tgl_mutasi'], 'short') ?></td>
                                <td><?= htmlspecialchars($r['nama']) ?></td>
                                <td><?= htmlspecialchars($r['nisn']) ?></td>
                                <td><?= htmlspecialchars($r['nama_kelas'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badge = 'badge-secondary';
                                    if ($r['jenis_mutasi'] == 'Keluar')
                                        $badge = 'badge-danger';
                                    elseif ($r['jenis_mutasi'] == 'Berhenti')
                                        $badge = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= htmlspecialchars($r['jenis_mutasi']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($r['alasan_mutasi']) ?></td>
                                <td class="text-center text-nowrap">
                                    <button class="btn btn-xs btn-info"
                                        onclick="openModalEdit('<?= $r['id_siswa'] ?>', '<?= htmlspecialchars($r['nama']) ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="<?= BASE_URL ?>mutasi_siswa/batal?id_siswa=<?= $r['id_siswa'] ?>"
                                        class="btn btn-xs btn-outline-secondary"
                                        onclick="return confirm('⚠️ Batalkan mutasi siswa ini? \nSiswa akan dikembalikan ke status Aktif dan masuk kembali ke kelas asalnya.')">
                                        <i class="fas fa-undo"></i> Batal
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($riwayat_mutasi)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Belum ada data riwayat mutasi untuk TA
                                    terpilih.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- MODAL FORM MUTASI -->
<div class="modal fade" id="modal-mutasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title" id="modal-title">Formulir Mutasi Keluar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-mutasi" action="<?= BASE_URL ?>mutasi_siswa/save" method="POST">
                <input type="hidden" name="is_edit" id="is_edit" value="0">
                <div class="modal-body">
                    <div id="section-pilih-siswa">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pilih Kelas Siswa</label>
                                    <select id="id_kelas_filter" class="form-control" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php foreach ($kelas_list as $k): ?>
                                            <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pilih Siswa (dari Kelas terpilih)</label>
                                    <select name="id_siswa" id="id_siswa" class="form-control" required>
                                        <option value="">-- Pilih Kelas Dahulu --</option>
                                    </select>
                                    <input type="hidden" name="id_kelas_asal" id="id_kelas_asal" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="section-edit-siswa" style="display:none;">
                        <div class="form-group">
                            <label>Siswa</label>
                            <input type="text" id="display_nama_siswa" class="form-control" readonly>
                            <input type="hidden" name="id_siswa_edit" id="id_siswa_edit">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Mutasi</label>
                                <select name="jenis_mutasi" id="jenis_mutasi" class="form-control" required>
                                    <option value="Keluar">Mutasi Keluar</option>
                                    <option value="Berhenti">Berhenti/Drop Out</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mutasi</label>
                                <input type="date" name="tanggal_mutasi" id="tanggal_mutasi" class="form-control"
                                    value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alasan</label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan Data Mutasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModalAdd() {
        document.getElementById('modal-title').innerText = 'Tambah Mutasi Keluar';
        document.getElementById('is_edit').value = '0';
        document.getElementById('form-mutasi').reset();
        document.getElementById('section-pilih-siswa').style.display = 'block';
        document.getElementById('section-edit-siswa').style.display = 'none';

        // Re-enable for Add mode so validation works
        document.getElementById('id_siswa').disabled = false;
        document.getElementById('id_kelas_filter').disabled = false;
        document.getElementById('id_siswa').required = true;

        // Ensure id_siswa_edit doesn't steal the name
        document.getElementById('id_siswa_edit').name = 'id_siswa_edit';

        $('#modal-mutasi').modal('show');
    }

    function openModalEdit(idSiswa, namaSiswa) {
        document.getElementById('modal-title').innerText = 'Edit Mutasi Keluar';
        document.getElementById('is_edit').value = '1';
        document.getElementById('section-pilih-siswa').style.display = 'none';
        document.getElementById('section-edit-siswa').style.display = 'block';
        document.getElementById('display_nama_siswa').value = namaSiswa;

        // Disable hidden required fields so browser validation skips them
        document.getElementById('id_siswa').disabled = true;
        document.getElementById('id_kelas_filter').disabled = true;
        document.getElementById('id_siswa').required = false;

        // Use the hidden field for id_siswa
        const idEdit = document.getElementById('id_siswa_edit');
        idEdit.value = idSiswa;
        idEdit.name = 'id_siswa'; // Match controller expectation

        // Fetch mutation data
        fetch(`<?= BASE_URL ?>mutasi_siswa/get_mutation_api?id_siswa=${idSiswa}`)
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    const data = result.data;
                    document.getElementById('jenis_mutasi').value = data.jenis_mutasi;
                    document.getElementById('tanggal_mutasi').value = data.tgl_mutasi;
                    document.getElementById('alasan').value = data.alasan_mutasi;
                    $('#modal-mutasi').modal('show');
                } else {
                    alert('Gagal mengambil data: ' + result.message);
                }
            });
    }

    // Script AJAX untuk memuat siswa berdasarkan kelas
    document.getElementById('id_kelas_filter').addEventListener('change', function () {
        const idKelas = this.value;
        const siswaSelect = document.getElementById('id_siswa');
        const kelasAsalInput = document.getElementById('id_kelas_asal');

        siswaSelect.innerHTML = '<option value="">Memuat...</option>';

        if (!idKelas) {
            siswaSelect.innerHTML = '<option value="">-- Pilih Kelas Dahulu --</option>';
            kelasAsalInput.value = '';
            return;
        }

        kelasAsalInput.value = idKelas;

        fetch(`<?= BASE_URL ?>mutasi_siswa/get_siswa_api?id_kelas=${idKelas}`)
            .then(response => response.json())
            .then(data => {
                siswaSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';
                if (data.length > 0) {
                    data.forEach(siswa => {
                        const option = new Option(siswa.nama + ' (' + siswa.nisn + ')', siswa.id_siswa);
                        siswaSelect.appendChild(option);
                    });
                } else {
                    siswaSelect.innerHTML = '<option value="">-- Tidak ada siswa aktif di kelas ini --</option>';
                }
            });
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>