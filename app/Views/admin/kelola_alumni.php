<?= $this->extend('admin/templates/index');?>

<?= $this->section('content');?>
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

<h1 class="h3 mb-2 text-gray-800">Daftar Alumni Magang</h1>
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
                        for ($i = 2025; $i <= $tahunSekarang + 2; $i++): ?>
                            <option value="<?= $i ?>" <?= ($i == @$_GET['tahun']) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <a href="<?= base_url('admin/export-alumni?bulan_masuk='.service('request')->getGet('bulan_masuk').'&bulan_keluar='.service('request')->getGet('bulan_keluar').'&tahun='.service('request')->getGet('tahun')) ?>" 
                class="btn btn-success mb-3">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>

            <table class="table table-bordered table-striped" width="100%" cellspacing="0" id="dataTable">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Unit Kerja</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Tanggal Approve</th>
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
                            <td><?= format_tanggal_indonesia($item['tanggal_approve']) ?></td>                                       
                            <td>    
                                <div class="aksi-wrapper">  
                                    <button class="btn btn-sm btn-info btn-detail-peserta" data-id="<?= $item['magang_id'] ?>" title="Detail"><i class="fas fa-eye"></i></button>                         
                                    <!-- <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal<?= $item['magang_id'] ?>" title="Detail"><i class="fas fa-eye"></i></button> -->
                                    <a href="<?= base_url('admin/cetak-sertifikat/' . $item['magang_id']) ?>" 
                                        target="_blank" 
                                        class="btn btn-success btn-sm" 
                                        title="Sertifikat">
                                            <i class="fas fa-file-pdf"></i> 
                                    </a>
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


</div>

<?= $this->endSection() ?>
