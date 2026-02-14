<!-- Widget: Data Lulusan & Study Tracer -->
<div class="row mt-3">
    <div class="col-md-6">
        <!-- Tabel Data Lulusan -->
        <div class="card">
            <div class="card-header bg-gradient-success">
                <h3 class="card-title"><i class="fas fa-user-graduate"></i> Data Lulusan & Study Tracer (5 Tahun Terakhir)</h3>
                <div class="card-tools">
                    <a href="index.php?mod=tracer_study" class="btn btn-sm btn-light">
                        <i class="fas fa-cog"></i> Kelola
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-sm table-hover" id="table_tracer_dashboard">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="2" class="align-middle text-center" style="width: 40px">No</th>
                            <th rowspan="2" class="align-middle text-center">Tahun Ajaran</th>
                            <th colspan="3" class="text-center bg-info">Jumlah Lulusan</th>
                            <th colspan="4" class="text-center bg-warning">Status Setelah Lulus</th>
                        </tr>
                        <tr>
                            <th class="text-center bg-info-light">L</th>
                            <th class="text-center bg-info-light">P</th>
                            <th class="text-center bg-info-light">Total</th>
                            <th class="text-center bg-warning-light">A</th>
                            <th class="text-center bg-warning-light">B</th>
                            <th class="text-center bg-warning-light">C</th>
                            <th class="text-center bg-warning-light">D</th>
                        </tr>
                    </thead>
                    <tbody id="tracer_table_body">
                        <tr>
                            <td colspan="9" class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-3 bg-light">
                    <small class="text-muted">
                        <strong>Keterangan:</strong><br>
                        <span class="badge badge-success">A</span> = PTN/PTS (Perguruan Tinggi) | 
                        <span class="badge badge-warning">B</span> = Bekerja | 
                        <span class="badge badge-info">C</span> = Wirausaha | 
                        <span class="badge badge-secondary">D</span> = Lain-lain
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Grafik Visualisasi -->
        <div class="card">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Visualisasi Data Lulusan</h3>
            </div>
            <div class="card-body">
                <!-- Bar Chart: Trend Lulusan per Tahun (L/P) -->
                <div class="mb-4">
                    <h6 class="text-center mb-3">Trend Jumlah Lulusan (5 Tahun Terakhir)</h6>
                    <canvas id="chartLulusanTrend" height="120"></canvas>
                </div>
                
                <hr>
                
                <!-- Stacked Bar Chart: Distribusi Status -->
                <div>
                    <h6 class="text-center mb-3">Distribusi Status Alumni</h6>
                    <canvas id="chartStatusDistribusi" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================================
// TRACER STUDY DASHBOARD WIDGET
// ============================================================

let chartLulusanTrend = null;
let chartStatusDistribusi = null;

// Load Tracer Study Statistics
function loadTracerStatistics() {
    fetch('../api/api.php?mod=dashboard&act=get_tracer_statistics')
        .then(response => response.json())
        .then(result => {
            if (result.status === 'ok' && result.data) {
                renderTracerTable(result.data);
                renderTracerCharts(result.data);
            } else {
                document.getElementById('tracer_table_body').innerHTML = 
                    '<tr><td colspan="9" class="text-center text-danger">Gagal memuat data tracer</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading tracer statistics:', error);
            document.getElementById('tracer_table_body').innerHTML = 
                '<tr><td colspan="9" class="text-center text-danger">Error: ' + error.message + '</td></tr>';
        });
}

// Render Tracer Table
function renderTracerTable(data) {
    const tbody = document.getElementById('tracer_table_body');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Belum ada data lulusan</td></tr>';
        return;
    }
    
    let html = '';
    data.forEach((row, index) => {
        const total = parseInt(row.total_lulus) || 0;
        const laki = parseInt(row.laki_laki) || 0;
        const perempuan = parseInt(row.perempuan) || 0;
        const ptn_pts = parseInt(row.ptn_pts) || 0;
        const bekerja = parseInt(row.bekerja) || 0;
        const wirausaha = parseInt(row.wirausaha) || 0;
        const lain_lain = parseInt(row.lain_lain) || 0;
        
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td class="text-center font-weight-bold">${row.tahun_lulus || '-'}</td>
                <td class="text-center">${laki}</td>
                <td class="text-center">${perempuan}</td>
                <td class="text-center font-weight-bold">${total}</td>
                <td class="text-center ${ptn_pts > 0 ? 'bg-success-light' : ''}">${ptn_pts}</td>
                <td class="text-center ${bekerja > 0 ? 'bg-warning-light' : ''}">${bekerja}</td>
                <td class="text-center ${wirausaha > 0 ? 'bg-info-light' : ''}">${wirausaha}</td>
                <td class="text-center ${lain_lain > 0 ? 'bg-secondary-light' : ''}">${lain_lain}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Render Tracer Charts
function renderTracerCharts(data) {
    if (!data || data.length === 0) return;
    
    // Prepare data for charts
    const labels = data.map(row => row.tahun_lulus).reverse();
    const dataLaki = data.map(row => parseInt(row.laki_laki) || 0).reverse();
    const dataPerempuan = data.map(row => parseInt(row.perempuan) || 0).reverse();
    const dataPtnPts = data.map(row => parseInt(row.ptn_pts) || 0).reverse();
    const dataBekerja = data.map(row => parseInt(row.bekerja) || 0).reverse();
    const dataWirausaha = data.map(row => parseInt(row.wirausaha) || 0).reverse();
    const dataLainLain = data.map(row => parseInt(row.lain_lain) || 0).reverse();
    
    // Chart 1: Trend Lulusan (Bar Chart - L/P)
    const ctxTrend = document.getElementById('chartLulusanTrend').getContext('2d');
    
    if (chartLulusanTrend) {
        chartLulusanTrend.destroy();
    }
    
    chartLulusanTrend = new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Laki-laki',
                    data: dataLaki,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Perempuan',
                    data: dataPerempuan,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' siswa';
                        }
                    }
                }
            }
        }
    });
    
    // Chart 2: Distribusi Status (Stacked Bar Chart)
    const ctxStatus = document.getElementById('chartStatusDistribusi').getContext('2d');
    
    if (chartStatusDistribusi) {
        chartStatusDistribusi.destroy();
    }
    
    chartStatusDistribusi = new Chart(ctxStatus, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'PTN/PTS',
                    data: dataPtnPts,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Bekerja',
                    data: dataBekerja,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Wirausaha',
                    data: dataWirausaha,
                    backgroundColor: 'rgba(23, 162, 184, 0.8)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Lain-lain',
                    data: dataLainLain,
                    backgroundColor: 'rgba(108, 117, 125, 0.8)',
                    borderColor: 'rgba(108, 117, 125, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' alumni';
                        }
                    }
                }
            }
        }
    });
}

// Load data on page load
$(document).ready(function() {
    loadTracerStatistics();
});
</script>

<style>
/* Custom styling for tracer widget */
.bg-info-light {
    background-color: #d1ecf1 !important;
}
.bg-warning-light {
    background-color: #fff3cd !important;
}
.bg-success-light {
    background-color: #d4edda !important;
}
.bg-secondary-light {
    background-color: #e2e3e5 !important;
}
</style>
