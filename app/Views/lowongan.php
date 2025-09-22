<?php $this->setVar('force_scrolled', true); ?>
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
<section class="page-section bg-light" style="padding-top: 140px;">
    <div class="container text-center">
        <h1 class="mb-4">Temukan kesempatan magang di berbagai unit di perusahaan <span class="text-primary">PT Semen Padang</span></h1>
        <p class="lead">Bergabung dengan PT Semen Padang Hari Ini dan Temukan Pengalaman yang Paling Sesuai untuk Anda</p>
    </div>
    
</section>

<!-- Job Filter Section -->
<section class="page-section bg-white py-5">
<div class="container">
  <?php
  $bulanPenempatan = date('F Y', strtotime('+2 months'));
  ?>
  <div class="alert alert-info d-flex align-items-center shadow-sm mt-4" role="alert">
      <i class="bi bi-info-circle-fill me-2"></i>
      Ketersediaan yang ditampilkan berlaku untuk penempatan pada bulan <?= format_bulan_indonesia($bulanPenempatan); ?>.
  </div>
  <?php if (logged_in()): ?>
  <div class="alert alert-info d-flex align-items-center shadow-sm mt-4" role="alert">
      <i class="bi bi-info-circle-fill me-2"></i>
      Setelah login, ketersediaan magang yang ditampilkan akan disesuaikan dengan latar belakang pendidikan Anda. 
      Jika tidak ada ketersediaan yang muncul, kemungkinan belum tersedia unit yang sesuai untuk saat ini.
  </div>
  <?php endif; ?>
<form method="GET" action="<?= base_url('/magang') ?>">
  <div class="row mb-4">
    <div class="col-md-4 mb-2">
    <select class="form-control select2" data-placeholder="Pilih Unit Kerja" name="unit_kerja[]" multiple>
        <?php foreach ($list_unit_kerja as $unit): ?>
          <option value="<?= $unit['unit_kerja'] ?>"><?= $unit['unit_kerja'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3 mb-2">
      <select class="form-control select2" data-placeholder="Pilih Jenjang Pendidikan" name="pendidikan[]" multiple>
        <option value="SMK">SMK</option>
        <option value="Perguruan Tinggi">Perguruan Tinggi</option>
      </select>
    </div>

    <div class="col-md-3 mb-2">
      <select class="form-control select2" data-placeholder="Pilih Jurusan" name="jurusan[]" multiple>
        <?php foreach ($list_jurusan as $jrs): ?>
          <option value="<?= $jrs['nama_jurusan'] ?>"><?= $jrs['nama_jurusan'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2 mb-2">
      <button type="submit" class="btn btn-danger w-100">Cari</button>
    </div>
  </div>
</form>
<?php if ($periode): ?>
    <div class="row">
    <?php if (count($data_unit) > 0): ?>
    <div class="row">
    <?php foreach ($data_unit as $unit): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-2"><?= esc($unit->unit_kerja) ?></h5>
                    <?php if (!is_null($unit->jurusan)): ?>
                    <p class="text-muted mb-2"><?= esc($unit->jurusan) ?></p>
                    <?php else : ?>
                    <p class="text-muted mb-2">Semua Jurusan</p>
                    <?php endif; ?>
                    <p class="text-muted mb-1">Tingkat : <?= esc($unit->tingkat_pendidikan) ?></p>
                    <p><strong><?= $unit->sisa_kuota ?> Posisi</strong>  <small class="text-muted"><?= $unit->jumlah_pendaftar?> pendaftar</small></p>
                    <div class="mb-2">
                        <span class="badge <?= $unit->sisa_kuota > 0 ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $unit->sisa_kuota > 0 ? 'Tersedia' : 'Penuh' ?>
                        </span>
                    </div>
                    <hr>

                    <p class="text-danger fw-semibold mb-3">Penutupan: <?= date('d M Y', strtotime($periode->tanggal_tutup)) ?></p>
                      <?php if (logged_in()) : ?>
                          <?php if ($isProfilComplite) : ?>
                            <button
                            class="btn btn-outline-danger w-100 btn-daftar"
                            data-bs-toggle="modal"
                            data-bs-target="#modalPendaftaran"
                            data-unit-id="<?= $unit->unit_id ?>"
                            >
                            Daftar Sekarang <i class="bi bi-arrow-right"></i>
                            </button>
                        <?php else : ?>
                            <button class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#modalLengkapiProfil">
                                Lengkapi Profil <i class="bi bi-exclamation-circle"></i>
                            </button>
                        <?php endif; ?>
                      <?php else : ?>
                            <a href="/pendaftaran" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#loginModal">Daftar Sekarang <i class="bi bi-arrow-right"></i></a>

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
                      <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
        <!-- paginatoin -->
        <?php if ($totalPages > 1): ?>
            <nav class="d-flex justify-content-center">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= !empty($_SERVER['QUERY_STRING']) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
        <!-- end pagination -->
     </div>
  <?php else: ?>
    <div class="alert alert-info text-center">
        <?php if ($filterByUserJurusan): ?>
            <strong>Tidak ada posisi magang yang sesuai jurusan Anda saat ini.</strong>
        <?php else: ?>
            <strong>Data Posisi Magang Tidak Ada</strong>
        <?php endif; ?>
    </div>
  <?php endif; ?>
<?php else: ?>
<div class="alert alert-warning text-center">
        <strong>Pendaftaran Magang PT Semen Padang Belum Dibuka Saat Ini.</strong><br>
        Silakan cek kembali pada minggu ke-pertama setiap bulannya.
    </div>
<?php endif; ?>
</div>
<?php if (logged_in()) : ?>
<!-- Modal Pendaftaran -->
<div class="modal fade" id="modalPendaftaran" tabindex="-1" aria-labelledby="modalPendaftaranLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-4">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold" id="modalPendaftaranLabel">Konfirmasi Pendaftaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-4">Silakan periksa kembali dokumen Anda sebelum mendaftar.</p>

        <form id="formPendaftaran">
          <?php if (user()->tingkat_pendidikan !== 'SMK') : ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">CV <span class="text-danger">*</span></label>
            <div class="d-flex align-items-center justify-content-between bg-light rounded px-3 py-2">
              <span><?= esc(user()->cv ?? '-') ?></span>
              <?php if (!empty(user()->cv)) : ?>
                <a href="<?= base_url('uploads/cv/' . user()->cv) ?>" target="_blank" class="btn btn-outline-primary btn-sm ms-3">
                  Lihat
                </a>
              <?php endif; ?>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Proposal <span class="text-danger">*</span></label>
            <div class="d-flex align-items-center justify-content-between bg-light rounded px-3 py-2">
              <span><?= esc(user()->proposal ?? '-') ?></span>
              <?php if (!empty(user()->proposal)) : ?>
                <a href="<?= base_url('uploads/proposal/' . user()->proposal) ?>" target="_blank" class="btn btn-outline-primary btn-sm ms-3">
                  Lihat
                </a>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Surat Permohonan Kampus <span class="text-danger">*</span></label>
            <div class="d-flex align-items-center justify-content-between bg-light rounded px-3 py-2">
              <span><?= esc(user()->surat_permohonan ?? '-') ?></span>
              <?php if (!empty(user()->surat_permohonan)) : ?>
                <a href="<?= base_url('uploads/surat-permohonan/' . user()->surat_permohonan) ?>" target="_blank" class="btn btn-outline-primary btn-sm ms-3">
                  Lihat
                </a>
              <?php endif; ?>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Durasi Magang <span class="text-danger">*</span></label>
            <select class="form-select" name="durasi" id="durasiSelect" required>
            <option value="">-- Pilih Durasi --</option>
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?> bulan</option>
            <?php endfor; ?>
            </select>
            </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Kembali</button>
        <button type="button" class="btn btn-primary rounded-pill" id="btnKonfirmasiDaftar">Daftar Sekarang</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Final -->
<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-sm rounded-4">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Konfirmasi Pendaftaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-4">Apakah Anda yakin ingin mendaftar di unit ini?</p>
        <div class="d-flex justify-content-center gap-2">
          <form method="post" action="<?= base_url('magang/daftar') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="durasi" id="durasiHidden">
            <input type="hidden" name="unit_id" id="unitIdHidden">
            <button type="submit" class="btn btn-success rounded-pill px-4">Ya, Daftar</button>
          </form>
          <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Lengkapi Profil -->
<div class="modal fade" id="modalLengkapiProfil" tabindex="-1" aria-labelledby="modalLengkapiProfilLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-warning text-dark rounded-top-4">
        <h5 class="modal-title fw-bold">Lengkapi Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p>Anda perlu melengkapi profil terlebih dahulu untuk dapat mendaftar.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <a href="<?= base_url('/profile') ?>" class="btn btn-warning rounded-pill px-4">Lengkapi Sekarang</a>
      </div>
    </div>
  </div>
</div>
<?php endif ?>

<script>
    $('.select2').select2({
    
    width: '100%',
    allowClear: true,
    placeholder: function(){
        return $(this).data('placeholder');
    }
    });

  let selectedUnitId = null;

  document.querySelectorAll('.btn-daftar').forEach(button => {
    button.addEventListener('click', function () {
      selectedUnitId = this.getAttribute('data-unit-id');
    });
  });

  document.getElementById('btnKonfirmasiDaftar').addEventListener('click', function () {
    const durasi = document.getElementById('durasiSelect').value;
    if (!durasi) {
      alert('Silakan pilih durasi magang.');
      return;
    }

    document.getElementById('durasiHidden').value = durasi;
    document.getElementById('unitIdHidden').value = selectedUnitId;

    const modalPendaftaran = bootstrap.Modal.getInstance(document.getElementById('modalPendaftaran'));
    modalPendaftaran.hide();

    const modalKonfirmasi = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
    modalKonfirmasi.show();
  });
</script>
</section>
<?= $this->endSection(); ?>
