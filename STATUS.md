# Status CBT Integration - March 1, 2026

## ✅ Completed

1. **CBT Repository Structure**
   - ✅ Folder `/www/wwwroot/simaks.app/cbt/` ready
   - ✅ Config files: `config/db.php`, `config/bridge.php`, `config/session.php`
   - ✅ Public router: `cbt/public/index.php`
   - ✅ Controllers: AuthController, DashboardController, AdminController, BankSoalController, UjianController

2. **Nginx Configuration**
   - ✅ Vhost `simaks.app` configured for port 7166
   - ✅ Symlink created: `/public/simaks/cbt → ../../cbt/public`
   - ✅ HTTP 200 response from `/simaks/cbt/` URL
   - ✅ Asset paths fixed in header/footer (removed `/simaks/public/` prefix)

3. **Database**
   - ✅ CBT tables exist in `db_simaks`:
     - cbt_bank_soal
     - cbt_jadwal
     - cbt_jawaban
     - cbt_kelas
     - cbt_log_aktivitas
     - cbt_mapel
     - cbt_nilai
     - cbt_paket
     - cbt_peserta
     - cbt_siswa
     - cbt_soal
     - cbt_soal_media
     - cbt_soal_opsi
     - cbt_users
   - ✅ User `administrator` has GRANT on `db_simaks`

4. **Configuration**
   - ✅ Updated `cbt/config/db.php` to use `db_simaks` (not `db_simaks_cbt`)
   - ✅ Feature flags configured: `ENABLE_SIMAKS=true`, `ENABLE_CBT=true`
   - ✅ Created `SETUP_GUIDE.md` with 3 deployment scenarios
   - ✅ Created `FEATURE_FLAGS.md` for configuration reference

---

## ⚠️ Current Issue

**Symptom:** Database connection error from web context (PHP-FPM)

```
{"status":"error","message":"Gagal koneksi DB CBT: SQLSTATE[HY000] [1044] Access denied for user 'administrator'@'localhost'..."}
```

**Context:**
- ✅ MySQL CLI connection works: `mysql -uadministrator -p'20247166'` → SUCCESS
- ✅ PHP CLI test works: `php -r "PDO("mysql:..."); echo 'OK';"` → SUCCESS  
- ❌ PHP-FPM web context fails: Browser request to `/simaks/cbt/` → DB ERROR

**Root Cause (Suspected):**
- PHP-FPM runs as `www` user
- Possible socket permission issue between `www` user and MySQL socket
- Possible PHP-FPM pool isolation
- Cache/persistent connection issue

---

## 🔧 Troubleshooting Steps (For Development Team)

### Option 1: Verify MySQL Socket Permissions

```bash
# Check socket location and permissions
ls -l /var/run/mysqld/mysql.sock
ls -l /tmp/mysql.sock

# Ensure www user can read socket
sudo usermod -a -G mysql www
sudo systemctl restart mysql
sudo systemctl restart php-fpm-80

# Test again
curl -H "Host: simaks.app" http://192.168.1.100:7166/simaks/cbt/
```

### Option 2: Clear PHP-FPM Cache

```bash
# Restart PHP-FPM to clear persistent connections
sudo systemctl restart php-fpm-80

# Reload Nginx
sudo systemctl reload nginx

# Test
curl -H "Host: simaks.app" http://192.168.1.100:7166/simaks/cbt/
```

### Option 3: Use TCP Instead of Socket

**File: `cbt/config/db.php`** (Alternative)

```php
// Change from socket to TCP
$host = '127.0.0.1:3306';  // ← TCP instead of socket
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
```

### Option 4: Check aPanel Database Permissions

In aPanel:
1. Go to **Databases** → Select `db_simaks`
2. Verify **Users** → `administrator` has ALL PRIVILEGES
3. If not, click **Privileges** → Grant ALL
4. Save and test

### Option 5: Debug via Test File

```bash
# Create test file
cat > /www/wwwroot/simaks.app/cbt/public/test-db.php << 'EOF'
<?php
echo "Testing database connection...<br>";
$user = 'administrator';
$pass = '20247166';
$host = 'localhost';
$db = 'db_simaks';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "✓ Connection successful<br>";
    $result = $pdo->query("SELECT COUNT(*) as cnt FROM cbt_users")->fetch();
    echo "✓ CBT Users count: " . $result['cnt'];
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
EOF

# Test via browser
curl -H "Host: simaks.app" http://192.168.1.100:7166/simaks/cbt/test-db.php
```

---

## 📋 Next Steps

### For Immediate Deployment (Workaround):

If database issue cannot be resolved quickly, **use Skenario 3 (CBT Standalone)**:

1. Create dedicated `db_cbt` database
2. Migrate CBT tables from `db_simaks` to `db_cbt`
3. Deploy as separate vhost: `cbt.app` or `cbt.sekolah.com`
4. This isolates CBT from SIMAKS database issues

### For Production Fix:

1. Resolve PHP-FPM ↔ MySQL socket issue
2. Test full SIMAKS+CBT integration
3. Run comprehensive test suite
4. Document solution for team

### Documentation Complete:

- ✅ `SETUP_GUIDE.md` - Step-by-step for all 3 scenarios
- ✅ `FEATURE_FLAGS.md` - Configuration reference
- ✅ `README.md` (can be updated with link to guides)

---

## 📞 Reference URLs

**Test URLs:**
- SIMAKS: `http://simaks.app:7166/` or `http://192.168.1.100:7166/`
- CBT: `http://simaks.app:7166/simaks/cbt/` (expected after DB fix)
- Test DB: `http://simaks.app:7166/simaks/cbt/test-db.php` (for debugging)

**Log Files:**
- Nginx: `/var/log/nginx/error.log`
- PHP-FPM: `/var/log/php-fpm.log` or `/www/wwwlogs/php-error.log`
- MySQL: `/var/log/mysql/error.log` or `/var/log/mariadb/error.log`

---

## 📝 Files Modified/Created

1. **Config Updates:**
   - `/www/wwwroot/simaks.app/cbt/config/db.php` - Changed to `db_simaks`
   - `/www/wwwroot/simaks.app/cbt/app/views/partials/header.php` - Fixed asset paths
   - `/www/wwwroot/simaks.app/cbt/app/views/partials/footer.php` - Fixed asset paths

2. **Nginx:**
   - `/www/server/panel/vhost/nginx/simaks.app.conf` - Configured CBT routing

3. **Symlink:**
   - `/www/wwwroot/simaks.app/public/simaks/cbt → ../../cbt/public`

4. **Documentation:**
   - `SETUP_GUIDE.md` - Comprehensive setup for 3 scenarios
   - `FEATURE_FLAGS.md` - Configuration reference
   - `STATUS.md` (this file) - Current status and troubleshooting

---

## ✨ Success Indicators

When issue resolved, expect:

```bash
$ curl -s -H "Host: simaks.app" http://192.168.1.100:7166/simaks/cbt/ | head -n 5
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

# Or if not logged in, redirect to login:
$ curl -I -H "Host: simaks.app" http://192.168.1.100:7166/simaks/cbt/
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
```

And menu CBT visible in SIMAKS sidebar.

---

**Last Updated:** March 1, 2026, 05:30 UTC+7  
**Status:** Integration structure complete, DB access pending fix
