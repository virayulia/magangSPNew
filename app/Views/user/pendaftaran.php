<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>
<section id="pendaftaran" class="py-5">
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Formulir Pendaftaran Magang / Penelitian</h2>
            <p class="lead mb-0">Silakan isi data di bawah ini dengan lengkap</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form id="formPendaftaran" action="<?= base_url('/pendaftaran/save'); ?>" method="post" enctype="multipart/form-data" novalidate>
                    
                    <div class="form-floating mb-3">
                        <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Lengkap" required />
                        <label for="nama">Nama Lengkap</label>
                        <div class="invalid-feedback">Nama lengkap harus diisi.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="nim" name="nim" type="text" placeholder="NIM" required pattern="\d+" />
                        <label for="nim">NIM</label>
                        <div class="invalid-feedback">NIM harus berupa angka.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="nik" name="nik" type="text" placeholder="NIK" required pattern="\d{16}" />
                        <label for="nik">NIK</label>
                        <div class="invalid-feedback">NIK harus 16 digit angka.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="universitas" name="universitas" type="text" placeholder="Perguruan Tinggi" required />
                        <label for="universitas">Perguruan Tinggi</label>
                        <div class="invalid-feedback">Perguruan Tinggi harus diisi.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="fakultas" name="fakultas" type="text" placeholder="Fakultas" required />
                        <label for="fakultas">Fakultas</label>
                        <div class="invalid-feedback">Fakultas harus diisi.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="jurusan" name="jurusan" type="text" placeholder="Jurusan" required />
                        <label for="jurusan">Jurusan</label>
                        <div class="invalid-feedback">Jurusan harus diisi.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="semester" name="semester" type="number" placeholder="Semester" min="1" max="14" required />
                        <label for="semester">Semester</label>
                        <div class="invalid-feedback">Semester harus diisi dengan angka antara 1-14.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="no_hp" name="no_hp" type="text" placeholder="Nomor HP" required pattern="^\d{10,15}$" />
                        <label for="no_hp">Nomor HP</label>
                        <div class="invalid-feedback">Nomor HP harus 10-15 digit angka.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select" id="jenis_pengajuan" name="jenis_pengajuan" required>
                            <option value="" selected disabled>Pilih Jenis Pengajuan</option>
                            <option value="Magang">Magang</option>
                            <option value="Penelitian">Penelitian</option>
                        </select>
                        <label for="jenis_pengajuan">Jenis Pengajuan</label>
                        <div class="invalid-feedback">Pilih salah satu jenis pengajuan.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" type="date" required />
                        <label for="tanggal_pengajuan">Tanggal Mulai Magang/Penelitian</label>
                        <div class="invalid-feedback">Tanggal mulai magang/penelitian harus diisi.</div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="lama_pelaksanaan" name="lama_pelaksanaan" type="number" placeholder="Lama Magang/Penelitian" required min="1" />
                        <label for="lama_pelaksanaan">Lama Magang/Penelitian <span>(Masukkan dalam hitungan hari kerja)</span></label>
                        <div class="invalid-feedback">Isi dengan jumlah hari kerja (angka saja).</div>
                    </div>

                    <div class="mb-3">
                        <label for="cv" class="form-label">Upload CV (PDF/DOC/DOCX)</label>
                        <input class="form-control" id="cv" name="cv" type="file" accept=".pdf,.doc,.docx" required />
                        <div class="invalid-feedback">Harap upload CV dalam format PDF, DOC, atau DOCX.</div>
                    </div>

                    <div class="mb-3">
                        <label for="proposal" class="form-label">Upload Proposal (PDF/DOC/DOCX)</label>
                        <input class="form-control" id="proposal" name="proposal" type="file" accept=".pdf,.doc,.docx" required />
                        <div class="invalid-feedback">Harap upload Proposal dalam format PDF, DOC, atau DOCX.</div>
                    </div>

                    <div class="mb-3">
                        <label for="surat_permohonanpt" class="form-label">Upload Surat Permohonan Perguruan Tinggi (PDF/DOC/DOCX)</label>
                        <input class="form-control" id="surat_permohonanpt" name="surat_permohonanpt" type="file" accept=".pdf,.doc,.docx" required />
                        <div class="invalid-feedback">Harap upload Surat Permohonan Perguruan Tinggi dalam format PDF, DOC, atau DOCX.</div>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary btn-lg" type="submit">Kirim Pendaftaran</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

<!-- Validasi JavaScript -->
<script>
    (function () {
        'use strict'

        var forms = document.querySelectorAll('#formPendaftaran')

        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated');
                }, false);
            });
    })();
</script>


<?= $this->endSection(); ?>