<?= $this->extend('admin/templates/index');?>
<?= $this->section('content');?>
<style>
.rating .star {
    font-size: 24px;
    color: #ccc;
    cursor: pointer;
}
.rating .star.selected,
.rating .star.hover {
    color: gold;
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
    <!-- <h1 class="h3 mb-4 text-gray-800">Penilaian</h1> -->
    <h1 class="h3 mb-4 text-gray-800">
                    Penilaian
                    <?php if (!empty($unitPembimbing)): ?>
                        - 
                        <?= implode(', ', array_column($unitPembimbing, 'unit_kerja')) ?>
                    <?php endif; ?>
                </h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                
    <table class="table table-bordered table-striped" id="dataTable">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM/NISN</th>
                <th>Instansi</th>
                <th>Pembimbing</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Laporan</th>
                <th>Absensi</th>
                <th>Penilaian</th>
                <th>Approve</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($peserta)): ?>
                <tr><td colspan="12" class="text-center">Tidak ada peserta magang</td></tr>
            <?php else: ?>
                <?php foreach ($peserta as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($item['nama_peserta']) ?></td>
                        <td><?= esc($item['nisn_nim']) ?></td>
                        <td><?= esc($item['nama_instansi']) ?></td>
                        <td>
                            <?php if (empty($item['nama_pembimbing'])): ?>
                                Belum Ada
                                <!-- Tombol tambah -->
                                <button class="btn btn-sm btn-success ml-2" data-toggle="modal" data-target="#tambahPembimbingModal<?= $item['magang_id'] ?>">
                                    <i class="fas fa-plus"></i>
                                </button>
                            <?php else: ?>
                                <?= esc($item['nama_pembimbing']) ?>
                                <?php if($userLogin['eselon'] == 2):?>
                                <!-- Tombol edit -->
                                <a href="#" class="text-primary ml-2" data-toggle="modal" title="Ganti Pembimbing" data-target="#editPembimbingModal<?= $item['magang_id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y', strtotime($item['tanggal_masuk'])) ?></td>
                        <td><?= date('d M Y', strtotime($item['tanggal_selesai'])) ?></td>
                        <td>
                            <?php if (!empty($item['laporan'])): ?>
                                <a href="<?= base_url('uploads/laporan/' . $item['laporan']) ?>" target="_blank">
                                    Lihat Laporan
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($item['absensi'])): ?>
                                <a href="<?= base_url('uploads/absensi/' . $item['absensi']) ?>" target="_blank">
                                    Lihat Absensi
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($item['penilaian_id']): ?>
                                <?php 
                                $total = $item['nilai_disiplin'] + $item['nilai_kerajinan'] + $item['nilai_tingkahlaku'] +
                                        $item['nilai_kerjasama'] + $item['nilai_kreativitas'] + $item['nilai_kemampuankerja'] +
                                        $item['nilai_tanggungjawab'] + $item['nilai_penyerapan'];
                                $rata = round($total / 8, 2);
                                ?>
                                <span class="badge bg-success text-light">
                                    <strong><?= $rata ?></strong>
                                </span>
                                <!-- Icon Edit -->
                                <a href="#" class="text-primary ml-2" data-toggle="modal" data-target="#editNilaiModal<?= $item['penilaian_id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Modal Edit Penilaian -->
                                <div class="modal fade" id="editNilaiModal<?= $item['penilaian_id'] ?>" tabindex="-1" aria-labelledby="editNilaiLabel<?= $item['penilaian_id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form action="<?= base_url('pembimbing/penilaian/save') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="magang_id" value="<?= $item['magang_id'] ?>">

                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title" id="editNilaiLabel<?= $item['penilaian_id'] ?>">
                                                        Edit Penilaian: <?= esc($item['nama_peserta']) ?>
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
                                                        $nilaiMap = [60=>1,70=>2,80=>3,90=>4,100=>5];
                                                        foreach ($fields as $name => $label):
                                                            $nilai = (int) $item['nilai_'.$name];
                                                            $bintangAktif = $nilaiMap[$nilai] ?? 0;
                                                        ?>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="font-weight-bold"><?= $label ?></label>
                                                                <div class="rating d-block" data-name="<?= $name ?>">
                                                                    <?php for ($j = 1; $j <= 5; $j++): ?>
                                                                        <i class="fa fa-star star <?= ($j <= $bintangAktif) ? 'selected' : '' ?>" data-value="<?= $j ?>"></i>
                                                                    <?php endfor; ?>
                                                                    <input type="hidden" name="<?= $name ?>" value="<?= $nilai ?>" required>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>

                                                    <!-- Rata-rata & Kategori -->
                                                    <div class="alert alert-info mt-3">
                                                        <strong>Rata-rata Nilai:</strong> <span id="avgScore-<?= $item['penilaian_id'] ?>"><?= $rata ?></span><br>
                                                        <strong>Kategori:</strong> 
                                                        <span id="kategori-<?= $item['penilaian_id'] ?>">
                                                            <?php 
                                                                if ($rata >= 90) echo "Baik Sekali";
                                                                elseif ($rata >= 80) echo "Baik";
                                                                elseif ($rata >= 70) echo "Cukup";
                                                                elseif ($rata >= 60) echo "Kurang";
                                                                else echo "-";
                                                            ?>
                                                        </span>
                                                    </div>

                                                    <div class="form-group mt-3">
                                                        <label class="font-weight-bold">Catatan atau Komentar</label>
                                                        <textarea name="catatan" class="form-control" rows="3"><?= esc($item['catatan']) ?></textarea>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php else: ?>
                                <span class="badge bg-warning text-light">Belum Dinilai</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($item['approve_kaunit'] == 1): ?>
                                <span class="badge bg-success text-light">Disetujui</span>
                            <?php elseif ($item['approve_kaunit'] == 2): ?>
                                <span class="badge bg-danger text-light">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-light">Belum Disetujui</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($item['pembimbing_id'])): ?>
                                <?php if ($item['penilaian_id']): ?>
                                    <button type="button" title="Detail Nilai" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalDetail-<?= $item['magang_id'] ?>">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <?php if(!empty($item['laporan']) && !empty($item['absensi'])): ?>
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalNilai-<?= $item['magang_id'] ?>">
                                        <i class="bi bi-pencil-square"></i> Nilai
                                    </button>
                                    <?php else : ?>
                                        <button type="button" class="btn btn-sm btn-secondary" title="Laporan & Absensi Belum Diupload">
                                        <i class="bi bi-pencil-square"></i> Nilai
                                        </button>
                                    <?php endif;?>
                                <?php endif; ?>
                            <?php else :?>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#tambahPembimbingModal<?= $item['magang_id'] ?>">
                                    <i class="fas fa-user-plus"></i> Tambah Pembimbing
                                </button>
                                
                            <?php endif; ?>
                            

                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>

    <?php if (!empty($peserta)): ?>
        <?php foreach ($peserta as $item): ?>
            <!-- Modal Tambah Pembimbing -->
            <div class="modal fade" id="tambahPembimbingModal<?= $item['magang_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?= $item['magang_id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <form action="<?= base_url('pembimbing/assignPembimbing/'.$item['magang_id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel<?= $item['magang_id'] ?>">Pilih Pembimbing</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                        <div class="form-group">
                            <label for="pembimbing_id<?= $item['magang_id'] ?>">Pembimbing</label>
                            <select name="pembimbing_id" id="pembimbing_id<?= $item['magang_id'] ?>" class="form-control select2" required>
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
            <div class="modal fade" id="editPembimbingModal<?= $item['magang_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $item['magang_id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <form action="<?= base_url('pembimbing/updatePembimbing/'.$item['magang_id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?= $item['magang_id'] ?>">Edit Pembimbing</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_pembimbing_id<?= $item['magang_id'] ?>">Pembimbing</label>
                                <select name="pembimbing_id" id="edit_pembimbing_id<?= $item['magang_id'] ?>" class="form-control select2" required>
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

            <!-- Modal Penilaian -->
            <div class="modal fade" id="modalNilai-<?= $item['magang_id'] ?>" tabindex="-1" aria-labelledby="modalNilaiLabel-<?= $item['magang_id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form action="<?= base_url('pembimbing/penilaian/save') ?>" method="post"  onsubmit="return validatePenilaian(this)">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="magang_id" value="<?= $item['magang_id'] ?>">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="modalNilaiLabel-<?= $item['magang_id'] ?>">
                                    Form Penilaian: <?= esc($item['nama_peserta']) ?>
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
                                    foreach ($fields as $name => $label): ?>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold"><?= $label ?></label>
                                            <div class="rating d-block" data-name="<?= $name ?>">
                                                <?php for ($j = 1; $j <= 5; $j++): ?>
                                                    <i class="fa fa-star star" data-value="<?= $j ?>"></i>
                                                <?php endfor; ?>
                                                <input type="hidden" name="<?= $name ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Hasil rata-rata -->
                                <div class="alert alert-info mt-3">
                                    <strong>Rata-rata Nilai:</strong> <span id="avgScore-<?= $item['magang_id'] ?>">-</span><br>
                                    <strong>Kategori:</strong> <span id="kategori-<?= $item['magang_id'] ?>">-</span>
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">Catatan atau Komentar</label>
                                    <textarea name="catatan" class="form-control" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Simpan Nilai</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Modal Detail Penilaian -->
            <div class="modal fade" id="modalDetail-<?= $item['magang_id'] ?>" tabindex="-1" aria-labelledby="modalDetailLabel-<?= $item['magang_id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
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
                                $totalNilai = 0;
                                $jumlahField = count($fields);

                                foreach ($fields as $name => $label): 
                                    $nilai = (int) $item['nilai_' . $name];
                                    $stars = ($nilai - 50) / 10; // 60=1, 70=2, ... 100=5
                                    $totalNilai += $nilai;
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




        <?php endforeach; ?>
    <?php endif; ?>

</div>

        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nilaiMap = {1:60, 2:70, 3:80, 4:90, 5:100};

    document.querySelectorAll('.rating').forEach(rating => {
        const stars = rating.querySelectorAll('.star');
        const input = rating.querySelector('input[type="hidden"]');

        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                stars.forEach(s => s.classList.remove('hover'));
                star.classList.add('hover');
                let prev = star.previousElementSibling;
                while (prev) {
                    prev.classList.add('hover');
                    prev = prev.previousElementSibling;
                }
            });

            star.addEventListener('mouseout', () => {
                stars.forEach(s => s.classList.remove('hover'));
            });

            star.addEventListener('click', () => {
                const value = parseInt(star.getAttribute('data-value'));
                input.value = nilaiMap[value]; // simpan nilai ke hidden input

                stars.forEach(s => s.classList.remove('selected'));
                star.classList.add('selected');
                let prev = star.previousElementSibling;
                while (prev) {
                    prev.classList.add('selected');
                    prev = prev.previousElementSibling;
                }

                // hitung rata-rata setelah klik
                updateAverage(rating.closest('.modal'));
            });
        });
    });

    function updateAverage(modal) {
        // hanya ambil input hidden di dalam .rating
        const inputs = modal.querySelectorAll('.rating input[type="hidden"]');
        let total = 0, count = 0;

        inputs.forEach(input => {
            if (input.value) {
                total += parseInt(input.value);
                count++;
            }
        });

        if (count > 0) {
            const avg = (total / count).toFixed(2);
            modal.querySelector('[id^="avgScore-"]').textContent = avg;

            let kategori = "-";
            if (avg >= 60 && avg <= 69) kategori = "Kurang";
            else if (avg >= 70 && avg <= 79) kategori = "Cukup";
            else if (avg >= 80 && avg <= 89) kategori = "Baik";
            else if (avg >= 90 && avg <= 100) kategori = "Baik Sekali";

            modal.querySelector('[id^="kategori-"]').textContent = kategori;
        }
    }

});
</script>

<script>
function validatePenilaian(form) {
    let valid = true;
    let pesan = [];

    form.querySelectorAll('.rating').forEach(rating => {
        const input = rating.querySelector('input[type="hidden"]');
        const label = rating.previousElementSibling?.textContent || 'Aspek';

        if (!input.value) {
            valid = false;
            pesan.push(`- ${label} wajib diisi`);
            rating.classList.add('border', 'border-danger', 'p-2', 'rounded'); // highlight merah
        } else {
            rating.classList.remove('border', 'border-danger', 'p-2', 'rounded');
        }
    });

    if (!valid) {
        alert("Harap isi semua penilaian:\n\n" + pesan.join("\n"));
    }

    return valid; // kalau false -> form tidak jadi submit
}
</script>




<?= $this->endSection() ?>