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

<h1 class="h3 mb-4 text-gray-800">Kelola Kuota Unit</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Unit Kerja</th>
                        <th>Tingkat Pendidikan</th>
                        <th>Total Kuota</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($unit as $item): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= esc($item['unit_kerja']); ?></td>
                            <td><?= esc($item['tingkat_pendidikan']); ?></td>
                            <td><?= $item['kuota']; ?></td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $item['unit_id']; ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <!-- Modal Edit Periode -->
                        <div class="modal fade" id="editModal<?= $item['unit_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $item['unit_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="<?= base_url('/kelola-kuota-unit/update/' . $item['unit_id']); ?>" method="post">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" id="editModalLabel<?= $item['unit_id']; ?>">Edit Kuota Unit</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="unit_kerja_<?= $item['unit_id']; ?>">Unit Kerja</label>
                                            <input type="text" class="form-control" name="unit_kerja" id="unit_kerja_<?= $item['unit_id']; ?>" value="<?= esc($item['unit_kerja']); ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="tingkat_pendidikan_<?= $item['unit_id']; ?>">Tingkat Pendidikan</label>
                                            <select class="form-control" name="tingkat_pendidikan" id="tingkat_pendidikan_<?= $item['unit_id']; ?>" required>
                                                <option value="" disabled hidden>Pilih Tingkat Pendidikan</option>
                                                <option value="sma/smk" <?= ($item['tingkat_pendidikan'] === 'sma/smk') ? 'selected' : ''; ?>>SMA/SMK Sederajat</option>
                                                <option value="perguruan tinggi" <?= ($item['tingkat_pendidikan'] === 'perguruan tinggi') ? 'selected' : ''; ?>>Perguruan Tinggi</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="kuota_<?= $item['unit_id']; ?>">Kuota</label>
                                            <input type="number" class="form-control" name="kuota" id="kuota_<?= $item['unit_id']; ?>" value="<?= esc($item['kuota']); ?>" required>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

<?= $this->endSection() ?>