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
    <h1 class="h3 mb-4 text-gray-800">Penilaian</h1>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peserta)): ?>
                            <tr><td colspan="8" class="text-center">Tidak ada peserta yang sudah dinilai</td></tr>
                        <?php else: ?>
                            <?php foreach ($peserta as $i => $item): ?>
                                <?php if ($item['penilaian_id']): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= esc($item['fullname']) ?></td>
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
                                            <?php if (!$item['approve_kaunit']): ?>
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalApprove<?= $item['magang_id'] ?>">
                                                    ✔️ Approve
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Sudah diapprove</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <!-- Modal Approve -->
                                    <div class="modal fade" id="modalApprove<?= $item['magang_id'] ?>" tabindex="-1" aria-labelledby="modalApproveLabel<?= $item['magang_id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <form action="<?= base_url('pembimbing/approve/save') ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="magang_id" value="<?= $item['magang_id'] ?>">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="modalApproveLabel<?= $item['magang_id'] ?>">Detail Penilaian: <?= esc($item['fullname']) ?></h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="form-row">
                                                            <?php
                                                            $fields = [
                                                                'Disiplin' => 'nilai_disiplin',
                                                                'Kerajinan' => 'nilai_kerajinan',
                                                                'Tingkah Laku' => 'nilai_tingkahlaku',
                                                                'Kerja Sama' => 'nilai_kerjasama',
                                                                'Kreativitas' => 'nilai_kreativitas',
                                                                'Kemampuan Kerja' => 'nilai_kemampuankerja',
                                                                'Tanggung Jawab' => 'nilai_tanggungjawab',
                                                                'Penyerapan' => 'nilai_penyerapan',
                                                            ];
                                                            foreach ($fields as $label => $field): ?>
                                                                <div class="form-group col-md-6">
                                                                    <label><?= $label ?></label>
                                                                    <input type="number" class="form-control" value="<?= $item[$field] ?>" readonly>
                                                                </div>
                                                            <?php endforeach; ?>
                                                            <div class="form-group col-md-12">
                                                                <label>Catatan atau Komentar</label>
                                                                <textarea class="form-control" rows="3" readonly><?= $item['catatan'] ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Approve</button>
                                                    </div>
                                                </div>
                                            </form>
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