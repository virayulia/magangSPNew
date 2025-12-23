Lewati ke konten
Riwayat obrolan


Anda bilang:
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
        <?php if ($pendaftaran['status_akhir'] === 'magang' || $pendaftaran['status_akhir'] === 'lulus'): ?>
        <div class="tab-content" id="lamaranTabContent">
            <div class="tab-pane fade show active" id="pelaksanaan" role="tabpanel">

                <!-- Unggah Laporan & Absensi Magang -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-file-earmark-arrow-up me-2"></i> Unggah Laporan & Absensi Magang</h5>

                        <form action="<?= base_url('unggah-laporan/'. $pendaftaran['magang_id']) ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Laporan Magang <small class="text-danger">(sesuai dengan yang tertulis pada laporan magang)</small></label>
                                <small class="text-danger">*</small>
                                <input type="text" name="judul" id="judul" class="form-control" required>
                                
                                <?php if (!empty($pendaftaran['laporan'])): ?>
                                    <div class="mt-2">
                                        <?php if (!empty($pendaftaran['judul_laporan'])): ?>
                                            <p class="mb-1">
                                                <strong>Judul Laporan:</strong> 
                                                <?= esc($pendaftaran['judul_laporan']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
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
            Belum Ada Pelaksanaan Magang.
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>
perbaiki halaman upload ini, kalau belum upload apa2 tampil pesan belum ada laporan dan absensi yang diupload trus tombol upload keluar modal upload kayak form yang udah ada diatas, kemudian upload. setelah ada data yang diupload tampilkan dihalaman itu kemudian beri tombol edit di kanan atas  dengan icon pencil gitu terus kalau edit tetap pakai modal tambah tadi juga
ChatGPT bilang:
Berikut aku buatkan versi halaman upload laporan sesuai permintaan:

✔ Jika belum ada laporan dan absensi → tampil pesan “Belum ada laporan & absensi yang diupload” + tombol Upload → membuka modal form upload.
✔ Jika sudah ada laporan/absensi → tampil datanya di halaman + tombol Edit (ikon pencil) di kanan atas → membuka modal form yang sama untuk update.

✅ KODE LENGKAP YANG SUDAH DIBENAHI
Tinggal salin tempel dan file akan langsung bekerja selama route / controller sudah siap.

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

<div class="profile-card">
    <h5 class="mb-3">Unggah Laporan Magang</h5>
    <p class="text-muted">Unggah laporan dan absensi magang kamu di sini.</p>
    <hr>

    <?php if (!empty($pendaftaran) && ($pendaftaran['status_akhir'] === 'magang' || $pendaftaran['status_akhir'] === 'lulus')): ?>

    <?php
        $sudahUpload = !empty($pendaftaran['laporan']) || !empty($pendaftaran['absensi']);
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
            <i class="bi bi-info-circle me-1"></i> Belum ada laporan & absensi yang diunggah.
        </div>
    <?php else: ?>
        <!-- Tampilkan data laporan & absensi jika sudah diupload -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <?php if (!empty($pendaftaran['judul_laporan'])): ?>
                <p><strong>Judul Laporan:</strong> <?= esc($pendaftaran['judul_laporan']) ?></p>
                <?php endif; ?>

                <?php if (!empty($pendaftaran['laporan'])): ?>
                <p>
                    <i class="bi bi-file-earmark-pdf text-danger"></i> Laporan:
                    <a href="<?= base_url('uploads/laporan/' . $pendaftaran['laporan']) ?>" target="_blank">Lihat file</a>
                </p>
                <?php endif; ?>

                <?php if (!empty($pendaftaran['absensi'])): ?>
                <p>
                    <i class="bi bi-journal-check text-success"></i> Absensi:
                    <a href="<?= base_url('uploads/absensi/' . $pendaftaran['absensi']) ?>" target="_blank">Lihat file</a>
                </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== MODAL UPLOAD / EDIT ==================== -->
    <div class="modal fade" id="modalUpload" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="<?= base_url('unggah-laporan/' . $pendaftaran['magang_id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $sudahUpload ? 'Edit Laporan & Absensi Magang' : 'Upload Laporan & Absensi Magang' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Laporan Magang <small class="text-danger">*</small></label>
                            <input type="text" name="judul" id="judul" class="form-control"
                                value="<?= esc($pendaftaran['judul_laporan'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">📄 Laporan Magang (PDF, max 10 MB)</label>
                            <input type="file" name="laporan" class="form-control" accept="application/pdf">
                            <small class="text-danger">* wajib ada tanda tangan pembimbing min Band 3</small>

                            <?php if (!empty($pendaftaran['laporan'])): ?>
                                <a href="<?= base_url('uploads/laporan/' . $pendaftaran['laporan']) ?>" target="_blank" class="d-block mt-2">Lihat file sebelumnya</a>
                            <?php endif; ?>

                            <?php if(!empty($pendaftaran['catatan_laporan'])): ?>
                                <div class="alert alert-danger mt-2">Catatan: <?= esc($pendaftaran['catatan_laporan']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">🗒️ Absensi Magang (PDF, max 2 MB)</label>
                            <input type="file" name="absensi" class="form-control" accept="application/pdf">

                            <?php if (!empty($pendaftaran['absensi'])): ?>
                                <a href="<?= base_url('uploads/absensi/' . $pendaftaran['absensi']) ?>" target="_blank" class="d-block mt-2">Lihat file sebelumnya</a>
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
        <div class="alert alert-warning"><i class="bi bi-exclamation-circle me-1"></i> Kamu belum menyelesaikan pendaftaran magang.</div>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>

