<?php

class KeuanganTagihanController
{
    private $pdo;
    private $tagihanModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->tagihanModel = new KeuanganTagihanModel($pdo);
    }

    public function index()
    {
        $taAktif = TahunAjaranModel::aktif($this->pdo);

        $filters = [
            'id_kelas' => $_GET['id_kelas'] ?? '',
            'id_jenis' => $_GET['id_jenis'] ?? '',
            'periode' => $_GET['periode'] ?? '',
            'id_ta' => $_GET['id_ta'] ?? ($taAktif['id_ta'] ?? ''),
            'status' => $_GET['status'] ?? ''
        ];

        // Load data for filters
        $pdo = $this->pdo;
        $jenisModel = new KeuanganJenisModel($this->pdo);

        $kelasList = KelasModel::all($this->pdo, $filters['id_ta']);
        $jenisList = $jenisModel->getAll();

        $tagihan = $this->tagihanModel->getAll($filters);

        include '../app/views/keuangan_tagihan_index.php';
    }

    public function create()
    {
        // Prepare data for the generation form
        $pdo = $this->pdo; // Expose to view
        $taAktif = TahunAjaranModel::aktif($this->pdo); // Get active academic year
        $id_ta_aktif = $taAktif ? $taAktif['id_ta'] : 0;
        $kelasList = KelasModel::all($this->pdo, $id_ta_aktif);

        require_once '../app/models/KeuanganJenisModel.php';
        $jenisModel = new KeuanganJenisModel($this->pdo);
        $jenisList = $jenisModel->getAll();

        include '../app/views/keuangan_tagihan_create.php';
    }

    public function store()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            // Fetch TA Active ID
            require_once '../app/models/TahunAjaranModel.php';
            $taAktif = TahunAjaranModel::aktif($this->pdo);
            $id_ta_aktif = $taAktif ? $taAktif['id_ta'] : 0;

            $id_kelas = $_POST['id_kelas'];
            $id_jenis = $_POST['id_jenis'];
            $bulan_awal = $_POST['bulan_awal']; // e.g. "2024-07"
            $bulan_akhir = $_POST['bulan_akhir'];
            $jatuh_tempo_day = $_POST['tanggal_jatuh_tempo_day'] ?? '10';

            // 0. Fetch Item Metadata (Recurring status)
            $stmtJenis = $this->pdo->prepare("SELECT price.*, cat.tipe 
                                             FROM keuangan_jenis price 
                                             JOIN keuangan_kategori cat ON price.id_kategori = cat.id_kategori 
                                             WHERE price.id_jenis = ?");
            $stmtJenis->execute([$id_jenis]);
            $jenisMeta = $stmtJenis->fetch(PDO::FETCH_ASSOC);

            if (!$jenisMeta)
                throw new Exception("Jenis pembayaran tidak valid.");

            $is_recurring = (int) ($jenisMeta['is_recurring'] ?? 1);
            $default_price = (float) $jenisMeta['harga_default'];

            // Prepare Models
            require_once '../app/models/KeuanganTarifModel.php';
            $tarifModel = new KeuanganTarifModel($this->pdo);
            require_once '../app/models/PenempatanModel.php';

            // 1. Get List of Students
            if (!empty($_POST['id_siswa_specific'])) {
                $student_ids = $_POST['id_siswa_specific'];
                $students = [];
                foreach ($student_ids as $sid) {
                    $students[] = ['id_siswa' => $sid];
                }
            } else {
                $students = PenempatanModel::getAssignedStudents($this->pdo, $id_kelas, $id_ta_aktif);
            }

            if (empty($students)) {
                throw new Exception("Tidak ada siswa ditemukan.");
            }

            // 2. Pre-fetch Data
            $studentIds = array_column($students, 'id_siswa');

            // CROSS-YEAR CHECK for duplicate prevention
            // We always check across all years for recurring items to prevent overlapping periods
            $checkTa = null;
            $existingMap = $this->tagihanModel->getExistingMap($id_jenis, $checkTa);
            $tariffMap = $tarifModel->getTariffsBulk($id_kelas, $studentIds);

            // 3. Generate Period Range
            if ($is_recurring) {
                $start = new DateTime($bulan_awal . '-01');
                $end = new DateTime($bulan_akhir . '-01');
                $interval = new DateInterval('P1M');
                $period_range = new DatePeriod($start, $interval, $end->modify('+1 day'));
            } else {
                // Non-recurring: only ONE period (based on month_start)
                $period_range = [new DateTime($bulan_awal . '-01')];
            }

            $rowsToInsert = [];
            $count_skipped_existing = 0;
            $count_skipped_matrix = 0;

            foreach ($students as $siswa) {
                $sid = $siswa['id_siswa'];
                $myRule = $tariffMap[$sid][$id_jenis] ?? null;

                foreach ($period_range as $dt) {
                    $periode = $dt->format('Y-m');

                    // Duplicate Check (Logic differs based on $existingMap scope)
                    // If non-recurring, existingMap contains bills from ALL years.
                    if (isset($existingMap[$sid][$periode]) || (!$is_recurring && !empty($existingMap[$sid]))) {
                        $count_skipped_existing++;
                        continue;
                    }

                    $nominal = $default_price;
                    if (is_array($myRule)) {
                        $meta = json_decode($myRule['keterangan'] ?? '', true);
                        $activeMonths = is_array($meta) ? ($meta['months'] ?? null) : null;
                        $mIdx = (int) $dt->format('n');

                        // If it's recurring, check month exclusion. 
                        // If non-recurring, we usually ignore month exclusion unless explicitly desired.
                        if ($is_recurring && $activeMonths !== null && !in_array($mIdx, $activeMonths)) {
                            $count_skipped_matrix++;
                            continue;
                        }
                        $nominal = (float) $myRule['nominal'];
                    }

                    $jatuh_tempo = $periode . '-' . str_pad($jatuh_tempo_day, 2, '0', STR_PAD_LEFT);

                    $rowsToInsert[] = [
                        'id_siswa' => $sid,
                        'id_jenis' => $id_jenis,
                        'tahun_ajaran' => $id_ta_aktif,
                        'periode' => $periode,
                        'tanggal_jatuh_tempo' => $jatuh_tempo,
                        'jumlah_tagihan' => $nominal,
                        'keterangan' => "Auto-Gen Ledger (" . ($is_recurring ? "Rutin" : "Sekali Bayar") . ")"
                    ];
                }
            }

            // 4. Execute Batch Insert
            if (!empty($rowsToInsert)) {
                $chunks = array_chunk($rowsToInsert, 100);
                foreach ($chunks as $chunk) {
                    $this->tagihanModel->createBatch($chunk);
                }
            }

            $count_success = count($rowsToInsert);
            $msg = "Proses Selesai. " .
                "Dibuat: $count_success. " .
                "Sudah Ada: $count_skipped_existing. " .
                ($count_skipped_matrix > 0 ? "Dikecualikan Matriks: $count_skipped_matrix." : "");

            echo json_encode([
                'success' => true,
                'message' => $msg,
                'details' => [
                    'success' => $count_success,
                    'skipped_existing' => $count_skipped_existing,
                    'skipped_matrix' => $count_skipped_matrix
                ]
            ]);

        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
