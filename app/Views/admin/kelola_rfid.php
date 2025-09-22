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
    <h1 class="h3 mb-2 text-gray-800">Kelola RFID</h1>

    <!-- DataTables Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahJurusan">
                    <i class="fas fa-plus"></i> Tambah RFID
                </button>

                <!-- Modal Tambah Jurusan -->
                <div class="modal fade" id="modalTambahJurusan" tabindex="-1" role="dialog" aria-labelledby="modalTambahJurusanLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="<?= base_url('admin/rfid/save') ?>" method="post">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="modalTambahJurusanLabel">Tambah RFID</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="rfid_no">No RFID</label>
                                        <input type="text" class="form-control" id="rfid_no" name="rfid_no" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rfid_no">Status RFID</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="" disabled selected>---Pilih Status---</option>
                                            <option value="available">Tersedia</option>
                                            <option value="assigned">Digunakan</option>
                                            <option value="lost">Hilang</option>
                                            <option value="inactive">Tidak Tersedia</option>
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
                            <th>No. RFID</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rfid) && is_array($rfid)) : ?>
                            <?php $no = 1; foreach ($rfid as $data) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($data['rfid_no']) ?></td>
                                    <td>
                                        <?php if ($data['status'] == 'available'): ?>
                                            Tersedia
                                        <?php elseif ($data['status'] == 'assigned'): ?>
                                            Digunakan
                                        <?php elseif ($data['status'] == 'lost'): ?>
                                            Hilang
                                        <?php elseif ($data['status'] == 'inactive'): ?>
                                            Tidak Tersedia
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $data['id_rfid']; ?>">
                                            Edit
                                        </button>
                                        <!-- <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?= $data['id_rfid']; ?>">
                                            Delete
                                        </button> -->
                                    </td>
                                </tr>

                                <!-- Modal Edit Jurusan -->
                                <div class="modal fade" id="editModal<?= $data['id_rfid']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $data['id_rfid']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="<?= base_url('admin/rfid/update/' . $data['id_rfid']); ?>" method="post">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title" id="editModalLabel<?= $data['id_rfid']; ?>">Edit RFID</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="rfid_no_<?= $data['id_rfid']; ?>">No. RFID</label>
                                                        <input type="text" class="form-control" name="rfid_no" id="rfid_no<?= $data['id_rfid']; ?>" value="<?= esc($data['rfid_no']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="status">Status RFID</label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="" disabled>---Pilih Status---</option>
                                                            <option value="available" <?= ($data['status'] == 'available') ? 'selected' : '' ?>>Tersedia</option>
                                                            <option value="assigned" <?= ($data['status']  == 'assigned') ? 'selected' : '' ?>>Digunakan</option>
                                                            <option value="lost" <?= ($data['status']  == 'lost') ? 'selected' : '' ?>>Hilang</option>
                                                            <option value="inactive" <?= ($data['status']  == 'inactive') ? 'selected' : '' ?>>Tidak Tersedia</option>
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
                                <div class="modal fade" id="deleteModal<?= $data['id_rfid']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?= $data['id_rfid']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="<?= base_url('rfid/delete/' . $data['id_rfid']); ?>" method="post">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteModalLabel<?= $data['id_rfid']; ?>">Konfirmasi Hapus</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus RFID <strong><?= esc($data['rfid_no']); ?></strong>?
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
