<?= $this->extend('admin/templates/index') ?>
<?= $this->section('content') ?>

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

  <h1 class="h3 mb-4 text-gray-800"><?= esc($title) ?></h1>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive-md">
        <form action="<?= base_url('pembimbing/approve-unit-penelitian/save') ?>" method="post" id="bulkForm">
          <input type="hidden" name="status" id="bulkStatus">
          <div class="mb-3">
              <div class="mb-3">
                <button type="button" class="btn btn-success btn-sm" id="btnApprove">
                  ✔️ Approve Terpilih
                </button>

                <button type="button" class="btn btn-danger btn-sm" id="btnReject">
                  ❌ Reject Terpilih
                </button>
              </div>
          </div>

          <table class="table table-bordered table-striped w-100" id="dataTable">
            <thead class="thead-dark">
              <tr>
                <th colspan="11">
                  <label>
                      <input type="checkbox" id="selectAll"> Pilih Semua
                  </label>
                </th>
              </tr>
              <tr>
                <th>Pilih</th>
                <th>No</th>
                <th>Nama Mahasiswa</th>
                <th>Jurusan</th>
                <th>Penelitian</th>
                <th>Unit Kerja</th>
                <th>Tanggal Daftar</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no=1; foreach ($penelitian as $p): ?>
                <tr>
                  <td><input type="checkbox" name="penelitian_ids[]" value="<?= $p->penelitian_id ?>" class="checkbox-pendaftar"></td>
                  <td><?= $no++ ?></td>
                  <td><?= esc($p->fullname) ?></td>
                  <td><?= esc($p->nama_jurusan) ?></td>
                  <td><?php if(!empty($p->judul_penelitian)):?>
                        <button 
                            type="button"
                            title="Detail Penelitian"
                            class="btn btn-sm btn-info btn-view-penelitian"
                            data-penelitian="<?= esc($p->judul_penelitian); ?>"
                            data-deskripsi="<?= esc($p->deskripsi); ?>"
                            data-judul="Detail Penelitian"
                            data-keyword="<?= esc($p->keywords); ?>"
                            data-dosen="<?= esc($p->dosen_pembimbing); ?>"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td><?= esc($p->unit_kerja) ?></td>
                  <td><?= date('d-m-Y, H:i', strtotime($p->tanggal_daftar)) ?></td>
                  <td>
                      <?php if ($p->status_akhir == 'pendaftaran'): ?>
                          <span class="btn btn-sm btn-warning text-light"><i class="fas fa-hourglass-half" title="Menunggu"></i></span>
                      <?php elseif ($p->status_akhir == 'proses'): ?>
                          <?php if ($p->status_seleksi == 'Y' && ($p->status_konfirmasi == NULL || $p->status_konfirmasi == '')): ?>
                              <span class="btn btn-sm btn-primary text-light"><i class="fas fa-check-circle" title="Diterima"></i> </span>
                          <?php elseif ($p->status_konfirmasi == 'Y'): ?>
                              <span class="btn btn-sm btn-success text-light"><i class="fas fa-user-check" title="Terkonfirmasi"></i> </span>
                          <?php else: ?>
                              <span class="btn btn-sm btn-secondary text-light"><i class="fas fa-spinner fa-spin" title="Dalam Proses"></i> </span>
                          <?php endif; ?>
                      <?php endif; ?>
                  </td>
                  <td>
                      <button type="button" class="btn btn-sm btn-info btn-detail-penelitian-pembimbing" data-id="<?= $p->penelitian_id ?>" title="Detail"><i class="fas fa-eye"></i></button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </form>
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
                <!-- <button id="btnEditPenelitian" class="btn btn-sm btn-warning d-none" title="Edit"><i class="fas fa-edit"></i></button>
                <button id="btnBatalkanPenelitian" class="btn btn-sm btn-danger d-none"  title="Batalkan"><i class="fas fa-ban"></i></button> -->
                <button class="btn btn-secondary" data-dismiss="modal"  title="Tutup"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bulk Reject -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Catatan Penolakan</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <form action="<?= base_url('pembimbing/approve-unit-penelitian/save') ?>" method="post" id="rejectForm">
        <div class="modal-body">
          <input type="hidden" name="penelitian_ids[]" id="rejectIds">
          <div class="form-group">
              <label>Alasan / Catatan</label>
              <textarea name="catatan_reject" id="catatan_reject" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="status" value="reject" id="btnRejectSubmit" class="btn btn-danger">Kirim</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Penelitian -->
<div class="modal fade" id="modalPenelitian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalJudul">
                    Detail Penelitian
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Info -->
                <div class="mb-3">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th width="25%">Judul Penelitian</th>
                            <td id="modalJudulP"></td>
                        </tr>
                        <tr>
                            <th>Keywords</th>
                            <td id="modalKeywords"></td>
                        </tr>
                        <tr>
                            <th>Dosen Pembimbing</th>
                            <td id="modalDosen"></td>
                        </tr>
                    </table>
                </div>

                <hr class="mt-1">

                <div class="p-3 bg-light rounded">
                    <label for="modalDeskripsi"><strong>Deskripsi:</strong></label>
                    <p id="modalDeskripsi" class="mb-0 text-justify"></p>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const selectAll = document.getElementById('selectAll');
  const checkboxes = document.querySelectorAll('.checkbox-pendaftar');
  const btnApprove = document.getElementById('btnApprove');
  const btnReject = document.getElementById('btnReject');
  const bulkForm = document.getElementById('bulkForm');
  const bulkStatus = document.getElementById('bulkStatus');

  // PILIH SEMUA CHECKBOX
  selectAll.addEventListener('click', function() {
    checkboxes.forEach(cb => cb.checked = this.checked);
  });

  // APPROVE dengan konfirmasi
  btnApprove.addEventListener('click', function(e) {
    e.preventDefault();
    const selected = Array.from(checkboxes).filter(cb => cb.checked);
    if (selected.length === 0) {
      Swal.fire({icon:'warning', title:'Tidak ada data', text:'Pilih minimal satu data untuk approve'});
      return;
    }

    Swal.fire({
      title: 'Yakin menyetujui?',
      text: `Sebanyak ${selected.length} penelitian akan di-approve`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Setujui',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        // tambahkan input penelitian_ids[]
        selected.forEach(cb => {
          let input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'penelitian_ids[]';
          input.value = cb.value;
          bulkForm.appendChild(input);
        });

        bulkStatus.value = 'approve';
        bulkForm.submit();
      }
    });
  });

  // REJECT → buka modal
  btnReject.addEventListener('click', function() {
    const selected = Array.from(checkboxes).filter(cb => cb.checked);
    if (selected.length === 0) {
      Swal.fire({icon:'warning', title:'Tidak ada data', text:'Pilih minimal satu data untuk reject'});
      return;
    }

    let ids = selected.map(cb => cb.value);
    document.getElementById('rejectIds').value = ids.join(',');
    $('#bulkRejectModal').modal('show');
  });

  // kirim modal reject → ubah rejectIds jadi penelitian_ids[]
  document.getElementById('rejectForm').addEventListener('submit', function() {
    let ids = document.getElementById('rejectIds').value.split(',');
    ids.forEach(id => {
      let input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'penelitian_ids[]';
      input.value = id;
      this.appendChild(input);
    });
  });

});
</script>
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-view-penelitian');
    if (!btn) return;

    e.preventDefault(); 

    document.getElementById('modalJudul').innerText     = btn.dataset.judul;
    document.getElementById('modalJudulP').innerText    = btn.dataset.penelitian;
    document.getElementById('modalDeskripsi').innerText = btn.dataset.deskripsi;
    document.getElementById('modalKeywords').innerText  = btn.dataset.keyword;
    document.getElementById('modalDosen').innerText  = btn.dataset.dosen;

    $('#modalPenelitian').modal('show');
});
</script>

<?= $this->endSection() ?>
