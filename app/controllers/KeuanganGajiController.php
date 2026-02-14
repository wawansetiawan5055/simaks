<?php
// app/controllers/KeuanganGajiController.php

require_once '../app/models/KeuanganGajiModel.php';
require_once '../app/models/GuruModel.php';

class KeuanganGajiController {
    private $pdo;
    private $gajiModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->gajiModel = new KeuanganGajiModel($pdo);
    }

    public function config() {
        $pdo = $this->pdo;
        $this->gajiModel->initV4Tables(); // Ensure migration runs
        $config = $this->gajiModel->getV4Config();
        $ekskulList = $this->gajiModel->getMasterEkskul(); 
        $ekskulRates = $this->gajiModel->getV4EkskulRates();
        include '../app/views/keuangan_gaji_config.php';
    }

    public function save_config() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'tarif_jjm' => str_replace('.', '', $_POST['tarif_jjm'] ?? 0),
                'tarif_transport' => str_replace('.', '', $_POST['tarif_transport'] ?? 0),
                'tarif_kinerja' => str_replace('.', '', $_POST['tarif_kinerja'] ?? 0),
                'tunj_kepsek' => str_replace('.', '', $_POST['tunj_kepsek'] ?? 0),
                'tunj_tas' => str_replace('.', '', $_POST['tunj_tas'] ?? 0),
                'tunj_plk' => str_replace('.', '', $_POST['tunj_plk'] ?? 0),
                'tunj_penjaga' => str_replace('.', '', $_POST['tunj_penjaga'] ?? 0),
                'tunj_satpam' => str_replace('.', '', $_POST['tunj_satpam'] ?? 0),
                'tunj_sopir' => str_replace('.', '', $_POST['tunj_sopir'] ?? 0),
                'tunj_waka_kurikulum' => str_replace('.', '', $_POST['tunj_waka_kurikulum'] ?? 0),
                'tunj_waka_kesiswaan' => str_replace('.', '', $_POST['tunj_waka_kesiswaan'] ?? 0),
                'tunj_sarpras' => str_replace('.', '', $_POST['tunj_sarpras'] ?? 0),
                'tunj_waka_humas' => str_replace('.', '', $_POST['tunj_waka_humas'] ?? 0),
                'tunj_kepala_lab' => str_replace('.', '', $_POST['tunj_kepala_lab'] ?? 0),
                'tunj_kepala_perpus' => str_replace('.', '', $_POST['tunj_kepala_perpus'] ?? 0),
                'tunj_operator' => str_replace('.', '', $_POST['tunj_operator'] ?? 0),
                'tunj_pembina_keagamaan' => str_replace('.', '', $_POST['tunj_pembina_keagamaan'] ?? 0),
                'tunj_pengelola_smater' => str_replace('.', '', $_POST['tunj_pengelola_smater'] ?? 0),
                'tarif_ekskul_global' => str_replace('.', '', $_POST['tarif_ekskul_global'] ?? 0),
                'tunj_walas' => str_replace('.', '', $_POST['tunj_walas'] ?? 0),
            ];
            $this->gajiModel->saveV4Config($data);

            // Save Specific Ekskul Rates
            if (isset($_POST['ekskul_rates'])) {
                $rates = [];
                foreach($_POST['ekskul_rates'] as $id => $val) {
                    $rates[$id] = str_replace('.', '', $val);
                }
                $this->gajiModel->saveV4EkskulRates($rates);
            }

            header('Location: index.php?mod=keuangan_gaji&act=setting&status=config_saved');
        }
    }
    
    public function index() {
        $pdo = $this->pdo;
        $this->gajiModel->initV4Tables(); // Ensure migration
        // Default View: List of Payroll Periods
        $list_gaji = $this->gajiModel->getAllPeriods();
        include '../app/views/keuangan_gaji_index.php';
    }
    
    public function setting() {
        $pdo = $this->pdo;
        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
        
        // View: Setting Tariffs Matrix
        // Fetch all teachers with their existing rules
        $matrix = $this->gajiModel->getAllRulesWithTeachers();
        
        // Load Global Config for Modal
        $config = $this->gajiModel->getV4Config();
        $ekskulList = $this->gajiModel->getMasterEkskul(); 
        $ekskulRates = $this->gajiModel->getV4EkskulRates();
        $assignments = $this->gajiModel->getAssignments($id_ta);
        
        include '../app/views/keuangan_gaji_setting.php';
    }
    
    public function save_setting() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rules = $_POST['rules'] ?? [];
            
            $count = 0;
            foreach ($rules as $id_guru => $data) {
                // Formatting: remove dots from currency inputs
                $cleanData = [
                    'tarif_jjm' => str_replace('.', '', $data['tarif_jjm'] ?? 0),
                    'tarif_transport' => str_replace('.', '', $data['tarif_transport'] ?? 0),
                    'tarif_kinerja' => str_replace('.', '', $data['tarif_kinerja'] ?? 0),
                    'tunj_ekskul' => str_replace('.', '', $data['tunj_ekskul'] ?? 0),

                    'tunj_kepsek' => str_replace('.', '', $data['tunj_kepsek'] ?? 0),
                    'tunj_tas' => str_replace('.', '', $data['tunj_tas'] ?? 0),
                    'tunj_plk' => str_replace('.', '', $data['tunj_plk'] ?? 0),
                    'tunj_penjaga' => str_replace('.', '', $data['tunj_penjaga'] ?? 0),
                    'tunj_satpam' => str_replace('.', '', $data['tunj_satpam'] ?? 0),
                    'tunj_sopir' => str_replace('.', '', $data['tunj_sopir'] ?? 0),

                    'tunj_kurikulum' => str_replace('.', '', $data['tunj_kurikulum'] ?? 0),
                    'tunj_kesiswaan' => str_replace('.', '', $data['tunj_kesiswaan'] ?? 0),
                    'tunj_sarpras' => str_replace('.', '', $data['tunj_sarpras'] ?? 0),
                    'tunj_humas' => str_replace('.', '', $data['tunj_humas'] ?? 0),
                    'tunj_kepala_lab' => str_replace('.', '', $data['tunj_kepala_lab'] ?? 0),
                    'tunj_kepala_perpus' => str_replace('.', '', $data['tunj_kepala_perpus'] ?? 0),
                    'tunj_operator' => str_replace('.', '', $data['tunj_operator'] ?? 0),
                    'tunj_pembina_keagamaan' => str_replace('.', '', $data['tunj_pembina_keagamaan'] ?? 0),
                    'tunj_pengelola_smater' => str_replace('.', '', $data['tunj_pengelola_smater'] ?? 0),
                    'tunj_walas' => str_replace('.', '', $data['tunj_walas'] ?? 0),
                    
                    'tunj_pembina' => str_replace('.', '', $data['tunj_pembina'] ?? 0),
                    'tunjangan_lain' => str_replace('.', '', $data['tunjangan_lain'] ?? 0),

                    'potongan_bpjs_kes' => str_replace('.', '', $data['potongan_bpjs_kes'] ?? 0),
                    'potongan_bpjs_tk' => str_replace('.', '', $data['potongan_bpjs_tk'] ?? 0),
                    'potongan_kasbon' => str_replace('.', '', $data['potongan_kasbon'] ?? 0),
                    'potongan_lain' => str_replace('.', '', $data['potongan_lain'] ?? 0),
                ];
                
                $this->gajiModel->saveRules($id_guru, $cleanData);
                $count++;
            }
            
            header('Location: index.php?mod=keuangan_gaji&act=setting&status=success&count='.$count);
        }
    }
    
    public function create() {
        $pdo = $this->pdo;
        // View: Create New Payroll Period
        $teachers = GuruModel::getAll($this->pdo); 
        include '../app/views/keuangan_gaji_create.php';
    }
    
    public function generate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bulan = $_POST['bulan'];
            $tahun = $_POST['tahun'];
            $id_ta = $_SESSION['id_ta_aktif'] ?? 1;
            
            $res = $this->gajiModel->generateGaji($bulan, $tahun, $id_ta);
            
            if (isset($res['status']) && $res['status'] === 'error') {
                 echo "<script>alert('".$res['message']."'); window.history.back();</script>";
                 return;
            }
            
            // If ID returned directly (old way) or via array (new way)
            $id_gaji = is_array($res) ? ($this->pdo->lastInsertId()) : $res; 
            // Better: generateGaji returns ID on success? 
            // In my new Model code, it returns ['status'=>'success', 'count'=>...] OR ID?
            // Wait, looking at Model: "return ['status' => 'success', 'count' => count($gurus)];"
            // AND it generates Insert ID inside but doesn't return it in array?
            // Ah, I need to fetch the ID of the created payroll.
            // Or Update Model to return ID.
            
            // Let's query the ID based on Month/Year since we just created it.
            $stmt = $this->pdo->prepare("SELECT id_gaji FROM keuangan_gaji WHERE bulan=? AND tahun=?");
            $stmt->execute([$bulan, $tahun]);
            $id_gaji = $stmt->fetchColumn();
            
            header('Location: index.php?mod=keuangan_gaji&act=detail&id=' . $id_gaji);
        }
    }
    
    public function detail() {
        $pdo = $this->pdo;
        $id = $_GET['id'];
        $gaji = $this->gajiModel->getPeriodById($id);
        $details = $this->gajiModel->getDetails($id);
        include '../app/views/keuangan_gaji_detail.php';
    }

    public function recalculate() {
        $id = $_GET['id'];
        $res = $this->gajiModel->regenerateGaji($id);
        
        if (isset($res['status']) && $res['status'] === 'error') {
            echo "<script>alert('".$res['message']."'); window.history.back();</script>";
            return;
        }

        header('Location: index.php?mod=keuangan_gaji&act=detail&id=' . $id . '&status=regenerated');
    }

    public function finalize() {
        $id = $_GET['id'];
        $this->gajiModel->updateStatus($id, 'FINAL');
        header('Location: index.php?mod=keuangan_gaji&act=detail&id=' . $id . '&status=finalized');
    }
    
    public function delete() {
        $id = $_GET['id'];
        $this->gajiModel->deletePeriod($id);
        header('Location: index.php?mod=keuangan_gaji');
    }
    
    public function print_slip() {
        $pdo = $this->pdo;
        $id_detail = $_GET['id_detail'];
        $data = $this->gajiModel->getSlipDetail($id_detail);
        include '../app/views/keuangan_gaji_print_slip.php';
    }

    public function print_rekap() {
        $pdo = $this->pdo;
        $id_gaji = $_GET['id'];
        $gaji = $this->gajiModel->getPeriodById($id_gaji);
        $details = $this->gajiModel->getDetails($id_gaji);
        include '../app/views/keuangan_gaji_print_rekap.php';
    }
}
