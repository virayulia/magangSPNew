<?= $this->extend('admin/templates/index') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title) ?></h1>

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

    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0" >
                    <thead class="thead-dark">
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <th>Judul Penelitian</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Daftar</th>
                            <th>Bidang</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($penelitian as $p): ?>
                        <tr>
                            <td><?= esc($p->fullname) ?></td>
                            <td><?= esc($p->judul_penelitian) ?></td>
                            <td><?= date('d M Y', strtotime($p->tanggal_masuk)) ?></td>
                            <td><?= date('d M Y H:i', strtotime($p->tanggal_daftar)) ?></td>
                            <td><?= esc($p->bidang) ?></td>
                            <td>
                                <?php if ($p->status_akhir == 'pendaftaran'): ?>
                                    <span class="badge badge-warning">Menunggu</span>
                                <?php elseif ($p->status_akhir == 'diterima'): ?>
                                    <span class="badge badge-success">Diterima</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="/admin/penelitian/update-status/<?= $p->penelitian_id ?>" method="post" class="d-flex flex-column gap-1">
                                    <?= csrf_field() ?>
                                    <button type="submit" name="status" value="diterima" class="btn btn-success btn-sm mb-1">
                                        <i class="fas fa-check"></i> Terima
                                    </button>
                                    <button type="submit" name="status" value="ditolak" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7">
                                <strong>Deskripsi:</strong> <?= esc($p->deskripsi) ?: '-' ?> <br>
                                <strong>Dosen Pembimbing:</strong> <?= esc($p->dosen_pembimbing) ?: '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
