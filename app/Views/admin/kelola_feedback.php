<?= $this->extend('admin/templates/index'); ?>

<?= $this->section('content'); ?>

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
                            <th class="text-center">Supervisor</th>
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
                                    <td><?= format_tanggal_indonesia_dengan_jam(esc($data['created_at'])); ?></td>
                                    <td><?= esc($data['diklat_website']); ?></td>
                                    <td><?= esc($data['diklat_admin']); ?></td>
                                    <td><?= esc($data['diklat_saran']); ?></td>
                                    <td><strong><?= $totalDiklat; ?></strong></td>
                                    <td><?= esc($data['unit_kerja']); ?></td>
                                    <td><?= esc($data['unit_supervisor']); ?></td>
                                    <td><?= esc($data['unit_pengalaman']); ?></td>
                                    <td><?= esc($data['unit_suasana']); ?></td>
                                    <td><?= esc($data['unit_kesan']); ?></td>
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

<?= $this->endSection(); ?>
