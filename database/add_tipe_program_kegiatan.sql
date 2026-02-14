ALTER TABLE program_kegiatan ADD COLUMN tipe ENUM('program', 'agenda') DEFAULT 'agenda' AFTER id_ekskul;
