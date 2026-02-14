<?php
// app/controllers/SuratController.php

require_once '../app/models/SuratModel.php';

class SuratController {
    private $pdo;
    private $suratModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->suratModel = new SuratModel($pdo);
        // Ensure tables exist
        $this->suratModel->initTables();
    }

    public function index() {
        $pdo = $this->pdo;
        $totalMasuk = count($this->suratModel->getSuratMasuk());
        $allKeluar = $this->suratModel->getSuratKeluar();
        $totalKeluar = count(array_filter($allKeluar, function($s) { return $s['status'] == 'Final'; }));
        $totalDraft = count(array_filter($allKeluar, function($s) { return $s['status'] == 'Draft'; }));
        $templates = $this->suratModel->getTemplates();
        
        include '../app/views/surat_index.php';
    }

    public function masuk() {
        $pdo = $this->pdo;
        $list = $this->suratModel->getSuratMasuk();
        include '../app/views/surat_masuk_view.php';
    }

    public function keluar() {
        $pdo = $this->pdo;
        $list = $this->suratModel->getSuratKeluar();
        $kategori = $this->suratModel->getKategori();
        $templates = $this->suratModel->getTemplates();
        
        include '../app/views/surat_keluar_view.php';
    }

    public function template() {
        $pdo = $this->pdo;
        $list = $this->suratModel->getTemplates();
        $kategori = $this->suratModel->getKategori();
        include '../app/views/surat_template_view.php';
    }

    public function get_nomor_otomatis() {
        $id_kategori = $_GET['id_kategori'];
        echo $this->suratModel->generateNomorSurat($id_kategori);
    }

    public function get_template_content() {
        $id = $_GET['id'];
        $template = $this->suratModel->getTemplateById($id);
        echo json_encode($template);
    }


    public function save_masuk() {
        if ($_POST) {
            $this->suratModel->saveSuratMasuk($_POST);
            header("Location: index.php?mod=surat&act=masuk");
        }
    }

    public function save_keluar() {
        if ($_POST) {
            $this->suratModel->saveSuratKeluar($_POST);
            header("Location: index.php?mod=surat&act=keluar");
        }
    }

    public function save_template() {
        if ($_POST) {
            $this->suratModel->saveTemplate($_POST);
            header("Location: index.php?mod=surat&act=template");
        }
    }

    public function print_keluar() {
        $id = $_GET['id'];
        $surat = $this->suratModel->getSuratKeluarById($id);
        
        if ($surat) {
            // Mail Merge: Parse template if needed
            $surat['isi_surat'] = $this->suratModel->parseTemplate(
                $surat['isi_surat'], 
                $surat['id_referensi_siswa'], 
                $surat['id_referensi_guru']
            );

            // Fetch School Profile for Kop Surat
            require_once '../app/models/ProfilSekolahModel.php';
            $sekolah = ProfilSekolahModel::getProfil($this->pdo);

            include '../app/views/surat_print.php';
        } else {
            echo "Surat tidak ditemukan.";
        }
    }
}
?>
