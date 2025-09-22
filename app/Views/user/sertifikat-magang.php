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

<!-- Tabs Sertifikat -->
<div class="profile-card">
    <ul class="nav nav-tabs profile-tabs mb-4" id="sertifikatTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="sertifikat-tab" data-bs-toggle="tab" data-bs-target="#pelaksanaan" type="button" role="tab">
                Sertifikat Magang
            </button>
        </li>
    </ul>

    <p class="text-muted">Unduh sertifikat magang kamu di sini.</p>
    <hr>

    <?php if (!empty($penilaian)): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📋 Sertifikat Magang</h5>
                    <?php if (date('Y-m-d') >= $pendaftaran['tanggal_selesai']): ?>
                        <?php if ($pendaftaran): ?>
                            <?php if (!$feedback): ?>
                                <div class="alert alert-warning">
                                    Sebelum mengunduh sertifikat, silakan isi feedback terlebih dahulu.
                                </div>
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                                    Isi Feedback
                                </button>                     
                            <?php else: ?>
                                <div class="card border-0 shadow-sm mb-4 rounded-3">
                                    <div class="card-body">
                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px; height:40px;">
                                                    <i class="bi bi-person-fill fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Feedback Anda</h6>
                                                    <small class="text-muted">Terima kasih sudah mengisi feedback 😊</small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                        </div>

                                        <!-- Content -->
                                        <div class="row g-4">
                                            <!-- Feedback Pusdiklat -->
                                            <div class="col-md-6">
                                                <div class="p-3 rounded bg-light">
                                                    <h6 class="fw-bold text-primary mb-3">
                                                        <i class="bi bi-building"></i> Pusdiklat
                                                    </h6>
                                                    <p class="mb-2"><strong>Website magang SP:</strong><br>
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="bi <?= $i <= $feedback->diklat_website ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                                        <?php endfor; ?>
                                                        <small class="text-muted">(<?= $feedback->diklat_website ?>/5)</small>
                                                    </p>
                                                    <p class="mb-2"><strong>Pelayanan admin:</strong><br>
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="bi <?= $i <= $feedback->diklat_admin ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                                        <?php endfor; ?>
                                                        <small class="text-muted">(<?= $feedback->diklat_admin ?>/5)</small>
                                                    </p>
                                                    <p class="mb-0"><strong>Saran:</strong><br>
                                                        <span class="fst-italic"><?= nl2br(esc($feedback->diklat_saran)) ?></span>
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Feedback Unit Kerja -->
                                            <div class="col-md-6">
                                                <div class="p-3 rounded bg-light">
                                                    <h6 class="fw-bold text-success mb-3">
                                                        <i class="bi bi-people"></i> Unit Kerja
                                                    </h6>
                                                    <p class="mb-2"><strong>Pendampingan supervisor:</strong><br>
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="bi <?= $i <= $feedback->unit_supervisor ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                                        <?php endfor; ?>
                                                        <small class="text-muted">(<?= $feedback->unit_supervisor ?>/5)</small>
                                                    </p>
                                                    <p class="mb-2"><strong>Pengalaman sesuai harapan:</strong><br>
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="bi <?= $i <= $feedback->unit_pengalaman ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                                        <?php endfor; ?>
                                                        <small class="text-muted">(<?= $feedback->unit_pengalaman ?>/5)</small>
                                                    </p>
                                                    <p class="mb-2"><strong>Suasana kerja:</strong><br>
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="bi <?= $i <= $feedback->unit_suasana ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                                        <?php endfor; ?>
                                                        <small class="text-muted">(<?= $feedback->unit_suasana ?>/5)</small>
                                                    </p>
                                                    <p class="mb-0"><strong>Kesan & pesan:</strong><br>
                                                        <span class="fst-italic"><?= nl2br(esc($feedback->unit_kesan)) ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <?php if($pendaftaran['ka_unit_approve'] == 1) :?>
                                    <div class="card border-success shadow-sm p-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 text-success">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 text-success fw-bold">Sertifikat Tersedia</h6>
                                                <p class="mb-1 small text-muted">Selamat 🎉 Sertifikat magang Anda sudah siap diunduh.</p>
                                                <a href="<?= base_url('cetak-sertifikat') ?>" target="_blank" class="btn btn-success btn-sm">
                                                    <i class="fas fa-file-pdf"></i> Unduh Sertifikat
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="card border-warning shadow-sm p-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 text-warning">
                                                <i class="fas fa-clock fa-2x"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 text-warning fw-bold">Sertifikat Belum Tersedia</h6>
                                                <p class="mb-0 small text-muted">Sertifikat Anda sedang diproses. Silakan cek kembali nanti 🙏</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>


                            <?php endif; ?>
                                <!-- Modal Feedback -->
                                <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                        <form action="<?= base_url('sertifikat/saveFeedback') ?>" method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Feedback Magang</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <input type="hidden" name="magang_id" value="<?= $pendaftaran['magang_id'] ?>">

                                                <!-- Step 1: Feedback Diklat -->
                                                <div id="step1" class="feedback-step">
                                                    <h6 class="fw-bold mb-3">Feedback untuk Pusdiklat (Penyelenggara Program Magang)</h6>
                                                    
                                                    <div class="mb-3">
                                                        <label>1. Apakah website magang SP membantu dan mudah digunakan?</label>
                                                        <div class="star-rating">
                                                            <input type="hidden" name="diklat_website" id="diklatWebsiteInput" value="<?= $feedback->diklat_website ?? 0 ?>">
                                                            <?php for ($i=1; $i<=5; $i++): ?>
                                                                <i class="bi <?= ($feedback->diklat_website ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#diklatWebsiteInput"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>2. Bagaimana pelayanan admin Pusdiklat dalam mendukung program magang?</label>
                                                        <div class="star-rating">
                                                            <input type="hidden" name="diklat_admin" id="diklatAdminInput" value="<?= $feedback->diklat_admin ?? 0 ?>">
                                                            <?php for ($i=1; $i<=5; $i++): ?>
                                                                <i class="bi <?= ($feedback->diklat_admin ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#diklatAdminInput"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>3. Saran untuk pengembangan website atau pelayanan Pusdiklat:</label>
                                                        <textarea name="diklat_saran" class="form-control" rows="3"><?= $feedback->diklat_saran ?? '' ?></textarea>
                                                    </div>
                                                </div>

                                                <!-- Step 2: Feedback Unit Kerja -->
                                                <div id="step2" class="feedback-step d-none">
                                                    <h6 class="fw-bold mb-3">Feedback untuk Unit Kerja (Tempat Penempatan Magang)</h6>
                                                    
                                                    <div class="mb-3">
                                                        <label>1. Apakah pendampingan supervisor/pembimbing sesuai kebutuhan Anda?</label>
                                                        <div class="star-rating">
                                                            <input type="hidden" name="unit_supervisor" id="unitSupervisorInput" value="<?= $feedback->unit_supervisor ?? 0 ?>">
                                                            <?php for ($i=1; $i<=5; $i++): ?>
                                                                <i class="bi <?= ($feedback->unit_supervisor ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#unitSupervisorInput"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>2. Apakah pengalaman yang didapatkan sesuai dengan harapan Anda?</label>
                                                        <div class="star-rating">
                                                            <input type="hidden" name="unit_pengalaman" id="unitPengalamanInput" value="<?= $feedback->unit_pengalaman ?? 0 ?>">
                                                            <?php for ($i=1; $i<=5; $i++): ?>
                                                                <i class="bi <?= ($feedback->unit_pengalaman ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#unitPengalamanInput"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>3. Apakah suasana kerja di unit mendukung pelaksanaan magang?</label>
                                                        <div class="star-rating">
                                                            <input type="hidden" name="unit_suasana" id="unitSuasanaInput" value="<?= $feedback->unit_suasana ?? 0 ?>">
                                                            <?php for ($i=1; $i<=5; $i++): ?>
                                                                <i class="bi <?= ($feedback->unit_suasana ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#unitSuasanaInput"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>4. Kesan dan pesan Anda selama magang di unit kerja:</label>
                                                        <textarea name="unit_kesan" class="form-control" rows="3"><?= $feedback->unit_kesan ?? '' ?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" id="prevStep" class="btn btn-secondary d-none">Sebelumnya</button>
                                                <button type="button" id="nextStep" class="btn btn-primary">Lanjut</button>
                                                <button type="submit" id="submitFeedback" class="btn btn-success d-none">Simpan Feedback</button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <br>
                        <p class="text-muted">Belum ada sertifikat magang.</p>
                    <?php endif; ?>
                </div>
            </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Belum ada Sertifikat Magang.
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const step1 = document.getElementById("step1");
    const step2 = document.getElementById("step2");
    const nextBtn = document.getElementById("nextStep");
    const prevBtn = document.getElementById("prevStep");
    const submitBtn = document.getElementById("submitFeedback");

    // Navigasi step
    nextBtn.addEventListener("click", function(){
        step1.classList.add("d-none");
        step2.classList.remove("d-none");
        nextBtn.classList.add("d-none");
        prevBtn.classList.remove("d-none");
        submitBtn.classList.remove("d-none");
    });

    prevBtn.addEventListener("click", function(){
        step1.classList.remove("d-none");
        step2.classList.add("d-none");
        nextBtn.classList.remove("d-none");
        prevBtn.classList.add("d-none");
        submitBtn.classList.add("d-none");
    });

    // Rating bintang interaktif
    document.querySelectorAll(".star").forEach(star => {
        star.addEventListener("click", function(){
            const value = this.getAttribute("data-value");
            const target = document.querySelector(this.getAttribute("data-target"));
            target.value = value;
            const parent = this.parentNode;
            parent.querySelectorAll(".star").forEach(s => {
                s.classList.remove("bi-star-fill", "text-warning");
                s.classList.add("bi-star", "text-secondary");
            });
            for(let i=0; i<value; i++){
                parent.querySelectorAll(".star")[i].classList.remove("bi-star", "text-secondary");
                parent.querySelectorAll(".star")[i].classList.add("bi-star-fill", "text-warning");
            }
        });
    });
});
</script>


<?= $this->endSection(); ?>
