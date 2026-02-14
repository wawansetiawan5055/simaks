<style>
    .kop-surat-standard {
        font-family: 'Times New Roman', Times, serif !important;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        text-align: center;
        position: relative;
        color: #000;
        line-height: 1.3;
    }

    .kop-surat-standard h1,
    .kop-surat-standard h2,
    .kop-surat-standard h3,
    .kop-surat-standard h4,
    .kop-surat-standard p {
        font-family: 'Times New Roman', Times, serif !important;
        margin: 2px 0;
    }

    .kop-surat-standard .kop-logo-img {
        height: 80px;
        width: auto;
    }

    .kop-surat-standard hr {
        border-top: 2px solid black;
        border-bottom: 1px solid black;
        height: 3px;
        margin-top: 5px;
        margin-bottom: 0;
        width: 100%;
    }
</style>
<div class="kop-surat-standard">
    <?php if (!empty($kop['logo'])): ?>
        <div style="position: absolute; left: 0;">
            <img src="assets/img/<?= $kop['logo'] ?>" class="kop-logo-img">
        </div>
    <?php endif; ?>
    <div style="flex-grow: 1;">
        <h4 style="font-size: 14pt; font-weight: bold; margin: 0;">YAYASAN TARBIYATUSSHIBYAN INDONESIA</h4>
        <h3 style="font-size: 16pt; font-weight: bold; margin: 0;"><?= strtoupper($kop['kop_nama'] ?? 'NAMA SEKOLAH') ?>
        </h3>
        <p style="font-size: 11pt; font-weight: bold; margin: 0;">NPSN: <?= $kop['kop_npsn'] ?? 'NPSN' ?></p>
        <p style="font-size: 10pt; margin: 0;"><?= $kop['kop_alamat'] ?? 'Alamat Sekolah' ?></p>
        <hr>
    </div>
</div>