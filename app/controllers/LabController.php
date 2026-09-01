<?php
// app/controllers/LabController.php

class LabController
{

    public static function index($pdo)
    {
        $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;

        // 1. Ambil Inventaris
        $inventaris = $pdo->prepare("SELECT * FROM lab_inventaris WHERE id_ta = ? ORDER BY nama_barang ASC");
        $inventaris->execute([$id_ta]);
        $items = $inventaris->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil Jadwal/Agenda (Reuse table agenda_tugas_tambahan)
        $stmt_agenda = $pdo->prepare("SELECT * FROM agenda_tugas_tambahan WHERE jenis_tugas_tambahan = 'manajemen_lab' AND id_ta = ? ORDER BY tanggal ASC");
        $stmt_agenda->execute([$id_ta]);
        $agendas = $stmt_agenda->fetchAll(PDO::FETCH_ASSOC);

        // 3. Ambil Galeri (Simpel: Scan folder saja untuk sekarang agar ringan)
        $galeri_dir = 'uploads/lab_galeri/';
        $abs_galeri_dir = __DIR__ . '/../../public/' . $galeri_dir;
        $photos = [];
        if (is_dir($abs_galeri_dir)) {
            $files = scandir($abs_galeri_dir);
            foreach ($files as $file) {
                if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
                    $photos[] = $galeri_dir . $file;
                }
            }
        }

        $tahun_ajaran = $pdo->query("SELECT * FROM tahun_ajaran ORDER BY id_ta DESC")->fetchAll(PDO::FETCH_ASSOC);

        $title = "Manajemen Lab Komputer";
        require_once __DIR__ . '/../views/lab_index.php';
    }

    public static function save_inventaris($pdo)
    {
        $id = $_POST['id_inventaris'] ?? null;
        $nama = $_POST['nama_barang'] ?? '';
        $merek = $_POST['merek_tipe'] ?? '';
        $baik = $_POST['kondisi_baik'] ?? 0;
        $rusak = $_POST['kondisi_rusak'] ?? 0;
        $id_ta = $_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;
        $total = $baik + $rusak;

        if ($id) {
            $sql = "UPDATE lab_inventaris SET nama_barang=?, merek_tipe=?, jumlah_total=?, kondisi_baik=?, kondisi_rusak=?, id_ta=? WHERE id_inventaris=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $merek, $total, $baik, $rusak, $id_ta, $id]);
        } else {
            $sql = "INSERT INTO lab_inventaris (nama_barang, merek_tipe, jumlah_total, kondisi_baik, kondisi_rusak, id_ta) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $merek, $total, $baik, $rusak, $id_ta]);
        }

        $_SESSION['pesan_sukses'] = "Data inventaris disimpan.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public static function delete_inventaris($pdo)
    {
        $id = $_GET['id'] ?? 0;
        $pdo->prepare("DELETE FROM lab_inventaris WHERE id_inventaris = ?")->execute([$id]);
        $_SESSION['pesan_sukses'] = "Data dihapus.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public static function upload_foto($pdo)
    {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = __DIR__ . '/../../public/uploads/lab_galeri/';
            if (!is_dir($target_dir))
                mkdir($target_dir, 0777, true);

            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $filename = 'lab_' . time() . '_' . rand(100, 999) . '.' . $ext;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $filename)) {
                $_SESSION['pesan_sukses'] = "Foto berhasil diunggah.";
            }
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
