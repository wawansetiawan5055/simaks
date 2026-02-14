<?php

require_once '../app/models/KeuanganTarifModel.php';
require_once '../app/models/KeuanganJenisModel.php';
require_once '../app/models/KelasModel.php';
// SiswaModel might be needed if we implement search, but for now we might use AJAX or simple list if small.

class KeuanganTarifController
{
    private $pdo;
    private $tarifModel;
    private $jenisModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->tarifModel = new KeuanganTarifModel($pdo);
        $this->jenisModel = new KeuanganJenisModel($pdo);
    }

    public function index()
    {
        $pdo = $this->pdo; // Expose to view
        $tarifs = $this->tarifModel->getAll();
        include '../app/views/keuangan_tarif_index.php';
    }

    public function create()
    {
        $pdo = $this->pdo; // Expose to view
        $jenisList = $this->jenisModel->getAll();
        $taAktif = TahunAjaranModel::aktif($this->pdo);
        $id_ta_aktif = $taAktif ? $taAktif['id_ta'] : 0;
        $kelasList = KelasModel::all($this->pdo, $id_ta_aktif); // Static method
        include '../app/views/keuangan_tarif_create.php';
    }

    public function store()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            // Input Validation
            if (empty($_POST['id_jenis']) || empty($_POST['nominal'])) {
                throw new Exception("Jenis Transaksi dan Nominal wajib diisi.");
            }

            // Validate Scope (Must be either Class or Student, or Both? Usually mutually exclusive for clarity, but logic handles priority)
            // Let's allow specific Student OR specific Class.

            $data = [
                'id_jenis' => $_POST['id_jenis'],
                'id_kelas' => $_POST['id_kelas'] ?: null,
                'id_siswa' => $_POST['id_siswa'] ?: null,
                'nominal' => $_POST['nominal'],
                'keterangan' => $_POST['keterangan']
            ];

            if ($this->tarifModel->create($data)) {
                // Success
                header('Location: index.php?mod=keuangan_tarif&act=index&status=success');
                exit;
            } else {
                throw new Exception("Gagal menyimpan data.");
            }

        } catch (Exception $e) {
            // Handle error (redirect with message or show view)
            $error = $e->getMessage();
            // Reload create view with error? Or simple alert
            $jenisList = $this->jenisModel->getAll();
            $taAktif = TahunAjaranModel::aktif($this->pdo);
            $id_ta_aktif = $taAktif ? $taAktif['id_ta'] : 0;
            $kelasList = KelasModel::all($this->pdo, $id_ta_aktif);
            include '../app/views/keuangan_tarif_create.php';
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($id && $this->tarifModel->delete($id)) {
            header('Location: index.php?mod=keuangan_tarif&act=index&status=deleted');
        } else {
            header('Location: index.php?mod=keuangan_tarif&act=index&status=error');
        }
        exit;
    }

    public function matrix()
    {
        $pdo = $this->pdo;

        // 1. Get Income Categories (Tipe = MASUK)
        $stmt = $this->pdo->prepare("SELECT * FROM keuangan_kategori WHERE tipe = 'MASUK' ORDER BY id_kategori ASC");
        $stmt->execute();
        $incomeCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Handle Filter Kategori (Default: All Income Categories)
        $allIncomeCatIds = array_column($incomeCategories, 'id_kategori');
        $selectedCatIds = $_GET['filter_kategori'] ?? $allIncomeCatIds;

        // Ensure selectedCatIds is array if single value
        if (!is_array($selectedCatIds))
            $selectedCatIds = [$selectedCatIds];

        // 3. Get Fee Types (Jenis) based on Selected Categories
        // We join to keuangan_kategori to ensure we only get MASUK types (redundant but safe)
        // AND match selected categories
        if (!empty($selectedCatIds)) {
            $placeholders = implode(',', array_fill(0, count($selectedCatIds), '?'));
            $sqlJenis = "SELECT j.*, k.nama_kategori 
                         FROM keuangan_jenis j 
                         JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                         WHERE k.tipe = 'MASUK' AND j.id_kategori IN ($placeholders)
                         ORDER BY j.id_kategori, j.id_jenis";
            $stmt = $this->pdo->prepare($sqlJenis);
            $stmt->execute($selectedCatIds);
            $jenisList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $jenisList = [];
        }

        // Load Data
        $taAktifForTA = TahunAjaranModel::aktif($this->pdo);
        $id_ta_for_kelas = $taAktifForTA['id_ta'] ?? 0;
        $kelasList = KelasModel::all($this->pdo, $id_ta_for_kelas);

        $id_kelas = $_GET['id_kelas'] ?? '';
        $students = [];
        $existingTarifs = [];

        if ($id_kelas) {
            // Get Selected Class Info for Display
            $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_kelas = ?");
            $stmt->execute([$id_kelas]);
            $selectedClassInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            // Load Students in Class
            require_once '../app/models/PenempatanModel.php';
            require_once '../app/models/TahunAjaranModel.php';

            $taAktif = TahunAjaranModel::aktif($this->pdo);
            $id_ta = $taAktif['id_ta'] ?? 0;

            $students = PenempatanModel::getAssignedStudents($this->pdo, $id_kelas, $id_ta, true);

            // Load Exisiting Tariffs
            // Fetch Keterangan too (for JSON metadata)
            $sql = "SELECT id_siswa, id_jenis, nominal, keterangan FROM keuangan_tarif WHERE id_siswa IN (
                SELECT id_siswa FROM penempatan_siswa WHERE id_kelas = ? AND status_penempatan = 'Aktif'
            )";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_kelas]);
            $rawTarifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rawTarifs as $rt) {
                // Parse JSON metadata from keterangan if present
                $meta = json_decode($rt['keterangan'] ?? '', true);
                $months = $meta['months'] ?? null; // If null, means ALL or N/A

                $existingTarifs[$rt['id_siswa']][$rt['id_jenis']] = [
                    'nominal' => $rt['nominal'],
                    'months' => $months,
                    'keterangan_text' => $meta['text'] ?? $rt['keterangan'] // Fallback
                ];
            }
        }

        include '../app/views/keuangan_tarif_matrix.php';
    }

    public function save_matrix()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            $id_kelas = $_POST['id_kelas'];
            $action = $_POST['action_type'] ?? 'save_rule'; // 'save_rule' or 'generate'
            $items = $_POST['items'] ?? []; // Fallback for traditional submission

            // Check for JSON submission (to bypass max_input_vars)
            if (!empty($_POST['matrix_data'])) {
                $decoded = json_decode($_POST['matrix_data'], true);
                if (is_array($decoded)) {
                    $items = $decoded;
                }
            }

            $count_rules = 0;
            $count_bills = 0;

            $this->pdo->beginTransaction();

            require_once '../app/models/KeuanganTagihanModel.php';
            $tagihanModel = new KeuanganTagihanModel($this->pdo);

            $taAktif = TahunAjaranModel::aktif($this->pdo); // Call static method with PDO
            $id_ta_aktif = $taAktif['id_ta'] ?? 0;

            foreach ($items as $sid => $jenisGroup) {
                foreach ($jenisGroup as $jid => $data) {
                    $isActive = !empty($data['active']);
                    $nominal = str_replace('.', '', $data['nominal']);
                    $months = $data['months'] ?? range(1, 12);

                    // 1. Handle Tariff Rules
                    if ($isActive) {
                        $meta = [
                            'months' => $months,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        $jsonKeterangan = json_encode($meta);

                        $this->pdo->prepare("DELETE FROM keuangan_tarif WHERE id_siswa=? AND id_jenis=?")->execute([$sid, $jid]);
                        $this->pdo->prepare("INSERT INTO keuangan_tarif (id_jenis, id_siswa, nominal, keterangan) VALUES (?, ?, ?, ?)")
                            ->execute([$jid, $sid, $nominal, $jsonKeterangan]);
                        $count_rules++;
                    } else {
                        // Unchecked: Remove Rule
                        $this->pdo->prepare("DELETE FROM keuangan_tarif WHERE id_siswa=? AND id_jenis=?")->execute([$sid, $jid]);
                    }
                }
            }

            $this->pdo->commit();

            $msg = "Berhasil memproses data. Aturan disimpan: $count_rules.";
            if ($action === 'generate')
                $msg .= " Tagihan dibuat: $count_bills.";

            $_SESSION['pesan_sukses'] = $msg;
            header('Location: index.php?mod=keuangan_tarif&act=matrix&id_kelas=' . $id_kelas);
            exit;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['pesan_error'] = "Terjadi kesalahan: " . $e->getMessage();
            header('Location: index.php?mod=keuangan_tarif&act=matrix&id_kelas=' . $id_kelas);
            exit;
        }
    }
}
