<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>
<style>
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
    .form-check-input:focus {
        box-shadow: none;
        border-color: #28a745;
    }
</style>

<div class="container-fluid">
<?php $session = \Config\Services::session(); ?>
<?php if ($session->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $session->getFlashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<?php if ($session->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $session->getFlashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<h1 class="h3 mb-4 text-gray-800">Validasi Konfirmasi</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/manage-validasi-konfirmasi/bulk') ?>" method="post" id="bulkForm">
            <?= csrf_field() ?>
            <div class="mb-3">
                <button type="submit" name="action" value="approve" class="btn btn-success" onclick="return confirm('Yakin ingin APPROVE peserta terpilih? Email akan dikirim otomatis.')">Approve Terpilih</button>
                <button type="submit" id="btnRejectBulk" name="action" value="reject" class="btn btn-danger">Kirim Tidak Approve</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Status Konfirmasi</th>
                            <th>Tanggal Konfirmasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data)): ?>
                            <?php foreach ($data as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['status_konfirmasi'] === 'Y'): ?>
                                            <input type="checkbox" name="ids[]" value="<?= esc($item['magang_id']) ?>" class="checkbox-pendaftar">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item['fullname']) ?></td>
                                    <td><?= esc($item['nisn_nim']) ?></td>
                                    <td><?= $item['status_konfirmasi'] === 'Y' ? 'Terkonfirmasi' : 'Tidak Konfirmasi' ?></td>
                                    <td><?= esc($item['tanggal_konfirmasi']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal Tidak Approve Bulk -->
            <div class="modal fade" id="modalTolakBulk" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Alasan Tidak Approve</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="catatan_bulk" class="form-control" rows="3" placeholder="Wajib diisi"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger">Kirim Tidak Approve</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function(e) {
        document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = e.target.checked);
    });
    document.getElementById('btnRejectBulk').addEventListener('click', function() {
        document.getElementById('catatan_bulk').setAttribute('required', 'required');
    });
</script>

<?= $this->endSection(); ?>
