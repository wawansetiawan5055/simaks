# 📱 PANDUAN UPGRADE WEBSITE SIMAKS - MENJADI WEBSITE PROFESIONAL

**Version**: 2.0.0  
**Last Updated**: 10 Maret 2026  
**Status**: Planning & Implementation Guide

---

## 📋 TABLE OF CONTENTS

1. [Overview](#overview)
2. [Fitur-Fitur Baru](#fitur-fitur-baru)
3. [Database Changes](#database-changes)
4. [File Structure](#file-structure)
5. [Step-by-Step Implementation](#step-by-step-implementation)
6. [Admin Panel Features](#admin-panel-features)
7. [Frontend Features](#frontend-features)
8. [Testing Checklist](#testing-checklist)
9. [Troubleshooting](#troubleshooting)
10. [FAQ](#faq)

---

## 🎯 OVERVIEW

Upgrade landing page SIMAKS dari website dasar menjadi **website sekolah profesional** yang menampilkan seluruh informasi entitas sekolah dengan UX/UI modern, konten yang lengkap, dan fitur management yang komprehensif.

### Target Audience
- 👨‍👩‍👧 Calon siswa & orang tua
- 👨‍🎓 Siswa aktif
- 🏫 Publik/masyarakat umum

### Key Improvements
✅ **Visual & Design** - Modern, responsive, professional  
✅ **Content Structure** - Informasi lengkap tentang sekolah  
✅ **Blog/News System** - Artikel panjang, detail per berita  
✅ **Headmaster Greeting** - Sambutan Kepala Sekolah  
✅ **Management UI** - Admin panel untuk manage konten  
✅ **SEO Optimization** - URL-friendly, meta tags, performance  
✅ **Security** - CSRF protection, input validation, file upload security  

---

## ✨ FITUR-FITUR BARU

### 1. **Halaman Berita Detail (Blog Post Style)**

Setiap berita akan memiliki halaman detail yang menampilkan konten lengkap (bukan hanya excerpt).

#### Features:
- 📄 **Full Article Content**: Tampilkan konten artikel secara lengkap dengan formatting
- 🔗 **SEO-Friendly URL**: `/index.php?mod=landing&act=berita_detail&slug=judul-berita-seo`
- 👤 **Author Info**: Nama penulis + tanggal publikasi + waktu baca
- 🔍 **Breadcrumb**: Home > Berita > Judul Berita
- 🏷️ **Tags/Kategori**: Untuk filtering dan SEO
- 📊 **View Counter**: Menampilkan berapa kali artikel dibaca
- 🔗 **Related Articles**: 3-4 berita terkait di bawah
- 📤 **Share Buttons**: Share ke Facebook, WhatsApp, Twitter, LinkedIn
- ▶️ **Navigation**: Tombol Next/Previous artikel
- 👤 **Author Bio Card**: Foto + nama + deskripsi singkat guru

#### URL Structure:
```
/index.php?mod=landing&act=berita_detail&slug=judul-berita-panjang
/index.php?mod=landing&act=berita_list              (List semua berita)
/index.php?mod=landing&act=berita_by_category=akademik  (Filter by category)
/index.php?mod=landing&act=berita_search?q=keyword  (Search berita)
```

### 2. **Sambutan Kepala Sekolah**

Halaman khusus untuk sambutan/pesan dari Kepala Sekolah.

#### Features:
- 👨‍💼 **Headmaster Profile**: Foto, nama, jabatan
- 📝 **Greeting Message**: Konten sambutan panjang dan mendalam
- 🎨 **Professional Design**: Card-based atau full-width section
- 🔗 **Detail Page**: Halaman terpisah untuk sambutan lengkap
- ✉️ **Contact Info**: Email & telepon Kepala Sekolah
- 📍 **Landing Preview**: Excerpt + link "Baca Selengkapnya" di halaman utama
- ✍️ **Signature**: Tanda tangan digital (opsional)

#### URL Structure:
```
/index.php?mod=landing&act=sambutan_kepala  (Halaman detail sambutan)
```

### 3. **Profil Sekolah Lengkap**

Setiap aspek sekolah akan ditampilkan di halaman terpisah atau section.

#### Features:
- 🎓 **Visi & Misi**: Already implemented, improve styling
- 🏢 **Profil Sekolah**: Nama, alamat, kontak, website
- 📊 **Statistik**: Jumlah siswa, guru, fasilitas (auto-sync dari DB)
- 👨‍🏫 **Data Guru/GTK**: Daftar guru dengan foto & mata pelajaran
- 🎯 **Program Unggulan**: Program-program unggulan sekolah
- 🎪 **Kegiatan Sekolah**: Ekskul, kegiatan tahunan, dll
- 🏛️ **Fasilitas**: Lab, perpustakaan, aula, dll dengan foto
- 🏆 **Prestasi**: Pencapaian siswa & sekolah
- 💬 **Testimonial**: Dari siswa/alumni/orang tua

### 4. **Improved Landing Page**

Struktur landing page yang lebih lengkap dan profesional.

#### Sections:
```
1. Navbar (fixed top)
   ├── Logo & School Name
   ├── Menu Navigation
   └── Login Button

2. Hero Slider
   ├── Auto-play carousel dengan gambar
   └── CTA buttons (PPDB, Learn More)

3. Sambutan Kepala Sekolah (NEW)
   ├── Foto + Nama
   ├── Excerpt sambutan
   └── Read More button

4. About Section
   ├── Visi & Misi
   └── Statistics (Siswa, Guru, Prestasi, Tahun Berdiri)

5. Features/Highlights
   ├── Program unggulan
   ├── Kegiatan
   └── Fasilitas (cards grid)

6. Guru/GTK Section (NEW)
   ├── Daftar guru dengan foto
   └── Mapel yang diajar

7. Berita & Pengumuman
   ├── Grid/card layout
   └── Link ke detail page

8. Gallery Kegiatan
   ├── Grid layout
   └── Filter by category

9. PPDB Section
   ├── Info periode
   ├── Syarat-syarat
   └── CTA buttons (Daftar, Cek Status)

10. FAQ (NEW)
    ├── Accordian style
    └── Collapse/expand

11. Contact Section (NEW)
    ├── Form contact
    ├── Map
    └── Info kontak

12. Footer
    ├── Quick links
    ├── Contact info
    ├── Social media
    └── Copyright
```

---

## 🗄️ DATABASE CHANGES

### 1. **Modify Table: `landing_news`**

```sql
-- Add new columns
ALTER TABLE landing_news ADD COLUMN IF NOT EXISTS `slug` VARCHAR(255) UNIQUE AFTER `id`;
ALTER TABLE landing_news ADD COLUMN IF NOT EXISTS `author` VARCHAR(255) AFTER `title`;
ALTER TABLE landing_news ADD COLUMN IF NOT EXISTS `excerpt` LONGTEXT AFTER `content`;
ALTER TABLE landing_news ADD COLUMN IF NOT EXISTS `category` VARCHAR(100) AFTER `type`;
ALTER TABLE landing_news ADD COLUMN IF NOT EXISTS `tags` VARCHAR(500) AFTER `category`;
ALTER TABLE landing_news ADD COLUMN IF NOT EXISTS `view_count` INT DEFAULT 0 AFTER `tags`;
ALTER TABLE landing_news ADD COLUMN IF NOT EXISTS `meta_description` VARCHAR(255) AFTER `view_count`;
ALTER TABLE landing_news ADD COLUMN IF NOT EXISTS `seo_keywords` VARCHAR(255) AFTER `meta_description`;

-- Add indexes for performance
CREATE INDEX idx_slug ON landing_news(slug);
CREATE INDEX idx_category ON landing_news(category);
CREATE INDEX idx_publish_date ON landing_news(publish_date);
CREATE INDEX idx_is_published ON landing_news(is_published);
```

### 2. **Create New Table: `landing_headmaster_greeting`**

```sql
CREATE TABLE IF NOT EXISTS `landing_headmaster_greeting` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `position` VARCHAR(255),
  `photo` VARCHAR(500),
  `greeting_text` LONGTEXT,
  `excerpt` TEXT,
  `email` VARCHAR(255),
  `phone` VARCHAR(20),
  `is_active` TINYINT DEFAULT 1,
  `order_display` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. **Create New Table: `landing_programs` (Program Unggulan)**

```sql
CREATE TABLE IF NOT EXISTS `landing_programs` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` LONGTEXT,
  `image` VARCHAR(500),
  `icon` VARCHAR(100),
  `order_display` INT DEFAULT 1,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. **Create New Table: `landing_facilities` (Fasilitas)**

```sql
CREATE TABLE IF NOT EXISTS `landing_facilities` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` LONGTEXT,
  `image` VARCHAR(500),
  `category` VARCHAR(100),
  `order_display` INT DEFAULT 1,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. **Create New Table: `landing_testimonials`**

```sql
CREATE TABLE IF NOT EXISTS `landing_testimonials` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `position` VARCHAR(255),
  `photo` VARCHAR(500),
  `message` TEXT,
  `rating` INT DEFAULT 5,
  `is_active` TINYINT DEFAULT 1,
  `order_display` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 6. **Create New Table: `landing_faqs`**

```sql
CREATE TABLE IF NOT EXISTS `landing_faqs` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `question` VARCHAR(500) NOT NULL,
  `answer` LONGTEXT,
  `category` VARCHAR(100),
  `order_display` INT DEFAULT 1,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 7. **Modify Table: `landing_gallery`** (if exists)

```sql
-- Add category column if not exists
ALTER TABLE landing_gallery ADD COLUMN IF NOT EXISTS `category` VARCHAR(100) AFTER `title`;
ALTER TABLE landing_gallery ADD COLUMN IF NOT EXISTS `description` TEXT AFTER `category`;

-- Add indexes
CREATE INDEX idx_category ON landing_gallery(category);
```

---

## 📁 FILE STRUCTURE

### Files to Create/Modify

```
simaks/
├── app/
│   ├── controllers/
│   │   ├── LandingController.php (MODIFY)
│   │   │   ├── berita_list()
│   │   │   ├── berita_detail()
│   │   │   ├── berita_search()
│   │   │   ├── berita_by_category()
│   │   │   ├── sambutan_kepala()
│   │   │   ├── program_list()
│   │   │   ├── facilities_list()
│   │   │   └── faq_list()
│   │   │
│   │   └── LandingAdminController.php (MODIFY)
│   │       ├── landing_admin_berita_form() (UPDATE)
│   │       ├── landing_admin_sambutan() (NEW)
│   │       ├── landing_admin_program() (NEW)
│   │       ├── landing_admin_facilities() (NEW)
│   │       ├── landing_admin_testimonials() (NEW)
│   │       └── landing_admin_faqs() (NEW)
│   │
│   ├── views/
│   │   ├── landing_page.php (MODIFY - improve struktur)
│   │   ├── berita_list.php (NEW)
│   │   ├── berita_detail.php (NEW)
│   │   ├── berita_search_results.php (NEW)
│   │   ├── sambutan_kepala_detail.php (NEW)
│   │   ├── program_list.php (NEW)
│   │   ├── facilities_list.php (NEW)
│   │   ├── faq_page.php (NEW)
│   │   │
│   │   └── landing_admin/
│   │       ├── settings.php (MODIFY)
│   │       ├── news_form.php (MODIFY - add slug, author, tags)
│   │       ├── sambutan_form.php (NEW)
│   │       ├── program_form.php (NEW)
│   │       ├── facilities_form.php (NEW)
│   │       ├── testimonials_form.php (NEW)
│   │       ├── faqs_form.php (NEW)
│   │       ├── sambutan_index.php (NEW)
│   │       ├── program_index.php (NEW)
│   │       ├── facilities_index.php (NEW)
│   │       ├── testimonials_index.php (NEW)
│   │       └── faqs_index.php (NEW)
│   │
│   └── helpers/
│       └── SlugHelper.php (NEW - untuk generate slug dari title)
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── landing-page.css (MODIFY - improve styling)
│   │   │   ├── berita-detail.css (NEW)
│   │   │   └── responsive.css (NEW)
│   │   │
│   │   ├── js/
│   │   │   ├── landing-page.js (MODIFY)
│   │   │   ├── berita-detail.js (NEW - for sharing, comments)
│   │   │   └── article-search.js (NEW)
│   │   │
│   │   └── img/
│   │       └── (for default images)
│   │
│   └── uploads/
│       ├── berita/ (for article featured images)
│       ├── guru/ (for teacher photos)
│       ├── program/ (for program images)
│       ├── fasilitas/ (for facility images)
│       └── sambutan/ (for headmaster photo)
│
└── docs/
    └── UPGRADE_WEBSITE.md (THIS FILE)
```

---

## 🚀 STEP-BY-STEP IMPLEMENTATION

### **PHASE 1: Database Setup & Helpers**

#### Step 1.1: Run SQL Migrations

```bash
# Connect to database and run the SQL scripts above
# Option 1: Via phpMyAdmin
# Copy-paste semua SQL queries ke SQL tab

# Option 2: Via command line
mysql -u root -p simaks_db < database/landing_page_upgrade.sql

# Or execute individual queries
```

#### Step 1.2: Create SlugHelper

Create file: `app/helpers/SlugHelper.php`

```php
<?php
// app/helpers/SlugHelper.php

class SlugHelper {
    /**
     * Generate URL-friendly slug dari string
     */
    public static function generate($text) {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Replace spaces dengan hyphen
        $text = preg_replace('/\s+/', '-', $text);
        
        // Remove special characters
        $text = preg_replace('/[^a-z0-9\-]/', '', $text);
        
        // Remove multiple hyphens
        $text = preg_replace('/-+/', '-', $text);
        
        // Trim hyphens dari awal & akhir
        return trim($text, '-');
    }
    
    /**
     * Check if slug already exists
     */
    public static function exists($pdo, $slug, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM landing_news WHERE slug = ?";
        $params = [$slug];
        
        if ($exclude_id) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
}
```

---

### **PHASE 2: Update Controllers & Create New Views**

#### Step 2.1: Update LandingController.php

Update `app/controllers/LandingController.php` dengan fungsi-fungsi baru:

```php
/**
 * List semua berita
 */
function berita_list($pdo) {
    $page = $_GET['page'] ?? 1;
    $per_page = 9;
    $offset = ($page - 1) * $per_page;
    
    $stmt = $pdo->query("SELECT * FROM landing_news WHERE is_published = 1 
                         ORDER BY publish_date DESC LIMIT $per_page OFFSET $offset");
    $berita_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get pagination info
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM landing_news WHERE is_published = 1");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total / $per_page);
    
    include '../app/views/berita_list.php';
}

/**
 * Detail berita
 */
function berita_detail($pdo) {
    $slug = $_GET['slug'] ?? null;
    
    if (!$slug) {
        redirect('index.php?mod=landing&act=berita_list');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM landing_news WHERE slug = ? AND is_published = 1");
    $stmt->execute([$slug]);
    $berita = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$berita) {
        redirect('index.php?mod=landing&act=berita_list');
    }
    
    // Increment view count
    $pdo->prepare("UPDATE landing_news SET view_count = view_count + 1 WHERE id = ?")->execute([$berita['id']]);
    
    // Get related articles
    $stmt = $pdo->prepare("SELECT * FROM landing_news WHERE category = ? AND id != ? 
                           AND is_published = 1 ORDER BY publish_date DESC LIMIT 3");
    $stmt->execute([$berita['category'], $berita['id']]);
    $related = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get previous & next article
    $stmt = $pdo->prepare("SELECT id, title, slug FROM landing_news WHERE publish_date < ? 
                           AND is_published = 1 ORDER BY publish_date DESC LIMIT 1");
    $stmt->execute([$berita['publish_date']]);
    $prev_article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT id, title, slug FROM landing_news WHERE publish_date > ? 
                           AND is_published = 1 ORDER BY publish_date ASC LIMIT 1");
    $stmt->execute([$berita['publish_date']]);
    $next_article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    include '../app/views/berita_detail.php';
}

/**
 * Sambutan Kepala Sekolah
 */
function sambutan_kepala($pdo) {
    $stmt = $pdo->query("SELECT * FROM landing_headmaster_greeting WHERE is_active = 1 LIMIT 1");
    $sambutan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$sambutan) {
        redirect('index.php?mod=landing&act=index');
    }
    
    include '../app/views/sambutan_kepala_detail.php';
}

/**
 * Search berita
 */
function berita_search($pdo) {
    $query = $_GET['q'] ?? '';
    $page = $_GET['page'] ?? 1;
    $per_page = 9;
    $offset = ($page - 1) * $per_page;
    
    $search_pattern = '%' . $query . '%';
    
    $stmt = $pdo->prepare("SELECT * FROM landing_news WHERE is_published = 1 
                           AND (title LIKE ? OR content LIKE ? OR tags LIKE ?)
                           ORDER BY publish_date DESC LIMIT $per_page OFFSET $offset");
    $stmt->execute([$search_pattern, $search_pattern, $search_pattern]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    include '../app/views/berita_search_results.php';
}

/**
 * Filter berita by category
 */
function berita_by_category($pdo) {
    $category = $_GET['category'] ?? null;
    $page = $_GET['page'] ?? 1;
    $per_page = 9;
    $offset = ($page - 1) * $per_page;
    
    if (!$category) {
        redirect('index.php?mod=landing&act=berita_list');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM landing_news WHERE is_published = 1 
                           AND category = ? ORDER BY publish_date DESC 
                           LIMIT $per_page OFFSET $offset");
    $stmt->execute([$category]);
    $berita_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    include '../app/views/berita_list.php';
}
```

#### Step 2.2: Update LandingAdminController.php

Add new functions untuk manage sambutan, program, fasilitas, dll.

```php
// ========== SAMBUTAN KEPALA SEKOLAH ==========

function landing_admin_sambutan($pdo) {
    if (!check_access('landing_admin', 'index')) redirect('index.php');
    
    $stmt = $pdo->query("SELECT * FROM landing_headmaster_greeting ORDER BY id DESC");
    $sambutan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    require '../app/views/landing_admin/sambutan_index.php';
}

function landing_admin_sambutan_form($pdo) {
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

function landing_admin_sambutan_save($pdo) {
    if (!can_do($pdo, 'landing_admin', 'update') && !can_do($pdo, 'landing_admin', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=landing_admin&act=sambutan');
    }
    
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $position = $_POST['position'] ?? '';
    $greeting_text = $_POST['greeting_text'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    // Handle photo upload
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $photo = upload_landing_file('photo', 'uploads/sambutan/');
    }
    
    if ($id) {
        // Update existing
        $stmt = $pdo->prepare("UPDATE landing_headmaster_greeting 
                               SET name = ?, position = ?, greeting_text = ?, excerpt = ?, 
                                   email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $position, $greeting_text, $excerpt, $email, $phone, $id]);
        
        if ($photo) {
            $pdo->prepare("UPDATE landing_headmaster_greeting SET photo = ? WHERE id = ?")
                ->execute([$photo, $id]);
        }
        
        $_SESSION['pesan_sukses'] = "Sambutan berhasil diperbarui.";
    } else {
        // Create new
        $stmt = $pdo->prepare("INSERT INTO landing_headmaster_greeting 
                               (name, position, greeting_text, excerpt, email, phone, photo) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $position, $greeting_text, $excerpt, $email, $phone, $photo]);
        $_SESSION['pesan_sukses'] = "Sambutan berhasil ditambahkan.";
    }
    
    redirect('index.php?mod=landing_admin&act=sambutan');
}

// Similar functions untuk Program, Fasilitas, Testimonials, FAQs...
```

#### Step 2.3: Update public/index.php

Add routing untuk fitur baru:

```php
case 'landing':
    if ($act == 'index')
        landing_index($pdo);
    elseif ($act == 'berita_list')
        berita_list($pdo);
    elseif ($act == 'berita_detail')
        berita_detail($pdo);
    elseif ($act == 'berita_search')
        berita_search($pdo);
    elseif ($act == 'berita_by_category')
        berita_by_category($pdo);
    elseif ($act == 'sambutan_kepala')
        sambutan_kepala($pdo);
    elseif ($act == 'ppdb_form')
        ppdb_public_form($pdo);
    // ... rest of existing routes
    else
        landing_index($pdo);
    break;

case 'landing_admin':
    if ($act == 'index' || $act == '')
        landing_admin_dashboard($pdo);
    elseif ($act == 'settings')
        landing_admin_settings($pdo);
    elseif ($act == 'save_settings')
        landing_admin_settings_save($pdo);
    elseif ($act == 'news')
        landing_admin_news_index($pdo);
    elseif ($act == 'news_form')
        landing_admin_news_form($pdo);
    elseif ($act == 'news_save')
        landing_admin_news_save($pdo);
    elseif ($act == 'sambutan')
        landing_admin_sambutan($pdo);
    elseif ($act == 'sambutan_form')
        landing_admin_sambutan_form($pdo);
    elseif ($act == 'sambutan_save')
        landing_admin_sambutan_save($pdo);
    // ... add more routes for programs, facilities, etc.
    else
        landing_admin_dashboard($pdo);
    break;
```

---

### **PHASE 3: Create Views**

#### Step 3.1: Create `berita_detail.php`

Create file: `app/views/berita_detail.php`

```php
<?php
// app/views/berita_detail.php
// Tampilkan artikel berita lengkap dengan konten penuh

$title = $berita['title'];
$config = require '../config/app.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $berita['title'] ?> - <?= $config['school']['name'] ?></title>
    <meta name="description" content="<?= $berita['meta_description'] ?? substr($berita['content'], 0, 160) ?>">
    <meta name="keywords" content="<?= $berita['seo_keywords'] ?? $berita['tags'] ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing-page.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/berita-detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <!-- Same as landing_page.php -->
    </nav>
    
    <!-- BREADCRUMB -->
    <div class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?mod=landing">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?mod=landing&act=berita_list">Berita</a></li>
                    <li class="breadcrumb-item active"><?= $berita['title'] ?></li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- ARTICLE DETAIL -->
    <article class="article-detail">
        <div class="container">
            <!-- FEATURED IMAGE -->
            <div class="article-header">
                <img src="<?= BASE_URL . $berita['featured_image'] ?>" alt="<?= $berita['title'] ?>" 
                     class="featured-image">
            </div>
            
            <!-- ARTICLE META -->
            <div class="article-meta">
                <h1><?= $berita['title'] ?></h1>
                <div class="meta-info">
                    <span class="badge"><?= ucfirst($berita['category']) ?></span>
                    <span class="author">👤 <?= $berita['author'] ?></span>
                    <span class="date">📅 <?= date('d M Y', strtotime($berita['publish_date'])) ?></span>
                    <span class="reading-time">⏱ <?= ceil(str_word_count($berita['content']) / 200) ?> min baca</span>
                    <span class="views">👁 <?= $berita['view_count'] ?> views</span>
                </div>
            </div>
            
            <div class="article-body">
                <!-- SHARE BUTTONS -->
                <div class="share-buttons">
                    <span>Bagikan:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                       target="_blank" class="share-btn facebook" title="Share on Facebook">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://wa.me/?text=<?= urlencode($berita['title'] . ' ' . $_SERVER['REQUEST_URI']) ?>" 
                       target="_blank" class="share-btn whatsapp" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($berita['title']) ?>" 
                       target="_blank" class="share-btn twitter" title="Share on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
                
                <!-- ARTICLE CONTENT -->
                <div class="article-content">
                    <?= $berita['content'] ?>
                </div>
                
                <!-- TAGS -->
                <?php if (!empty($berita['tags'])): ?>
                <div class="article-tags">
                    <?php foreach (explode(',', $berita['tags']) as $tag): ?>
                        <a href="index.php?mod=landing&act=berita_search?q=<?= urlencode(trim($tag)) ?>" 
                           class="tag">#<?= trim($tag) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- AUTHOR BIO -->
            <div class="author-bio">
                <div class="author-card">
                    <img src="<?= BASE_URL ?>assets/img/default-avatar.png" alt="<?= $berita['author'] ?>" 
                         class="author-photo">
                    <div class="author-info">
                        <h3><?= $berita['author'] ?></h3>
                        <p>Guru di <?= $config['school']['name'] ?></p>
                        <p class="bio">Seorang pendidik berpengalaman yang passionate dalam bidang pendidikan.</p>
                    </div>
                </div>
            </div>
            
            <!-- NAVIGATION -->
            <div class="article-navigation">
                <?php if ($prev_article): ?>
                    <a href="index.php?mod=landing&act=berita_detail&slug=<?= $prev_article['slug'] ?>" 
                       class="nav-prev">
                        <i class="fas fa-chevron-left"></i> <?= $prev_article['title'] ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($next_article): ?>
                    <a href="index.php?mod=landing&act=berita_detail&slug=<?= $next_article['slug'] ?>" 
                       class="nav-next">
                        <?= $next_article['title'] ?> <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- RELATED ARTICLES -->
            <?php if (!empty($related)): ?>
            <section class="related-articles">
                <h2>Berita Terkait</h2>
                <div class="article-grid">
                    <?php foreach ($related as $rel): ?>
                        <div class="article-card">
                            <img src="<?= BASE_URL . $rel['featured_image'] ?>" 
                                 alt="<?= $rel['title'] ?>" class="article-image">
                            <div class="article-info">
                                <h3><?= $rel['title'] ?></h3>
                                <p><?= substr($rel['excerpt'], 0, 100) ?>...</p>
                                <a href="index.php?mod=landing&act=berita_detail&slug=<?= $rel['slug'] ?>" 
                                   class="read-more">Baca Selengkapnya →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- BACK TO LIST -->
            <div class="back-to-list">
                <a href="index.php?mod=landing&act=berita_list">← Kembali ke Daftar Berita</a>
            </div>
        </div>
    </article>
    
    <!-- FOOTER -->
    <?php include __DIR__ . '/partials/footer_landing.php'; ?>
    
    <script src="<?= BASE_URL ?>assets/js/landing-page.js"></script>
    <script src="<?= BASE_URL ?>assets/js/berita-detail.js"></script>
</body>
</html>
```

#### Step 3.2: Create `sambutan_kepala_detail.php`

Create file: `app/views/sambutan_kepala_detail.php`

```php
<?php
// app/views/sambutan_kepala_detail.php
// Halaman detail sambutan kepala sekolah

$config = require '../config/app.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sambutan Kepala Sekolah - <?= $config['school']['name'] ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing-page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <!-- Same as landing_page.php -->
    </nav>
    
    <!-- SAMBUTAN SECTION -->
    <section class="sambutan-detail">
        <div class="container">
            <h1>Sambutan Kepala Sekolah</h1>
            
            <div class="sambutan-content">
                <!-- LEFT: PHOTO -->
                <div class="sambutan-photo">
                    <img src="<?= BASE_URL . $sambutan['photo'] ?>" alt="<?= $sambutan['name'] ?>" 
                         class="headmaster-photo">
                </div>
                
                <!-- RIGHT: INFO & MESSAGE -->
                <div class="sambutan-info">
                    <h2><?= $sambutan['name'] ?></h2>
                    <p class="position">
                        <i class="fas fa-briefcase"></i> <?= $sambutan['position'] ?>
                    </p>
                    
                    <div class="sambutan-text">
                        <?= nl2br($sambutan['greeting_text']) ?>
                    </div>
                    
                    <!-- SIGNATURE (OPTIONAL) -->
                    <div class="sambutan-signature">
                        <p style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #ddd;">
                            <?= $sambutan['name'] ?><br>
                            <?= $sambutan['position'] ?>
                        </p>
                    </div>
                    
                    <!-- CONTACT -->
                    <div class="sambutan-contact">
                        <h3>Hubungi</h3>
                        <?php if ($sambutan['email']): ?>
                            <p><i class="fas fa-envelope"></i> <a href="mailto:<?= $sambutan['email'] ?>">
                                <?= $sambutan['email'] ?></a>
                            </p>
                        <?php endif; ?>
                        <?php if ($sambutan['phone']): ?>
                            <p><i class="fas fa-phone"></i> <a href="tel:<?= $sambutan['phone'] ?>">
                                <?= $sambutan['phone'] ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- BACK BUTTON -->
            <div class="back-button">
                <a href="index.php?mod=landing">← Kembali ke Beranda</a>
            </div>
        </div>
    </section>
    
    <!-- FOOTER -->
    <?php include __DIR__ . '/partials/footer_landing.php'; ?>
    
    <script src="<?= BASE_URL ?>assets/js/landing-page.js"></script>
</body>
</html>
```

#### Step 3.3: Update `berita_list.php`

Create file: `app/views/berita_list.php` - untuk list semua berita dengan pagination

---

### **PHASE 4: Update Admin Forms**

#### Step 4.1: Update `landing_admin/news_form.php`

Tambahkan field baru untuk slug, author, tags, excerpt:

```php
<!-- Add this to news_form.php -->

<!-- Author Field -->
<div class="form-group">
    <label>Penulis <span class="text-danger">*</span></label>
    <input type="text" class="form-control" name="author"
        value="<?= $is_edit ? htmlspecialchars($news['author']) : '' ?>" 
        placeholder="Nama guru/staff penulis" required>
</div>

<!-- Slug Field -->
<div class="form-group">
    <label>Slug (URL-friendly) <span class="text-danger">*</span></label>
    <input type="text" class="form-control" name="slug"
        value="<?= $is_edit ? htmlspecialchars($news['slug']) : '' ?>"
        placeholder="Auto-generated dari judul" required>
    <small class="text-muted">Gunakan huruf kecil, angka, dan hyphen (-)</small>
</div>

<!-- Excerpt Field -->
<div class="form-group">
    <label>Ringkasan (Excerpt)</label>
    <textarea class="form-control" name="excerpt" rows="3"
        placeholder="Ringkasan artikel untuk preview..."><?= $is_edit ? htmlspecialchars($news['excerpt']) : '' ?></textarea>
    <small class="text-muted">Ditampilkan di halaman daftar berita (jika kosong akan diambil dari konten)</small>
</div>

<!-- Tags Field -->
<div class="form-group">
    <label>Tags/Label</label>
    <input type="text" class="form-control" name="tags"
        value="<?= $is_edit ? htmlspecialchars($news['tags']) : '' ?>"
        placeholder="akademik, prestasi, ppdb (pisahkan dengan koma)">
</div>

<!-- SEO Meta Description -->
<div class="form-group">
    <label>Meta Description (SEO)</label>
    <textarea class="form-control" name="meta_description" rows="2"
        maxlength="160"><?= $is_edit ? htmlspecialchars($news['meta_description']) : '' ?></textarea>
    <small class="text-muted">Max 160 karakter untuk SEO</small>
</div>

<!-- Content Editor - UPGRADE dengan TinyMCE -->
<div class="form-group">
    <label>Konten Artikel <span class="text-danger">*</span></label>
    <textarea class="form-control tinymce" name="content" rows="15" required>
        <?= $is_edit ? htmlspecialchars($news['content']) : '' ?>
    </textarea>
</div>

<!-- Add TinyMCE Script -->
<script src="https://cdn.tiny.cloud/1/YOUR_TINYMCE_API_KEY/tinymce/6/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '.tinymce',
    plugins: 'image link code lists table',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image',
    images_upload_url: 'index.php?mod=landing_admin&act=upload_image',
    automatic_uploads: true
});
</script>
```

#### Step 4.2: Create `landing_admin/sambutan_form.php`

```php
<!-- app/views/landing_admin/sambutan_form.php -->

<?php
$title = "Form Sambutan Kepala Sekolah";
$is_edit = isset($sambutan);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header">
    <h1><?= $is_edit ? 'Edit Sambutan' : 'Tambah Sambutan' ?></h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?mod=landing_admin&act=sambutan_save" method="post" enctype="multipart/form-data">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= $sambutan['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Nama Kepala Sekolah <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name"
                    value="<?= $is_edit ? htmlspecialchars($sambutan['name']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Posisi/Jabatan</label>
                <input type="text" class="form-control" name="position"
                    value="<?= $is_edit ? htmlspecialchars($sambutan['position']) : '' ?>"
                    placeholder="Kepala Sekolah">
            </div>
            
            <div class="form-group">
                <label>Foto</label>
                <?php if ($is_edit && !empty($sambutan['photo'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL . $sambutan['photo'] ?>" style="max-height: 150px;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" name="photo" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email"
                    value="<?= $is_edit ? htmlspecialchars($sambutan['email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="tel" class="form-control" name="phone"
                    value="<?= $is_edit ? htmlspecialchars($sambutan['phone']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label>Ringkasan Sambutan</label>
                <textarea class="form-control" name="excerpt" rows="3"
                    placeholder="Ringkasan untuk preview di halaman utama..."><?= $is_edit ? htmlspecialchars($sambutan['excerpt']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Teks Sambutan Lengkap <span class="text-danger">*</span></label>
                <textarea class="form-control tinymce" name="greeting_text" rows="15" required>
                    <?= $is_edit ? htmlspecialchars($sambutan['greeting_text']) : '' ?>
                </textarea>
            </div>
            
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                        <?= (!$is_edit || $sambutan['is_active']) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="is_active">Aktifkan</label>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?mod=landing_admin&act=sambutan" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
```

---

### **PHASE 5: CSS & JS Updates**

#### Step 5.1: Create `berita-detail.css`

```css
/* app/public/assets/css/berita-detail.css */

.breadcrumb-section {
    background: #f8f9fa;
    padding: 1rem 0;
    border-bottom: 1px solid #dee2e6;
}

.article-detail {
    padding: 3rem 0;
}

.article-header {
    margin-bottom: 2rem;
}

.featured-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 15px;
}

.article-meta {
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 1.5rem;
    margin-bottom: 2rem;
}

.article-meta h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--text-dark);
}

.meta-info {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    align-items: center;
    color: var(--text-light);
    font-size: 0.95rem;
}

.meta-info .badge {
    background: var(--primary);
    color: white;
    padding: 0.3rem 1rem;
    border-radius: 20px;
}

.share-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #dee2e6;
    align-items: center;
}

.share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s;
}

.share-btn.facebook {
    background: #3b5998;
}

.share-btn.whatsapp {
    background: #25d366;
}

.share-btn.twitter {
    background: #1da1f2;
}

.share-btn:hover {
    transform: translateY(-3px);
}

.article-content {
    font-size: 1.1rem;
    line-height: 1.8;
    margin-bottom: 2rem;
    color: var(--text-dark);
}

.article-content h2,
.article-content h3 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: var(--primary);
}

.article-content p {
    margin-bottom: 1rem;
}

.article-content img {
    max-width: 100%;
    border-radius: 15px;
    margin: 2rem 0;
}

.article-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin: 2rem 0;
    padding: 1rem 0;
    border-top: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
}

.tag {
    background: #f0f0f0;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    color: var(--primary);
    transition: all 0.3s;
}

.tag:hover {
    background: var(--primary);
    color: white;
}

.author-bio {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    margin: 3rem 0;
}

.author-card {
    display: grid;
    grid-template-columns: 100px 1fr;
    gap: 2rem;
    align-items: center;
}

.author-photo {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}

.author-info h3 {
    margin: 0 0 0.5rem 0;
    color: var(--primary);
}

.author-info .bio {
    color: var(--text-light);
}

.article-navigation {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin: 3rem 0;
}

.nav-prev,
.nav-next {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: var(--primary);
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.nav-prev:hover,
.nav-next:hover {
    background: var(--primary);
    color: white;
}

.nav-next {
    justify-content: flex-end;
}

.related-articles {
    margin: 3rem 0;
}

.related-articles h2 {
    color: var(--primary);
    margin-bottom: 2rem;
}

.article-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.article-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.article-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.article-info {
    padding: 1.5rem;
}

.article-info h3 {
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.read-more {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    margin-top: 1rem;
}

.back-to-list {
    text-align: center;
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #dee2e6;
}

.back-to-list a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .article-meta h1 {
        font-size: 1.8rem;
    }
    
    .meta-info {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }
    
    .author-card {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .author-photo {
        margin: 0 auto;
    }
    
    .article-navigation {
        grid-template-columns: 1fr;
    }
}

/* SAMBUTAN SECTION */
.sambutan-detail {
    padding: 3rem 0;
}

.sambutan-detail h1 {
    text-align: center;
    color: var(--primary);
    margin-bottom: 3rem;
}

.sambutan-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 3rem;
    align-items: start;
    margin-bottom: 3rem;
}

.sambutan-photo {
    text-align: center;
}

.headmaster-photo {
    width: 100%;
    max-width: 300px;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.sambutan-info h2 {
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.position {
    color: var(--text-light);
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.sambutan-text {
    font-size: 1.05rem;
    line-height: 1.8;
    color: var(--text-dark);
    margin-bottom: 2rem;
}

.sambutan-contact {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin-top: 2rem;
}

.sambutan-contact h3 {
    color: var(--primary);
    margin-bottom: 1rem;
}

.sambutan-contact p {
    margin: 0.5rem 0;
}

.sambutan-contact a {
    color: var(--primary);
    text-decoration: none;
}

.back-button {
    text-align: center;
    margin-top: 3rem;
}

.back-button a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

@media (max-width: 768px) {
    .sambutan-content {
        grid-template-columns: 1fr;
    }
}
```

---

## 🛠️ ADMIN PANEL FEATURES

### Landing Admin Menu Structure

```
Landing Admin
├── Dashboard
│   └── Overview (visitors, berita count, gallery count)
├── Settings (EXISTING)
├── Berita & Pengumuman (IMPROVED)
│   ├── Daftar Berita
│   ├── Tambah Berita
│   └── Edit Berita
├── Gallery (EXISTING)
├── Sambutan Kepala Sekolah (NEW)
│   ├── Kelola Sambutan
│   └── Edit Sambutan
├── Program Unggulan (NEW)
├── Fasilitas (NEW)
├── Testimonial (NEW)
├── FAQ (NEW)
└── SEO Settings (NEW)
```

### Key Features to Add

1. **Berita Management**
   - ✅ Auto-generate slug dari title
   - ✅ Rich text editor (TinyMCE/CKEditor)
   - ✅ Featured image upload
   - ✅ Author selection
   - ✅ Tags/category
   - ✅ SEO meta fields
   - ✅ Publish/Draft toggle
   - ✅ Featured toggle

2. **Sambutan Management**
   - ✅ Upload headmaster photo
   - ✅ Rich text editor
   - ✅ Contact info
   - ✅ Active/inactive toggle

3. **Dashboard**
   - ✅ Stats cards (total berita, views, gallery items)
   - ✅ Recent articles
   - ✅ Quick actions

---

## ✅ TESTING CHECKLIST

- [ ] Database migrations berjalan lancar
- [ ] Landing page utama tampil dengan baik
- [ ] Navbar & navigasi berfungsi
- [ ] Hero slider auto-play
- [ ] Berita list page tampil dengan pagination
- [ ] Berita detail page tampil dengan konten lengkap
- [ ] Share buttons bekerja
- [ ] Related articles muncul
- [ ] Previous/next navigation berfungsi
- [ ] Sambutan detail page tampil
- [ ] Admin form untuk berita dapat menyimpan data
- [ ] Admin form untuk sambutan dapat menyimpan data
- [ ] Image upload berfungsi
- [ ] Slug auto-generate (jika kosong)
- [ ] SEO meta tags tampil di halaman detail
- [ ] Mobile responsive di semua halaman
- [ ] Performance optimization (lazy loading, caching)
- [ ] Security: CSRF protection on forms
- [ ] Security: Input sanitization
- [ ] Admin access control

---

## 🐛 TROUBLESHOOTING

### Issue: Slug duplikat

**Solusi:**
- Pastikan setiap berita memiliki slug yang unik
- Gunakan SlugHelper::generate() untuk auto-generate
- Add unique constraint di database

### Issue: Gambar tidak muncul

**Solusi:**
- Cek folder uploads permissions
- Pastikan path relatif benar di database
- Use BASE_URL constant untuk image paths

### Issue: TinyMCE tidak load

**Solusi:**
- Ganti API KEY dengan yang valid
- Check browser console untuk errors
- Pastikan CDN dapat diakses

### Issue: Database errors

**Solusi:**
- Run semua SQL migrations
- Check table names dan column names
- Backup database sebelum alter table

---

## ❓ FAQ

**Q: Bagaimana cara membuat slug otomatis?**  
A: Gunakan SlugHelper::generate() di controller saat save. Cek apakah slug sudah ada sebelum insert.

**Q: Bisa integrate dengan search engine?**  
A: Ya, pastikan meta tags dan structured data (schema.org) sudah ada. Add sitemap.xml untuk SEO.

**Q: Apakah perlu install library tambahan?**  
A: Ya, TinyMCE untuk editor (bisa dari CDN). Bisa juga gunakan CKEditor.

**Q: Bagaimana cara backup berita?**  
A: Regular database backup menggunakan phpMyAdmin atau command line: `mysqldump -u root -p simaks_db > backup.sql`

---

## 📞 SUPPORT & NEXT STEPS

### Implementation Timeline
- **Phase 1** (Database): 1-2 hari
- **Phase 2** (Controllers & Routes): 2-3 hari
- **Phase 3** (Views): 2-3 hari
- **Phase 4** (Admin Forms): 1-2 hari
- **Phase 5** (Styling & JS): 2-3 hari
- **Phase 6** (Testing & Polish): 1-2 hari

**Total: 1-2 minggu**

### Recommended Implementation Order
1. Database setup (Phase 1)
2. Controllers & routing (Phase 2)
3. Create views (Phase 3)
4. Admin management (Phase 4)
5. Styling & UX (Phase 5)
6. Testing & deployment (Phase 6)

---

**Version History:**
- v1.0 (10 Mar 2026): Initial planning document
- v2.0 (10 Mar 2026): Detailed implementation guide

**Created by:** GitHub Copilot Assistant  
**Last Modified:** 10 Maret 2026
