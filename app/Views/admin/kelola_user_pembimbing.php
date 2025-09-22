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
<h1 class="h3 mb-4 text-gray-800">Kelola User Pembimbing</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">

            <!-- Tombol Tambah Pembimbing -->
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahPembimbing">
                <i class="fas fa-plus"></i> Tambah Pembimbing
            </button>

            <!-- Modal Tambah Pembimbing -->
            <div class="modal fade" id="modalTambahPembimbing" tabindex="-1" role="dialog" aria-labelledby="modalTambahPembimbingLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="<?= base_url('admin/manage-user-pembimbing/save') ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="modalTambahPembimbingLabel">Tambah Pembimbing Baru</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="fullname" class="form-control" required value="<?= old('fullname'); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" required value="<?= old('username'); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required value="<?= old('email'); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Password (min 8 karakter)</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Jabatan</label>
                                    <select name="eselon" class="form-control" required>
                                        <option value="" disabled selected>-- Pilih Jabatan --</option>
                                        <option value="2">Eselon 2</option>
                                        <option value="3">Eselon 3</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Unit Kerja</label>
                                    <select name="unit_id" class="form-control select2">
                                        <option value="" disabled selected>-- Pilih Unit Kerja --</option>
                                        <?php foreach ($units as $unit): ?>
                                            <option value="<?= $unit['unit_id'] ?>"><?= esc($unit['unit_kerja']) ?></option>
                                        <?php endforeach; ?>
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

            <!-- Tombol Import Excel -->
            <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalImportPembimbing">
                <i class="fas fa-file-excel"></i> Import Excel
            </button>

            <!-- Modal Import Pembimbing -->
            <div class="modal fade" id="modalImportPembimbing" tabindex="-1" role="dialog" aria-labelledby="modalImportPembimbingLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="<?= base_url('admin/manage-user-pembimbing/import') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="modalImportPembimbingLabel">
                                    <i class="fas fa-file-excel"></i> Import Pembimbing dari Excel
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Upload File Excel (.xlsx)</label>
                                    <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
                                    <small class="text-muted">
                                        Gunakan template dengan kolom: 
                                        <code>fullname, username, email, password, eselon, unit_id</code>.
                                    </small>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-upload"></i> Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Tabel Pembimbing -->
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Email</th>
                        <th>Unit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($pembimbing as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item->fullname) ?></td>
                            <td>Eselon <?= esc($item->eselon) ?></td>
                            <td><?= esc($item->email) ?></td>
                            <td><?= esc($item->unit_kerja ?? '-') ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditPembimbing<?= $item->id ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= base_url('admin/manage-user-pembimbing/delete/'.$item->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit Pembimbing -->
                        <div class="modal fade" id="modalEditPembimbing<?= $item->id ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="<?= base_url('admin/manage-user-pembimbing/update/' . $item->id) ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title">Edit Pembimbing</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Lengkap</label>
                                                <input type="text" name="fullname" class="form-control" value="<?= esc($item->fullname) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" name="username" class="form-control" value="<?= esc($item->username) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= esc($item->email) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Jabatan</label>
                                                <select name="eselon" class="form-control" required>
                                                    <option value="" disabled>-- Pilih Jabatan --</option>
                                                    <option value="2" <?= $item->eselon == '2' ? 'selected' : '' ?>>Eselon 2</option>
                                                    <option value="3" <?= $item->eselon == '3' ? 'selected' : '' ?>>Eselon 3</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Unit Kerja</label>
                                                <select name="unit_id" class="form-control">
                                                    <?php foreach ($units as $unit): ?>
                                                        <option value="<?= $unit['unit_id'] ?>" <?= $item->unit_id == $unit['unit_id'] ? 'selected' : '' ?>>
                                                            <?= esc($unit['unit_kerja']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
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