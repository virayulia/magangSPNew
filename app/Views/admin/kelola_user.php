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
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
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
                              <button
                                type="button"
                                class="btn btn-sm btn-warning btn-edit"
                                data-toggle="modal"
                                data-target="#editUserModal"
                                data-id="<?= $user->id ?>"
                                data-email="<?= esc($user->email) ?>"
                                data-fullname="<?= esc($user->fullname) ?>"
                                data-no_hp="<?= esc($user->no_hp) ?>"
                                data-semester="<?= esc($user->semester) ?>"
                                data-nilai_ipk="<?= esc($user->nilai_ipk) ?>"
                                data-instansi="<?= $user->instansi_id ?>"
                                data-jurusan="<?= $user->jurusan_id ?>"
                            >
                                <i class="fas fa-edit"></i>
                            </button>


                              <a href="<?= base_url('admin/manage-user/delete/'.$user->id) ?>" title="Hapus" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                <i class="fas fa-trash"></i> 
                              </a>
                            </td>
                        </tr>
                        
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Edit -->
</div>
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="formEditUser" method="post">
      <?= csrf_field() ?>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" id="editEmail" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Fullname</label>
              <input type="text" name="fullname" id="editFullname" class="form-control" required>
            </div>
            <div class="form-group">
              <label>No HP</label>
              <input type="text" name="no_hp" id="editNoHp" class="form-control">
            </div>
            <div class="form-group">
              <label>Semester</label>
              <input type="number" name="semester" id="editSemester" class="form-control">
            </div>
            <div class="form-group">
              <label>Nilai IPK</label>
              <input type="text" name="nilai_ipk" id="editNilaiIpk" class="form-control">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Instansi</label>
              <select name="instansi_id" id="editInstansi" class="form-control select2" >
                <?php foreach ($instansi as $ins): ?>
                  <option value="<?= $ins['instansi_id'] ?>">
                    <?= $ins['nama_instansi'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Jurusan</label>
              <select name="jurusan_id" id="editJurusan" class="form-control select2" >
                <?php foreach ($jurusan as $jur): ?>
                  <option value="<?= $jur['jurusan_id'] ?>">
                    <?= $jur['nama_jurusan'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Batal
          </button>
        </div>
      </div>
    </form>
  </div>
</div>


<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
$(document).on('click', '.btn-edit', function () {
    const btn = $(this);

    $('#editEmail').val(btn.data('email'));
    $('#editFullname').val(btn.data('fullname'));
    $('#editNoHp').val(btn.data('no_hp'));
    $('#editSemester').val(btn.data('semester'));
    $('#editNilaiIpk').val(btn.data('nilai_ipk'));

    $('#editInstansi')
        .val(btn.data('instansi'))
        .trigger('change');

    $('#editJurusan')
        .val(btn.data('jurusan'))
        .trigger('change');

    $('#formEditUser').attr(
        'action',
        "<?= base_url('admin/manage-user/update') ?>/" + btn.data('id')
    );
});
</script>


<?= $this->endSection(); ?>