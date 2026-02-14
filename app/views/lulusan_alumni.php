<?php include __DIR__ . '/partials/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<section class="content-header">
    <div class="container-fluid">
        <h1>Data Alumni & Tracer Study</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <!-- (GRAFIK & SUMMARY DIPINDAHKAN KE DASHBOARD) -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Grafik dan Rekapitulasi Lulusan kini dapat dilihat di <a
                        href="index.php?mod=dashboard" class="font-weight-bold">Dashboard Utama</a>.
                </div>
            </div>
        </div>

        <!-- ROW 2: DETAIL ALUMNI TABLE -->
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Daftar Detail Alumni</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped table-sm" id="tabel_alumni">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Thn Lulus</th>
                            <th>NISN</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas Akhir</th>
                            <th>L/P</th>
                            <th>Status Alumni</th>
                            <th>Institusi/Ket</th>
                            <?php if (can_do($pdo, 'lulusan', 'update')): ?>
                            <th class="text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($alumni_list as $a):
                            // Cek data tracer (bisa diimprove dengan join di query utama tapi ini cukup utk skrg)
                            $tracer = TracerStudyModel::getTracerByAlumni($pdo, $a['id_siswa']);
                            $status_alumni = $tracer['status_setelah_lulus'] ?? '-';
                            $info_institusi = $tracer['nama_institusi'] ?? $tracer['keterangan'] ?? '-';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $a['tahun_lulus'] ?></td>
                                <td><?= $a['nisn'] ?></td>
                                <td class="font-weight-bold"><?= $a['nama'] ?></td>
                                <td><?= htmlspecialchars($a['nama_kelas'] ?? '-') ?></td>
                                <td><?= $a['jk'] == 'Laki-laki' ? 'L' : 'P' ?></td>
                                <td>
                                    <?php if ($status_alumni == 'PTN/PTS'): ?>
                                        <span class="badge badge-primary">Kuliah</span>
                                    <?php elseif ($status_alumni == 'Bekerja'): ?>
                                        <span class="badge badge-success">Bekerja</span>
                                    <?php elseif ($status_alumni == 'Wirausaha'): ?>
                                        <span class="badge badge-warning">Wirausaha</span>
                                    <?php elseif ($status_alumni == '-'): ?>
                                        <span class="badge badge-secondary">Belum Input</span>
                                    <?php else: ?>
                                        <span class="badge badge-info"><?= $status_alumni ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $info_institusi ?></td>

                                <?php if (can_do($pdo, 'lulusan', 'update')): ?>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-xs btn-info btn-tracer"
                                            onclick="populateTracerModal(this)" data-id="<?= $a['id_siswa'] ?>"
                                            data-nama="<?= htmlspecialchars($a['nama'], ENT_QUOTES) ?>"
                                            data-tahun="<?= $a['tahun_lulus'] ?>"
                                            data-id-tracer="<?= $tracer['id_tracer'] ?? '' ?>"
                                            data-status="<?= htmlspecialchars($tracer['status_setelah_lulus'] ?? '', ENT_QUOTES) ?>"
                                            data-institusi="<?= htmlspecialchars($tracer['nama_institusi'] ?? '', ENT_QUOTES) ?>"
                                            data-jurusan="<?= htmlspecialchars($tracer['jurusan_pekerjaan'] ?? '', ENT_QUOTES) ?>"
                                            data-kota="<?= htmlspecialchars($tracer['kota'] ?? '', ENT_QUOTES) ?>"
                                            data-ket="<?= htmlspecialchars($tracer['keterangan'] ?? '', ENT_QUOTES) ?>"
                                            title="Update Tracer Study">
                                            <i class="fas fa-edit"></i> Tracer
                                        </button>

                                        <a href="index.php?mod=lulusan&act=batal&id=<?= $a['id_siswa'] ?>"
                                            class="btn btn-xs btn-danger"
                                            onclick="return confirm('Batalkan status lulus? Siswa akan kembali menjadi Aktif.')"
                                            title="Batalkan Kelulusan">
                                            <i class="fas fa-undo"></i> Batal
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- MODAL TRACER STUDY -->
<div class="modal fade" id="modalTracer">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">Update Tracer Study</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="index.php?mod=lulusan&act=update_tracer" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_siswa" id="tracer_id_siswa">
                    <input type="hidden" name="id_tracer" id="tracer_id">

                    <div class="form-group">
                        <label>Nama Alumni</label>
                        <input type="text" class="form-control" id="tracer_nama" readonly>
                    </div>
                    <div class="form-group">
                        <label>Tahun Lulus</label>
                        <input type="text" class="form-control" name="tahun_lulus" id="tracer_tahun" readonly>
                    </div>
                    <div class="form-group">
                        <label>Status Setelah Lulus</label>
                        <select class="form-control" name="status_setelah_lulus" id="tracer_status" required
                            onchange="toggleInstitusi(this.value)">
                            <option value="">-- Pilih Status --</option>
                            <option value="PTN/PTS">Melanjutkan Pendidikan (Kuliah)</option>
                            <option value="Bekerja">Bekerja</option>
                            <option value="Wirausaha">Wirausaha</option>
                            <option value="Lain-lain">Lainnya / Belum Bekerja</option>
                        </select>
                    </div>

                    <!-- Conditional Fields -->
                    <div id="field_institusi" class="d-none">
                        <div class="form-group">
                            <label>Nama Universitas / Perusahaan</label>
                            <input type="text" class="form-control" name="nama_institusi" id="tracer_institusi"
                                placeholder="Contoh: Universitas Indonesia / PT. Telkom">
                        </div>
                        <div class="form-group">
                            <label>Jurusan / Posisi Pekerjaan</label>
                            <input type="text" class="form-control" name="jurusan_pekerjaan" id="tracer_jurusan"
                                placeholder="Contoh: Teknik Informatika / Staff IT">
                        </div>
                        <div class="form-group">
                            <label>Kota Lokasi</label>
                            <input type="text" class="form-control" name="kota" id="tracer_kota"
                                placeholder="Kota tempat kuliah/kerja">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan Tambahan (Opsional)</label>
                        <textarea class="form-control" name="keterangan" id="tracer_ket" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Define function in global scope
    function populateTracerModal(btn) {
        // Debug check
        // alert('Tombol Tracer diklik!'); 

        var dataset = btn.dataset;

        // Set Values
        document.getElementById('tracer_id_siswa').value = dataset.id;
        document.getElementById('tracer_nama').value = dataset.nama;
        document.getElementById('tracer_tahun').value = dataset.tahun;

        document.getElementById('tracer_id').value = dataset.idTracer;

        // Set Status and trigger change manually
        var statusSelect = document.getElementById('tracer_status');
        statusSelect.value = dataset.status;
        toggleInstitusi(dataset.status);

        document.getElementById('tracer_institusi').value = dataset.institusi;
        document.getElementById('tracer_jurusan').value = dataset.jurusan;
        document.getElementById('tracer_kota').value = dataset.kota;
        document.getElementById('tracer_ket').value = dataset.ket;

        // Show Modal using jQuery
        $('#modalTracer').modal('show');
    }

    function toggleInstitusi(val) {
        var field = document.getElementById('field_institusi');
        if (val == 'PTN/PTS' || val == 'Bekerja' || val == 'Wirausaha') {
            field.classList.remove('d-none');
        } else {
            field.classList.add('d-none');
        }
    }

    $(function () {
        // DataTable
        $('#tabel_alumni').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[1, "desc"]] // Sort by Tahun Lulus Desc
        });
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>