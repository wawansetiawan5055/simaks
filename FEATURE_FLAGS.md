# SIMAKS Feature Flags Configuration

Quick reference untuk mengaktifkan/menonaktifkan fitur SIMAKS dan CBT.

## File Konfigurasi

**Location:** `config/app.php`

## Feature Flags

```php
<?php
/**
 * SIMAKS Feature Flags
 * Kontrol fitur mana yang aktif dalam aplikasi
 */

// ===== SIMAKS MODULE =====
define('ENABLE_SIMAKS', true);      // Aktifkan modul SIMAKS utama (default: true)

// ===== CBT MODULE =====
define('ENABLE_CBT', true);         // Aktifkan modul CBT (Computer Based Test) (default: true)

// ===== DATABASE CONFIG =====
define('CBT_DB_SHARED', true);      // Gunakan database shared (db_simaks) untuk CBT
                                     // Jika false: gunakan database terpisah (db_cbt)
```

## Matrix Konfigurasi

### Skenario 1: SIMAKS + CBT (Default)

```php
define('ENABLE_SIMAKS', true);
define('ENABLE_CBT', true);
define('CBT_DB_SHARED', true);
```

**Hasil:**
- ✅ Menu SIMAKS di sidebar
- ✅ Menu CBT di sidebar (dapat diakses)
- 📊 Database: `db_simaks` (shared)
- 🌐 URL: `/index.php?mod=...` (SIMAKS) + `/simaks/cbt/` (CBT)

---

### Skenario 2: SIMAKS Saja

```php
define('ENABLE_SIMAKS', true);
define('ENABLE_CBT', false);        // ← Disable CBT
define('CBT_DB_SHARED', true);      // Tidak relevan
```

**Hasil:**
- ✅ Menu SIMAKS di sidebar
- ❌ Menu CBT tidak muncul di sidebar
- 📊 Database: `db_simaks` (hanya tabel simaks_*)
- 🌐 URL: `/index.php?mod=...` (SIMAKS saja)
- ❌ Akses `/simaks/cbt/` return 404 atau "Feature Disabled"

---

### Skenario 3: CBT Standalone

**Konfigurasi:**
- Folder terpisah: `/www/wwwroot/cbt.app/`
- File config: `cbt.app/config/db.php` (bukan di SIMAKS)
- Database: `db_cbt` (dedicated)
- Tidak perlu flag di SIMAKS (karena aplikasi terpisah)

```php
// Di cbt.app/config/db.php
$dbname = 'db_cbt';      // ← Database terpisah
$user = 'cbt_admin';
$pass = 'cbt_password_123';
```

**Hasil:**
- ❌ SIMAKS tidak running
- ✅ Menu CBT saja (full UI CBT)
- 📊 Database: `db_cbt` (dedicated)
- 🌐 URL: `http://cbt.app:7166/` (atau custom subdomain/port)

---

## Cara Mengubah Konfigurasi

### Dari SIMAKS+CBT → SIMAKS Saja

```php
// File: config/app.php
// Ubah dari:
define('ENABLE_CBT', true);

// Menjadi:
define('ENABLE_CBT', false);

// Kemudian reload browser
```

### Dari SIMAKS+CBT → CBT Saja (Manual)

Tidak ada flag di SIMAKS. Sebaliknya:

1. Siapkan folder CBT terpisah di `/www/wwwroot/cbt.app/`
2. Buat database `db_cbt`
3. Setup Nginx vhost untuk `cbt.app`
4. Akses aplikasi via URL CBT

Referensi lengkap: Lihat `SETUP_GUIDE.md` → Skenario 3

---

## Pengaruh Flag terhadap UI/Menu

### Flag: ENABLE_SIMAKS

| Value | Sidebar | Dashboard | Controllers | Database |
|-------|---------|-----------|-------------|----------|
| `true` | Menu SIMAKS visible | ✅ Accessible | ✅ Loaded | `db_simaks` |
| `false` | Menu SIMAKS hidden | ❌ 404 | ⚠️ Not loaded | Not used |

### Flag: ENABLE_CBT

| Value | Sidebar | URL `/simaks/cbt/` | Controllers | Database |
|-------|---------|---|---|---|
| `true` | Menu CBT visible | ✅ Accessible | ✅ Loaded | `db_simaks` (shared) |
| `false` | Menu CBT hidden | ❌ 404 or disabled | ⚠️ Not loaded | Not used |

### Flag: CBT_DB_SHARED

| Value | Database untuk CBT | Config File | Use Case |
|-------|---|---|---|
| `true` | `db_simaks` | `cbt/config/db.php` → db_simaks | SIMAKS+CBT integrated |
| `false` | `db_cbt` | `cbt/config/db.php` → db_cbt | CBT standalone (Skenario 3) |

---

## Environment Variables (Alternative)

Jika ingin manage flags via environment variables:

**File: `.env`**

```env
ENABLE_SIMAKS=true
ENABLE_CBT=true
CBT_DB_SHARED=true
```

**File: `config/app.php`**

```php
// Load dari .env (gunakan library seperti vlucas/phpdotenv)
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

define('ENABLE_SIMAKS', $_ENV['ENABLE_SIMAKS'] ?? 'true');
define('ENABLE_CBT', $_ENV['ENABLE_CBT'] ?? 'true');
define('CBT_DB_SHARED', $_ENV['CBT_DB_SHARED'] ?? 'true');
```

---

## Kondisional di View/Sidebar

**File: `app/views/partials/sidebar.php`**

```php
<!-- Menu SIMAKS (conditional) -->
<?php if (defined('ENABLE_SIMAKS') && ENABLE_SIMAKS): ?>
    <li class="nav-item">
        <a href="<?= BASE_URL ?>?mod=dashboard" class="nav-link">
            <i class="fas fa-chart-bar"></i>
            <p>Dashboard SIMAKS</p>
        </a>
    </li>
    <!-- ... modul SIMAKS lain ... -->
<?php endif; ?>

<!-- Menu CBT (conditional) -->
<?php if (defined('ENABLE_CBT') && ENABLE_CBT): ?>
    <li class="nav-item">
        <a href="<?= BASE_URL ?>/simaks/cbt/" class="nav-link">
            <i class="fas fa-laptop-code"></i>
            <p>CBT</p>
        </a>
    </li>
<?php endif; ?>
```

---

## Testing Configuration

### Test SIMAKS Active

```bash
curl http://simaks.app:7166/index.php?mod=dashboard
# Response: HTML dashboard SIMAKS
```

### Test CBT Active

```bash
curl -H "Host: simaks.app" http://192.168.1.100:7166/simaks/cbt/
# Response: JSON atau HTML login CBT
```

### Test Feature Disabled

```bash
# Jika ENABLE_CBT = false
curl http://simaks.app:7166/simaks/cbt/
# Response: 404 Not Found atau "Feature Disabled"
```

---

## Checklist

- [ ] Pahami 3 skenario deployment
- [ ] Set flags di `config/app.php` sesuai kebutuhan
- [ ] Verify database credentials di `config/db.php`
- [ ] Test URL masing-masing fitur
- [ ] Clear browser cache jika perlu
- [ ] Document custom changes untuk team

---

**Last Updated:** March 2026  
**Maintained By:** SIMAKS Development Team
