-- ============================================================
-- Migration: Menambah kolom id_ta ke tabel kelas
-- Tanggal: 2026-01-15
-- Tujuan: Testing - Menambah id_ta untuk mendukung rombel per tahun ajaran
-- ============================================================

-- STEP 1: Tambah kolom id_ta (nullable dulu)
ALTER TABLE `kelas` 
ADD COLUMN `id_ta` INT NULL 
COMMENT 'FK ke tahun_ajaran - mengikat kelas ke tahun ajaran tertentu';

-- STEP 2: Set nilai default untuk data kelas yang sudah ada
-- Sesuaikan id_ta dengan tahun ajaran yang sedang aktif
-- Contoh: 5 adalah id_ta untuk tahun 2024/2025
UPDATE `kelas` 
SET `id_ta` = 5 
WHERE `id_ta` IS NULL;

-- STEP 3: Ubah kolom menjadi NOT NULL
ALTER TABLE `kelas` 
MODIFY COLUMN `id_ta` INT NOT NULL;

-- STEP 4: Tambahkan foreign key constraint
ALTER TABLE `kelas`
ADD CONSTRAINT `fk_kelas_tahun_ajaran` 
FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran`(`id_ta`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- STEP 5: Tambahkan index untuk performance
ALTER TABLE `kelas`
ADD INDEX `idx_kelas_id_ta` (`id_ta`);

-- ============================================================
-- CATATAN TESTING:
-- ============================================================
-- 1. Setelah migrasi ini, tabel kelas akan punya kolom id_ta
-- 2. Semua kelas existing akan ter-assign ke TA dengan id = 5
-- 3. APLIKASI BELUM DIUBAH, jadi:
--    - KelasModel::all() masih menampilkan semua kelas (tidak filter id_ta)
--    - Form kelas belum ada input id_ta
--    - Dropdown kelas di modul lain masih global
-- 4. Untuk test lengkap, perlu update application layer (Fase selanjutnya)
--
-- Cara Test Berhasil:
-- - SELECT * FROM kelas; → semua record punya id_ta = 5
-- - SELECT * FROM kelas WHERE id_ta = 5; → menampilkan kelas
-- - INSERT INTO kelas (nama_kelas, tingkat, id_ta) VALUES ('X-4', 'X', 5); → berhasil
-- - INSERT INTO kelas (nama_kelas, tingkat, id_ta) VALUES ('X-5', 'X', 999); → GAGAL (FK constraint)
-- ============================================================

-- ============================================================
-- ROLLBACK (jika perlu dibatalkan)
-- ============================================================
-- Uncomment baris di bawah untuk rollback

-- ALTER TABLE `kelas` DROP FOREIGN KEY `fk_kelas_tahun_ajaran`;
-- ALTER TABLE `kelas` DROP INDEX `idx_kelas_id_ta`;
-- ALTER TABLE `kelas` DROP COLUMN `id_ta`;
