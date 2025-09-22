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
                Pelaksanaan Magang
            </button>
        </li>
    </ul>
    <p class="text-muted">Lanjutkan proses pelaksanaan magang kamu di sini.</p>
    <hr>
    <?php if (!empty($pendaftaran)): ?>
        <?php if($pendaftaran['status_akhir'] === 'magang'): ?>
        <div class="tab-content" id="lamaranTabContent">
            <div class="tab-pane fade show active" id="pelaksanaan" role="tabpanel">
                <p class="text-muted">Panduan dan tugas penting yang perlu kamu selesaikan sebelum memulai magang:</p>

                <?php
                    // ---- FLAGS / STATUS STEP ----
                    $isBerkasComplete     = !empty($user_data->bpjs_tk) && !empty($user_data->buktibpjs_tk);
                    $isPernyataanSetuju   = !empty($pendaftaran['tanggal_setujui_pernyataan'] ?? null);

                    // Sudah lulus/selesai safety induction (mis: ada nilai >= 70 pada riwayat)
                    $isSafetyInduction    = false;
                    if (!empty($riwayat_safety) && is_array($riwayat_safety)) {
                        foreach ($riwayat_safety as $r) {
                            if ((int)($r['nilai'] ?? 0) >= 70) { $isSafetyInduction = true; break; }
                        }
                    }

                    // ---- GATING (izin akses tombol) ----
                    // Cetak ID hanya setelah berkas lengkap
                    $canPrintId           = $isBerkasComplete;

                    // Surat Pernyataan bisa diisi setelah berkas lengkap (sesuai kebijakanmu)
                    $canFillPernyataan    = $isBerkasComplete;

                    // Window tes safety: dari tanggal masuk s/d +3 hari
                    $canSafetyWindow      = false;
                    if (!empty($pendaftaran['tanggal_masuk'] ?? null)) {
                        $masuk       = new DateTime($pendaftaran['tanggal_masuk']);
                        $today       = new DateTime();
                        $batasAkhir  = (clone $masuk)->modify('+3 days');
                        $canSafetyWindow = ($today >= $masuk && $today <= $batasAkhir);
                    }

                    // Ikut tes safety hanya jika berkas lengkap + pernyataan disetujui + dalam window tes
                    $canTakeSafety        = $isBerkasComplete && $isPernyataanSetuju && $canSafetyWindow;
                ?>

                <!-- Kelengkapan Berkas -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">📁 Kelengkapan Berkas</h5>
                        <?php if (!empty($user_data->bpjs_tk) && !empty($user_data->buktibpjs_tk)): ?>
                            <div class="alert alert-success p-4 text-center">
                                <h5 class="mb-3">✅ Terima Kasih!</h5>
                                Kamu telah melengkapi <strong>Berkas Magang</strong>. <br><br>
                                <a href="/profile?tab=dokumen" target="_blank" class="btn btn-outline-success">
                                    Lihat Dokumen
                                </a>
                            </div>
                        <?php else: ?>
                            <?php if(!empty($pendaftaran['cttn_berkas_lengkap'])): ?>
                                <div class="alert alert-danger mt-2"> Catatan : <?= esc($pendaftaran['cttn_berkas_lengkap']); ?></div>
                            <?php endif; ?>
                            <p>Silakan unggah berkas berikut sebelum memulai magang:</p>
                            <ul>
                                <li>Kartu BPJS Ketenagakerjaan</li>
                                <li>Bukti Pembayaran/Masa Berlaku BPJS Ketenagakerjaan</li>
                            </ul>
                            <a href="/profile?tab=dokumen" class="btn btn-primary">Lengkapi Berkas</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cetak Tanda Pengenal -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">🎫 Cetak Tanda Pengenal</h5>
                        <p>Tanda pengenal wajib dicetak dan dibawa saat hari pertama magang.</p>

                        <?php if (!empty($user_data->bpjs_tk) && !empty($user_data->buktibpjs_tk)): ?>
                            <?php if(!empty($pendaftaran['status_berkas_lengkap']) && $pendaftaran['status_berkas_lengkap'] ==='Y' && !empty($pendaftaran['tanggal_berkas_lengkap'])): ?>
                            <a href="<?= base_url('/cetak-tanda-pengenal/' . $pendaftaran['magang_id']) ?>" 
                            target="_blank" class="btn btn-danger">
                                Cetak Tanda Pengenal
                            </a>
                            <?php else: ?>
                                <div class="alert alert-secondary mt-2">⚠️ Menunggu validasi berkas oleh Admin.</div>
                                <button class="btn btn-secondary" disabled>Cetak Tanda Pengenal</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-secondary mt-2">⚠️ Lengkapi berkas terlebih dahulu.</div>
                            <button class="btn btn-secondary" disabled>Cetak Tanda Pengenal</button>
                        <?php endif; ?>

                        <?php if (!empty($pendaftaran['safety']) && $pendaftaran['safety'] == 1): ?>
                            <div class="alert alert-warning mt-3">
                                <strong>Perhatian:</strong> Unit kerja Anda mewajibkan kelengkapan APD.<br>
                                Siapkan perlengkapan berikut:
                                <ul class="mb-0 mt-1">
                                    <li>Rompi safety</li>
                                    <li>Helm safety warna biru</li>
                                    <li>Sepatu safety</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Surat Pernyataan -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">📝 Surat Pernyataan</h5>

                        <?php if (!empty($pendaftaran['tanggal_setujui_pernyataan'])): ?>
                            <?php if(!empty($pendaftaran['status_berkas_lengkap']) && $pendaftaran['status_berkas_lengkap'] ==='Y' && !empty($pendaftaran['tanggal_berkas_lengkap'])): ?>
                            <div class="alert alert-success p-4 text-center">
                                <h5 class="mb-3">✅ Terima Kasih!</h5>
                                Anda telah menyetujui <strong>Surat Pernyataan</strong>.<br>
                                Tanggal persetujuan: <br>
                                <strong><?= format_tanggal_indonesia(date('d M Y', strtotime($pendaftaran['tanggal_setujui_pernyataan']))) ?></strong><br><br>

                                <a href="<?= base_url('magang/surat-pernyataan') ?>" target="_blank" class="btn btn-outline-success">
                                    Lihat Surat Pernyataan
                                </a>
                            </div>
                            <?php else: ?>
                                <div class="alert alert-secondary">⚠️ Menunggu validasi berkas oleh Admin.</div>
                                <button class="btn btn-secondary" disabled>Baca & Setujui</button>
                            <?php endif; ?>
                        <?php elseif (!empty($user_data->bpjs_tk) && !empty($user_data->buktibpjs_tk)): ?>
                            <p>Klik tombol di bawah ini untuk membaca dan menyetujui surat pernyataan.</p>
                            <a href="<?= base_url('magang/surat-pernyataan') ?>" class="btn btn-primary">Baca & Setujui Surat Pernyataan</a>
                        <?php else: ?>
                            <div class="alert alert-secondary">⚠️ Lengkapi berkas terlebih dahulu.</div>
                            <button class="btn btn-secondary" disabled>Baca & Setujui</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Safety Induction -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">🦺 Safety Induction</h5>
                        <p>Harap mempelajari prosedur keselamatan kerja berikut. Tes dapat dikerjakan maksimal <strong>3 kali</strong> dengan nilai minimal <strong>70</strong>.</p>


<div class="d-flex flex-wrap gap-3 mb-3">
    <a href="https://docs.google.com/presentation/d/1kL9-zVEipfdnUarMJraO5CFldiIe6IZI/edit?usp=sharing&ouid=106778029981766455288&rtpof=true&sd=true" 
       class="btn btn-outline-info" target="_blank">
        📘 Penjelasan Safety Induction
    </a>

    <?php
        $tanggal_masuk = $pendaftaran['tanggal_masuk'];
        $masuk = new DateTime($tanggal_masuk);

        // Waktu minimal tes: jam 08:30 pada hari masuk
        $waktu_mulai = clone $masuk;
        $waktu_mulai->setTime(8, 30);

        // Batas akhir: 3 hari setelah tanggal masuk
        $batas_akhir = clone $masuk;
        $batas_akhir->modify('+3 days');

        $hari_ini = new DateTime();
    ?>

    <?php if (
        !empty($pendaftaran['tanggal_setujui_pernyataan']) && 
        $hari_ini >= $waktu_mulai && 
        $hari_ini <= $batas_akhir
    ): ?>
        <a href="<?= base_url('safety-tes') ?>" class="btn btn-outline-warning">
            🚀 Ikuti Tes Safety Induction
        </a>
    <?php else: ?>
        <button class="btn btn-secondary" disabled>Ikuti Tes Safety Induction</button>
        <div class="alert alert-secondary mt-2">
            ⚠️ Tes dapat diikuti pada <strong>hari pertama magang mulai 08:30</strong>, 
            setelah Anda menyetujui <strong>Surat Pernyataan</strong>.
        </div>
    <?php endif; ?>
</div>


                        <!-- Riwayat Tes -->
                        <?php if (!empty($riwayat_safety)): ?>
                            <div class="mt-4">
                                <h6>Riwayat Tes Safety Induction:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Tanggal</th>
                                                <th>Percobaan</th>
                                                <th>Skor</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($riwayat_safety as $i => $r): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></td>
                                                <td><?= $r['percobaan_ke'] ?></td>
                                                <td><?= $r['nilai'] ?></td>
                                                <td>
                                                    <?= $r['nilai'] >= 70 
                                                        ? '<span class="badge bg-success">Lulus</span>' 
                                                        : '<span class="badge bg-danger">Tidak Lulus</span>' ?>
                                                </td>
                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
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
