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
                Unggah Laporan Magang
            </button>
        </li>
    </ul>
    <p class="text-muted">Unggah laporan magang kamu di sini.</p>
    <hr>
    <?php if (!empty($pendaftaran)): ?>
        <?php if($pendaftaran['status_akhir'] === 'magang'): ?>
        <div class="tab-content" id="lamaranTabContent">
            <div class="tab-pane fade show active" id="pelaksanaan" role="tabpanel">

                <!-- Unggah Laporan & Absensi Magang -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-file-earmark-arrow-up me-2"></i> Unggah Laporan & Absensi Magang</h5>

                        <form action="<?= base_url('unggah-laporan/'. $pendaftaran['magang_id']) ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="laporan" class="form-label">📄 Laporan Magang (PDF, max 10 MB)</label>
                                <br><small class="text-danger">*Laporan yang diunggah wajib dilengkapi tanda tangan pembimbing, minimal setingkat Band 3.</small>
                                <input type="file" name="laporan" id="laporan" class="form-control" 
                                    accept="application/pdf">
                                
                                <?php if (!empty($pendaftaran['laporan'])): ?>
                                    <div class="mt-2">
                                        <a href="<?= base_url('uploads/laporan/' . $pendaftaran['laporan']) ?>" 
                                        target="_blank" class="btn btn-sm btn-success">
                                        <i class="bi bi-eye me-1"></i> Lihat Laporan
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?php if(!empty($pendaftaran['catatan_laporan'])): ?>
                                        <div class="alert alert-danger mt-2"> Catatan : <?= esc($pendaftaran['catatan_laporan']); ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="absensi" class="form-label">🗒️ Absensi Magang (PDF, max 2 MB)</label>
                                <input type="file" name="absensi" id="absensi" class="form-control" 
                                    accept="application/pdf">

                                <?php if (!empty($pendaftaran['absensi'])): ?>
                                    <div class="mt-2">
                                        <a href="<?= base_url('uploads/absensi/' . $pendaftaran['absensi']) ?>" 
                                        target="_blank" class="btn btn-sm btn-success">
                                        <i class="bi bi-eye me-1"></i> Lihat Absensi
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?php if(!empty($pendaftaran['catatan_absensi'])): ?>
                                        <div class="alert alert-danger mt-2"> Catatan : <?= esc($pendaftaran['catatan_absensi']); ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload me-1"></i> Unggah Berkas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning"><i class="fas fa-exclamation-circle me-1"></i> Kamu belum menyelesaikan <strong>Pendaftaran Magang</strong>, sehingga belum bisa mengakses informasi pelaksanaan magang.</div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Belum ada Pelaksanaan Magang.
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>
