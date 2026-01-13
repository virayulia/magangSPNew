<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Access Log</h1>
    <p class="mb-4">Statistik login harian Aplikasi.</p>

    <div class="row mb-3">
        <div class="col-md-3">
            <label>Bulan</label>
            <select id="filterBulan" class="form-control">
                <?php for ($b = 1; $b <= 12; $b++): ?>
                    <option value="<?= $b ?>" <?= $b == date('n') ? 'selected' : '' ?>>
                        <?= nama_bulan_indonesia($b) ?>
                    </option>
                <?php endfor; ?>
            </select>

        </div>

        <div class="col-md-3">
            <label>Tahun</label>
            <select id="filterTahun" class="form-control">
                <?php for ($t = date('Y'); $t >= date('Y') - 2; $t--): ?>
                    <option value="<?= $t ?>" <?= $t == date('Y') ? 'selected' : '' ?>>
                        <?= $t ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-chart-bar"></i> Login Harian 
            </h6>
        </div>
        <div class="p-4" style="height: 400px;">
            <canvas id="loginChart"></canvas>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('loginChart').getContext('2d');

// 1️⃣ Chart pertama kali (dari index)
let loginChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels:  <?= json_encode($labels); ?>,
        datasets: [
            {
                label: 'Internal',
                data: <?= json_encode($internal); ?>,
                backgroundColor: '#36b9cc'
            },
            {
                label: 'Eksternal',
                data: <?= json_encode($eksternal); ?>,
                backgroundColor: '#f6c23e'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: 10
        },
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    title: function (tooltipItems) {
                        return 'Tanggal ' + tooltipItems[0].label;
                    }
                }
            }
        }
    }

});

// 2️⃣ Filter AJAX (INI TAMBAHAN)
$('#filterBulan, #filterTahun').on('change', function () {
    let bulan = $('#filterBulan').val();
    let tahun = $('#filterTahun').val();

    $.getJSON('<?= base_url('admin/access-log/chart-data'); ?>', {
        bulan: bulan,
        tahun: tahun
    }, function (res) {

        loginChart.data.labels = res.labels;
        loginChart.data.datasets[0].data = res.internal;
        loginChart.data.datasets[1].data = res.eksternal;
        loginChart.update();

    });
});
</script>
<?= $this->endSection(); ?>

