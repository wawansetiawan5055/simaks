-- Database Update for 4 Modules: Kewirausahaan, Tahfidz, Pembiasaan, Kokurikuler

-- 1. Kewirausahaan
ALTER TABLE kewirausahaan_agenda ADD COLUMN tipe ENUM('program', 'agenda') DEFAULT 'agenda' AFTER keterangan;
ALTER TABLE kewirausahaan_agenda ADD COLUMN lokasi VARCHAR(255) AFTER nama_kegiatan;

-- 2. Tahfidz
ALTER TABLE tahfidz_agenda ADD COLUMN tipe ENUM('program', 'agenda') DEFAULT 'agenda' AFTER keterangan;
ALTER TABLE tahfidz_agenda ADD COLUMN lokasi VARCHAR(255) AFTER nama_agenda;

-- 3. Pembiasaan
ALTER TABLE agenda_pembiasaan ADD COLUMN tipe ENUM('program', 'agenda') DEFAULT 'agenda' AFTER keterangan;
ALTER TABLE agenda_pembiasaan ADD COLUMN lokasi VARCHAR(255) AFTER nama_agenda;

-- 4. Kokulikuler
ALTER TABLE agenda_kokulikuler ADD COLUMN tipe ENUM('program', 'agenda') DEFAULT 'agenda' AFTER keterangan;
ALTER TABLE agenda_kokulikuler ADD COLUMN lokasi VARCHAR(255) AFTER nama_agenda;

-- Data Migration: Set 'program' for records that look like program files (optional but good)
-- Usually records with file_path and general names are programs.
-- For now, let's just add the columns. Existing records will be 'agenda' by default.
-- In the views, we can filter by `(tipe = 'program' OR (tipe = 'agenda' AND name LIKE '%Program%'))` if needed, 
-- but better to just set them via SQL if possible.

UPDATE kewirausahaan_agenda SET tipe = 'program' WHERE nama_kegiatan LIKE '%Program%';
UPDATE tahfidz_agenda SET tipe = 'program' WHERE nama_agenda LIKE '%Program%';
UPDATE agenda_pembiasaan SET tipe = 'program' WHERE nama_agenda LIKE '%Program%';
UPDATE agenda_kokulikuler SET tipe = 'program' WHERE nama_agenda LIKE '%Program%';
