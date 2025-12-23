<?= $this->extend('admin/templates/index') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= esc($title) ?></h1>

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

    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
        <div class="card-body">
            <div class="table-responsive">
                <form id="formTerimaPendaftar" >
                    <button type="button" class="btn btn-primary mt-2" onclick="terimaBeberapa()">Terima yang Dipilih</button>
                    <button type="button" class="btn btn-danger mt-2 " onclick="bukaModalTolak()">Tolak yang Dipilih</button> <br><br>
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0" >
                        <thead class="thead-dark">
                            <tr>
                                <th colspan="11" style="text-align: left;">
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
                                <th>Rencana Mulai</th>
                                <th>Tanggal Daftar</th>
                                <th>Nama Pembimbing</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; foreach ($penelitian as $p): ?>
                            <tr>
                                <td rowspan="2">
                                    <input type="checkbox" name="pendaftar_ids[]" value="<?= $p->penelitian_id ?>" class="checkbox-pendaftar">
                                </td>
                                <td><?= $no++; ?></td>
                                <td><?= esc($p->fullname) ?></td>
                                <td><?= esc($p->nama_jurusan) ?></td>
                                <td><?= esc($p->judul_penelitian) ?></td>
                                <td><?= date('d M Y', strtotime($p->rencana_masuk)) ?></td>
                                <td><?= date('d M Y H:i', strtotime($p->tanggal_daftar)) ?></td>
                                <td><?= esc($p->dosen_pembimbing) ?></td>
                                <td>
                                    <?php if ($p->status_akhir == 'pendaftaran'): ?>
                                        <span class="badge badge-warning">Menunggu</span>
                                    <?php elseif ($p->status_akhir == 'diterima'): ?>
                                        <span class="badge badge-success">Diterima</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- <form action="/admin/penelitian/update-status/<?= $p->penelitian_id ?>" method="post" class="d-flex flex-column gap-1">
                                        <?= csrf_field() ?>
                                        <button type="submit" name="status" value="diterima" class="btn btn-success btn-sm mb-1">
                                            <i class="fas fa-check"></i> Terima
                                        </button>
                                        <button type="submit" name="status" value="ditolak" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form> -->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="10">
                                    <strong>Deskripsi:</strong> <?= esc($p->deskripsi) ?: '-' ?> <br>
                                    <strong>Keyword:</strong> <?= esc($p->keywords) ?: '-' ?>
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

<!-- Modal Terima Beberapa -->
<div class="modal fade" id="modalTerima" tabindex="-1" aria-labelledby="modalTerimaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTerimaLabel">Terima Pendaftar yang Dipilih</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="formTerimaFinal">
        <div class="modal-body">
          <div class="alert alert-info">
            <strong>Pendaftar yang dipilih:</strong>
            <ul id="listPendaftarTerpilih"></ul>
          </div>

          <div class="form-group">
            <label for="unit_id">Pilih Unit Penempatan</label>
            <select id="unit_id" name="unit_id" class="form-control select2" required>
              <option value="">-- Pilih Unit --</option>
              <?php foreach ($unitKerja as $u): ?>
                <option value="<?= $u['unit_id'] ?>"><?= esc($u['unit_kerja']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="tanggal_mulai">Tanggal Mulai Penelitian</label>
            <select class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
              <option value="">-- Pilih Tanggal --</option>
              <?php foreach ($pilihanTanggal as $tgl): ?>
                <option value="<?= $tgl ?>">
                  <?= date('d F Y', strtotime($tgl)) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Hidden input untuk menampung ID yang dipilih -->
          <input type="hidden" name="pendaftar_ids" id="pendaftar_ids">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Konfirmasi Terima</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Catatan Penolakan -->
<div class="modal fade" id="modalCatatanTolak" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Catatan Penolakan</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <textarea id="catatanTolak" class="form-control" rows="4" placeholder="Tulis alasan penolakan di sini..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" onclick="tolakBeberapa()">Konfirmasi Tolak</button>
      </div>
    </div>
  </div>
</div>

<script>
    // Fungsi terima banyak
function terimaBeberapa() {
  const checkboxes = document.querySelectorAll('.checkbox-pendaftar:checked');
  if (checkboxes.length === 0) {
    alert('Silakan pilih minimal satu pendaftar.');
    return;
  }

  // ambil data yang dipilih
  const selectedIds = [];
  const listContainer = document.getElementById('listPendaftarTerpilih');
  listContainer.innerHTML = '';

  checkboxes.forEach(cb => {
    selectedIds.push(cb.value);
    const row = cb.closest('tr');
    const nama = row.querySelector('td:nth-child(3)').textContent.trim();
    const jurusan = row.querySelector('td:nth-child(4)').textContent.trim();
    const rencanaMulai = row.querySelector('td:nth-child(6)').textContent.trim();
    listContainer.innerHTML += `<li><strong>${nama} (${jurusan})</strong> — Rencana mulai: ${rencanaMulai}</li>`;
  });

  // masukkan ID ke hidden input
  document.getElementById('pendaftar_ids').value = selectedIds.join(',');

  // tampilkan modal
  $('#modalTerima').modal('show');
}

document.getElementById('formTerimaFinal').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  fetch('<?= base_url('admin/penelitian/terima-banyak') ?>', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(res => {
    alert(res.message || 'Berhasil memproses.');
    $('#modalTerima').modal('hide');
    location.reload();
  })
  .catch(err => {
    console.error(err);
    alert('Terjadi kesalahan.');
  });
});


let alasanTolak = ''; // variabel global sementara

function bukaModalTolak() {
    // Periksa apakah ada yang dipilih dulu
    const form = document.getElementById('formTerimaPendaftar');
    if (!form) return alert('Form tidak ditemukan!');

    const formData = new FormData(form);
    const selected = formData.getAll('pendaftar_ids[]');

    if (selected.length === 0) {
        alert('Silakan pilih minimal satu pendaftar.');
        return;
    }

    // Jika ada yang dipilih, buka modal catatan
    $('#modalCatatanTolak').modal('show');
}

function tolakBeberapa() {
    setModeKuota('tolak');

    const form = document.getElementById('formTerimaPendaftar');
    const formData = new FormData(form);
    const selected = formData.getAll('pendaftar_ids[]');

    if (selected.length === 0) {
        alert('Silakan pilih minimal satu pendaftar.');
        return;
    }

    const catatan = document.getElementById('catatanTolak').value.trim();
    if (!catatan) {
        alert('Silakan isi alasan penolakan.');
        return;
    }

    formData.append('catatan', catatan);

    if (!confirm(`Yakin ingin menolak ${selected.length} pendaftar?`)) return;

    fetch('manage-seleksi/tolak-banyak', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        $('#modalCatatanTolak').modal('hide');
        $('#modalPendaftar').modal('hide');
        location.reload();
    });
}
</script>
<?= $this->endSection() ?>
