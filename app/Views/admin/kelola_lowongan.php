<?= $this->extend('admin/templates/index');?>

<?= $this->section('content');?>

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
    <h1 class="h3 mb-2 text-gray-800">Kelola Lowongan</h1>
    <!-- <p class="mb-4">Berikut adalah data tangg</p> -->

    <!-- DataTables Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
        <div class="table-responsive">
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahPeriode">
                <i class="fas fa-plus"></i> Tambah Periode Magang
            </button>
            <!-- Modal tambah periode -->
             <div class="modal fade" id="modalTambahPeriode" tabindex="-1" role="dialog" aria-labelledby="modalTambahPeriodeLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="<?= base_url('admin/periode/save') ?>" method="post">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTambahPeriodeLabel">Tambah Periode Magang</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        
                        <div class="modal-body">
                        <div class="form-group">
                            <label for="tanggal_buka">Tanggal Buka</label>
                            <input type="date" class="form-control" id="tanggal_buka" name="tanggal_buka" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_tutup">Tanggal Tutup</label>
                            <input type="date" class="form-control" id="tanggal_tutup" name="tanggal_tutup" required>
                        </div>
                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>

            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Periode</th>
                        <th>Tanggal Buka</th>
                        <th>Tanggal Tutup</th> 
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($periode) && is_array($periode)) : ?>
                        <?php $no = 1; foreach ($periode as $data) : ?>
                            <?php
                                $fmt = new \IntlDateFormatter('id_ID', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
                                $tanggal_buka = $fmt->format(strtotime($data['tanggal_buka']));
                                $tanggal_tutup = $fmt->format(strtotime($data['tanggal_tutup']));
                                $fmtt = new \IntlDateFormatter('id_ID', \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'MMMM yyyy');
                                $periode = $fmtt->format(strtotime($data['tanggal_buka']));
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $periode; ?></td>
                                <td><?= $tanggal_buka; ?></td>
                                <td><?= $tanggal_tutup; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $data['periode_id']; ?>">
                                        Edit
                                    </button>
                                    <a href="<?= base_url('admin/periode/delete/'.$data['periode_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <!-- Modal Edit Periode -->
                            <div class="modal fade" id="editModal<?= $data['periode_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $data['periode_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="<?= base_url('admin/periode/update/' . $data['periode_id']); ?>" method="post">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" id="editModalLabel<?= $data['periode_id']; ?>">Edit Periode Magang</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    
                                    <div class="modal-body">
                                    <div class="form-group">
                                        <label for="tanggal_buka_<?= $data['periode_id']; ?>">Tanggal Buka</label>
                                        <input type="date" class="form-control" name="tanggal_buka" id="tanggal_buka_<?= $data['periode_id']; ?>" value="<?= esc($data['tanggal_buka']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="tanggal_tutup_<?= $data['periode_id']; ?>">Tanggal Tutup</label>
                                        <input type="date" class="form-control" name="tanggal_tutup" id="tanggal_tutup_<?= $data['periode_id']; ?>" value="<?= esc($data['tanggal_tutup']); ?>" required>
                                    </div>
                                    </div>

                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning">Update</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                            </div>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


</div>

<?= $this->endSection()?>
