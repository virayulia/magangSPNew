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
            Pendaftaran Magang
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="histori-lamaran-tab" data-bs-toggle="tab" data-bs-target="#histori-lamaran" type="button" role="tab">
            Histori Pendaftaran
        </button>
    </li>
</ul>

<div class="tab-content" id="lamaranTabContent">
    <!-- Status Lamaran Tab -->
    <div class="tab-pane fade show active" id="status-lamaran" role="tabpanel">
    <p class="text-muted">Pantau perkembangan pendaftaran magang kamu di sini.</p>
    <hr>

    <?php if (empty($pendaftaran)) : ?>
        <div class="alert alert-info text-center">
            Belum ada Pendaftaran Magang.
        </div>
    <?php else : ?>
    <!-- Progress Bar Pendaftaran -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">Progress Pendaftaran Magang</h5>
            <?php 
            $step = 1;
            $today = date('Y-m-d');

            if (!empty($pendaftaran['status_berkas_lengkap']) && $pendaftaran['status_berkas_lengkap']=== 'Y')  {
                $step = 6;
            } elseif (!empty($pendaftaran['status_validasi_berkas']) && !empty($pendaftaran['tanggal_validasi_berkas'])) {
                $step = 5;
            } elseif (!empty($pendaftaran['status_konfirmasi']) && !empty($pendaftaran['tanggal_konfirmasi'])) {
                $step = 4;
            } elseif (!empty($pendaftaran['status_seleksi']) && !empty($pendaftaran['tanggal_seleksi'])) {
                $step = 3;
            } elseif (!empty($pendaftaran['tanggal_daftar'])) {
                if ($periode && $periode->tanggal_tutup < $today) {
                    $step = 2;
                } else {
                    $step = 1;
                }
            }
            ?>

            <div class="timeline-vertical">

                <!-- Step 1: Pendaftaran -->
                <div class="timeline-step">
                    <div class="circle <?= $step >= 1 ? ($step > 1 ? 'completed' : 'active') : '' ?>"></div>
                    <div class="timeline-content">
                        <h6>Pendaftaran</h6>
                        <?php if($step <= 1): ?>
                            <div class="info-card mt-3">
                                ✅ Pendaftaran berhasil! <br>
                                Kamu telah mendaftar ke <strong><?= esc($pendaftaran['unit_kerja']) ?></strong> pada tanggal <strong><?= format_tanggal_indonesia (date('d F Y', strtotime($pendaftaran['tanggal_daftar']))) ?></strong>. <br><br>
                                
                                📢 Silakan cek website ini secara berkala untuk melihat status pendaftaranmu. <br>
                                📧 Kamu juga akan menerima pemberitahuan melalui email jika ada informasi terbaru terkait proses seleksi magang.
                            </div>
                        <?php endif;?>
                    </div>
                </div>
                <!-- Step 2: Seleksi -->
                <div class="timeline-step">
                    <div class="circle <?= $step >= 2 ? ($step > 2 ? 'completed' : 'active') : '' ?>"></div>
                    <div class="timeline-content">
                        <h6>Seleksi</h6>
                        <?php if($step <2): ?>
                        <small>Menunggu hasil seleksi dari admin.</small>
                        <?php elseif ($step == 2): ?>
                            <br><br>
                            <div class="info-card">
                                Anda sedang dalam tahap <strong>seleksi</strong>. <br>
                                Silakan cek email secara berkala untuk perkembangan pendaftaran Anda.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Step 3: Konfirmasi Penerimaan -->
                <div class="timeline-step">
                    <div class="circle <?= $step >= 3 ? ($step > 3 ? 'completed' : 'active') : '' ?>"></div>
                    <div class="timeline-content">
                        <h6>Konfirmasi Penerimaan</h6>
                        <?php if ($step < 3):?>
                        <small>Konfirmasi penerimaan oleh pendaftar.</small>
                        <?php elseif ($step == 3): ?>
                            <?php if ($pendaftaran['status_seleksi'] == 'Ditolak'): ?>
                                <br><br>
                                <div class="info-card error">
                                    <strong>Terima kasih telah mendaftar.</strong>
                                    Setelah melalui proses seleksi, kami informasikan bahwa Anda <strong>belum berhasil lolos</strong> dalam tahap ini.
                                    Kami sangat mengapresiasi minat dan usaha Anda.
                                    Jangan berkecil hati—tetap semangat dan terus tingkatkan kemampuan Anda!
                                    Semoga sukses untuk kesempatan berikutnya.
                                </div>
                            <?php elseif ($pendaftaran['status_seleksi'] == 'Diterima'): ?>
                                <br><br>
                                <div class="info-card success">
                                    <strong>Selamat!</strong> <br>
                                    Anda telah diterima sebagai peserta magang di <strong><?= esc($pendaftaran['unit_kerja']) ?></strong>. <br>
                                    Silakan lakukan konfirmasi penerimaan dalam waktu <strong>maksimal 3 hari</strong> sejak pengumuman ini. <br>   
                                    Jika tidak ada konfirmasi hingga batas waktu tersebut, maka kesempatan ini akan dianggap <strong>gugur</strong>. <br>
                                    Kami tunggu konfirmasi Anda, dan selamat bergabung! <br><br>
                                    <strong>Silakan klik tombol <em>Konfirmasi</em> di bawah halaman ini untuk menyatakan bahwa Anda menerima tawaran magang ini.</strong><br><br>

                                    <?php if ($pendaftaran['safety'] == 1): ?>
                                        <div class="alert alert-warning mt-2">
                                            <?php if (!empty($pendaftaran['catatan'])): ?>
                                            <p><strong>Catatan : <?= $pendaftaran['catatan']; ?></strong></p>
                                            <?php endif; ?>
                                            <strong>Perhatian:</strong> Unit kerja Anda mewajibkan kelengkapan alat pelindung diri (APD). <br>
                                            Silakan menyiapkan perlengkapan berikut secara mandiri:
                                            <ul class="mb-0 mt-1">
                                                <li>Rompi safety</li>
                                                <li>Helm safety warna biru</li>
                                                <li>Sepatu safety</li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"  data-bs-target="#modalKonfirmasi">
                                        Konfirmasi Penerimaan
                                    </button>
                                </div>
                                <!-- Modal Konfirmasi-->
                                <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4">
                                        <div class="modal-header bg-primary text-white rounded-top-4">
                                            <h5 class="modal-title fw-bold" id="modalKonfirmasiLabel">Konfirmasi Kesediaan Magang</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                        <form action="<?= base_url('magang/konfirmasi') ?>" method="post">

                                        <p>Dengan ini, saya menyatakan bersedia untuk mengikuti program magang yang akan dimulai pada <strong><?= format_tanggal_indonesia(date('d M Y', strtotime($pendaftaran['tanggal_masuk']))) ?></strong>.</p>
                                        <p>Saya berkomitmen untuk menjalankan seluruh kegiatan magang dengan penuh tanggung jawab dan disiplin sesuai ketentuan yang berlaku.</p>

                                    
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" value="" id="setuju" required>
                                            <label class="form-check-label" for="setuju">
                                                Saya menyetujui pernyataan di atas dan bersedia mengikuti program magang.
                                            </label>
                                        </div>
                                        <input type="hidden" name="magang_id" value="<?= $pendaftaran['magang_id'] ?>">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Konfirmasi</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>     
                                
                <!-- Step 4: Pengumpulan Berkas -->
                <div class="timeline-step">
                    <div class="circle <?= $step >= 4 ? ($step > 4 ? 'completed' : 'active') : '' ?>"></div>
                    <div class="timeline-content">
                        <h6>Kelengkapan Berkas</h6>

                        <?php if($step < 4) : ?>
                            <small>Melengkapi persyaratan sebelum H-3 magang</small>        

                        <?php elseif ($step == 4): ?>
                            <?php if ($pendaftaran['status_berkas_lengkap'] === 'N'): ?>
                                <div class="alert alert-warning">
                                    ❌ Berkas yang Anda lampirkan sebelumnya <strong>belum lengkap atau tidak sesuai</strong>.<br>
                                    Mohon lengkapi dokumen Anda melalui menu <strong><a href="/profile?tab=dokumen">Profil</a></strong> dan lakukan validasi ulang.<br><br>
                                    Setelah melengkapi dokumen, silakan klik tombol <strong>Validasi Berkas Lengkap</strong> di bawah untuk mengajukan kembali.<br><br>
                                    Jika tidak dilengkapi sebelum <strong><?= format_tanggal_indonesia(date('d M Y', strtotime('+7 days', strtotime($pendaftaran['tanggal_konfirmasi'])))) ?></strong>, maka kesempatan ini akan dianggap <strong>gugur</strong>.
                                </div>
                            <?php else: ?>
                                <br><br>
                                <div class="info-card">
                                    Mohon segera melengkapi dokumen persyaratan Anda melalui menu <strong><a href="/profile?tab=dokumen">Profil</a></strong> selambat-lambatnya pada tanggal <strong><?= format_tanggal_indonesia(date('d M Y', strtotime('+7 days', strtotime($pendaftaran['tanggal_konfirmasi'])))) ?></strong>.<br><br>

                                    Dokumen yang <strong>wajib</strong> dilengkapi:
                                    <ul class="mb-2 mt-1">
                                        <li>BPJS Ketenagakerjaan</li>
                                        <li>Bukti Pembayaran BPJS Ketenagakerjaan</li>
                                    </ul>

                                    Dokumen <strong>opsional</strong> (jika ada):
                                    <ul class="mb-2 mt-1">
                                        <li>BPJS Kesehatan <small>(tidak wajib, tetapi disarankan untuk dilampirkan jika ada)</small></li>
                                    </ul>

                                    Setelah melengkapi seluruh dokumen, jangan lupa untuk menekan tombol <strong>Validasi Berkas Lengkap</strong> agar dokumen Anda dapat diverifikasi oleh tim kami.<br><br>

                                    Apabila dokumen tidak dilengkapi hingga batas waktu yang ditentukan, maka kesempatan Anda akan dianggap <strong>gugur</strong>.
                                </div>

                            <?php endif; ?>
                            <?php 
                                $isBpjsFilled = !empty($user_data->bpjs_tk) && !empty($user_data->buktibpjs_tk);
                                $btnClass = $isBpjsFilled ? 'btn-danger' : 'btn-secondary'; // abu-abu jika tidak lengkap
                                ?>
                                <button class="btn <?= $btnClass ?> mt-3" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="<?= $isBpjsFilled ? '#validasiBerkasModal' : '' ?>" 
                                        <?= $isBpjsFilled ? '' : 'disabled' ?>>
                                    Validasi Berkas Lengkap
                                </button>
                            
                            <!-- Modal Validasi Berkas -->
                            <div class="modal fade" id="validasiBerkasModal" tabindex="-1" aria-labelledby="validasiBerkasModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4">
                                        <div class="modal-header bg-primary text-white rounded-top-4">
                                            <h5 class="modal-title fw-bold" id="validasiBerkasModalLabel">Pernyataan Validasi Berkas</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <form action="<?= base_url('magang/validasi-berkas') ?>" method="post">
                                            <div class="modal-body">
                                                <p>
                                                    Dengan ini, saya menyatakan bahwa seluruh dokumen yang saya unggah sebagai syarat administrasi magang adalah benar, sah, dan sesuai dengan keadaan sebenarnya.
                                                </p>
                                                <p>
                                                    Apabila di kemudian hari ditemukan ketidaksesuaian atau ketidakbenaran atas dokumen yang saya berikan, saya bersedia menerima segala konsekuensi sesuai ketentuan yang berlaku.
                                                </p>
                                                <p>
                                                    Saya juga menyatakan memberikan persetujuan penuh kepada PT Semen Padang untuk menggunakan, menyimpan, dan memproses data pribadi serta dokumen yang saya serahkan, termasuk namun tidak terbatas pada data identitas, riwayat pendidikan, serta dokumen pendukung lainnya, untuk keperluan administrasi, evaluasi, dan kegiatan lain yang berkaitan dengan proses magang.
                                                </p>
                                                <p>
                                                    Pernyataan ini saya buat dengan sebenar-benarnya dan saya memahami bahwa data pribadi saya akan dikelola sesuai dengan ketentuan yang berlaku, termasuk Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi.
                                                </p>

                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="checkbox" name="setuju_berkas" id="setuju_berkas" required>
                                                    <label class="form-check-label" for="setuju_berkas">
                                                        Saya menyetujui seluruh pernyataan di atas dan menyatakan bahwa berkas saya telah lengkap.
                                                    </label>
                                                </div>

                                                <input type="hidden" name="magang_id" value="<?= $pendaftaran['magang_id'] ?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Validasi Sekarang</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                        <?php endif; ?>
                    </div>
                </div>

                <!-- Step 5: Validasi Berkas -->
                <div class="timeline-step">
                    <div class="circle <?= $step >= 5 ? ($step > 5 ? 'completed' : 'active') : '' ?>"></div>
                    <div class="timeline-content">
                        <h6>Validasi Berkas</h6>
                        <?php if ($step == 5): ?>
                            <br><br>
                            <div class="info-card">
                                📁 <strong>Kamu telah mengonfirmasi kelengkapan berkas</strong> pada
                                    <strong><?= format_tanggal_indonesia(date('d F Y', strtotime($pendaftaran['tanggal_konfirmasi']))) ?></strong>. <br>
                                    ⏳ Saat ini kamu sedang <strong>menunggu verifikasi dokumen oleh admin</strong>. <br>
                                    📧 Mohon cek email dan website ini secara berkala untuk mengetahui hasil verifikasi.
                            </div>
                        <?php elseif ($step < 5): ?>
                        <small>Menunggu verifikasi dokumen oleh admin</small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Step 6: Pelaksanaan -->
                <div class="timeline-step">
                    <div class="circle <?= $step >= 6 ? 'completed' : '' ?>"></div>
                    <div class="timeline-content">
                        <h6>Pelaksanaan</h6>
                        <?php if($step < 6): ?>
                            <small>Mulai kegiatan magang</small>
                        <?php elseif ($step == 6): ?>
                            <br><br>
                            <div class="info-card">
                                🎉 Selamat! <br>
                                Kamu telah <strong>diterima magang di PT. Semen Padang</strong> pada <strong><?= esc($pendaftaran['unit_kerja']) ?></strong>. Selamat bergabung dan semangat menjalani pengalaman barumu! <br>
                                Tanggal pelaksanaan magang dimulai dari <strong><?= format_tanggal_indonesia(date('d F Y', strtotime($pendaftaran['tanggal_masuk']))) ?></strong> hingga <strong><?= format_tanggal_indonesia(date('d F Y', strtotime($pendaftaran['tanggal_selesai']))) ?></strong>.
                                <br><br>
                                Pada hari pertama, kamu diharapkan hadir di <strong>Gedung Diklat PT Semen Padang</strong> pada pukul <strong>08.00 WIB</strong> untuk mengikuti pengarahan awal dan registrasi peserta magang.
                                <br><br>
                                Untuk melanjutkan proses magang, silakan buka halaman pelaksanaan di bawah ini. Di sana kamu perlu melengkapi beberapa informasi tambahan sebelum mulai magang.
                                <br><br>
                                <a href="<?= base_url('/pelaksanaan') ?>" style="background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                                    Lihat Pelaksanaan Magang
                                </a>

                                <?php if (!empty($pendaftaran['safety']) && $pendaftaran['safety'] == 1): ?>
                                    <div class="alert alert-warning mt-3">
                                        <?php if (!empty($pendaftaran['catatan'])): ?>
                                        <p><strong>Catatan : <?= $pendaftaran['catatan']; ?></strong></p>
                                        <?php endif; ?>
                                        <strong>Perhatian:</strong> Unit kerja Anda mewajibkan kelengkapan alat pelindung diri (APD). <br>
                                        Silakan menyiapkan perlengkapan berikut secara mandiri:
                                        <ul class="mb-0 mt-1">
                                            <li>Rompi safety</li>
                                            <li>Helm safety warna biru</li>
                                            <li>Sepatu safety</li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
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
    <h4 class="fw-bold mb-3">Histori Pendaftaran</h4>
    <p class="text-muted">Riwayat semua pendaftaran yang pernah diajukan.</p>

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
            Belum ada histori pendaftaran.
        </div>
    <?php endif ?>
</div>

</div>

                                


<!-- Optional: Include Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<?= $this->endSection(); ?>