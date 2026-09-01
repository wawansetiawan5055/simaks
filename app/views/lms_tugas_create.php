<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    .lms-card {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
        overflow: hidden;
        background: #ffffff;
    }
    .lms-card-header {
        background: transparent !important;
        border-bottom: 1px solid #f0f0f0 !important;
        padding: 20px 25px !important;
    }
    .lms-card-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin: 0;
    }
    .form-control-lms {
        border-radius: 10px !important;
        padding: 12px 15px !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.3s ease;
    }
    .form-control-lms:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
    }
    .btn-lms {
        border-radius: 10px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        transition: all 0.3s ease;
    }
    .btn-lms-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        color: #fff !important;
    }
    .btn-lms-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3) !important;
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Buat Tugas Pembelajaran Baru
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>lms/tugas_list_guru" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card lms-card shadow-lg">
                    <div class="lms-card-header d-flex align-items-center">
                        <div class="icon-box mr-3 bg-light p-2 rounded">
                            <i class="fas fa-tasks text-success"></i>
                        </div>
                        <h3 class="lms-card-title">Buat Tugas Baru</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
                                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= BASE_URL ?>lms/tugas_create" method="POST">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">Tugas Untuk Materi Apa?</label>
                                    <select name="id_materi" class="form-control form-control-lms select2" id="selectMateri" required>
                                        <option value="">Pilih Materi Induk</option>
                                        <?php foreach ($materi_list as $mat): ?>
                                            <option value="<?php echo $mat['id_materi']; ?>" data-mapel="<?php echo $mat['id_mapel']; ?>" data-judul="<?php echo htmlspecialchars($mat['judul_materi']); ?>">
                                                [<?php echo htmlspecialchars($mat['nama_mapel']); ?>] <?php echo htmlspecialchars($mat['judul_materi']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="id_mapel" id="hiddenMapel">
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">Judul Tugas</label>
                                    <input type="text" name="judul_tugas" id="judulTugas" class="form-control form-control-lms" placeholder="Contoh: Tugas Mandiri - Struktur Atom" required>
                                </div>
                                
                                <div class="col-md-8 mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">Pilih Kelas (Rombel)</label>
                                    <select name="id_kelas" class="form-control form-control-lms select2" required>
                                        <option value="">Pilih Rombel</option>
                                        <?php foreach ($rombel_list as $r): ?>
                                            <option value="<?php echo $r['id_kelas']; ?>">
                                                <?php echo htmlspecialchars($r['nama_kelas']); ?> (Tingkat <?php echo $r['tingkat']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted mt-1 d-block">Pilih kelas yang akan mengerjakan tugas ini.</small>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">Bobot Nilai (%)</label>
                                    <input type="number" name="bobot_nilai" class="form-control form-control-lms" value="100" min="1" max="100" required>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">Deadline Pengumpulan</label>
                                    <input type="datetime-local" name="deadline" class="form-control form-control-lms" required>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">Instruksi Pengerjaan</label>
                                    <textarea name="instruksi" class="form-control form-control-lms" rows="6" placeholder="Berikan instruksi lengkap mengenai tugas yang harus dikerjakan siswa..."></textarea>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="<?= BASE_URL ?>lms/tugas_list" class="text-muted font-weight-bold">
                                            <i class="fas fa-arrow-left mr-1"></i> Batal
                                        </a>
                                        <button type="submit" class="btn btn-lms btn-lms-success px-5 shadow-sm">
                                            <i class="fas fa-save mr-2"></i> Publikasikan Tugas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('selectMateri').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            var mapelId = selectedOption.getAttribute('data-mapel');
            var judul = selectedOption.getAttribute('data-judul');
            document.getElementById('hiddenMapel').value = mapelId;
            document.getElementById('judulTugas').value = "Tugas: " + judul;
        }
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
