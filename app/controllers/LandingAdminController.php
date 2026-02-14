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
    if (!check_access('landing_admin', 'index')) redirect('index.php');

    // Get existing settings
    $stmt = $pdo->query("SELECT * FROM app_settings");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
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
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'social_youtube',
        'landing_slider_interval'
    ];

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $pdo->prepare($sql);

        foreach ($keys as $key) {
            $value = $_POST[$key] ?? '';
            $stmt->execute([$key, $value]);
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
    if (!check_access('landing_admin', 'index')) redirect('index.php');

    $stmt = $pdo->query("SELECT * FROM landing_news ORDER BY created_at DESC");
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
        $stmt = $pdo->prepare("SELECT * FROM landing_news WHERE id = ?");
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
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $content = $_POST['content'];
    $excerpt = $_POST['excerpt'] ?? substr($content, 0, 150);
    $type = $_POST['type'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $publish_date = $_POST['publish_date'] ?? date('Y-m-d');

    // Handle image
    $featured_image = upload_landing_file('featured_image', 'uploads/news/');

    try {
        if ($id) {
            // Update
            $sql = "UPDATE landing_news SET 
                    title=?, slug=?, content=?, excerpt=?, type=?, 
                    is_featured=?, is_published=?, publish_date=?
                    WHERE id=?";
            $params = [$title, $slug, $content, $excerpt, $type, $is_featured, $is_published, $publish_date, $id];

            if ($featured_image) {
                // If new image uploaded, update it too
                $sql = str_replace("WHERE id=?", ", featured_image=? WHERE id=?", $sql);
                // Insert new image param before ID
                array_splice($params, count($params) - 1, 0, $featured_image);
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

        } else {
            // Insert
            $sql = "INSERT INTO landing_news 
                    (title, slug, content, excerpt, type, is_featured, is_published, publish_date, featured_image, author_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $slug,
                $content,
                $excerpt,
                $type,
                $is_featured,
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
        $stmt = $pdo->prepare("DELETE FROM landing_news WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['pesan_sukses'] = "Berita dihapus.";
    }
    redirect('index.php?mod=landing_admin&act=news');
}

// --- GALLERY ---

function landing_admin_gallery_index($pdo)
{
    if (!check_access('landing_admin', 'index')) redirect('index.php');

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
