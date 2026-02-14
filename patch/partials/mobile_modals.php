<!-- MOBILE MODALS FOR BOTTOM NAVIGATION -->

<!-- 1. MOBILE GRID MENU MODAL (SUPER APP QUICK MENU) -->
<style>
    /* Carousel Indicators Stylings */
    .mobile-menu-dots {
        position: relative !important;
        bottom: 0 !important;
        margin: 0 !important;
        display: flex !important;
        justify-content: center;
        align-items: center;
        width: 100%;
        z-index: 1050;
    }
    .mobile-menu-dots li {
        background-color: #cbd5e1 !important;
        width: 10px !important;
        height: 10px !important;
        border-radius: 50% !important;
        margin: 0 6px !important;
        border: none !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 1 !important;
        cursor: pointer;
    }
    .mobile-menu-dots li.active {
        background-color: #3b82f6 !important;
        width: 24px !important;
        border-radius: 10px !important;
    }

    /* Modal Height Fixers */
    .icon-grid-drawer .modal-content {
        height: calc(100vh - 70px) !important;
        max-height: calc(100vh - 70px) !important;
    }
</style>

<div class="modal fade icon-grid-drawer" id="mobileGridModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 30px 30px 0 0; background: #fff !important; height: calc(100vh - 70px) !important; display: flex; flex-direction: column;">
            <div class="modal-header border-0 pb-0 justify-content-center pt-2" style="background: #fff !important; border-radius: 30px 30px 0 0; flex-shrink: 0;">
                <div style="width: 40px; height: 5px; background: #e2e8f0; border-radius: 10px; margin-bottom: 5px;"></div>
            </div>
            
            <div class="px-3 pt-2 flex-shrink-0">
                <h6 class="text-center font-weight-bold mb-3" style="color: #1e293b;">Menu Cepat SIMAKS</h6>
            </div>

            <div class="modal-body p-0" style="flex: 1; overflow-y: auto; overflow-x: hidden;">
                <?php 
                $grid_items = [];
                $icon_colors = [
                    'Absensi' => 'bg-success', 'Jadwal' => 'bg-info', 'Nilai' => 'bg-warning', 
                    'Keuangan' => 'bg-primary', 'Siswa' => 'bg-teal', 'Guru' => 'bg-purple',
                    'Laporan' => 'bg-indigo', 'Pengaturan' => 'bg-secondary', 'Master' => 'bg-danger',
                    'PPDB' => 'bg-pink', 'Profil' => 'bg-maroon', 'Akademik' => 'bg-orange',
                    'Menu' => 'bg-success', 'User' => 'bg-primary', 'Peran' => 'bg-info',
                    'Akses' => 'bg-olive', 'Utilitas' => 'bg-fuchsia', 'Database' => 'bg-fuchsia',
                    'Perangkat' => 'bg-indigo', 'Kurikulum' => 'bg-warning', 'Mutasi' => 'bg-orange',
                    'Default' => 'bg-primary'
                ];

                if(!function_exists('flattenMenuGlobal')) {
                    function flattenMenuGlobal($menus, &$result) {
                        foreach ($menus as $menu) {
                            if ($menu['id_menu'] == 1) continue;
                            if (!empty($menu['children'])) flattenMenuGlobal($menu['children'], $result);
                            elseif ($menu['link'] !== '#' && $menu['link'] !== 'index.php#') $result[] = $menu;
                        }
                    }
                }

                $flat_items = [];
                flattenMenuGlobal($user_menu ?? [], $flat_items);
                $grid_items = $flat_items;
                $items_per_slide = 24;
                $menu_chunks = array_chunk($grid_items, $items_per_slide);
                
                if(!function_exists('getGridColorGlobal')) {
                    function getGridColorGlobal($name, $colors) {
                        foreach($colors as $key => $color) { if(stripos($name, $key) !== false) return $color; }
                        return $colors['Default'];
                    }
                }

                if(!function_exists('getGridIconGlobal')) {
                    function getGridIconGlobal($name, $default) {
                        $overrides = [
                            'Absensi' => 'fas fa-user-check', 'Siswa' => 'fas fa-user-graduate', 'Guru' => 'fas fa-chalkboard-teacher',
                            'Jadwal' => 'fas fa-calendar-alt', 'Nilai' => 'fas fa-file-invoice', 'Keuangan' => 'fas fa-wallet',
                            'Bayar' => 'fas fa-receipt', 'Laporan' => 'fas fa-chart-pie', 'Pengaturan' => 'fas fa-sliders-h',
                            'Master' => 'fas fa-database', 'PPDB' => 'fas fa-user-plus', 'Jurnal' => 'fas fa-book-reader',
                            'Kurikulum' => 'fas fa-graduation-cap', 'Profil' => 'fas fa-user-circle', 'Tabungan' => 'fas fa-piggy-bank',
                            'Mutasi' => 'fas fa-exchange-alt', 'Lulus' => 'fas fa-user-shield', 'Alumni' => 'fas fa-users-cog',
                            'Kelas' => 'fas fa-school', 'Pelajaran' => 'fas fa-book-open', 'Tahun' => 'fas fa-calendar-check',
                            'Kegiatan' => 'fas fa-walking', 'Jam' => 'fas fa-clock', 'Dashboard' => 'fas fa-tachometer-alt',
                            'Pemasukan' => 'fas fa-file-import', 'Pengeluaran' => 'fas fa-file-export', 'Rekening' => 'fas fa-university',
                            'Tagihan' => 'fas fa-file-invoice-dollar', 'Group' => 'fas fa-layer-group', 'Kategori' => 'fas fa-tags',
                            'Pendaftaran' => 'fas fa-edit', 'Verifikasi' => 'fas fa-user-check', 'Seleksi' => 'fas fa-filter',
                            'Statistik' => 'fas fa-chart-bar', 'Promosi' => 'fas fa-user-tie', 'Administrasi' => 'fas fa-folder-open',
                            'Program' => 'fas fa-tasks', 'GTK' => 'fas fa-chalkboard-teacher', 'Mutasi Masuk' => 'fas fa-right-to-bracket',
                        ];
                        foreach($overrides as $key => $icon) { if(stripos($name, $key) !== false) return $icon; }
                        return $default;
                    }
                }
                ?>

                <div id="mobileMenuCarousel" class="carousel slide" data-interval="false" data-wrap="true" style="width: 100%; touch-action: pan-y;">
                    <div class="carousel-inner">
                        <?php foreach($menu_chunks as $index => $chunk): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <div class="row text-center m-0 pt-2 pb-4">
                                    <?php foreach($chunk as $menu): 
                                        $has_sub = !empty($menu['children']);
                                        $bg_color = getGridColorGlobal($menu['nama_menu'], $icon_colors);
                                        $onclick = $has_sub ? 'openSubmenuFromData(this); return false;' : '';
                                        $href = $has_sub ? 'javascript:void(0);' : $menu['link'];
                                        $dataAttrs = $has_sub ? 'data-has-submenu="true" data-submenu-name="' . htmlspecialchars($menu['nama_menu'], ENT_QUOTES) . '" data-submenu-json=\'' . htmlspecialchars(json_encode($menu['children']), ENT_QUOTES) . '\'' : '';
                                    ?>
                                        <div class="col-3 mb-3 px-1">
                                            <a href="<?= $href ?>" <?= $dataAttrs ?> onclick="<?= $onclick ?>" class="text-decoration-none text-dark d-block icon-hover">
                                                <div class="icon-circle mb-1 mx-auto shadow-sm text-white d-flex align-items-center justify-content-center <?= $bg_color ?>" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1rem;">
                                                    <i class="<?= getGridIconGlobal($menu['nama_menu'], $menu['icon']) ?>"></i>
                                                </div>
                                                <span style="font-size: 0.65rem; font-weight: 600; display: block; line-height: 1.1; color: #343a40; min-height: 2.2em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                    <?= $menu['nama_menu'] ?>
                                                </span>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

                <div class="modal-footer border-0 p-0 d-flex justify-content-center align-items-center" style="min-height: 55px; background: #fff; border-top: 1px solid #f1f5f9; flex-shrink: 0;">
                    <?php if(count($menu_chunks) > 1): ?>
                        <ol class="carousel-indicators mobile-menu-dots">
                            <?php foreach($menu_chunks as $index => $chunk): ?>
                                <li data-target="#mobileMenuCarousel" data-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>"></li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</div>


<!-- 2. SUBMENU MODAL -->
<div class="modal fade" id="submenuModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1045;">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document" style="padding-bottom: 70px;">
    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: #fff !important;">
      <div class="modal-header border-0 pb-0 justify-content-center" style="background: #fff !important; border-radius: 20px 20px 0 0;">
        <h6 class="modal-title font-weight-bold text-dark" id="submenuTitle" style="font-size: 1rem;">Menu</h6>
      </div>
      <div class="modal-body pt-3 pb-3" id="submenuList"></div>
    </div>
  </div>
</div>

<!-- 3. JADWAL HARI INI MODAL -->
<div class="modal fade" id="modal-jadwal-mengajar" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1045;">
    <div class="modal-dialog modal-lg" role="document" style="padding-bottom: 70px;">
        <div class="modal-content">
            <div class="modal-header border-0 justify-content-center pt-3" style="background: #fff !important;">
                <h5 class="modal-title font-weight-bold" style="color: #1e293b;"><i class="fas fa-calendar-alt mr-2 text-primary"></i> Jadwal Hari Ini</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th class="text-center" style="width: 120px;">Jam</th>
                                <th>Jadwal Terintegrasi</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="jadwal-mengajar-content-global"></tbody>
                    </table>
                </div>
                <div id="modal-status-msg-global" class="text-center p-4" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-pink { background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); }
    .icon-hover:active .icon-circle { transform: scale(0.95); transition: 0.2s; }
</style>

<script>
// SHARED MOBILE LOGIC
function openSubmenuFromData(el) {
    let title = $(el).data('submenu-name');
    let children = $(el).data('submenu-json');
    renderSubmenuModal(title, children);
}

const iconColorsGlobal = {
    'Absensi': 'bg-success', 'Jadwal': 'bg-info', 'Nilai': 'bg-warning', 
    'Keuangan': 'bg-primary', 'Siswa': 'bg-teal', 'Guru': 'bg-purple',
    'Laporan': 'bg-indigo', 'Pengaturan': 'bg-secondary', 'Master': 'bg-danger',
    'PPDB': 'bg-pink', 'Profil': 'bg-maroon', 'Akademik': 'bg-orange',
    'Menu': 'bg-success', 'User': 'bg-primary', 'Peran': 'bg-info',
    'Akses': 'bg-olive', 'Utilitas': 'bg-fuchsia', 'Database': 'bg-fuchsia',
    'Perangkat': 'bg-indigo', 'Kurikulum': 'bg-warning', 'Mutasi': 'bg-orange',
    'Default': 'bg-primary'
};

function getGridColorJS(name) {
    for (let key in iconColorsGlobal) {
        if (name.toLowerCase().includes(key.toLowerCase())) return iconColorsGlobal[key];
    }
    return iconColorsGlobal['Default'];
}

const iconOverridesJS = {
    'Absensi': 'fas fa-user-check',
    'Siswa': 'fas fa-user-graduate',
    'Guru': 'fas fa-chalkboard-teacher',
    'Jadwal': 'fas fa-calendar-alt',
    'Nilai': 'fas fa-file-invoice',
    'Keuangan': 'fas fa-wallet',
    'Bayar': 'fas fa-receipt',
    'Laporan': 'fas fa-chart-pie',
    'Pengaturan': 'fas fa-sliders-h',
    'Master': 'fas fa-database',
    'PPDB': 'fas fa-user-plus',
    'Jurnal': 'fas fa-book-reader',
    'Kurikulum': 'fas fa-graduation-cap',
    'Profil': 'fas fa-user-circle',
    'Tabungan': 'fas fa-piggy-bank',
    'Mutasi': 'fas fa-exchange-alt',
    'Lulus': 'fas fa-user-shield',
    'Alumni': 'fas fa-users-cog',
    'Kelas': 'fas fa-school',
    'Pelajaran': 'fas fa-book-open',
    'Tahun': 'fas fa-calendar-check',
    'Kegiatan': 'fas fa-walking',
    'Jam': 'fas fa-clock',
    'Dashboard': 'fas fa-tachometer-alt',
    'Pemasukan': 'fas fa-file-import',
    'Pengeluaran': 'fas fa-file-export',
    'Rekening': 'fas fa-university',
    'Tagihan': 'fas fa-file-invoice-dollar',
    'Group': 'fas fa-layer-group',
    'Kategori': 'fas fa-tags',
    'Pendaftaran': 'fas fa-edit',
    'Verifikasi': 'fas fa-user-check',
    'Seleksi': 'fas fa-filter',
    'Statistik': 'fas fa-chart-bar',
    'Promosi': 'fas fa-user-tie',
    'Administrasi': 'fas fa-folder-open',
    'Program': 'fas fa-tasks',
    'GTK': 'fas fa-chalkboard-teacher',
    'Mutasi Masuk': 'fas fa-right-to-bracket',
};

function getGridIconJS(name, defaultIcon) {
    for (let key in iconOverridesJS) {
        if (name.toLowerCase().includes(key.toLowerCase())) return iconOverridesJS[key];
    }
    return defaultIcon;
}

function renderSubmenuModal(title, children) {
    $('#submenuTitle').text(title);
    let html = '<div class="row text-center m-0">';
    children.forEach(item => {
        let bgColor = getGridColorJS(item.nama_menu);
        let icon = getGridIconJS(item.nama_menu, item.icon);
        // If child, we might want to inherit parent color or stay colorful. User wants "warna-warni".
        html += `
            <div class="col-4 mb-4 px-1">
                <a href="${item.link}" class="text-decoration-none text-dark d-block">
                    <div class="icon-circle mb-2 mx-auto shadow-sm text-white d-flex align-items-center justify-content-center ${bgColor}" 
                         style="width: 45px; height: 45px; border-radius: 12px; font-size: 1rem;">
                        <i class="${icon}"></i>
                    </div>
                    <span style="font-size: 0.65rem; font-weight: 600; display: block; line-height: 1.2; height: 2.4em; overflow: hidden;">${item.nama_menu}</span>
                </a>
            </div>
        `;
    });
    html += '</div>';
    $('#submenuList').html(html);
    
    // HIDE main grid modal first to avoid backdrop conflict
    $('#mobileGridModal').modal('hide');
    
    // SHOW submenu modal with a small delay for smooth transition
    setTimeout(() => {
        $('#submenuModal').modal('show');
    }, 300);
}

// JADWAL HARI INI LOGIC (GLOBAL)
let globalScheduleData = [];
function fetchGlobalSchedule(callback) {
    const apiUrl = '<?= $api_url ?? "" ?>?mod=jadwal&act=get_daily';
    if(!apiUrl) return;
    
    $.ajax({
        url: apiUrl,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response && response.status === 'success' && response.data) {
                globalScheduleData = response.data;
                if (callback) callback(true, response.data);
            } else {
                if (callback) callback(false, response?.msg || 'Error data');
            }
        },
        error: function (xhr, status, error) {
            if (callback) callback(false, error);
        }
    });
}

$('#modal-jadwal-mengajar').on('show.bs.modal', function () {
    const tbody = $('#jadwal-mengajar-content-global');
    const msgBox = $('#modal-status-msg-global');
    tbody.empty();
    msgBox.html('<i class="fas fa-spinner fa-spin"></i> Memuat...').show();

    fetchGlobalSchedule(function (success, data) {
        msgBox.hide();
        if (success) {
            renderGlobalModalTable(data);
        } else {
            msgBox.html('<div class="alert alert-danger">Gagal memuat jadwal.</div>').show();
        }
    });
});

function renderGlobalModalTable(data) {
    const tbody = $('#jadwal-mengajar-content-global');
    const now = new Date();
    const currentHi = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    tbody.empty();

    let grouped = {};
    data.forEach(item => {
        let key = `${item.jam_mulai.substring(0,5)} - ${item.jam_selesai.substring(0,5)}`;
        if(!grouped[key]) grouped[key] = { start: item.jam_mulai.substring(0,5), end: item.jam_selesai.substring(0,5), items: [] };
        grouped[key].items.push(item);
    });

    let no = 1;
    $.each(grouped, function(slot, g) {
        let isActive = (currentHi >= g.start && currentHi < g.end);
        let badge = isActive ? '<span class="badge badge-success">Berlangsung</span>' : (currentHi >= g.end ? '<span class="badge badge-secondary">Selesai</span>' : '<span class="badge badge-light">Belum</span>');
        
        let details = g.items.map(item => `
            <div class="mb-1 p-1 border-left" style="border-left: 3px solid #007bff !important; background: #f8f9fa;">
                <small class="badge badge-info">${item.kelas}</small> <strong>${item.mapel}</strong>
            </div>
        `).join('');

        tbody.append(`
            <tr class="${isActive ? 'table-success' : ''}">
                <td class="text-center">${no++}</td>
                <td class="text-center"><strong>${slot}</strong></td>
                <td>${details}</td>
                <td class="text-center">${badge}</td>
            </tr>
        `);
    });
}

// Carousel Global Logic (Swipe & Sync)
$(document).ready(function() {
    const $carousel = $('#mobileMenuCarousel');
    const $dots = $('.mobile-menu-dots li');
    const totalSlides = $dots.length;
    
    // Initialize Carousel
    $carousel.carousel({
        interval: false,
        wrap: true,
        touch: false 
    });

    // --- ACCURATE DOTS SYNC (Fires DURING transition) ---
    $carousel.on('slide.bs.carousel', function (e) {
        $dots.removeClass('active');
        $dots.eq(e.to).addClass('active');
    });

    // --- BULLETPROOF SWIPE HANDLER ---
    let xDown = null;                                                        
    let yDown = null;

    function handleTouchStart(evt) {
        xDown = evt.touches[0].clientX;                                      
        yDown = evt.touches[0].clientY;                                      
    }                                                

    function handleTouchEnd(evt) {
        if (!xDown || !yDown) return;

        let xUp = evt.changedTouches[0].clientX;                                    
        let yUp = evt.changedTouches[0].clientY;

        let xDiff = xDown - xUp;
        let yDiff = yDown - yUp;

        // Check if swipe is horizontal and exceeds threshold
        if (Math.abs(xDiff) > Math.abs(yDiff) && Math.abs(xDiff) > 40) {
            const activeIndex = $carousel.find('.carousel-item.active').index();
            
            if (xDiff > 0) { // Swipe Left -> Next
                if (activeIndex === totalSlides - 1) {
                    $carousel.carousel(0); // Manual wrap to first slide
                } else {
                    $carousel.carousel('next');
                }
            } else { // Swipe Right -> Prev
                if (activeIndex === 0) {
                    $carousel.carousel(totalSlides - 1); // Manual wrap to last slide
                } else {
                    $carousel.carousel('prev');
                }
            }
        }
        
        // Reset values
        xDown = null;
        yDown = null;
    }

    const carouselEl = document.getElementById('mobileMenuCarousel');
    if (carouselEl) {
        carouselEl.addEventListener('touchstart', handleTouchStart, {passive: true});        
        // Use touchend for completion of gesture
        carouselEl.addEventListener('touchend', handleTouchEnd, {passive: true});
    }
});
</script>
