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
                Surat Keterangan Penelitian
            </button>
        </li>
    </ul>

    <p class="text-muted">Unduh Surat Keterangan Penelitian kamu di sini.</p>
    <hr>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📋 Surat Keterangan Penelitian</h5>
                    <?php if ($pendaftaran): ?>
                        <?php if (date('Y-m-d') >= $pendaftaran['tanggal_selesai']): ?>
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
                                                    <i class="bi bi-journal-text fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Feedback Penelitian Anda</h6>
                                                    <small class="text-muted">Terima kasih sudah berbagi pengalaman penelitian 😊</small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feedbackPenelitianModal">
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
                                                    <p class="mb-2"><strong>Website pendaftaran penelitian:</strong><br>
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="bi <?= $i <= $feedback->diklat_website ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                                        <?php endfor; ?>
                                                        <small class="text-muted">(<?= $feedback->diklat_website ?>/5)</small>
                                                    </p>
                                                    <p class="mb-2"><strong>Pelayanan administrasi & respons petugas:</strong><br>
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
                                                        <i class="bi bi-person-workspace"></i> Unit Kerja / Pembimbing
                                                    </h6>
                                                    <p class="mb-2"><strong>Dukungan & pendampingan pembimbing:</strong><br>
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="bi <?= $i <= $feedback->unit_supervisor ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                                        <?php endfor; ?>
                                                        <small class="text-muted">(<?= $feedback->unit_supervisor ?>/5)</small>
                                                    </p>
                                                    <p class="mb-2"><strong>Kemudahan koordinasi & penjadwalan kegiatan:</strong><br>
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="bi <?= $i <= $feedback->unit_pengalaman ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?>"></i>
                                                        <?php endfor; ?>
                                                        <small class="text-muted">(<?= $feedback->unit_pengalaman ?>/5)</small>
                                                    </p>
                                                    <p class="mb-2"><strong>Akses terhadap data & fasilitas penelitian:</strong><br>
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
                                                <h6 class="mb-1 text-success fw-bold">Surat Keterangan Penelitian</h6>
                                                <p class="mb-1 small text-muted">Selamat 🎉 Surat Keterangan Penelitian Anda sudah siap diunduh.</p>
                                                <a href="<?= base_url('cetak-surat-keterangan') ?>" target="_blank" class="btn btn-success btn-sm">
                                                    <i class="fas fa-file-pdf"></i> Unduh Surat Keterangan
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
                                                <h6 class="mb-1 text-warning fw-bold">Surat Keterangan Penelitian Belum Tersedia</h6>
                                                <p class="mb-0 small text-muted">Surat Keterangan Penelitian Anda sedang diproses. Silakan cek kembali nanti 🙏</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>


                            <?php endif; ?>
                            
                                <!-- Modal Feedback -->
                                <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <form action="<?= base_url('penelitian/saveFeedback') ?>" method="post" id="formFeedback">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-chat-dots"></i> Feedback Magang/Penelitian</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <input type="hidden" name="penelitian_id" value="<?= $pendaftaran['penelitian_id'] ?>">

                                                    <!-- STEP 1: Feedback Diklat -->
                                                    <div id="step1" class="feedback-step">
                                                        <h6 class="fw-bold mb-3 text-primary">Feedback untuk Pusdiklat (Penyelenggara Program Magang/penelitian)</h6>
                                                        <small class="text-danger d-block mb-3">*Harap mengisi dengan keadaan sebenarnya</small>

                                                        <!-- Website -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">1. Apakah website magang/penelitian SP membantu dan mudah digunakan?</label>
                                                            <div class="star-rating">
                                                                <input type="hidden" name="diklat_website" id="diklatWebsiteInput" value="<?= $feedback->diklat_website ?? 0 ?>">
                                                                <?php for ($i=1; $i<=5; $i++): ?>
                                                                    <i class="bi <?= ($feedback->diklat_website ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#diklatWebsiteInput"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Admin -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">2. Bagaimana pelayanan admin Pusdiklat dalam mendukung program magang/penelitian?</label>
                                                            <div class="star-rating">
                                                                <input type="hidden" name="diklat_admin" id="diklatAdminInput" value="<?= $feedback->diklat_admin ?? 0 ?>">
                                                                <?php for ($i=1; $i<=5; $i++): ?>
                                                                    <i class="bi <?= ($feedback->diklat_admin ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#diklatAdminInput"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Saran -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">3. Saran untuk pengembangan website atau pelayanan Pusdiklat:</label>
                                                            <textarea name="diklat_saran" class="form-control" rows="3" maxlength="300" required placeholder="Tuliskan saran Anda (maks. 300 karakter)"><?= $feedback->diklat_saran ?? '' ?></textarea>
                                                            <div class="form-text text-end"><span id="countSaran">0</span>/300</div>
                                                        </div>
                                                    </div>

                                                    <!-- STEP 2: Feedback Unit Kerja -->
                                                    <div id="step2" class="feedback-step d-none">
                                                        <h6 class="fw-bold mb-3 text-success">Feedback untuk Unit Kerja (Tempat Penempatan Magang/Penelitian)</h6>
                                                        <small class="text-danger d-block mb-3">*Harap mengisi dengan keadaan sebenarnya</small>

                                                        <!-- Supervisor -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">1. Apakah pendampingan supervisor/pembimbing sesuai kebutuhan Anda?</label>
                                                            <div class="star-rating">
                                                                <input type="hidden" name="unit_supervisor" id="unitSupervisorInput" value="<?= $feedback->unit_supervisor ?? 0 ?>">
                                                                <?php for ($i=1; $i<=5; $i++): ?>
                                                                    <i class="bi <?= ($feedback->unit_supervisor ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#unitSupervisorInput"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Pengalaman -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">2. Apakah pengalaman yang didapatkan sesuai dengan harapan Anda?</label>
                                                            <div class="star-rating">
                                                                <input type="hidden" name="unit_pengalaman" id="unitPengalamanInput" value="<?= $feedback->unit_pengalaman ?? 0 ?>">
                                                                <?php for ($i=1; $i<=5; $i++): ?>
                                                                    <i class="bi <?= ($feedback->unit_pengalaman ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#unitPengalamanInput"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Suasana -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">3. Apakah suasana kerja di unit mendukung pelaksanaan magang/penelitian?</label>
                                                            <div class="star-rating">
                                                                <input type="hidden" name="unit_suasana" id="unitSuasanaInput" value="<?= $feedback->unit_suasana ?? 0 ?>">
                                                                <?php for ($i=1; $i<=5; $i++): ?>
                                                                    <i class="bi <?= ($feedback->unit_suasana ?? 0) >= $i ? 'bi-star-fill text-warning' : 'bi-star text-secondary' ?> fs-3 me-1 star" data-value="<?= $i ?>" data-target="#unitSuasanaInput"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Kesan -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">4. Kesan dan pesan Anda selama magang/penelitian di unit kerja:</label>
                                                            <textarea name="unit_kesan" class="form-control" rows="3" maxlength="300" required placeholder="Tuliskan kesan & pesan Anda (maks. 300 karakter)"><?= $feedback->unit_kesan ?? '' ?></textarea>
                                                            <div class="form-text text-end"><span id="countKesan">0</span>/300</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" id="prevStep" class="btn btn-secondary d-none"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                                                    <button type="button" id="nextStep" class="btn btn-primary"><i class="bi bi-arrow-right"></i> Lanjut</button>
                                                    <button type="submit" id="submitFeedback" class="btn btn-success d-none"><i class="bi bi-send"></i> Simpan Feedback</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                Jadwal Penelitian Anda belum berakhir.
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            Belum Ada Surat Keterangan Penelitian.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

</div>

<!-- <script>
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
</script> -->

<script>
document.addEventListener("DOMContentLoaded", function () {

    // === Star rating interaktif ===
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            const target = document.querySelector(this.dataset.target);
            const value = this.dataset.value;
            target.value = value;

            const parent = this.parentElement;
            parent.querySelectorAll('.star').forEach(s => {
                s.classList.remove('bi-star-fill', 'text-warning');
                s.classList.add('bi-star', 'text-secondary');
            });

            for (let i = 0; i < value; i++) {
                parent.querySelectorAll('.star')[i].classList.remove('bi-star', 'text-secondary');
                parent.querySelectorAll('.star')[i].classList.add('bi-star-fill', 'text-warning');
            }
        });
    });

    // === Hitung karakter textarea ===
    const saran = document.querySelector('textarea[name="diklat_saran"]');
    const kesan = document.querySelector('textarea[name="unit_kesan"]');
    const countSaran = document.getElementById('countSaran');
    const countKesan = document.getElementById('countKesan');

    saran.addEventListener('input', () => countSaran.textContent = saran.value.length);
    kesan.addEventListener('input', () => countKesan.textContent = kesan.value.length);

    // === Navigasi antar step ===
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const nextBtn = document.getElementById('nextStep');
    const prevBtn = document.getElementById('prevStep');
    const submitBtn = document.getElementById('submitFeedback');

    nextBtn.addEventListener('click', function() {
        // Validasi step 1 sebelum lanjut
        const web = document.getElementById('diklatWebsiteInput').value;
        const admin = document.getElementById('diklatAdminInput').value;
        const saranText = saran.value.trim();

        if (web == 0 || admin == 0 || saranText === '') {
            alert('Harap isi semua feedback Pusdiklat terlebih dahulu!');
            return;
        }

        step1.classList.add('d-none');
        step2.classList.remove('d-none');
        nextBtn.classList.add('d-none');
        prevBtn.classList.remove('d-none');
        submitBtn.classList.remove('d-none');
    });

    prevBtn.addEventListener('click', function() {
        step2.classList.add('d-none');
        step1.classList.remove('d-none');
        nextBtn.classList.remove('d-none');
        prevBtn.classList.add('d-none');
        submitBtn.classList.add('d-none');
    });

    // === Validasi akhir sebelum submit ===
    document.getElementById('formFeedback').addEventListener('submit', function(e) {
        const supervisor = document.getElementById('unitSupervisorInput').value;
        const pengalaman = document.getElementById('unitPengalamanInput').value;
        const suasana = document.getElementById('unitSuasanaInput').value;
        const kesanText = kesan.value.trim();

        if (supervisor == 0 || pengalaman == 0 || suasana == 0 || kesanText === '') {
            e.preventDefault();
            alert('Harap isi semua feedback untuk Unit Kerja sebelum menyimpan!');
        }
    });

});
</script>



<?= $this->endSection(); ?>
