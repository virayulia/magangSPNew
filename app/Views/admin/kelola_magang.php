<?= $this->extend('admin/templates/index');?>

<?= $this->section('content');?>
<style>
    .small-card .card-body {
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}

.small-card .h5 {
    margin-top: 2px;
    margin-bottom: 2px;
}

.small-card .text-xs {
    font-size: 0.68rem !important;
    margin-bottom: 2px !important;
}

.mb-4 {
    margin-bottom: 0.8rem !important; /* lebih kecil dari default 1.5rem */
}

.mt-4 {
    margin-top: 1rem !important;
}

.hover-card {
    transition: 0.15s ease;
    cursor: pointer;
}

.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}
.chart-small {
    height: 230px !important;
    margin: auto;
}


</style>
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
    <!-- <div class="mb-2">
        <?php if (!empty($totalKuota)): ?>

            <?php 
                $grupKuota = [];
                foreach ($totalKuota as $k) {
                    $grupKuota[$k->tingkat_pendidikan][] = (array)$k;
                }
            ?>

            <?php foreach ($grupKuota as $tingkat => $items): ?>
                <h6 class="mb-2 mt-3 fw-bold text-grey text-center text-uppercase"><b><?= $tingkat ?></b></h6>

                <?php foreach ($items as $k): ?>
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-6 col-md-2">
                                <div class="card border-left-primary shadow h-100 py-2 small-card">
                                    <div class="card-body py-2 text-center">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Kuota
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $k['kuota'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="card border-left-secondary shadow h-100 py-2 small-card">
                                    <div class="card-body py-2 text-center">
                                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                            Proses Daftar
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $k['jumlah_proses']?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="<?= base_url('admin/manage-magang?filter=aktif&tingkat='.$k['tingkat_pendidikan'].'&unit_kerja='.@$_GET['unit_kerja']) ?>" class="text-decoration-none">
                                    <div class="card border-left-success shadow h-100 py-2 small-card hover-card">
                                        <div class="card-body py-2 text-center">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Aktif Magang
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $k['jumlah_aktif'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="<?= base_url('admin/manage-magang?filter=akan_magang&tingkat='.$k['tingkat_pendidikan'].'&unit_kerja='.@$_GET['unit_kerja']) ?>" class="text-decoration-none">
                                    <div class="card border-left-info shadow h-100 py-2 small-card hover-card">
                                        <div class="card-body py-2 text-center">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Akan Magang
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $k['jumlah_akan_magang'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="<?= base_url('admin/manage-magang?filter=belum_selesai&tingkat='.$k['tingkat_pendidikan'].'&unit_kerja='.@$_GET['unit_kerja']) ?>" class="text-decoration-none">
                                    <div class="card border-left-dark shadow h-100 py-2 small-card hover-card">
                                        <div class="card-body py-2 text-center">
                                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                                Belum Lulus
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $k['jumlah_belum_selesai'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="card border-left-danger shadow h-100 py-2 small-card">
                                    <div class="card-body py-2 text-center">

                                        <?php
                                            $bulanPenempatan = date('Y-m-01', strtotime('+2 months'));
                                            $hariKe = date('N', strtotime($bulanPenempatan));

                                            if ($hariKe == 6) $tanggalMulai = date('Y-m-d', strtotime("$bulanPenempatan +2 days"));
                                            elseif ($hariKe == 7) $tanggalMulai = date('Y-m-d', strtotime("$bulanPenempatan +1 day"));
                                            else $tanggalMulai = $bulanPenempatan;
                                        ?>

                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Kuota <?= date('j', strtotime($tanggalMulai)); ?> <?= format_bulan_singkat($tanggalMulai); ?>
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $k['sisa_kuota'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endforeach; ?>

        <?php else: ?>
            <p><i>Tidak ada kuota untuk unit ini.</i></p>
        <?php endif; ?>
    </div> -->
    <div class="card shadow"> 
        <div class="card-body">
            <form method="get" class="row g-2 mb-3">

                <div class="col-md-3">
                    <label class="form-label">Unit Kerja</label>
                    <select name="unit_kerja" class="form-control select2">
                        <option value="">-- Unit Kerja --</option>
                        <?php foreach ($unitList as $key): ?>
                            <option value="<?= $key['unit_id'] ?>"
                                <?= ($key['unit_id'] == @$_GET['unit_kerja']) ? 'selected' : '' ?>>
                                <?= $key['unit_kerja'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Masuk</label>
                    <input type="text"
                        name="tanggal_masuk"
                        class="form-control monthpicker"
                        placeholder="Pilih Bulan & Tahun"
                        value="<?= esc(@$_GET['tanggal_masuk']) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Keluar</label>
                    <input type="text"
                        name="tanggal_keluar"
                        class="form-control monthpicker"
                        placeholder="Pilih Bulan & Tahun"
                        value="<?= esc(@$_GET['tanggal_keluar']) ?>">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>

            </form>

        </div>
    </div>

    <div class="row mt-4">
        
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 text-secondary fw-bold font-weight-bold text-center text-uppercase">SMK</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartSmk" class="chart-small"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 text-secondary fw-bold font-weight-bold text-center text-uppercase">PERGURUAN TINGGI</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartPt"  class="chart-small"></canvas>
                </div>
            </div>
        </div>
    </div>





<!-- Card Tabel -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            

            <a href="<?= base_url('admin/export-peserta?bulan_masuk='.service('request')->getGet('bulan_masuk').'&bulan_keluar='.service('request')->getGet('bulan_keluar').'&tahun='.service('request')->getGet('tahun')) ?>" 
                class="btn btn-success mb-3">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>

            <table class="table table-bordered table-striped" width="100%" cellspacing="0" id="dataTable" >
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <!-- <th>NIM</th> -->
                        <th>Unit Kerja</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>No. RFID</th>
                        <th>Validasi Berkas</th>
                        <th>Setuju Pernyataan</th>
                        <th>Nilai Tes</th>
                        <th>Pembimbing</th>
                        <th>Laporan</th>
                        <th>Absensi</th>
                        <th>Nilai Magang</th>
                        <th>Status RFID</th>
                        <th>Feedback</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php $no = 1; foreach ($data as $item): ?>
                            <td><?= $no++; ?></td>
                            <td><?= esc($item['fullname']) ?></td>
                            <!-- <td><?= esc($item['nisn_nim']) ?></td> -->
                            <td><?= esc($item['unit_kerja']) ?></td>
                            <td><?= format_tanggal($item['tanggal_masuk']) ?></td>
                            <td><?= format_tanggal($item['tanggal_selesai']) ?></td>   
                            <td><?= esc($item['rfid_no']) ?: '-' ?></td>
                            <td><?php if(!empty($item['status_berkas_lengkap']) && $item['status_berkas_lengkap'] === 'Y'): ?>
                                    <span class="btn btn-sm btn-success text-light"><i class="fas fa-check-circle" title="Valid"></i></span>
                                                                
                                <?php else: ?>
                                <a href="<?= base_url('admin/manage-kelengkapan-berkas/' . $item['magang_id']) ?>">
                                <span class="btn btn-sm btn-danger text-light"><i class="fas fa-times-circle" title="Tidak Valid"></i></span>
                                </a>
                                <?php endif; ?>
                                
                            </td>
                            <td><?php if(!empty($item['tanggal_setujui_pernyataan'])): ?>
                                <span class="btn btn-sm btn-success text-light"><strong><i class="fas fa-check-circle" title="Setuju"></i></strong></span>
                                <?php else: ?>
                                <span class="btn btn-sm btn-danger text-light"><strong><i class="fas fa-times-circle" title="Belum Setuju"></i></strong></span>
                                <?php endif; ?>
                            </td>  
                            <td>
                                <?php
                                    $badgeClass = 'btn btn-sm btn-danger text-light'; 
                                    if ($item['status_tes'] === 'Lulus') {
                                        $badgeClass = 'btn btn-sm btn-success text-light'; 
                                        $title = 'Lulus';
                                    } elseif ($item['status_tes'] === 'Belum Lulus') {
                                        $badgeClass = 'btn btn-sm btn-warning text-light'; 
                                        $title = 'Belum Lulus';
                                    }elseif ($item['status_tes'] === '-') {
                                        $badgeClass = 'btn btn-sm btn-danger text-light'; 
                                        $title = 'Belum Tes';
                                    }
                                ?>

                                <?php if(!empty($item['nilai_maksimal'])): ?>
                                    <span class="<?= $badgeClass ?>" title="<?= $title; ?>"><strong><?= $item['nilai_maksimal'] ?></strong></span>
                                <?php else : ?>
                                    <span class="<?= $badgeClass ?>" title="<?= $title; ?>">
                                        <strong><?= $item['status_tes'] ?></strong>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($item['nama_pembimbing'])): ?>
                                    <!-- Belum Ada -->
                                    <button 
                                        class="btn btn-sm btn-primary ml-2 btn-set-pembimbing"
                                        data-id="<?= $item['magang_id'] ?>"
                                        data-unit="<?= $item['unit_id'] ?>"
                                        data-pembimbing=""
                                        data-nama="<?= esc($item['fullname']) ?>"
                                        data-mode="tambah"
                                        title="Tambah Pembimbing"
                                    >
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <span title="<?= esc($item['nama_pembimbing']) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></span>
                                    <button 
                                        class="btn btn-sm btn-warning ml-2 btn-set-pembimbing"
                                        data-id="<?= $item['magang_id'] ?>"
                                        data-unit="<?= $item['unit_id'] ?>"
                                        data-pembimbing="<?= $item['pembimbing_id'] ?>"
                                        data-nama="<?= esc($item['fullname']) ?>"
                                        data-mode="edit"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                       
                            <td>
                                <?php if (!empty($item['laporan']) || !empty($item['url_laporan'])): ?>
                                    <?php if(!empty($item['laporan'])):?>
                                        <a href="<?= base_url('uploads/laporan/' . $item['laporan']) ?>" target="_blank" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye" title="Lihat Laporan"></i>
                                        </a>
                                    <?php elseif(!empty($item['url_laporan'])): ?>
                                        <a href="<?= $item['url_laporan'] ?>" target="_blank" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye" title="Lihat Laporan"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-danger btn-sm btn-tolak-laporan" 
                                        data-id="<?= $item['magang_id'] ?>" 
                                        data-nama="<?= $item['fullname'] ?>"
                                        title="Tolak Laporan">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary"> <i class="fas fa-times-circle" title="Belum Ada"></i></button>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($item['absensi']) || !empty($item['url_absensi'])): ?>
                                    <?php if(!empty($item['absensi'])):?>
                                        <a href="<?= base_url('uploads/absensi/' . $item['absensi']) ?>" target="_blank" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye" title="Lihat Absensi"></i>
                                        </a>
                                    <?php elseif(!empty($item['url_absensi'])): ?>
                                        <a href="<?= $item['url_absensi'] ?>" target="_blank" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye" title="Lihat Absensi"></i>
                                        </a>
                                    <?php endif; ?>

                                    <button type="button" class="btn btn-danger btn-sm btn-tolak-absensi" 
                                        title="Tolak Absensi"
                                        data-id="<?= $item['magang_id'] ?>" 
                                        data-nama="<?= $item['fullname'] ?>">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary"> <i class="fas fa-times-circle" title="Belum Ada"></i></button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $total = $item['nilai_disiplin'] + $item['nilai_kerajinan'] + $item['nilai_tingkahlaku'] +
                                            $item['nilai_kerjasama'] + $item['nilai_kreativitas'] + $item['nilai_kemampuankerja'] +
                                            $item['nilai_tanggungjawab'] + $item['nilai_penyerapan'];
                                    $rata = round($total / 8, 2);
                                ?>
                                <!-- Tombol detail nilai -->
                                <button type="button" 
                                        class="btn btn-sm btn-info mt-1 btn-detail-nilai" 
                                        title="Klik Untuk Lihat Detail"
                                        data-id="<?= $item['magang_id'] ?>" >
                                    <strong><?= $rata ?></strong>
                                </button>
                            </td>  
                            <td><?php if($item['status_rfid'] === 'returned'): ?>
                                <span class="btn btn-sm btn-success text-light" title="Dikembalikan"><strong><i class="fas fa-check-circle"></i></strong></span>
                                <?php elseif($item['status_rfid'] === 'lost') : ?>
                                <span class="btn btn-sm btn-danger text-light" title="Denda"><strong><i class="fas fa-money-bill-alt"></i></strong></span>
                                <?php elseif($item['status_rfid'] === 'aktif') :?>
                                <span class="btn btn-sm btn-primary text-light" title="Digunakan"><strong><i class="fas fa-check-circle"></i></strong></span>
                                <?php else : ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td>           
                                <?php if (!empty($item['feedback_id'])): ?>
                                    <button class="btn btn-sm btn-success"><i class="fas fa-check-circle" title="Sudah isi feedback"></i></button>
                                    
                                <?php else: ?>
                                    <button class="btn btn-sm btn-danger"> <i class="fas fa-times-circle" title="Belum isi feedback"></i></button>
                                    
                                <?php endif; ?>
                            </td>                                          
                            <td class="text-center">
                                <div class="aksi-wrapper">
                                    <?php
                                    $hariTerakhir = date('Y-m-d') >= $item['tanggal_selesai']; 
                                    $laporanAda   = !empty($item['laporan']) || !empty($item['url_laporan']);
                                    $absensiAda   = !empty($item['absensi']) || !empty($item['url_absensi']);
                                    $nilaiAda     = ($rata > 0);
                                    $rfidOk       = empty($item['status_rfid']) || in_array($item['status_rfid'], ['returned','lost']);
                                    $feedbackAda  = !empty($item['feedback_id']);
                                    ?>

                                                                   
                                    <button class="btn btn-sm btn-info btn-detail-peserta" data-id="<?= $item['magang_id'] ?>" title="Detail"><i class="fas fa-eye"></i></button>
                                    <!-- Tombol Aksi RFID -->
                                    <?php if (!empty($item['rfid_no']) && $item['status_rfid'] != 'returned'): ?>
                                        <!-- Tombol Kembalikan RFID -->
                                        <button 
                                            class="btn btn-sm btn-danger btn-rfid"
                                            data-type="return"
                                            data-magang-id="<?= $item['magang_id'] ?>"
                                            data-assignment-id="<?= $item['assignment_id'] ?>"
                                            data-id-rfid="<?= $item['id_rfid'] ?>"
                                            data-rfid-no="<?= esc($item['rfid_no']) ?>"
                                            title="Kembalikan RFID"
                                        >
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php else: ?>
                                        <!-- Tombol Tambah RFID -->
                                        <button 
                                            class="btn btn-sm btn-success btn-rfid"
                                            data-type="add"
                                            data-magang-id="<?= $item['magang_id'] ?>"
                                            title="Tambah RFID"
                                        >
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                    <!-- Tombol Aksi Upload -->
                                    <?php if ($item['allow_upload']): ?>
                                        <button type="button"
                                                class="btn btn-warning btn-sm btn-toggle-upload"
                                                data-url="<?= base_url('admin/manage-magang/tutupUpload/' . $item['magang_id']) ?>"
                                                data-action="tutup"
                                                title="Tutup Akses Upload">
                                            <i class="bi bi-lock-fill"></i>
                                        </button>

                                    <?php else: ?>
                                        <button type="button"
                                                class="btn btn-success btn-sm btn-toggle-upload"
                                                data-url="<?= base_url('admin/manage-magang/bukaUpload/' . $item['magang_id']) ?>"
                                                data-action="buka"
                                                 title="Buka Akses Upload">
                                            <i class="bi bi-unlock-fill"></i>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($hariTerakhir && $laporanAda && $absensiAda && $nilaiAda && $rfidOk && $feedbackAda ): ?>
                                        <?php if ($item['finalisasi'] == null): ?>
                                            <form action="<?= base_url('admin/finalisasi/'.$item['magang_id']) ?>" 
                                                method="post" class="finalisasi-form" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-success btn-sm finalisasi-btn" title="Lanjut Finalisasi">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled title="Menunggu Approve Pusdiklat">
                                                <i class="fas fa-hourglass-half"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled title="Belum bisa finalisasi">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    <?php endif; ?> 
                                </div>
                            </td>
                                                    
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pembimbing -->
<div class="modal fade" id="pembimbingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form id="formPembimbing" method="post" action="">
        <?= csrf_field() ?>

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalPembimbingTitle">Pilih Pembimbing</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <p class="font-weight-bold text-primary" id="namaPeserta"></p>

          <div class="form-group">
            <label>Pembimbing</label>
            <select name="pembimbing_id" id="selectPembimbing" class="form-control select2" required>
              <option value="" disabled selected>Memuat data...</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSubmitPembimbing">Simpan</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Modal Tolak Laporan -->
<div class="modal fade" id="tolakLaporanModal" tabindex="-1" aria-labelledby="tolakLaporanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formTolakLaporan" method="post" action="">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="tolakLaporanLabel">Tolak Laporan</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p id="namaPesertaLaporan" class="font-weight-bold text-primary"></p>
          <div class="form-group">
            <label>Alasan Penolakan</label>
            <textarea name="catatan" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tolak Absensi -->
<div class="modal fade" id="tolakAbsensiModal" tabindex="-1" aria-labelledby="tolakAbsensiLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formTolakAbsensi" method="post" action="">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="tolakAbsensiLabel">Tolak Absensi</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p id="namaPesertaAbsensi" class="font-weight-bold text-primary"></p>
          <div class="form-group">
            <label>Alasan Penolakan</label>
            <textarea name="catatan" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Nilai -->
<div class="modal fade" id="modalDetailNilai" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Detail Nilai Magang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="detailContent">Memuat data...</div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Peserta -->
<div class="modal fade" id="modalDetailPeserta" tabindex="-1" aria-labelledby="modalDetailPesertaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailPesertaLabel">Detail Peserta</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailPesertaContent">
                <p class="text-center text-muted">Memuat data peserta...</p>
            </div>
            <div class="modal-footer">
                <button id="btnEditMagang" class="btn btn-sm btn-warning d-none" title="Edit"><i class="fas fa-edit"></i></button>
                <button id="btnBatalkanMagang" class="btn btn-sm btn-danger d-none" title="Batalkan"><i class="fas fa-ban"></i></button>
                <button class="btn btn-secondary" data-dismiss="modal" title="Tutup"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Magang -->
<div class="modal fade" id="modalEditMagang" tabindex="-1" aria-labelledby="modalEditMagangLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditMagang" method="post" action="">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="modalEditMagangLabel">Edit Data Magang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body" id="editMagangContent">
          <p class="text-center text-muted">Memuat data...</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- 🔹 Modal Tunggal untuk Tambah / Kembalikan RFID -->
<div class="modal fade" id="rfidModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="rfidForm" method="post">
      <?= csrf_field(); ?>
      <input type="hidden" name="magang_id" id="magang_id">
      <input type="hidden" name="assignment_id" id="assignment_id">
      <input type="hidden" name="id_rfid" id="id_rfid">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rfidModalTitle"></h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>

        <div class="modal-body" id="rfidModalBody">
          <!-- Konten akan diganti dinamis via JS -->
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" id="rfidSubmitBtn" class="btn">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
flatpickr(".monthpicker", {
    plugins: [
        new monthSelectPlugin({
            shorthand: false,
            dateFormat: "Y-m",
            altFormat: "F Y",
            altInput: true
        })
    ]
});
</script>


<script>
document.querySelectorAll('.finalisasi-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // stop dulu submitnya

        Swal.fire({
            title: 'Finalisasi Magang?',
            text: "Setelah difinalisasi, data tidak bisa diubah lagi. Yakin ingin melanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Finalisasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // kirim form kalau setuju
            }
        });
    });
});
</script>
<!-- Old Pie Chart -->
<!-- <script>
    const baseUrl = "<?= base_url('admin/manage-magang'); ?>";
    const unitKerja = "<?= @$_GET['unit_kerja'] ?>";

    const chartData = <?= json_encode($chartData) ?>;

    function createPieChart(canvasId, tingkat) {
        const ctx = document.getElementById(canvasId);

        const labels = ["Aktif", "Akan Magang", "Belum Selesai", "Proses Daftar", "Sisa Kuota"];
        const values = [
            chartData[tingkat]['jumlah_aktif'],
            chartData[tingkat]['jumlah_akan_magang'],
            chartData[tingkat]['jumlah_belum_selesai'],
            chartData[tingkat]['jumlah_proses'],
            chartData[tingkat]['sisa_kuota']
        ];

        const filters = ["aktif", "akan_magang", "belum_selesai", "proses_daftar", "sisa_kuota"];

        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ["#28a745","#17a2b8","#343a40","#6c757d","#dc3545"],
                }]
            },
            options: {
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const parameter = filters[index];

                        window.location.href = `${baseUrl}?filter=${parameter}&tingkat=${tingkat}&unit_kerja=${unitKerja}`;
                    }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    createPieChart("chartPT", "Perguruan Tinggi");
    createPieChart("chartSMK", "SMK");
</script> -->
<!-- New Pie Chart -->
<script>
const makeRedirect = (filter, tingkat) => {
    const unit = "<?= $unitGet ?>";
    let url = "<?= base_url('admin/manage-magang') ?>";
    const params = [];
    if (filter) params.push("filter=" + filter);
    if (tingkat) params.push("tingkat=" + tingkat);
    if (unit) params.push("unit_kerja=" + unit);
    window.location.href = url + "?" + params.join("&");
};

// Data SMK
const dataSmk = {
    labels: ['Proses Daftar', 'Aktif Magang', 'Akan Magang', 'Belum Lulus'],
    values: [
        <?= $chartData['SMK']['proses'] ?>,
        <?= $chartData['SMK']['aktif'] ?>,
        <?= $chartData['SMK']['akan_masuk'] ?>,
        <?= $chartData['SMK']['belum_lulus'] ?>
    ],
    filters: ['proses', 'aktif', 'akan_magang', 'belum_selesai']
};

// Data PT
const dataPt = {
    labels: ['Proses Daftar', 'Aktif Magang', 'Akan Magang', 'Belum Lulus'],
    values: [
        <?= $chartData['Perguruan Tinggi']['proses'] ?>,
        <?= $chartData['Perguruan Tinggi']['aktif'] ?>,
        <?= $chartData['Perguruan Tinggi']['akan_masuk'] ?>,
        <?= $chartData['Perguruan Tinggi']['belum_lulus'] ?>
    ],
    filters: ['proses', 'aktif', 'akan_magang', 'belum_selesai']
};

// COLORS 
const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#5a5c69'];

function makePie(canvasId, data, tingkat) {

    const total = data.values.reduce((a, b) => a + b, 0);
    if (total === 0) {

        const canvas = document.getElementById(canvasId);

        // Ganti canvas dengan teks dalam card-body yang sama
        canvas.outerHTML = `
            <div class="d-flex justify-content-center align-items-center" 
                 style="height: 230px; color:#6c757d; font-size:18px;">
                Data tidak ada
            </div>
        `;

        return;
    }
    
    const ctx = document.getElementById(canvasId).getContext('2d');
    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: colors,
                hoverOffset: 8,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        // plugins: [ChartDataLabels],
        options: {
            responsive: true,
            cutout: "55%",
            maintainAspectRatio: false, 
            aspectRatio: 1.6,
            plugins: {
                // datalabels: {
                //     font: { weight: 'bold', size: 11 },
                //     color: '#6c757d',
                //     formatter: (value, ctx) => {
                //         return value; 
                //     }
                // },
                legend: { position: 'right' }
            },
            onClick: (e, item) => {
                if (item.length > 0) {
                    let index = item[0].index;
                    makeRedirect(data.filters[index], tingkat);
                }
            }
        }
    });
}

// Render chart
makePie('chartSmk', dataSmk, 'SMK');
makePie('chartPt', dataPt, 'Perguruan Tinggi');

</script>

<script>
document.querySelectorAll('.btn-toggle-upload').forEach(function(btn) {
    btn.addEventListener('click', function () {

        const url = this.dataset.url;
        const action = this.dataset.action;

        let title = '';
        let text = '';
        let confirmText = '';

        if (action === 'buka') {
            title = 'Buka Akses Upload?';
            text = 'Mahasiswa akan dapat mengupload ulang laporan & absensi.';
            confirmText = 'Ya, Buka';
        } else {
            title = 'Tutup Akses Upload?';
            text = 'Mahasiswa tidak akan bisa mengupload ulang laporan & absensi.';
            confirmText = 'Ya, Tutup';
        }

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });

    });
});
</script>


<!-- <script>
    document.addEventListener("DOMContentLoaded", () => {
    const ctxs = document.querySelectorAll("canvas[id^='chart_']");
    const filterData = document.querySelectorAll(".pie-filter");

    const labels = ["Proses Daftar", "Aktif", "Akan Magang", "Belum Lulus"];
    const colors = [
        "#4e73df", // primary
        "#1cc88a", // success
        "#36b9cc", // info
        "#5a5c69"  // dark
    ];

    ctxs.forEach((canvas, index) => {
        const row = filterData[index];

        const data = [
            row.dataset.proses,
            row.dataset.aktif,
            row.dataset.akan,
            row.dataset.belum
        ];

        const myChart = new Chart(canvas.getContext("2d"), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                cutout: "55%", // membuat jadi donat
                plugins: {
                    legend: {
                        display: true,
                        position: "bottom",
                        labels: { boxWidth: 15 }
                    }
                }
            }
        });

        // Event klik slice untuk filter
        canvas.onclick = evt => {
            const points = myChart.getElementsAtEventForMode(evt, "nearest", { intersect: true }, true);
            if (!points.length) return;
            const idx = points[0].index;
            const tipe = ["proses", "aktif", "akan", "belum"][idx];

            const url = row.dataset.url + "&tingkat=" + row.dataset.tingkat + "&filter=" + 
                (tipe === "proses" ? "proses" :
                tipe === "aktif" ? "aktif" :
                tipe === "akan" ? "akan_magang" :
                "belum_selesai");

            window.location.href = url;
        };
    });
});

</script> -->

</div>

<?= $this->endSection() ?>
