<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid"><h1>Absensi Siswa per Mata Pelajaran</h1></div>
</section>
<section class="content">
<div class="container-fluid">
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Pilih Kelas dan Tanggal</h3></div>
    <form action="index.php" method="GET">
        <input type="hidden" name="mod" value="absensi_mapel">
        <input type="hidden" name="act" value="form">
        <div class="card-body">
            <div class="form-group">
                <label>Kelas yang Anda Ajar</label>
                <select name="id_kelas" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelas_diajar as $k): ?>
                        <!-- Format Tampilan: Nama Kelas -->
                        <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>
        <div class="card-footer"><button type="submit" class="btn btn-primary">Lanjutkan ke Formulir Absensi</button></div>
    </form>
</div>
</div>
</section>
<?php include __DIR__.'/partials/footer.php'; ?>