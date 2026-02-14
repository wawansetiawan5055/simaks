<?php
/**
 * KeuanganController
 * Konversi ke Functional Style agar sesuai standar SIMAKS
 */


require_once '../app/models/KeuanganKategoriModel.php';
require_once '../app/models/KeuanganJenisModel.php';
require_once '../app/models/KeuanganRekeningModel.php';
require_once '../app/models/KeuanganTransaksiModel.php';
require_once '../app/models/KelasModel.php';
require_once '../app/models/TahunAjaranModel.php';
require_once '../app/models/PenempatanModel.php';
require_once '../app/models/KeuanganTagihanModel.php';
require_once '../app/models/KeuanganTarifModel.php';
require_once '../app/helpers/DateHelper.php';

// =========================================================================================
// DASHBOARD KEUANGAN
// =========================================================================================
function keuangan_dashboard($pdo)
{
    // if (!check_access('keuangan_dashboard', 'index')) redirect('index.php'); // Optional check

    $rekeningModel = new KeuanganRekeningModel($pdo);
    $transaksiModel = new KeuanganTransaksiModel($pdo);

    // Quick stats
    $total_saldo = $rekeningModel->getTotalSaldo();

    // Pendapatan & pengeluaran bulan ini
    $bulan_ini_awal = date('Y-m-01');
    $bulan_ini_akhir = date('Y-m-t');

    $pendapatan_bulan_ini = $transaksiModel->getTotalByTipe('MASUK', $bulan_ini_awal, $bulan_ini_akhir);
    $pengeluaran_bulan_ini = $transaksiModel->getTotalByTipe('KELUAR', $bulan_ini_awal, $bulan_ini_akhir);

    // Transaksi terbaru
    $transaksi_terbaru = $transaksiModel->getRecent(10);

    // Saldo per rekening
    $rekening_list = $rekeningModel->getActive();

    // Tunggakan
    $tagihanModel = new KeuanganTagihanModel($pdo);
    $total_tunggakan = $tagihanModel->getTotalTunggakan();

    // Load view
    $data = [
        'total_saldo' => $total_saldo,
        'pendapatan_bulan_ini' => $pendapatan_bulan_ini,
        'pengeluaran_bulan_ini' => $pengeluaran_bulan_ini,
        'transaksi_terbaru' => $transaksi_terbaru,
        'rekening_list' => $rekening_list,
        'total_tunggakan' => $total_tunggakan,
        'api_url' => 'index.php',
        'current_ta_id' => $_SESSION['id_ta_aktif'] ?? 0
    ];

    include '../app/views/keuangan_dashboard.php';
}

/**
 * Sync / Recalculate Balance
 */
function keuangan_sync_saldo($pdo)
{
    try {
        $rekeningModel = new KeuanganRekeningModel($pdo);
        $rekeningModel->recalculateBalances();

        $_SESSION['pesan_sukses'] = "Saldo rekening berhasil disinkronisasi dengan transaksi.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal sinkronisasi saldo: " . $e->getMessage();
    }
    header('Location: index.php?mod=keuangan_dashboard');
    exit;
}

// =========================================================================================
// TRANSAKSI MASUK (PENDAPATAN)
// =========================================================================================
function keuangan_masuk_index($pdo)
{
    // if (!check_access('keuangan_transaksi_masuk', 'index')) redirect('index.php');

    $transaksiModel = new KeuanganTransaksiModel($pdo);
    $jenisModel = new KeuanganJenisModel($pdo);
    $rekeningModel = new KeuanganRekeningModel($pdo);

    $bulan = (!empty($_GET['bulan'])) ? $_GET['bulan'] : date('m');
    $tahun = (!empty($_GET['tahun'])) ? $_GET['tahun'] : date('Y');

    $filters = [
        'tipe' => 'MASUK',
        'bulan' => $bulan,
        'tahun' => $tahun
    ];

    $transaksi = $transaksiModel->getAll($filters);
    $jenis_list = $jenisModel->getByTipe('MASUK');
    $rekening_list = $rekeningModel->getActive();
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    $kelas_list = KelasModel::all($pdo, $id_ta_aktif); // Fetch Class List with TA filter

    // Variables required by footer.php
    $api_url = 'index.php';
    $current_ta_id = $_SESSION['id_ta_aktif'] ?? 0;

    include '../app/views/keuangan_masuk_index.php';
}

function keuangan_masuk_save($pdo)
{
    try {
        $transaksiModel = new KeuanganTransaksiModel($pdo);
        $tagihanModel = new KeuanganTagihanModel($pdo);
        $tarifModel = new KeuanganTarifModel($pdo);

        $id_siswa = $_POST['id_siswa'] ?? null;
        $id_jenis = $_POST['id_jenis'] ?? null;

        // Determine periods to process
        $periods = [];
        $is_recurring = false;

        if ($id_jenis) {
            $stmtJenis = $pdo->prepare("SELECT is_recurring, harga_default FROM keuangan_jenis WHERE id_jenis = ?");
            $stmtJenis->execute([$id_jenis]);
            $jenis = $stmtJenis->fetch(PDO::FETCH_ASSOC);
            $is_recurring = ($jenis && $jenis['is_recurring'] == 1);
        }

        if ($is_recurring && !empty($_POST['bulan_awal']) && !empty($_POST['tahun_awal'])) {
            $m_start = (int) $_POST['bulan_awal'];
            $y_start = (int) $_POST['tahun_awal'];
            $m_end = (int) ($_POST['bulan_akhir'] ?: $m_start);
            $y_end = (int) ($_POST['tahun_akhir'] ?: $y_start);

            $currDate = new DateTime("$y_start-$m_start-01");
            $endDate = new DateTime("$y_end-$m_end-01");

            if ($endDate < $currDate)
                $endDate = clone $currDate;

            $safety = 0;
            while ($currDate <= $endDate && $safety < 36) {
                $periods[] = $currDate->format('Y-m');
                $currDate->modify('+1 month');
                $safety++;
            }
        } elseif (!empty($_POST['bulan']) || !empty($_POST['tahun'])) {
            // Backward compatibility / Single selection
            $m = str_pad($_POST['bulan'] ?? date('m'), 2, '0', STR_PAD_LEFT);
            $y = $_POST['tahun'] ?? date('Y');
            $periods[] = "$y-$m";
        } else {
            // Non-recurring or generic
            $periods[] = null;
        }

        $msg = 'Pemasukan berhasil disimpan';
        $ids = [];

        // 2. Fetch Active TA for JIT billing
        $stmtTA = $pdo->prepare("SELECT id_ta FROM tahun_ajaran WHERE status = 'Aktif' LIMIT 1");
        $stmtTA->execute();
        $id_ta_aktif = $stmtTA->fetchColumn() ?: 0;
        if ($id_ta_aktif === 0) {
            throw new Exception("Tahun ajaran aktif tidak ditemukan. Silakan aktifkan tahun ajaran di master data.");
        }

        $process_log = [];
        foreach ($periods as $index => $periode) {
            $log = ['periode' => $periode];
            $id_tagihan = null;
            $currentNominal = 0;

            if ($id_siswa && $id_jenis) {
                if ($is_recurring && $periode) {
                    $stmt = $pdo->prepare("SELECT id_tagihan, sisa_tagihan FROM keuangan_tagihan_siswa 
                                          WHERE id_siswa = ? AND id_jenis = ? AND periode = ?
                                          ORDER BY tahun_ajaran DESC, id_tagihan DESC LIMIT 1");
                    $stmt->execute([$id_siswa, $id_jenis, $periode]);
                } else {
                    $stmt = $pdo->prepare("SELECT id_tagihan, sisa_tagihan FROM keuangan_tagihan_siswa 
                                          WHERE id_siswa = ? AND id_jenis = ?
                                          ORDER BY tahun_ajaran DESC, id_tagihan DESC LIMIT 1");
                    $stmt->execute([$id_siswa, $id_jenis]);
                }

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $id_tagihan = $row['id_tagihan'];
                    $currentNominal = (float) $row['sisa_tagihan'];

                    // RECONCILIATION: Fix bills that are FREE but should be PAID, or vice-versa
                    if ($is_recurring && $periode) {
                        $rule = $tarifModel->getTarifForStudent($id_siswa, $id_jenis);
                        $config = ($rule && !empty($rule['keterangan'])) ? json_decode($rule['keterangan'], true) : [];
                        $activeMonths = array_map('intval', (isset($config['months']) ? $config['months'] : (isset($config['active_months']) ? $config['active_months'] : [])));
                        $m_current = (int) substr($periode, 5, 2);

                        $isFreeInMatrix = (!empty($activeMonths) && !in_array($m_current, $activeMonths));

                        if ($isFreeInMatrix && $currentNominal > 0) {
                            // Should be FREE but is currently PAID! Zero it out.
                            $pdo->prepare("UPDATE keuangan_tagihan_siswa SET jumlah_tagihan = 0, sisa_tagihan = 0, status = 'LUNAS', keterangan = CONCAT(keterangan, ' (Reconciled to Free via Matrix)') WHERE id_tagihan = ?")
                                ->execute([$id_tagihan]);
                            $currentNominal = 0;
                            $log['reconcile'] = 'fixed_to_free';
                        } elseif (!$isFreeInMatrix && $currentNominal == 0) {
                            // Should be PAID but is currently FREE (0)! Repair it.
                            $targetNominal = ($rule && isset($rule['nominal'])) ? (float) $rule['nominal'] : (float) $jenis['harga_default'];
                            if ($targetNominal > 0) {
                                $pdo->prepare("UPDATE keuangan_tagihan_siswa SET jumlah_tagihan = ?, sisa_tagihan = ?, status = 'BELUM_BAYAR', keterangan = CONCAT(keterangan, ' (Repaired from 0 via Multi-Pay)') WHERE id_tagihan = ?")
                                    ->execute([$targetNominal, $targetNominal, $id_tagihan]);
                                $currentNominal = $targetNominal;
                                $log['reconcile'] = 'repaired_to_paid';
                            }
                        }
                    }
                } elseif ($is_recurring) {
                    // JIT Creation for Recurring
                    $rule = $tarifModel->getTarifForStudent($id_siswa, $id_jenis);
                    $ruleNominal = ($rule && isset($rule['nominal'])) ? (float) $rule['nominal'] : (float) $jenis['harga_default'];

                    // Check for FREE MONTH in matrix
                    if ($rule && !empty($rule['keterangan'])) {
                        $config = json_decode($rule['keterangan'], true);
                        $m_current = (int) substr($periode, 5, 2);
                        $activeMonths = array_map('intval', (isset($config['months']) ? $config['months'] : (isset($config['active_months']) ? $config['active_months'] : [])));

                        if (!empty($activeMonths) && !in_array($m_current, $activeMonths)) {
                            $ruleNominal = 0; // Free month
                        }
                    }

                    $tagihanData = [
                        'id_siswa' => $id_siswa,
                        'id_jenis' => $id_jenis,
                        'tahun_ajaran' => $id_ta_aktif,
                        'periode' => $periode,
                        'tanggal_jatuh_tempo' => date('Y-m-d'),
                        'jumlah_tagihan' => $ruleNominal,
                        'keterangan' => 'Generated Otomatis (Multi-Payment Input)'
                    ];

                    if ($tagihanModel->create($tagihanData)) {
                        $id_tagihan = $pdo->lastInsertId();
                        $currentNominal = (float) $ruleNominal;
                    }
                }
            }

            // Calculate split amount
            // If it's the only period, use the whole amount. 
            // If multi-period, we ideally want to split 'jumlah' across them.
            // But since the frontend calculates 'jumlah' as the SUM of correctly priced months,
            // the backend should use the specific month's price if it's Lunas.

            $inputJumlahTotal = (float) str_replace('.', '', $_POST['jumlah']);
            $payAmount = $inputJumlahTotal;

            if (count($periods) > 1) {
                // For multi-month, we use the JIT/Bill nominal for each specific month
                $payAmount = $currentNominal;
            }

            $userKeterangan = $_POST['keterangan'] ?? '';
            $finalKeterangan = $userKeterangan;
            if ($periode) {
                $finalKeterangan .= (empty($userKeterangan) ? "" : " - ") . "Periode " . $periode;
            }

            $data = [
                'tanggal' => $_POST['tanggal'],
                'tipe' => 'MASUK',
                'id_jenis' => $id_jenis,
                'id_rekening' => $_POST['id_rekening'],
                'id_siswa' => $id_siswa,
                'id_tagihan' => $id_tagihan,
                'jumlah' => $payAmount,
                'metode_pembayaran' => $_POST['metode_pembayaran'] ?? 'TUNAI',
                'referensi' => $_POST['referensi'] ?? null,
                'keterangan' => $finalKeterangan,
                'id_pengguna' => $_SESSION['user_id'] ?? 1
            ];

            // Record if amount > 0 OR if it's a recurring item with a valid bill (free month record)
            if ($payAmount > 0 || ($is_recurring && !empty($id_tagihan))) {
                if ($index === 0 && !empty($_POST['id_transaksi'])) {
                    $transaksiModel->update($_POST['id_transaksi'], $data);
                    $ids[] = $_POST['id_transaksi'];
                    if (isset($log))
                        $log['action'] = 'updated';
                } else {
                    $newId = $transaksiModel->create($data);
                    if ($newId) {
                        $ids[] = $newId;
                        if (isset($log))
                            $log['action'] = 'created';
                    }
                }
            }
            if (isset($log)) {
                $log['payAmount'] = $payAmount;
                $process_log[] = $log;
            }
        }

        $savedCount = count($ids);
        if ($savedCount > 0) {
            $msg = "Berhasil menyimpan $savedCount transaksi periode: " . implode(', ', $periods);
        } else {
            $msg = "Tidak ada transaksi yang disimpan. Pastikan nominal > 0 atau sesuaikan Matrix.";
        }

        echo json_encode([
            'success' => true,
            'message' => $msg,
            'ids' => $ids,
            'debug' => [
                'periods' => $periods,
                'process_log' => $process_log ?? []
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// =========================================================================================
// TRANSAKSI KELUAR (PENGELUARAN)
// =========================================================================================
function keuangan_keluar_index($pdo)
{
    // if (!check_access('keuangan_transaksi_keluar', 'index')) redirect('index.php');

    $transaksiModel = new KeuanganTransaksiModel($pdo);
    $jenisModel = new KeuanganJenisModel($pdo);
    $rekeningModel = new KeuanganRekeningModel($pdo);

    $filters = [
        'tipe' => 'KELUAR',
        'tanggal_dari' => $_GET['tanggal_dari'] ?? date('Y-m-01'),
        'tanggal_sampai' => $_GET['tanggal_sampai'] ?? date('Y-m-t')
    ];

    $transaksi = $transaksiModel->getAll($filters);
    $jenis_list = $jenisModel->getByTipe('KELUAR');
    $rekening_list = $rekeningModel->getActive();

    // Variables required by footer.php
    $api_url = 'index.php';
    $current_ta_id = $_SESSION['id_ta_aktif'] ?? 0;

    include '../app/views/keuangan_keluar_index.php';
}

function keuangan_keluar_save($pdo)
{
    try {
        $transaksiModel = new KeuanganTransaksiModel($pdo);

        $data = [
            'tanggal' => $_POST['tanggal'],
            'tipe' => 'KELUAR',
            'id_jenis' => $_POST['id_jenis'] ?? $_POST['jenis_id'],
            'id_rekening' => $_POST['id_rekening'] ?? $_POST['rekening_id'],
            'jumlah' => str_replace('.', '', $_POST['jumlah']),
            'referensi' => $_POST['referensi'] ?? null,
            'keterangan' => $_POST['keterangan'] ?? null,
            'id_pengguna' => $_SESSION['user_id'] ?? 1
        ];

        if (!empty($_POST['id_transaksi'])) {
            $transaksiModel->update($_POST['id_transaksi'], $data);
            $msg = 'Pengeluaran berhasil diperbarui';
            $id = $_POST['id_transaksi'];
        } else {
            $id = $transaksiModel->create($data);
            $msg = 'Pengeluaran berhasil disimpan';
        }

        echo json_encode([
            'success' => true,
            'message' => $msg,
            'id' => $id
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// =========================================================================================
// JURNAL UMUM & MEMORIAL
// =========================================================================================

function keuangan_laporan_pembayaran($pdo)
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?mod=login');
        exit;
    }

    $filters = [
        'id_kelas' => $_GET['id_kelas'] ?? '',
        'id_jenis' => $_GET['id_jenis'] ?? '',
        'id_siswa' => $_GET['id_siswa'] ?? '',
        'id_ta' => $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0
    ];

    $jenisModel = new KeuanganJenisModel($pdo);
    $tagihanModel = new KeuanganTagihanModel($pdo);

    $kelasList = KelasModel::all($pdo, $filters['id_ta']);
    $jenisList = $jenisModel->getAll();
    $siswaList = [];
    if (!empty($filters['id_kelas'])) {
        $siswaList = PenempatanModel::getAssignedStudents($pdo, $filters['id_kelas'], $filters['id_ta']);
    }

    $reportData = [];
    $siswaHeader = null;

    if (!empty($filters['id_siswa'])) {
        // SIMULASI 3: PER SISWA
        $reportData = $tagihanModel->getIndividualHistory($filters['id_siswa']);
        $siswaHeader = SiswaModel::find($pdo, $filters['id_siswa']);
    } elseif (!empty($filters['id_jenis'])) {
        // SIMULASI 1: PER KATEGORI
        $reportData = $tagihanModel->getReportArrears($filters);
    } elseif (!empty($filters['id_kelas'])) {
        // SIMULASI 2: MATRIX
        $bills = $tagihanModel->getReportArrears(['id_kelas' => $filters['id_kelas'], 'id_ta' => $filters['id_ta']]);

        $matrix = [];
        $categories = [];

        // Pivot Logic
        foreach ($bills as $b) {
            $catKey = $b['nama_jenis'] . ($b['periode'] ? ' (' . $b['periode'] . ')' : '');
            if (!in_array($catKey, $categories))
                $categories[] = $catKey;

            if (!isset($matrix[$b['id_siswa']])) {
                $matrix[$b['id_siswa']] = [
                    'nama' => $b['nama'],
                    'data' => []
                ];
            }

            $matrix[$b['id_siswa']]['data'][$catKey] = [
                'pay' => $b['jumlah_tagihan'] - $b['sisa_tagihan'],
                'sisa' => $b['sisa_tagihan']
            ];
        }

        // Fetch students who might not have bills (optional, but good for "empty" rows)
        foreach ($siswaList as $s) {
            if (!isset($matrix[$s['id_siswa']])) {
                $matrix[$s['id_siswa']] = ['nama' => $s['nama'], 'data' => []];
            }
        }

        uasort($matrix, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']); });
        sort($categories); // Sort columns by name (SPP, then etc)

        $reportData = ['matrix' => $matrix, 'categories' => $categories];
    }

    include '../app/views/keuangan_laporan_pembayaran.php';
}

// =========================================================================================
// JURNAL PEMBANTU PENERIMAAN KAS
// =========================================================================================
function keuangan_jurnal_pembantu($pdo)
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?mod=login');
        exit;
    }

    // Auto-detect Active ID TA if not provided (though filter removed, we need it for logic)
    $stmt = $pdo->prepare("SELECT * FROM tahun_ajaran WHERE status = 'Aktif' LIMIT 1");
    $stmt->execute();
    $activeTA = $stmt->fetch(PDO::FETCH_ASSOC);
    $selected_id_ta = $activeTA['id_ta'] ?? 0;

    $filters = [
        'id_kelas' => $_GET['id_kelas'] ?? '',
        'id_jenis' => $_GET['id_jenis'] ?? '',
        'tipe_rekap' => $_GET['tipe_rekap'] ?? 'bulanan', // Default to monthly
        'bulan' => $_GET['bulan'] ?? date('m'),
        'id_ta' => $selected_id_ta
    ];

    $tagihanModel = new KeuanganTagihanModel($pdo);
    $jenisModel = new KeuanganJenisModel($pdo);

    $kelasList = KelasModel::all($pdo, $filters['id_ta']);
    // Filter Jenis List: Hanya tampilkan yang ada di Matrix (Tarif) untuk TA aktif
    // Filter Jenis List: Hanya tampilkan yang ada di Matrix (Tarif) untuk TA aktif dan Tipe MASUK
    // Menggunakan getByTipe agar konsisten dengan menu Pemasukan
    $allJenis = $jenisModel->getByTipe('MASUK'); 
    
    $jenisList = [];
    $activeJenisIds = [];

    if (!empty($filters['id_kelas'])) {
        // Strict Filter: 
        // 1. Tarif yang diset langsung ke KELAS ini
        // 2. Tarif yang diset ke SISWA yang ada di kelas ini (Penempatan Aktif)
        $stmtActive = $pdo->prepare("
            SELECT DISTINCT t.id_jenis 
            FROM keuangan_tarif t 
            LEFT JOIN penempatan_siswa ps ON t.id_siswa = ps.id_siswa 
            WHERE t.id_kelas = ? 
               OR (ps.id_kelas = ? AND ps.status_penempatan = 'Aktif')
        ");
        $stmtActive->execute([$filters['id_kelas'], $filters['id_kelas']]);
        $activeJenisIds = $stmtActive->fetchAll(PDO::FETCH_COLUMN);
    } else {
        // General Filter fallback (TA basis)
        $stmtActive = $pdo->prepare("
            SELECT DISTINCT t.id_jenis 
            FROM keuangan_tarif t 
            JOIN kelas k ON t.id_kelas = k.id_kelas 
            WHERE k.id_ta = ?
        ");
        $stmtActive->execute([$filters['id_ta']]);
        $activeJenisIds = $stmtActive->fetchAll(PDO::FETCH_COLUMN);
    }

    // Filter the full list
    if (!empty($activeJenisIds)) {
        foreach ($allJenis as $j) {
            if (in_array($j['id_jenis'], $activeJenisIds)) {
                $jenisList[] = $j;
            }
        }
    } else {
        // Fallback: If no strict match found (e.g. no matrix set yet), show All MASUK types
        // This prevents empty dropdowns for new setups
        $jenisList = $allJenis;
    }

    // Get Jenis Info
    $jenisInfo = null;
    $reportData = [];
    $reportType = 'list';
    $periods = [];
    $kelasInfo = null;
    $taInfo = $activeTA; // Force use active TA info

    if (!empty($filters['id_jenis'])) {
        $stmt = $pdo->prepare("SELECT * FROM keuangan_jenis WHERE id_jenis = ?");
        $stmt->execute([$filters['id_jenis']]);
        $jenisInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!empty($filters['id_kelas'])) {
        $kelasInfo = KelasModel::find($pdo, $filters['id_kelas']);
    }

    // Calculate Periods dynamically based on Active TP dates
    if ($taInfo && !empty($taInfo['tanggal_mulai'])) {
        $startDate = new DateTime($taInfo['tanggal_mulai']);
        // Force Start Date to be 1st of July of the start year (Standard Academic Year)
        $startYear = $startDate->format('Y');
        $startDate->setDate($startYear, 7, 1);

        if ($filters['tipe_rekap'] == 'tahunan') {
            // Full Academic Year (12 Months: Jul - Jun)
            $current = clone $startDate;
            for ($i = 0; $i < 12; $i++) {
                $periods[] = $current->format('Y-m');
                $current->modify('+1 month');
            }
        } elseif ($filters['tipe_rekap'] == 'semester1') {
            // Semester 1 (6 Months: Jul - Dec)
            $current = clone $startDate;
            for ($i = 0; $i < 6; $i++) {
                $periods[] = $current->format('Y-m');
                $current->modify('+1 month');
            }
        } elseif ($filters['tipe_rekap'] == 'semester2') {
            // Semester 2 (6 Months: Jan - Jun next year)
            $current = clone $startDate;
            $current->modify('+6 months'); // Jump to January
            for ($i = 0; $i < 6; $i++) {
                $periods[] = $current->format('Y-m');
                $current->modify('+1 month');
            }
        } elseif ($filters['tipe_rekap'] == 'bulanan') {
            // Single month logic
            $selectedMonth = str_pad($filters['bulan'], 2, '0', STR_PAD_LEFT);
            // Determine year: Jul-Dec = startYear, Jan-Jun = startYear + 1
            if (intval($selectedMonth) >= 7) {
                $year = $startYear;
            } else {
                $year = $startYear + 1;
            }
            $periods = [$year . '-' . $selectedMonth];
        }
    }

    // Fetch Report Data
    if (!empty($filters['id_kelas']) && !empty($filters['id_jenis']) && $jenisInfo && !empty($periods)) {
        if ($jenisInfo['is_recurring'] == 1) {
            $reportType = 'matrix';
            $reportData = $tagihanModel->getMonthlyMatrix(
                $filters['id_kelas'],
                $filters['id_jenis'],
                $filters['id_ta'],
                $periods
            );
        } else {
            $reportType = 'list';
            $reportData = $tagihanModel->getListReport(
                $filters['id_kelas'],
                $filters['id_jenis'],
                $filters['id_ta']
            );
        }
    }

    include '../app/views/keuangan_jurnal_pembantu_index.php';
}

function keuangan_jurnal($pdo)
{
    // Model manual include (if not autoloaded)
    require_once '../app/models/KeuanganJurnalModel.php';

    $jurnalModel = new KeuanganJurnalModel($pdo);

    // Filter Params
    $tanggal_dari = $_GET['tanggal_dari'] ?? date('Y-m-01');
    $tanggal_sampai = $_GET['tanggal_sampai'] ?? date('Y-m-t');

    // Fetch Data
    $jurnal = $jurnalModel->getAll($tanggal_dari, $tanggal_sampai);

    // Fetch Accounts for Input Memorial Options (Unified COA & Rekening)
    $kategoriModel = new KeuanganKategoriModel($pdo);
    $rekeningModel = new KeuanganRekeningModel($pdo);
    $jenisModel = new KeuanganJenisModel($pdo); // Added missing model instantiation

    $jenis_list = $jenisModel->getAll();
    $rekening_list = $rekeningModel->getAll();

    include '../app/views/keuangan_jurnal_index.php';
}

function keuangan_memorial_save($pdo)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        die('Invalid Method');

    require_once '../app/models/KeuanganJurnalModel.php';
    $jurnalModel = new KeuanganJurnalModel($pdo);

    try {
        // Validate Balance
        $totalDebit = 0;
        $totalKredit = 0;

        $details = [];
        // Loop through posted details (assuming array inputs like akun[], tipe[], jumlah[])
        if (isset($_POST['akun']) && is_array($_POST['akun'])) {
            foreach ($_POST['akun'] as $i => $kode_akun) {
                if (empty($kode_akun))
                    continue;

                // Parse Kode Akun (It might be "1101|Kas Tunai")
                $parts = explode('|', $kode_akun);
                $kode = $parts[0];
                $nama = $parts[1] ?? 'Akun';

                $tipe = $_POST['tipe'][$i];
                $jumlah = str_replace('.', '', $_POST['jumlah'][$i]);

                if ($jumlah > 0) {
                    $details[] = [
                        'kode_akun' => $kode,
                        'nama_akun' => $nama,
                        'tipe' => $tipe,
                        'jumlah' => $jumlah
                    ];

                    if ($tipe == 'DEBIT')
                        $totalDebit += $jumlah;
                    else
                        $totalKredit += $jumlah;
                }
            }
        }

        if ($totalDebit != $totalKredit) {
            throw new Exception("Jurnal Tidak Balance! Debit: " . number_format($totalDebit) . ", Kredit: " . number_format($totalKredit));
        }

        if (empty($details)) {
            throw new Exception("Belum ada akun yang diiputkan.");
        }

        // Header Data
        $dataHeader = [
            'no_bukti' => $_POST['no_bukti'], // e.g., MEM/202401/001
            'tanggal' => $_POST['tanggal'],
            'keterangan' => $_POST['keterangan']
        ];

        $id = $jurnalModel->createMemorial($dataHeader, $details);

        echo json_encode(['success' => true, 'message' => 'Jurnal Memorial Berhasil Disimpan', 'id' => $id]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}


// =========================================================================================
// MASTER DATA
// =========================================================================================



// NEW: Unified COA Master
function keuangan_master_coa($pdo)
{
    $kategoriModel = new KeuanganKategoriModel($pdo);
    $coa = $kategoriModel->getAllRecursive();
    include '../app/views/keuangan_master_coa.php';
}

function keuangan_kategori_save($pdo)
{
    try {
        $model = new KeuanganKategoriModel($pdo);
        $data = [
            'tipe' => $_POST['tipe'],
            'kode_kategori' => $_POST['kode_kategori'],
            'kode_akun' => $_POST['kode_akun'],
            'nama_kategori' => $_POST['nama_kategori'],
            'keterangan' => $_POST['keterangan'] ?? null
        ];

        if (!empty($_POST['id_kategori'])) {
            $model->update($_POST['id_kategori'], $data);
            $msg = 'Kategori berhasil diperbarui';
        } else {
            $model->create($data);
            $msg = 'Kategori berhasil ditambahkan';
        }

        // Redirect back
        header('Location: index.php?mod=keuangan_master&act=coa');
        exit;

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

function keuangan_master_kategori($pdo)
{
    $kategoriModel = new KeuanganKategoriModel($pdo);

    $kategori = $kategoriModel->getAllWithJenisCount();
    include '../app/views/keuangan_master_kategori.php';
}

function keuangan_master_jenis($pdo)
{
    $jenisModel = new KeuanganJenisModel($pdo);
    $kategoriModel = new KeuanganKategoriModel($pdo);

    $jenis = $jenisModel->getAll();
    $kategori = $kategoriModel->getAll();

    include '../app/views/keuangan_master_jenis.php';
}

function keuangan_jenis_save($pdo)
{
    try {
        $jenisModel = new KeuanganJenisModel($pdo);

        $data = [
            'id_kategori' => $_POST['id_kategori'] ?? $_POST['kategori_id'],
            'kode_jenis' => $_POST['kode_jenis'],
            'kode_akun' => $_POST['kode_akun'],
            'nama_jenis' => $_POST['nama_jenis'],
            'harga_default' => str_replace('.', '', $_POST['harga_default']),
            'is_recurring' => (isset($_POST['is_recurring']) && $_POST['is_recurring'] == 1) ? 1 : 0,
            'recurring_period' => $_POST['recurring_period'] ?? null,
            'keterangan' => $_POST['keterangan'] ?? null,
            'is_active' => $_POST['is_active'] ?? 1
        ];

        if (!empty($_POST['id'])) {
            // Update
            $success = $jenisModel->update($_POST['id'], $data);
            $message = 'Jenis pembayaran berhasil diperbarui';
        } else {
            // Create
            $success = $jenisModel->create($data);
            $message = 'Jenis pembayaran berhasil ditambahkan';
        }

        echo json_encode(['success' => $success, 'message' => $message]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}


function keuangan_master_rekening($pdo)
{
    $rekeningModel = new KeuanganRekeningModel($pdo);
    $rekening = $rekeningModel->getAll();
    include '../app/views/keuangan_master_rekening.php';
}

// =========================================================================================
// AJAX HELPERS
// =========================================================================================
function keuangan_get_tagihan_siswa($pdo)
{
    header('Content-Type: application/json');
    $id_siswa = $_GET['id_siswa'] ?? 0;
    if (!$id_siswa) {
        echo json_encode(['success' => false, 'data' => []]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT t.id_tagihan, t.periode, t.sisa_tagihan, j.nama_jenis 
                          FROM keuangan_tagihan_siswa t
                          JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                          WHERE t.id_siswa = ? AND t.status != 'LUNAS' 
                          ORDER BY t.periode ASC");
    $stmt->execute([$id_siswa]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

// =========================================================================================
// BUKU KAS UMUM (BKU)
// =========================================================================================
function keuangan_bku($pdo)
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?mod=login');
        exit;
    }

    $transaksiModel = new KeuanganTransaksiModel($pdo);
    $rekeningModel = new KeuanganRekeningModel($pdo);

    // Filters
    $startDate = $_GET['tanggal_dari'] ?? date('Y-m-01');
    $endDate = $_GET['tanggal_sampai'] ?? date('Y-m-t');
    $id_rekening = $_GET['id_rekening'] ?? '';

    // Fetch Data
    $bkuData = $transaksiModel->getBKU($startDate, $endDate, $id_rekening);
    $rekeningList = $rekeningModel->getAll();

    $saldoAwal = $bkuData['saldo_awal'];
    $transaksi = $bkuData['transaksi'];

    // Calculate Summary
    $totalMasuk = 0;
    $totalKeluar = 0;
    foreach($transaksi as $t) {
        if ($t['tipe'] == 'MASUK')
            $totalMasuk += $t['jumlah'];
        else
            $totalKeluar += $t['jumlah'];
    }
    $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

    include '../app/views/keuangan_bku_index.php';
}

function keuangan_get_siswa_by_kelas($pdo)
{
    header('Content-Type: application/json');
    $id_kelas = $_GET['id_kelas'] ?? $_GET['kelas_id'] ?? null;

    if (!$id_kelas) {
        echo json_encode(['status' => 'error', 'message' => 'ID Kelas tidak ditemukan']);
        return;
    }

    // STRATEGY 1: Priority - Use Session Active TA (used by Absensi modules)
    $id_ta = $_SESSION['id_ta_aktif'] ?? null;
    $method_used = "Session";

    // STRATEGY 2: If session empty, use Database Active Flag
    if (!$id_ta) {
        $ta_aktif = TahunAjaranModel::aktif($pdo);
        if ($ta_aktif) {
            $id_ta = $ta_aktif['id_ta'];
            $method_used = "DB Active Flag";
        }
    }

    $siswa = [];
    $ta_name = "Unknown";

    // Attempt Fetch with identified TA
    if ($id_ta) {
        try {
            // Get TA Name for UI
            $stmt_name = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
            $stmt_name->execute([$id_ta]);
            $ta_name = $stmt_name->fetchColumn();

            $siswa = PenempatanModel::getAssignedStudents($pdo, $id_kelas, $id_ta, true);
        } catch (Exception $e) { /* Ignore error to try fallback */
        }
    }

    // STRATEGY 3: FALLBACK - If no students found, find LATEST TA that has students for this class
    if (empty($siswa)) {
        $stmt_fallback = $pdo->prepare("SELECT DISTINCT id_ta FROM penempatan_siswa WHERE id_kelas = ? ORDER BY id_ta DESC LIMIT 1");
        $stmt_fallback->execute([$id_kelas]);
        $fallback_ta = $stmt_fallback->fetchColumn();

        if ($fallback_ta) {
            $id_ta = $fallback_ta;
            $siswa = PenempatanModel::getAssignedStudents($pdo, $id_kelas, $id_ta, true);

            // Get Name
            $stmt_name = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
            $stmt_name->execute([$id_ta]);
            $ta_name = $stmt_name->fetchColumn() . " (Auto-Detected)";
            $method_used = "Fallback: Latest Class Data";
        }
    }

    // STRATEGY 4: LAST RESORT - Get ALL students ever in this class (Ignore TA)
    if (empty($siswa)) {
        $stmt_last = $pdo->prepare("SELECT DISTINCT s.id_siswa, s.nama, s.nisn 
                                     FROM siswa s 
                                     JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                                     WHERE ps.id_kelas = ? AND s.status_aktif = 'Aktif'
                                     ORDER BY s.nama ASC");
        $stmt_last->execute([$id_kelas]);
        $siswa = $stmt_last->fetchAll(PDO::FETCH_ASSOC);
        $ta_name = "Semua Periode";
        $method_used = "Last Resort: Ignore TA";
    }

    echo json_encode([
        'status' => 'ok',
        'data' => $siswa,
        'ta_nama' => $ta_name,
        'debug_method' => $method_used,
        'debug_id_ta' => $id_ta
    ]);
}

function keuangan_get_siswa_matrix($pdo)
{
    header('Content-Type: application/json');
    $id_siswa = $_GET['id_siswa'] ?? null;
    if (!$id_siswa) {
        echo json_encode(['status' => 'error', 'message' => 'ID Siswa tidak ditemukan']);
        return;
    }

    $tarifModel = new KeuanganTarifModel($pdo);
    $data = $tarifModel->getTariffsForStudentMatrix($id_siswa);

    echo json_encode([
        'status' => 'ok',
        'data' => $data
    ]);
}

function keuangan_masuk_print($pdo)
{
    $id_param = $_GET['id'] ?? null;
    if (!$id_param)
        die("ID Transaksi tidak ditemukan");

    $ids = explode(',', $id_param);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Fetch Full Transaction Details
    $sql = "SELECT t.*, 
                   j.nama_jenis, j.kode_akun, j.is_recurring,
                   s.nama AS nama_siswa, s.nisn,
                   kl.nama_kelas,
                   tg.periode,
                   r.nama_rekening
            FROM keuangan_transaksi t
            LEFT JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
            LEFT JOIN siswa s ON t.id_siswa = s.id_siswa
            LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.status_penempatan = 'Aktif'
            LEFT JOIN kelas kl ON ps.id_kelas = kl.id_kelas
            LEFT JOIN keuangan_tagihan_siswa tg ON t.id_tagihan = tg.id_tagihan
            LEFT JOIN keuangan_rekening r ON t.id_rekening = r.id_rekening
            WHERE t.id_transaksi IN ($placeholders)
            ORDER BY t.created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows))
        die("Data transaksi tidak ditemukan");

    // Primary data (using first record for identity)
    $data = $rows[0];
    $id_siswa = $data['id_siswa'];

    // Fetch Total Arrears for this student (all unpaid bills across all academic years)
    $sisa_tunggakan = 0;
    if ($id_siswa) {
        $stmtSisa = $pdo->prepare("SELECT SUM(sisa_tagihan) FROM keuangan_tagihan_siswa WHERE id_siswa = ? AND status != 'LUNAS'");
        $stmtSisa->execute([$id_siswa]);
        $sisa_tunggakan = (float) ($stmtSisa->fetchColumn() ?: 0);
    }

    // Fetch School Profile for Kop
    require_once '../app/models/ProfilSekolahModel.php';
    $kop = ProfilSekolahModel::getProfil($pdo);

    include '../app/views/keuangan_masuk_print.php';
}
