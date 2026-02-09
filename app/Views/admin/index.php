<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

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

    <h1 class="h3 mb-2 text-gray-800">Data Pendaftaran</h1>

    <div class="mb-2">
        <?php if (!empty($statusCount)): ?>

            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="<?= base_url('admin/manage-pendaftaran?filter=pendaftaran') ?>" class="text-decoration-none">
                            <div class="card border-left-primary shadow h-100 py-2 small-card hover-card">
                                <div class="card-body py-2 text-center">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Pendaftaran
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $statusCount['Pendaftaran'] ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= base_url('admin/manage-pendaftaran?filter=diterima') ?>" class="text-decoration-none">
                            <div class="card border-left-secondary shadow h-100 py-2 small-card">
                                <div class="card-body py-2 text-center">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                        Diterima
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $statusCount['Diterima']?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= base_url('admin/manage-pendaftaran?filter=terkonfirmasi') ?>" class="text-decoration-none">
                            <div class="card border-left-success shadow h-100 py-2 small-card hover-card">
                                <div class="card-body py-2 text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Terkonfirmasi
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $statusCount['Terkonfirmasi'] ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= base_url('admin/manage-pendaftaran?filter=tidak-valid') ?>" class="text-decoration-none">
                            <div class="card border-left-info shadow h-100 py-2 small-card hover-card">
                                <div class="card-body py-2 text-center">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Berkas Tidak Valid
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $statusCount['Berkas Tidak Valid'] ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

        <?php endif; ?>
    </div>

    <!-- <div class="card shadow mb-4">
        <div class="card-header text-center fw-bold">
            Status Pendaftaran Magang
        </div>
        <div class="card-body d-flex justify-content-center">
            <canvas id="chartStatus" width="300" height="300"></canvas>
        </div>
    </div> -->

    

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Unit Kerja</th>
                            <th>Tanggal Daftar</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pendaftaran) && is_array($pendaftaran)) : ?>
                            <?php $no = 1; foreach ($pendaftaran as $data) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($data['fullname']); ?></td>
                                    <td><?= esc($data['unit_kerja']); ?></td>
                                    <td><?= date('d-m-Y, H:i', strtotime($data['tanggal_daftar'])); ?></td>
                                    <td><?= !empty($data['tanggal_masuk']) ? date('d-m-Y', strtotime($data['tanggal_masuk'])) : '-' ?></td>
                                    <td><?= !empty($data['tanggal_selesai']) ? date('d-m-Y', strtotime($data['tanggal_selesai'])) : '-' ?></td>
                                    <td>
                                        <?php 
                                        if (!is_null($data['tanggal_validasi_berkas'])) {
                                            if ($data['status_validasi_berkas'] === 'Y') {
                                                echo "Proses Validasi";
                                            } else {
                                                echo "Berkas Tidak Valid";
                                            }
                                        } elseif(!is_null($data['status_konfirmasi'])){
                                            if($data['status_konfirmasi'] === 'Y'){
                                                echo "Terkonfirmasi";
                                            }else{
                                                echo "Tidak Konfirmasi";
                                            }
                                        } elseif(!is_null($data['status_seleksi'])) {
                                            echo $data['status_seleksi'];
                                        }else{
                                            echo "Pendaftaran";
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- <a href="<?= base_url('admin/manage-pendaftaran/detail/' . $data['magang_id']); ?>" class="btn btn-info btn-sm">Detail</a> -->
                                        <!-- <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal<?= $data['magang_id'] ?>">Detail</button> -->
                                        <button class="btn btn-sm btn-info btn-detail-peserta" title="Detail" data-id="<?= $data['magang_id'] ?>" >
                                            <i class="fa fa-info-circle"></i>
                                        </button>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail Peserta -->
    <div class="modal fade" id="modalDetailPeserta" tabindex="-1" aria-labelledby="modalDetailPesertaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetailPesertaLabel">Detail Peserta</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detailPesertaContent">
                    <p class="text-center text-muted">Memuat data peserta...</p>
                </div>
                <div class="modal-footer">
                    <button id="btnEditMagang" class="btn btn-sm btn-warning d-none" title="Edit"><i class="fas fa-edit"></i></button>
                    <button id="btnBatalkanMagang" class="btn btn-sm btn-danger d-none"  title="Batalkan"><i class="fas fa-ban"></i></button>
                    <button class="btn btn-secondary" data-dismiss="modal"  title="Tutup"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Magang -->
    <div class="modal fade" id="modalEditMagang" tabindex="-1" aria-labelledby="modalEditMagangLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditMagang" method="post" action="">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title" id="modalEditMagangLabel">Edit Data Magang</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
            </button>
            </div>
            <div class="modal-body" id="editMagangContent">
            <p class="text-center text-muted">Memuat data...</p>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
        </form>
    </div>
    </div>


</div>

<script>
function batalkanMagang(id, modalId) {
    // Tutup modal Bootstrap dulu
    $('#' + modalId).modal('hide');

    // Delay agar modal benar-benar tertutup sebelum SweetAlert muncul
    setTimeout(function() {
        Swal.fire({
            title: 'Batalkan Magang?',
            input: 'textarea',
            inputLabel: 'Alasan Pembatalan',
            inputPlaceholder: 'Tulis alasan pembatalan di sini...',
            inputAttributes: {
                'aria-label': 'Tulis alasan pembatalan'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            preConfirm: (alasan) => {
                if (!alasan) {
                    Swal.showValidationMessage('Alasan pembatalan wajib diisi.');
                }
                return alasan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('admin/batalkanMagang') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'id': id,
                        'alasan': result.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Berhasil', 'Peserta magang telah dibatalkan.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }, 300); 
}

</script>

<script>
const statusLabels = [
    "Pendaftaran",
    "Proses Validasi",
    "Berkas Tidak Valid",
    "Terkonfirmasi",
    "Tidak Konfirmasi",
    "Diterima"
];

const statusValues = [
    <?= $statusCount['Pendaftaran'] ?>,
    <?= $statusCount['Proses Validasi'] ?>,
    <?= $statusCount['Berkas Tidak Valid'] ?>,
    <?= $statusCount['Terkonfirmasi'] ?>,
    <?= $statusCount['Tidak Konfirmasi'] ?>,
    <?= $statusCount['Diterima'] ?>
];

new Chart(document.getElementById("chartStatus"), {
    type: "doughnut", // atau "pie"
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusValues,
            backgroundColor: [
                "#858796", // Pendaftaran
                "#4e73df", // Proses Validasi
                "#e74a3b", // Berkas Tidak Valid
                "#1cc88a", // Terkonfirmasi
                "#f6c23e", // Tidak Konfirmasi
                "#36b9cc"  // Diterima
            ],
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "50%", // kalau mau donat
        plugins: {
            legend: {
                position: "right"
            }
        }
    }
});
</script>
<?= $this->endSection(); ?>
