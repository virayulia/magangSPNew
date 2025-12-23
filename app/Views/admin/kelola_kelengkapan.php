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
    .aksi-wrapper {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .aksi-wrapper form {
        display: inline;
        margin: 0;
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
<div class="mb-2">
    <?php if(!empty($statusBerkas)): ?>
        <div class="mb-4">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <a href="<?= base_url('admin/manage-kelengkapan-berkas') ?>" class="text-decoration-none">
                        <div class="card border-left-secondary shadow h-100 py-2 small-card">
                            <div class="card-body py-2 text-center">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Semua Berkas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $statusBerkas['lengkap']+$statusBerkas['tk_bukti_saja']+$statusBerkas['belum'] ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-3">
                    <a href="<?= base_url('admin/manage-kelengkapan-berkas?filter=lengkap') ?>" class="text-decoration-none">
                        <div class="card border-left-success shadow h-100 py-2 small-card">
                            <div class="card-body py-2 text-center">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Berkas Lengkap
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $statusBerkas['lengkap'] ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-3">
                    <a href="<?= base_url('admin/manage-kelengkapan-berkas?filter=tk_bukti_saja') ?>" class="text-decoration-none">
                        <div class="card border-left-primary shadow h-100 py-2 small-card">
                            <div class="card-body py-2 text-center">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    BPJS TK + Bukti
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $statusBerkas['tk_bukti_saja'] ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-3">
                    <a href="<?= base_url('admin/manage-kelengkapan-berkas?filter=belum') ?>" class="text-decoration-none">
                        <div class="card border-left-danger shadow h-100 py-2 small-card">
                            <div class="card-body py-2 text-center">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Belum Lengkap
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $statusBerkas['belum'] ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    <?php endif; ?>
</div>



<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable">
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
                                    <div class="aksi-wrapper">
                                        <!-- Form untuk Approve -->
                                        <form action="<?= base_url('admin/manage-kelengkapan-berkas/valid/'.$id) ?>" method="post" class="d-inline form-approve">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-secondary btn-approve" title="Approve" id="approve<?= $id ?>" disabled><i class="fas fa-check-circle"></i></button>
                                        </form>

                                        <!-- Form untuk Tidak Approve -->
                                        <button type="button" class="btn btn-sm btn-danger" title="Tolak Berkas" data-toggle="modal" data-target="#modalTolak<?= $id ?>">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </div>

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




<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
document.addEventListener('submit', function (e) {
    if (e.target.classList.contains('form-approve')) {
        e.preventDefault();

        Swal.fire({
            title: 'Approve Berkas?',
            text: 'Pastikan semua berkas sudah benar dan lengkap.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Approve',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
});

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
