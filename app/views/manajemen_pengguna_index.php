<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-users-cog mr-2"></i> Manajemen Pengguna</h1>
            </div>
            <div class="col-sm-6 text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-danger btn-sm shadow-sm px-3 mr-2" style="border-radius: 8px;"
                        onclick="confirmCleanup()">
                        <i class="fas fa-broom mr-1"></i> Bersihkan Akun Uji Coba
                    </button>
                    <a href="index.php?mod=manajemen_pengguna&act=form" class="btn btn-primary btn-sm shadow-sm px-3"
                        style="border-radius: 8px;">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Pengguna
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">

            <div class="px-4">
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
            </div>

            <div class="card-body pt-0">
                <!-- Nav Tabs -->
                <ul class="nav nav-pills mb-3 border-bottom pb-2" id="userTabs" role="tablist">
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

                <div class="tab-content" id="userTabsContent">
                    <!-- TAB GTK -->
                    <div class="tab-pane fade show active" id="guru" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
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
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
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
                        <div class="d-flex mb-3 px-1">
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

<!-- Modal Generate Accounts -->
<div class="modal fade" id="modalGenerate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form action="index.php?mod=manajemen_pengguna&act=generate" method="POST">
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
        if (confirm("⚠️ PERINGATAN KRITIS!\n\nSeluruh akun pengguna uji coba (kecuali Admin Utama) akan dihapus permanen. Hubungan data guru/siswa dengan akun juga akan diputus.\n\nLanjutkan pembersihan?")) {
            window.location.href = "index.php?mod=manajemen_pengguna&act=cleanup";
        }
    }
</script>

<?php
function renderUserTable($users, $type = 'sistem')
{
    $theme_color = $type == 'guru' ? '#166534' : ($type == 'siswa' ? '#1e40af' : '#b91c1c');
    $bg_color = $type == 'guru' ? '#f0fdf4' : ($type == 'siswa' ? '#eff6ff' : '#fef2f2');
    ?>
    <div class="table-responsive bg-white rounded border">
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
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 shadow-none border"
                                        style="width: 28px; height: 28px; font-size: 0.65rem; color: <?= $theme_color ?>; background: <?= $bg_color ?>; font-weight: 800;">
                                        <?= strtoupper(substr($u['nama_pengguna'], 0, 1)) ?>
                                    </div>
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
                                    <a href="index.php?mod=manajemen_pengguna&act=form&id=<?= $u['id_pengguna'] ?>"
                                        class="btn btn-xs btn-outline-warning text-warning border-0 p-1 mr-1"
                                        style="background: #fffbeb; width: 24px; height: 24px; border-radius: 6px;" title="Edit">
                                        <i class="fas fa-pencil-alt" style="font-size: 0.7rem;"></i>
                                    </a>
                                    <a href="index.php?mod=manajemen_pengguna&act=delete&id=<?= $u['id_pengguna'] ?>"
                                        class="btn btn-xs btn-outline-danger text-danger border-0 p-1"
                                        style="background: #fef2f2; width: 24px; height: 24px; border-radius: 6px;"
                                        onclick="return confirm('⚠️ Hapus akun ini?')" title="Hapus">
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
    <?php
}
?>

<?php include __DIR__ . '/partials/footer.php'; ?>