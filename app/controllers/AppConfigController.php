<?php
// app/controllers/AppConfigController.php
require_once __DIR__ . '/../models/AppConfigModel.php';

function app_config_index($pdo) {
    if (!is_admin()) {
        redirect('index.php?mod=dashboard');
    }

    $config = AppConfigModel::getAll($pdo);
    include __DIR__ . '/../views/app_config_index.php';
}

function app_config_save($pdo) {
    if (!is_admin()) {
        redirect('index.php?mod=dashboard');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect all theme keys from POST
        $theme_keys = [
            'theme_accent_color',
            'theme_menu_active_bg',
            'theme_sidebar_bg',
            'theme_body_bg',
            'theme_navbar_bg',
            'theme_footer_bg',
            'theme_table_header_bg',
            'theme_font_header',
            'theme_font_subtitle',
            'theme_font_table_header',
            'theme_font_table_content',
            'theme_font_body',
            'theme_font_small',
            'theme_font_size',
            'theme_color_header',
            'theme_color_subtitle',
            'theme_color_table_header',
            'theme_color_table_content',
            'theme_color_body',
            'theme_color_small',
            'theme_color_sidebar_text',
            'gemini_api_key',
            'login_quote_1',
            'login_quote_2',
            'login_quote_3'
        ];

        $data_to_update = [];
        foreach ($theme_keys as $key) {
            if (isset($_POST[$key])) {
                $data_to_update[$key] = $_POST[$key];
            }
        }

        // Handle Image Uploads for login background sliders (1, 2, 3)
        $upload_dir = __DIR__ . '/../../public/assets/img/';
        $allowed_types = ['jpg', 'png', 'jpeg', 'webp'];
        
        for ($i = 1; $i <= 3; $i++) {
            $input_name = "login_bg_image_$i";
            if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
                $file_name = 'login_bg_' . $i . '_' . time() . '_' . basename(str_replace(' ', '_', $_FILES[$input_name]['name']));
                $target_file = $upload_dir . $file_name;
                
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_file)) {
                        $data_to_update[$input_name] = $file_name;
                        // For backward compatibility, also set login_bg_image if slide 1 is uploaded
                        if ($i === 1) {
                            $data_to_update['login_bg_image'] = $file_name;
                        }
                    } else {
                        $_SESSION['pesan_error'] = "Gagal mengunggah gambar latar login $i.";
                    }
                } else {
                    $_SESSION['pesan_error'] = "Format file gambar $i tidak didukung.";
                }
            }
        }

        if (AppConfigModel::updateBulk($pdo, $data_to_update)) {
            if (!isset($_SESSION['pesan_error'])) {
                $_SESSION['pesan_sukses'] = "Pengaturan tampilan berhasil diperbarui.";
            }
        } else {
            $_SESSION['pesan_error'] = "Gagal memperbarui pengaturan tampilan.";
        }
    }

    redirect('index.php?mod=app_config&act=index');
}

function app_config_reset($pdo) {
    if (!is_admin()) {
        redirect('index.php?mod=dashboard');
    }

    if (AppConfigModel::deleteThemeConfig($pdo)) {
        $_SESSION['pesan_sukses'] = "Pengaturan tampilan berhasil dikembalikan ke standar awal.";
    } else {
        $_SESSION['pesan_error'] = "Gagal mereset pengaturan tampilan.";
    }

    redirect('index.php?mod=app_config&act=index');
}
