<?php
// kewirausahaan_tabs_extra.php - Additional tabs content
?>

<!-- TAB 2: TAHAPAN -->
<div class="tab-pane fade <?= ($tab == 'tahapan') ? 'show active' : '' ?>" id="tahapan" role="tabpanel">
    <div class="mb-3">
        <button class="btn btn-primary" onclick="showAddTahapan(); return false;">
            <i class="fas fa-plus"></i> Tambah Tahapan
        </button>
    </div>
    
    <table class="table table-bordered">
        <thead class="bg-light">
            <tr>
                <th width="5%">#</th>
                <th>Nama Tahapan</th>
                <th width="15%">Tanggal Mulai</th>
                <th width="15%">Tanggal Selesai</th>
                <th width="15%">Status</th>
                <th width="15%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tahapan_list)): ?>
                <tr><td colspan="6" class="text-center text-muted">Belum ada tahapan</td></tr>
            <?php else: ?>
                <?php foreach ($tahapan_list as $t): ?>
                <tr>
                    <td class="text-center"><?= $t['urutan'] ?></td>
                    <td><?= htmlspecialchars($t['nama_tahapan']) ?></td>
                    <td><?= $t['tanggal_mulai'] ? date('d/m/Y', strtotime($t['tanggal_mulai'])) : '-' ?></td>
                    <td><?= $t['tanggal_selesai'] ? date('d/m/Y', strtotime($t['tanggal_selesai'])) : '-' ?></td>
                    <td>
                        <?php 
                        $badge_class = ['Belum Mulai' => 'secondary', 'Sedang Berjalan' => 'primary', 'Selesai' => 'success'];
                        ?>
                        <span class="badge badge-<?= $badge_class[$t['status']] ?>"><?= $t['status'] ?></span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-xs" onclick="editTahapan(<?= htmlspecialchars(json_encode($t)) ?>); return false;">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="<?= BASE_URL ?>kewirausahaan/tahapan_delete?id_kewirausahaan=<?= $id ?>&id_tahapan=<?= $t['id_tahapan'] ?>" 
                           class="btn btn-danger btn-xs" onclick="return confirmDelete(event)">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- TAB: PRODUK -->
<div class="tab-pane fade <?= ($tab == 'produk') ? 'show active' : '' ?>" id="produk" role="tabpanel">
    <div class="mb-3">
        <button class="btn btn-primary" onclick="showAddProduk(); return false;">
            <i class="fas fa-plus"></i> Tambah Produk
        </button>
    </div>
    
    <div class="row">
        <?php if (empty($produk_list)): ?>
            <div class="col-12">
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> Belum ada produk.</div>
            </div>
        <?php else: ?>
            <?php foreach ($produk_list as $p): ?>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <?php if ($p['foto_produk']): ?>
                        <img src="<?= $p['foto_produk'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white text-center" style="height: 200px; line-height: 200px;">
                            <i class="fas fa-box fa-3x"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body p-2">
                        <h6 class="mb-1"><?= htmlspecialchars($p['nama_produk']) ?></h6>
                        <p class="text-sm mb-1"><?= htmlspecialchars($p['deskripsi']) ?></p>
                        <p class="mb-1"><strong>Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></strong></p>
                        <p class="text-muted text-sm mb-2">Stok: <?= $p['stok'] ?></p>
                        <a href="<?= BASE_URL ?>kewirausahaan/produk_delete?id_kewirausahaan=<?= $id ?>&id_produk=<?= $p['id_produk'] ?>" 
                           class="btn btn-danger btn-xs btn-block" onclick="return confirmDelete(event)">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- TAB: KEUANGAN -->
<div class="tab-pane fade <?= ($tab == 'keuangan') ? 'show active' : '' ?>" id="keuangan" role="tabpanel">
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h4>Rp <?= number_format($summary['total_modal'] ?? 0, 0, ',', '.') ?></h4>
                    <p>Modal</p>
                </div>
                <div class="icon"><i class="fas fa-wallet"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h4>Rp <?= number_format($summary['total_pemasukan'] ?? 0, 0, ',', '.') ?></h4>
                    <p>Pemasukan</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-up"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h4>Rp <?= number_format($summary['total_pengeluaran'] ?? 0, 0, ',', '.') ?></h4>
                    <p>Pengeluaran</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h4>Rp <?= number_format($summary['saldo'] ?? 0, 0, ',', '.') ?></h4>
                    <p>Saldo</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <button class="btn btn-primary" onclick="showAddKeuangan(); return false;">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </button>
    </div>
    
    <table class="table table-bordered table-striped">
        <thead class="bg-light">
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($keuangan_list)): ?>
                <tr><td colspan="5" class="text-center text-muted">Belum ada transaksi</td></tr>
            <?php else: ?>
                <?php foreach ($keuangan_list as $k): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($k['tanggal'])) ?></td>
                    <td>
                        <?php 
                        $badge_jenis = ['Modal' => 'info', 'Pemasukan' => 'success', 'Pengeluaran' => 'danger'];
                        ?>
                        <span class="badge badge-<?= $badge_jenis[$k['jenis']] ?>"><?= $k['jenis'] ?></span>
                    </td>
                    <td><?= htmlspecialchars($k['keterangan']) ?></td>
                    <td class="text-right">Rp <?= number_format($k['jumlah'], 0, ',', '.') ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>kewirausahaan/keuangan_delete?id_kewirausahaan=<?= $id ?>&id_transaksi=<?= $k['id_transaksi'] ?>" 
                           class="btn btn-danger btn-xs" onclick="return confirmDelete(event)">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODALS -->

<!-- Modal Tahapan -->
<div class="modal" id="modalTahapan" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>kewirausahaan/tahapan_save" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah/Edit Tahapan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" value="<?= $id ?>">
                    <input type="hidden" name="id_tahapan" id="tahapan_id">
                    
                    <div class="form-group">
                        <label>Nama Tahapan</label>
                        <input type="text" name="nama_tahapan" id="tahapan_nama" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tahapan_mulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tahapan_selesai" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="tahapan_status" class="form-control">
                            <option value="Belum Mulai">Belum Mulai</option>
                            <option value="Sedang Berjalan">Sedang Berjalan</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" name="urutan" id="tahapan_urutan" class="form-control" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="tahapan_ket" class="form-control" rows="3"></textarea>
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

<!-- Modal Produk -->
<div class="modal" id="modalProduk" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>kewirausahaan/produk_save" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" value="<?= $id ?>">
                    
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stok" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Foto Produk</label>
                        <input type="file" name="foto_produk" class="form-control" accept="image/*">
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

<!-- Modal Keuangan -->
<div class="modal" id="modalKeuangan" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>kewirausahaan/keuangan_save" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" value="<?= $id ?>">
                    
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Jenis Transaksi</label>
                        <select name="jenis" class="form-control" required>
                            <option value="Modal">Modal</option>
                            <option value="Pemasukan">Pemasukan</option>
                            <option value="Pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control" required>
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

