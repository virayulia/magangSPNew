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
    <h1 class="h3 mb-4 text-gray-800">Approve Nilai
                    <?php if (!empty($unitPembimbing)): ?>
                        - 
                        <?= implode(', ', array_column($unitPembimbing, 'unit_kerja')) ?>
                    <?php endif; ?>
    </h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIM/NISN</th>
            <th>Instansi</th>
            <th>Tgl Mulai</th>
            <th>Tgl Selesai</th>
            <th>Status Approve</th>
            <th>Rata-rata</th>
            <th>Catatan</th>
            <th>Catatan Approval</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($peserta)): ?>
            <tr>
                <td colspan="10" class="text-center">Tidak ada peserta yang perlu diapprove</td>
            </tr>
        <?php else: ?>
            <?php foreach ($peserta as $i => $item): ?>
                <?php if ($item['penilaian_id']): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($item['nama_peserta']) ?></td>
                        <td><?= esc($item['nisn_nim']) ?></td>
                        <td><?= esc($item['nama_instansi']) ?></td>
                        <td><?= date('d M Y', strtotime($item['tanggal_masuk'])) ?></td>
                        <td><?= date('d M Y', strtotime($item['tanggal_selesai'])) ?></td>
                        <td>
                            <?php if ($item['approve_kaunit']): ?>
                                <span class="badge bg-primary text-white">Sudah Disetujui</span>
                            <?php else: ?>
                                <span class="badge bg-secondary text-white">Belum Disetujui</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php 
                                $total = $item['nilai_disiplin'] + $item['nilai_kerajinan'] + $item['nilai_tingkahlaku'] +
                                        $item['nilai_kerjasama'] + $item['nilai_kreativitas'] + $item['nilai_kemampuankerja'] +
                                        $item['nilai_tanggungjawab'] + $item['nilai_penyerapan'];
                                $rata = round($total / 8, 2);
                            ?>
                            <strong><?= $rata ?></strong><br>
                            <!-- Tombol detail nilai -->
                            <button type="button" 
                                    class="btn btn-sm btn-info mt-1" 
                                    data-toggle="modal" 
                                    data-target="#modalDetail-<?= $item['magang_id'] ?>">
                                Detail Nilai
                            </button>
                        </td>

                        <td><?= esc($item['catatan']) ?></td>
                        <td><?= esc($item['catatan_approval']) ?></td>
                        <td>
                            <?php if (!$item['approve_kaunit']): ?>
                                <!-- Tombol Approve -->
                                <form action="<?= base_url('pembimbing/approve/save') ?>" method="post" style="display:inline;">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="magang_id" value="<?= $item['magang_id'] ?>">
                                    <input type="hidden" name="status" value="approve">
                                    <button type="submit" 
                                            class="btn btn-sm btn-success"
                                            onclick="return confirm('Yakin ingin menyetujui penilaian ini?')">
                                        ✔️ Approve
                                    </button>
                                </form>

                                <!-- Tombol Tidak Approve (panggil modal) -->
                                <button type="button" 
                                        class="btn btn-sm btn-danger"
                                        data-toggle="modal" 
                                        data-target="#rejectModal-<?= $item['magang_id'] ?>">
                                    ❌ Tidak Approve
                                </button>

                                <!-- Modal Catatan Penolakan -->
                                <div class="modal fade" id="rejectModal-<?= $item['magang_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Catatan Penolakan</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form action="<?= base_url('pembimbing/approve/save') ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="magang_id" value="<?= $item['magang_id'] ?>">
                                                <input type="hidden" name="status" value="reject">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Alasan / Catatan</label>
                                                        <textarea name="catatan_reject" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger">Kirim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-success text-light">Sudah diapprove</span>
                            <?php endif; ?>
                        </td>


                    </tr>
                    <!-- Modal detail -->
                    <div class="modal fade" id="modalDetail-<?= $item['magang_id'] ?>" tabindex="-1" aria-labelledby="modalDetailLabel-<?= $item['magang_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg"><!-- modal-lg biar lebar -->
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="modalDetailLabel-<?= $item['magang_id'] ?>">
                                        Detail Penilaian: <?= esc($item['nama_peserta']) ?>
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <?php 
                                        $fields = [
                                            'disiplin' => 'Disiplin',
                                            'kerajinan' => 'Kerajinan',
                                            'tingkahlaku' => 'Tingkah Laku',
                                            'kerjasama' => 'Kerja Sama',
                                            'kreativitas' => 'Kreativitas',
                                            'kemampuankerja' => 'Kemampuan Kerja',
                                            'tanggungjawab' => 'Tanggung Jawab',
                                            'penyerapan' => 'Penyerapan'
                                        ];
                                        foreach ($fields as $name => $label): 
                                            $nilai = (int) $item['nilai_' . $name];
                                            $stars = ($nilai - 50) / 10; // 60=1, 70=2, ... 100=5
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

                <?php endif; ?>

            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>


            </div>

        </div>
    </div>
</div>


<?= $this->endSection() ?>