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

    

    
    <h1 class="h3 mb-2 text-gray-800">
        Daftar Peserta Tes Safety Induction 
    </h1>


    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">

                <form method="get" class="mb-3 row g-3">
                    <div class="col-md-3">
                        <label for="bulan" class="form-label">Pilih Bulan</label>
                        <select name="bulan" id="bulan" class="form-control">
                            <?php
                                $bulanNama = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                foreach ($bulanNama as $num => $nama):
                            ?>
                                <option value="<?= $num ?>" <?= ($bulan == $num) ? 'selected' : '' ?>>
                                    <?= $nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="tahun" class="form-label">Pilih Tahun</label>
                        <select name="tahun" id="tahun" class="form-control">
                            <?php
                                $tahunSekarang = date('Y');
                                for ($i = $tahunSekarang; $i >= $tahunSekarang - 2; $i--):
                            ?>
                                <option value="<?= $i ?>" <?= ($tahun == $i) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-2 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Tampilkan
                        </button>
                    </div>
                </form>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>NIM/NISN</th>
                            <th>Unit Magang</th>
                            <th>Nilai Maksimal</th>
                            <th>Status</th>
                            <th>Jumlah Percobaan</th>
                            <th>Tanggal Tes Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($hasil)): ?>
                            <?php $no = 1; foreach ($hasil as $i => $row): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($row->fullname) ?></td>
                                    <td><?= esc($row->nisn_nim) ?></td>
                                    <td><?= esc($row->unit_kerja) ?></td>
                                    <td><?= esc($row->nilai_maksimal) ?></td>
                                    <td>
                                        <span class="badge <?= $row->status === 'Lulus' ? 'bg-success' : 'bg-danger' ?> text-light">
                                            <?= $row->status ?>
                                        </span>
                                    </td>
                                    <td><?= esc($row->percobaan_terakhir) ?></td>
                                    <td><?= format_tanggal_indonesia_dengan_jam(date('d M Y, H:i', strtotime($row->tanggal_terakhir))) ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
