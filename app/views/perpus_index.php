<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-book-reader mr-2 text-teal"></i><?= $title ?></h1>
            </div>
            <div class="col-sm-6 text-right">
                <form action="index.php" method="GET" class="form-inline d-inline-block">
                    <input type="hidden" name="mod" value="manajemen_perpus">
                    <select name="id_ta" class="form-control form-control-sm" onchange="this.form.submit()">
                        <?php foreach ($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta['id_ta'] ?>" <?= $ta['id_ta'] == $id_ta ? 'selected' : '' ?>>TA: <?= htmlspecialchars($ta['nama_ta']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-teal card-outline card-tabs shadow-sm">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="perpusTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="buku-tab" data-toggle="pill" href="#tab-buku" role="tab"><i class="fas fa-book mr-2"></i>Katalog Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pinjam-tab" data-toggle="pill" href="#tab-pinjam" role="tab"><i class="fas fa-exchange-alt mr-2"></i>Sirkulasi Peminjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="riwayat-tab" data-toggle="pill" href="#tab-riwayat" role="tab"><i class="fas fa-history mr-2"></i>Riwayat Peminjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="agenda-per-tab" data-toggle="pill" href="#tab-agenda-per" role="tab"><i class="fas fa-calendar-check mr-2"></i>Agenda Kegiatan</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB 1: KATALOG BUKU -->
                    <div class="tab-pane fade show active" id="tab-buku" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-12 text-sm">
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalBuku"><i class="fas fa-plus mr-1"></i>Tambah Koleksi</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-sm">
                                <thead class="bg-teal text-white">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Judul Buku</th>
                                        <th>Pengarang</th>
                                        <th>Penerbit</th>
                                        <th class="text-center">Stok</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($buku)): ?>
                                        <tr><td colspan="6" class="text-center py-4 text-muted">Katalog kosong.</td></tr>
                                    <?php else: foreach($buku as $b): ?>
                                        <tr>
                                            <td><span class="badge badge-light border"><?= htmlspecialchars($b['kode_buku']) ?></span></td>
                                            <td class="font-weight-bold"><?= htmlspecialchars($b['judul_buku']) ?></td>
                                            <td><?= htmlspecialchars($b['pengarang']) ?></td>
                                            <td><?= htmlspecialchars($b['penerbit']) ?></td>
                                            <td class="text-center font-weight-bold"><?= $b['jumlah_stok'] ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-xs btn-info edit-buku" data-json='<?= json_encode($b) ?>'><i class="fas fa-edit"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: SIRKULASI -->
                    <div class="tab-pane fade" id="tab-pinjam" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border shadow-none">
                                    <div class="card-header bg-light"><h3 class="card-title text-sm font-weight-bold">Form Peminjaman</h3></div>
                                    <form action="<?= BASE_URL ?>manajemen_perpus/save_peminjaman" method="POST">
                                        <div class="card-body p-3">
                                            <div class="form-group text-sm">
                                                <label>Pilih Buku</label>
                                                <select name="id_buku" class="form-control select2" required style="width: 100%;">
                                                    <option value="">- Pilih Buku -</option>
                                                    <?php foreach($buku as $b): ?>
                                                        <option value="<?= $b['id_buku'] ?>"><?= htmlspecialchars($b['judul_buku']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Tipe Peminjam</label>
                                                <select name="peminjam_tipe" id="peminjam_tipe" class="form-control" required onchange="updatePeminjamForm()">
                                                    <option value="Siswa">Siswa</option>
                                                    <option value="Guru">Guru</option>
                                                </select>
                                            </div>
                                            <div id="form-siswa" style="display:block;">
                                                <div class="form-group text-sm">
                                                    <label>Pilih Kelas</label>
                                                    <select name="id_kelas_peminjam" id="id_kelas_peminjam" class="form-control select2" style="width: 100%;" onchange="loadSiswaByKelas()">
                                                        <option value="">- Pilih Kelas -</option>
                                                        <?php foreach($kelas_list as $k): ?>
                                                            <option value="<?= $k['id_kelas'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?> (Tingkat <?= $k['tingkat'] ?>)</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group text-sm">
                                                    <label>Pilih Siswa</label>
                                                    <select name="id_peminjam" id="id_peminjam" class="form-control select2" style="width: 100%;" required>
                                                        <option value="">- Pilih Kelas Dulu -</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="form-guru" style="display:none;">
                                                <div class="form-group text-sm">
                                                    <label>Nama Guru</label>
                                                    <input type="text" name="guru_nama" id="guru_nama" class="form-control" placeholder="Nama guru...">
                                                    <input type="hidden" name="id_peminjam" value="0">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6"><div class="form-group text-sm"><label>Tgl Pinjam</label><input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required></div></div>
                                                <div class="col-6"><div class="form-group text-sm"><label>Kembali</label><input type="date" name="tgl_kembali_rencana" class="form-control" required></div></div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white"><button type="submit" class="btn btn-teal btn-block btn-sm">Pinjamkan Buku</button></div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <table class="table table-sm text-sm">
                                        <thead><tr><th>Buku</th><th>Peminjam</th><th>Kelas</th><th>Tgl Pinjam</th><th>Status</th><th>Aksi</th></tr></thead>
                                        <tbody>
                                            <?php if(empty($pinjam)): ?>
                                                <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada peminjaman aktif.</td></tr>
                                            <?php else: foreach($pinjam as $p): ?>
                                                <tr>
                                                    <td class="font-weight-bold"><?= htmlspecialchars($p['judul_buku']) ?></td>
                                                    <td><?= $p['peminjam_tipe'] == 'Siswa' ? htmlspecialchars($p['nama_peminjam'] ?? '-') : htmlspecialchars($p['guru_nama'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($p['nama_kelas'] ?? '-') ?></td>
                                                    <td><?= date('d/m/y', strtotime($p['tgl_pinjam'])) ?></td>
                                                    <td><span class="badge badge-warning"><?= $p['status'] ?></span></td>
                                                    <td><a href="<?= BASE_URL ?>manajemen_perpus/kembalikan?id=<?= $p['id_peminjaman'] ?>" class="btn btn-xs btn-success">Kembalikan</a></td>
                                                </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: RIWAYAT PEMINJAMAN -->
                    <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-sm">
                                <thead class="bg-teal text-white">
                                    <tr>
                                        <th>Buku</th>
                                        <th>Peminjam</th>
                                        <th>Kelas</th>
                                        <th>Tgl Pinjam</th>
                                        <th>Tgl Kembali Rencana</th>
                                        <th>Tgl Kembali Aktual</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($riwayat)): ?>
                                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat peminjaman.</td></tr>
                                    <?php else: foreach($riwayat as $r): ?>
                                        <tr>
                                            <td class="font-weight-bold"><?= htmlspecialchars($r['judul_buku']) ?></td>
                                            <td><?= $r['peminjam_tipe'] == 'Siswa' ? htmlspecialchars($r['nama_peminjam'] ?? '-') : htmlspecialchars($r['guru_nama'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($r['nama_kelas'] ?? '-') ?></td>
                                            <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($r['tgl_kembali_rencana'])) ?></td>
                                            <td><?= $r['tgl_kembali_real'] ? date('d/m/Y', strtotime($r['tgl_kembali_real'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td>
                                                <?php if($r['status'] == 'Kembali'): ?>
                                                    <span class="badge badge-success">✓ Dikembalikan</span>
                                                <?php elseif($r['status'] == 'Dipinjam'): ?>
                                                    <span class="badge badge-warning">📤 Dipinjam</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">⚠ Terlambat</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 4: AGENDA -->
                    <div class="tab-pane fade" id="tab-agenda-per" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border shadow-none">
                                    <div class="card-header bg-light"><h3 class="card-title text-sm font-weight-bold">Agenda Perpustakaan</h3></div>
                                    <form action="<?= BASE_URL ?>tugas_tambahan/save_agenda" method="POST">
                                        <input type="hidden" name="jenis" value="manajemen_perpus">
                                        <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
                                        <div class="card-body">
                                            <div class="form-group text-sm">
                                                <label>Kegiatan</label>
                                                <input type="text" name="nama_kegiatan" class="form-control" required>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white"><button type="submit" class="btn btn-teal btn-block btn-sm">Simpan</button></div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <table class="table table-sm text-sm">
                                    <thead><tr><th>Tanggal</th><th>Kegiatan</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($agendas)): ?>
                                            <tr><td colspan="3" class="text-center py-4 text-muted">Kosong.</td></tr>
                                        <?php else: foreach($agendas as $ag): ?>
                                            <tr><td><?= $ag['tanggal'] ?></td><td class="font-weight-bold"><?= $ag['nama_kegiatan'] ?></td><td><a href="<?= BASE_URL ?>tugas_tambahan/delete_agenda?id=<?= $ag['id_agenda'] ?>" class="text-danger"><i class="fas fa-trash"></i></a></td></tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Buku -->
<div class="modal fade" id="modalBuku" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= BASE_URL ?>manajemen_perpus/save_buku" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-teal text-white"><h5 class="modal-title">Data Koleksi Buku</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body text-sm">
                    <input type="hidden" name="id_buku" id="form-id-buku">
                    <div class="form-group"><label>Kode Buku / ISBN</label><input type="text" name="kode_buku" id="form-kode" class="form-control"></div>
                    <div class="form-group"><label>Judul Buku</label><input type="text" name="judul_buku" id="form-judul" class="form-control" required></div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label>Pengarang</label><input type="text" name="pengarang" id="form-pengarang" class="form-control"></div></div>
                        <div class="col-6"><div class="form-group"><label>Penerbit</label><input type="text" name="penerbit" id="form-penerbit" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label>Stok</label><input type="number" name="jumlah_stok" id="form-stok" class="form-control" value="0"></div></div>
                        <div class="col-6"><div class="form-group"><label>Lokasi Rak</label><input type="text" name="lokasi_rak" id="form-rak" class="form-control"></div></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-teal">Simpan Katalog</button></div>
            </div>
        </form>
    </div>
</div>

<script>
function updatePeminjamForm() {
    let tipe = document.getElementById('peminjam_tipe').value;
    if (tipe === 'Siswa') {
        document.getElementById('form-siswa').style.display = 'block';
        document.getElementById('form-guru').style.display = 'none';
        document.getElementById('id_peminjam').setAttribute('required', 'required');
        document.getElementById('guru_nama').removeAttribute('required');
    } else {
        document.getElementById('form-siswa').style.display = 'none';
        document.getElementById('form-guru').style.display = 'block';
        document.getElementById('id_peminjam').removeAttribute('required');
        document.getElementById('guru_nama').setAttribute('required', 'required');
    }
}

function loadSiswaByKelas() {
    let id_kelas = document.getElementById('id_kelas_peminjam').value;
    let siswaSelect = document.getElementById('id_peminjam');
    
    if (!id_kelas) {
        siswaSelect.innerHTML = '<option value="">- Pilih Kelas Dulu -</option>';
        return;
    }
    
    fetch('<?= BASE_URL ?>manajemen_perpus/get_siswa_by_kelas?id_kelas=' + id_kelas)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                siswaSelect.innerHTML = '<option value="">- Pilih Siswa -</option>';
                data.data.forEach(siswa => {
                    let option = document.createElement('option');
                    option.value = siswa.id_siswa;
                    option.textContent = siswa.nama + ' (' + siswa.nisn + ')';
                    siswaSelect.appendChild(option);
                });
            } else {
                siswaSelect.innerHTML = '<option value="">- Tidak ada siswa di kelas ini -</option>';
            }
        })
        .catch(err => {
            console.error('Error loading siswa:', err);
            siswaSelect.innerHTML = '<option value="">- Error loading data -</option>';
        });
}

$(document).ready(function(){
    updatePeminjamForm();
    
    $('.edit-buku').click(function(){
        let data = $(this).data('json');
        $('#form-id-buku').val(data.id_buku);
        $('#form-kode').val(data.kode_buku);
        $('#form-judul').val(data.judul_buku);
        $('#form-pengarang').val(data.pengarang);
        $('#form-penerbit').val(data.penerbit);
        $('#form-stok').val(data.jumlah_stok);
        $('#form-rak').val(data.lokasi_rak);
        $('#modalBuku').modal('show');
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
