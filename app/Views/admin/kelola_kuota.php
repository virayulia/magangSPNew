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

<h1 class="h3 mb-4 text-gray-800">Kelola Kuota Magang</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahUnit">
                <i class="fas fa-plus"></i> Tambah Unit Kerja
            </button>
            <!-- Modal Tambah Instansi -->
            <div class="modal fade" id="modalTambahUnit" tabindex="-1" role="dialog" aria-labelledby="modalTambahUnitLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="<?= base_url('admin/kuota-unit/save') ?>" method="post">
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
                                    <select class="form-control select2" id="unit_kerja" name="unit_id" required>
                                        <option value="" disabled selected>-- Pilih Unit Kerja --</option>
                                        <?php foreach ($unit_kerja as $unit): ?>
                                            <option value="<?= esc($unit['unit_id']); ?>">
                                                <?= esc($unit['unit_kerja']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tingkat_pendidikan">Tingkat Pendidikan</label>
                                    <select name="tingkat_pendidikan" id="tingkat_pendidikan" class="form-control" required>
                                        <option value="" disabled selected>-- Pilih Tingkat Instansi --</option>
                                        <option value="SMK">Sekolah Menengah Kejuruan (SMK)</option>
                                        <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="kuota">Kuota</label>
                                    <input type="text" class="form-control" id="kuota" name="kuota" required>
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
                        <th>Tingkat Pendidikan</th>
                        <th>Total Kuota</th>
                        <th>Jumlah Terisi</th>
                        <th>Kuota Tersedia</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($kuota_unit as $item): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= esc($item->unit_kerja); ?></td>
                            <td><?= esc($item->tingkat_pendidikan); ?></td>
                            <td><?= $item->kuota; ?></td>
                            <td><?= $item->jumlah_diterima_atau_magang; ?></td>
                            <td><?= $item->sisa_kuota; ?></td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $item->kuota_unit_id; ?>">
                                    Edit
                                </button>

                            </td>
                        </tr>
                       <!-- Modal Edit -->
                        <div class="modal fade" id="editModal<?= $item->kuota_unit_id; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $item->kuota_unit_id; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="<?= base_url('admin/kelola-kuota-unit/update/' . $item->kuota_unit_id); ?>" method="post">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title" id="editModalLabel<?= $item->kuota_unit_id; ?>">Edit Kuota Unit</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="unit_kerja_<?= $item->kuota_unit_id; ?>">Unit Kerja</label>
                                                <input type="text" class="form-control" id="unit_kerja_<?= $item->kuota_unit_id; ?>" value="<?= esc($item->unit_kerja); ?>" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="tingkat_pendidikan_<?= $item->kuota_unit_id; ?>">Tingkat Pendidikan</label>
                                                <select class="form-control" name="tingkat_pendidikan" id="tingkat_pendidikan_<?= $item->kuota_unit_id; ?>" required>
                                                    <option value="" disabled <?= ($item->tingkat_pendidikan === null || $item->tingkat_pendidikan === '') ? 'selected' : ''; ?>>Pilih Tingkat Pendidikan</option>
                                                    <option value="SMK" <?= ($item->tingkat_pendidikan === 'SMK') ? 'selected' : ''; ?>>Sekolah Menengah Kejuruan (SMK)</option>
                                                    <option value="Perguruan Tinggi" <?= ($item->tingkat_pendidikan === 'Perguruan Tinggi') ? 'selected' : ''; ?>>Perguruan Tinggi (PT)</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="kuota_<?= $item->kuota_unit_id; ?>">Kuota</label>
                                                <input type="number" class="form-control" name="kuota" id="kuota_<?= $item->kuota_unit_id; ?>" value="<?= esc($item->kuota); ?>" required>
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