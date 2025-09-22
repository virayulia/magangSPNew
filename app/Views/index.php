<?= $this->extend('template'); ?>
<?= $this->section('content'); ?>
<style>
    .select2-container--default
    .select2-selection--multiple
    .select2-selection__choice {
    background-color: #dc3545;
    border: none;
    color: white;
    padding: 4px 10px 4px 10px; /* Tambah padding kiri-kanan */
    margin-top: 4px;
    margin-right: 4px;
    border-radius: 6px;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    }

    .select2-container--default
    .select2-selection--multiple
    .select2-selection__choice__remove {
    position: relative;
    margin-right: 6px; /* Jarak X dengan teks */
    left: 0; /* pastikan tidak geser */
    color: rgba(255, 255, 255, 0.8);
    font-weight: bold;
    cursor: pointer;
    }

    .select2-container--default
    .select2-selection--multiple
    .select2-selection__choice__remove:hover {
    color: white;
    }

    .portfolio-img-wrapper {
      position: relative;
      width: 100%;
      padding-top: 100%; /* Rasio 1:1 (square) */
      overflow: hidden;
      border-radius: 8px; /* Kalau mau rounded, boleh dihapus */
    }

    .portfolio-img-wrapper img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .portfolio-img-wrapper img:hover {
      transform: scale(1.05);
    }

</style>

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

<!-- Tambah AOS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
<script>
  const getImageUrl = "<?= base_url('get-images') ?>";
</script>
<script>
    $.getJSON(getImageUrl, function (data) {
    $(".masthead").backstretch(data, {
      duration: 3000,
      fade: 1000,
    });
  });
</script>
<!-- Masthead -->
<header class="masthead">
  <div class="container px-4 px-lg-5 h-100">
    <div class="row gx-4 gx-lg-5 h-100 align-items-start justify-content-start text-start" style="padding-top: 15rem;">
      <div class="col-lg-12" style="margin-left: -13px;" data-aos="fade-down" data-aos-delay="200">
        <h1 class="text-white font-weight-bold">
          Magang <br>PT Semen Padang
        </h1>
        <a class="btn btn-danger btn-xl mt-2" href="/register" data-aos="zoom-in" data-aos-delay="600">
          Daftar Sekarang
        </a>
        <a class="btn btn-danger btn-xl mt-2" href="/magang" data-aos="zoom-in" data-aos-delay="600">
          Lihat Ketersediaan
        </a>
        <a class="btn btn-danger btn-xl mt-2" href="#program-alur" data-aos="zoom-in" data-aos-delay="600">
          Lihat Program dan Alur Pendaftaran
        </a>
      </div>
    </div>
  </div>
</header>

<!-- Program -->
<!-- <section class="page-section bg-primary" id="program">
  <div class="container px-4 px-lg-5">
    <div class="row gx-4 gx-lg-5 justify-content-center" data-aos="fade-up">
      <div class="col-lg-8 text-center">
        <h2 class="text-white mt-0">Program</h2>
        <hr class="divider divider-light" />
      </div>
    </div>
    <div class="row gx-4 gx-lg-5 mt-5 justify-content-center">
     Card Magang 
    <div class="col-md-5 mb-4" data-aos="fade-right" data-aos-delay="300">
      <div class="card h-100 text-center shadow-lg rounded-5">
        <div class="card-body p-5">
          <i class="bi bi-briefcase-fill fs-1 text-primary mb-3"></i>
          <h5 class="h4 mb-2">Magang</h5>
          <p class="text-muted text-justify mb-2">
            Program Magang di PT Semen Padang dirancang sebagai sarana pembelajaran langsung bagi pelajar dan mahasiswa untuk mengaplikasikan pengetahuan yang telah diperoleh selama studi ke dunia kerja nyata.
            <br><br>Melalui program ini, peserta magang akan mendapatkan pengalaman praktis di lingkungan industri, serta pemahaman yang lebih mendalam tentang proses kerja di perusahaan manufaktur terkemuka. 
            <br><br>Program ini juga menjadi jembatan untuk mengembangkan keterampilan profesional serta memperluas wawasan karier peserta.
          </p>
          <div class="text-start text-muted small mb-3">
            <strong>Berkas yang perlu disiapkan:</strong>
            <ul class="mb-0">
              <li><strong>SMK:</strong> Surat permohonan dari sekolah, KTP/KK</li>
              <li><strong>Perguruan Tinggi:</strong> CV, Proposal, Surat permohonan kampus (minimal ttd Kaprodi), KTP/KK</li>
            </ul>
          </div>
          <a href="/magang" class="btn btn-danger w-100 btn-daftar">Daftar Magang</a>
        </div>
      </div>
    </div>

    Card Penelitian
    <div class="col-md-5 mb-4" data-aos="fade-left" data-aos-delay="300">
      <div class="card h-100 text-center shadow-lg rounded-5">
        <div class="card-body p-5">
          <i class="bi bi-journal-bookmark-fill fs-1 text-primary mb-3"></i>
          <h5 class="h4 mb-2">Penelitian</h5>
          <p class="text-muted text-justify mb-2">
            Program Penelitian PT Semen Padang ditujukan bagi mahasiswa dan dosen yang ingin melakukan penelitian akademik di lingkungan perusahaan.
            <br><br>Program ini mendukung penyusunan karya ilmiah seperti skripsi, tesis, maupun penelitian lainnya yang relevan dengan bidang industri semen dan operasional perusahaan. 
            <br><br>Dengan membuka akses terhadap data dan informasi yang diperlukan, PT Semen Padang memberikan kontribusi nyata dalam mendukung pengembangan ilmu pengetahuan dan teknologi di tingkat perguruan tinggi.
          </p>
          <div class="text-start text-muted small mb-3"> <br>
            <strong>Berkas yang perlu disiapkan:</strong>
            <ul class="mb-0">
              <li>CV</li>
              <li>Proposal</li>
              <li>Surat permohonan kampus (minimal ttd Kaprodi)</li>
              <li>KTP/KK</li>
            </ul>
          </div>

          <?php if (logged_in()) : ?>
              <button class="btn btn-danger w-100 btn-daftar" data-bs-toggle="modal" data-bs-target="#modalPenelitianComingSoon">
                  Daftar Penelitian
              </button>
          <?php else : ?>
              <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#modalPenelitianComingSoon">
                  Daftar Penelitian
              </button>
          <?php endif; ?>


        </div>
      </div>
    </div>

    </div>
  </div>
</section> -->

<!-- Masthead -->
<!-- <header class="masthead text-white">
  <div class="container px-4 px-lg-5 h-100">
    <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-start text-start" style="padding-top: 15rem;">
      <div class="col-lg-8" data-aos="fade-down" data-aos-delay="200">
        <h1 class="font-weight-bold mb-4">
          Magang & Penelitian <br>PT Semen Padang
        </h1>
        <div class="d-flex flex-wrap gap-3">
          <a class="btn btn-danger btn-lg" href="/register" data-aos="zoom-in" data-aos-delay="600">Daftar Sekarang</a>
          <a class="btn btn-outline-light btn-lg" href="/magang" data-aos="zoom-in" data-aos-delay="600">Lihat Kuota</a>
          <a class="btn btn-outline-light btn-lg" href="#program-alur" data-aos="zoom-in" data-aos-delay="600">Program & Alur</a>
        </div>
      </div>
    </div>
  </div>
</header> -->

<section class="page-section bg-white text-dark" id="program-alur">
  <div class="container px-4 px-lg-5">
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="mt-0">Program & Alur Pendaftaran</h2>
      <hr class="divider" />
    </div>

    <div class="row align-items-start">
      <!-- Program Card Section -->
      <div class="col-md-5 mb-4" data-aos="fade-right">
        <h4 class="mb-3 fw-bold">Program yang Tersedia</h4>

        <div class="d-flex align-items-center">
          <!-- Card Wrapper -->
          <div id="programCard" class="card p-4 shadow-lg rounded-4 w-100 transition">
            <!-- Isi card akan diubah via JS -->
            <div id="cardContent">
              <!-- Magang default -->
              <div>
                <div class="text-center mb-3">
                  <i class="bi bi-briefcase-fill fs-1 text-danger"></i>
                  <h5 class="fw-bold mt-2">Magang</h5>
                </div>
                <p class="text-muted text-justify">
                 Program Magang di PT Semen Padang dirancang sebagai sarana pembelajaran langsung bagi pelajar dan mahasiswa untuk mengaplikasikan pengetahuan yang telah diperoleh selama studi ke dunia kerja nyata.
                <br><br>Melalui program ini, peserta magang akan mendapatkan pengalaman praktis di lingkungan industri, serta pemahaman yang lebih mendalam tentang proses kerja di perusahaan manufaktur terkemuka. 
                <br><br>Program ini juga menjadi jembatan untuk mengembangkan keterampilan profesional serta memperluas wawasan karier peserta.
                </p>
                <div class="small text-muted">
                  <strong>Berkas yang perlu disiapkan:</strong>
                  <ul class="mb-0">
                    <li><strong>SMK:</strong> Surat permohonan dari sekolah, KTP/KK</li>
                    <li><strong>Perguruan Tinggi:</strong> CV, Proposal, Surat permohonan kampus (minimal ttd Kaprodi), KTP/KK</li>
                  </ul>
                </div>
                <a href="/magang" class="btn btn-danger w-100 mt-3">Daftar Magang</a>
              </div>
            </div>
          </div>

          <!-- Tombol navigasi kanan -->
          <button class="btn btn-outline-danger ms-3" id="nextCardBtn">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>

      <!-- Alur Section -->
      <div class="col-md-7 text-center" data-aos="fade-left">
        <h4 class="mb-3 fw-bold">Alur Pendaftaran Magang</h4>
        <img src="<?= base_url('assets/img/alur-pendaftaran-magang2.png') ?>" 
             alt="Alur Pendaftaran Magang"
             class="img-fluid shadow rounded"
             style="width: 100%; object-fit: contain;">
        <p class="text-muted mt-2 small">Ikuti tahapan sesuai diagram di atas.</p>
      </div>
    </div>
  </div>
</section>

<script>
  const cardContent = document.getElementById('cardContent');
  const btn = document.getElementById('nextCardBtn');
  let isMagang = true;

  btn.addEventListener('click', () => {
    if (isMagang) {
      cardContent.innerHTML = `
        <div>
          <div class="text-center mb-3">
            <i class="bi bi-journal-bookmark-fill fs-1 text-danger"></i>
            <h5 class="fw-bold mt-2">Penelitian</h5>
          </div>
          <p class="text-muted text-justify">
             Program Penelitian PT Semen Padang ditujukan bagi mahasiswa dan dosen yang ingin melakukan penelitian akademik di lingkungan perusahaan.
            <br><br>Program ini mendukung penyusunan karya ilmiah seperti skripsi, tesis, maupun penelitian lainnya yang relevan dengan bidang industri semen dan operasional perusahaan. 
            <br><br>Dengan membuka akses terhadap data dan informasi yang diperlukan, PT Semen Padang memberikan kontribusi nyata dalam mendukung pengembangan ilmu pengetahuan dan teknologi di tingkat perguruan tinggi.
          </p>
          <div class="small text-muted">
            <strong>Berkas yang perlu disiapkan:</strong>
            <ul class="mb-0">
              <li>CV</li>
              <li>Proposal</li>
              <li>Surat permohonan kampus (minimal ttd Kaprodi)</li>
              <li>KTP/KK</li>
            </ul>
          </div>
          <button class="btn btn-danger w-100 mt-3" data-bs-toggle="modal" data-bs-target="#modalPenelitianComingSoon">Daftar Penelitian</button>
        </div>
      `;
      btn.innerHTML = `<i class="bi bi-chevron-left"></i>`;
    } else {
      cardContent.innerHTML = `
        <div>
          <div class="text-center mb-3">
            <i class="bi bi-briefcase-fill fs-1 text-danger"></i>
            <h5 class="fw-bold mt-2">Magang</h5>
          </div>
          <p class="text-muted text-justify">
            Program Magang di PT Semen Padang dirancang sebagai sarana pembelajaran langsung bagi pelajar dan mahasiswa untuk mengaplikasikan pengetahuan yang telah diperoleh selama studi ke dunia kerja nyata.
            <br><br>Melalui program ini, peserta magang akan mendapatkan pengalaman praktis di lingkungan industri, serta pemahaman yang lebih mendalam tentang proses kerja di perusahaan manufaktur terkemuka. 
            <br><br>Program ini juga menjadi jembatan untuk mengembangkan keterampilan profesional serta memperluas wawasan karier peserta.
          </p>
          <div class="small text-muted">
            <strong>Berkas yang perlu disiapkan:</strong>
            <ul class="mb-0">
              <li><strong>SMK:</strong> Surat permohonan dari sekolah, KTP/KK</li>
              <li><strong>Perguruan Tinggi:</strong> CV, Proposal, Surat permohonan kampus (minimal ttd Kaprodi), KTP/KK</li>
            </ul>
          </div>
          <a href="/magang" class="btn btn-danger w-100 mt-3">Daftar Magang</a>
        </div>
      `;
      btn.innerHTML = `<i class="bi bi-chevron-right"></i>`;
    }
    isMagang = !isMagang;
  });
</script>



<!-- Modal Pemberitahuan -->
<div class="modal fade" id="modalPenelitianComingSoon" tabindex="-1" aria-labelledby="modalPenelitianLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalPenelitianLabel">Informasi Pendaftaran Penelitian</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">
          Saat ini, pendaftaran <strong>Penelitian</strong> melalui website masih dalam tahap pengembangan.
          Untuk sementara, proses pendaftaran dilakukan secara manual melalui <strong>Pusat Pendidikan dan Pelatihan (Pusdiklat) PT Semen Padang</strong>.
        </p>
        <p class="mt-2">
          Kami akan segera membuka fitur ini dalam waktu dekat. Terima kasih atas pengertiannya.
        </p>
      </div>
      <div class="modal-footer justify-content-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- modal login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-3">
      <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Peringatan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
          <p>Anda harus login terlebih dahulu untuk mendaftar.</p>
      </div>
      <div class="modal-footer d-flex justify-content-center">
          <a href="<?= base_url('/login'); ?>" class="btn btn-danger rounded-pill px-4">Login</a>
      </div>
      </div>
  </div>
</div>
<!-- modal pendaftaran penelitian -->
<div class="modal fade" id="modalPendaftaranPenelitian" tabindex="-1" aria-labelledby="modalPenelitianLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPenelitianLabel">Pendaftaran Penelitian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="/penelitian/daftar" method="post">
        <div class="modal-body">
          <div class="mb-3">
            <label for="judul" class="form-label">Judul Penelitian</label>
            <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul penelitian" required>
          </div>
          <div class="mb-3">
            <label for="tanggal_mulai" class="form-label">Tanggal Mulai Penelitian</label>
            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
          </div>
          <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Singkat</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Tuliskan ringkasan topik atau metode penelitian"></textarea>
          </div>
          <div class="mb-3">
            <label for="dosen_pembimbing" class="form-label">Nama Dosen Pembimbing</label>
            <input type="text" class="form-control" id="dosen_pembimbing" name="dosen_pembimbing" placeholder="Opsional">
          </div>
          <div class="mb-3">
            <label for="bidang" class="form-label">Bidang Penelitian</label>
            <input type="text" class="form-control" id="bidang" name="bidang" placeholder="Contoh: Teknologi Semen, Lingkungan, dll">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Kirim Pendaftaran</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- FAQ -->
<section class="page-section bg-dark text-white" id="faq">
  <div class="container px-4 px-lg-5">
    <div class="text-center" data-aos="fade-up">
      <h2 class="mb-4">FAQ</h2>
      <hr class="divider divider-light" />
    </div>
    <div class="accordion accordion-flush mt-5" id="faqAccordion">
      <?php
      $faqs = [
        [
          "question" => "Siapa saja yang bisa mendaftar program magang di PT Semen Padang?",
          "answer" => "Program magang terbuka untuk <strong>pelajar tingkat SMK</strong>, serta <strong>mahasiswa aktif</strong> dari perguruan tinggi negeri maupun swasta yang sedang menjalani masa studi."
        ],
        [
          "question" => "Apakah program ini terbuka untuk mahasiswa dari luar Sumatera Barat?",
          "answer" => "Ya, program magang dan penelitian ini terbuka untuk peserta dari <strong>seluruh Indonesia</strong>, selama memenuhi persyaratan dan dapat hadir secara langsung di lokasi kerja."
        ],
        [
          "question" => "Bagaimana cara mendaftar magang atau penelitian di PT Semen Padang?",
          "answer" => "Pendaftaran dilakukan melalui website ini dengan mengisi formulir pendaftaran dan melampirkan dokumen yang diminta, seperti:<br>• Surat pengantar dari institusi<br>• CV<br>• KTP<br>• Proposal (untuk program penelitian)"
        ],
        [
          "question" => "Apakah peserta magang akan mendapatkan uang saku atau fasilitas lain?",
          "answer" => "Saat ini, program magang bersifat <strong>non-paid (tidak berbayar)</strong>. Namun, peserta akan mendapatkan <strong>pengalaman kerja langsung</strong>, <strong>sertifikat magang</strong>, serta <strong>bimbingan dari mentor profesional</strong>."
        ],
        [
          "question" => "Berapa lama durasi magang di PT Semen Padang?",
          "answer" => "Durasi magang bervariasi tergantung kebutuhan peserta dan persetujuan dari pihak PT Semen Padang, biasanya berkisar antara <strong>1 hingga 6 bulan</strong>."
        ],
        [
          "question" => "Apakah bisa memilih unit/divisi tempat magang?",
          "answer" => "Ya, peserta dapat memilih unit/divisi . Namun, penempatan akhir akan <strong>disesuaikan dengan kebutuhan perusahaan</strong> dan latar belakang pendidikan peserta."
        ],
        [
          "question" => "Apakah program penelitian juga membutuhkan proposal?",
          "answer" => "Ya, untuk mengikuti program penelitian, peserta wajib mengirimkan <strong>proposal penelitian</strong> yang menjelaskan <strong>topik, tujuan, dan metode</strong>, agar dapat ditinjau oleh tim terkait."
        ],
        [
          "question" => "Apakah akan ada pembimbing selama magang atau penelitian?",
          "answer" => "Tentu. Setiap peserta akan ditempatkan di bawah <strong>bimbingan mentor</strong> dari PT Semen Padang yang akan mendampingi selama masa magang atau pelaksanaan penelitian."
        ],
        [
          "question" => "Kapan pendaftaran dibuka?",
          "answer" => "Pendaftaran dibuka secara <strong>berkala pada awal bulan</strong>. Silakan cek halaman Beranda website ini atau Media Sosial PT Semen Padang untuk informasi periode pendaftaran terbaru."
        ],
        [
          "question" => "Apakah saya akan mendapat sertifikat setelah menyelesaikan magang?",
          "answer" => "Ya, peserta yang menyelesaikan program dengan baik akan mendapatkan <strong>sertifikat resmi</strong> dari PT Semen Padang sebagai bukti partisipasi."
        ]
      ];


      $i = 1;
      foreach ($faqs as $key => $faq) :
      ?>
        <div class="accordion-item bg-dark border-0" data-aos="fade-up" data-aos-delay="<?= 100 * ($key + 1) ?>">
          <h2 class="accordion-header" id="flush-heading<?= $i ?>">
            <button class="accordion-button collapsed bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $i ?>" aria-expanded="false" aria-controls="flush-collapse<?= $i ?>">
              <?= $faq["question"] ?>
            </button>
          </h2>
          <div id="flush-collapse<?= $i ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?= $i ?>" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-white-50">
              <?= $faq["answer"] ?>
            </div>
          </div>
        </div>
      <?php $i++; endforeach; ?>

    </div>
  </div>
</section>

<!-- Script AOS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
  });


</script>


<?= $this->endSection(); ?>
