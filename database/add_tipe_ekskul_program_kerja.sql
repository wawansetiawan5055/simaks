ALTER TABLE ekskul_program_kerja ADD COLUMN tipe ENUM('program', 'agenda') DEFAULT 'agenda' AFTER id_ekskul;
