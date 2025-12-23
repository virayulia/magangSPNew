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

    <h1 class="h3 mb-2 text-gray-800">Change Log Aplikasi</h1>
    <p class="mb-4 text-muted">
        Riwayat perubahan dan pembaruan sistem
    </p>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahChangeLog">
                    <i class="fas fa-plus"></i> Tambah Change Log
                </button>
                <table class="table table-bordered table-striped" id="dataTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Versi</th>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($changeLogs as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= format_tanggal_indonesia_dengan_jam($row['created_at']) ?></td>
                            <td><?= esc($row['version']) ?></td>
                            <td><?= esc($row['title']) ?></td>
                            <td class="text-center">
                                <?php if ($row['type'] == 'feature'): ?>
                                    <span class="badge badge-success">Feature</span>
                                <?php elseif ($row['type'] == 'fix'): ?>
                                    <span class="badge badge-danger">Fix</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Improvement</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-info btn-sm btn-detail"
                                    data-title="<?= esc($row['title']) ?>"
                                    data-version="<?= esc($row['version']) ?>"
                                    data-date="<?= format_tanggal_indonesia_dengan_jam($row['created_at']) ?>"
                                    data-description="<?= esc($row['description']) ?>"
                                    data-type="<?= esc($row['type']) ?>">
                                    <i class="fa fa-eye"></i>
                                </button>

                                <button class="btn btn-warning btn-sm btn-edit"
                                    data-id="<?= $row['id'] ?>"
                                    data-version="<?= esc($row['version']) ?>"
                                    data-title="<?= esc($row['title']) ?>"
                                    data-description="<?= esc($row['description']) ?>"
                                    data-type="<?= esc($row['type']) ?>">
                                    <i class="fa fa-pen"></i>
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete"
                                    data-id="<?= $row['id'] ?>"
                                    data-title="<?= esc($row['title']) ?>">
                                    <i class="fa fa-trash"></i>
                                </button>


                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahChangeLog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="<?= base_url('admin/change-log/save') ?>" method="post">
            <?= csrf_field() ?>

            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Change Log</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Versi</label>
                            <input type="text" name="version" class="form-control" placeholder="v1.2.0">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tipe Perubahan</label>
                            <select name="type" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <option value="feature">Feature</option>
                                <option value="fix">Fix</option>
                                <option value="improvement">Improvement</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Judul Perubahan</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" rows="5" class="form-control" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal Edit -->
<div class="modal fade" id="modalEditChangeLog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formEditChangeLog" method="post">
            <?= csrf_field() ?>

            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Edit Change Log</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" id="editId">

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Versi</label>
                            <input type="text" name="version" id="editVersion" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tipe</label>
                            <select name="type" id="editType" class="form-control" required>
                                <option value="feature">Feature</option>
                                <option value="fix">Fix</option>
                                <option value="improvement">Improvement</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" id="editDescription" rows="5" class="form-control" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal Detail -->
<div class="modal fade" id="modalChangeLog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-muted mb-2">
                    <span id="modalVersion"></span> •
                    <span id="modalDate"></span>
                </p>

                <span class="badge mb-3" id="modalType"></span>

                <hr>

                <p id="modalDescription" class="text-justify"></p>
            </div>
        </div>
    </div>
</div>
<!-- Modal Delete -->
<div class="modal fade" id="modalDeleteChangeLog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formDeleteChangeLog" method="post">
            <?= csrf_field() ?>

            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus change log
                    <strong id="deleteTitle"></strong>?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Hapus
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>




<?= $this->endSection(); ?>
<?= $this->section('scripts') ?>
<script>
$(document).on('show.bs.modal', '.modal', function () {
    $(this).appendTo('body');
});
</script>
<script>
$(document).on('click', '.btn-edit', function () {

    const id          = $(this).data('id');
    const version     = $(this).data('version');
    const title       = $(this).data('title');
    const description = $(this).data('description');
    const type        = $(this).data('type');

    $('#editId').val(id);
    $('#editVersion').val(version);
    $('#editTitle').val(title);
    $('#editDescription').val(description);
    $('#editType').val(type);

    // set action form
    $('#formEditChangeLog').attr(
        'action',
        '<?= base_url('admin/change-log/update/') ?>' + id
    );

    $('#modalEditChangeLog').modal('show');
});

$(document).on('click', '.btn-detail', function () {

    const title       = $(this).data('title');
    const version     = $(this).data('version');
    const date        = $(this).data('date');
    const description = $(this).data('description');
    const type        = $(this).data('type');

    $('#modalTitle').text(title);
    $('#modalVersion').text('Versi ' + version);
    $('#modalDate').text(date);
    $('#modalDescription').text(description);

    // Badge type
    let badgeClass = 'badge-secondary';
    let typeLabel  = type;

    if (type === 'feature') {
        badgeClass = 'badge-success';
        typeLabel  = 'Feature';
    } else if (type === 'fix') {
        badgeClass = 'badge-danger';
        typeLabel  = 'Fix';
    } else if (type === 'improvement') {
        badgeClass = 'badge-warning';
        typeLabel  = 'Improvement';
    }

    $('#modalType')
        .removeClass()
        .addClass('badge ' + badgeClass)
        .text(typeLabel);

    $('#modalChangeLog').modal('show');
});

$(document).on('click', '.btn-delete', function () {
    const id    = $(this).data('id');
    const title = $(this).data('title');

    $('#deleteTitle').text(title);
    $('#formDeleteChangeLog').attr(
        'action',
        '<?= base_url('admin/change-log/delete/') ?>' + id
    );

    $('#modalDeleteChangeLog').modal('show');
});

</script>


<?= $this->endSection(); ?>

