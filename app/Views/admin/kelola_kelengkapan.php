<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>
<style>
    /* Ubah warna checkbox saat dicentang */
    .form-check-input:checked {
        background-color: #28a745; /* hijau */
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
<h1 class="h3 mb-4 text-gray-800">Validasi Kelengkapan Berkas</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Tanggal Selesai</th>
                            <th>BPJS Kes</th>
                            <th>BPJS TK</th>
                            <th>Bukti Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data)): ?>
                            <?php foreach ($data as $item): ?>
                                <?php $id = $item['magang_id']; ?>
                                <tr>
                                    <td><?= esc($item['fullname']) ?></td>
                                    <td><?= esc($item['nisn_nim']) ?></td>
                                    <td><?= esc(format_tanggal_indonesia($item['tanggal_selesai'])) ?></td>

                                    <!-- BPJS Kes -->
                                    <td>
                                        <?php if ($item['bpjs_kes']): ?>
                                            <input type="checkbox" class="form-check-input checkbox-berkas" data-id="<?= $id ?>" data-type="kes">
                                            <a href="<?= base_url('uploads/bpjs-kes/'.$item['bpjs_kes']) ?>" target="_blank">Lihat</a>
                                        <?php else: ?>
                                            Belum Ada
                                        <?php endif; ?>
                                    </td>

                                    <!-- BPJS TK -->
                                    <td>
                                        <?php if ($item['bpjs_tk']): ?>
                                            <input type="checkbox" class="form-check-input checkbox-berkas" data-id="<?= $id ?>" data-type="tk">
                                            <a href="<?= base_url('uploads/bpjs-tk/'.$item['bpjs_tk']) ?>" target="_blank">Lihat</a>
                                        <?php else: ?>
                                            Belum Ada
                                        <?php endif; ?>
                                    </td>

                                    <!-- Bukti Pembayaran -->
                                    <td>
                                        <?php if ($item['buktibpjs_tk']): ?>
                                            <input type="checkbox" class="form-check-input checkbox-berkas" data-id="<?= $id ?>" data-type="bukti">
                                            <a href="<?= base_url('uploads/buktibpjs-tk/'.$item['buktibpjs_tk']) ?>" target="_blank">Lihat</a>
                                        <?php else: ?>
                                            Belum Ada
                                        <?php endif; ?>
                                    </td>

                                    <!-- Tombol aksi -->
                                    <td>
                                       <!-- Form untuk Approve -->
                                        <form action="<?= base_url('admin/manage-kelengkapan-berkas/valid/'.$id) ?>" method="post" class="d-inline" onsubmit="return confirmApprove()">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-secondary btn-approve" id="approve<?= $id ?>" disabled>Approve</button>
                                        </form>

                                        <!-- Form untuk Tidak Approve -->
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalTolak<?= $id ?>">
                                            Tidak Approve
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modalTolak<?= $id ?>" tabindex="-1" aria-labelledby="modalTolakLabel<?= $id ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="<?= base_url('admin/manage-kelengkapan-berkas/tidakValid/'.$id) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalTolakLabel<?= $id ?>">Alasan Tidak Approve</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="catatan<?= $id ?>">Catatan</label>
                                                                <textarea name="catatan" id="catatan<?= $id ?>" class="form-control" rows="3" placeholder="Wajib diisi" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Kirim Tidak Approve</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<script>
function confirmApprove() {
    return confirm("Apakah Anda yakin ingin Approve?");
}
</script>

<script>
    document.querySelectorAll('.checkbox-berkas').forEach(cb => {
        cb.addEventListener('change', function() {
            const id = this.dataset.id;
            const row = this.closest('tr');
            const allCheckboxes = row.querySelectorAll('.checkbox-berkas');
            const approveBtn = document.getElementById('approve' + id);

            const allChecked = Array.from(allCheckboxes).every(box => box.checked);

            if (allChecked) {
                approveBtn.disabled = false;
                approveBtn.classList.remove('btn-secondary');
                approveBtn.classList.add('btn-success');
            } else {
                approveBtn.disabled = true;
                approveBtn.classList.remove('btn-success');
                approveBtn.classList.add('btn-secondary');
            }
        });
    });
</script>


<?= $this->endSection(); ?>
