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

<!-- Flash Sukses dn error-->
<div class="profile-card">

    <!-- Flash message -->
    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('message') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs profile-tabs mb-4" id="profileTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="data-pribadi-tab" data-bs-toggle="tab" data-bs-target="#data-pribadi" type="button" role="tab">Data Pribadi</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="data-akademik-tab" data-bs-toggle="tab" data-bs-target="#data-akademik" type="button" role="tab">Data Akademik</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen" type="button" role="tab">Dokumen</button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabContent">

        <!-- Data Pribadi -->
        <div class="tab-pane fade show active" id="data-pribadi" role="tabpanel">
            <div class="card p-4 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Data Pribadi</h5>
                    <a href="/profile/data-pribadi" class="text-muted" title="Edit"><i class="bi bi-pencil-square fs-5"></i></a>
                </div>
                <p class="text-muted mb-4">Pastikan data pribadi benar untuk mempermudah proses pendaftaran.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-1 fw-semibold">Nama Lengkap</p>
                        <p class="text-muted"><?= esc($user_data->fullname ?? 'Data belum diisi'); ?></p>
                        <p class="mb-1 fw-semibold">NISN/NIM</p>
                        <p class="text-muted"><?= esc($user_data->nisn_nim ?? 'Data belum diisi'); ?></p>
                        <p class="mb-1 fw-semibold">Instagram</p>
                        <p class="text-muted"><?= esc($user_data->instagram ?? 'Data belum diisi'); ?></p>
                        <p class="mb-1 fw-semibold">Tiktok</p>
                        <p class="text-muted"><?= esc($user_data->tiktok ?? 'Data belum diisi'); ?></p>
                        <p class="mb-1 fw-semibold">Email</p>
                        <p class="text-muted"><?= esc($user_data->email ?? 'Data belum diisi'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 fw-semibold">Jenis Kelamin</p>
                        <p class="text-muted">
                            <?php if ($user_data->jenis_kelamin === 'L') : ?>
                                Laki-Laki
                            <?php elseif ($user_data->jenis_kelamin === 'P') : ?>
                                Perempuan
                            <?php else : ?>
                                Data belum diisi
                            <?php endif; ?>
                        </p>
                        <p class="mb-1 fw-semibold">No Handphone</p>
                        <p class="text-muted"><?= esc($user_data->no_hp ?? 'Data belum diisi'); ?></p>
                        <p class="mb-1 fw-semibold">Jumlah Follower Instagram</p>
                        <p class="text-muted"><?= esc($user_data->instagram_followers ?? 'Data belum diisi'); ?></p>
                        <p class="mb-1 fw-semibold">Jumlah Follower Tiktok</p>
                        <p class="text-muted"><?= esc($user_data->tiktok_followers ?? 'Data belum diisi'); ?></p>
                    </div>
                </div>

                <p class="mb-1 fw-semibold">Alamat Sesuai KTP</p>
                <p class="text-muted">
                    <?php
                        $alamat = $user_data->alamat ?? '';
                        $kota = trim(($user_data->tipe_kota_ktp ?? '') . ' ' . ($user_data->kota_ktp ?? ''));
                        $prov = $user_data->provinsi_ktp ?? '';
                        $parts = array_filter([$alamat, $kota, $prov]);
                        echo esc(implode(', ', $parts)) ?: 'Data belum diisi';
                    ?>
                </p>

                <p class="mb-1 fw-semibold">Alamat Domisili</p>
                <p class="text-muted">
                    <?php
                        $alamat = $user_data->domisili ?? '';
                        $kota = trim(($user_data->tipe_kota_domisili ?? '') . ' ' . ($user_data->kota_domisili ?? ''));
                        $prov = $user_data->provinsi_domisili ?? '';
                        $parts = array_filter([$alamat, $kota, $prov]);
                        echo esc(implode(', ', $parts)) ?: 'Data belum diisi';
                    ?>
                </p>
            </div>
        </div>

        <!-- Data Akademik -->
        <div class="tab-pane fade" id="data-akademik" role="tabpanel">
            <div class="card p-4 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Informasi Akademik</h5>
                    <a href="/profile/data-akademik" class="text-muted" title="Edit"><i class="bi bi-pencil-square fs-5"></i></a>
                </div>
                <p class="text-muted mb-4">Pastikan data akademik benar untuk mempermudah proses pendaftaran.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-1 fw-semibold">Tingkat Pendidikan</p>
                        <p class="text-muted"><?= esc($user_data->tingkat_pendidikan ?? 'Data belum diisi'); ?></p>
                        <?php if($user_data->tingkat_pendidikan === 'SMK'):?>
                            <p class="mb-1 fw-semibold">Sekolah</p>
                            <p class="text-muted"><?= esc($user_data->nama_instansi ?? 'Data belum diisi'); ?></p>
                        <?php else: ?>
                            <p class="mb-1 fw-semibold">Perguruan Tinggi</p>
                            <p class="text-muted"><?= esc($user_data->nama_instansi ?? 'Data belum diisi'); ?></p>
                        <?php endif; ?> 
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 fw-semibold">Jurusan</p>
                        <p class="text-muted"><?= esc($user_data->nama_jurusan ?? 'Data belum diisi'); ?></p>
                        <?php if($user_data->tingkat_pendidikan === 'SMK'):?>
                            <p class="mb-1 fw-semibold">Kelas</p>
                            <p class="text-muted"><?= esc($user_data->semester ?? 'Data belum diisi'); ?></p>
                        <?php else: ?>
                            <p class="mb-1 fw-semibold">Semester</p>
                            <p class="text-muted"><?= esc($user_data->semester ?? 'Data belum diisi'); ?></p>
                        <?php endif; ?> 
                        <?php if($user_data->tingkat_pendidikan != 'SMK'): ?>
                        <p class="mb-1 fw-semibold">IPK</p>
                        <p class="text-muted"><?= esc($user_data->nilai_ipk ?? 'Data belum diisi'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen -->
        <div class="tab-pane fade" id="dokumen" role="tabpanel">
            <div class="tab-pane fade show non-active" id="dokumen" role="tabpanel">
                <div class="card p-4 shadow">
                    <h5 class="fw-bold mb-3">
                        Kelengkapan Dokumen
                    </h5>
                    <p class="text-muted mb-4">Lengkapi dokumen untuk mempermudah proses pendaftaran magang.
                        <br><small class="text-danger">*Wajib diisi</small>
                    </p>

                    <!-- CV -->
                    <div class="border rounded p-3 mb-3 shadow-sm">
                        <h6 class="fw-semibold mb-2">Curriculum Vitae<span class="text-danger">*</span></h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <?php if (!empty($user_data->cv)): ?>
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i> <?= esc($user_data->cv) ?>
                                </div>
                                <div>
                                    <a href="<?= base_url('uploads/cv/' . $user_data->cv) ?>" target="_blank" class="btn btn-primary btn-sm me-2">Lihat file</a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteCV(<?= $user_data->id ?>)">Delete</button>                            
                                </div>
                            <?php else: ?>
                                <div class="text-muted">Dokumen belum diupload</div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('cvFile').click();">Upload file</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="cv" id="cvFile" style="display:none;" onchange="uploadCV(this)">
                        <div id="uploadMessage" class="mt-2"></div>
                    </div>

                    <!-- Proposal -->
                    <div class="border rounded p-3 mb-3 shadow-sm">
                        <h6 class="fw-semibold mb-2">Proposal</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <?php if (!empty($user_data->proposal)): ?>
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i> <?= esc($user_data->proposal) ?>
                                </div>
                                <div>
                                    <a href="<?= base_url('uploads/proposal/' . $user_data->proposal) ?>" target="_blank" class="btn btn-primary btn-sm me-2">Lihat file</a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteProposal(<?= $user_data->id ?>)">Delete</button>                            
                                </div>
                            <?php else: ?>
                                <div class="text-muted">Dokumen belum diupload</div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('proposalFile').click();">Upload file</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="proposal" id="proposalFile" style="display:none;" onchange="uploadProposal(this)">
                        <div id="uploadMessageProposal" class="mt-2"></div>
                    </div>

                    <!-- Surat Permohonan -->
                    <div class="border rounded p-3 mb-3 shadow-sm">
                        <h6 class="fw-semibold mb-2">Surat Permohonan<span class="text-danger">*</span></h6>
                        <?php if (!empty($user_data->surat_permohonan)): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i> <?= esc($user_data->surat_permohonan) ?>
                                </div>
                                <div>
                                    <a href="<?= base_url('uploads/surat-permohonan/' . $user_data->surat_permohonan) ?>" target="_blank" class="btn btn-primary btn-sm me-2">Lihat file</a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteSurat(<?= $user_data->id ?>)">Delete</button>  
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <p class="mb-1 fw-bold">No. Surat</p>
                                    <p class="text-muted"><?= esc($user_data->no_surat) ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 fw-bold">Tanggal Surat</p>
                                    <p class="text-muted"><?= esc(date('d-m-Y', strtotime($user_data->tanggal_surat))) ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 fw-bold">Pimpinan</p>
                                    <p class="text-muted"><?= esc($user_data->nama_pimpinan) ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1 fw-bold">Jabatan</p>
                                    <p class="text-muted"><?= esc($user_data->jabatan) ?></p>
                                </div>
                                <div class="col-md-8">
                                    <p class="mb-1 fw-bold">Email Kaprodi/Kepala Sekolah</p>
                                    <p class="text-muted"><?= esc($user_data->email_instansi) ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">Dokumen belum diupload</div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadSuratModal">
                                        Upload file
                                    </button>                                
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- KTP/KK -->
                    <div class="border rounded p-3 mb-3 shadow-sm">
                        <h6 class="fw-semibold mb-2">KTP/KK</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <?php if (!empty($user_data->ktp_kk)): ?>
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i> <?= esc($user_data->ktp_kk) ?>
                                </div>
                                <div>
                                    <a href="<?= base_url('uploads/ktp-kk/' . $user_data->ktp_kk) ?>" target="_blank" class="btn btn-primary btn-sm me-2">Lihat file</a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteKTP(<?= $user_data->id ?>)">Delete</button>                            
                                </div>
                            <?php else: ?>
                                <div class="text-muted">Dokumen belum diupload</div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('ktpFile').click();">Upload file</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="ktp_kk" id="ktpFile" style="display:none;" onchange="uploadKTPKK(this)">
                    </div>

                    <!-- BPJS Kesehatan -->
                    <div class="border rounded p-3 mb-3 shadow-sm">
                        <h6 class="fw-semibold mb-2">BPJS Kesehatan</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <?php if (!empty($user_data->bpjs_kes)): ?>
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i> <?= esc($user_data->bpjs_kes) ?>
                                </div>
                                <div>
                                    <a href="<?= base_url('uploads/bpjs-kes/' . $user_data->bpjs_kes) ?>" target="_blank" class="btn btn-primary btn-sm me-2">Lihat file</a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteBPJSKes(<?= $user_data->id ?>)">Delete</button>                            
                                </div>
                            <?php else: ?>
                                <div class="text-muted">Dokumen belum diupload</div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('bpjs_kesFile').click();">Upload file</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="bpjs_kes" id="bpjs_kesFile" style="display:none;" onchange="uploadBPJSKes(this)">
                    </div>

                    <!-- BPJS TK -->
                    <div class="border rounded p-3 mb-3 shadow-sm">
                        <h6 class="fw-semibold mb-2">BPJS Ketenagakerjaan</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <?php if (!empty($user_data->bpjs_tk)): ?>
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i> <?= esc($user_data->bpjs_tk) ?>
                                </div>
                                <div>
                                    <a href="<?= base_url('uploads/bpjs-tk/' . $user_data->bpjs_tk) ?>" target="_blank" class="btn btn-primary btn-sm me-2">Lihat file</a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteBPJSTK(<?= $user_data->id ?>)">Delete</button>                            
                                </div>
                            <?php else: ?>
                                <div class="text-muted">Dokumen belum diupload</div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('bpjs_tkFile').click();">Upload file</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="bpjs_tk" id="bpjs_tkFile" style="display:none;" onchange="uploadBPJSTK(this)">
                    </div>

                    <!-- Bukti BPJS TK -->
                    <div class="border rounded p-3 mb-3 shadow-sm">
                        <h6 class="fw-semibold mb-2">Bukti Pembayaran/Masa Berlaku BPJS Ketenagakerjaan</h6>
                        <?php if(!empty($pendaftaran['tanggal_selesai'])): ?>
                            <small class="text-danger">*pastikan masa berlaku BPJS Ketenagakerjaan aktif sampai <?= format_tanggal_indonesia($pendaftaran['tanggal_selesai']);?>.</small>
                        <?php else: ?>
                            <small class="text-danger">*pastikan masa berlaku BPJS Ketenagakerjaan aktif sampai habis masa magang.</small>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <?php if (!empty($user_data->buktibpjs_tk)): ?>
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i> <?= esc($user_data->buktibpjs_tk) ?>
                                </div>
                                <div>
                                    <a href="<?= base_url('uploads/buktibpjs-tk/' . $user_data->buktibpjs_tk) ?>" target="_blank" class="btn btn-primary btn-sm me-2">Lihat file</a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteBuktiBPJSTK(<?= $user_data->id ?>)">Delete</button>                            
                                </div>
                            <?php else: ?>
                                <div class="text-muted">Dokumen belum diupload</div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('buktibpjs_tkFile').click();">Upload file</button>
                                    <!-- Tombol lihat contoh -->
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#contohBPJSTKModal">Lihat Contoh</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="buktibpjs_tk" id="buktibpjs_tkFile" style="display:none;" onchange="uploadBuktiBPJSTK(this)">
                    </div>

                    <!-- Modal Contoh Bukti BPJS -->
                    <div class="modal fade" id="contohBPJSTKModal" tabindex="-1" aria-labelledby="contohBPJSTKModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="contohBPJSTKModalLabel">Contoh Bukti Pembayaran BPJS TK</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="<?= base_url('assets/img/contoh-bpjs-tk.jpg') ?>" class="img-fluid rounded shadow w-50" alt="Contoh Bukti BPJS TK">
                        </div>
                        </div>
                    </div>
                    </div>

                    <!-- Modal Upload Surat Permohonan -->
                    <div class="modal fade" id="uploadSuratModal" tabindex="-1" aria-labelledby="uploadSuratModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded">
                                <form id="uploadSuratForm" enctype="multipart/form-data">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title" id="uploadSuratModalLabel">Upload Surat Permohonan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="file_surat" class="form-label">File Surat<span class="text-danger">*</span><span class="text-muted">(format : pdf, maksimal 2mb)</span></label>
                                            <input type="file" class="form-control" name="file_surat" id="file_surat" accept="application/pdf" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="no_surat" class="form-label">No. Surat<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="no_surat" id="no_surat" placeholder="Masukkan Nomor Surat Permohonan" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tanggal_surat" class="form-label">Tanggal Surat<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tanggal_surat" id="tanggal_surat" placeholder="Masukkan Tanggal Surat Permohonan" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pimpinan" class="form-label">Nama Pimpinan<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="pimpinan" id="pimpinan" placeholder="Masukkan Nama Pimpinan" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jabatan" class="form-label">Jabatan Pimpinan<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="jabatan" id="jabatan" placeholder="Masukkan Jabatan Pimpinan" required>
                                        </div>
                                        <label for="email_instansi" class="form-label">
                                            Email Kaprodi/Kepala Sekolah<span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control" name="email_instansi" id="email_instansi" placeholder="Masukkan Email Kaprodi/Kepala Sekolah" required>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- End Tab Data Profil -->
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var urlParams = new URLSearchParams(window.location.search);
        var tabParam = urlParams.get('tab');

        if (tabParam) {
            var triggerEl = document.querySelector('button[data-bs-target="#' + tabParam + '"]');
            if (triggerEl) {
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }
    });
</script>
<script>

    function uploadCV(input) {
        if (input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('cv', file);

        fetch("<?= base_url('cv/uploads/') . ($user_data->id ?? 'null') ?>", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Upload gagal. Silakan coba lagi.'
            });
            console.error('Upload error:', error);
        });
    }

    function confirmDeleteCV(userId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Dokumen CV akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
            
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL delete
                window.location.href = "<?= base_url('cv/delete') ?>/" + userId;
            }
        });
    }

    function uploadProposal(input) {
        if (input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('proposal', file);

        fetch('<?= base_url('proposal/uploads/') . ($user_data->id ?? 'null') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Upload gagal. Silakan coba lagi.'
            });
            console.error('Upload error:', error);
        });
    }

    function confirmDeleteProposal(userId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Dokumen Proposal akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
            
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL delete
                window.location.href = "<?= base_url('proposal/delete') ?>/" + userId;
            }
        });
    }

    document.getElementById('uploadSuratForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch("<?= base_url('surat-permohonan/uploads/') . ($user_data->id ?? 'null') ?>", {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat upload.'
            });
        });
    });

    function confirmDeleteSurat(userId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Dokumen Surat Permohonan akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
            
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL delete
                window.location.href = "<?= base_url('surat-permohonan/delete') ?>/" + userId;
            }
        });
    }

    function uploadKTPKK(input) {
        if (input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('ktp', file);

        fetch('<?= base_url('ktp-kk/uploads/') . ($user_data->id ?? 'null') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Upload gagal. Silakan coba lagi.'
            });
            console.error('Upload error:', error);
        });
    }

    function confirmDeleteKTP(userId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Dokumen akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
            
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL delete
                window.location.href = "<?= base_url('ktp/delete') ?>/" + userId;
            }
        });
    }

    function uploadBPJSKes(input) {
        if (input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('bpjs_kes', file);

        fetch('<?= base_url('bpjs-kes/uploads/') . ($user_data->id?? 'null') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Upload gagal. Silakan coba lagi.'
            });
            console.error('Upload error:', error);
        });
    }

    function confirmDeleteBPJSKes(userId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Dokumen BPJS Kesehatan akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
            
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL delete
                window.location.href = "<?= base_url('bpjs-kes/delete') ?>/" + userId;
            }
        });
    }

    function uploadBPJSTK(input) {
        if (input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('bpjs_tk', file);

        fetch('<?= base_url('bpjs-tk/uploads/') . ($user_data->id ?? 'null') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Upload gagal. Silakan coba lagi.'
            });
            console.error('Upload error:', error);
        });
    }

    function confirmDeleteBPJSTK(userId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Dokumen BPJS Ketenagakerjaan akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
            
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL delete
                window.location.href = "<?= base_url('bpjs-tk/delete') ?>/" + userId;
            }
        });
    }

    function uploadBuktiBPJSTK(input) {
        if (input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('buktibpjs_tk', file);

        fetch('<?= base_url('buktibpjs-tk/uploads/') . ($user_data->id ?? 'null') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Upload gagal. Silakan coba lagi.'
            });
            console.error('Upload error:', error);
        });
    }

    function confirmDeleteBuktiBPJSTK(userId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Dokumen Bukti Pembayaran BPJS Ketenagakerjaan akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
            
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL delete
                window.location.href = "<?= base_url('buktibpjs-tk/delete') ?>/" + userId;
            }
        });
    }


</script>

<?= $this->endSection(); ?>