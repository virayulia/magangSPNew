<?php $uri = service('uri'); ?>
<?= $this->extend('user/template'); ?>
<?= $this->section('main-content'); ?>
<!-- Tab Data Profil -->
<div class="profile-card">
    <?php if (session()->get('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->get('error')) ?></div>
<?php endif; ?>
    <!-- Tabs -->
    <ul class="nav nav-tabs profile-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?= current_url(true)->getSegment(2) === 'data-pribadi' ? 'active' : '' ?>" 
        href="<?= base_url('profile/data-pribadi') ?>">Data Pribadi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= current_url(true)->getSegment(2) === 'data-akademik' ? 'active' : '' ?>" 
        href="<?= base_url('profile/data-akademik') ?>">Data Akademik</a>
    </li>
    </ul>

    <!-- Tab Content Akademik -->
    <div class="tab-content" id="profileTabContent">
        <div class="tab-pane fade show active" id="data-akademik" role="tabpanel">
            <div class="card p-4">
                <p class="text-muted">Pastikan data akademik benar untuk mempermudah proses pendaftaran</p>
                <small class="text-danger">*Wajib diisi</small>
                <hr>
                <form action="/profile/data-akademik" method="post">
                    <?= csrf_field() ?>

                    <!-- Jenjang Pendidikan -->
                    <div class="mb-3">
                        <label for="jenjang-pendidikan" class="form-label">Jenjang Pendidikan<span class="text-danger">*</span></label>
                        <select name="jenjang_pendidikan" id="jenjang-pendidikan" class="form-select" required>
                            <option value="" disabled <?= empty($user_data->tingkat_pendidikan) ? 'selected' : '' ?>>Pilih</option>
                            <option value="SMK" <?= $user_data->tingkat_pendidikan === "SMK" ? 'selected' : '' ?>>SMK</option>
                            <option value="D3" <?= $user_data->tingkat_pendidikan === "D3" ? 'selected' : '' ?>>D3</option>
                            <option value="D4/S1" <?= $user_data->tingkat_pendidikan === "D4/S1" ? 'selected' : '' ?>>D4/S1</option>
                            <option value="S2" <?= $user_data->tingkat_pendidikan === "S2" ? 'selected' : '' ?>>S2</option>
                        </select>
                    </div>

                    <!-- Asal Instansi -->
                    <div class="mb-3">
                        <label for="instansi" class="form-label" id="labelInstansi">Asal Instansi<span class="text-danger">*</span></label>
                        <select name="instansi" id="instansi" class="form-control" required>
                            <option value="" disabled selected>Pilih Instansi</option>
                            <?php foreach ($instansi as $item): ?>
                                <option value="<?= $item['instansi_id'] ?>" <?= $item['instansi_id'] == $user_data->instansi_id ? 'selected' : '' ?>>
                                    <?= esc($item['nama_instansi']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Jurusan -->
                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan<span class="text-danger">*</span></label>
                        <select name="jurusan" id="jurusan" class="form-control" required>
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <?php foreach ($jurusan as $item): ?>
                                <option value="<?= $item['jurusan_id'] ?>" <?= $item['jurusan_id'] == $user_data->jurusan_id ? 'selected' : '' ?>>
                                    <?= esc($item['nama_jurusan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Semester / Kelas -->
                    <div class="mb-3">
                        <label for="semester" class="form-label" id="labelSemester">Semester/Kelas<span class="text-danger">*</span></label>
                        <select name="semester" id="semester" class="form-select" required>
                            <!-- Akan diisi JS -->
                        </select>
                    </div>

                    <!-- Nilai IPK  -->
                    <div class="mb-3" id="group-nilai">
                        <label for="nilai-ipk" class="form-label">IPK<span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="4" class="form-control" name="ipk" id="nilai-ipk" placeholder="Contoh: 3.75" value="<?= esc($user_data->nilai_ipk ?? '') ?>">
                    </div>

                    <button type="submit" class="btn btn-primary ">Simpan Data</button>
                </form>



            </div>
        </div>
    </div>

</div>
<!-- End Tab Data Profil -->
<!-- Load jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<!-- Script logika dinamis -->
<script>
$('#instansi, #jurusan').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: function(){
            $(this).data('placeholder');
        },
        allowClear: true
});
document.addEventListener('DOMContentLoaded', () => {
    const jenjangSelect     = document.getElementById('jenjang-pendidikan');
    const labelInstansi     = document.getElementById('labelInstansi');
    const instansiSelect    = document.getElementById('instansi');
    const semesterSelect    = document.getElementById('semester');
    const semesterLabel     = document.getElementById('labelSemester');
    const nilaiGroup        = document.getElementById('group-nilai');
    const nilaiInput        = document.getElementById('nilai-ipk');

    const selectedJenjang   = jenjangSelect.value;
    const selectedInstansi  = "<?= $user_data->instansi_id ?? '' ?>";
    const selectedSemester  = "<?= $user_data->semester ?? '' ?>";

    function updateByJenjang({ reloadInstansiData = true } = {}) {
        const jenjang = jenjangSelect.value;

        // Label instansi
        labelInstansi.textContent = (jenjang === 'SMK') ? 'Asal Sekolah' : 'Asal Perguruan Tinggi';

        // Update Semester/Kelas
        semesterSelect.innerHTML = '';
        if (jenjang === 'SMK') {
            semesterLabel.textContent = 'Kelas';
            semesterSelect.innerHTML = `
                <option value="" disabled selected>Pilih Kelas</option>
                <option value="11" ${selectedSemester == 11 ? 'selected' : ''}>Kelas 11</option>
                <option value="12" ${selectedSemester == 12 ? 'selected' : ''}>Kelas 12</option>
            `;
        } else {
            semesterLabel.textContent = 'Semester';
            semesterSelect.innerHTML = `<option value="" disabled selected>Pilih Semester</option>`;
            for (let i = 4; i <= 13; i++) {
                const selected = selectedSemester == i ? 'selected' : '';
                semesterSelect.innerHTML += `<option value="${i}" ${selected}>Semester ${i}</option>`;
            }
        }

        // Tampilkan/sembunyikan IPK
        if (jenjang === 'SMK') {
            nilaiGroup.style.display = 'none';
            nilaiInput.removeAttribute('required');
        } else {
            nilaiGroup.style.display = '';
            nilaiInput.setAttribute('required', 'required');
        }

        // Reload instansi hanya jika diminta (saat jenjang berubah)
        if (reloadInstansiData) {
            fetchInstansi(jenjang);
        }
    }

    function fetchInstansi(jenjang) {
        const kelompok = jenjang === 'SMK' ? 'smk' : 'pt';
        const placeholder = jenjang === 'SMK' ? 'Pilih Sekolah' : 'Pilih Perguruan Tinggi';

        // Hapus Select2 dulu jika ada
        if ($.fn.select2 && $('#instansi').hasClass("select2-hidden-accessible")) {
            $('#instansi').select2('destroy');
        }

        instansiSelect.innerHTML = `<option value="" disabled>Memuat...</option>`;

        $.get('/get-instansi', { kelompok }).done(res => {
            instansiSelect.innerHTML = `<option value="" disabled>${placeholder}</option>`;
            res.forEach(item => {
                const option = document.createElement('option');
                option.value = item.instansi_id;
                option.textContent = item.nama_instansi;
                if (item.instansi_id == selectedInstansi) {
                    option.selected = true;
                }
                instansiSelect.appendChild(option);
            });

            // Inisialisasi ulang Select2 dan set nilai kembali
            $('#instansi').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: placeholder,
                allowClear: true
            }).val(selectedInstansi).trigger('change');

        }).fail(() => {
            instansiSelect.innerHTML = `<option value="" disabled selected>Gagal memuat data</option>`;
        });
    }

    // Format IPK agar maksimal 2 digit desimal
    nilaiInput.addEventListener('input', e => {
        const val = e.target.value;
        if (val.includes('.') && val.split('.')[1].length > 2) {
            e.target.value = parseFloat(val).toFixed(2);
        }
    });

    // Saat jenjang berubah, update dan reload instansi
    jenjangSelect.addEventListener('change', () => {
        updateByJenjang({ reloadInstansiData: true });
    });

    // Pertama kali jalankan TANPA reload instansi
    updateByJenjang({ reloadInstansiData: false });

    // Inisialisasi Select2 jurusan (instansi jangan dulu)
    $('#jurusan').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Pilih Jurusan',
        allowClear: true
    });
});
</script>







<?= $this->endSection(); ?>