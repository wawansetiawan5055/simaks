<?php include __DIR__ . '/partials/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Data Alumni &amp; Tracer Study
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Alumni &amp; Tracer</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- (GRAFIK & SUMMARY DIPINDAHKAN KE DASHBOARD) -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Grafik dan Rekapitulasi Lulusan kini dapat dilihat di <a
                        href="<?= BASE_URL ?>dashboard" class="font-weight-bold">Dashboard Utama</a>.
                </div>
            </div>
        </div>

        <!-- ROW 2: DETAIL ALUMNI TABLE -->
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Daftar Detail Alumni</h3>
            </div>
            <div class="card-body table-responsive">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Filter Tahun Lulus</label>
                        <select id="filter_tahun" class="form-control">
                            <option value="">Semua Tahun</option>
                            <?php
                            $tahunOptions = array_unique(array_column($alumni_list, 'tahun_lulus'));
                            sort($tahunOptions);
                            foreach ($tahunOptions as $tahun): ?>
                                <?php if ($tahun !== null && $tahun !== '' && $tahun != 0): ?>
                                    <option value="<?= $tahun ?>"><?= $tahun ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">Filter Kelas Akhir</label>
                        <select id="filter_kelas" class="form-control">
                            <option value="">Semua Kelas</option>
                            <?php
                            $kelasOptions = array_unique(array_map(function($a) { return $a['nama_kelas'] ?? '-'; }, $alumni_list));
                            sort($kelasOptions);
                            foreach ($kelasOptions as $kelas): ?>
                                <?php if ($kelas !== null && $kelas !== ''): ?>
                                    <option value="<?= htmlspecialchars($kelas) ?>"><?= htmlspecialchars($kelas) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">Filter Status Alumni</label>
                        <select id="filter_status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="Belum Input">Belum Input</option>
                            <option value="Kuliah">Kuliah</option>
                            <option value="Bekerja">Bekerja</option>
                            <option value="Wirausaha">Wirausaha</option>
                            <option value="Lainnya">Lain-lain</option>
                        </select>
                    </div>
                </div>

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
                            $tracer = TracerStudyModel::getTracerByAlumni($pdo, $a['id_siswa']);
                            $status_alumni = $tracer['status_setelah_lulus'] ?? '-';
                            $info_institusi = $tracer['nama_institusi'] ?? $tracer['keterangan'] ?? '-';
                            // Map status untuk filter
                            $filter_status = '-';
                            if ($status_alumni == 'PTN/PTS') {
                                $filter_status = 'Kuliah';
                            } elseif ($status_alumni == 'Bekerja') {
                                $filter_status = 'Bekerja';
                            } elseif ($status_alumni == 'Wirausaha') {
                                $filter_status = 'Wirausaha';
                            } elseif ($status_alumni == '-') {
                                $filter_status = 'Belum Input';
                            }
                            ?>
                            <tr class="alumni-row" data-tahun="<?= htmlspecialchars($a['tahun_lulus']) ?>" data-kelas="<?= htmlspecialchars($a['nama_kelas'] ?? '-') ?>" data-status="<?= htmlspecialchars($filter_status) ?>">
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
                                        <span class="badge badge-info"><?= htmlspecialchars($status_alumni) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($info_institusi) ?></td>

                                <?php if (can_do($pdo, 'lulusan', 'update')): ?>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-xs btn-info btn-tracer"
                                            onclick="populateTracerModal(this)"
                                            data-id="<?= $a['id_siswa'] ?>"
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

                                        <a href="<?= BASE_URL ?>lulusan/batal?id=<?= $a['id_siswa'] ?>"
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
            <form action="<?= BASE_URL ?>lulusan/update_tracer" method="post">
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
                        <select class="form-control" name="status_setelah_lulus" id="tracer_status" required onchange="toggleInstitusi(this.value)">
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
                            <input type="text" class="form-control" name="nama_institusi" id="tracer_institusi" placeholder="Contoh: Universitas Indonesia / PT. Telkom">
                        </div>
                        <div class="form-group">
                            <label>Jurusan / Posisi Pekerjaan</label>
                            <input type="text" class="form-control" name="jurusan_pekerjaan" id="tracer_jurusan" placeholder="Contoh: Teknik Informatika / Staff IT">
                        </div>
                        <div class="form-group">
                            <label>Kota Lokasi</label>
                            <input type="text" class="form-control" name="kota" id="tracer_kota" placeholder="Kota tempat kuliah/kerja">
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
    function populateTracerModal(btn) {
        var dataset = btn.dataset;
        document.getElementById('tracer_id_siswa').value = dataset.id;
        document.getElementById('tracer_nama').value = dataset.nama;
        document.getElementById('tracer_tahun').value = dataset.tahun;
        document.getElementById('tracer_id').value = dataset.idTracer;

        var statusSelect = document.getElementById('tracer_status');
        statusSelect.value = dataset.status;
        toggleInstitusi(dataset.status);

        document.getElementById('tracer_institusi').value = dataset.institusi;
        document.getElementById('tracer_jurusan').value = dataset.jurusan;
        document.getElementById('tracer_kota').value = dataset.kota;
        document.getElementById('tracer_ket').value = dataset.ket;
        $('#modalTracer').modal('show');
    }

    function toggleInstitusi(val) {
        var field = document.getElementById('field_institusi');
        if (val === 'PTN/PTS' || val === 'Bekerja' || val === 'Wirausaha') {
            field.classList.remove('d-none');
        } else {
            field.classList.add('d-none');
        }
    }

    function filterAlumni() {
        var filterTahun = document.getElementById('filter_tahun').value;
        var filterKelas = document.getElementById('filter_kelas').value;
        var filterStatus = document.getElementById('filter_status').value;
        var rows = document.querySelectorAll('.alumni-row');

        rows.forEach(function(row) {
            var rowTahun = row.getAttribute('data-tahun');
            var rowKelas = row.getAttribute('data-kelas');
            var rowStatus = row.getAttribute('data-status');

            var tahunMatch = !filterTahun || rowTahun === filterTahun;
            var kelasMatch = !filterKelas || rowKelas === filterKelas;
            var statusMatch = !filterStatus || rowStatus === filterStatus;

            if (tahunMatch && kelasMatch && statusMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    $(function () {
        $('#filter_tahun, #filter_kelas, #filter_status').on('change', function() {
            filterAlumni();
        });
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
