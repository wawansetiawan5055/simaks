<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-book-open mr-2"></i> Jurnal Umum & Memorial</h1>
            </div>
            <div class="col-sm-6 text-right">
                <!-- Breadcrumb could go here -->
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-laporan-link" data-toggle="pill" href="#tab-laporan"
                            role="tab"><i class="fas fa-book"></i> Laporan Jurnal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-input-link" data-toggle="pill" href="#tab-input" role="tab"><i
                                class="fas fa-pen-nib"></i> Input Jurnal Memorial</a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">

                    <!-- TAB 1: LAPORAN JURNAL -->
                    <div class="tab-pane fade show active" id="tab-laporan" role="tabpanel">

                        <form action="" method="get" class="mb-3">
                            <input type="hidden" name="mod" value="keuangan_jurnal">
                            <div class="form-row align-items-end">
                                <div class="col-md-3">
                                    <label>Dari Tanggal</label>
                                    <input type="date" name="tanggal_dari" class="form-control"
                                        value="<?= $_GET['tanggal_dari'] ?? date('Y-m-01') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Sampai Tanggal</label>
                                    <input type="date" name="tanggal_sampai" class="form-control"
                                        value="<?= $_GET['tanggal_sampai'] ?? date('Y-m-t') ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-block"><i
                                            class="fas fa-search"></i> Tampilkan</button>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-default btn-block" onclick="window.print()"><i
                                            class="fas fa-print"></i> Cetak</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-sm table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">No. Bukti</th>
                                        <th>Keterangan / Akun</th>
                                        <th class="text-center">Ref</th>
                                        <th class="text-right">Debit</th>
                                        <th class="text-right">Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalDebit = 0;
                                    $totalKredit = 0;
                                    if (!empty($jurnal)):
                                        foreach ($jurnal as $row):
                                            // Formatting Logic: If CREDIT, indent the name
                                            $isDebit = ($row['debit'] > 0);
                                            $style = $isDebit ? "font-weight:bold;" : "padding-left: 30px;";

                                            $totalDebit += $row['debit'];
                                            $totalKredit += $row['kredit'];
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= date('d/m/y', strtotime($row['tanggal'])) ?></td>
                                                <td class="text-center"><?= $row['no_bukti'] ?></td>
                                                <td style="<?= $style ?>">
                                                    <?php if ($isDebit): ?>
                                                        <?= $row['nama_akun'] ?> <br>
                                                        <small
                                                            class="text-muted font-weight-normal"><?= $row['keterangan'] ?></small>
                                                    <?php else: ?>
                                                        <?= $row['nama_akun'] ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $row['kode_akun'] ?></td>
                                                <td class="text-right">
                                                    <?= ($row['debit'] > 0) ? number_format($row['debit'], 0, ',', '.') : '-' ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= ($row['kredit'] > 0) ? number_format($row['kredit'], 0, ',', '.') : '-' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">Tidak ada data transaksi.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="4" class="text-right">TOTAL</td>
                                        <td class="text-right"><?= number_format($totalDebit, 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($totalKredit, 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: INPUT MEMORIAL -->
                    <div class="tab-pane fade" id="tab-input" role="tabpanel">
                        <form id="form-memorial" method="post">
                            <input type="hidden" name="mod" value="keuangan_memorial">

                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Header Jurnal</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>No. Bukti</label>
                                                <input type="text" name="no_bukti" class="form-control"
                                                    value="MEM/<?= date('Ymd') ?>/<?= rand(100, 999) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control"
                                                    value="<?= date('Y-m-d') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Keterangan Memorial</label>
                                                <input type="text" name="keterangan" class="form-control"
                                                    placeholder="Contoh: Penyusutan Aset Bulan Januari" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-default">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">Detail Akun</h3>
                                    <button type="button" class="btn btn-sm btn-success" onclick="addRow()"><i
                                            class="fas fa-plus"></i> Tambah Baris</button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped" id="table-input-detail">
                                        <thead>
                                            <tr>
                                                <th width="40%">Akun</th>
                                                <th width="20%">Posisi</th>
                                                <th width="30%">Jumlah (Rp)</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-detail">
                                            <!-- Rows added by JS -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-right">Total Balance:</th>
                                                <th id="balance-display" class="text-right">0</th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <span id="status-balance" class="badge badge-success">BALANCE</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary float-right" id="btn-save" disabled><i
                                            class="fas fa-save"></i> Simpan Jurnal</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- TEMPLATE ROW -->
<template id="row-template">
    <tr>
        <td>
            <select name="akun[]" class="form-control select2-me" required>
                <option value="">-- Pilih Akun --</option>
                <optgroup label="Harta & Kas (Rekening)">
                    <?php foreach ($rekening_list as $r): ?>
                        <option value="<?= $r['kode_rekening'] ?>|<?= $r['nama_rekening'] ?>">[<?= $r['kode_rekening'] ?>]
                            <?= $r['nama_rekening'] ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Akun Lainnya (Pendapatan/Beban)">
                    <?php foreach ($jenis_list as $j): ?>
                        <option value="<?= $j['kode_akun'] ?>|<?= $j['nama_jenis'] ?>">[<?= $j['kode_akun'] ?>]
                            <?= $j['nama_jenis'] ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </td>
        <td>
            <select name="tipe[]" class="form-control tipe-select" onchange="calculateBalance()">
                <option value="DEBIT">Debit</option>
                <option value="KREDIT">Kredit</option>
            </select>
        </td>
        <td>
            <input type="number" name="jumlah[]" class="form-control jumlah-input" oninput="calculateBalance()"
                placeholder="0" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i
                    class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

<script>
    function addRow() {
        var template = document.getElementById('row-template');
        var clone = template.content.cloneNode(true);
        document.getElementById('tbody-detail').appendChild(clone);
        calculateBalance();
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        calculateBalance();
    }

    function calculateBalance() {
        var debits = 0;
        var credits = 0;

        var rows = document.querySelectorAll('#tbody-detail tr');
        rows.forEach(function (row) {
            var tipe = row.querySelector('.tipe-select').value;
            var jumlah = parseFloat(row.querySelector('.jumlah-input').value) || 0;

            if (tipe === 'DEBIT') debits += jumlah;
            else credits += jumlah;
        });

        var diff = debits - credits;
        var display = document.getElementById('balance-display');
        var status = document.getElementById('status-balance');
        var btn = document.getElementById('btn-save');

        display.innerHTML = new Intl.NumberFormat('id-ID').format(diff);

        if (rows.length > 0 && diff === 0 && debits > 0) {
            status.className = 'badge badge-success';
            status.innerHTML = 'BALANCE (Seimbang)';
            btn.disabled = false;
        } else {
            status.className = 'badge badge-danger';
            status.innerHTML = 'TIDAK BALANCE (Selisih: ' + new Intl.NumberFormat('id-ID').format(diff) + ')';
            btn.disabled = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Add 2 initial rows for convenience
        addRow();
        addRow();

        var form = document.getElementById('form-memorial');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (document.getElementById('btn-save').disabled) return;

                var formData = new FormData(form);

                fetch('<?= BASE_URL ?>keuangan_memorial/save', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            alert('Berhasil: ' + res.message);
                            location.reload(); // Reload to see in report tab
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    })
                    .catch(err => alert('Error Sistem'));
            });
        }
    });
</script>

<?php include '../app/views/partials/footer.php'; ?>