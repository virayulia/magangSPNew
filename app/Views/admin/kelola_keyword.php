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

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Kelola Keyword</h1>

    <!-- DataTables Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahJurusan">
                    <i class="fas fa-plus"></i> Tambah Keyword
                </button>

                <!-- Modal Tambah -->
                <div class="modal fade" id="modalTambahJurusan" tabindex="-1" role="dialog" aria-labelledby="modalTambahJurusanLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="<?= base_url('admin/keyword/save') ?>" method="post">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="modalTambahJurusanLabel">Tambah Keyword</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="keyword_id">Keyword</label>
                                        <input type="text" class="form-control" id="keyword_nama" name="keyword_nama" required>
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
                
                <div class="d-flex align-items-center mb-3">
                    <form method="get" class="d-flex align-items-center mb-0" style="margin-right: 15px;">
                        <select name="status" class="form-control" style="width: 200px; margin-right: 10px;">
                            <option value="">-- Pilih Status --</option>
                            <option value="approved">Approved</option>
                            <option value="waiting">Waiting</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>

                    <a href="<?= base_url('admin/export-keyword?status=' . service('request')->getGet('status')) ?>" 
                    class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                </div>      

                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Keyword</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($keyword) && is_array($keyword)) : ?>
                            <?php $no = 1; foreach ($keyword as $data) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($data['keyword_nama']) ?></td>
                                    <td>
                                        <?php if ($data['status'] == 'approved'): ?>
                                            Approved
                                        <?php elseif ($data['status'] == 'waiting'): ?>
                                            Waiting
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm" title="Edit" data-toggle="modal" data-target="#editModal<?= $data['keyword_id']; ?>">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" title="Delete" data-toggle="modal" data-target="#deleteModal<?= $data['keyword_id']; ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Edit Jurusan -->
                                <div class="modal fade" id="editModal<?= $data['keyword_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $data['keyword_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="<?= base_url('admin/keyword/update/' . $data['keyword_id']); ?>" method="post">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title" id="editModalLabel<?= $data['keyword_id']; ?>">Edit keyword</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="keyword_id_<?= $data['keyword_id']; ?>">Keyword</label>
                                                        <input type="text" class="form-control" name="keyword_nama" id="keyword_nama<?= $data['keyword_id']; ?>" value="<?= esc($data['keyword_nama']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="status">Status keyword</label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="" disabled>---Pilih Status---</option>
                                                            <option value="approved" <?= ($data['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                                                            <option value="waiting" <?= ($data['status']  == 'waiting') ? 'selected' : '' ?>>Waiting</option>
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
                                <!-- Modal Delete Jurusan -->
                                <div class="modal fade" id="deleteModal<?= $data['keyword_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?= $data['keyword_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="<?= base_url('admin/keyword/delete/' . $data['keyword_id']); ?>" method="post">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteModalLabel<?= $data['keyword_id']; ?>">Konfirmasi Hapus</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus keyword <strong><?= esc($data['keyword_nama']); ?></strong>?
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
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

<?= $this->endSection(); ?>
