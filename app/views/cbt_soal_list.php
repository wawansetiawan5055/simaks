<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header"><div class="container-fluid"><div class="row mb-2">
  <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-list-ol text-danger mr-2"></i><?= $title ?></h1></div>
  <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>cbt_bank_soal">Bank Soal</a></li><li class="breadcrumb-item active">Butir Soal</li></ol></div>
</div></div></div>
<section class="content"><div class="container-fluid">
  <?php include __DIR__ . '/partials/flash_message.php'; ?>
  <div class="card card-outline card-danger shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
      <h3 class="card-title mb-0"><strong>Bank:</strong> <?= htmlspecialchars($bank['nama_bank']) ?> (<?= htmlspecialchars($bank['nama_mapel'] ?? '-') ?>)</h3>
      <div>
        <a href="<?= BASE_URL ?>cbt_bank_soal/create_soal?id_bank=<?= $bank['id_bank'] ?>" class="btn btn-sm btn-danger"><i class="fas fa-plus mr-1"></i> Tambah Butir Soal</a>
        <a href="<?= BASE_URL ?>cbt_bank_soal" class="btn btn-sm btn-secondary ml-1"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Bank Soal</a>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover mb-0">
          <thead class="thead-light"><tr><th width="40">No</th><th width="80">Tipe</th><th>Pertanyaan</th><th width="80">Kunci</th><th width="60">Bobot</th><th width="100">Aksi</th></tr></thead>
          <tbody>
            <?php if (empty($soal_list)): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada butir soal di bank ini. Klik "Tambah Butir Soal".</td></tr>
            <?php else: foreach ($soal_list as $i => $s): ?>
            <tr>
              <td class="text-center"><?= $s['nomor_urut'] ?: ($i+1) ?></td>
              <td><span class="badge badge-<?= $s['tipe_soal']==='pg'?'primary':'warning' ?>"><?= strtoupper($s['tipe_soal']) ?></span></td>
              <td><?= nl2br(htmlspecialchars(substr(strip_tags($s['pertanyaan']), 0, 150))) ?><?= strlen(strip_tags($s['pertanyaan']))>150?'...':'' ?></td>
              <td class="text-center"><strong><?= htmlspecialchars($s['kunci_pg'] ?? '-') ?></strong></td>
              <td class="text-center"><?= $s['bobot'] ?></td>
              <td>
                <a href="<?= BASE_URL ?>cbt_bank_soal/edit_soal?id_soal=<?= $s['id_soal'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                <a href="<?= BASE_URL ?>cbt_bank_soal/delete_soal?id_soal=<?= $s['id_soal'] ?>&id_bank=<?= $bank['id_bank'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Hapus soal ini?')"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div></section>
<?php include __DIR__ . '/partials/footer.php'; ?>
