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
    <?php
        $validStatus = !empty($pendaftaran)
            && in_array($pendaftaran['status_akhir'], ['magang', 'lulus']);

        $today = new DateTime(date('Y-m-d'));
        $tglSelesai = new DateTime($pendaftaran['tanggal_selesai']);
        $selisihHari = $tglSelesai->diff($today)->days;
        $lewat15Hari = ($today > $tglSelesai && $selisihHari > 15);

        $allowUploadAdmin = $pendaftaran['allow_upload_laporan'] ?? 0;

        $sudahUpload =
            !empty($pendaftaran['judul_laporan']) ||
            !empty($pendaftaran['laporan']) ||
            !empty($pendaftaran['url_laporan']) ||
            !empty($pendaftaran['absensi']) ||
            !empty($pendaftaran['url_absensi']);

        $bolehUpload =
            date('Y-m-d') >= $pendaftaran['tanggal_selesai']
            && (!$lewat15Hari || $allowUploadAdmin);
    ?>

    <?php if ($validStatus): ?>

        <?php if ($sudahUpload): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">

                    <?php if (!empty($pendaftaran['judul_laporan'])): ?>
                        <p><strong>Judul Laporan:</strong><br><?= esc($pendaftaran['judul_laporan']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($pendaftaran['laporan'])): ?>
                        <p>
                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                            Laporan:
                            <a href="<?= base_url('uploads/laporan/' . $pendaftaran['laporan']) ?>" target="_blank">
                                Lihat file
                            </a>
                        </p>
                    <?php elseif (!empty($pendaftaran['url_laporan'])): ?>
                        <p>
                            <i class="bi bi-link-45deg"></i>
                            Laporan:
                            <a href="<?= esc($pendaftaran['url_laporan']) ?>" target="_blank">
                                Lihat file
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($pendaftaran['absensi'])): ?>
                        <p>
                            <i class="bi bi-journal-check text-success"></i>
                            Absensi:
                            <a href="<?= base_url('uploads/absensi/' . $pendaftaran['absensi']) ?>" target="_blank">
                                Lihat file
                            </a>
                        </p>
                    <?php elseif (!empty($pendaftaran['url_absensi'])): ?>
                        <p>
                            <i class="bi bi-link-45deg"></i>
                            Absensi:
                            <a href="<?= esc($pendaftaran['url_absensi']) ?>" target="_blank">
                                Lihat file
                            </a>
                        </p>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if ($bolehUpload): ?>
            <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpload">
                    <?php if ($sudahUpload): ?>
                        <i class="bi bi-pencil-square me-1"></i> Edit Upload
                    <?php else: ?>
                        <i class="bi bi-upload me-1"></i> Upload Berkas
                    <?php endif; ?>
                </button>
            </div>

            <div class="modal fade" id="modalUpload" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="<?= base_url('unggah-laporan/' . $pendaftaran['magang_id']) ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <?= $sudahUpload ? 'Edit Laporan & Absensi Magang' : 'Upload Laporan & Absensi Magang' ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">Judul Laporan <span class="text-danger">*</span></label>
                                    <input type="text" name="judul" class="form-control"
                                        value="<?= esc($pendaftaran['judul_laporan'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">📄 Laporan (PDF)</label>
                                    <input type="file" name="laporan" class="form-control" accept="application/pdf">
                                    <small>Atau URL Laporan</small>
                                    <input type="text" name="url_laporan" class="form-control">
                                    <?php if (!empty($pendaftaran['catatan_laporan'])): ?>
                                        <div class="alert alert-danger mt-2">
                                            Catatan: <?= esc($pendaftaran['catatan_laporan']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">🗒️ Absensi (PDF)</label>
                                    <input type="file" name="absensi" class="form-control" accept="application/pdf">
                                    <small>Atau URL Absensi</small>
                                    <input type="text" name="url_absensi" class="form-control">
                                    <?php if (!empty($pendaftaran['catatan_absensi'])): ?>
                                        <div class="alert alert-danger mt-2">
                                            Catatan: <?= esc($pendaftaran['catatan_absensi']) ?>
                                        </div>
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

        <?php elseif ($lewat15Hari && !$allowUploadAdmin): ?>
            <div class="alert alert-danger text-center">
                <i class="bi bi-lock-fill me-1"></i>
                Upload laporan & absensi telah ditutup (15 hari setelah magang selesai).<br>
                Data yang sudah diunggah tetap dapat dilihat.<br>
                Silakan hubungi admin untuk membuka kembali akses.
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Masa magang anda belum berakhir. Anda belum bisa mengupload Laporan dan Absensi Magang.
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-circle me-1"></i>
            Kamu belum menyelesaikan pendaftaran magang.
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection(); ?>
