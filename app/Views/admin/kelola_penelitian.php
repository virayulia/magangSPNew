<?= $this->extend('admin/templates/index') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800"><?= esc($title) ?></h1>
  <div class="mb-2">
        <?php if (!empty($statusCount)): ?>

            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="<?= base_url('admin/manage-penelitian?filter=pendaftaran') ?>" class="text-decoration-none">
                            <div class="card border-left-primary shadow h-100 py-2 small-card hover-card">
                                <div class="card-body py-2 text-center">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Pendaftaran
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $statusCount['Pendaftaran'] ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= base_url('admin/manage-penelitian?filter=menunggu-approval') ?>" class="text-decoration-none">
                            <div class="card border-left-warning shadow h-100 py-2 small-card">
                                <div class="card-body py-2 text-center">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Menunggu Approval
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $statusCount['Menunggu Approval']?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= base_url('admin/manage-penelitian?filter=diterima') ?>" class="text-decoration-none">
                            <div class="card border-left-secondary shadow h-100 py-2 small-card">
                                <div class="card-body py-2 text-center">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                        Diterima
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $statusCount['Diterima']?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= base_url('admin/manage-penelitian?filter=terkonfirmasi') ?>" class="text-decoration-none">
                            <div class="card border-left-success shadow h-100 py-2 small-card hover-card">
                                <div class="card-body py-2 text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Terkonfirmasi
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $statusCount['Terkonfirmasi'] ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

        <?php endif; ?>
    </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="dataTable">
          <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Nama Mahasiswa</th>
                <!-- <th>Jurusan</th> -->
                <th>Judul Penelitian</th>
                <th>Deskripsi</th>
                <th>Tanggal Daftar</th>
                <th>Nama Pembimbing</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no=1; foreach ($penelitian as $p): ?>
              <tr>
                <td rowspan="2"><?= $no++ ?></td>
                <td><?= esc($p->fullname) ?></td>
                <!-- <td><?= esc($p->nama_jurusan) ?></td> -->
                <td><?= esc($p->judul_penelitian) ?></td>
                <td><?= esc($p->deskripsi) ?></td>
                <td><?= date('d M Y H:i', strtotime($p->tanggal_daftar)) ?></td>
                <td><?= esc($p->dosen_pembimbing) ?></td>
                <td>
                    <?php if ($p->status_seleksi == 'Ditolak'): ?>
                        <span class="badge bg-danger text-light">Ditolak Admin</span>

                    <?php elseif ($p->status_seleksi == 'Diterima'): ?>

                        <?php if (is_null($p->approve_unit)): ?>
                            <span class="badge bg-warning text-light">Menunggu Approval</span>

                        <?php elseif ($p->approve_unit == 'N'): ?>
                            <span class="badge bg-danger text-light">Tidak Diapprove</span><br>
                            <?php if (!empty($p->cttn_approve_unit)): ?>
                                <small class="text-muted">
                                    Cttn: <span class="badge bg-light text-dark"><?= esc($p->cttn_approve_unit) ?></span>
                                </small>
                            <?php endif; ?>

                        <?php elseif ($p->approve_unit == 'Y'): ?>

                            <?php if ($p->status_konfirmasi == 'Y'): ?>
                                <span class="badge bg-success text-light">Terkonfirmasi</span>
                            <?php else: ?>
                                <span class="badge bg-primary text-light">Diterima</span>
                            <?php endif; ?>

                        <?php endif; ?>


                    <?php else: ?>
                        <span class="badge bg-secondary text-light">Pendaftaran</span>
                    <?php endif; ?>
                </td>



                <td>
                  <div class="aksi-wrapper">
                    <button class="btn btn-sm btn-info btn-detail-penelitian" data-id="<?= $p->penelitian_id ?>" title="Detail" ><i class="fas fa-info-circle"></i></button>

                    <?php if($p->status_seleksi == NULL) :?>
                      <button class="btn btn-success btn-sm" 
                              onclick="bukaModalTerima(<?= $p->penelitian_id ?>)"
                              title="Terima">
                        <i class="fas fa-check"></i> 
                      </button>
                    <?php elseif($p->approve_unit == NULL) :?>
                        <button class="btn btn-secondary btn-sm" title="Menunggu Approval">
                        <i class="fas fa-hourglass-half"></i>
                      </button>
                    <?php elseif($p->approve_unit == 'N') :?>
                      <button class="btn btn-warning btn-sm" 
                              onclick="bukaModalTerima(<?= $p->penelitian_id ?>)">
                        <i class="fas fa-check"></i> Edit
                      </button>
                    <?php endif;?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal rekomendasi unit -->
<div class="modal fade" id="modalTerimaSatu" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTerimaSatuLabel">Terima Penelitian</h5>
        <button class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <form id="formTerimaSatu">
        <div class="modal-body">
          <div id="infoPenelitian"></div>
          <div class="form-group mt-3">
            <label>Rekomendasi Unit Berdasarkan Keyword</label>
            <select name="unit_id" id="unit_id" class="form-control select2" required>
            </select>
          </div>

          <div class="form-group">
            <label for="tanggal_mulai">Tanggal Mulai Penelitian</label>
            <select name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
              <option value="">-- Pilih Tanggal --</option>
              <?php foreach ($pilihanTanggal as $tgl): ?>
                <option value="<?= $tgl ?>"><?= format_tanggal_indonesia($tgl) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <input type="hidden" id="penelitian_id" name="penelitian_id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Konfirmasi Terima</button>
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
                <button id="btnEditPenelitian" class="btn btn-sm btn-warning d-none" title="Edit"><i class="fas fa-edit"></i></button>
                <button id="btnBatalkanPenelitian" class="btn btn-sm btn-danger d-none" title="Batalkan"><i class="fas fa-ban"></i></button>
                <button class="btn btn-secondary" data-dismiss="modal" title="Tutup"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Penelitian -->
<div class="modal fade" id="modalEditPenelitian" tabindex="-1" aria-labelledby="modalEditPenelitianLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditPenelitian" method="post" action="">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="modalEditPenelitianLabel">Edit Data Magang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body" id="editPenelitianContent">
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
function bukaModalTerima(id) {
  $('#modalTerimaSatu').modal('show');
  $('#penelitian_id').val(id);
  $('#unit_id').html('<option value="">-- Memuat rekomendasi unit... --</option>');

  $('#modalTerimaSatuLabel').text('Terima Penelitian');


  fetch('<?= base_url('admin/penelitian/get-rekomendasi-unit') ?>/' + id)
    .then(res => res.json())
    .then(res => {
      if (res.status === 'success') {
        $('#modalTerimaSatuLabel').text('Terima Penelitian - ' + res.penelitian.fullname);

        let options = '<option value="">-- Pilih Unit --</option>';
        res.units.forEach(u => {
  const keywords = u.matched_keywords ? ` - [${u.matched_keywords}]` : '';
  options += `
    <option value="${u.unit_id}">
      ${u.unit_kerja} (${u.match_count} keyword cocok)${keywords}
    </option>`;
});
        $('#unit_id').html(options);
        $('#infoPenelitian').html(`
          <strong>Rencana Masuk:</strong> ${res.penelitian.rencana_masuk_formatted} (${res.penelitian.durasi} bulan)<br>
          <strong>Jurusan:</strong> ${res.penelitian.nama_jurusan}<br>
          <strong>Judul:</strong> ${res.penelitian.judul_penelitian}<br>
          <strong>Keyword:</strong> ${res.penelitian.keywords || '-'}
        `);
      } else {
        $('#unit_id').html('<option value="">Tidak ada rekomendasi ditemukan.</option>');
      }
    })
    .catch(err => {
      console.error(err);
      $('#unit_id').html('<option value="">Gagal memuat rekomendasi.</option>');
    });
}


document.getElementById('formTerimaSatu').addEventListener('submit', function(e) {
  e.preventDefault(); // stop submit biasa

  Swal.fire({
    title: "Konfirmasi Penerimaan",
    text: "Apakah Anda yakin ingin menerima penelitian ini?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, Terima",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      // lanjut kirim jika dikonfirmasi
      const formData = new FormData(e.target);

      fetch('<?= base_url('admin/penelitian/terima-satu') ?>', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        Swal.fire({
          icon: res.status ? 'success' : 'error',
          title: res.message,
          timer: 2000,
          showConfirmButton: false
        });

        if (res.status) {
          $('#modalTerimaSatu').modal('hide');
          setTimeout(() => location.reload(), 1500);
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire({
          icon: 'error',
          title: 'Terjadi kesalahan',
          text: 'Gagal memproses data.',
        });
      });
    }
  });
});

</script>

<?= $this->endSection() ?>
