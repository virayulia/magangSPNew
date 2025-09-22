<?php $this->setVar('force_scrolled', true); ?>
<?= $this->extend('template'); ?>
<?= $this->section('content'); ?>

<!-- Tambah AOS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />

<!-- Divider CSS -->
<style>
  .custom-divider {
    width: 60px;
    height: 4px;
    background: #fff;
    margin: 0 auto;
    margin-bottom: 1rem;
    border-radius: 2px;
  }
</style>

<section class="page-section bg-light" id="tentang">
  <div class="container px-4 px-lg-5">
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="mt-0">Tentang Kami</h2>
      <hr class="divider" />
      <p class="text-muted fs-5">Program Magang & Penelitian PT Semen Padang</p>
    </div>
    <div class="row gx-4 gx-lg-5 align-items-center mb-5">
      <div class="col-lg-6 text-center mb-4 mb-lg-0" data-aos="fade-right">
        <img class="img-fluid rounded-4 shadow-lg" src="<?= base_url('assets/img/bg-masthead2.jpeg') ?>" alt="Tentang Kami" />
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <p class="text-muted">
          <strong>Program Magang & Penelitian PT Semen Padang</strong> adalah inisiatif untuk mendukung pengembangan SDM Indonesia. Peserta dapat belajar langsung di lapangan, memperluas wawasan, dan mempersiapkan diri menjadi profesional unggul.
        </p>
        <p class="text-muted">
          Dengan bimbingan mentor berpengalaman, peserta akan memahami proses industri secara utuh serta mengasah keterampilan praktis dan teoritis.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="page-section bg-white">
  <div class="container px-4 px-lg-5">
    <div class="text-center mb-5" data-aos="fade-up">
      <h3>Jenis Program</h3>
      <hr class="divider" />
      <p class="text-muted">Kami menawarkan dua program utama untuk mendukung pengembangan akademik dan karir Anda.</p>
    </div>
    <div class="row gx-4 gx-lg-5">
      <div class="col-md-6 text-center mb-5" data-aos="zoom-in" data-aos-delay="100">
        <i class="bi bi-briefcase-fill fs-1 text-danger mb-3"></i>
        <h4>Program Magang</h4>
        <p class="text-muted">
          Memberikan pengalaman nyata di berbagai unit kerja, meningkatkan keterampilan teknis & soft skill, serta memperluas jaringan profesional.
        </p>
      </div>
      <div class="col-md-6 text-center mb-5" data-aos="zoom-in" data-aos-delay="200">
        <i class="bi bi-journal-bookmark-fill fs-1 text-danger mb-3"></i>
        <h4>Program Penelitian</h4>
        <p class="text-muted">
          Mendukung mahasiswa & akademisi untuk melakukan riset industri yang aplikatif dan relevan, dengan akses data serta dukungan teknis langsung.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="page-section bg-primary text-white">
  <div class="container px-4 px-lg-5">
    <div class="text-center mb-5" data-aos="fade-up">
      <h3>Tujuan Program</h3>
      <div class="custom-divider"></div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <ul class="list-group list-group-flush">
          <li class="list-group-item bg-primary text-white border-0 d-flex align-items-center" data-aos="fade-right" data-aos-delay="100">
            <i class="bi bi-check-circle-fill me-2"></i> Memberikan pengalaman kerja nyata untuk mendukung kesiapan profesional peserta.
          </li>
          <li class="list-group-item bg-primary text-white border-0 d-flex align-items-center" data-aos="fade-right" data-aos-delay="200">
            <i class="bi bi-check-circle-fill me-2"></i> Meningkatkan keterampilan teknis dan soft skill yang relevan.
          </li>
          <li class="list-group-item bg-primary text-white border-0 d-flex align-items-center" data-aos="fade-right" data-aos-delay="300">
            <i class="bi bi-check-circle-fill me-2"></i> Mendukung lahirnya riset inovatif untuk pengembangan industri.
          </li>
          <li class="list-group-item bg-primary text-white border-0 d-flex align-items-center" data-aos="fade-right" data-aos-delay="400">
            <i class="bi bi-check-circle-fill me-2"></i> Membangun sinergi antara perusahaan dan dunia pendidikan.
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="page-section bg-light">
  <div class="container px-4 px-lg-5">
    <div class="text-center mb-4" data-aos="fade-up">
      <h3>Nilai-Nilai yang Kami Junjung</h3>
      <hr class="divider" />
      <p class="text-muted">Kami berlandaskan nilai AKHLAK yang menjadi dasar budaya kerja.</p>
    </div>
    <div class="row gx-4 gx-lg-5 justify-content-center">
      <div class="col-md-10">
        <div class="row text-center">
          <?php
          $values = [
            'Amanah' => 'Dapat dipercaya & bertanggung jawab',
            'Kompeten' => 'Terus belajar & mengembangkan diri',
            'Harmonis' => 'Saling menghargai & mendukung',
            'Loyal' => 'Dedikasi untuk kemajuan bersama',
            'Adaptif' => 'Fleksibel & inovatif terhadap perubahan',
            'Kolaboratif' => 'Mengutamakan kerja sama sinergis'
          ];
          $i = 0;
          foreach ($values as $key => $desc):
          ?>
            <div class="col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="<?= 100 + ($i++ * 100) ?>">
              <div class="border rounded-4 p-3 h-100 shadow-sm bg-white">
                <h5 class="text-danger"><?= $key ?></h5>
                <p class="text-muted small mb-0"><?= $desc ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="page-section bg-white">
  <div class="container px-4 px-lg-5">
    <div class="text-center" data-aos="fade-up">
      <h3>Harapan Kami</h3>
      <hr class="divider" />
      <p class="text-muted fs-6 mx-auto" style="max-width: 800px;">
        Kami berharap kolaborasi ini mampu membentuk generasi muda yang lebih siap menghadapi tantangan global. PT Semen Padang berkomitmen menjadi mitra belajar yang inspiratif, kolaboratif, serta mendukung pengembangan talenta bangsa.
      </p>
    </div>
  </div>
</section>

<!-- Tambah AOS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
  });
</script>

<?= $this->endSection(); ?>
