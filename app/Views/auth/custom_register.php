<?= $this->extend('auth/template_plain'); ?>
<?= $this->section('content'); ?>

<style>
  .stepper {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 2rem;
  }
  .stepper::before {
    content: "";
    position: absolute;
    top: 20px;
    left: 15px;
    right: 15px;
    border-top: 2px dashed #ccc;
    z-index: 0;
  }
  .step {
    position: relative;
    z-index: 1;
    text-align: center;
    flex: 1;
  }
  .step .circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #ccc;
    background-color: #fff;
    margin: 0 auto;
    line-height: 36px;
    font-weight: bold;
    color: #ccc;
    transition: all 0.3s;
  }
  .step.active .circle,
  .step.completed .circle {
    border-color: #dc3545;
    background-color: #dc3545;
    color: #fff;
  }
  .step p {
    margin-top: 8px;
    font-size: 0.9rem;
    color: #333;
  }
  .required-star::after {
    content: " *";
    color: red;
  }
  .password-check {
    font-size: 0.85rem;
    display: flex;
    align-items: center;
  }
  .password-check i {
    margin-right: 5px;
  }
  .password-check.valid i {
    color: green;
  }
  .password-check.invalid i {
    color: red;
  }

  
</style>


<section class="d-flex align-items-center bg-light py-5">
  <div class="container px-4 px-lg-5">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10">
        <div id="formCard" class="card shadow-lg border-0 rounded-4 p-4">
          <h3 class="text-center text-danger fw-bold mb-3">Pendaftaran</h3>
          <?php if (session()->has('errors')): ?>
              <div class="alert alert-danger">
                  <?php foreach (session('errors') as $error): ?>
                      <div><?= esc($error) ?></div>
                  <?php endforeach ?>
              </div>
          <?php endif ?>
          <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger">
                  <?= session('error') ?>
              </div>
          <?php endif; ?>


          <!-- Stepper -->
          <div class="stepper mb-4">
            <div id="step1" class="step active">
              <div class="circle">1</div>
              <p>Data Akun & Diri</p>
            </div>
            <div id="step2" class="step">
              <div class="circle">2</div>
              <p>Data Akademik</p>
            </div>
            <div id="step3" class="step">
              <div class="circle">3</div>
              <p>Dokumen</p>
            </div>
          </div>

          <form id="regForm" action="<?= site_url('register/process'); ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <p class="text-danger"> *Wajib diisi</p>
            <!-- Step 1 -->
            <div class="form-step" id="form-step-1">
              <div class="mb-2">
                <label class="required-star">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap" value="<?= old('nama') ?>" required>
              </div>
              <div class="mb-2">
                <label class="required-star">Email Aktif</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan Email" value="<?= old('email') ?>" required>
              </div>
              <div class="mb-2">
                <label class="required-star">Password</label>
                <input type="password" id="password" name="password" class="form-control" minlength="8" placeholder="Masukkan Password" required >
                <div class="mt-1">
                  <div id="check-length" class="password-check invalid"><i class="bi bi-x-circle"></i> Minimal 8 karakter</div>
                  <div id="check-uppercase" class="password-check invalid"><i class="bi bi-x-circle"></i> Kombinasi huruf kapital dan huruf kecil</div>
                  <div id="check-number" class="password-check invalid"><i class="bi bi-x-circle"></i> Minimal 1 angka atau simbol</div>
                </div>
              </div>
              <div class="mb-2">
                <label class="required-star">Konfirmasi Password</label>
                <input type="password" name="pass_confirm" class="form-control" minlength="8" placeholder="Masukkan Konfirmasi Password" required >
                <div id="confirmError" class="text-danger small mt-1"></div>
              </div>
              <div class="mb-2">
                <label class="required-star">No Handphone</label>
                <input type="number" name="no_hp" class="form-control" placeholder="Masukkan No Handphone" value="<?= old('no_hp') ?>" required>
              </div>
              <div class="mb-2">
                <label class="required-star">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select" required>
                  <option value="" disabled <?= old('jenis_kelamin') === null ? 'selected' : '' ?>>Pilih Jenis Kelamin</option>
                  <option value="L" <?= old('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                  <option value="P" <?= old('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="required-star">NISN/NIM</label>
                <input type="text" name="nisn_nim" class="form-control" placeholder="Masukkan Nomor Induk Mahasiswa/Nomor Induk Siswa Nasional" value="<?= old('nisn_nim') ?>" required>
              </div>
              <div class="mb-2">
                <label class="required-star">Alamat Lengkap (Sesuai KTP)</label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan Alamat Lengkap (Sesuai KTP)" required><?= old('alamat') ?></textarea>
              </div>
              <div class="mb-2">
                <label class="required-star">Provinsi</label>
                <select name="provinsi" id="provinsi" class="form-control" required>
                    <option value="" disabled <?= old('provinsi') === null ? 'selected' : '' ?>>Pilih Provinsi</option>
                    <?php foreach ($provinsi as $p) : ?>
                        <option value="<?= $p['id']; ?>" <?= old('provinsi') == $p['id'] ? 'selected' : '' ?>>
                            <?= $p['province']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="provinsiError" class="text-danger small mt-1"></div>
              </div>
              <div class="mb-2">
                <label class="required-star">Kota/Kabupaten</label>
                <select name="kota" id="kota" class="form-select" required>
                    <option value="" disabled selected>Pilih Kota/Kabupaten</option>
                </select>
                <div id="kotaError" class="text-danger small mt-1"></div>
              </div>
              <div class="mb-2">
                <label>Alamat Lengkap Domisili</label>
                <textarea name="domisili" class="form-control" rows="3" placeholder="Masukkan Alamat Lengkap Domisili"><?= old('domisili') ?></textarea>
              </div>
              <div class="mb-2">
                <label>Provinsi Domisili</label>
                <select name="provinsiDom" id="provinsiDom" class="form-select" >
                    <option value="" disabled selected>Pilih Provinsi Domisili</option>
                    <?php foreach ($provinsi as $p) : ?>
                    <option value="<?= $p['id']; ?>" <?= old('provinsiDom') == $p['id'] ? 'selected' : '' ?>><?= $p['province']; ?></option>
                    <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-2">
                <label>Kota/Kabupaten Domisili</label>
                <select name="kotaDom" id="kotaDom" class="form-select">
                    <option value="" disabled selected>Pilih Kota/Kabupaten Domisili</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="required-star">Foto Profil <small class="text-danger">(format: jpg, jpeg, png, maksimal 2 MB)</small></label>
                <input type="file" name="user_image" class="form-control" accept=".jpg,.jpeg,.png" required>
              </div>
              <div class="mb-2">
                <label>Instagram</label>
                <input type="text" name="instagram" class="form-control" placeholder="@username" value="<?= old('instagram') ?>">
              </div>
              <div class="mb-2">
                <label>Jumlah Follower Instagram</label>
                <input type="number" name="instagram_followers" class="form-control" placeholder="Masukkan jumlah follower" value="<?= old('instagram_followers') ?>">
              </div>

              <div class="mb-2">
                <label>TikTok</label>
                <input type="text" name="tiktok" class="form-control" placeholder="@username" value="<?= old('tiktok') ?>">
              </div>
              <div class="mb-2">
                <label>Jumlah Follower TikTok</label>
                <input type="number" name="tiktok_followers" class="form-control" placeholder="Masukkan jumlah follower" value="<?= old('tiktok_followers') ?>">
              </div>
              <button type="button" class="btn btn-danger w-100" onclick="nextStep(1)">Lanjut</button>
            </div>

            <!-- Step 2 -->
            <div class="form-step d-none" id="form-step-2">
                <div class="mb-2">
                    <label class="required-star">Jenjang Pendidikan</label>
                    <select name="jenjang_pendidikan" id="jenjang-pendidikan" class="form-select" required>
                        <option value="" disabled selected>Pilih</option>
                        <option value="SMK" <?= old('jenjang_pendidikan') == "SMK" ? 'selected' : '' ?>>SMK</option>
                        <option value="D3" <?= old('jenjang_pendidikan') == "D3" ? 'selected' : '' ?>>D3</option>
                        <option value="D4/S1" <?= old('jenjang_pendidikan') == "D4/S1" ? 'selected' : '' ?>>D4/S1</option>
                        <option value="S2" <?= old('jenjang_pendidikan') == "S2" ? 'selected' : '' ?>>S2</option>
                    </select>
                </div>

                <!-- Field lain disembunyikan dulu -->
                <div id="other-fields" style="display: none;">
                    <!-- Asal Instansi -->
                    <div class="mb-2">
                        <label class="required-star" id="labelInstansi">Asal Instansi</label>
                        <select name="instansi" id="instansi" class="form-select" required>
                            <option value="" disabled selected>Pilih Instansi</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="required-star">Jurusan</label>
                        <select name="jurusan" id="jurusan" class="form-select" required>
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <?php foreach($jurusan as $item): ?>
                                <option value="<?= $item['jurusan_id'] ?>" <?= old('jurusan') == $item['jurusan_id'] ? 'selected' : '' ?>><?= $item['nama_jurusan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="group-kelas-semester">
                        <label class="required-star" id="labelSemester">Semester Saat Ini</label>
                        <select name="semester" id="semester" class="form-select" required>
                            <!-- Options akan diganti lewat JS -->
                        </select>
                    </div>
                    <!-- Nilai/IPK -->
                    <div class="mb-3" id="group-nilai">
                        <label class="required-star">IPK Terakhir</label>
                        <input type="number" name="nilai_ipk" id="nilai-ipk" class="form-control" step="0.01" min="1" max="4" value="<?= old('nilai_ipk') ?>" placeholder="Masukkan IPK" required>
                        <small class="text-muted" id="help-nilai">Gunakan titik (.) sebagai pemisah desimal, contoh: 3.50</small>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary w-100 mb-2" onclick="prevStep(2)">Kembali</button>
                <button type="button" class="btn btn-danger w-100" onclick="nextStep(2)">Lanjut</button>
            </div>


            <!-- Step 3 -->
            <div class="form-step d-none" id="form-step-3">

              <div id="pt-fields">
                  <div class="mb-3">
                      <label class="required-star">CV <small class="text-danger">(format : pdf, maksimal 2mb)</small></label>
                      <input type="file" name="cv" class="form-control" accept=".pdf" required>
                  </div>
                  <div class="mb-3">
                      <label class="required-star">Proposal <small class="text-danger">(format : pdf, maksimal 2mb)</small></label>
                      <input type="file" name="proposal" class="form-control" accept=".pdf"required>
                  </div>
              </div>
              <div class="mb-3">
                  <label class="required-star">KTP/KK <small class="text-danger">(format : jpg, jpeg, png, pdf, maksimal 2mb)</small></label>
                  <input type="file" name="ktp_kk" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
              </div>
              <div class="mb-3">
                  <label class="required-star">Surat Permohonan dari Kampus/Sekolah <small class="text-danger">(format : pdf, maksimal 2mb)</small></label>
                  <input type="file" name="surat_permohonan" class="form-control" accept=".pdf" required>
              </div>
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label class="required-star">No Surat Permohonan</label>
                      <input type="text" name="no_surat" class="form-control" placeholder="Masukkan No Surat Permohonan" required>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="required-star">Tanggal Surat Permohonan</label>
                      <input type="date" name="tanggal_surat" class="form-control" required>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="required-star">Nama Pimpinan <small class="text-danger">(yang menandatangani Surat Permohonan)</small></label>
                      <input type="text" name="nama_pimpinan" class="form-control" placeholder="Masukkan Nama Pimpinan" required>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="required-star">Jabatan Pimpinan <small class="text-danger">(yang menandatangani Surat Permohonan)</small></label>
                      <input type="text" name="jabatan" class="form-control" placeholder="Masukkan Jabatan Pimpinan" required>
                  </div>
                  <div class="col-12 mb-3">
                      <label class="required-star" id="labelEmail">Email Instansi (untuk balasan)</label>
                      <input type="email" name="email_instansi" class="form-control" placeholder="Masukkan Email" required>
                  </div>
              </div>
              <button type="button" class="btn btn-secondary w-100 mb-2" onclick="prevStep(3)">Kembali</button>
              <button type="submit" class="btn btn-success w-100">Simpan & Daftar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Load jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {
    const pendidikan = document.getElementById('jenjang-pendidikan');
    const otherFields = document.getElementById('other-fields');
    const labelInstansi = document.getElementById('labelInstansi');
    const groupNilai = document.getElementById('group-nilai');
    const nilaiInput = document.querySelector('input[name="nilai_ipk"]');
    const labelSemester = document.getElementById('labelSemester');
    const instansiSelect = document.getElementById('instansi');
    

    // Elemen Step 3
    const cvField = document.querySelector('input[name="cv"]');
    const proposalField = document.querySelector('input[name="proposal"]');
    const cvGroup = cvField.closest('.mb-3');
    const proposalGroup = proposalField.closest('.mb-3');
    const labelEmail = document.getElementById('labelEmail');
    const inputEmail = document.querySelector('input[name="email_instansi"]');

    function toggleFields() {
    const value = pendidikan.value;
    if (!value) {
        otherFields.style.display = 'none';
        return;
    }

    // Tampilkan field lain di step 2
    otherFields.style.display = 'block';

    // Reset opsi semester/kelas
    const semesterSelect = document.getElementById('semester');
    semesterSelect.innerHTML = '';

      if (value === 'SMK') {
          labelInstansi.textContent = 'Asal Sekolah';
          instansiSelect.options[0].textContent = 'Pilih Sekolah';

          // Kelas: select kelas 11 dan 12
          labelSemester.textContent = 'Kelas Saat Ini';
          semesterSelect.innerHTML = `
              <option value="" disabled selected>Pilih Kelas</option>
              <option value="11">Kelas 11</option>
              <option value="12">Kelas 12</option>
          `;

          // Sembunyikan IPK
          groupNilai.style.display = 'none';
          nilaiInput.removeAttribute('required');

          // Step 3: hide CV & Proposal
          cvGroup.style.display = 'none';
          proposalGroup.style.display = 'none';
          cvField.removeAttribute('required');
          proposalField.removeAttribute('required');
          labelEmail.textContent = 'Email Kepala Sekolah';
          inputEmail.placeholder = 'Masukkan Email Kepala Sekolah';

      } else {
          labelInstansi.textContent = 'Asal Kampus';
          instansiSelect.options[0].textContent = 'Pilih Perguruan Tinggi';

          // Semester: select dari semester 4–13
          labelSemester.textContent = 'Semester Saat Ini';
          semesterSelect.innerHTML = '<option value="" disabled selected>Pilih Semester</option>';
          for (let i = 4; i <= 13; i++) {
              const opt = document.createElement('option');
              opt.value = i;
              opt.textContent = `Semester ${i}`;
              semesterSelect.appendChild(opt);
          }

          // Tampilkan IPK
          groupNilai.style.display = 'block';
          nilaiInput.setAttribute('required', 'required');

          // Step 3: show CV & Proposal
          cvGroup.style.display = 'block';
          cvField.setAttribute('required', 'required');
          proposalGroup.style.display = 'block';
          labelEmail.textContent = 'Email Ketua Program Studi';
          inputEmail.placeholder = 'Masukkan Ketua Program Studi';
      }

        getInstansi(value);
    }

    function getInstansi(jenjang) {
        instansiSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
        let kelompok = (jenjang === 'SMK') ? 'smk' : 'pt';
        let placeholder = (jenjang === 'SMK') ? 'Pilih Sekolah' : 'Pilih Perguruan Tinggi';

        $.ajax({
            url: '/get-instansi',
            type: 'GET',
            data: { kelompok: kelompok },
            success: function(response) {
                instansiSelect.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
                response.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item.instansi_id;
                    option.textContent = item.nama_instansi;
                    instansiSelect.appendChild(option);
                });
            },
            error: function() {
                instansiSelect.innerHTML = `<option value="" disabled selected>Gagal load ${placeholder.toLowerCase()}</option>`;
            }
        });
    }


    pendidikan.addEventListener('change', toggleFields);
});

document.getElementById('nilai-ipk').addEventListener('input', function () {
    const val = this.value;
    if (val.includes('.')) {
        const decimal = val.split('.')[1];
        if (decimal.length > 2) {
            this.value = parseFloat(val).toFixed(2);
        }
    }
});

</script>

<script>
function nextStep(current) {
  let currentStep = document.getElementById('form-step-' + current);
  let inputs = currentStep.querySelectorAll("input, select");

  // Reset error messages
  document.getElementById('confirmError').innerText = '';
  document.getElementById('provinsiError').innerText = '';
  document.getElementById('kotaError').innerText = '';

  if (parseInt(current) === 1) {
    const pass = document.querySelector("input[name='password']").value;
    const confirmInput = document.querySelector("input[name='pass_confirm']");
    const confirm = confirmInput.value;

    if (pass !== confirm) {
      document.getElementById('confirmError').innerText = "Password dan konfirmasi password tidak sama!";
      confirmInput.focus();
      return;
    }

    // Validasi manual provinsi
    const provinsiVal = $('#provinsi').val();
    const kotaVal = $('#kota').val();
    let hasError = false;

    if (!provinsiVal) {
      document.getElementById('provinsiError').innerText = "Silakan pilih provinsi.";
      hasError = true;
    }

    if (!kotaVal) {
      document.getElementById('kotaError').innerText = "Silakan pilih kota/kabupaten.";
      hasError = true;
    }

    if (hasError) {
      return;
    }
  }

  // Validasi HTML native (selain provinsi & kota)
  for (let i = 0; i < inputs.length; i++) {
    if (inputs[i].name !== 'provinsi' && inputs[i].name !== 'kota' && !inputs[i].checkValidity()) {
      inputs[i].reportValidity();
      return;
    }
  }

  // Lanjut ke step berikutnya
  currentStep.classList.add('d-none');
  document.getElementById('form-step-' + (parseInt(current) + 1)).classList.remove('d-none');

  document.getElementById('step' + current).classList.remove('active');
  document.getElementById('step' + current).classList.add('completed');
  document.getElementById('step' + (parseInt(current) + 1)).classList.add('active');

  document.getElementById('formCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
}


  function prevStep(current) {
    document.getElementById('form-step-' + current).classList.add('d-none');
    document.getElementById('form-step-' + (current - 1)).classList.remove('d-none');

    document.getElementById('step' + current).classList.remove('active');
    document.getElementById('step' + (current - 1)).classList.add('active');
    document.getElementById('step' + (current - 1)).classList.remove('completed');

    document.getElementById('formCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  
  const passwordInput = document.getElementById('password');

  passwordInput.addEventListener('input', function() {
    const val = passwordInput.value;
    let validLength = val.length >= 8;
    let validUpper = /[A-Z]/.test(val) && /[a-z]/.test(val);
    let validNumber = /\d|[^a-zA-Z]/.test(val);

    updateCheck('check-length', validLength);
    updateCheck('check-uppercase', validUpper);
    updateCheck('check-number', validNumber);
  });

  function updateCheck(id, valid) {
    const item = document.getElementById(id);
    if (valid) {
      item.classList.remove('invalid');
      item.classList.add('valid');
      item.querySelector('i').className = 'bi bi-check-circle';
    } else {
      item.classList.remove('valid');
      item.classList.add('invalid');
      item.querySelector('i').className = 'bi bi-x-circle';
    }
  }

  $(document).ready(function() {
    $('#provinsi, #instansi, #jurusan, #provinsiDom, #kotaDom').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: function(){
            $(this).data('placeholder');
        },
        allowClear: true
    });
    $('#provinsi').on('change', function () {
        $('#kota').val(null).trigger('change');
    });
    $('#kota').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Pilih Kota',
        allowClear: true,
        ajax: {
            url: '<?= base_url(); ?>/api/kota',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    provinsi: $('#provinsi').val(), // ini sudah benar
                    searchTerm: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.data
                };
            },
            cache: true
        }
    });
    $('#provinsiDom').on('change', function () {
        $('#kotaDom').val(null).trigger('change');
    });
    $('#kotaDom').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Pilih Kota',
        allowClear: true,
        ajax: {
            url: '<?= base_url(); ?>/api/kota',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    provinsi: $('#provinsiDom').val(), // ini sudah benar
                    searchTerm: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.data
                };
            },
            cache: true
        }
    });
  });

</script>

<?= $this->endSection(); ?>
