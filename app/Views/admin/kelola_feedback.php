<?= $this->extend('admin/templates/index'); ?>

<?= $this->section('content'); ?>
<style>
#modalIsi {
    white-space: pre-wrap;
}
</style>

<div class="container-fluid">
<?php $session = \Config\Services::session(); ?>
<?php if ($session->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $session->getFlashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($session->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $session->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Kelola Feedback</h1>

    <!-- DataTables Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Nama</th>
                            <th rowspan="2">Tanggal</th>
                            <th colspan="4" class="text-center">Feedback Untuk Diklat</th>
                            <th colspan="6" class="text-center">Feedback Untuk Unit Kerja</th>
                            <th rowspan="2">Total</th>

                        </tr>
                        <tr>
                            <th class="text-center">Website</th>
                            <th class="text-center">Admin</th>
                            <th class="text-center">Saran</th>
                            <th class="text-center">Total Diklat</th>
                            <th class="text-center">Unit Kerja</th>
                            <th class="text-center">Pembimbing</th>
                            <th class="text-center">Pengalaman</th>
                            <th class="text-center">Suasana</th>
                            <th class="text-center">Kesan</th>
                            <th class="text-center">Total Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($feedback) && is_array($feedback)) : ?>
                            <?php $no = 1; foreach ($feedback as $data) : ?>
                                <?php
                                $totalDiklat = (int)$data['diklat_website'] 
                                    + (int)$data['diklat_admin'];
                                $totalUnit = (int)$data['unit_supervisor'] 
                                    + (int)$data['unit_pengalaman'] 
                                    + (int)$data['unit_suasana'];
                                $total = (int)$data['diklat_website'] 
                                    + (int)$data['diklat_admin']
                                    + (int)$data['unit_supervisor'] 
                                    + (int)$data['unit_pengalaman'] 
                                    + (int)$data['unit_suasana'];
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($data['fullname']); ?></td>
                                    <td><?= format_tanggal_jam(esc($data['created_at'])); ?></td>
                                    <td><?= esc($data['diklat_website']); ?></td>
                                    <td><?= esc($data['diklat_admin']); ?></td>
                                    <!-- <td><?= esc($data['diklat_saran']); ?></td> -->
                                     <td class="text-center">
                                        <?php if (!empty($data['diklat_saran'])) : ?>
                                            <button 
                                                class="btn btn-sm btn-info btn-view-feedback"
                                                data-nama="<?= esc($data['fullname']); ?>"
                                                data-tanggal="<?= format_tanggal_jam(esc($data['created_at'])); ?>"
                                                data-judul="Saran Diklat"
                                                data-isi="<?= esc($data['diklat_saran']); ?>"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= $totalDiklat; ?></strong></td>
                                    <td><?= esc($data['unit_kerja']); ?></td>
                                    <td><?= esc($data['unit_supervisor']); ?></td>
                                    <td><?= esc($data['unit_pengalaman']); ?></td>
                                    <td><?= esc($data['unit_suasana']); ?></td>
                                    <!-- <td><?= esc($data['unit_kesan']); ?></td> -->
                                    <td class="text-center">
                                        <?php if (!empty($data['unit_kesan'])) : ?>
                                            <button 
                                                class="btn btn-sm btn-success btn-view-feedback"
                                                data-nama="<?= esc($data['fullname']); ?>"
                                                data-tanggal="<?= format_tanggal_jam(esc($data['created_at'])); ?>"
                                                data-judul="Kesan Unit Kerja"
                                                data-isi="<?= esc($data['unit_kesan']); ?>"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= $totalUnit; ?></strong></td>
                                    <td><strong><?= $total; ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Feedback -->
<div class="modal fade" id="modalFeedback" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalJudul">
                    Detail Feedback
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Info -->
                <div class="mb-3">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th width="25%">Nama</th>
                            <td id="modalNama"></td>
                        </tr>
                        <tr>
                            <th>Tanggal Feedback</th>
                            <td id="modalTanggal"></td>
                        </tr>
                    </table>
                </div>

                <hr class="mt-1">

                <!-- Isi feedback -->
                <div class="p-3 bg-light rounded">
                    <p id="modalIsi" class="mb-0 text-justify"></p>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-view-feedback')) {
        const btn = e.target.closest('.btn-view-feedback');

        document.getElementById('modalJudul').innerText   = btn.dataset.judul;
        document.getElementById('modalNama').innerText    = btn.dataset.nama;
        document.getElementById('modalTanggal').innerText = btn.dataset.tanggal;
        document.getElementById('modalIsi').innerText     = btn.dataset.isi;

        const modal = new bootstrap.Modal(document.getElementById('modalFeedback'));
        modal.show();
    }
});
</script>

<?= $this->endSection(); ?>
