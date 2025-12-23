<?= $this->extend('user/template'); ?>
<?= $this->section('main-content'); ?>

<?php if (session()->getFlashdata('success')): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Sukses',
    text: '<?= session()->getFlashdata('success') ?>',
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php elseif (session()->getFlashdata('error')): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= session()->getFlashdata('error') ?>'
});
</script>
<?php endif; ?>

<!-- Tabs Lamaran -->
<div class="profile-card">
    <ul class="nav nav-tabs profile-tabs mb-4" id="lamaranTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="status-lamaran-tab" data-bs-toggle="tab" data-bs-target="#pelaksanaan" type="button" role="tab">
                Unggah Form Pengambilan Data
            </button>
        </li>
    </ul>
    <p class="text-muted">Unggah form pengambilan data kamu di sini.</p>
    <hr>

    <?php if (!empty($pendaftaran) && ($pendaftaran['status_akhir'] === 'penelitian' || $pendaftaran['status_akhir'] === 'lulus')): ?>

    <?php
        $sudahUpload = !empty($pendaftaran['formulir_penelitian']) || !empty($pendaftaran['absensi']);
    ?>

    <!-- Tombol Edit / Upload -->
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpload">
            <?php if ($sudahUpload): ?>
                <i class="bi bi-pencil-square me-1"></i> Edit Upload
            <?php else: ?>
                <i class="bi bi-upload me-1"></i> Upload Berkas
            <?php endif; ?>
        </button>
    </div>

    <?php if (!$sudahUpload): ?>
        <div class="alert alert-warning text-center">
            <i class="bi bi-info-circle me-1"></i> Belum ada formulir penelitian & absensi yang diunggah.
        </div>
    <?php else: ?>
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <?php if (!empty($pendaftaran['formulir_penelitian'])): ?>
                <p>
                    <i class="bi bi-file-earmark-pdf text-danger"></i> Formulir Pengambilan Data:
                    <a href="<?= base_url('uploads/form-penelitian/' . $pendaftaran['formulir_penelitian']) ?>" target="_blank">Lihat file</a>
                </p>
                <?php endif; ?>

                <?php if (!empty($pendaftaran['absensi'])): ?>
                <p>
                    <i class="bi bi-journal-check text-success"></i> Absensi:
                    <a href="<?= base_url('uploads/absensi-penelitian/' . $pendaftaran['absensi']) ?>" target="_blank">Lihat file</a>
                </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== MODAL UPLOAD / EDIT ==================== -->
    <div class="modal fade" id="modalUpload" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="<?= base_url('unggah-form-penelitian/' . $pendaftaran['penelitian_id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <?= $sudahUpload ? 'Edit Formulir & Absensi Penelitian' : 'Upload Formulir & Absensi Penelitian' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">📄 Formulir Pengambilan Data (PDF, max 2 MB)</label>
                            <input type="file" name="formData" class="form-control" accept="application/pdf">
                            <small class="text-danger">* wajib ada tanda tangan pembimbing min Band 3</small>

                            <?php if (!empty($pendaftaran['formulir_penelitian'])): ?>
                                <a href="<?= base_url('uploads/form-penelitian/' . $pendaftaran['formulir_penelitian']) ?>" target="_blank" class="d-block mt-2">Lihat file sebelumnya</a>
                            <?php endif; ?>

                            <?php if(!empty($pendaftaran['catatan_formulir'])): ?>
                                <div class="alert alert-danger mt-2">Catatan: <?= esc($pendaftaran['catatan_formulir']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">🗒️ Absensi Magang (PDF, max 2 MB)</label>
                            <input type="file" name="absensi" class="form-control" accept="application/pdf">

                            <?php if (!empty($pendaftaran['absensi'])): ?>
                                <a href="<?= base_url('uploads/absensi-penelitian/' . $pendaftaran['absensi']) ?>" target="_blank" class="d-block mt-2">Lihat file sebelumnya</a>
                            <?php endif; ?>

                            <?php if(!empty($pendaftaran['catatan_absensi'])): ?>
                                <div class="alert alert-danger mt-2">Catatan: <?= esc($pendaftaran['catatan_absensi']) ?></div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ==================== AKHIR MODAL ==================== -->

    <?php else: ?>
        <div class="alert alert-warning"><i class="bi bi-exclamation-circle me-1"></i> Kamu belum menyelesaikan pendaftaran penelitian.</div>
    <?php endif; ?>



</div>

<?= $this->endSection(); ?>
