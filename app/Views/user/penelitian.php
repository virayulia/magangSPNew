<?= $this->extend('user/template'); ?>
<?= $this->section('main-content'); ?>
<style>
    .timeline-step {
    position: relative;
    padding-left: 25px;
    }

    .timeline-step::before {
    content: '';
    position: absolute;
    top: 0;
    left: 8px;
    width: 2px;
    height: 100%;
    background-color: #dee2e6;
    }

    .timeline-vertical {
        position: relative;
        padding-left: 30px;
        border-left: 3px solid #dee2e6;
    }

    .timeline-vertical .timeline-step {
        position: relative;
        margin-bottom: 40px;
    }

    .timeline-vertical .timeline-step:last-child {
        margin-bottom: 0;
    }

    .timeline-vertical .circle {
        position: absolute;
        left: -14px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #dee2e6;
        border: 3px solid white;
        z-index: 1;
    }

    .timeline-vertical .circle.active {
        background-color: #0d6efd;
    }

    .timeline-vertical .circle.completed {
        background-color: #198754;
    }

    .timeline-vertical .timeline-content {
        margin-left: 20px;
    }

    .timeline-vertical h6 {
        margin-bottom: 4px;
        font-weight: 600;
    }

    .timeline-vertical small {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Card pemberitahuan */
    .info-card {
        margin-top: -30px;
        margin-bottom: 30px;
        padding: 15px 20px;
        border-left: 5px solid #0d6efd;
        background-color: #e7f1ff;
        color: #0d6efd;
        border-radius: 4px;
        font-weight: 500;
    }

    .info-card.error {
        border-left-color: #dc3545;
        background-color: #f8d7da;
        color: #842029;
    }

    .info-card.success {
        border-left-color: #198754;
        background-color: #d1e7dd;
        color: #0f5132;
    }

</style>
<?php if (session()->getFlashdata('success')): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Sukses',
    text: '<?= session()->getFlashdata('success') ?>',
    confirmButtonText: 'OK',
    showConfirmButton: true,
    allowOutsideClick: false, 
    allowEscapeKey: false
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
        <button class="nav-link active" id="status-lamaran-tab" data-bs-toggle="tab" data-bs-target="#status-lamaran" type="button" role="tab">
            Pengajuan Penelitian
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="histori-lamaran-tab" data-bs-toggle="tab" data-bs-target="#histori-lamaran" type="button" role="tab">
            Histori Pengajuan
        </button>
    </li>
</ul>

<div class="tab-content" id="lamaranTabContent">
    <!-- Status Lamaran Tab -->
    <div class="tab-pane fade show active" id="status-lamaran" role="tabpanel">
    <p class="text-muted">Pantau perkembangan pengajuan penelitian kamu di sini.</p>
    <hr>
    <?php if (empty($pendaftaran)) : ?>
        <div class="alert alert-info text-center">
            Belum ada pengajuan penelitian.
        </div>
    <?php else : ?>
        <!-- Progress Bar Penelitian -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Progress Penelitian</h5>

                <?php 
                $step = 1;

                if (!empty($pendaftaran['tanggal_mulai'])) {
                    $step = 4; // Sudah mulai penelitian
                } elseif (!empty($pendaftaran['status_konfirmasi']) && $pendaftaran['status_konfirmasi'] == 'Y') {
                    $step = 3; // Sudah konfirmasi
                } elseif (!empty($pendaftaran['status_verifikasi']) && $pendaftaran['status_verifikasi'] !== 'Menunggu') {
                    $step = 2; // Sudah diverifikasi
                } elseif (!empty($pendaftaran['tanggal_daftar'])) {
                    $step = 1;
                }
                ?>

                <div class="timeline-vertical">
                    <!-- Step 1: Pengajuan -->
                    <div class="timeline-step">
                        <div class="circle <?= $step >= 1 ? ($step > 1 ? 'completed' : 'active') : '' ?>"></div>
                        <div class="timeline-content">
                            <h6>Pengajuan</h6>
                            <div class="info-card mt-2">
                                📄 Kamu telah mengajukan permohonan penelitian pada 
                                <strong><?= format_tanggal_indonesia(date('d F Y', strtotime($pendaftaran['tanggal_daftar']))) ?></strong>. <br>
                                Mohon tunggu proses verifikasi dari pihak admin.
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Verifikasi Admin -->
                    <div class="timeline-step">
                        <div class="circle <?= $step >= 2 ? ($step > 2 ? 'completed' : 'active') : '' ?>"></div>
                        <div class="timeline-content">
                            <h6>Verifikasi Admin</h6>
                            <?php if ($step < 2): ?>
                                <small>Menunggu proses verifikasi dan koordinasi dengan unit terkait.</small>
                            <?php else: ?>
                                <div class="info-card mt-2">
                                    <?= $pendaftaran['status_verifikasi'] == 'Diterima' 
                                        ? '✅ Permohonanmu telah diterima. Silakan konfirmasi untuk memulai penelitian.'
                                        : '❌ Maaf, permohonan penelitian kamu <strong>tidak diterima</strong>.' ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Step 3: Konfirmasi Penerimaan -->
                    <div class="timeline-step">
                        <div class="circle <?= $step >= 3 ? ($step > 3 ? 'completed' : 'active') : '' ?>"></div>
                        <div class="timeline-content">
                            <h6>Konfirmasi Permohonan</h6>
                            <?php if ($step < 3 && $pendaftaran['status_verifikasi'] == 'Diterima'): ?>
                                <div class="info-card mt-2">
                                    Silakan konfirmasi kesediaanmu untuk melaksanakan penelitian melalui tombol di bawah ini.
                                    <form action="<?= base_url('penelitian/konfirmasi') ?>" method="post" class="mt-3">
                                        <input type="hidden" name="penelitian_id" value="<?= $pendaftaran['penelitian_id'] ?>">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" required>
                                            <label class="form-check-label">Saya bersedia melaksanakan penelitian sesuai ketentuan</label>
                                        </div>
                                        <button type="submit" class="btn btn-success">Konfirmasi Penelitian</button>
                                    </form>
                                </div>
                            <?php elseif ($step >= 3): ?>
                                <div class="info-card mt-2">
                                    ✅ Kamu telah mengonfirmasi untuk memulai penelitian.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Step 4: Pelaksanaan -->
                    <div class="timeline-step">
                        <div class="circle <?= $step >= 4 ? 'completed' : '' ?>"></div>
                        <div class="timeline-content">
                            <h6>Pelaksanaan</h6>
                            <?php if ($step < 4): ?>
                                <small>Menunggu jadwal pelaksanaan dimulai</small>
                            <?php else: ?>
                                <div class="info-card mt-2">
                                    📌 Penelitian kamu sudah dimulai pada 
                                    <strong><?= format_tanggal_indonesia(date('d F Y', strtotime($pendaftaran['tanggal_mulai']))) ?></strong>. <br>
                                    Selamat menjalankan proses penelitian, semoga berjalan lancar! 💡
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    </div>
</div>


<!-- Histori Lamaran Tab -->
<div class="tab-pane fade" id="histori-lamaran" role="tabpanel">
    <h4 class="fw-bold mb-3">Histori Pengajuan</h4>
    <p class="text-muted">Riwayat semua pengajuan yang pernah diajukan.</p>

    <?php if (!empty($histori)) : ?>
        <div class="d-flex flex-column gap-3">
            <?php foreach ($histori as $riwayat) : ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                        <div class="me-3">
                            <h5 class="card-title mb-1"><?= esc($riwayat['unit_kerja']) ?></h5>
                            <p class="text-muted mb-0 small">
                                Tanggal Daftar: <?= date('d M Y', strtotime($riwayat['tanggal_daftar'])) ?>
                            </p>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 
                            <?php
                                switch ($riwayat['status_akhir']) {
                                    case 'pendaftaran': echo 'bg-secondary'; break;
                                    case 'proses': echo 'bg-warning text-dark'; break;
                                    case 'magang': echo 'bg-success'; break;
                                    case 'selesai': echo 'bg-primary'; break;
                                    case 'ditolak': echo 'bg-danger'; break;
                                    default: echo 'bg-light text-dark'; break;
                                }
                            ?>
                        ">
                            <?= ucfirst($riwayat['status_akhir']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php else : ?>
        <div class="alert alert-info mt-3">
            Belum ada histori pengajuan.
        </div>
    <?php endif ?>
</div>

</div>

                                

<!-- Optional: Include Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<?= $this->endSection(); ?>