<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran PPDB - SIMAKS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Toast Notification CSS -->
    <link rel="stylesheet" href="assets/css/notification.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #C41E3A;
            --secondary: #2D8A4E;
            --accent: #FFD700;
            --dark: #1a1a2e;
            --light: #f8f9fa;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }
        
        .header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            opacity: 0.95;
        }
        
        .form-container {
            padding: 3rem 2rem;
        }
        
        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .progress-bar::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 60px;
            right: 60px;
            height: 3px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .progress-step {
            text-align: center;
            flex: 1;
            position: relative;
            z-index: 1;
        }
        
        .progress-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            margin: 0 auto 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .progress-step.active .progress-circle {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }
        
        .progress-step.completed .progress-circle {
            background: var(--secondary);
            color: white;
        }
        
        .progress-step.completed .progress-circle i {
            display: block;
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.3rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--secondary);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-group label.required::after {
            content: ' *';
            color: var(--primary);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.1);
        }
        
        .file-upload {
            border: 2px dashed #e0e0e0;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-upload:hover {
            border-color: var(--primary);
            background: rgba(196, 30, 58, 0.05);
        }
        
        .file-upload input {
            display: none;
        }
        
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .file-upload-label i {
            font-size: 2rem;
            color: var(--primary);
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #f0f0f0;
        }
        
        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(196, 30, 58, 0.3);
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .alert-info {
            background: #e3f2fd;
            color: #1976d2;
            border-left: 4px solid #1976d2;
        }
        
        .alert-warning {
            background: #fff3e0;
            color: #f57c00;
            border-left: 4px solid #f57c00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Form Pendaftaran PPDB</h1>
            <p>Tahun Ajaran <?= $data['config']['ppdb']['year'] ?></p>
        </div>
        
        <div class="form-container">
            <!-- Progress Bar -->
            <div class="progress-bar">
                <div class="progress-step active" data-step="1">
                    <div class="progress-circle">1</div>
                    <span>Data Pribadi</span>
                </div>
                <div class="progress-step" data-step="2">
                    <div class="progress-circle">2</div>
                    <span>Data Orang Tua</span>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="progress-circle">3</div>
                    <span>Asal Sekolah</span>
                </div>
                <div class="progress-step" data-step="4">
                    <div class="progress-circle">4</div>
                    <span>Upload Dokumen</span>
                </div>
            </div>
            
            <form action="index.php?mod=landing&act=ppdb_save" method="POST" enctype="multipart/form-data" id="ppdbForm">
                <!-- STEP 1: Data Pribadi -->
                <div class="step-content active" data-step="1">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Lengkapi data pribadi calon siswa dengan benar. Tanda (*) wajib diisi.
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title">Data Pribadi Calon Siswa</h3>
                        
                        <div class="form-group">
                            <label class="required">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control" maxlength="16">
                            </div>
                            <div class="form-group">
                                <label>NISN</label>
                                <input type="text" name="nisn" class="form-control" maxlength="10">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="required">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="required">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="required">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">Pilih...</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="required">Agama</label>
                                <select name="agama" class="form-control" required>
                                    <option value="">Pilih...</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="required">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>RT</label>
                                <input type="text" name="rt" class="form-control" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>RW</label>
                                <input type="text" name="rw" class="form-control" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>Kelurahan</label>
                                <input type="text" name="kelurahan" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Kota</label>
                                <input type="text" name="kota" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Provinsi</label>
                                <input type="text" name="provinsi" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control" maxlength="10">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>No. HP Siswa</label>
                                <input type="tel" name="no_hp_siswa" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Email Siswa</label>
                                <input type="email" name="email_siswa" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- STEP 2: Data Orang Tua -->
                <div class="step-content" data-step="2">
                    <div class="form-section">
                        <h3 class="section-title">Data Ayah</h3>
                        <div class="form-group">
                            <label class="required">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="form-control">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Penghasilan/Bulan</label>
                                <select name="penghasilan_ayah" class="form-control">
                                    <option value="">Pilih...</option>
                                    <option value="< 1 Juta">< 1 Juta</option>
                                    <option value="1-2 Juta">1-2 Juta</option>
                                    <option value="2-5 Juta">2-5 Juta</option>
                                    <option value="> 5 Juta">> 5 Juta</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>No. HP Ayah</label>
                            <input type="tel" name="no_hp_ayah" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title">Data Ibu</h3>
                        <div class="form-group">
                            <label class="required">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="form-control">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Penghasilan/Bulan</label>
                                <select name="penghasilan_ibu" class="form-control">
                                    <option value="">Pilih...</option>
                                    <option value="< 1 Juta">< 1 Juta</option>
                                    <option value="1-2 Juta">1-2 Juta</option>
                                    <option value="2-5 Juta">2-5 Juta</option>
                                    <option value="> 5 Juta">> 5 Juta</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>No. HP Ibu</label>
                            <input type="tel" name="no_hp_ibu" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title">Data Wali (Opsional)</h3>
                        <div class="form-group">
                            <label>Nama Wali</label>
                            <input type="text" name="nama_wali" class="form-control">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Pekerjaan Wali</label>
                                <input type="text" name="pekerjaan_wali" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>No. HP Wali</label>
                                <input type="tel" name="no_hp_wali" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- STEP 3: Asal Sekolah -->
                <div class="step-content" data-step="3">
                    <div class="form-section">
                        <h3 class="section-title">Asal Sekolah</h3>
                        <div class="form-group">
                            <label class="required">Nama Sekolah Asal</label>
                            <input type="text" name="asal_sekolah" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Alamat Sekolah</label>
                            <textarea name="alamat_sekolah" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>NPSN Sekolah</label>
                            <input type="text" name="npsn_sekolah" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="required">Jalur Pendaftaran</label>
                            <select name="jalur_pendaftaran" class="form-control" required>
                                <option value="Zonasi">Zonasi</option>
                                <option value="Prestasi">Prestasi</option>
                                <option value="Afirmasi">Afirmasi</option>
                                <option value="Perpindahan">Perpindahan</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- STEP 4: Upload Dokumen -->
                <div class="step-content" data-step="4">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Upload dokumen dalam format JPG, PNG, atau PDF (Max 2MB)
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title">Upload Dokumen</h3>
                        
                        <div class="form-group">
                            <label class="required">Foto Siswa (3x4)</label>
                            <div class="file-upload">
                                <input type="file" name="foto_siswa" id="foto_siswa" accept="image/*" required>
                                <label for="foto_siswa" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Klik untuk upload foto siswa</span>
                                    <small>JPG, PNG (Max 2MB)</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="required">Foto Kartu Keluarga</label>
                            <div class="file-upload">
                                <input type="file" name="foto_kk" id="foto_kk" accept="image/*,application/pdf" required>
                                <label for="foto_kk" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Klik untuk upload KK</span>
                                    <small>JPG, PNG, PDF (Max 2MB)</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Foto Akta Kelahiran</label>
                            <div class="file-upload">
                                <input type="file" name="foto_akta" id="foto_akta" accept="image/*,application/pdf">
                                <label for="foto_akta" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Klik untuk upload Akta</span>
                                    <small>JPG, PNG, PDF (Max 2MB)</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Foto Ijazah/SKHUN</label>
                            <div class="file-upload">
                                <input type="file" name="foto_ijazah" id="foto_ijazah" accept="image/*,application/pdf">
                                <label for="foto_ijazah" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Klik untuk upload Ijazah</span>
                                    <small>JPG, PNG, PDF (Max 2MB)</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Foto Raport</label>
                            <div class="file-upload">
                                <input type="file" name="foto_raport" id="foto_raport" accept="image/*,application/pdf">
                                <label for="foto_raport" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Klik untuk upload Raport</span>
                                    <small>JPG, PNG, PDF (Max 2MB)</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Buttons -->
                <div class="button-group">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                        <i class="fas fa-check"></i> Kirim Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let currentStep = 1;
        const totalSteps = 4;
        
        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            document.querySelectorAll('.progress-step').forEach(progressStep => {
                progressStep.classList.remove('active');
                const stepNum = parseInt(progressStep.getAttribute('data-step'));
                if (stepNum < step) {
                    progressStep.classList.add('completed');
                } else {
                    progressStep.classList.remove('completed');
                }
            });
            
            document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');
            document.querySelector(`.progress-step[data-step="${step}"]`).classList.add('active');
            
            // Button visibility
            document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'block';
            document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'block';
            document.getElementById('submitBtn').style.display = step === totalSteps ? 'block' : 'none';
        }
        
        document.getElementById('nextBtn').addEventListener('click', () => {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });
        
        document.getElementById('prevBtn').addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
        
        // File upload preview
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const label = e.target.nextElementSibling.querySelector('span');
                    label.textContent = file.name;
                    label.style.color = 'var(--secondary)';
                }
            });
        });
        
        // Form validation
        document.getElementById('ppdbForm').addEventListener('submit', (e) => {
            // Additional validation can be added here
        });
    </script>
    <!-- Toast Notification System -->
    <script src="assets/js/notification.js"></script>
    <script>
    // Pass PHP session messages to JavaScript for toast notifications
    <?php
    $hasMessages = isset($_SESSION['pesan_sukses']) || isset($_SESSION['pesan_error']) 
        || isset($_SESSION['pesan_warning']) || isset($_SESSION['pesan_info']);
    
    if ($hasMessages):
    ?>
    window.phpSessionMessages = {
        success: <?= isset($_SESSION['pesan_sukses']) ? json_encode($_SESSION['pesan_sukses']) : 'null' ?>,
        error: <?= isset($_SESSION['pesan_error']) ? json_encode($_SESSION['pesan_error']) : 'null' ?>,
        warning: <?= isset($_SESSION['pesan_warning']) ? json_encode($_SESSION['pesan_warning']) : 'null' ?>,
        info: <?= isset($_SESSION['pesan_info']) ? json_encode($_SESSION['pesan_info']) : 'null' ?>
    };
    <?php
        // Clear session messages after passing to JavaScript
        unset($_SESSION['pesan_sukses']);
        unset($_SESSION['pesan_error']);
        unset($_SESSION['pesan_warning']);
        unset($_SESSION['pesan_info']);
    endif;
    ?>
    </script>
</body>
</html>
