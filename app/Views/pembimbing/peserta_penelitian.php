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

<h1 class="h3 mb-2 text-gray-800">Peserta Penelitian 
                    <?php if (!empty($unitPembimbing)): ?>
                        - 
                        <?= implode(', ', array_column($unitPembimbing, 'unit_kerja')) ?>
                    <?php endif; ?></h1>
<!-- Card Tabel -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0" id="dataTable" >
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Jurusan</th>
                        <th>Instansi</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Pembimbing</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Form Ambil Data</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php $no = 1; foreach ($data as $item): ?>
                            <td><?= $no++; ?></td>
                            <td><?= esc($item['nama_peserta']) ?></td>
                            <td><?= esc($item['nisn_nim']) ?></td>
                            <td><?= esc($item['nama_jurusan']) ?></td>
                            <td><?= esc($item['nama_instansi']) ?></td>
                            <td><?= esc($item['judul_penelitian']) ?></td> 
                            <td><?= esc($item['deskripsi']) ?></td>
                            <td>
                                <?php if (empty($item['nama_pembimbing'])): ?>
                                    Belum Ada
                                    <!-- Tombol tambah -->
                                    <button class="btn btn-sm btn-success ml-2" data-toggle="modal" data-target="#tambahPembimbingModal<?= $item['penelitian_id'] ?>">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <?= esc($item['nama_pembimbing']) ?>
                                    <?php if($userLogin['eselon'] == 2):?>
                                    <!-- Tombol edit -->
                                    <a href="#" class="text-primary ml-2" data-toggle="modal" title="Ganti Pembimbing" data-target="#editPembimbingModal<?= $item['penelitian_id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= format_tanggal_indonesia($item['tanggal_masuk']) ?></td>
                            <td><?= format_tanggal_indonesia($item['tanggal_selesai']) ?></td>
                            <td>
                                <?php if (!empty($item['formulir_penelitian'])): ?>
                                    <a href="<?= base_url('uploads/formPenelitian/' . $item['formulir_penelitian']) ?>" target="_blank">
                                        Lihat Form
                                    </a>
                                    <?php if ($item['status_pembimbing'] == NULL): ?>
                                        <button class="btn btn-sm btn-success btn-approve" data-id="<?= $item['penelitian_id'] ?>">
                                            <i class="fas fa-check"></i> Terima
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-reject" data-id="<?= $item['penelitian_id'] ?>">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>

                                    <?php elseif ($item['status_pembimbing'] == 'Disetujui'): ?>
                                        <span class="badge bg-success text-light">Disetujui</span>

                                    <?php elseif ($item['status_pembimbing'] == 'Ditolak'): ?>
                                        <span class="badge bg-danger text-light">Ditolak</span>
                                        <?php if (!empty($item['catatan_pembimbing'])): ?>
                                            <br><small><b>Catatan:</b> <?= esc($item['catatan_pembimbing']); ?></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada</span>
                                <?php endif; ?>
                            </td>                                   
                            <td>       
                            <button class="btn btn-sm btn-primary btn-detail-peserta" data-id="<?= $item['penelitian_id'] ?>" >Detail</button>
                                  
                            </td>
                                                    
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($data)): ?>
    <?php foreach ($data as $item): ?>
        <!-- Modal Tambah Pembimbing -->
        <div class="modal fade" id="tambahPembimbingModal<?= $item['penelitian_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?= $item['penelitian_id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <form action="<?= base_url('pembimbing/assignPembimbingPenelitian/'.$item['penelitian_id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel<?= $item['penelitian_id'] ?>">Pilih Pembimbing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                    <div class="form-group">
                        <label for="pembimbing_id<?= $item['penelitian_id'] ?>">Pembimbing</label>
                        <select name="pembimbing_id" id="pembimbing_id<?= $item['penelitian_id'] ?>" class="form-control select2" required>
                        <option value="" disabled selected>Pilih Pembimbing</option>
                        <?php foreach ($pembimbing as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['fullname']) ?> (<?= esc($p['email']) ?>)</option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Modal Edit Pembimbing -->
        <div class="modal fade" id="editPembimbingModal<?= $item['penelitian_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $item['penelitian_id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <form action="<?= base_url('pembimbing/updatePembimbingPenelitian/'.$item['penelitian_id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel<?= $item['penelitian_id'] ?>">Edit Pembimbing</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_pembimbing_id<?= $item['penelitian_id'] ?>">Pembimbing</label>
                            <select name="pembimbing_id" id="edit_pembimbing_id<?= $item['penelitian_id'] ?>" class="form-control select2" required>
                                <option value="" disabled>Pilih Pembimbing</option>
                                <?php foreach ($pembimbing as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($item['pembimbing_id'] == $p['id']) ? 'selected' : '' ?>>
                                        <?= esc($p['fullname']) ?> (<?= esc($p['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<!-- Modal Tolak Formulir -->
<div class="modal fade" id="modalTolakPembimbing" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formTolakPembimbing">
        <div class="modal-header">
          <h5 class="modal-title">Tolak Form Penelitian</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="penelitian_id_tolak" name="penelitian_id">
          <div class="form-group">
            <label>Catatan Penolakan</label>
            <textarea name="catatan" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Tolak</button>
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
          <h5 class="modal-title" id="tolakLaporanLabel">Tolak Formulir Penelitian</h5>
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

</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
    // klik tombol terima
    $('.btn-approve').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Setujui Form Ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Terima',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $.post("<?= base_url('pembimbing/approve-formulir') ?>", { id: id }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }, 'json');
            }
        });
    });

    // klik tombol tolak
    $('.btn-reject').on('click', function() {
        $('#penelitian_id_tolak').val($(this).data('id'));
        $('#modalTolakPembimbing').modal('show');
    });

    // submit form tolak
    $('#formTolakPembimbing').on('submit', function(e) {
        e.preventDefault();
        $.post("<?= base_url('pembimbing/reject-formulir') ?>", $(this).serialize(), function(res) {
            $('#modalTolakPembimbing').modal('hide');
            Swal.fire('Terkirim!', res.message, 'success').then(() => location.reload());
        }, 'json');
    });
});

</script>
<?= $this->endSection() ?>