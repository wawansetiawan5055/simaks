<?php
require 'config/db.php';
$pdo = connect_db();

$common_cols = ['id_siswa', 'nama', 'nisn', 'nipd', 'nik', 'jk', 'tempat_lahir', 'tanggal_lahir', 'sekolah_asal', 'status_aktif', 'id_ta_masuk', 'id_pengguna'];
$col_str = implode(',', $common_cols);

echo "--- RESTORASI DATA SISWA ---\n";

// 0. Sanitasi: Ubah '' menjadi NULL agar tidak bentrok dengan UNIQUE KEY (MySQL memperbolehkan banyak NULL)
$unique_cols = ['nisn', 'nipd', 'nik'];
foreach(['siswa', 'siswa_alumni', 'siswa_mutasi'] as $table) {
    foreach($unique_cols as $col) {
        $pdo->exec("UPDATE $table SET $col = NULL WHERE $col = ''");
    }
}
echo "Sanitasi UNIQUE columns selesai.\n";

// 1. Dari Alumni
$sql_alumni = "INSERT INTO siswa ($col_str) 
               SELECT $col_str FROM siswa_alumni 
               WHERE id_siswa NOT IN (SELECT id_siswa FROM siswa)";
$res_alumni = $pdo->exec($sql_alumni);
echo "Berhasil memulihkan $res_alumni siswa dari Alumni.\n";

// 2. Dari Mutasi
$sql_mutasi = "INSERT INTO siswa ($col_str) 
               SELECT $col_str FROM siswa_mutasi 
               WHERE id_siswa NOT IN (SELECT id_siswa FROM siswa)";
$res_mutasi = $pdo->exec($sql_mutasi);
echo "Berhasil memulihkan $res_mutasi siswa dari Mutasi.\n";

// 3. Pastikan status_aktif sinkron
$pdo->exec("UPDATE siswa s JOIN siswa_alumni sa ON s.id_siswa = sa.id_siswa SET s.status_aktif = 'Lulus'");
$pdo->exec("UPDATE siswa s JOIN siswa_mutasi sm ON s.id_siswa = sm.id_siswa SET s.status_aktif = 'Keluar'");

echo "Sinkronisasi status selesai.\n";
