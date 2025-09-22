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
                
<form action="<?= base_url('pembimbing/approve/bulk') ?>" method="post" id="bulkForm">
    <?= csrf_field(); ?>

    <div class="mb-3">
        <button type="submit" name="status" value="approve" class="btn btn-success btn-sm"
                onclick="return confirm('Yakin ingin menyetujui semua yang dipilih?')">
            ✔️ Approve Terpilih
        </button>
        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#bulkRejectModal">
            ❌ Reject Terpilih
        </button>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($peserta as $i => $item): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="magang_ids[]" value="<?= $item['magang_id'] ?>">
                    </td>
                    <td><?= $i+1 ?></td>
                    <td><?= esc($item['nama_peserta']) ?></td>
                    <td><?= esc($item['nisn_nim']) ?></td>
                    <td><?= esc($item['nama_instansi']) ?></td>
                    <td><?= date('d M Y', strtotime($item['tanggal_masuk'])) ?></td>
                    <td><?= date('d M Y', strtotime($item['tanggal_selesai'])) ?></td>
                    <td>
                        <?php if ($item['approve_kaunit'] == 1): ?>
                            <span class="badge bg-primary text-light">Sudah Disetujui</span>
                        <?php elseif ($item['approve_kaunit'] == 2): ?>
                            <span class="badge bg-danger text-light">Ditolak</span>
                        <?php else: ?>
                            <span class="badge bg-secondary text-light">Belum Disetujui</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                            $total = $item['nilai_disiplin'] + $item['nilai_kerajinan'] + $item['nilai_tingkahlaku'] +
                                    $item['nilai_kerjasama'] + $item['nilai_kreativitas'] + $item['nilai_kemampuankerja'] +
                                    $item['nilai_tanggungjawab'] + $item['nilai_penyerapan'];
                            $rata = round($total / 8, 2);
                        ?>
                                <span class="badge bg-success text-light">
                                    <strong><?= $rata ?></strong>
                                </span>
                        <button type="button" 
                                class="btn btn-sm btn-info mt-1" 
                                title="Detail Nilai"
                                data-toggle="modal" 
                                data-target="#modalDetail-<?= $item['magang_id'] ?>">
                            <i class="fas fa-info-circle"></i>
                        </button>
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
                                                'disiplin'       => 'Disiplin',
                                                'kerajinan'      => 'Kerajinan',
                                                'tingkahlaku'    => 'Tingkah Laku',
                                                'kerjasama'      => 'Kerja Sama',
                                                'kreativitas'    => 'Kreativitas',
                                                'kemampuankerja' => 'Kemampuan Kerja',
                                                'tanggungjawab'  => 'Tanggung Jawab',
                                                'penyerapan'     => 'Penyerapan'
                                            ];
                                            $totalNilai = 0;
                                            $jumlahField = count($fields);
                                            foreach ($fields as $name => $label): 
                                                $nilai = (int) $item['nilai_' . $name];
                                                $stars = ($nilai - 50) / 10; // 60=1, 70=2, ... 100=5
                                                $totalNilai += $nilai; // ✅ jumlahkan nilai tiap field
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

                                        <?php 
                                        $rataRata = $jumlahField > 0 ? $totalNilai / $jumlahField : 0;
                                        if ($rataRata >= 60 && $rataRata <= 69) {
                                            $kategori = "Kurang";
                                        } elseif ($rataRata >= 70 && $rataRata <= 79) {
                                            $kategori = "Cukup";
                                        } elseif ($rataRata >= 80 && $rataRata <= 89) {
                                            $kategori = "Baik";
                                        } elseif ($rataRata >= 90 && $rataRata <= 100) {
                                            $kategori = "Baik Sekali";
                                        } else {
                                            $kategori = "-";
                                        }
                                        ?>

                                        <!-- Rata-rata & Kategori -->
                                        <div class="alert alert-info mt-3">
                                            <strong>Rata-rata Nilai:</strong> <?= number_format($rataRata, 2) ?><br>
                                            <strong>Kategori:</strong> <?= $kategori ?>
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

                    </td>
                    <td><?= esc($item['catatan']) ?></td>
                    <td><?= esc($item['catatan_approval']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal Bulk Reject -->
    <div class="modal fade" id="bulkRejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Catatan Penolakan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan / Catatan</label>
                        <textarea name="catatan_reject" id="catatan_reject" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="status" value="reject" id="btnRejectSubmit" class="btn btn-danger">Kirim</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('checkAll').addEventListener('change', function() {
    let checkboxes = document.querySelectorAll('input[name="magang_ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// atur required hanya saat klik tombol "Kirim Reject"
const rejectTextarea = document.getElementById('catatan_reject');
const rejectBtn = document.getElementById('btnRejectSubmit');

rejectBtn.addEventListener('click', function() {
    rejectTextarea.required = true; // wajib diisi kalau submit reject
});
</script>



            </div>

        </div>
    </div>
</div>


<?= $this->endSection() ?>