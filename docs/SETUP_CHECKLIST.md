# Setup Checklist - SIMAKS Environment
**Tanggal:** April 19, 2026  
**Status:** Dalam Proses

---

## 1. PHP Version
- **Status:** ✅ OK
- **Versi:** 8.5.2 (CLI)
- **Kompatibilitas:** >= 8.0 ✅
- **Catatan:** Warning zip.so extension (tidak kritikal untuk core functionality)

## 2. Database Server
- **Status:** ✅ OK
- **Versi:** MariaDB 10.11.10
- **Engine:** InnoDB supported ✅
- **Catatan:** -

## 3. Composer Dependencies
- **Status:** ⚠️ Perlu Perbaikan
- **Issues:**
  - Git ownership: `detected dubious ownership` → Jalankan `git config --global --add safe.directory /www/wwwroot/simaks.app`
  - PHP version too high: htmlpurifier max 8.4, current 8.5 → Update composer.lock atau downgrade PHP jika perlu
  - Missing ext-fileinfo → Install extension atau gunakan `--ignore-platform-req=ext-fileinfo`
- **Rekomendasi:** Jalankan `composer update` setelah fix ownership

## 4. PDO Configuration
- **Status:** ✅ OK
- **File:** `config/db.php`
- **Fitur:** Prepared statements, persistent connections ✅
- **Credentials Default:** localhost, db_simaks, administrator, 20247166
- **Catatan:** Test koneksi manual diperlukan

## 5. Backup & Version Control
- **Status:** ⏳ Belum Dicek
- **Git:** Repository ada, tapi ownership issue
- **Backup DB:** Perlu export manual dari phpMyAdmin
- **Rekomendasi:** Backup sebelum modifikasi

## 6. File Permissions
- **Status:** ⏳ Belum Dicek
- **Uploads:** Folder `uploads/` dan subfolder perlu write permission
- **Logs:** Folder `logs/` perlu write permission
- **Rekomendasi:** `chmod 755` untuk folders, `644` untuk files

---

## Next Steps:
1. Fix git ownership: `git config --global --add safe.directory /www/wwwroot/simaks.app`
2. Update composer: `composer update --ignore-platform-req=ext-fileinfo`
3. Test database connection dengan credentials yang benar
4. Backup database dan kode
5. Set permissions untuk uploads/logs

**Overall Status:** 60% Complete - Ready for Phase 1 after fixes.