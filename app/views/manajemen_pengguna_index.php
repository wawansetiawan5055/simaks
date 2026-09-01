<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Manajemen Pengguna &amp; Akun
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-info btn-sm dropdown-toggle shadow-sm px-3 mr-2" data-toggle="dropdown" style="border-radius: 8px;">
                        <i class="fas fa-id-card mr-1"></i> Cetak Kartu Login
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 12px; min-width: 260px;">
                        <a class="dropdown-item py-2" href="#" onclick="$('#modalPilihKelasKartu').modal('show'); return false;">
                            <i class="fas fa-filter text-primary mr-2"></i> Cetak Kartu Siswa (Pilih Kelas)
                        </a>
                        <a class="dropdown-item py-2" href="#" onclick="showReportPreview('<?= BASE_URL ?>manajemen_pengguna/print_kartu?type=siswa', 'Cetak Seluruh Kartu Siswa'); return false;">
                            <i class="fas fa-user-graduate text-info mr-2"></i> Cetak Seluruh Kartu Siswa
                        </a>
                        <a class="dropdown-item py-2" href="#" onclick="showReportPreview('<?= BASE_URL ?>manajemen_pengguna/print_kartu?type=guru', 'Cetak Kartu Login Guru'); return false;">
                            <i class="fas fa-chalkboard-teacher text-success mr-2"></i> Cetak Kartu Login Guru
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item py-2" href="#" onclick="showReportPreview('<?= BASE_URL ?>manajemen_pengguna/print_kartu?type=all', 'Cetak Seluruh Kartu Login'); return false;">
                            <i class="fas fa-users text-secondary mr-2"></i> Cetak Seluruh Kartu Pengguna
                        </a>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm shadow-sm px-3 mr-2" style="border-radius: 8px;"
                        onclick="confirmCleanup()">
                        <i class="fas fa-broom mr-1"></i> Bersihkan Akun Uji Coba
                    </button>
                    <a href="<?= BASE_URL ?>manajemen_pengguna/form" class="btn btn-primary btn-sm shadow-sm px-3"
                        style="border-radius: 8px;">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Pengguna
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
<div class="container-fluid">
<div class="row">
    <div class="col-md-12">
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-pill px-4" role="alert">
                <i class="fas fa-check-circle mr-2"></i> <?= $_SESSION['pesan_sukses']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-pill px-4" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> <?= $_SESSION['pesan_error']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_error']); ?>
        <?php endif; ?>

        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header p-3 bg-white border-bottom">
                <!-- Nav Tabs -->
                <ul class="nav nav-pills" id="userTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-2 px-4 shadow-none" id="guru-tab"
                            data-toggle="pill" href="#guru" role="tab" style="border-radius: 10px;">
                            <i class="fas fa-chalkboard-teacher mr-2"></i> Akun Guru
                        </a>
                    </li>
                    <li class="nav-item ml-2">
                        <a class="nav-link font-weight-bold py-2 px-4 shadow-none" id="siswa-tab" data-toggle="pill"
                            href="#siswa" role="tab" style="border-radius: 10px;">
                            <i class="fas fa-user-graduate mr-2"></i> Akun Siswa
                        </a>
                    </li>
                    <li class="nav-item ml-2">
                        <a class="nav-link font-weight-bold py-2 px-4 shadow-none" id="sistem-tab" data-toggle="pill"
                            href="#sistem" role="tab" style="border-radius: 10px;">
                            <i class="fas fa-cogs mr-2"></i> Akun Sistem
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body" style="padding: 20px 0;">
                <div class="tab-content" id="userTabsContent">
                    <!-- TAB GTK -->
                    <div class="tab-pane fade show active" id="guru" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-2" style="padding: 0 20px;">
                            <h6 class="text-muted mb-0 font-weight-bold" style="font-size: 0.8rem;"><i
                                    class="fas fa-list-ul mr-2 text-success"></i> MAKSIMALKAN DAFTAR AKUN GTK</h6>
                            <button class="btn btn-outline-success btn-xs px-3 shadow-none border-0" data-toggle="modal"
                                data-target="#modalGenerate" onclick="setTarget('guru')"
                                style="background: #f0fdf4; color: #166534; border-radius: 6px;">
                                <i class="fas fa-magic mr-1"></i> Generate Akun GTK
                            </button>
                        </div>
                        <?php renderUserTable($guru_users, 'guru'); ?>
                    </div>

                    <!-- TAB SISWA -->
                    <div class="tab-pane fade" id="siswa" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-2" style="padding: 0 20px;">
                            <h6 class="text-muted mb-0 font-weight-bold" style="font-size: 0.8rem;"><i
                                    class="fas fa-list-ul mr-2 text-primary"></i> MAKSIMALKAN DAFTAR AKUN SISWA</h6>
                            <button class="btn btn-outline-primary btn-xs px-3 shadow-none border-0" data-toggle="modal"
                                data-target="#modalGenerate" onclick="setTarget('siswa')"
                                style="background: #eff6ff; color: #1e40af; border-radius: 6px;">
                                <i class="fas fa-magic mr-1"></i> Generate Akun Siswa
                            </button>
                        </div>
                        <?php renderUserTable($siswa_users, 'siswa'); ?>
                    </div>

                    <!-- TAB SISTEM -->
                    <div class="tab-pane fade" id="sistem" role="tabpanel">
                        <div class="d-flex mb-2" style="padding: 0 20px;">
                            <h6 class="text-muted mb-0 font-weight-bold" style="font-size: 0.8rem;"><i
                                    class="fas fa-shield-alt mr-2 text-danger"></i> AKUN SISTEM & ADMINISTRATOR</h6>
                        </div>
                        <?php renderUserTable($sistem_users, 'sistem'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div><!-- end container-fluid -->
</section><!-- end content -->

<!-- Modal Generate Accounts -->
<div class="modal fade" id="modalGenerate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <form action="<?= BASE_URL ?>manajemen_pengguna/generate" method="POST">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-key text-warning mr-2"></i> Set Password
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-3">
                    <input type="hidden" name="target" id="generateTarget">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted uppercase">Password Default :</label>
                        <input type="text" name="password" class="form-control" value="123456" required>
                        <small class="text-muted">Password ini akan digunakan untuk semua akun baru.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary btn-block shadow-sm" style="border-radius: 8px;">Mulai
                        Generate</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function setTarget(target) {
        document.getElementById('generateTarget').value = target;
    }

    function confirmCleanup() {
        if (confirm("âš ï¸ PERINGATAN KRITIS!\n\nSeluruh akun pengguna uji coba (kecuali Admin Utama) akan dihapus permanen. Hubungan data guru/siswa dengan akun juga akan diputus.\n\nLanjutkan pembersihan?")) {
            window.location.href = "<?= BASE_URL ?>manajemen_pengguna/cleanup";
        }
    }
</script>

<?php
function renderUserTable($users, $type = 'sistem')
{
    $theme_color = $type == 'guru' ? '#166534' : ($type == 'siswa' ? '#1e40af' : '#b91c1c');
    $bg_color = $type == 'guru' ? '#f0fdf4' : ($type == 'siswa' ? '#eff6ff' : '#fef2f2');
    ?>
    <div style="padding: 0 20px 0 20px;">
    <div class="table-responsive bg-white rounded border" style="border-radius: 10px; overflow: hidden;">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="text-muted" style="background: #f8fafc;">
                <tr>
                    <th class="py-2 border-bottom pl-3" style="font-size: 0.7rem; letter-spacing: 1px; width: 40%">NAMA
                        PENGGUNA</th>
                    <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 25%">ID / USERNAME
                    </th>
                    <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">HAK AKSES</th>
                    <th class="py-2 border-bottom text-center"
                        style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted small"><em>Belum ada data di kategori ini.</em></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="py-1 pl-3 align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-dark"
                                            style="font-size: 0.8rem; line-height: 1.2;"><?= htmlspecialchars($u['nama_pengguna']) ?></span>
                                        <?php if ($u['email']): ?><span class="text-muted"
                                                style="font-size: 0.65rem;"><?= htmlspecialchars($u['email']) ?></span><?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="py-1 align-middle">
                                <code class="px-2 py-0.5 rounded border"
                                    style="font-size: 0.75rem; background: #f1f5f9; color: #475569; font-family: 'JetBrains Mono', monospace;">
                                                <?= htmlspecialchars($u['username']) ?>
                                            </code>
                            </td>
                            <td class="py-1 align-middle">
                                <?php if ($u['roles']): ?>
                                    <?php $roles = explode(', ', $u['roles']);
                                    foreach ($roles as $role):
                                        $badge_class = 'bg-light text-muted';
                                        $icon = 'fa-user-tag';
                                        if (strpos(strtolower($role), 'admin') !== false) {
                                            $badge_class = 'badge-danger';
                                            $icon = 'fa-shield-alt';
                                        } elseif (strpos(strtolower($role), 'guru') !== false) {
                                            $badge_class = 'badge-success';
                                            $icon = 'fa-chalkboard-teacher';
                                        } elseif (strpos(strtolower($role), 'siswa') !== false) {
                                            $badge_class = 'badge-primary';
                                            $icon = 'fa-user-graduate';
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?> font-weight-normal px-2 py-1 mb-1 mr-1 shadow-none"
                                            style="border-radius: 4px; font-size: 0.65rem; border: 1px solid rgba(0,0,0,0.05);">
                                            <i class="fas <?= $icon ?> mr-1" style="font-size: 0.6rem; opacity: 0.8;"></i> <?= $role ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted small opacity-50" style="font-size: 0.6rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-1 text-center align-middle">
                                <div class="btn-group">
                                    <button type="button"
                                        class="btn btn-xs btn-outline-info text-info border-0 p-1 mr-1"
                                        style="background: #e0f2fe; width: 24px; height: 24px; border-radius: 6px;"
                                        onclick="showQrModal(<?= $u['id_pengguna'] ?>, '<?= htmlspecialchars($u['nama_pengguna'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>', '<?= $u['qr_token'] ?? '' ?>')"
                                        title="Lihat & Cetak Kartu Login">
                                        <i class="fas fa-qrcode" style="font-size: 0.7rem;"></i>
                                    </button>
                                    <a href="<?= BASE_URL ?>manajemen_pengguna/form?id=<?= $u['id_pengguna'] ?>"
                                        class="btn btn-xs btn-outline-warning text-warning border-0 p-1 mr-1"
                                        style="background: #fffbeb; width: 24px; height: 24px; border-radius: 6px;" title="Edit">
                                        <i class="fas fa-pencil-alt" style="font-size: 0.7rem;"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>manajemen_pengguna/delete?id=<?= $u['id_pengguna'] ?>"
                                        class="btn btn-xs btn-outline-danger text-danger border-0 p-1"
                                        style="background: #fef2f2; width: 24px; height: 24px; border-radius: 6px;"
                                        onclick="return confirm('âš ï¸ Hapus akun ini?')" title="Hapus">
                                        <i class="fas fa-trash-alt" style="font-size: 0.7rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
    <?php
}
?>

<!-- Modal QR Code Login -->
<div class="modal fade" id="modalQrCode" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-qrcode text-info mr-2"></i> Kartu QR Code Login</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="qrPrintArea" class="p-3 border rounded bg-white shadow-sm d-inline-block">
                    <h6 id="qrModalNama" class="font-weight-bold mb-1 text-dark">Nama Pengguna</h6>
                    <small id="qrModalUser" class="text-muted d-block mb-3">Username: -</small>
                    <img id="qrModalImg" src="" alt="QR Code" style="width: 200px; height: 200px; border-radius: 8px;">
                    <div class="mt-2 text-muted" style="font-size: 0.7rem;">SIMAKS QR LOGIN CODE</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-info px-4 rounded-pill font-weight-bold" onclick="printQrCard()">
                    <i class="fas fa-print mr-1"></i> Cetak Kartu QR
                </button>
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentUserId = null;

function showQrModal(idUser, nama, username, qrToken) {
    const tokenToUse = qrToken || username;
    currentUserId = idUser;
    document.getElementById('qrModalNama').innerText = nama;
    document.getElementById('qrModalUser').innerText = 'Username: ' + username;
    const qrUrl = 'https://quickchart.io/qr?text=' + encodeURIComponent(tokenToUse) + '&size=250&margin=1';
    document.getElementById('qrModalImg').src = qrUrl;
    $('#modalQrCode').modal('show');
}

function printQrCard() {
    $('#modalQrCode').modal('hide');
    if (currentUserId) {
        showReportPreview('<?= BASE_URL ?>manajemen_pengguna/print_kartu?id=' + currentUserId, 'Cetak Kartu Login SIMAKS');
    }
}

function prosesCetakKartuPerKelas() {
    const sel = document.getElementById('pilih_kelas_kartu');
    const idKelas = sel.value;
    const namaKelas = sel.options[sel.selectedIndex].text;
    $('#modalPilihKelasKartu').modal('hide');
    
    let url = '<?= BASE_URL ?>manajemen_pengguna/print_kartu?type=siswa';
    let title = 'Cetak Kartu Siswa';
    if (idKelas) {
        url += '&id_kelas=' + idKelas;
        title += ' - ' + namaKelas;
    }
    showReportPreview(url, title);
}
</script>

<!-- Modal Pilih Kelas untuk Cetak Kartu Siswa -->
<div class="modal fade" id="modalPilihKelasKartu" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-id-card text-primary mr-2"></i> Cetak Kartu Siswa Per Kelas
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-4">
                <div class="form-group mb-0">
                    <label class="font-weight-bold small text-dark mb-2">Pilih Kelas yang Ingin Dicetak:</label>
                    <select id="pilih_kelas_kartu" class="form-control" style="border-radius: 8px; height: 45px;">
                        <option value="">-- Semua Kelas (Seluruh Siswa) --</option>
                        <?php if (!empty($kelas_list)): ?>
                            <?php foreach ($kelas_list as $kls): ?>
                                <option value="<?= $kls['id_kelas'] ?>">
                                    <?= htmlspecialchars($kls['nama_kelas']) ?> (Tingkat <?= $kls['tingkat'] ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle mr-1"></i> Hanya siswa aktif pada kelas terpilih yang akan dicetak kartunya.
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary px-3 rounded-pill" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill font-weight-bold shadow-sm" onclick="prosesCetakKartuPerKelas()">
                    <i class="fas fa-print mr-1"></i> Buka Kartu Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
