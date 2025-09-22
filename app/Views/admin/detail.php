<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Pendaftaran</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Nama</th>
                    <td><?= esc($pendaftaran['fullname']); ?></td>
                </tr>
                <tr>
                    <th>No HP</th>
                    <td><?= esc($pendaftaran['no_hp']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= esc($pendaftaran['email']); ?></td>
                </tr>
                <tr>
                    <th>Alamat KTP</th>
                    <td>
                    <?php
                        $alamat = $pendaftaran['alamat'] ?? '';
                        $kota = trim(($pendaftaran['tipe_kota_ktp'] ?? '') . ' ' . ($pendaftaran['kota_ktp'] ?? ''));
                        $prov = $pendaftaran['provinsi_ktp'] ?? '';
                        $parts = array_filter([$alamat, $kota, $prov]);
                        echo esc(implode(', ', $parts)) ?: 'Data belum diisi';
                    ?>
                    </td>
                </tr>
                <tr>
                    <th>Alamat Domisili</th>
                    <td>
                      <?php
                          $alamat = $pendaftaran['domisili'] ?? '';
                          $kota = trim(($pendaftaran['tipe_kota_domisili'] ?? '') . ' ' . ($pendaftaran['kota_domisili'] ?? ''));
                          $prov = $pendaftaran['provinsi_domisili'] ?? '';
                          $parts = array_filter([$alamat, $kota, $prov]);
                          echo esc(implode(', ', $parts)) ?: 'Data belum diisi';
                      ?>
                    </td>
                </tr>
                <tr>
                    <th>NIM</th>
                    <td><?= esc($pendaftaran['nisn_nim']); ?></td>
                </tr>
                <?php if($pendaftaran['tingkat_pendidikan'] === 'SMK'): ?>
                  <tr>
                      <th>Sekolah</th>
                      <td><?= esc($pendaftaran['nama_instansi']); ?></td>
                  </tr>
                <?php else: ?>
                  <tr>
                      <th>Perguruan Tinggi</th>
                      <td><?= esc($pendaftaran['nama_instansi']); ?></td>
                  </tr>
                <?php endif; ?>
                <tr>
                    <th>Jurusan</th>
                    <td><?= esc($pendaftaran['nama_jurusan']); ?></td>
                </tr>
                <?php if($pendaftaran['tingkat_pendidikan'] === 'SMK'): ?>
                  <tr>
                      <th>Kelas</th>
                      <td>Kelas <?= esc($pendaftaran['semester']); ?></td>
                  </tr>
                <?php else: ?>
                  <tr>
                      <th>Semester</th>
                      <td>Semester <?= esc($pendaftaran['semester']); ?></td>
                  </tr>
                <?php endif; ?>
                <tr>
                    <th>Tanggal Daftar</th>
                    <td><?= date('d-m-Y, H:i', strtotime($pendaftaran['tanggal_daftar'])); ?></td>
                </tr>
                <tr>
                    <th>Lama Magang</th>
                    <td><?= esc($pendaftaran['durasi']); ?> hari</td>
                </tr>
                <tr>
                    <th>CV</th>
                    <td><a href="<?= base_url('uploads/cv/' . $pendaftaran['cv']); ?>" target="_blank">Lihat CV</a></td>
                </tr>
                <tr>
                    <th>Proposal</th>
                    <td><a href="<?= base_url('uploads/proposal/' . $pendaftaran['proposal']); ?>" target="_blank">Lihat Proposal</a></td>
                </tr>
                <tr>
                    <th>Surat Permohonan</th>
                    <td><a href="<?= base_url('uploads/surat-permohonan/' . $pendaftaran['surat_permohonan']); ?>" target="_blank">Lihat Surat Permohonan</a></td>
                </tr>
            </table>
            <div class="mt-4">
              <a href="<?= base_url('admin/manage-pendaftaran'); ?>" class="btn btn-secondary">Kembali</a>
            </div>

            <!-- <div class="mt-4">
                <button class="btn btn-success" data-toggle="modal" data-target="#approveModal">Valid</button>
                <button class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">Tidak Valid</button>
            </div> -->
        </div>
    </div>
</div>

<!-- Modal Setujui -->
<!-- <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="<?= base_url('manage-pendaftaran/approve/' . $pendaftaran['id']); ?>" method="post">
      <?= csrf_field(); ?>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="approveModalLabel">Berkas Pendaftaran Valid?</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Setujui</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div> -->

<!-- Modal Tolak -->
<!-- <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="<?= base_url('manage-pendaftaran/reject/' . $pendaftaran['id']); ?>" method="post">
      <?= csrf_field(); ?>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rejectModalLabel">Tolak Pendaftaran</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="alasan">Alasan Penolakan</label>
            <textarea class="form-control" id="alasan" name="alasan" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Tolak</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div> -->

<?= $this->endSection(); ?>
