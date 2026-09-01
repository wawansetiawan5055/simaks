<?php
// app/controllers/LandingAdminController.php

/**
 * Landing Admin Controller
 * Mengelola pengaturan, berita, dan galeri landing page.
 */

// --- UTILITIES ---

/**
 * Handle file upload helper
 */
function upload_landing_file($field_name, $target_dir = 'uploads/landing/')
{
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] != UPLOAD_ERR_OK) {
        return null;
    }

    $upload_path = __DIR__ . '/../../public/' . $target_dir;
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    $file = $_FILES[$field_name];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed))
        return null;

    $filename = uniqid() . '.' . $ext;
    $destination = $upload_path . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $target_dir . $filename;
    }
    return null;
}

// --- SETTINGS ---

function landing_admin_settings($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    // Get existing settings
    $stmt = $pdo->query("SELECT * FROM app_settings");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    // Get quotes for listing
    $quotes = [];
    try {
        $stmt_q = $pdo->query("SELECT * FROM landing_quotes ORDER BY quote_position ASC, id DESC");
        $quotes = $stmt_q->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Table doesn't exist yet
        $quotes = [];
    }

    // Get dynamic links for listing
    $links = [];
    try {
        $stmt_l = $pdo->query("SELECT * FROM landing_links ORDER BY display_order ASC");
        $links = $stmt_l->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Table doesn't exist yet
        $links = [];
    }

    require '../app/views/landing_admin/settings.php';
}

function landing_admin_settings_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=settings');
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('index.php?mod=landing_admin&act=settings');
    }

    $keys = [
        'landing_page_enabled',
        'school_name',
        'school_address',
        'school_phone',
        'school_email',
        'school_website',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'whatsapp_sekolah',
        'landing_slider_interval',
        'school_motto',
        'school_accreditation',
        'school_description',
        'school_vision',
        'school_mission',
        'school_goals',
        'tahun_berdiri',
        'tahun_perubahan',
        'headmaster_name',
        'headmaster_message',
        'landing_quote_text',
        'landing_quote_source',
        'landing_school_profile_excerpt'
    ];

    $file_keys = ['school_logo', 'headmaster_photo', 'landing_school_profile_image'];
    foreach ($file_keys as $file_key) {
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == UPLOAD_ERR_OK) {
            $upload_path = 'uploads/settings/';
            $public_upload_dir = __DIR__ . '/../../public/' . $upload_path;
            if (!is_dir($public_upload_dir)) {
                mkdir($public_upload_dir, 0777, true);
            }
            $filename = uniqid() . '_' . basename($_FILES[$file_key]['name']);
            $target_file = $public_upload_dir . $filename;
            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file)) {
                $_POST[$file_key] = $upload_path . $filename;
                $keys[] = $file_key;
            }
        }
    }

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $pdo->prepare($sql);

        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                $value = $_POST[$key];
                $stmt->execute([$key, $value]);
            }
        }

        $pdo->commit();
        $_SESSION['pesan_sukses'] = "Pengaturan berhasil disimpan.";

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['pesan_error'] = "Gagal menyimpan pengaturan: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=settings');
}

// --- NEWS ---

function landing_admin_news_index($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_informasi ORDER BY tanggal_publikasi DESC, created_at DESC");
    $news_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    require '../app/views/landing_admin/news_index.php';
}

function landing_admin_news_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=news');
    }

    $id = $_GET['id'] ?? null;
    $news = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_informasi WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    require '../app/views/landing_admin/news_form.php';
}

function landing_admin_news_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=news');
        return;
    }

    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $content = $_POST['content'];
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $publish_date = $_POST['publish_date'] ?? date('Y-m-d');

    // Handle image
    $featured_image = upload_landing_file('featured_image', 'uploads/news/');

    try {
        if ($id) {
            // Update
            $sql = "UPDATE landing_informasi SET 
                    judul=?, konten=?, is_active=?, tanggal_publikasi=?
                    WHERE id=?";
            $params = [$title, $content, $is_published, $publish_date, $id];

            if ($featured_image) {
                // If new image uploaded, update it too
                $sql = str_replace("WHERE id=?", ", gambar=? WHERE id=?", $sql);
                // Insert new image param before ID
                array_splice($params, count($params) - 1, 0, $featured_image);
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

        } else {
            // Insert
            $sql = "INSERT INTO landing_informasi 
                    (judul, konten, is_active, tanggal_publikasi, gambar, author_id)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $content,
                $is_published,
                $publish_date,
                $featured_image,
                $_SESSION['id_user'] ?? 1
            ]);
        }

        $_SESSION['pesan_sukses'] = "Berita berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=news');
}

function landing_admin_news_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=news');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_informasi WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Berita dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=news');
}

// --- GALLERY ---

function landing_admin_gallery_index($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_gallery ORDER BY created_at DESC");
    $gallery_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    require '../app/views/landing_admin/gallery_index.php';
}

function landing_admin_gallery_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=gallery');
    }

    $id = $_GET['id'] ?? null;
    $gallery = null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_gallery WHERE id = ?");
        $stmt->execute([$id]);
        $gallery = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    require '../app/views/landing_admin/gallery_form.php';
}

function landing_admin_gallery_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=gallery');
        return;
    }

    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'];
    $is_slider = isset($_POST['is_slider']) ? 1 : 0;

    $image_path = upload_landing_file('image_path', 'uploads/gallery/');

    try {
        if ($id) {
            // Update
            $sql = "UPDATE landing_gallery SET 
                    title=?, description=?, category=?, is_slider=?
                    WHERE id=?";
            $params = [$title, $description, $category, $is_slider, $id];

            if ($image_path) {
                $sql = str_replace("WHERE id=?", ", image_path=? WHERE id=?", $sql);
                array_splice($params, count($params) - 1, 0, $image_path);
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            // Insert
            if (!$image_path)
                throw new Exception("Gambar wajib diupload.");

            $sql = "INSERT INTO landing_gallery (title, description, category, is_slider, image_path) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $category, $is_slider, $image_path]);
        }
        $_SESSION['pesan_sukses'] = "Galeri disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=gallery');
}

function landing_admin_gallery_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=gallery');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        // Ideally delete file too
        $stmt = $pdo->prepare("DELETE FROM landing_gallery WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Item galeri dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=gallery');
}

// =====================================================
// NEW FUNCTIONS FOR PHASE 2 - MANAGEMENT FEATURES
// =====================================================

// ========== SAMBUTAN KEPALA SEKOLAH ==========

function landing_admin_sambutan($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_headmaster_greeting ORDER BY id DESC");
    $sambutan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require '../app/views/landing_admin/sambutan_index.php';
}

function landing_admin_sambutan_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=sambutan');
    }

    $id = $_GET['id'] ?? null;
    $sambutan = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_headmaster_greeting WHERE id = ?");
        $stmt->execute([$id]);
        $sambutan = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    require '../app/views/landing_admin/sambutan_form.php';
}

function landing_admin_sambutan_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'update') && !can_do($pdo, 'landing_admin', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=sambutan');
        return;
    }

    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $position = $_POST['position'] ?? '';
    $greeting_text = $_POST['greeting_text'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Handle photo upload
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $photo = upload_landing_file('photo', 'uploads/sambutan/');
    }

    try {
        if ($id) {
            // Update existing
            $stmt = $pdo->prepare("UPDATE landing_headmaster_greeting
                                   SET name = ?, position = ?, greeting_text = ?, excerpt = ?,
                                       email = ?, phone = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$name, $position, $greeting_text, $excerpt, $email, $phone, $is_active, $id]);

            if ($photo) {
                $pdo->prepare("UPDATE landing_headmaster_greeting SET photo = ? WHERE id = ?")
                    ->execute([$photo, $id]);
            }

            $_SESSION['pesan_sukses'] = "Sambutan berhasil diperbarui.";
        } else {
            // Create new
            $stmt = $pdo->prepare("INSERT INTO landing_headmaster_greeting
                                   (name, position, greeting_text, excerpt, email, phone, photo, is_active)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $position, $greeting_text, $excerpt, $email, $phone, $photo, $is_active]);
            $_SESSION['pesan_sukses'] = "Sambutan berhasil ditambahkan.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=sambutan');
}

function landing_admin_sambutan_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=sambutan');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_headmaster_greeting WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Sambutan dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=sambutan');
}

// ========== PROGRAM UNGGULAN ==========

function landing_admin_program($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_programs ORDER BY order_display ASC");
    $program_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require '../app/views/landing_admin/program_index.php';
}

function landing_admin_program_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=program');
    }

    $id = $_GET['id'] ?? null;
    $program = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_programs WHERE id = ?");
        $stmt->execute([$id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil data untuk sinkronisasi (Tahfidz, Ekskul, Wirausaha)
    $sync_data = [
        'tahfidz' => $pdo->query("SELECT id_tahfidz as id, nama_kegiatan FROM tahfidz WHERE status='Aktif' ORDER BY nama_kegiatan")->fetchAll(PDO::FETCH_ASSOC),
        'ekskul' => $pdo->query("SELECT id_ekskul as id, nama_ekskul as nama_kegiatan FROM ekstrakurikuler WHERE status='Aktif' ORDER BY nama_ekskul")->fetchAll(PDO::FETCH_ASSOC),
        'wirausaha' => $pdo->query("SELECT id_kewirausahaan as id, nama_kegiatan FROM kewirausahaan WHERE status='Aktif' ORDER BY nama_kegiatan")->fetchAll(PDO::FETCH_ASSOC)
    ];

    require '../app/views/landing_admin/program_form.php';
}

function landing_admin_program_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'update') && !can_do($pdo, 'landing_admin', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=program');
        return;
    }

    $id = $_POST['id'] ?? null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $icon = $_POST['icon'] ?? '';
    $order_display = (int) ($_POST['order_display'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Sync Columns
    $ref_module = $_POST['ref_module'] ?? 'custom';
    $ref_id = (!empty($_POST['ref_id']) && $ref_module !== 'custom') ? (int)$_POST['ref_id'] : null;

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = upload_landing_file('image', 'uploads/program/');
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE landing_programs
                                   SET title = ?, description = ?, icon = ?, order_display = ?, is_active = ?,
                                       ref_module = ?, ref_id = ?
                                   WHERE id = ?");
            $stmt->execute([$title, $description, $icon, $order_display, $is_active, $ref_module, $ref_id, $id]);

            if ($image) {
                $pdo->prepare("UPDATE landing_programs SET image = ? WHERE id = ?")
                    ->execute([$image, $id]);
            }

            $_SESSION['pesan_sukses'] = "Program berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO landing_programs
                                   (title, description, image, icon, order_display, is_active, ref_module, ref_id)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $image, $icon, $order_display, $is_active, $ref_module, $ref_id]);
            $_SESSION['pesan_sukses'] = "Program berhasil ditambahkan.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=program');
}

function landing_admin_program_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=program');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_programs WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Program dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=program');
}

// ========== EKSTRAKURIKULER ==========

function landing_admin_ekskul($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_ekstrakurikuler ORDER BY display_order ASC, id DESC");
    $ekskul_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require '../app/views/landing_admin/ekskul_index.php';
}

function landing_admin_ekskul_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=ekskul');
    }

    $id = $_GET['id'] ?? null;
    $ekskul = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_ekstrakurikuler WHERE id = ?");
        $stmt->execute([$id]);
        $ekskul = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil data untuk sinkronisasi Ekskul
    $sync_data = [];
    try {
        $sync_data['ekskul'] = $pdo->query("SELECT id_ekskul as id, nama_ekskul as nama_kegiatan FROM ekstrakurikuler WHERE status='Aktif' ORDER BY nama_ekskul")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}

    require '../app/views/landing_admin/ekskul_form.php';
}

function landing_admin_ekskul_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'update') && !can_do($pdo, 'landing_admin', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=ekskul');
        return;
    }

    $id = $_POST['id'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $pembina = $_POST['pembina'] ?? '';
    $jadwal = $_POST['jadwal'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $icon = $_POST['icon'] ?? '';
    $order_display = (int) ($_POST['order_display'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Sync Columns
    $ref_id = !empty($_POST['ref_id']) ? (int)$_POST['ref_id'] : null;

    // Handle image upload
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $gambar = upload_landing_file('gambar', 'uploads/ekskul/');
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE landing_ekstrakurikuler
                                   SET nama = ?, pembina = ?, jadwal = ?, deskripsi = ?, lokasi = ?, icon = ?, display_order = ?, is_active = ?, ref_id = ?
                                   WHERE id = ?");
            $stmt->execute([$nama, $pembina, $jadwal, $deskripsi, $lokasi, $icon, $order_display, $is_active, $ref_id, $id]);

            if ($gambar) {
                $pdo->prepare("UPDATE landing_ekstrakurikuler SET gambar = ? WHERE id = ?")
                    ->execute([$gambar, $id]);
            }

            $_SESSION['pesan_sukses'] = "Ekstrakurikuler berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO landing_ekstrakurikuler
                                   (nama, pembina, jadwal, deskripsi, lokasi, icon, gambar, display_order, is_active, ref_id)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $pembina, $jadwal, $deskripsi, $lokasi, $icon, $gambar, $order_display, $is_active, $ref_id]);
            $_SESSION['pesan_sukses'] = "Ekstrakurikuler berhasil ditambahkan.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=ekskul');
}

function landing_admin_ekskul_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=ekskul');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_ekstrakurikuler WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Ekstrakurikuler dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=ekskul');
}

// ========== VIDEO ==========

function landing_admin_video($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_video ORDER BY display_order ASC, id DESC");
    $video_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require '../app/views/landing_admin/video_index.php';
}

function landing_admin_video_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=video');
    }

    $id = $_GET['id'] ?? null;
    $video = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_video WHERE id = ?");
        $stmt->execute([$id]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    require '../app/views/landing_admin/video_form.php';
}

function landing_admin_video_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'update') && !can_do($pdo, 'landing_admin', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=video');
        return;
    }

    $id = $_POST['id'] ?? null;
    $judul = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $video_url = $_POST['video_url'] ?? '';
    $tipe = $_POST['tipe'] ?? 'youtube';
    $kategori = $_POST['kategori'] ?? '';
    $durasi = $_POST['durasi'] ?? '';
    $display_order = (int) ($_POST['display_order'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Handle image upload
    $thumbnail = null;
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK) {
        $thumbnail = upload_landing_file('thumbnail', 'uploads/video/');
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE landing_video
                                   SET judul = ?, deskripsi = ?, video_url = ?, tipe = ?, kategori = ?, durasi = ?, display_order = ?, is_active = ?, is_featured = ?
                                   WHERE id = ?");
            $stmt->execute([$judul, $deskripsi, $video_url, $tipe, $kategori, $durasi, $display_order, $is_active, $is_featured, $id]);

            if ($thumbnail) {
                $pdo->prepare("UPDATE landing_video SET thumbnail = ? WHERE id = ?")
                    ->execute([$thumbnail, $id]);
            }

            $_SESSION['pesan_sukses'] = "Video berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO landing_video
                                   (judul, deskripsi, video_url, thumbnail, tipe, kategori, durasi, display_order, is_active, is_featured)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$judul, $deskripsi, $video_url, $thumbnail, $tipe, $kategori, $durasi, $display_order, $is_active, $is_featured]);
            $_SESSION['pesan_sukses'] = "Video berhasil ditambahkan.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=video');
}

function landing_admin_video_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=video');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_video WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Video dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=video');
}

// ========== FASILITAS ==========

function landing_admin_facilities($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_facilities ORDER BY order_display ASC");
    $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require '../app/views/landing_admin/facilities_index.php';
}

function landing_admin_facilities_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=facilities');
    }

    $id = $_GET['id'] ?? null;
    $facility = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_facilities WHERE id = ?");
        $stmt->execute([$id]);
        $facility = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    require '../app/views/landing_admin/facilities_form.php';
}

function landing_admin_facilities_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'update') && !can_do($pdo, 'landing_admin', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=facilities');
        return;
    }

    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $order_display = (int) ($_POST['order_display'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = upload_landing_file('image', 'uploads/fasilitas/');
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE landing_facilities
                                   SET name = ?, description = ?, category = ?, order_display = ?, is_active = ?
                                   WHERE id = ?");
            $stmt->execute([$name, $description, $category, $order_display, $is_active, $id]);

            if ($image) {
                $pdo->prepare("UPDATE landing_facilities SET image = ? WHERE id = ?")
                    ->execute([$image, $id]);
            }

            $_SESSION['pesan_sukses'] = "Fasilitas berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO landing_facilities
                                   (name, description, image, category, order_display, is_active)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $image, $category, $order_display, $is_active]);
            $_SESSION['pesan_sukses'] = "Fasilitas berhasil ditambahkan.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=facilities');
}

function landing_admin_facilities_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=facilities');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_facilities WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Fasilitas dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=facilities');
}

// ========== TESTIMONIALS ==========

function landing_admin_testimonials($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_testimonials ORDER BY order_display ASC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require '../app/views/landing_admin/testimonials_index.php';
}

function landing_admin_testimonials_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=testimonials');
    }

    $id = $_GET['id'] ?? null;
    $testimonial = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_testimonials WHERE id = ?");
        $stmt->execute([$id]);
        $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    require '../app/views/landing_admin/testimonials_form.php';
}

function landing_admin_testimonials_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'update') && !can_do($pdo, 'landing_admin', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=testimonials');
        return;
    }

    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $position = $_POST['position'] ?? '';
    $message = $_POST['message'] ?? '';
    $rating = (int) ($_POST['rating'] ?? 5);
    $order_display = (int) ($_POST['order_display'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Handle photo upload
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $photo = upload_landing_file('photo', 'uploads/testimonials/');
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE landing_testimonials
                                   SET name = ?, position = ?, message = ?, rating = ?,
                                       order_display = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$name, $position, $message, $rating, $order_display, $is_active, $id]);

            if ($photo) {
                $pdo->prepare("UPDATE landing_testimonials SET photo = ? WHERE id = ?")
                    ->execute([$photo, $id]);
            }

            $_SESSION['pesan_sukses'] = "Testimonial berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO landing_testimonials
                                   (name, position, photo, message, rating, order_display, is_active)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $position, $photo, $message, $rating, $order_display, $is_active]);
            $_SESSION['pesan_sukses'] = "Testimonial berhasil ditambahkan.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=testimonials');
}

function landing_admin_testimonials_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=testimonials');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_testimonials WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Testimonial dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=testimonials');
}

// ========== FAQS ==========

function landing_admin_faqs($pdo)
{
    if (!check_access('landing_admin', 'index'))
        redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_faqs ORDER BY order_display ASC");
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require '../app/views/landing_admin/faqs_index.php';
}

function landing_admin_faqs_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=faqs');
    }

    $id = $_GET['id'] ?? null;
    $faq = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_faqs WHERE id = ?");
        $stmt->execute([$id]);
        $faq = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    require '../app/views/landing_admin/faqs_form.php';
}

function landing_admin_faqs_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'update') && !can_do($pdo, 'landing_admin', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=faqs');
        return;
    }

    $id = $_POST['id'] ?? null;
    $question = $_POST['question'] ?? '';
    $answer = $_POST['answer'] ?? '';
    $category = $_POST['category'] ?? '';
    $order_display = (int) ($_POST['order_display'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE landing_faqs
                                   SET question = ?, answer = ?, category = ?, order_display = ?, is_active = ?
                                   WHERE id = ?");
            $stmt->execute([$question, $answer, $category, $order_display, $is_active, $id]);
            $_SESSION['pesan_sukses'] = "FAQ berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO landing_faqs
                                   (question, answer, category, order_display, is_active)
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$question, $answer, $category, $order_display, $is_active]);
            $_SESSION['pesan_sukses'] = "FAQ berhasil ditambahkan.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=faqs');
}

function landing_admin_faqs_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=faqs');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_faqs WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "FAQ dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=faqs');
}

// =========================================================
// QUOTES MANAGEMENT
// =========================================================

function landing_admin_quotes_index($pdo) {
    if (!can_do($pdo, 'landing_admin', 'read')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=dashboard');
        return;
    }
    
    // Ensure table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS landing_quotes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        quote_text TEXT NOT NULL,
        quote_source VARCHAR(100),
        quote_position VARCHAR(50) DEFAULT 'sidebar',
        is_active TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $quotes = $pdo->query("SELECT * FROM landing_quotes ORDER BY created_at DESC")->fetchAll();
    require '../app/views/landing_admin/quotes_index.php';
}

function landing_admin_quote_form($pdo) {
    if (!can_do($pdo, 'landing_admin', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=settings');
        return;
    }
    
    $id = $_GET['id'] ?? null;
    $quote = null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_quotes WHERE id = ?");
        $stmt->execute([$id]);
        $quote = $stmt->fetch();
    }
    require '../app/views/landing_admin/quote_form.php';
}

function landing_admin_quote_save($pdo) {
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=settings');
        return;
    }
    
    $id = $_POST['id'] ?? null;
    $text = $_POST['text'] ?? $_POST['quote_text'] ?? '';
    $source = $_POST['source'] ?? $_POST['quote_source'] ?? '';
    $position = $_POST['position'] ?? $_POST['quote_position'] ?? 'sidebar';
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE landing_quotes SET quote_text = ?, quote_source = ?, quote_position = ? WHERE id = ?");
        $stmt->execute([$text, $source, $position, $id]);
        $_SESSION['pesan_sukses'] = "Quote diperbarui.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO landing_quotes (quote_text, quote_source, quote_position) VALUES (?, ?, ?)");
        $stmt->execute([$text, $source, $position]);
        $_SESSION['pesan_sukses'] = "Quote ditambahkan.";
    }
    redirect('index.php?mod=landing_admin&act=settings');
}

function landing_admin_quote_delete($pdo) {
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=settings');
        return;
    }
    
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM landing_quotes WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Quote dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=settings');
}

// --- TAUTAN PENTING ---

function landing_admin_links_index($pdo)
{
    if (!check_access('landing_admin', 'index')) redirect('index.php');
    
    $stmt = $pdo->query("SELECT * FROM landing_links ORDER BY display_order ASC");
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $title = "Manajemen Tautan Penting";
    $content = '../app/views/landing_admin/links.php';
    require '../app/views/admin/layout.php';
}

function landing_admin_links_form($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        redirect('index.php?mod=landing_admin&act=settings');
    }

    $id = $_GET['id'] ?? null;
    $link = null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM landing_links WHERE id = ?");
        $stmt->execute([$id]);
        $link = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $title = ($id ? "Edit" : "Tambah") . " Tautan Penting";
    $content = '../app/views/landing_admin/links_form.php';
    require '../app/views/admin/layout.php';
}

function landing_admin_links_save($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'create') && !can_do($pdo, 'landing_admin', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=settings');
        return;
    }

    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $url = $_POST['url'];
    $icon = $_POST['icon'];
    $display_order = $_POST['display_order'] ?? 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE landing_links SET title=?, url=?, icon=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$title, $url, $icon, $display_order, $is_active, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO landing_links (title, url, icon, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $url, $icon, $display_order, $is_active]);
        }
        $_SESSION['pesan_sukses'] = "Tautan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect('index.php?mod=landing_admin&act=settings');
}

function landing_admin_links_delete($pdo)
{
    if (!can_do($pdo, 'landing_admin', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=settings');
        return;
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $pdo->prepare("DELETE FROM landing_links WHERE id = ?")->execute([$id]);
        $_SESSION['pesan_sukses'] = "Tautan dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=settings');
}
