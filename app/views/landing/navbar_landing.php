<?php
// app/views/landing/navbar_landing.php
$config = $config ?? (isset($data['config']) ? $data['config'] : []);
$logoPath = !empty($config['school_logo']) ? BASE_URL . $config['school_logo'] : BASE_URL . 'assets/img/logo.png';
$b_url = BASE_URL . "landing";
?>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid px-lg-5">
        <a class="navbar-brand d-flex align-items-center" href="<?= $b_url ?>#home">
            <img src="<?= $logoPath ?>" alt="Logo" width="48" height="48" class="rounded bg-white p-1 shadow-sm"
                onerror="this.src='https://placehold.co/48x48/ffffff/000000?text=LOGO'">
            <div class="ms-3">
                <div class="fw-bold text-white text-uppercase"
                    style="line-height:1; font-size:1.15rem; letter-spacing:0.8px;">
                    <?= htmlspecialchars($config['school_name'] ?? 'SMA PLUS AL MANSHURIYAH') ?>
                </div>
                <small class="text-warning fw-bold text-uppercase"
                    style="font-size:0.72rem; letter-spacing:1px; opacity: 0.9;">
                    <?= htmlspecialchars($config['school_motto'] ?? 'Unggul, Terampil, Dan Mandiri') ?>
                </small>
            </div>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto gap-2 align-items-center">
                <li class="nav-item"><a class="nav-link px-3 active" href="<?= $b_url ?>#home">Beranda</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link px-3 dropdown-toggle" href="#" data-bs-toggle="dropdown">Profil</a>
                    <ul class="dropdown-menu shadow border-0 mt-2">
                        <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>landing/profil_sekolah">Profil
                                Sekolah</a></li>
                        <li><a class="dropdown-item py-2"
                                href="<?= BASE_URL ?>landing/profil_sekolah#visi-misi">Visi & Misi</a></li>
                        <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>landing/guru_list">Profil GTK</a>
                        </li>
                        <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>landing/siswa_list">Daftar
                                Siswa</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link px-3 dropdown-toggle" href="#" data-bs-toggle="dropdown">Program</a>
                    <ul class="dropdown-menu shadow border-0 mt-2">
                        <?php
                        $programs_menu = $data['programs'] ?? [];
                        if (!empty($programs_menu)):
                            foreach ($programs_menu as $p): ?>
                                <li><a class="dropdown-item py-2"
                                        href="<?= BASE_URL ?>landing/program_detail?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a>
                                </li>
                            <?php endforeach;
                        else: ?>
                            <li><a class="dropdown-item py-2"
                                    href="<?= BASE_URL ?>landing/program_detail?id=1">Kewirausahaan</a></li>
                            <li><a class="dropdown-item py-2"
                                    href="<?= BASE_URL ?>landing/program_detail?id=2">Tahfidz Al Qur'an</a></li>
                            <li><a class="dropdown-item py-2"
                                    href="<?= BASE_URL ?>landing/program_detail?id=3">Pembiasaan Ibadah</a></li>
                            <li><a class="dropdown-item py-2"
                                    href="<?= BASE_URL ?>landing/program_detail?id=4">Kajian Kitab Kuning</a></li>
                        <?php endif; ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item py-2"
                                href="<?= BASE_URL ?>landing/ekstrakurikuler_list">Ekstrakurikuler</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link px-3" href="<?= $b_url ?>#galeri">Galeri</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="<?= $b_url ?>#informasi">Informasi</a></li>

                <!-- CTA BUTTONS -->
                <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                    <a class="btn-spmb" href="<?= BASE_URL ?>landing/ppdb_form">
                        SPMB 2026
                    </a>
                </li>
                <li class="nav-item mt-2 mt-lg-0">
                    <a class="btn-simaks-container" href="<?= BASE_URL ?>auth/login">
                        <div class="btn-simaks-inner">
                            <div class="btn-simaks-front">SIMAKS</div>
                            <div class="btn-simaks-back">LOGIN</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar {
        padding: 0.8rem 0;
        backdrop-filter: blur(10px);
        background: #000033 !important;
        /* Extra Dark Blue */
        border-bottom: 2px solid #f59e0b;
        /* Thinner Golden Border */
    }

    .nav-link {
        font-weight: 500;
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .nav-link:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.1);
    }

    .nav-link.active {
        color: #ffffff !important;
        background: rgba(245, 158, 11, 0.2);
        border-radius: 8px;
        font-weight: 700;
    }

    .btn-spmb {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 8px;
        width: 110px;
        background: linear-gradient(45deg, #f59e0b, #fbbf24);
        color: #1a237e !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .btn-spmb:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        background: linear-gradient(45deg, #fbbf24, #f59e0b);
    }

    .btn-simaks-container {
        display: inline-block;
        perspective: 1000px;
        text-decoration: none;
    }

    .btn-simaks-inner {
        display: grid;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        transform-style: preserve-3d;
    }

    .btn-simaks-container:hover .btn-simaks-inner {
        transform: rotateY(180deg);
    }

    .btn-simaks-front,
    .btn-simaks-back {
        grid-area: 1 / 1;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px 8px;
        width: 110px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: white !important;
        border: 2px solid #dc3545;
    }

    .btn-simaks-front {
        background: transparent;
    }

    .btn-simaks-back {
        background: #f1061aff;
        border-color: #eba309ff;
        transform: rotateY(180deg);
    }

    @media (max-width: 991.98px) {
        .navbar-nav {
            background: rgba(0, 0, 0, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin-top: 15px;
        }

        .btn-spmb,
        .btn-simaks-container {
            width: 100%;
        }

        .btn-spmb {
            justify-content: center;
        }

        .btn-simaks-container {
            display: block;
        }
    }
</style>
</div>
</nav>