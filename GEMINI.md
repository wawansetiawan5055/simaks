# SIMAKS ARCHITECTURE & AGENT DEVELOPMENT RULES

Lihat panduan lengkap A-Z di [AGENTS.md](file:///d:/BtSoft/wwwroot/simaks.app/AGENTS.md).

### RINGKASAN ATURAN WAJIB (MUST READ):
1. **STRICT SEPARATION OF CONCERNS**: JANGAN PERNAH menyisipkan query `UPDATE`, `INSERT`, `ALTER TABLE`, atau migrasi/seeder otomatis ke dalam `AppMenuModel::getUserMenu()`, `sidebar.php`, `header.php`, atau `index.php`. Migrasi menu dan database HARUS selalu dibuat di file terpisah (`sql/` atau `patch/`).
2. **DATABASE INTEGRITY**: Selalu gunakan PDO Prepared Statements (`$pdo->prepare()`) untuk menghindari celah SQL Injection.
3. **ROUTING & URL**: Selalu gunakan konstanta `BASE_URL` untuk link internal dan redirect (jangan hardcode URL absolut).
4. **PERFORMANCE & DISK I/O**: Dilarang melakukan penulisan log sinkron menggunakan `file_put_contents` di setiap request HTTP.
5. **RBAC & SECURITY**: Selalu validasi login (`is_logged_in()`) dan hak akses peran (`$_SESSION['roles']`).
