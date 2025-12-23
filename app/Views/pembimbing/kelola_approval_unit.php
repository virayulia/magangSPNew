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
      <div class="table-responsive">
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

          <table class="table table-bordered table-striped" id="dataTable">
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
                <th>Judul Penelitian</th>
                <th>Deskripsi</th>
                <th>Keyword</th>
                <th>Unit Kerja</th>
                <th>Tanggal Daftar</th>
                <th>Nama Pembimbing</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php $no=1; foreach ($penelitian as $p): ?>
                <tr>
                  <td><input type="checkbox" name="penelitian_ids[]" value="<?= $p->penelitian_id ?>" class="checkbox-pendaftar"></td>
                  <td><?= $no++ ?></td>
                  <td><?= esc($p->fullname) ?></td>
                  <td><?= esc($p->nama_jurusan) ?></td>
                  <td><?= esc($p->judul_penelitian) ?></td>
                  <td><?= esc($p->deskripsi) ?></td>
                  <td><?= esc($p->keywords) ?></td>
                  <td><?= esc($p->unit_kerja) ?></td>
                  <td><?= date('d M Y H:i', strtotime($p->tanggal_daftar)) ?></td>
                  <td><?= esc($p->dosen_pembimbing) ?></td>
                  <td>
                      <?php if ($p->status_akhir == 'pendaftaran'): ?>
                          <span class="btn btn-sm btn-warning text-light"><i class="fas fa-hourglass-half" title="Menunggu"></i></span>
                      <?php elseif ($p->status_akhir == 'proses'): ?>
                          <?php if ($p->status_verifikasi == 'Y' && ($p->status_konfirmasi == NULL || $p->status_konfirmasi == '')): ?>
                              <span class="btn btn-sm btn-primary text-light"><i class="fas fa-check-circle" title="Diterima"></i> </span>
                          <?php elseif ($p->status_konfirmasi == 'Y'): ?>
                              <span class="btn btn-sm btn-success text-light"><i class="fas fa-user-check" title="Terkonfirmasi"></i> </span>
                          <?php else: ?>
                              <span class="btn btn-sm btn-secondary text-light"><i class="fas fa-spinner fa-spin" title="Dalam Proses"></i> </span>
                          <?php endif; ?>
                      <?php endif; ?>
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
<?= $this->endSection() ?>
