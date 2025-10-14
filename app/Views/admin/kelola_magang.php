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
                    <select name="bulan_masuk" class="form-control">
                        <option value="">-- Pilih Bulan Masuk --</option>
                        <?php 
                        $bulanList = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        foreach ($bulanList as $key => $nama): ?>
                            <option value="<?= $key ?>" <?= ($key == @$_GET['bulan_masuk']) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="bulan_keluar" class="form-control">
                        <option value="">-- Pilih Bulan Keluar --</option>
                        <?php foreach ($bulanList as $key => $nama): ?>
                            <option value="<?= $key ?>" <?= ($key == @$_GET['bulan_keluar']) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="tahun" class="form-control">
                        <option value="">-- Pilih Tahun --</option>
                        <?php
                        $tahunSekarang = date('Y');
                        for ($i = $tahunSekarang - 2; $i <= $tahunSekarang+1; $i++): ?>
                            <option value="<?= $i ?>" <?= ($i == (@$_GET['tahun'] ?: $tahunSekarang)) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <a href="<?= base_url('admin/export-peserta?bulan_masuk='.service('request')->getGet('bulan_masuk').'&bulan_keluar='.service('request')->getGet('bulan_keluar').'&tahun='.service('request')->getGet('tahun')) ?>" 
                class="btn btn-success mb-3">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>

            <table class="table table-bordered table-striped" width="100%" cellspacing="0" id="dataTable" >
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
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
                            <td><?= esc($item['nisn_nim']) ?></td>
                            <td><?= esc($item['unit_kerja']) ?></td>
                            <td><?= format_tanggal_indonesia($item['tanggal_masuk']) ?></td>
                            <td><?= format_tanggal_indonesia($item['tanggal_selesai']) ?></td>   
                            <td><?= esc($item['rfid_no']) ?: '-' ?></td>
                            <td><?php if(!empty($item['status_berkas_lengkap']) && $item['status_berkas_lengkap'] === 'Y'): ?>
                                    <span class="badge bg-success text-light">Valid</span>
                                                                
                                <?php else: ?>
                                <a href="<?= base_url('admin/manage-kelengkapan-berkas/' . $item['magang_id']) ?>">
                                <span class="badge bg-danger text-light">Tidak Valid</span>
                                </a>
                                <?php endif; ?>
                                
                            </td>
                            <td><?php if(!empty($item['tanggal_setujui_pernyataan'])): ?>
                                <span class="badge bg-success text-light">Disetujui</span>
                                <?php else: ?>
                                <span class="badge bg-danger text-light">Belum Setuju</span>
                                <?php endif; ?>
                            </td>  
                            <td>
                                <?php
                                    $badgeClass = 'bg-danger text-light'; 
                                    if ($item['status_tes'] === 'Lulus') {
                                        $badgeClass = 'bg-success text-light'; 
                                    } elseif ($item['status_tes'] === 'Belum Lulus') {
                                        $badgeClass = 'bg-warning text-light'; 
                                    }
                                ?>

                                <?php if(!empty($item['nilai_maksimal'])): ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $item['nilai_maksimal'] ?></span>
                                <?php else : ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= $item['status_tes'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($item['nama_pembimbing']) ?></td>                          
                            <td>
                                <?php if (!empty($item['laporan'])): ?>
                                    <a href="<?= base_url('uploads/laporan/' . $item['laporan']) ?>" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-tolak-laporan" 
                                        data-id="<?= $item['magang_id'] ?>" 
                                        data-nama="<?= $item['fullname'] ?>"
                                        title="Tolak Laporan">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($item['absensi'])): ?>
                                    <a href="<?= base_url('uploads/absensi/' . $item['absensi']) ?>" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-tolak-absensi" 
                                        title="Tolak Absensi"
                                        data-id="<?= $item['magang_id'] ?>" 
                                        data-nama="<?= $item['fullname'] ?>">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
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
                                <!-- Tombol detail nilai -->
                                <button type="button" 
                                        class="btn btn-sm btn-info mt-1 btn-detail-nilai" 
                                        title="Klik Untuk Lihat Detail"
                                        data-id="<?= $item['magang_id'] ?>" >
                                    <strong><?= $rata ?></strong>
                                </button>
                            </td>  
                            <td><?php if($item['status_rfid'] === 'returned'): ?>
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
                            <td>
                                <?php
                                $hariTerakhir = date('Y-m-d') >= $item['tanggal_selesai']; 
                                $laporanAda   = !empty($item['laporan']);
                                $absensiAda   = !empty($item['absensi']);
                                $nilaiAda     = ($rata > 0);
                                $rfidOk       = empty($item['status_rfid']) || in_array($item['status_rfid'], ['returned','lost']);
                                $feedbackAda  = !empty($item['feedback_id']);
                                ?>

                                <?php if ($hariTerakhir && $laporanAda && $absensiAda && $nilaiAda && $rfidOk && $feedbackAda ): ?>
                                    <?php if ($item['finalisasi'] == null): ?>
                                        <form action="<?= base_url('admin/finalisasi/'.$item['magang_id']) ?>" 
                                            method="post" class="finalisasi-form" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success btn-sm finalisasi-btn">
                                                <i class="fas fa-check-circle"></i> Finalisasi
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled title="Menunggu Approve Pusdiklat">
                                            <i class="fas fa-hourglass-half"></i> Menunggu Approve
                                        </button>
                                    <?php endif; ?>

                                <?php endif; ?>                                
                                <button class="btn btn-sm btn-primary btn-detail-peserta" data-id="<?= $item['magang_id'] ?>" >Detail</button>
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
                                      >
                                          Kembalikan RFID
                                      </button>
                                  <?php else: ?>
                                      <!-- Tombol Tambah RFID -->
                                      <button 
                                          class="btn btn-sm btn-success btn-rfid"
                                          data-type="add"
                                          data-magang-id="<?= $item['magang_id'] ?>"
                                      >
                                          Tambah RFID
                                      </button>
                                  <?php endif; ?>

                                

                            </td>
                                                    
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
            <textarea name="alasan" class="form-control" required></textarea>
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
            <textarea name="alasan" class="form-control" required></textarea>
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
                <button id="btnEditMagang" class="btn btn-sm btn-warning d-none">Edit</button>
                <button id="btnBatalkanMagang" class="btn btn-sm btn-danger d-none">Batalkan</button>
                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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



</div>

<?= $this->endSection() ?>
