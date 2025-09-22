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

<h1 class="h3 mb-4 text-gray-800">Kelola Unit Kerja</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahUnit">
                <i class="fas fa-plus"></i> Tambah Unit Kerja
            </button>

            <!-- Modal Tambah Instansi -->
            <div class="modal fade" id="modalTambahUnit" tabindex="-1" role="dialog" aria-labelledby="modalTambahUnitLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="<?= base_url('admin/unit/save') ?>" method="post">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="modalTambahUnitLabel">Tambah Unit Kerja</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="unit_kerja">Unit Kerja</label>
                                    <input type="text" class="form-control" id="unit_kerja" name="unit_kerja" required>
                                </div>
                                <div class="form-group">
                                    <label for="nama_pimpinan">Nama Pimpinan</label>
                                    <input type="text" class="form-control" id="nama_pimpinan" name="nama_pimpinan" required>
                                </div>
                                <div class="form-group">
                                    <label for="email_pimpinan">Email Pimpinan</label>
                                    <input type="text" class="form-control" id="email_pimpinan" name="email_pimpinan" required>
                                </div>
                                <div class="form-group">
                                    <label for="safety">Safety</label>
                                    <select name="safety" id="safety" class="form-control" required>
                                        <option value="1">APD</option>
                                        <option value="0">Non APD</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="active">Active</label>
                                    <select name="active" id="active" class="form-control" required>
                                        <option value="1">Active</option>
                                        <option value="0">Non active</option>
                                    </select>
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
                        <th>Unit Kerja</th>
                        <th>Nama Pimpinan</th>
                        <th>Email Pimpinan</th>
                        <th>Safety(APD)</th>
                        <th>Active</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($unit as $item): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= esc($item['unit_kerja']); ?></td>
                            <td><?= esc($item['nama_pimpinan']); ?></td>
                            <td><?= esc($item['email_pimpinan']); ?></td>
                            <?php if($item['safety'] == 1): ?>
                                <td>Pakai</td>
                            <?php else : ?>
                                <td>Tidak Pakai</td>
                            <?php endif; ?>
                            <?php if($item['active'] == 1): ?>
                                <td>Aktif</td>
                            <?php else : ?>
                                <td>Tidak Aktif</td>
                            <?php endif; ?>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $item['unit_id']; ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <!-- Modal Edit Periode -->
                        <div class="modal fade" id="editModal<?= $item['unit_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $item['unit_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="<?= base_url('admin/kelola-unit/update/' . $item['unit_id']); ?>" method="post">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" id="editModalLabel<?= $item['unit_id']; ?>">Edit Unit Kerja</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="unit_kerja_<?= $item['unit_id']; ?>">Unit Kerja</label>
                                            <input type="text" class="form-control" name="unit_kerja" id="unit_kerja_<?= $item['unit_id']; ?>" value="<?= esc($item['unit_kerja']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_pimpinan_<?= $item['unit_id']; ?>">Nama Pimpinan</label>
                                            <input type="text" class="form-control" name="nama_pimpinan" id="nama_pimpinan_<?= $item['unit_id']; ?>" value="<?= esc($item['nama_pimpinan']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="email_pimpinan_<?= $item['unit_id']; ?>">Email Pimpinan</label>
                                            <input type="text" class="form-control" name="email_pimpinan" id="email_pimpinan_<?= $item['unit_id']; ?>" value="<?= esc($item['email_pimpinan']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="safety_<?= $item['unit_id']; ?>">Safety</label>
                                            <select class="form-control" name="safety" id="safety_<?= $item['unit_id']; ?>" required>
                                                <option value="" disabled hidden>Pilih Safety</option>
                                                <option value="0" <?= ((string)$item['safety'] === '0') ? 'selected' : ''; ?>>Non Lapangan</option>
                                                <option value="1" <?= ((string)$item['safety'] === '1') ? 'selected' : ''; ?>>Lapangan</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="active_<?= $item['unit_id']; ?>">Active</label>
                                            <select class="form-control" name="active" id="active_<?= $item['unit_id']; ?>" required>
                                                <option value="" disabled hidden>Pilih Active</option>
                                                <option value="0" <?= ((string)$item['active'] === '0') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                                <option value="1" <?= ((string)$item['active'] === '1') ? 'selected' : ''; ?>>Aktif</option>
                                            </select>
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