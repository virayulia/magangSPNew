<?= $this->extend('admin/templates/index');?>

<?= $this->section('content');?>

<div class="container-fluid">
<?php $session = \Config\Services::session(); ?>
<?php if ($session->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $session->getFlashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($session->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $session->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<h1 class="h3 mb-2 text-gray-800">Daftar Peserta Magang</h1>
<!-- Card Tabel -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <form method="get" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="bulan" class="form-control">
                        <option value="">-- Pilih Bulan Masuk --</option>
                        <?php 
                        $bulanList = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        foreach ($bulanList as $key => $nama): ?>
                            <option value="<?= $key ?>" <?= ($key == @$_GET['bulan']) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="tahun" class="form-control">
                        <option value="">-- Pilih Tahun --</option>
                        <?php
                        $tahunSekarang = date('Y');
                        for ($i = 2025; $i <= $tahunSekarang + 2; $i++): 
                            $selected = '';
                            if (!isset($_GET['tahun']) && $i == $tahunSekarang) {
                                // kalau belum ada filter, default tahun berjalan
                                $selected = 'selected';
                            } elseif (isset($_GET['tahun']) && $_GET['tahun'] == $i) {
                                // kalau sudah pilih, pakai yang dipilih
                                $selected = 'selected';
                            }
                        ?>
                            <option value="<?= $i ?>" <?= $selected ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>


                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <form action="<?= base_url('pembimbing/setApproveMagang') ?>" method="post" id="bulkApproveForm">
    <!-- Tombol Bulk Approve -->
    <div class="mb-2">
        <button type="submit" class="btn btn-success" onclick="return confirm('Yakin ingin bulk approve peserta terpilih?')">
            <i class="fas fa-check-circle"></i> Approve
        </button>
    </div>

    <table class="table table-bordered" width="100%" cellspacing="0" id="dataTable">
        <thead class="table-dark">
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Unit Kerja</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>No. RFID</th>
                <th>Laporan</th>
                <th>Absensi</th>
                <th>Nilai Magang</th>
                <th>Status RFID</th>
                <th>Feedback</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($peserta)): ?>
                <?php $no = 1; foreach ($peserta as $item): ?>
                    <tr>
                        <td>
                            <?php if (empty($item['ka_unit_approve'])): ?>
                                <input type="checkbox" name="magang_ids[]" value="<?= $item['magang_id'] ?>">
                            <?php endif; ?>
                        </td>
                        <td><?= $no++; ?></td>
                        <td><?= esc($item['fullname']) ?></td>
                        <td><?= esc($item['nisn_nim']) ?></td>
                        <td><?= esc($item['unit_kerja']) ?></td>
                        <td><?= format_tanggal_indonesia($item['tanggal_masuk']) ?></td>
                        <td><?= format_tanggal_indonesia($item['tanggal_selesai']) ?></td>
                        <td><?= esc($item['rfid_no']) ?: '-' ?></td>
                        <td>
                            <?php if (!empty($item['laporan'])): ?>
                                <a href="<?= base_url('uploads/laporan/' . $item['laporan']) ?>" target="_blank">Lihat Laporan</a>
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($item['absensi'])): ?>
                                <a href="<?= base_url('uploads/absensi/' . $item['absensi']) ?>" target="_blank">Lihat Absensi</a>
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                        <?php 
                            $total = $item['nilai_disiplin'] + $item['nilai_kerajinan'] + $item['nilai_tingkahlaku'] +
                                    $item['nilai_kerjasama'] + $item['nilai_kreativitas'] + $item['nilai_kemampuankerja'] +
                                    $item['nilai_tanggungjawab'] + $item['nilai_penyerapan'];
                            $rata = round($total / 8, 2);
                        ?>
                                <span class="badge bg-success text-light">
                                    <strong><?= $rata ?></strong>
                                </span>
                        <button type="button" 
                                class="btn btn-sm btn-info mt-1" 
                                title="Detail Nilai"
                                data-toggle="modal" 
                                data-target="#modalDetail-<?= $item['magang_id'] ?>">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <!-- Modal detail -->
                        <div class="modal fade" id="modalDetail-<?= $item['magang_id'] ?>" tabindex="-1" aria-labelledby="modalDetailLabel-<?= $item['magang_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg"><!-- modal-lg biar lebar -->
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="modalDetailLabel-<?= $item['magang_id'] ?>">
                                            Detail Penilaian: <?= esc($item['fullname']) ?>
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row">
                                            <?php 
                                            $fields = [
                                                'disiplin'       => 'Disiplin',
                                                'kerajinan'      => 'Kerajinan',
                                                'tingkahlaku'    => 'Tingkah Laku',
                                                'kerjasama'      => 'Kerja Sama',
                                                'kreativitas'    => 'Kreativitas',
                                                'kemampuankerja' => 'Kemampuan Kerja',
                                                'tanggungjawab'  => 'Tanggung Jawab',
                                                'penyerapan'     => 'Penyerapan'
                                            ];
                                            $totalNilai = 0;
                                            $jumlahField = count($fields);
                                            foreach ($fields as $name => $label): 
                                                $nilai = (int) $item['nilai_' . $name];
                                                $stars = ($nilai - 50) / 10; // 60=1, 70=2, ... 100=5
                                                $totalNilai += $nilai; // ✅ jumlahkan nilai tiap field
                                            ?>
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold"><?= $label ?></label><br>
                                                    <?php for ($i=1; $i<=5; $i++): ?>
                                                        <?php if ($i <= $stars): ?>
                                                            <i class="fas fa-star text-warning"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-star text-muted"></i>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                    <span class="ml-2">(<?= $nilai ?>)</span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <?php 
                                        $rataRata = $jumlahField > 0 ? $totalNilai / $jumlahField : 0;
                                        if ($rataRata >= 60 && $rataRata <= 69) {
                                            $kategori = "Kurang";
                                        } elseif ($rataRata >= 70 && $rataRata <= 79) {
                                            $kategori = "Cukup";
                                        } elseif ($rataRata >= 80 && $rataRata <= 89) {
                                            $kategori = "Baik";
                                        } elseif ($rataRata >= 90 && $rataRata <= 100) {
                                            $kategori = "Baik Sekali";
                                        } else {
                                            $kategori = "-";
                                        }
                                        ?>

                                        <!-- Rata-rata & Kategori -->
                                        <div class="alert alert-info mt-3">
                                            <strong>Rata-rata Nilai:</strong> <?= number_format($rataRata, 2) ?><br>
                                            <strong>Kategori:</strong> <?= $kategori ?>
                                        </div>

                                        <div class="form-group mt-3">
                                            <label class="font-weight-bold">Catatan atau Komentar</label>
                                            <textarea class="form-control" rows="3" readonly><?= esc($item['catatan']) ?></textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                        <td>
                            <?php if($item['status_rfid'] === 'returned'): ?>
                                <span class="badge bg-success text-light">Dikembalikan</span>
                            <?php elseif($item['status_rfid'] === 'lost') : ?>
                                <span class="badge bg-danger text-light">Denda</span>
                            <?php elseif($item['status_rfid'] === 'aktif') :?>
                                <span class="badge bg-primary text-light">Digunakan</span>
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($item['feedback_id'])): ?>
                                <i class="bi bi-check-circle-fill text-success" title="Sudah isi feedback"></i>
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill text-danger" title="Belum isi feedback"></i>
                            <?php endif; ?>
                        </td>
                        <!-- <td>
                            <?php if (!empty($item['ka_unit_approve'] && $item['ka_unit_approve'] == 1)): ?>
                                <i class="bi bi-check-circle-fill text-success" title="Sudah diapprove"></i>
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill text-danger" title="Belum diapprove"></i>
                            <?php endif; ?>
                        </td> -->
                        <td>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#detailModal<?= $item['magang_id'] ?>">Detail</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</form>

        </div>
    </div>
</div>

<!-- Modal Detail -->
<?php foreach ($peserta as $item): ?>
    <div class="modal fade" id="detailModal<?= $item['magang_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel<?= $item['magang_id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel<?= $item['magang_id'] ?>">Detail Peserta - <?= esc($item['fullname']) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- Infomasi Mahasiswa -->
                <h6 class="text-primary">📌 Informasi Mahasiswa</h6>
                <table class="table table-sm table-bordered">
                    <tr>
                        <th>Nama Lengkap</th>
                        <td><?= esc($item['fullname']) ?></td>
                        <!-- gambar ditaruh di sini dengan rowspan -->
                        <td rowspan="4" class="text-center align-middle" style="width: 180px;">
                            <?php if (!empty($item['user_image'])): ?>
                            <img src="<?= base_url('uploads/user-image/' . ($item['user_image'] ?? 'default.png')) ?>" 
                                alt="Foto <?= esc($item['fullname']) ?>" 
                                class="img-thumbnail" style="max-width: 150px;">
                            <?php else: ?>
                                <span class="text-muted">Tidak ada foto</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>NIM/NISN</th>
                        <td><?= esc($item['nisn_nim']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= esc($item['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <?php if ($item['jenis_kelamin'] === 'L'): ?>
                            <td>Laki-Laki</td>
                        <?php elseif ($item['jenis_kelamin'] === 'P'): ?>
                            <td>Perempuan</td>
                        <?php else: ?>
                            <td>-</td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td colspan="2">
                            <?php
                                $alamat = $item['alamat'] ?? '';
                                $kota = trim(($item['tipe_kota_ktp'] ?? '') . ' ' . ($item['kota_ktp'] ?? ''));
                                $prov = $item['provinsi_ktp'] ?? '';
                                $parts = array_filter([$alamat, $kota, $prov]);
                                echo esc(implode(', ', $parts)) ?: 'Data belum diisi';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Domisili</th>
                        <td colspan="2">
                            <?php
                                $alamat = $item['domisili'] ?? '';
                                $kota = trim(($item['tipe_kota_domisili'] ?? '') . ' ' . ($item['kota_domisili'] ?? ''));
                                $prov = $item['provinsi_domisili'] ?? '';
                                $parts = array_filter([$alamat, $kota, $prov]);
                                echo esc(implode(', ', $parts)) ?: 'Data belum diisi';
                            ?>
                        </td>
                    </tr>
                </table>
                <!-- Pendidikan -->
                <h6 class="text-primary mt-4">🎓 Pendidikan</h6>
                <table class="table table-sm table-bordered">
                    <tr><th>Tingkat</th><td><?= esc($item['tingkat_pendidikan'] ?? '-') ?></td></tr>
                    <?php if($item['tingkat_pendidikan'] === 'SMK'): ?>
                    <tr><th>Sekolah</th><td><?= esc($item['nama_instansi'] ?? '-') ?></td></tr>
                    <?php else: ?>
                    <tr><th>Perguruan Tinggi</th><td><?= esc($item['nama_instansi'] ?? '-') ?></td></tr>
                    <?php endif; ?>
                    <tr><th>Jurusan</th><td><?= esc($item['nama_jurusan'] ?? '-') ?></td></tr>
                    <?php if ($item['tingkat_pendidikan'] === 'SMK'): ?>
                        <tr><th>Kelas</th><td>Kelas <?= esc($item['semester']) ?></td></tr>
                    <?php else: ?>
                        <tr><th>Semester</th><td>Semester <?= esc($item['semester']) ?></td></tr>
                        <tr><th>IPK</th><td><?= esc($item['nilai_ipk'] ?? '-') ?></td></tr>
                    <?php endif; ?>
                </table>

                <h6 class="text-primary mt-4">🗂️ Dokumen</h6>
                <table class="table table-sm table-bordered">
                    <?php if($item['tingkat_pendidikan'] === 'SMK'):?>
                        <tr><th>Surat Permohonan</th><td><?= $item['surat_permohonan'] ? '<a href="'.base_url('uploads/surat-permohonan/'.$item['surat_permohonan']).'" target="_blank">Lihat Surat</a>' : 'Belum Ada' ?></td></tr>
                        <tr><th>Tanggal Surat</th><td><?= !empty($item['tanggal_surat']) ? date('d-m-Y', strtotime($item['tanggal_surat'])) : '-' ?></td></tr>
                        <tr><th>Nomor Surat</th><td><?= !empty($item['no_surat']) ? esc($item['no_surat']) : '-' ?></td></tr>
                        <tr><th>Nama Pimpinan</th><td><?= !empty($item['nama_pimpinan']) ? esc($item['nama_pimpinan']) : '-' ?></td></tr>
                        <tr><th>Jabatan Pimpinan</th><td><?= !empty($item['jabatan']) ? esc($item['jabatan']) : '-' ?></td></tr>
                        <tr><th>Email Instansi</th><td><?= !empty($item['email_instansi']) ? esc($item['email_instansi']) : '-' ?></td></tr>
                        <tr><th>KTP/KK</th><td><?= $item['ktp_kk'] ? '<a href="'.base_url('uploads/ktp-kk/'.$item['ktp_kk']).'" target="_blank">Lihat KTP/KK</a>' : 'Belum Ada' ?></td></tr>
                        <tr><th>BPJS TK</th><td><?= $item['bpjs_tk'] ? '<a href="'.base_url('uploads/bpjs-tk/'.$item['bpjs_tk']).'" target="_blank">Lihat BPJS TK</a>' : 'Belum Ada' ?></td></tr>
                        <tr><th>Bukti BPJS TK</th><td><?= $item['buktibpjs_tk'] ? '<a href="'.base_url('uploads/buktibpjs-tk/'.$item['buktibpjs_tk']).'" target="_blank">Lihat Bukti</a>' : 'Belum Ada' ?></td></tr>
                    <?php else:?>
                        <tr><th>CV</th><td><?= $item['cv'] ? '<a href="'.base_url('uploads/cv/'.$item['cv']).'" target="_blank">Lihat CV</a>' : 'Belum Ada' ?></td></tr>
                        <tr><th>Proposal</th><td><?= $item['proposal'] ? '<a href="'.base_url('uploads/proposal/'.$item['proposal']).'" target="_blank">Lihat Proposal</a>' : 'Belum Ada' ?></td></tr>
                        <tr><th>Surat Permohonan</th><td><?= $item['surat_permohonan'] ? '<a href="'.base_url('uploads/surat-permohonan/'.$item['surat_permohonan']).'" target="_blank">Lihat Surat</a>' : 'Belum Ada' ?></td></tr>
                        <tr><th>Tanggal Surat</th><td><?= !empty($item['tanggal_surat']) ? date('d-m-Y', strtotime($item['tanggal_surat'])) : '-' ?></td></tr>
                        <tr><th>Nomor Surat</th><td><?= !empty($item['no_surat']) ? esc($item['no_surat']) : '-' ?></td></tr>
                        <tr><th>Nama Pimpinan</th><td><?= !empty($item['nama_pimpinan']) ? esc($item['nama_pimpinan']) : '-' ?></td></tr>
                        <tr><th>Jabatan Pimpinan</th><td><?= !empty($item['jabatan']) ? esc($item['jabatan']) : '-' ?></td></tr>
                        <tr><th>Email Instansi</th><td><?= !empty($item['email_instansi']) ? esc($item['email_instansi']) : '-' ?></td></tr>
                        <tr><th>KTP/KK</th><td><?= $item['ktp_kk'] ? '<a href="'.base_url('uploads/ktp-kk/'.$item['ktp_kk']).'" target="_blank">Lihat KTP/KK</a>' : 'Belum Ada' ?></td></tr>
                        <tr><th>BPJS TK</th><td><?= $item['bpjs_tk'] ? '<a href="'.base_url('uploads/bpjs-tk/'.$item['bpjs_tk']).'" target="_blank">Lihat BPJS TK</a>' : 'Belum Ada' ?></td></tr>
                        <tr><th>Bukti BPJS TK</th><td><?= $item['buktibpjs_tk'] ? '<a href="'.base_url('uploads/buktibpjs-tk/'.$item['buktibpjs_tk']).'" target="_blank">Lihat Bukti</a>' : 'Belum Ada' ?></td></tr>
                    <?php endif;?>
                </table>

                <h6 class="text-primary mt-4">📆 Status Magang</h6>
                <table class="table table-sm table-bordered">
                    <tr><th>Unit Kerja</th><td><?= esc($item['unit_kerja']) ?></td></tr>
                    <tr><th>Tanggal Masuk</th><td><?= esc(format_tanggal_indonesia($item['tanggal_masuk'])) ?></td></tr>
                    <tr><th>Tanggal Selesai</th><td><?= esc(format_tanggal_indonesia($item['tanggal_selesai'])) ?></td></tr>
                    <tr><th>Durasi</th><td><?= esc($item['durasi']) ?> bulan</td></tr>
                    <tr><th>Status Seleksi</th><td><?= esc($item['status_seleksi'] ?? 'Belum Diseleksi') ?></td></tr>
                    <tr><th>Tanggal Seleksi</th><td><?= esc(format_tanggal_indonesia_dengan_jam($item['tanggal_seleksi']) ?? '-') ?></td></tr>
                    <tr><th>Status Konfirmasi</th><td><?= esc($item['status_konfirmasi'] === 'Y' ? 'Dikonfirmasi' : ($item['status_konfirmasi'] === 'N' ? 'Tidak Konfirmasi' : 'Belum Dikonfirmasi')) ?></td></tr>
                    <tr><th>Tanggal Konfirmasi</th><td><?= esc(format_tanggal_indonesia_dengan_jam($item['tanggal_konfirmasi']) ?? '-') ?></td></tr>
                    <tr><th>Status Approval Konfirmasi</th>
                        <td><?= $item['status_validasi_berkas'] === 'Y' ? 'Approved' : ($item['status_validasi_berkas'] === 'N' ? 'Tidak Approved' : 'Belum Approved') ?></td>
                    </tr>
                    <tr><th>Tanggal Approval Konfirmasi</th><td><?= $item['tanggal_validasi_berkas'] ? format_tanggal_indonesia_dengan_jam($item['tanggal_validasi_berkas']) : '-' ?></td></tr>
                    <tr><th>Status Berkas Lengkap</th>
                        <td><?= $item['status_berkas_lengkap'] === 'Y' ? 'Valid' : ($item['status_berkas_lengkap'] === 'N' ? 'Tidak Valid' : 'Belum Divalidasi') ?></td>
                    </tr>
                    <tr><th>Tanggal Berkas Lengkap</th><td><?= $item['tanggal_berkas_lengkap'] ? format_tanggal_indonesia_dengan_jam($item['tanggal_berkas_lengkap']) : '-' ?></td></tr>
                    <tr><th>Catatan Validasi Berkas</th><td><?= esc($item['cttn_berkas_lengkap'] ?? '-') ?></td></tr>
                    <tr><th>Tanggal Setujui Pernyataan</th><td><?= $item['tanggal_setujui_pernyataan'] ? format_tanggal_indonesia($item['tanggal_setujui_pernyataan']) : '-' ?></td></tr>
                    <tr><th>Status Akhir</th><td><span class="badge badge-info"><?= esc($item['status_akhir']) ?></span></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-danger" onclick="batalkanMagang(<?= $item['magang_id'] ?>, 'detailModal<?= $item['magang_id'] ?>')">Batalkan</button>
                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>


<script>
document.getElementById('selectAll').addEventListener('click', function(e) {
    const checkboxes = document.querySelectorAll('input[name="magang_ids[]"]');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
});

    function batalkanMagang(id, modalId) {
    // Tutup modal Bootstrap dulu
    $('#' + modalId).modal('hide');

    // Delay agar modal benar-benar tertutup sebelum SweetAlert muncul
    setTimeout(function() {
        Swal.fire({
            title: 'Batalkan Magang?',
            input: 'textarea',
            inputLabel: 'Alasan Pembatalan',
            inputPlaceholder: 'Tulis alasan pembatalan di sini...',
            inputAttributes: {
                'aria-label': 'Tulis alasan pembatalan'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            preConfirm: (alasan) => {
                if (!alasan) {
                    Swal.showValidationMessage('Alasan pembatalan wajib diisi.');
                }
                return alasan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('admin/batalkanMagang') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'id': id,
                        'alasan': result.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Berhasil', 'Peserta magang telah dibatalkan.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }, 300); 
}

</script>


</div>

<?= $this->endSection() ?>
