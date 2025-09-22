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
    <h1 class="h3 mb-2 text-gray-800">Kelola Perguruan Tinggi/Sekolah</h1>

    <!-- DataTables Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahInstansi">
                    <i class="fas fa-plus"></i> Tambah Perguruan Tinggi/Sekolah
                </button>

                <!-- Modal Tambah Instansi -->
                <div class="modal fade" id="modalTambahInstansi" tabindex="-1" role="dialog" aria-labelledby="modalTambahInstansiLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="<?= base_url('admin/instansi/save') ?>" method="post">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="modalTambahInstansiLabel">Tambah Perguruan Tinggi/Sekolah</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="tingkat">Tingkat Perguruan Tinggi/Sekolah</label>
                                        <select name="tingkat" id="tingkat" class="form-control" required>
                                            <option value="" hidden selected>Pilih Tingkat Perguruan Tinggi/Sekolah</option>
                                            <option value="smk">Sekolah Menengah Kejuruan (SMK)</option>
                                            <option value="pt">Perguruan Tinggi</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="nama_instansi">Nama Perguruan Tinggi/Sekolah</label>
                                        <input type="text" class="form-control" id="nama_instansi" name="nama_instansi" required>
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

                <form method="get" action="<?= base_url('admin/kelola-instansi'); ?>" class="form-inline mb-3">
                    <label for="filter_tingkat" class="mr-2">Filter Tingkat:</label>
                    <select name="tingkat" id="filter_tingkat" class="form-control mr-2">
                        <option value="">-- Semua --</option>
                        <option value="smk" <?= (isset($_GET['tingkat']) && $_GET['tingkat'] === 'smk') ? 'selected' : ''; ?>>SMK</option>
                        <option value="pt" <?= (isset($_GET['tingkat']) && $_GET['tingkat'] === 'pt') ? 'selected' : ''; ?>>Perguruan Tinggi</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </form>

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Perguruan Tinggi/Sekolah</th>
                            <th>Tingkat</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($instansi) && is_array($instansi)) : ?>
                            <?php $no = 1; foreach ($instansi as $data) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($data['nama_instansi']); ?></td>
                                    <td>
                                        <?= ($data['tingkat'] === 'smk') ? 'Sekolah Menengah Kejuruan (SMK)' : 'Perguruan Tinggi (PT)'; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $data['instansi_id']; ?>">
                                            Edit
                                        </button>
                                        <!-- <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?= $data['instansi_id']; ?>">
                                            Delete
                                        </button> -->
                                    </td>
                                </tr>

                                <!-- Modal Edit Instansi -->
                                <div class="modal fade" id="editModal<?= $data['instansi_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $data['instansi_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="<?= base_url('admin/instansi/update/' . $data['instansi_id']); ?>" method="post">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title" id="editModalLabel<?= $data['instansi_id']; ?>">Edit Perguruan Tinggi/Sekolah</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="tingkat_<?= $data['instansi_id']; ?>">Tingkat Instansi</label>
                                                        <select name="tingkat" id="tingkat_<?= $data['instansi_id']; ?>" class="form-control" required>
                                                            <option value="" disabled <?= empty($data['tingkat']) ? 'selected' : ''; ?>>Pilih Tingkat Perguruan Tinggi/Sekolah</option>
                                                            <option value="smk" <?= ($data['tingkat'] ?? '') === 'smk' ? 'selected' : ''; ?>>Sekolah Menengah Kejuruan (SMK)</option>
                                                            <option value="pt" <?= ($data['tingkat'] ?? '') === 'pt' ? 'selected' : ''; ?>>Perguruan Tinggi</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="nama_instansi_<?= $data['instansi_id']; ?>">Nama Perguruan Tinggi/Sekolah</label>
                                                        <input type="text" class="form-control" name="nama_instansi" id="nama_instansi_<?= $data['instansi_id']; ?>" value="<?= esc($data['nama_instansi']); ?>" required>
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
                                <!-- Modal Delete Instansi -->
                                <div class="modal fade" id="deleteModal<?= $data['instansi_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?= $data['instansi_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="<?= base_url('instansi/delete/' . $data['instansi_id']); ?>" method="post">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteModalLabel<?= $data['instansi_id']; ?>">Konfirmasi Hapus</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus instansi <strong><?= esc($data['nama_instansi']); ?></strong>?
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
