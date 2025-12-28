<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Magang PT Semen Padang</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/SP_logo.png') ?>" />

    <!-- Font & Template CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700" rel="stylesheet">
    <link href="<?= base_url('css/sb-admin-2.min.css') ?>" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />

    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- flatpicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <style>
        .aksi-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.aksi-wrapper form {
    display: inline;
    margin: 0;
}
    </style>

</head>

<body id="page-top">
    <div id="wrapper">
        <?= $this->include('admin/templates/sidebar') ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php if (session()->getFlashdata('error')) : ?>
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Akses Ditolak!',
                            text: '<?= session('error') ?>',
                        });
                    </script>
                <?php endif; ?>
                <?= $this->include('admin/templates/topbar') ?>
                <?= $this->renderSection('content') ?>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto text-center">
                    <span>Copyright &copy; <?= date('Y') ?> Semen Padang</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah jika Anda siap untuk mengakhiri sesi Anda saat ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="<?= base_url('logout'); ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Core JS (pakai CDN + fallback ke local) ===== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        if (typeof jQuery == 'undefined') {
            document.write('<script src="<?= base_url('vendor/jquery/jquery.min.js') ?>"><\/script>');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if (typeof $.fn.modal === 'undefined') {
            document.write('<script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"><\/script>');
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script>
        if (typeof $.easing == 'undefined') {
            document.write('<script src="<?= base_url('vendor/jquery-easing/jquery.easing.min.js') ?>"><\/script>');
        }
    </script>

    <!-- SB Admin 2 -->
    <script src="<?= base_url('js/sb-admin-2.min.js') ?>"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};
    </script>

    <?= $this->renderSection('scripts') ?>

    <!-- Inisialisasi Select2 & DataTable -->
<script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: function() {
                    $(this).data('placeholder');
                },
                allowClear: true
            });

            $('#dataTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    },
                    zeroRecords: "Data tidak ditemukan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)"
                }
            });
            
            //Modal Tambah Pembimbing
            $(document).on('click', '.btn-set-pembimbing', function () {

                const id = $(this).data('id');
                const unit = $(this).data('unit');
                const pembimbingId = $(this).data('pembimbing');
                const nama = $(this).data('nama');
                const mode = $(this).data('mode');

                $('#namaPeserta').text("Peserta: " + nama);
                $('#selectPembimbing').html('<option>Memuat data...</option>');

                // Set form action
                $('#formPembimbing').attr('action', "<?= base_url('admin/setPembimbing/') ?>" + id);

                // Load data pembimbing via AJAX
                $.ajax({
                    url: "<?= base_url('admin/getDataPembimbing') ?>/" + unit,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {

                        $('#selectPembimbing').empty();
                        $('#selectPembimbing').append('<option disabled selected>Pilih Pembimbing</option>');

                        if (response.length === 0) {
                            $('#selectPembimbing').append('<option disabled>Tidak ada pembimbing dalam unit ini</option>');
                            return;
                        }

                        $.each(response, function(index, p) {
                            $('#selectPembimbing').append(
                                `<option value="${p.id}">${p.fullname} (${p.email})</option>`
                            );
                        });

                        // Jika mode edit → set selected value
                        if (mode === "edit" && pembimbingId) {
                            $('#selectPembimbing').val(pembimbingId).trigger('change');
                        }
                    },
                    error: function() {
                        alert("Gagal memuat data pembimbing.");
                    }
                });

                // Judul modal
                if (mode === "tambah") {
                    $('#modalPembimbingTitle').text('Tambah Pembimbing');
                    $('#btnSubmitPembimbing').text('Simpan');
                } else {
                    $('#modalPembimbingTitle').text('Edit Pembimbing');
                    $('#btnSubmitPembimbing').text('Update');
                }

                $('#pembimbingModal').modal('show');
            });
            //Modal tambah pembimbing penelitian
            $(document).on('click', '.btn-set-pembimbing-penelitian', function () {

                const id = $(this).data('id');
                const unit = $(this).data('unit');
                const pembimbingId = $(this).data('pembimbing');
                const nama = $(this).data('nama');
                const mode = $(this).data('mode');

                $('#namaPeserta').text("Peserta: " + nama);
                $('#selectPembimbing').html('<option>Memuat data...</option>');

                // Set form action
                $('#formPembimbing').attr('action', "<?= base_url('admin/setPembimbingPenelitian/') ?>" + id);

                // Load data pembimbing via AJAX
                $.ajax({
                    url: "<?= base_url('admin/getDataPembimbingPenelitian') ?>/" + unit,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {

                        $('#selectPembimbing').empty();
                        $('#selectPembimbing').append('<option disabled selected>Pilih Pembimbing</option>');

                        if (response.length === 0) {
                            $('#selectPembimbing').append('<option disabled>Tidak ada pembimbing dalam unit ini</option>');
                            return;
                        }

                        $.each(response, function(index, p) {
                            $('#selectPembimbing').append(
                                `<option value="${p.id}">${p.fullname} (${p.email})</option>`
                            );
                        });

                        // Jika mode edit → set selected value
                        if (mode === "edit" && pembimbingId) {
                            $('#selectPembimbing').val(pembimbingId).trigger('change');
                        }
                    },
                    error: function() {
                        alert("Gagal memuat data pembimbing.");
                    }
                });

                // Judul modal
                if (mode === "tambah") {
                    $('#modalPembimbingTitle').text('Tambah Pembimbing');
                    $('#btnSubmitPembimbing').text('Simpan');
                } else {
                    $('#modalPembimbingTitle').text('Edit Pembimbing');
                    $('#btnSubmitPembimbing').text('Update');
                }

                $('#pembimbingModal').modal('show');
            });

            // Modal tolak laporan
            $(document).on('click', '.btn-tolak-laporan', function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                $('#namaPesertaLaporan').text('Peserta: ' + nama);
                $('#formTolakLaporan').attr('action', "<?= base_url('admin/manage-magang/tolakLaporan/') ?>" + id);

                $('#tolakLaporanModal').modal('show');
            });

            // Modal tolak absensi
            $(document).on('click', '.btn-tolak-absensi', function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                $('#namaPesertaAbsensi').text('Peserta: ' + nama);
                $('#formTolakAbsensi').attr('action', "<?= base_url('admin/manage-magang/tolakAbsensi/') ?>" + id);

                $('#tolakAbsensiModal').modal('show');
            });

            // Modal detail nilai
            $(document).on('click', '.btn-detail-nilai', function() {
                const id = $(this).data('id');
                $('#modalDetailNilai').modal('show');
                $('#detailContent').html('<p class="text-center text-muted">Sedang memuat...</p>');

                $.ajax({
                    url: "<?= site_url('admin/manage-magang/detailNilai/') ?>" + id,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            const d = response.data;
                            let html = `
                                <p><strong>Nama:</strong> ${d.fullname}</p>
                                <p><strong>NIM/NISN:</strong> ${d.nisn_nim}</p>
                                <p><strong>Unit Kerja:</strong> ${d.unit_kerja}</p>
                                <hr>
                            `;

                            const fields = {
                                disiplin: "Disiplin",
                                kerajinan: "Kerajinan",
                                tingkahlaku: "Tingkah Laku",
                                kerjasama: "Kerja Sama",
                                kreativitas: "Kreativitas",
                                kemampuankerja: "Kemampuan Kerja",
                                tanggungjawab: "Tanggung Jawab",
                                penyerapan: "Penyerapan"
                            };

                            let totalNilai = 0;
                            let jumlahField = 0;

                            html += `<div class="row">`;

                            for (const key in fields) {
                                const nilai = parseInt(d[`nilai_${key}`]) || 0;
                                if (nilai > 0) jumlahField++;
                                totalNilai += nilai;

                                const stars = (nilai - 50) / 10; // 60=1, 70=2, ... 100=5
                                html += `
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">${fields[key]}</label><br>
                                        ${[1,2,3,4,5].map(i =>
                                            i <= stars
                                                ? '<i class="fas fa-star text-warning"></i>'
                                                : '<i class="far fa-star text-muted"></i>'
                                        ).join('')}
                                        <span class="ml-2">(${nilai})</span>
                                    </div>
                                `;
                            }

                            html += `</div>`;

                            const rataRata = jumlahField > 0 ? totalNilai / jumlahField : 0;
                            let kategori = '-';
                            if (rataRata >= 60 && rataRata <= 69) kategori = "Kurang";
                            else if (rataRata >= 70 && rataRata <= 79) kategori = "Cukup";
                            else if (rataRata >= 80 && rataRata <= 89) kategori = "Baik";
                            else if (rataRata >= 90 && rataRata <= 100) kategori = "Baik Sekali";

                            html += `
                                <div class="alert alert-info mt-3">
                                    <strong>Rata-rata Nilai:</strong> ${rataRata.toFixed(2)}<br>
                                    <strong>Kategori:</strong> ${kategori}<br>
                                    <strong>Tanggal Penilaian:</strong> ${d.tgl_penilaian_format ?? '-'}<br>
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">Catatan atau Komentar</label>
                                    <textarea class="form-control" rows="5" readonly>${d.catatan ?? ''}</textarea>
                                </div>
                            `;

                            $('#detailContent').html(html);
                        } else {
                            $('#detailContent').html('<p class="text-danger">Data tidak ditemukan.</p>');
                        }
                    },
                    error: function() {
                        $('#detailContent').html('<p class="text-danger">Terjadi kesalahan saat mengambil data.</p>');
                    }
                });
            });

            // Modal detail 
            $(document).on('click', '.btn-detail-peserta', function() {
                const id = $(this).data('id');
                $('#modalDetailPeserta').modal('show');
                $('#detailPesertaContent').html('<p class="text-center text-muted">Memuat data peserta...</p>');
                $('#btnEditMagang, #btnBatalkanMagang').addClass('d-none');

                $.ajax({
                    url: "<?= site_url('admin/manage-magang/detailPeserta/') ?>" + id,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            const d = response.data;
                            $('#modalDetailPesertaLabel').text(`Detail Peserta - ${d.fullname}`);
                            
                            let img = d.user_image 
                                ? `<img src="<?= base_url('uploads/user-image/') ?>${d.user_image}" alt="Foto ${d.fullname}" class="img-thumbnail" style="max-width:150px;">`
                                : `<span class="text-muted">Tidak ada foto</span>`;

                            // Alamat dan Domisili
                            const alamat = [d.alamat, `${d.tipe_kota_ktp ?? ''} ${d.kota_ktp ?? ''}`.trim(), d.provinsi_ktp].filter(Boolean).join(', ') || 'Data belum diisi';
                            const domisili = [d.domisili, `${d.tipe_kota_domisili ?? ''} ${d.kota_domisili ?? ''}`.trim(), d.provinsi_domisili].filter(Boolean).join(', ') || 'Data belum diisi';

                            // Jenis Kelamin
                            const jk = d.jenis_kelamin === 'L' ? 'Laki-Laki' : (d.jenis_kelamin === 'P' ? 'Perempuan' : '-');

                            let html = `
                                <h6 class="text-primary">📌 Informasi Mahasiswa</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th>Nama Lengkap</th><td>${d.fullname}</td>
                                        <td rowspan="4" class="text-center align-middle" style="width:180px;">${img}</td>
                                    </tr>
                                    <tr><th>NIM/NISN</th><td>${d.nisn_nim}</td></tr>
                                    <tr><th>No HP</th><td>${d.no_hp}</td></tr>
                                    <tr><th>Email</th><td>${d.email}</td></tr>
                                    <tr><th>Jenis Kelamin</th><td>${jk}</td></tr>
                                    <tr><th>Alamat</th><td colspan="2">${alamat}</td></tr>
                                    <tr><th>Domisili</th><td colspan="2">${domisili}</td></tr>
                                </table>

                                <h6 class="text-primary mt-4">🎓 Pendidikan</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><th>Tingkat</th><td>${d.tingkat_pendidikan ?? '-'}</td></tr>
                                    <tr><th>${d.tingkat_pendidikan === 'SMK' ? 'Sekolah' : 'Perguruan Tinggi'}</th><td>${d.nama_instansi ?? '-'}</td></tr>
                                    <tr><th>Jurusan</th><td>${d.nama_jurusan ?? '-'}</td></tr>
                                    ${d.tingkat_pendidikan === 'SMK'
                                        ? `<tr><th>Kelas</th><td>Kelas ${d.semester ?? '-'}</td></tr>`
                                        : `<tr><th>Semester</th><td>Semester ${d.semester ?? '-'}</td></tr>
                                        <tr><th>IPK</th><td>${d.nilai_ipk ?? '-'}</td></tr>`}
                                </table>

                                <h6 class="text-primary mt-4">🗂️ Dokumen</h6>
                                <table class="table table-sm table-bordered">
                                    ${d.tingkat_pendidikan === 'SMK' ? `
                                        <tr><th>Surat Permohonan</th><td>${linkFile('surat-permohonan', d.surat_permohonan)}</td></tr>
                                        <tr><th>Tanggal Surat</th><td>${formatTanggal(d.tanggal_surat)}</td></tr>
                                        <tr><th>Nomor Surat</th><td>${d.no_surat ?? '-'}</td></tr>
                                        <tr><th>Nama Pimpinan</th><td>${d.nama_pimpinan ?? '-'}</td></tr>
                                        <tr><th>Jabatan Pimpinan</th><td>${d.jabatan ?? '-'}</td></tr>
                                        <tr><th>Email Instansi</th><td>${d.email_instansi ?? '-'}</td></tr>
                                        <tr><th>KTP/KK</th><td>${linkFile('ktp-kk', d.ktp_kk)}</td></tr>
                                        <tr><th>BPJS TK</th><td>${linkFile('bpjs-tk', d.bpjs_tk)}</td></tr>
                                        <tr><th>Bukti BPJS TK</th><td>${linkFile('buktibpjs-tk', d.buktibpjs_tk)}</td></tr>
                                    ` : `
                                        <tr><th>CV</th><td>${linkFile('cv', d.cv)}</td></tr>
                                        <tr><th>Proposal</th><td>${linkFile('proposal', d.proposal)}</td></tr>
                                        <tr><th>Surat Permohonan</th><td>${linkFile('surat-permohonan', d.surat_permohonan)}</td></tr>
                                        <tr><th>Tanggal Surat</th><td>${formatTanggal(d.tanggal_surat)}</td></tr>
                                        <tr><th>Nomor Surat</th><td>${d.no_surat ?? '-'}</td></tr>
                                        <tr><th>Nama Pimpinan</th><td>${d.nama_pimpinan ?? '-'}</td></tr>
                                        <tr><th>Jabatan Pimpinan</th><td>${d.jabatan ?? '-'}</td></tr>
                                        <tr><th>Email Instansi</th><td>${d.email_instansi ?? '-'}</td></tr>
                                        <tr><th>KTP/KK</th><td>${linkFile('ktp-kk', d.ktp_kk)}</td></tr>
                                        <tr><th>BPJS TK</th><td>${linkFile('bpjs-tk', d.bpjs_tk)}</td></tr>
                                        <tr><th>Bukti BPJS TK</th><td>${linkFile('buktibpjs-tk', d.buktibpjs_tk)}</td></tr>
                                    `}
                                </table>

                                <h6 class="text-primary mt-4">📆 Status Magang</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><th>Unit Kerja</th><td>${d.unit_kerja}</td></tr>
                                    <tr><th>Tanggal Masuk</th><td>${formatTanggal(d.tanggal_masuk)}</td></tr>
                                    <tr><th>Tanggal Selesai</th><td>${formatTanggal(d.tanggal_selesai)}</td></tr>
                                    <tr><th>Durasi</th><td>${d.durasi ?? '-'} bulan</td></tr>
                                    <tr><th>Status Seleksi</th><td>${d.status_seleksi ?? 'Belum Diseleksi'}</td></tr>
                                    <tr><th>Tanggal Seleksi</th><td>${formatTanggal(d.tanggal_seleksi)}</td></tr>
                                    <tr><th>Status Konfirmasi</th><td>${statusKonfirmasi(d.status_konfirmasi)}</td></tr>
                                    <tr><th>Tanggal Konfirmasi</th><td>${formatTanggal(d.tanggal_konfirmasi)}</td></tr>
                                    <tr><th>Status Approval Konfirmasi</th><td>${statusValidasi(d.status_validasi_berkas)}</td></tr>
                                    <tr><th>Tanggal Approval Konfirmasi</th><td>${formatTanggal(d.tanggal_validasi_berkas)}</td></tr>
                                    <tr><th>Status Berkas Lengkap</th><td>${statusBerkas(d.status_berkas_lengkap)}</td></tr>
                                    <tr><th>Tanggal Berkas Lengkap</th><td>${formatTanggal(d.tanggal_berkas_lengkap)}</td></tr>
                                    <tr><th>Catatan Validasi Berkas</th><td>${d.cttn_berkas_lengkap ?? '-'}</td></tr>
                                    <tr><th>Tanggal Setujui Pernyataan</th><td>${formatTanggal(d.tanggal_setujui_pernyataan)}</td></tr>
                                    <tr><th>Nama Pembimbing</th><td>${d.nama_pembimbing ?? '-'}</td></tr>
                                    <tr><th>Lembar Absensi</th><td>${linkFile('absensi', d.absensi)}
                                                                ${d.url_absensi 
                                        ? `<br><a href="${d.url_absensi.startsWith('http') ? d.url_absensi : 'https://' + d.url_absensi}" 
                                            target="_blank">Lihat Absensi</a>` 
                                        : ''
                                    }</td></tr>
                                    <tr><th>Catatan Absensi</th><td>${d.cttn_absensi ?? '-'}</td></tr>
                                    <tr><th>Tanggal Upload Absensi</th><td>${formatTanggal(d.tgl_upload_absensi)}</td></tr>
                                    <tr><th>Laporan Akhir</th><td>${linkFile('laporan', d.laporan)} ${d.url_laporan 
                                        ? `<br><a href="${d.url_laporan.startsWith('http') ? d.url_laporan : 'https://' + d.url_laporan}" 
                                            target="_blank">Lihat Laporan</a>` 
                                        : ''
                                    }</td></tr>
                                    <tr><th>Catatan Laporan</th><td>${d.cttn_laporan ?? '-'}</td></tr>
                                    <tr><th>Tanggal Upload Laporan</th><td>${formatTanggal(d.tgl_upload_laporan)}</td></tr>
                                    <tr><th>Status Akhir</th><td><span class="badge badge-info">${d.status_akhir ?? '-'}</span></td></tr>

                                </table>

                            `;

                            $('#detailPesertaContent').html(html);

                            // Tampilkan tombol edit & batalkan
                            $('#btnEditMagang')
                                .removeClass('d-none')
                                .off('click')
                                .on('click', function() {
                                    $.ajax({
                                        url: "<?= site_url('admin/manage-magang/getEditData/') ?>/" + id,
                                        type: "GET",
                                        dataType: "json",
                                        beforeSend: function() {
                                            $('#editMagangContent').html('<p class="text-center text-muted">Memuat data...</p>');
                                        },
                                        success: function(response) {
                                            if (response.status === 'success') {
                                                const d = response.data;
                                                const unitList = response.unitList;

                                                let options = unitList.map(unit =>
                                                    `<option value="${unit.unit_id}" ${unit.unit_id == d.unit_id ? 'selected' : ''}>
                                                        ${unit.unit_kerja}
                                                    </option>`
                                                ).join('');

                                                let formHtml = `
                                                    <input type="hidden" name="magang_id" value="${d.magang_id}">
                                                    <div class="form-group">
                                                        <label>Tanggal Masuk</label>
                                                        <input type="date" name="tanggal_masuk" class="form-control" value="${d.tanggal_masuk ?? ''}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Tanggal Selesai</label>
                                                        <input type="date" name="tanggal_selesai" class="form-control" value="${d.tanggal_selesai ?? ''}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Unit Kerja</label>
                                                        <select name="unit_id" class="form-control select2">${options}</select>
                                                    </div>
                                                `;

                                                $('#editMagangContent').html(formHtml);
                                                $('#editMagangContent .select2').select2({
                                                    dropdownParent: $('#modalEditMagang'),
                                                    theme: 'bootstrap4',
                                                    width: '100%',
                                                    placeholder: function() {
                                                        $(this).data('placeholder');
                                                    },
                                                    allowClear: true
                                                });
                                                $('#modalEditMagang').modal('show'); 
                                            } else {
                                                $('#editMagangContent').html('<p class="text-danger">Data tidak ditemukan.</p>');
                                            }
                                        },
                                        error: function() {
                                            $('#editMagangContent').html('<p class="text-danger">Gagal memuat data.</p>');
                                        }
                                    });
                            });
                            $('#btnBatalkanMagang')
                                .removeClass('d-none')
                                .off('click')
                                .on('click', function() { batalkanMagang(id, 'modalDetailPeserta'); });
                        } else {
                            $('#detailPesertaContent').html('<p class="text-danger">Data tidak ditemukan.</p>');
                        }
                    },
                    error: function() {
                        $('#detailPesertaContent').html('<p class="text-danger">Terjadi kesalahan saat mengambil data.</p>');
                    }
                });
            });

            // Modal detail Penelitian
            $(document).on('click', '.btn-detail-penelitian', function() {
                const id = $(this).data('id');
                $('#modalDetailPeserta').modal('show');
                $('#detailPesertaContent').html('<p class="text-center text-muted">Memuat data peserta...</p>');
                $('#btnEditPenelitian, #btnBatalkanPenelitian').addClass('d-none');

                $.ajax({
                    url: "<?= site_url('admin/penelitian/detailPeserta/') ?>" + id,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            const d = response.data;
                            $('#modalDetailPesertaLabel').text(`Detail Peserta - ${d.fullname}`);
                            
                            let img = d.user_image 
                                ? `<img src="<?= base_url('uploads/user-image/') ?>${d.user_image}" alt="Foto ${d.fullname}" class="img-thumbnail" style="max-width:150px;">`
                                : `<span class="text-muted">Tidak ada foto</span>`;

                            // Alamat dan Domisili
                            const alamat = [d.alamat, `${d.tipe_kota_ktp ?? ''} ${d.kota_ktp ?? ''}`.trim(), d.provinsi_ktp].filter(Boolean).join(', ') || 'Data belum diisi';
                            const domisili = [d.domisili, `${d.tipe_kota_domisili ?? ''} ${d.kota_domisili ?? ''}`.trim(), d.provinsi_domisili].filter(Boolean).join(', ') || 'Data belum diisi';

                            // Jenis Kelamin
                            const jk = d.jenis_kelamin === 'L' ? 'Laki-Laki' : (d.jenis_kelamin === 'P' ? 'Perempuan' : '-');

                            let html = `
                                <h6 class="text-primary">📌 Informasi Mahasiswa</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th>Nama Lengkap</th><td>${d.fullname}</td>
                                        <td rowspan="4" class="text-center align-middle" style="width:180px;">${img}</td>
                                    </tr>
                                    <tr><th>NIM/NISN</th><td>${d.nisn_nim}</td></tr>
                                    <tr><th>No HP</th><td>${d.no_hp}</td></tr>
                                    <tr><th>Email</th><td>${d.email}</td></tr>
                                    <tr><th>Jenis Kelamin</th><td>${jk}</td></tr>
                                    <tr><th>Alamat</th><td colspan="2">${alamat}</td></tr>
                                    <tr><th>Domisili</th><td colspan="2">${domisili}</td></tr>
                                </table>

                                <h6 class="text-primary mt-4">🎓 Pendidikan</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><th>Tingkat</th><td>${d.tingkat_pendidikan ?? '-'}</td></tr>
                                    <tr><th>${d.tingkat_pendidikan === 'SMK' ? 'Sekolah' : 'Perguruan Tinggi'}</th><td>${d.nama_instansi ?? '-'}</td></tr>
                                    <tr><th>Jurusan</th><td>${d.nama_jurusan ?? '-'}</td></tr>
                                    ${d.tingkat_pendidikan === 'SMK'
                                        ? `<tr><th>Kelas</th><td>Kelas ${d.semester ?? '-'}</td></tr>`
                                        : `<tr><th>Semester</th><td>Semester ${d.semester ?? '-'}</td></tr>
                                        <tr><th>IPK</th><td>${d.nilai_ipk ?? '-'}</td></tr>`}
                                </table>

                                <h6 class="text-primary mt-4">🗂️ Dokumen</h6>
                                <table class="table table-sm table-bordered">
                                    ${d.tingkat_pendidikan === 'SMK' ? `
                                        <tr><th>Surat Permohonan</th><td>${linkFile('surat-permohonan', d.surat_permohonan)}</td></tr>
                                        <tr><th>Tanggal Surat</th><td>${formatTanggal(d.tanggal_surat)}</td></tr>
                                        <tr><th>Nomor Surat</th><td>${d.no_surat ?? '-'}</td></tr>
                                        <tr><th>Nama Pimpinan</th><td>${d.nama_pimpinan ?? '-'}</td></tr>
                                        <tr><th>Jabatan Pimpinan</th><td>${d.jabatan ?? '-'}</td></tr>
                                        <tr><th>Email Instansi</th><td>${d.email_instansi ?? '-'}</td></tr>
                                        <tr><th>KTP/KK</th><td>${linkFile('ktp-kk', d.ktp_kk)}</td></tr>
                                        <tr><th>BPJS TK</th><td>${linkFile('bpjs-tk', d.bpjs_tk)}</td></tr>
                                        <tr><th>Bukti BPJS TK</th><td>${linkFile('buktibpjs-tk', d.buktibpjs_tk)}</td></tr>
                                    ` : `
                                        <tr><th>CV</th><td>${linkFile('cv', d.cv)}</td></tr>
                                        <tr><th>Proposal</th><td>${linkFile('proposal', d.proposal)}</td></tr>
                                        <tr><th>Surat Permohonan</th><td>${linkFile('surat-permohonan', d.surat_permohonan)}</td></tr>
                                        <tr><th>Tanggal Surat</th><td>${formatTanggal(d.tanggal_surat)}</td></tr>
                                        <tr><th>Nomor Surat</th><td>${d.no_surat ?? '-'}</td></tr>
                                        <tr><th>Nama Pimpinan</th><td>${d.nama_pimpinan ?? '-'}</td></tr>
                                        <tr><th>Jabatan Pimpinan</th><td>${d.jabatan ?? '-'}</td></tr>
                                        <tr><th>Email Instansi</th><td>${d.email_instansi ?? '-'}</td></tr>
                                        <tr><th>KTP/KK</th><td>${linkFile('ktp-kk', d.ktp_kk)}</td></tr>
                                        <tr><th>BPJS TK</th><td>${linkFile('bpjs-tk', d.bpjs_tk)}</td></tr>
                                        <tr><th>Bukti BPJS TK</th><td>${linkFile('buktibpjs-tk', d.buktibpjs_tk)}</td></tr>
                                    `}
                                </table>

                                <h6 class="text-primary mt-4">📆 Status Penelitian</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><th>Unit Kerja</th><td>${d.unit_kerja ?? 'Belum Ada Unit'}</td></tr>
                                    <tr><th>Tanggal Masuk</th><td>${formatTanggal(d.tanggal_masuk)}</td></tr>
                                    <tr><th>Tanggal Selesai</th><td>${formatTanggal(d.tanggal_selesai)}</td></tr>
                                    <tr><th>Durasi</th><td>${d.durasi ?? '-'} bulan</td></tr>
                                    <tr><th>Status Seleksi</th><td>${d.status_seleksi ?? 'Belum Diterima'}</td></tr>
                                    <tr><th>Tanggal Seleksi</th><td>${formatTanggal(d.tanggal_seleksi)}</td></tr>
                                    <tr><th>Status Approve Unit</th><td>${statusApproveUnit(d.approve_unit)}</td></tr>
                                    <tr><th>Tanggal Approve Unit</th><td>${formatTanggal(d.tanggal_approve_unit)}</td></tr>
                                    <tr><th>Catatan Approve Unit</th><td>${d.cttn_approve_unit ?? '-'}</td></tr>
                                    <tr><th>Status Konfirmasi</th><td>${statusKonfirmasi(d.status_konfirmasi)}</td></tr>
                                    <tr><th>Tanggal Konfirmasi</th><td>${formatTanggal(d.tanggal_konfirmasi)}</td></tr>
                                    <tr><th>Status Approval Konfirmasi</th><td>${statusValidasi(d.status_validasi_berkas)}</td></tr>
                                    <tr><th>Tanggal Approval Konfirmasi</th><td>${formatTanggal(d.tanggal_validasi_berkas)}</td></tr>
                                    <tr><th>Status Berkas Lengkap</th><td>${statusBerkas(d.status_berkas_lengkap)}</td></tr>
                                    <tr><th>Tanggal Berkas Lengkap</th><td>${formatTanggal(d.tanggal_berkas_lengkap)}</td></tr>
                                    <tr><th>Catatan Validasi Berkas</th><td>${d.cttn_berkas_lengkap ?? '-'}</td></tr>
                                    <tr><th>Tanggal Setujui Pernyataan</th><td>${formatTanggal(d.tanggal_setujui_pernyataan)}</td></tr>
                                    <tr><th>Nama Pembimbing</th><td>${d.nama_pembimbing ?? '-'}</td></tr>
                                    <tr><th>Lembar Absensi</th><td>${linkFile('absensi', d.absensi)}</td></tr>
                                    <tr><th>Catatan Absensi</th><td>${d.catatan_absensi ?? '-'}</td></tr>
                                    <tr><th>Tanggal Upload Absensi</th><td>${formatTanggal(d.tgl_upload_absensi)}</td></tr>
                                    <tr><th>Formulir Penelitian</th><td>${linkFile('formpenelitian', d.formulir_penelitian)}</td></tr>
                                    <tr><th>Catatan Pembimbing</th><td>${d.catatan_formulir ?? '-'}</td></tr>
                                    <tr><th>Tanggal Konfirmasi</th><td>${formatTanggal(d.tgl_upload_formulir)}</td></tr>
                                    <tr><th>Status Akhir</th><td><span class="badge badge-info">${d.status_akhir ?? '-'}</span></td></tr>

                                </table>

                            `;

                            $('#detailPesertaContent').html(html);

                            // Tampilkan tombol edit & batalkan
                            $('#btnEditPenelitian')
                                .removeClass('d-none')
                                .off('click')
                                .on('click', function() {
                                    $.ajax({
                                        url: "<?= site_url('admin/penelitian/getEditData/') ?>/" + id,
                                        type: "GET",
                                        dataType: "json",
                                        beforeSend: function() {
                                            $('#editPenelitianContent').html('<p class="text-center text-muted">Memuat data...</p>');
                                        },
                                        success: function(response) {
                                            if (response.status === 'success') {
                                                const d = response.data;
                                                const unitList = response.unitList;

                                                let options = unitList.map(unit =>
                                                    `<option value="${unit.unit_id}" ${unit.unit_id == d.unit_id ? 'selected' : ''}>
                                                        ${unit.unit_kerja}
                                                    </option>`
                                                ).join('');

                                                let formHtml = `
                                                    <input type="hidden" name="penelitian_id" value="${d.penelitian_id}">
                                                    <div class="form-group">
                                                        <label>Tanggal Masuk</label>
                                                        <input type="date" name="tanggal_masuk" class="form-control" value="${d.tanggal_masuk ?? ''}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Tanggal Selesai</label>
                                                        <input type="date" name="tanggal_selesai" class="form-control" value="${d.tanggal_selesai ?? ''}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Unit Kerja</label>
                                                        <select name="unit_id" class="form-control select2">${options}</select>
                                                    </div>
                                                `;

                                                $('#editPenelitianContent').html(formHtml);
                                                $('#editPenelitianContent .select2').select2({
                                                    dropdownParent: $('#modalEditPenelitian'),
                                                    theme: 'bootstrap4',
                                                    width: '100%',
                                                    placeholder: function() {
                                                        $(this).data('placeholder');
                                                    },
                                                    allowClear: true
                                                });
                                                $('#modalEditPenelitian').modal('show'); 
                                            } else {
                                                $('#editPenelitianContent').html('<p class="text-danger">Data tidak ditemukan.</p>');
                                            }
                                        },
                                        error: function() {
                                            $('#editPenelitianContent').html('<p class="text-danger">Gagal memuat data.</p>');
                                        }
                                    });
                            });
                            $('#btnBatalkanPenelitian')
                                .removeClass('d-none')
                                .off('click')
                                .on('click', function() { batalkanPenelitian(id, 'modalDetailPeserta'); });
                        } else {
                            $('#detailPesertaContent').html('<p class="text-danger">Data tidak ditemukan.</p>');
                        }
                    },
                    error: function() {
                        $('#detailPesertaContent').html('<p class="text-danger">Terjadi kesalahan saat mengambil data.</p>');
                    }
                });
            });

            $(document).on('click', '.btn-detail-penelitian-pembimbing', function() {
                const id = $(this).data('id');
                $('#modalDetailPeserta').modal('show');
                $('#detailPesertaContent').html('<p class="text-center text-muted">Memuat data peserta...</p>');
                
                $.ajax({
                    url: "<?= site_url('pembimbing/penelitian/detailPeserta/') ?>" + id,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            const d = response.data;
                            $('#modalDetailPesertaLabel').text(`Detail Peserta - ${d.fullname}`);
                            
                            let img = d.user_image 
                                ? `<img src="<?= base_url('uploads/user-image/') ?>${d.user_image}" alt="Foto ${d.fullname}" class="img-thumbnail" style="max-width:150px;">`
                                : `<span class="text-muted">Tidak ada foto</span>`;

                            // Alamat dan Domisili
                            const alamat = [d.alamat, `${d.tipe_kota_ktp ?? ''} ${d.kota_ktp ?? ''}`.trim(), d.provinsi_ktp].filter(Boolean).join(', ') || 'Data belum diisi';
                            const domisili = [d.domisili, `${d.tipe_kota_domisili ?? ''} ${d.kota_domisili ?? ''}`.trim(), d.provinsi_domisili].filter(Boolean).join(', ') || 'Data belum diisi';

                            // Jenis Kelamin
                            const jk = d.jenis_kelamin === 'L' ? 'Laki-Laki' : (d.jenis_kelamin === 'P' ? 'Perempuan' : '-');

                            let html = `
                                <h6 class="text-primary">📌 Informasi Mahasiswa</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th>Nama Lengkap</th><td>${d.fullname}</td>
                                        <td rowspan="4" class="text-center align-middle" style="width:180px;">${img}</td>
                                    </tr>
                                    <tr><th>NIM/NISN</th><td>${d.nisn_nim}</td></tr>
                                    <tr><th>No HP</th><td>${d.no_hp}</td></tr>
                                    <tr><th>Email</th><td>${d.email}</td></tr>
                                    <tr><th>Jenis Kelamin</th><td>${jk}</td></tr>
                                    <tr><th>Alamat</th><td colspan="2">${alamat}</td></tr>
                                    <tr><th>Domisili</th><td colspan="2">${domisili}</td></tr>
                                </table>

                                <h6 class="text-primary mt-4">🎓 Pendidikan</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><th>Tingkat</th><td>${d.tingkat_pendidikan ?? '-'}</td></tr>
                                    <tr><th>${d.tingkat_pendidikan === 'SMK' ? 'Sekolah' : 'Perguruan Tinggi'}</th><td>${d.nama_instansi ?? '-'}</td></tr>
                                    <tr><th>Jurusan</th><td>${d.nama_jurusan ?? '-'}</td></tr>
                                    ${d.tingkat_pendidikan === 'SMK'
                                        ? `<tr><th>Kelas</th><td>Kelas ${d.semester ?? '-'}</td></tr>`
                                        : `<tr><th>Semester</th><td>Semester ${d.semester ?? '-'}</td></tr>
                                        <tr><th>IPK</th><td>${d.nilai_ipk ?? '-'}</td></tr>`}
                                </table>

                                <h6 class="text-primary mt-4">🗂️ Dokumen</h6>
                                <table class="table table-sm table-bordered">
                                    ${d.tingkat_pendidikan === 'SMK' ? `
                                        <tr><th>Surat Permohonan</th><td>${linkFile('surat-permohonan', d.surat_permohonan)}</td></tr>
                                        <tr><th>Tanggal Surat</th><td>${formatTanggal(d.tanggal_surat)}</td></tr>
                                        <tr><th>Nomor Surat</th><td>${d.no_surat ?? '-'}</td></tr>
                                        <tr><th>Nama Pimpinan</th><td>${d.nama_pimpinan ?? '-'}</td></tr>
                                        <tr><th>Jabatan Pimpinan</th><td>${d.jabatan ?? '-'}</td></tr>
                                        <tr><th>Email Instansi</th><td>${d.email_instansi ?? '-'}</td></tr>
                                        <tr><th>KTP/KK</th><td>${linkFile('ktp-kk', d.ktp_kk)}</td></tr>
                                        <tr><th>BPJS TK</th><td>${linkFile('bpjs-tk', d.bpjs_tk)}</td></tr>
                                        <tr><th>Bukti BPJS TK</th><td>${linkFile('buktibpjs-tk', d.buktibpjs_tk)}</td></tr>
                                    ` : `
                                        <tr><th>CV</th><td>${linkFile('cv', d.cv)}</td></tr>
                                        <tr><th>Proposal</th><td>${linkFile('proposal', d.proposal)}</td></tr>
                                        <tr><th>Surat Permohonan</th><td>${linkFile('surat-permohonan', d.surat_permohonan)}</td></tr>
                                        <tr><th>Tanggal Surat</th><td>${formatTanggal(d.tanggal_surat)}</td></tr>
                                        <tr><th>Nomor Surat</th><td>${d.no_surat ?? '-'}</td></tr>
                                        <tr><th>Nama Pimpinan</th><td>${d.nama_pimpinan ?? '-'}</td></tr>
                                        <tr><th>Jabatan Pimpinan</th><td>${d.jabatan ?? '-'}</td></tr>
                                        <tr><th>Email Instansi</th><td>${d.email_instansi ?? '-'}</td></tr>
                                        <tr><th>KTP/KK</th><td>${linkFile('ktp-kk', d.ktp_kk)}</td></tr>
                                        <tr><th>BPJS TK</th><td>${linkFile('bpjs-tk', d.bpjs_tk)}</td></tr>
                                        <tr><th>Bukti BPJS TK</th><td>${linkFile('buktibpjs-tk', d.buktibpjs_tk)}</td></tr>
                                    `}
                                </table>

                                <h6 class="text-primary mt-4">📆 Status Penelitian</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><th>Unit Kerja</th><td>${d.unit_kerja ?? 'Belum Ada Unit'}</td></tr>
                                    <tr><th>Tanggal Masuk</th><td>${formatTanggal(d.tanggal_masuk)}</td></tr>
                                    <tr><th>Tanggal Selesai</th><td>${formatTanggal(d.tanggal_selesai)}</td></tr>
                                    <tr><th>Durasi</th><td>${d.durasi ?? '-'} bulan</td></tr>
                                    <tr><th>Status Seleksi</th><td>${d.status_seleksi ?? 'Belum Diterima'}</td></tr>
                                    <tr><th>Tanggal Seleksi</th><td>${formatTanggal(d.tanggal_seleksi)}</td></tr>
                                    <tr><th>Status Approve Unit</th><td>${statusApproveUnit(d.approve_unit)}</td></tr>
                                    <tr><th>Tanggal Approve Unit</th><td>${formatTanggal(d.tanggal_approve_unit)}</td></tr>
                                    <tr><th>Catatan Approve Unit</th><td>${d.cttn_approve_unit ?? '-'}</td></tr>
                                    <tr><th>Status Konfirmasi</th><td>${statusKonfirmasi(d.status_konfirmasi)}</td></tr>
                                    <tr><th>Tanggal Konfirmasi</th><td>${formatTanggal(d.tanggal_konfirmasi)}</td></tr>
                                    <tr><th>Status Approval Konfirmasi</th><td>${statusValidasi(d.status_validasi_berkas)}</td></tr>
                                    <tr><th>Tanggal Approval Konfirmasi</th><td>${formatTanggal(d.tanggal_validasi_berkas)}</td></tr>
                                    <tr><th>Status Berkas Lengkap</th><td>${statusBerkas(d.status_berkas_lengkap)}</td></tr>
                                    <tr><th>Tanggal Berkas Lengkap</th><td>${formatTanggal(d.tanggal_berkas_lengkap)}</td></tr>
                                    <tr><th>Catatan Validasi Berkas</th><td>${d.cttn_berkas_lengkap ?? '-'}</td></tr>
                                    <tr><th>Tanggal Setujui Pernyataan</th><td>${formatTanggal(d.tanggal_setujui_pernyataan)}</td></tr>
                                    <tr><th>Nama Pembimbing</th><td>${d.nama_pembimbing ?? '-'}</td></tr>
                                    <tr><th>Lembar Absensi</th><td>${linkFile('absensi', d.absensi)}</td></tr>
                                    <tr><th>Catatan Absensi</th><td>${d.catatan_absensi ?? '-'}</td></tr>
                                    <tr><th>Tanggal Upload Absensi</th><td>${formatTanggal(d.tgl_upload_absensi)}</td></tr>
                                    <tr><th>Formulir Penelitian</th><td>${linkFile('formpenelitian', d.formulir_penelitian)}</td></tr>
                                    <tr><th>Catatan Pembimbing</th><td>${d.catatan_formulir ?? '-'}</td></tr>
                                    <tr><th>Tanggal Konfirmasi</th><td>${formatTanggal(d.tgl_upload_formulir)}</td></tr>
                                    <tr><th>Status Akhir</th><td><span class="badge badge-info">${d.status_akhir ?? '-'}</span></td></tr>

                                </table>

                            `;

                            $('#detailPesertaContent').html(html);
                        } else {
                            $('#detailPesertaContent').html('<p class="text-danger">Data tidak ditemukan.</p>');
                        }
                    },
                    error: function() {
                        $('#detailPesertaContent').html('<p class="text-danger">Terjadi kesalahan saat mengambil data.</p>');
                    }
                });
            });

            // 🔧 Helper kecil (JS)
            function linkFile(folder, filename) {
                if (!filename) return 'Belum Ada';
                return `<a href="<?= base_url('uploads/') ?>${folder}/${filename}" target="_blank">Lihat</a>`;
            }
            function formatTanggal(dateStr) {
                if (!dateStr) return '-';
                const d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            }
            function statusKonfirmasi(v) {
                if (v === 'Y') return 'Dikonfirmasi';
                if (v === 'N') return 'Tidak Konfirmasi';
                return 'Belum Dikonfirmasi';
            }
            function statusApproveUnit(v) {
                if (v === 'Y') return 'Disetujui';
                if (v === 'N') return 'Tidak Disetujui';
                return 'Belum Disetujui';
            }
            function statusValidasi(v) {
                if (v === 'Y') return 'Approved';
                if (v === 'N') return 'Tidak Approved';
                return 'Belum Approved';
            }
            function statusBerkas(v) {
                if (v === 'Y') return 'Valid';
                if (v === 'N') return 'Tidak Valid';
                return 'Belum Divalidasi';
            }
            
            //Submit form edit
            $(document).on('submit', '#formEditMagang', function(e) {
                e.preventDefault(); // cegah reload halaman

                const formData = $(this).serialize(); // ambil semua input form
                const magangId = $('input[name="magang_id"]').val();

                $.ajax({
                    url: "<?= base_url('admin/manage-magang/updateMagang/') ?>" + magangId,
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        // 🔹 Tampilkan loading state
                        Swal.fire({
                            title: 'Menyimpan Perubahan...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close(); // 🔹 Tutup loading setelah respon diterima

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Data magang berhasil diperbarui',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $('#modalEditMagang').modal('hide');

                            // reload tabel kalau pakai DataTable
                            if (typeof table !== 'undefined') table.ajax.reload();
                            else location.reload(); // fallback kalau gak pakai DataTable
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menyimpan data'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close(); // 🔹 Pastikan loading tertutup juga kalau error
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan koneksi ke server'
                        });
                    }
                });
            });

            //Submit form edit penelitian
            $(document).on('submit', '#formEditPenelitian', function(e) {
                e.preventDefault(); // cegah reload halaman

                const formData = $(this).serialize(); // ambil semua input form
                const penelitianId = $('input[name="penelitian_id"]').val();

                $.ajax({
                    url: "<?= base_url('admin/penelitian/updatePenelitian/') ?>" + penelitianId,
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        // 🔹 Tampilkan loading state
                        Swal.fire({
                            title: 'Menyimpan Perubahan...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close(); // 🔹 Tutup loading setelah respon diterima

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Data magang berhasil diperbarui',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $('#modalEditPenelitian').modal('hide');

                            // reload tabel kalau pakai DataTable
                            if (typeof table !== 'undefined') table.ajax.reload();
                            else location.reload(); // fallback kalau gak pakai DataTable
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menyimpan data'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close(); // 🔹 Pastikan loading tertutup juga kalau error
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan koneksi ke server'
                        });
                    }
                });  
            });

            //Menampilkan modal tambah/kembali rfid
            $(document).on("click", ".btn-rfid", function () {
                const type = $(this).data("type");
                const magangId = $(this).data("magang-id");
                const assignmentId = $(this).data("assignment-id") || "";
                const idRfid = $(this).data("id-rfid") || "";
                const rfidNo = $(this).data("rfid-no") || "";

                $("#rfidForm")[0].reset();
                $("#magang_id").val(magangId);
                $("#assignment_id").val(assignmentId);
                $("#id_rfid").val(idRfid);

                let modalTitle = "";
                let formAction = "";
                let submitClass = "";
                let modalBody = "";

                if (type === "add") {
                // Tambah RFID
                modalTitle = "Atur Nomor RFID";
                formAction = "<?= base_url('admin/setRFID/') ?>";
                submitClass = "btn-success";

                modalBody = `
                    <div class="form-group">
                    <label for="rfidSelect">Pilih Nomor RFID</label>
                    <select name="rfid_id" id="rfidSelect" class="form-control select2" required>
                        <option value="">-- Pilih RFID --</option>
                    </select>
                    </div>
                `;
                } else {
                // Kembalikan RFID
                modalTitle = "Kembalikan RFID";
                formAction = "<?= base_url('admin/returnRFID') ?>";
                submitClass = "btn-danger";

                modalBody = `
                    <p>Nomor RFID: <strong>${rfidNo}</strong></p>
                    <p>Pilih status pengembalian RFID:</p>

                    <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="status_returned" value="returned" checked>
                    <label class="form-check-label" for="status_returned">Dikembalikan dengan benar</label>
                    </div>

                    <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="status" id="status_lost" value="lost">
                    <label class="form-check-label text-danger" for="status_lost">
                        Hilang (wajib bayar denda, boleh diganti RFID baru jika masih aktif magang)
                    </label>
                    </div>

                    <div id="gantiRfidWrapper" class="mt-3" style="display: none;">
                    <label for="new_rfid_id">Pilih RFID Pengganti (Opsional)</label>
                    <select name="new_rfid_id" id="new_rfid_id" class="form-control select2">
                        <option value="">-- Tidak Diganti --</option>
                    </select>
                    </div>
                `;
                }

                $("#rfidModalTitle").text(modalTitle);
                $("#rfidModalBody").html(modalBody);
                $("#rfidSubmitBtn").removeClass("btn-success btn-danger").addClass(submitClass);
                $("#rfidForm").attr("action", formAction);
                $("#rfidModal").modal("show");

                // 🔹 Isi opsi RFID dari server
                $.ajax({
                url: "<?= base_url('admin/manage-magang/getAvailableRfid') ?>",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    let options = '<option value="">-- Pilih RFID --</option>';
                    data.forEach((rfid) => {
                    options += `<option value="${rfid.id_rfid}">${rfid.rfid_no}</option>`;
                    });

                    // untuk dropdown yang ada
                    $("#rfidSelect, #new_rfid_id").each(function () {
                    if ($(this).length) $(this).html(options).select2({ theme: "bootstrap4", width: "100%" });
                    });
                },
                });

                // 🔹 Tampilkan pilihan ganti RFID jika status = hilang
                $(document).on("change", "input[name='status']", function () {
                if ($("#status_lost").is(":checked")) {
                    $("#gantiRfidWrapper").slideDown(200);
                } else {
                    $("#gantiRfidWrapper").slideUp(200);
                }
                });
            });

            //Menampilkan modal tambah/kembali rfid penelitian
            $(document).on("click", ".btn-rfidp", function () {
                const type = $(this).data("type");
                const penelitianId = $(this).data("penelitian-id");
                const assignmentId = $(this).data("assignment-id") || "";
                const idRfid = $(this).data("id-rfid") || "";
                const rfidNo = $(this).data("rfid-no") || "";

                $("#rfidForm")[0].reset();
                $("#penelitian_id").val(penelitianId);
                $("#assignment_id").val(assignmentId);
                $("#id_rfid").val(idRfid);

                let modalTitle = "";
                let formAction = "";
                let submitClass = "";
                let modalBody = "";

                if (type === "add") {
                // Tambah RFID
                modalTitle = "Atur Nomor RFID";
                formAction = "<?= base_url('admin/penelitian/setRFID/') ?>";
                submitClass = "btn-success";

                modalBody = `
                    <div class="form-group">
                    <label for="rfidSelect">Pilih Nomor RFID</label>
                    <select name="rfid_id" id="rfidSelect" class="form-control select2" required>
                        <option value="">-- Pilih RFID --</option>
                    </select>
                    </div>
                `;
                } else {
                // Kembalikan RFID
                modalTitle = "Kembalikan RFID";
                formAction = "<?= base_url('admin/penelitian/returnRFID') ?>";
                submitClass = "btn-danger";

                modalBody = `
                    <p>Nomor RFID: <strong>${rfidNo}</strong></p>
                    <p>Pilih status pengembalian RFID:</p>

                    <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="status_returned" value="returned" checked>
                    <label class="form-check-label" for="status_returned">Dikembalikan dengan benar</label>
                    </div>

                    <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="status" id="status_lost" value="lost">
                    <label class="form-check-label text-danger" for="status_lost">
                        Hilang (wajib bayar denda, boleh diganti RFID baru jika masih aktif magang)
                    </label>
                    </div>

                    <div id="gantiRfidWrapper" class="mt-3" style="display: none;">
                    <label for="new_rfid_id">Pilih RFID Pengganti (Opsional)</label>
                    <select name="new_rfid_id" id="new_rfid_id" class="form-control select2">
                        <option value="">-- Tidak Diganti --</option>
                    </select>
                    </div>
                `;
                }

                $("#rfidModalTitle").text(modalTitle);
                $("#rfidModalBody").html(modalBody);
                $("#rfidSubmitBtn").removeClass("btn-success btn-danger").addClass(submitClass);
                $("#rfidForm").attr("action", formAction);
                $("#rfidModal").modal("show");

                // 🔹 Isi opsi RFID dari server
                $.ajax({
                url: "<?= base_url('admin/manage-magang/getAvailableRfid') ?>",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    let options = '<option value="">-- Pilih RFID --</option>';
                    data.forEach((rfid) => {
                    options += `<option value="${rfid.id_rfid}">${rfid.rfid_no}</option>`;
                    });

                    // untuk dropdown yang ada
                    $("#rfidSelect, #new_rfid_id").each(function () {
                    if ($(this).length) $(this).html(options).select2({ theme: "bootstrap4", width: "100%" });
                    });
                },
                });

                // 🔹 Tampilkan pilihan ganti RFID jika status = hilang
                $(document).on("change", "input[name='status']", function () {
                if ($("#status_lost").is(":checked")) {
                    $("#gantiRfidWrapper").slideDown(200);
                } else {
                    $("#gantiRfidWrapper").slideUp(200);
                }
                });
            });

            // Modal tolak laporan
            $(document).on('click', '.btn-tolak-formulir', function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                $('#namaPesertaLaporan').text('Peserta: ' + nama);
                $('#formTolakLaporan').attr('action', "<?= base_url('admin/penelitian/tolakFormulir/') ?>" + id);

                $('#tolakLaporanModal').modal('show');
            });

            $(document).on('click', '.btn-tolak-absensi-penelitian', function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                $('#namaPesertaAbsensi').text('Peserta: ' + nama);
                $('#formTolakAbsensi').attr('action', "<?= base_url('admin/penelitian/tolakAbsensi/') ?>" + id);

                $('#tolakAbsensiModal').modal('show');
            });

        });
    </script>
</body>
</html>
