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
<h1 class="h3 mb-4 text-gray-800">Kelola User Admin</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">

            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahAdmin">
                <i class="fas fa-plus"></i> Tambah Admin
            </button>
            <!-- Modal Tambah Admin -->
            <div class="modal fade" id="modalTambahAdmin" tabindex="-1" role="dialog" aria-labelledby="modalTambahAdminLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="<?= base_url('admin/manage-user-admin/saveAdmin') ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="modalTambahAdminLabel">Tambah Admin Baru</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <?php if (session()->getFlashdata('errors')) : ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                <?php endif ?>

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
                        <th>Email</th>
                        <th>Fullname</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($users as $user): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= esc($user->email) ?></td>
                            <td><?= esc($user->fullname) ?></td>
                            <td><?= esc($user->username) ?></td>
                            <td><?= esc($user->role) ?></td>
                            <td>
                              <?php if ($user->active): ?>
                                <span class="badge badge-success">Aktif</span>
                              <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditAdmin<?= $user->id ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                              <a href="<?= base_url('admin/manage-user-admin/delete/'.$user->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                <i class="fas fa-trash"></i> Hapus
                              </a>
                            </td>
                        </tr>

                        <!-- Modal Edit Admin -->
                        <div class="modal fade" id="modalEditAdmin<?= $user->id ?>" tabindex="-1" aria-labelledby="modalEditAdminLabel<?= $user->id ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="<?= base_url('admin/manage-user-admin/updateAdmin/' . $user->id) ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title" id="modalEditAdminLabel<?= $user->id ?>">Edit Admin</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <?php if (session()->getFlashdata('errors')) : ?>
                                                <div class="alert alert-danger">
                                                    <ul class="mb-0">
                                                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                                            <li><?= esc($error) ?></li>
                                                        <?php endforeach ?>
                                                    </ul>
                                                </div>
                                            <?php endif ?>

                                            <div class="form-group">
                                                <label>Nama Lengkap</label>
                                                <input type="text" name="fullname" class="form-control" value="<?= esc($user->fullname) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" name="username" class="form-control" value="<?= esc($user->username) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= esc($user->email) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Password baru (kosongkan jika tidak ingin ganti)</label>
                                                <input type="password" name="password" class="form-control">
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