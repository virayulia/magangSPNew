<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>
<style>
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
    background-color: #4e73df; /* Sesuaikan warna dengan theme SB Admin */
    border: none;
    color: white;
    padding: 0.25rem 0.5rem;
    font-size: 0.85rem;
    border-radius: 0.35rem;
    margin-top: 0.3rem;
}
/* Hapus border dan ubah warna ikon "x" */
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
    color: #4e73df; /* ubah warna ikon "x" */
    border: none;
    margin-right: 4px;
    font-weight: bold;
    padding: 0 5px;
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #ffc107; /* saat hover, bisa pakai warna kuning atau lain */
    background-color: transparent;
}


</style>
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

    <h1 class="h3 mb-2 text-gray-800">Kelola Jurusan Unit</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahJurusanUnit">
                    <i class="fas fa-plus"></i> Tambah Jurusan Unit
                </button>

                <!-- Modal Tambah -->
                <div class="modal fade" id="modalTambahJurusanUnit" tabindex="-1" role="dialog" aria-labelledby="modalTambahJurusanUnitLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="<?= base_url('admin/jurusan-unit/save') ?>" method="post">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="modalTambahJurusanUnitLabel">Tambah Jurusan Unit</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                
                                <div class="modal-body">

                                    <div class="form-group">
                                        <label for="kuota_unit_id">Pilih Kuota Unit (Unit & Tingkat Pendidikan)</label>
                                        <select name="kuota_unit_id" id="kuota_unit_id" class="form-control select2" required>
                                            <option value="" disabled selected>Pilih Kuota Unit</option>
                                            <?php foreach ($kuota_units as $ku): ?>
                                                <?php 
                                                    $unitName = '';
                                                    foreach ($units as $u) {
                                                        if ($u['unit_id'] == $ku['unit_id']) {
                                                            $unitName = $u['unit_kerja'];
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <option value="<?= $ku['kuota_unit_id']; ?>">
                                                    <?= $unitName; ?> - <?= $ku['tingkat_pendidikan']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="jurusan_id">Pilih Jurusan</label>
                                        <select class="form-control select2" multiple="multiple" name="jurusan_id[]" id="jurusan_id">
                                            <?php foreach ($jurusans as $j): ?>
                                                <option value="<?= $j['jurusan_id']; ?>"><?= $j['nama_jurusan']; ?></option>
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

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Unit Kerja</th>
                            <th>Tingkat Pendidikan</th>
                            <th>Jurusan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($grouped_jurusan_unit as $key => $ju): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= esc($ju['unit_kerja']); ?></td>
                                <td><?= esc($ju['tingkat_pendidikan']); ?></td>
                                <td>
                                    <?php 
                                        // Ambil hanya kolom 'nama'
                                        $jurusan_nama = array_column($ju['jurusans'], 'nama_jurusan');
                                        echo implode(', ', $jurusan_nama);
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $ju['kuota_unit_id']; ?>">
                                        Edit
                                    </button>
                                </td>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="editModal<?= $ju['kuota_unit_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $ju['kuota_unit_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title" id="editModalLabel<?= $ju['kuota_unit_id']; ?>">Edit Jurusan Unit</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <h6><strong>Unit Kerja:</strong> <?= esc($ju['unit_kerja']); ?></h6>
                                                <h6><strong>Tingkat Pendidikan:</strong> <?= esc($ju['tingkat_pendidikan']); ?></h6>
                                                <hr>

                                                <h6>Jurusan Saat Ini:</h6>
                                                <ul class="list-group mb-3">
                                                    <?php foreach ($ju['jurusans'] as $jurusan): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <?= esc($jurusan['nama_jurusan']); ?>
                                                            <form action="<?= base_url('admin/jurusan-unit/deleteJurusan/' . $jurusan['jurusan_unit_id']); ?>" method="post" onsubmit="return confirm('Hapus jurusan ini?');">
                                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                            </form>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>

                                                <h6>Tambah Jurusan Baru:</h6>
                                                <form action="<?= base_url('admin/jurusan-unit/addJurusan') ?>" method="post">
                                                    <input type="hidden" name="kuota_unit_id" value="<?= $ju['kuota_unit_id']; ?>">
                                                    <div class="form-group">
                                                        <label for="jurusan_id_<?= $ju['kuota_unit_id']; ?>">Pilih Jurusan</label>
                                                        <select name="jurusan_id[]" id="jurusan_id_<?= $ju['kuota_unit_id']; ?>" class="form-control select2" multiple required>
                                                            <?php foreach ($jurusans as $j): ?>
                                                                <option value="<?= $j['jurusan_id']; ?>"><?= $j['nama_jurusan']; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-success">Tambah Jurusan</button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>
