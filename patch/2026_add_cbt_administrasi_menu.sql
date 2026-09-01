-- Patch: Tambah Menu Administrasi Ujian CBT
INSERT INTO app_menu (id_menu, nama_menu, link, icon, parent_id, urutan, status)
VALUES (4257, 'Administrasi Ujian', 'cbt_administrasi', 'fas fa-folder-open', 4250, 66, 'Aktif')
ON DUPLICATE KEY UPDATE 
    nama_menu = 'Administrasi Ujian', 
    link = 'cbt_administrasi', 
    icon = 'fas fa-folder-open', 
    parent_id = 4250, 
    urutan = 66, 
    status = 'Aktif';

-- Hak Akses untuk Admin (id_peran = 1)
INSERT INTO hak_akses (id_peran, id_menu, can_create, can_read, can_update, can_delete)
VALUES (1, 4257, 1, 1, 1, 1)
ON DUPLICATE KEY UPDATE can_create = 1, can_read = 1, can_update = 1, can_delete = 1;

-- Hak Akses untuk Guru (id_peran = 4)
INSERT INTO hak_akses (id_peran, id_menu, can_create, can_read, can_update, can_delete)
VALUES (4, 4257, 1, 1, 1, 1)
ON DUPLICATE KEY UPDATE can_create = 1, can_read = 1, can_update = 1, can_delete = 1;
