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

<h1 class="h3 mb-4 text-gray-800">Kelola User</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Email</th>
                        <th>Nama Lengkap</th>
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
                            <td><?= esc($user->role) ?></td>
                            <td>
                              <?php if ($user->active): ?>
                                <span class="badge badge-success">Aktif</span>
                              <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                              <?php endif; ?>
                            </td>
                            <td>
                            <?php if ($user->active == 0): ?>
                                <a href="<?= base_url('admin/manage-user/activate/'.$user->id) ?>" class="btn btn-sm btn-success" onclick="return confirm('Aktifkan akun ini?')">
                                    <i class="fas fa-check"></i> Aktivasi
                                </a>
                            <?php endif; ?>
                              <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editUserModal<?= $user->id ?>">
                                <i class="fas fa-edit"></i> Edit
                              </button>
                              <a href="<?= base_url('admin/manage-user/delete/'.$user->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                <i class="fas fa-trash"></i> Hapus
                              </a>
                            </td>
                        </tr>

                        <!-- Modal Edit-->
                        <div class="modal fade" id="editUserModal<?= $user->id ?>" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel<?= $user->id ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <form action="<?= base_url('admin/manage-user/update/' . $user->id) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="editUserModalLabel<?= $user->id ?>">Edit User: <?= esc($user->fullname) ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?= esc($user->email) ?>" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                    <label>Fullname</label>
                                    <input type="text" name="fullname" value="<?= esc($user->fullname) ?>" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                    <label>No HP</label>
                                    <input type="text" name="no_hp" value="<?= esc($user->no_hp) ?>" class="form-control">
                                    </div>
                                    <div class="form-group">
                                    <label>Semester</label>
                                    <input type="number" name="semester" value="<?= esc($user->semester) ?>" class="form-control">
                                    </div>
                                    <div class="form-group">
                                    <label>Nilai IPK</label>
                                    <input type="text" name="nilai_ipk" value="<?= esc($user->nilai_ipk) ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Instansi</label>
                                        <select name="instansi_id" id="editInstansi" class="form-control">
                                        <?php foreach ($instansi as $ins): ?>
                                            <option value="<?= $ins['instansi_id'] ?>"><?= $ins['nama_instansi'] ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Jurusan</label>
                                        <select name="jurusan_id" id="editJurusan" class="form-control">
                                        <?php foreach ($jurusan as $jur): ?>
                                            <option value="<?= $jur['jurusan_id'] ?>"><?= $jur['nama_jurusan'] ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                </div>
                                </div>
                                <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
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