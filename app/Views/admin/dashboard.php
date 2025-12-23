<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>
<style>
    .chart-container {
        width: 100%;
        overflow-x: auto;
    }

    .chart-box {
        width: 2000px; 
    }
    
    .pie-small {
        max-width: 180px;   
        margin: 0 auto;     
    }

</style>

<div class="container-fluid">
    <?php $session = \Config\Services::session(); ?>
    <?php if ($session->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $session->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <h1 class="h3 mb-2 text-gray-800">Dashboard</h1>


    <div class="row">

        <!-- Total Kuota -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalAktif ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Terisi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pendaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPendaftar ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pemagang -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pemagang</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?= $totalPemagang ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Lulus -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Lulus</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalLulus ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <form method="get" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <select name="periode" class="form-control" onchange="this.form.submit()">
                    <option value="6" <?= ($_GET['periode'] ?? '') == '6' ? 'selected' : '' ?>>Periode 6 Bulan</option>
                    <option value="12" <?= ($_GET['periode'] ?? '') == '12' ? 'selected' : '' ?>>Periode 1 Tahun</option>
                    <option value="24" <?= ($_GET['periode'] ?? '') == '24' ? 'selected' : '' ?>>Periode 2 Tahun</option>
                </select>
            </div>
        </div>
    </form>

    <div class="row">

        <!-- CHART MASUK KELUAR -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Peserta Magang Masuk & Selesai per Bulan
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMasukKeluar" height="130"></canvas>
                </div>
            </div>
        </div>

        <!-- CHART AKTIF PER BULAN -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Peserta Magang Aktif per Bulan
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartAktifPerBulan" height="120"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Peserta Magang per Unit</h6>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <div class="chart-box">
                    <canvas id="histogramUnit" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Pie Kuota -->
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kuota Terisi</h6>
                </div>
                <div class="card-body">
                    <canvas id="pieKuota" class="pie-small"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Durasi -->
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Durasi Magang</h6>
                </div>
                <div class="card-body">
                    <canvas id="pieDurasi" class="pie-small"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Gender -->
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Gender</h6>
                </div>
                <div class="card-body">
                    <canvas id="pieGender" class="pie-small"></canvas>
                </div>
            </div>
        </div>

    </div>




    <!-- <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Jumlah Masuk & Keluar Pemagang per Bulan</h6>
        </div>
        <div class="card-body">
            <canvas id="chartMasukKeluar" height="100"></canvas>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Jumlah Magang Aktif per Bulan</h6>
        </div>
        <div class="card-body">
            <canvas id="chartAktifPerBulan"></canvas>
        </div>
    </div> -->



</div>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
const sbColors = {
    blue: '#4e73df',
    green: '#1cc88a',
    cyan: '#36b9cc',
    yellow: '#f6c23e',
    red: '#e74a3b',
    gray: '#d1d3e2'
};

// ---------- Pie Kuota ----------
new Chart(document.getElementById('pieKuota'), {
    type: 'pie',
    data: {
        labels: ['Terisi', 'Sisa Kuota'],
        datasets: [{
            data: [<?= $pieKuota['terisi'] ?>, <?= $pieKuota['sisa'] ?>],
            backgroundColor: [sbColors.blue, sbColors.gray],
            hoverBackgroundColor: [sbColors.blue, sbColors.gray]
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// ---------- Pie Durasi ----------
new Chart(document.getElementById('pieDurasi'), {
    type: 'pie',
    data: {
        labels: ['1', '2', '3', '4', '5', '6 Bulan'],
        datasets: [{
            data: [
                <?= $durasiCount[1] ?>,
                <?= $durasiCount[2] ?>,
                <?= $durasiCount[3] ?>,
                <?= $durasiCount[4] ?>,
                <?= $durasiCount[5] ?>,
                <?= $durasiCount[6] ?>
            ],
            backgroundColor: [
                sbColors.blue,
                sbColors.green,
                sbColors.cyan,
                sbColors.yellow,
                sbColors.red,
                sbColors.gray
            ]
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// ---------- Pie Gender ----------
new Chart(document.getElementById('pieGender'), {
    type: 'pie',
    data: {
        labels: ['Laki-Laki', 'Perempuan'],
        datasets: [{
            data: [<?= $genderCount['L'] ?>, <?= $genderCount['P'] ?>],
            backgroundColor: [sbColors.cyan, sbColors.red]
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>


<script>
const ctx = document.getElementById('histogramUnit');

const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chart['labels']) ?>,
        datasets: [
            // === PT ===
            {
                label: 'PT - Aktif',
                data: <?= json_encode($chart['aktif_pt']) ?>,
                backgroundColor: 'rgba(28, 200, 138, 0.8)',
                stack: 'PT'
            },
            {
                label: 'PT - Akan Magang',
                data: <?= json_encode($chart['akan_pt']) ?>,
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                stack: 'PT'
            },
            {
                label: 'PT - Proses Daftar',
                data: <?= json_encode($chart['proses_pt']) ?>,
                backgroundColor: 'rgba(246, 194, 62, 0.85)',
                stack: 'PT'
            },
            {
                label: 'PT - Belum Lulus',
                data: <?= json_encode($chart['belum_pt']) ?>,
                backgroundColor: 'rgba(231, 74, 59, 0.85)',
                stack: 'PT'
            },
            {
                label: 'Kuota PT',
                data: <?= json_encode($chart['kuota_pt']) ?>,
                type: 'line',
                borderColor: 'rgba(30,136,229,0.9)',
                borderWidth: 2,
                borderDash: [6, 6],   
                pointRadius: 0,
                tension: 0.3,
                fill: false,
                order: 10,           
            },

            // === SMK ===
            {
                label: 'SMK - Aktif',
                data: <?= json_encode($chart['aktif_smk']) ?>,
                backgroundColor: 'rgba(20,150,100,0.7)',
                stack: 'SMK',
                hidden: true
            },
            {
                label: 'SMK - Akan Magang',
                data: <?= json_encode($chart['akan_smk']) ?>,
                backgroundColor: 'rgba(60,85,180,0.7)',
                stack: 'SMK',
                hidden: true
            },
            {
                label: 'SMK - Proses Daftar',
                data: <?= json_encode($chart['proses_smk']) ?>,
                backgroundColor: 'rgba(200,160,40,0.8)',
                stack: 'SMK',
                hidden: true
            },
            {
                label: 'SMK - Belum Lulus',
                data: <?= json_encode($chart['belum_smk']) ?>,
                backgroundColor: 'rgba(200,60,60,0.8)',
                stack: 'SMK',
                hidden: true
            },
            {
                label: 'Kuota SMK',
                data: <?= json_encode($chart['kuota_smk']) ?>,
                type: 'line',
                borderColor: 'rgba(255,160,0,0.95)',
                borderWidth: 2,
                borderDash: [6, 6],
                pointRadius: 0,
                tension: 0.3,
                fill: false,
                order: 10,
                hidden: true       
            }
        ]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },

        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    generateLabels: (chart) => {
                        const labels = Chart.defaults.plugins.legend.labels.generateLabels(chart);

                        return labels.sort((a, b) => {
                            const textA = a.text;
                            const textB = b.text;

                            const isPT_A  = textA.startsWith('PT') || textA === 'Kuota PT';
                            const isPT_B  = textB.startsWith('PT') || textB === 'Kuota PT';

                            if (isPT_A && !isPT_B) return -1;
                            if (!isPT_A && isPT_B) return 1;

                            return a.datasetIndex - b.datasetIndex;
                        });
                    }
                }
            },
            tooltip: { enabled: true }
        },

        scales: {
            x: {
                stacked: true,
                ticks: {
                    font: { family: 'Nunito' },
                    maxRotation: 90,
                    minRotation: 90
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                ticks: { font: { family: 'Nunito' } }
            }
        }
    }
});
</script>



<!-- <script>
    const ctx = document.getElementById('histogramUnit');

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart['labels']) ?>,
            datasets: [
                {
                    label: 'Aktif',
                    data: <?= json_encode($chart['aktif_pt']) ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.7)'
                },
                {
                    label: 'Akan Magang',
                    data: <?= json_encode($chart['akan_pt']) ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.7)'
                },
                {
                    label: 'Proses Daftar',
                    data: <?= json_encode($chart['proses_pt']) ?>,
                    backgroundColor: 'rgba(246, 194, 62, 0.7)'
                },
                {
                    label: 'Belum Lulus',
                    data: <?= json_encode($chart['belum_pt']) ?>,
                    backgroundColor: 'rgba(231, 74, 59, 0.7)'
                }
                
                
            ]
        },
        // plugins: [ChartDataLabels],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                // datalabels: {
                //     anchor: 'center',
                //     align: 'top',
                //     font: {
                //         weight: 'bold',
                //         size: 11
                //     },
                //     color: '#6c757d'
                // },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    enabled: true
                }
            },
            scales: {
                x: {
                    stacked: true,
                    ticks: { font: { family: 'Nunito' },
                             maxRotation: 90,
                             minRotation: 90 }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: { font: { family: 'Nunito' } }
                }
            }
        }
    });
</script> -->


<script>
    const ctx2 = document.getElementById('chartMasukKeluar').getContext('2d');

    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartMasukKeluar['labels']) ?>,
            datasets: [
                {
                    label: 'Masuk',
                    data: <?= json_encode($chartMasukKeluar['masuk']) ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.6)',
                },
                {
                    label: 'Selesai',
                    data: <?= json_encode($chartMasukKeluar['keluar']) ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.6)',
                }
            ]
        },
        plugins: [ChartDataLabels],
        options: {
            responsive: true,
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    font: {
                        weight: 'bold',
                        size: 11
                    },
                    color: '#6c757d'
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: { beginAtZero: true,
                     grace: '10%' 
                 }
            },

        }
    });

</script>



<script>
    const ctx3 = document.getElementById('chartAktifPerBulan').getContext('2d');

    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartAktifPerBulan['labels']) ?>,
            datasets: [

                /* --- Total Aktif Per Bulan --- */
                {
                    label: 'Total Aktif',
                    data: <?= json_encode($chartAktifPerBulan['aktif_total']) ?>,
                    borderWidth: 3,
                    tension: 0.3,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    fill: true,
                    hidden: false
                },

                /* --- Aktif SMK --- */
                {
                    label: 'Aktif SMK',
                    data: <?= json_encode($chartAktifPerBulan['aktif_smk']) ?>,
                    borderWidth: 3,
                    tension: 0.3,
                    borderColor:  'rgba(255, 99, 132, 1)'  ,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: false,
                    hidden: true
                },

                /* --- Aktif Perguruan Tinggi --- */
                {
                    label: 'Aktif Perguruan Tinggi',
                    data: <?= json_encode($chartAktifPerBulan['aktif_pt']) ?>,
                    borderWidth: 3,
                    tension: 0.3,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: false,
                    hidden: true
                },

                /* --- Kuota TOTAL (garis datar) --- */
                {
                    label: 'Total Kuota',
                    data: Array(<?= count($chartAktifPerBulan['labels']) ?>).fill(<?= $chartAktifPerBulan['kuota_total'] ?>),
                    borderDash: [5,5],
                    borderWidth: 2,
                    tension: 0,
                    borderColor: 'rgba(255, 205, 86, 1)',
                    fill: false,
                    hidden: false
                },

                /* --- Kuota SMK --- */
                {
                    label: 'Kuota SMK',
                    data: Array(<?= count($chartAktifPerBulan['labels']) ?>).fill(<?= $chartAktifPerBulan['kuota_smk'] ?>),
                    borderDash: [5,5],
                    borderWidth: 2,
                    tension: 0,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    fill: false,
                    hidden: true
                },

                /* --- Kuota Perguruan Tinggi --- */
                {
                    label: 'Kuota PT',
                    data: Array(<?= count($chartAktifPerBulan['labels']) ?>).fill(<?= $chartAktifPerBulan['kuota_pt'] ?>),
                    borderDash: [5,5],
                    borderWidth: 2,
                    tension: 0,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false,
                    hidden: true
                },
            ]
        },

        plugins: [ChartDataLabels],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    font: { weight: 'bold', size: 11 },
                    color: '#6c757d'
                },
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true, grace: '10%' }
            }
        }
    });
</script>

<!-- <script>
    const ctx3 = document.getElementById('chartAktifPerBulan').getContext('2d');

    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartAktifPerBulan['labels']) ?>,
            datasets: [{
                label: 'Aktif',
                data: <?= json_encode($chartAktifPerBulan['aktif_total']) ?>,
                borderWidth: 3,
                tension: 0.3,
                borderColor: 'rgba(255, 159, 64, 0.9)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                fill: true,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    font: {
                        weight: 'bold',
                        size: 11
                    },
                    color: '#6c757d'
                },
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true,
                    grace: '10%' 
                 }
            }
        }
    });
</script> -->



<?= $this->endSection(); ?>
