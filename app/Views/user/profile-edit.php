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
    <!-- Tab Content Profil-->
    <div class="tab-content" id="profileTabContent">
        <div class="tab-pane fade show active" id="data-pribadi" role="tabpanel">
            <div class="card p-4">
            <!-- <h5 class="fw-bold">Data Pribadi  
                <a href="/data-pribadi" class="text-decoration-none text-muted" title="Edit Data Pribadi">
                    <i class="bi bi-pencil-square"></i>
                </a>
            </h5> -->
            <p class="text-muted">Pastikan data pribadi benar untuk mempermudah proses pendaftaran</p>
            
            <hr>
            <form action="<?= base_url('profile/data-pribadi') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Input Gambar Profil -->
                <?php
                    $user_image = $user_data->user_image ?? 'default.svg';
                    $image_url = base_url('uploads/user-image/' . $user_image);
                    $is_default = $user_image === 'default.svg';
                ?>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto Profil <span class="text-danger">*</span></label>

                    <!-- Selalu tampilkan gambar -->
                    <div class="mb-2">
                        <img src="<?= $image_url ?>" alt="Foto Profil" width="120" class="rounded shadow border">
                    </div>

                    <!-- File input, required jika default.svg -->
                    <input
                        type="file"
                        class="form-control"
                        name="foto"
                        id="foto"
                        accept="image/*"
                        <?= $is_default ? 'required' : '' ?>
                    >
                    <small class="text-muted">Format: JPG, JPEG, atau PNG. Maks. ukuran 2MB.</small>
                </div>



                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label for="fullname" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="fullname" id="fullname" value="<?= esc($user_data->fullname ?? '') ?>" placeholder="Masukkan Nama Lengkap" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- NISN/NIM -->
                        <div class="mb-3">
                            <label for="nisn_nim" class="form-label">NISN/NIM <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nisn_nim" id="nisn_nim" value="<?= esc($user_data->nisn_nim ?? '') ?>" placeholder="Masukkan NISN/NIM" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" value="<?= esc($user_data->email ?? '') ?>" placeholder="Masukkan Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="instagram" class="form-label">Instagram</label>
                            <input type="text" class="form-control" name="instagram" id="instagram" value="<?= esc($user_data->instagram ?? '') ?>" placeholder="@username">
                        </div>
                        <div class="mb-3">
                            <label for="tiktok" class="form-label">Tiktok</label>
                            <input type="text" class="form-control" name="tiktok" id="tiktok" value="<?= esc($user_data->tiktok ?? '') ?>" placeholder="@username">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Jenis Kelamin -->
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <?php $jenis_kelamin = $user_data->jenis_kelamin ?? ''; ?>
                            <select class="form-select" name="jenis_kelamin" id="jenis_kelamin" required>
                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                <option value="L" <?= $jenis_kelamin === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= $jenis_kelamin === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>


                        <!-- No HP -->
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_hp" id="no_hp" value="<?= esc($user_data->no_hp ?? '') ?>" placeholder="Masukkan Nomor Telepon" required>
                        </div>

                        <div class="mb-3">
                            <label for="instagram_follower" class="form-label">Jumlah Followers Instagram</label>
                            <input type="number" class="form-control" name="instagram_followers" id="instagram_follower" value="<?= esc($user_data->instagram_followers ?? '') ?>" placeholder="Masukkan Jumlah Followers Instagram">
                        </div>
                        <div class="mb-3">
                            <label for="tiktok_followers" class="form-label">Jumlah Followers Tiktok</label>
                            <input type="text" class="form-control" name="tiktok_followers" id="tiktok_followers" value="<?= esc($user_data->tiktok_followers ?? '') ?>" placeholder="Masukkan Jumlah Followers Tiktok">
                        </div>
                    </div>
                </div>

                <!-- Alamat KTP -->
                <div class="row">
                    <div class="col-md-6">
                        <?php $province_id = $user_data->province_id ?? ''; ?>
                        <div class="mb-3">
                            <label for="state_id" class="form-label">Provinsi sesuai KTP<span class="text-danger">*</span></label>
                            <select id="state_id" name="state_id" class="form-control select2" required>
                                <option value="" disabled selected>Pilih Provinsi</option>
                                <?php foreach ($listState as $item): ?>
                                    <option value="<?= $item['id'] ?>" <?= $item['id'] == $province_id ? 'selected' : '' ?>>
                                        <?= esc($item['province']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="city_id" class="form-label">Kota/Kabupaten Sesuai KTP<span class="text-danger">*</span></label>
                            <select id="city_id" name="city_id" class="form-control select2" required>
                                <option value="" disabled selected>Pilih Kota</option>
                                <?php if (!empty($listCity)): ?>
                                    <?php foreach ($listCity as $city): ?>
                                        <option value="<?= $city['id'] ?>" <?= $city['id'] == ($user_data->city_id ?? '') ? 'selected' : '' ?>>
                                            <?= $city['regency'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Detail Alamat -->
                <div class="mb-3">
                    <label for="alamat" class="form-label">Detail Alamat Sesuai KTP<span class="text-danger">*</span></label>
                    <textarea class="form-control" name="alamat" id="alamat" style="height: 100px !important;" placeholder="Masukkan alamat lengkap sesuai KTP" required><?= esc($user_data->alamat ?? '') ?></textarea>
                </div>

                <!-- Domisili -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="stateDom_id" class="form-label">Provinsi Domisili</label>
                            <select id="stateDom_id" name="stateDom_id" class="form-control select2">
                                <option value="" disabled selected>Pilih Provinsi</option>
                                <?php foreach ($listState as $item): ?>
                                    <option value="<?= $item['id'] ?>" <?= $item['id'] == ($user_data->provinceDom_id ?? '') ? 'selected' : '' ?>>
                                        <?= $item['province'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cityDom_id" class="form-label">Kota/Kabupaten Domisili</label>
                            <select id="cityDom_id" name="cityDom_id" class="form-control select2">
                                <option value="" disabled selected>Pilih Kota</option>
                                <?php if (!empty($listCityDom)): ?>
                                    <?php foreach ($listCityDom as $city): ?>
                                        <option value="<?= $city['id'] ?>" <?= $city['id'] == ($user_data->cityDom_id ?? '') ? 'selected' : '' ?>>
                                            <?= $city['regency'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Detail Domisili -->
                <div class="mb-3">
                    <label for="domisili" class="form-label">Detail Alamat Domisili</label>
                    <textarea class="form-control" name="domisili" id="domisili" style="height: 100px !important;" placeholder="Masukkan alamat lengkap sesuai Domisili" ><?= esc($user_data->domisili ?? '') ?></textarea>
                </div>

                <!-- Tombol Simpan -->
                <div class="d-flex justify-content-between gap-2">
                    <button class="btn btn-primary" type="submit" name="submit">Simpan Data</button>
                    <a href="<?= site_url('profile/data-akademik') ?>" class="btn btn-outline-secondary">Edit Data Akademik</a>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- End Tab Data Profil -->
<!-- Load jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#state_id, #stateDom_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: function(){
            $(this).data('placeholder');
        },
        allowClear: true
    });

    // Reset kota ketika provinsi sesuai KTP diubah
    $('#state_id').on('change', function () {
        $('#city_id').val(null).trigger('change');
    });

    // Reset kota ketika provinsi domisili diubah
    $('#stateDom_id').on('change', function () {
        $('#cityDom_id').val(null).trigger('change');
    });

    // Untuk kota sesuai KTP
    $('#city_id').select2({
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
                    state_id: $('#state_id').val(), // ini sudah benar
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

    // Untuk kota domisili
    $('#cityDom_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Pilih Kota',
        allowClear: true,
        ajax: {
            url: '<?= base_url(); ?>/api/kotaDom',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    stateDom_id: $('#stateDom_id').val(), // pastikan ini sesuai
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