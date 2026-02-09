<?= $this->extend('admin/templates/index');?>

<?= $this->section('content');?>
<style>
.chart-small {
    height: 230px !important;
    margin: auto;
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

<h1 class="h3 mb-2 text-gray-800">Daftar Peserta Penelitian</h1>
<!-- Card Tabel -->
<div class="row mb-4">

    <!-- FILTER -->
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-secondary font-weight-bold text-center text-uppercase">Filter Data</h6>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">

                    <div class="col-12">
                        <label class="form-label">Unit Kerja</label>
                        <select name="unit_kerja" class="form-control select2">
                            <option value="">-- Unit Kerja --</option>
                            <?php foreach ($unitList as $key): ?>
                                <option value="<?= $key['unit_id'] ?>"
                                    <?= ($key['unit_id'] == @$_GET['unit_kerja']) ? 'selected' : '' ?>>
                                    <?= $key['unit_kerja'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Masuk</label>
                        <input type="text"
                            name="tanggal_masuk"
                            class="form-control monthpicker"
                            placeholder="Bulan & Tahun"
                            value="<?= esc(@$_GET['tanggal_masuk']) ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Keluar</label>
                        <input type="text"
                            name="tanggal_keluar"
                            class="form-control monthpicker"
                            placeholder="Bulan & Tahun"
                            value="<?= esc(@$_GET['tanggal_keluar']) ?>">
                    </div>

                    <div class="col-12 mb-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Terapkan Filter
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- CHART -->
    <div class="col-md-8">
        <div class="card shadow h-100">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-secondary font-weight-bold text-center text-uppercase">Penelitian</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="chartPenelitian" style="max-height:300px;"></canvas>
            </div>
        </div>
    </div>

</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <a href="<?= base_url('admin/export-peserta-penelitian?' . http_build_query(service('request')->getGet())) ?>"
                class="btn btn-success mb-3">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <table class="table table-bordered table-striped" width="100%" cellspacing="0" id="dataTable" >
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Unit Kerja</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>No. RFID</th>
                        <th>Validasi Berkas</th>
                        <th>Setuju Pernyataan</th>
                        <th>Nilai Tes</th>
                        <th>Pembimbing</th>
                        <th>Form Ambil Data</th>
                        <th>Absensi</th>
                        <th>Status RFID</th>
                        <th>Feedback</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php $no = 1; foreach ($data as $item): ?>
                            <td><?= $no++; ?></td>
                            <td><?= esc($item['fullname']) ?></td>
                            <td><?= esc($item['nisn_nim']) ?></td>
                            <td><?= esc($item['unit_kerja']) ?></td>
                            <td><?= format_tanggal_indonesia($item['tanggal_masuk']) ?></td>
                            <td><?= format_tanggal_indonesia($item['tanggal_selesai']) ?></td>    
                            <td><?= esc($item['rfid_no']) ?? '-' ?></td>
                            <td><?php if(!empty($item['status_berkas_lengkap']) && $item['status_berkas_lengkap'] === 'Y'): ?>
                                    <span class="btn btn-sm btn-success text-light"><i class="fas fa-check-circle" title="Valid"></i></span>
                                                                
                                <?php else: ?>
                                <a href="<?= base_url('admin/penelitian/kelengkapan-berkas/' . $item['penelitian_id']) ?>">
                                <span class="btn btn-sm btn-danger text-light"><i class="fas fa-times-circle" title="Tidak Valid"></i></span>
                                </a>
                                <?php endif; ?>
                                
                            </td>
                            <td><?php if(!empty($item['tanggal_setujui_pernyataan'])): ?>
                                <span class="btn btn-sm btn-success text-light"><i class="fas fa-check-circle" title="Setuju"></i></span>
                                <?php else: ?>
                                <span class="btn btn-sm btn-danger text-light"><i class="fas fa-times-circle" title="Belum Setuju"></i></span>
                                <?php endif; ?>
                            </td>  
                            <td>
                                <?php
                                    $badgeClass = 'btn btn-sm btn-danger text-light'; 
                                    if ($item['status_tes'] === 'Lulus') {
                                        $badgeClass = 'btn btn-sm btn-success text-light'; 
                                        $title = 'Lulus';
                                    } elseif ($item['status_tes'] === 'Belum Lulus') {
                                        $badgeClass = 'btn btn-sm btn-warning text-light'; 
                                        $title = 'Belum Lulus';
                                    }elseif ($item['status_tes'] === '-') {
                                        $badgeClass = 'btn btn-sm btn-danger text-light'; 
                                        $title = 'Belum Tes';
                                    }
                                ?>

                                <?php if(!empty($item['nilai_maksimal'])): ?>
                                    <span class=" <?= $badgeClass ?>" title="<?= $title; ?>"><?= $item['nilai_maksimal'] ?></span>
                                <?php else : ?>
                                    <span class=" <?= $badgeClass ?>" title="<?= $title; ?>">
                                        <?= $item['status_tes'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($item['nama_pembimbing'])): ?>
                                    <!-- Belum Ada -->
                                    <button 
                                        class="btn btn-sm btn-primary ml-2 btn-set-pembimbing-penelitian"
                                        data-id="<?= $item['penelitian_id'] ?>"
                                        data-unit="<?= $item['unit_id'] ?>"
                                        data-pembimbing=""
                                        data-nama="<?= esc($item['fullname']) ?>"
                                        data-mode="tambah"
                                        title="Tambah Pembimbing"
                                    >
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <span title="<?= esc($item['nama_pembimbing']) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></span>
                                    <button 
                                        class="btn btn-sm btn-warning ml-2 btn-set-pembimbing-penelitian"
                                        data-id="<?= $item['penelitian_id'] ?>"
                                        data-unit="<?= $item['unit_id'] ?>"
                                        data-pembimbing="<?= $item['pembimbing_id'] ?>"
                                        data-nama="<?= esc($item['fullname']) ?>"
                                        data-mode="edit"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php endif; ?>
                            </td>                       
                            <td>
                                <?php if (empty($item['formulir_penelitian'])): ?>
                                    <span class="btn btn-sm btn-danger"><i class="fas fa-times-circle" title="Belum Ada"></i></span>
                                    

                                <?php else: ?>
                                    <?php if ($item['status_pembimbing'] === 'Disetujui'): ?>
                                        <!-- Sudah diterima -->
                                        <a href="<?= base_url('uploads/form-penelitian/' . $item['formulir_penelitian']) ?>" 
                                        target="_blank" 
                                        class="btn btn-info btn-sm" 
                                        title="Lihat Formulir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm btn-tolak-formulir" 
                                                title="Tolak Formulir"
                                                data-id="<?= $item['penelitian_id'] ?>" 
                                                data-nama="<?= esc($item['fullname']) ?>">
                                            <i class="fas fa-times-circle"></i>
                                        </button>

                                    <?php elseif ($item['status_pembimbing'] === 'Ditolak'): ?>
                                        <!-- Ditolak oleh pembimbing -->
                                        <span class="btn btn-sm btn-danger text-light" title="Ditolak PemBimbing"><i class="fas fa-user-times"></i></span>

                                    <?php else: ?>
                                        <!-- Sudah upload tapi belum diterima -->
                                        <span class="btn btn-sm btn-warning text-dark" title="Belum Diterima"><i class="fas fa-user-times"></i></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($item['absensi'])): ?>
                                    <a href="<?= base_url('uploads/absensi-penelitian/' . $item['absensi']) ?>" target="_blank" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-tolak-absensi-penelitian" 
                                        title="Tolak Absensi"
                                        data-id="<?= $item['penelitian_id'] ?>" 
                                        data-nama="<?= $item['fullname'] ?>">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="btn btn-sm btn-danger"><i class="fas fa-times-circle" title="Belum Setuju"></i></span>
                                <?php endif; ?>
                            </td>
                            <td><?php if($item['status_rfid'] === 'returned'): ?>
                                <span class="btn btn-sm btn-success text-light" title="Dikembalikan"><strong><i class="fas fa-check-circle"></i></strong></span>
                                <?php elseif($item['status_rfid'] === 'lost') : ?>
                                <span class="btn btn-sm btn-danger text-light" title="Denda"><strong><i class="fas fa-money-bill-alt"></i></strong></span>
                                <?php elseif($item['status_rfid'] === 'aktif') :?>
                                <span class="btn btn-sm btn-primary text-light" title="Digunakan"><strong><i class="fas fa-check-circle"></i></strong></span>
                                <?php else : ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td>           
                                <?php if (!empty($item['feedbackp_id'])): ?>
                                    <button class="btn btn-sm btn-success"><i class="fas fa-check-circle" title="Sudah isi feedback"></i></button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-danger"> <i class="fas fa-times-circle" title="Belum isi feedback"></i></button>
                                <?php endif; ?>
                            </td>                                   
                            <td>      
                                <div class="aksi-wrapper">         
                                    <?php
                                        $hariTerakhir = date('Y-m-d') >= $item['tanggal_selesai']; 
                                        $formulirAda   = !empty($item['formulir_penelitian']);
                                        $rfidOk       = empty($item['status_rfid']) || in_array($item['status_rfid'], ['returned','lost']);
                                        $feedbackAda  = !empty($item['feedbackp_id']);
                                    ?>
                                    <button class="btn btn-sm btn-info btn-detail-penelitian" data-id="<?= $item['penelitian_id'] ?>" title="Detail"><i class="fas fa-eye"></i></button>
                                    <?php if (!empty($item['rfid_no']) && $item['status_rfid'] != 'returned'): ?>
                                        <!-- Tombol Kembalikan RFID -->
                                        <button 
                                            class="btn btn-sm btn-danger btn-rfidp"
                                            data-type="return"
                                            data-penelitian-id="<?= $item['penelitian_id'] ?>"
                                            data-assignment-id="<?= $item['assignment_id'] ?>"
                                            data-id-rfid="<?= $item['id_rfid'] ?>"
                                            data-rfid-no="<?= esc($item['rfid_no']) ?>"
                                            title="Kembalikan RFID"
                                        >
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php else: ?>
                                        <!-- Tombol Tambah RFID -->
                                        <button 
                                            class="btn btn-sm btn-success btn-rfidp"
                                            data-type="add"
                                            data-penelitian-id="<?= $item['penelitian_id'] ?>"
                                            title="Tambah RFID"
                                        >
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($hariTerakhir && $formulirAda && $rfidOk && $feedbackAda ): ?>
                                        <?php if ($item['finalisasi'] == null): ?>
                                            <form action="<?= base_url('admin/finalisasi-penelitian/'.$item['penelitian_id']) ?>" 
                                                method="post" class="finalisasi-form" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-success btn-sm finalisasi-btn" title="Lanjut Finalisasi">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled title="Menunggu Approve Pusdiklat">
                                                <i class="fas fa-hourglass-half"></i> 
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled title="Belum bisa finalisasi">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    <?php endif; ?>      
                                    
                                </div> 
                            </td>
                                                    
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- <div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <div class="card shadow"> 
        <div class="card-body">
            <form method="get" class="row g-2 mb-3">

                <div class="col-md-3">
                    <label class="form-label">Unit Kerja</label>
                    <select name="unit_kerja" class="form-control select2">
                        <option value="">-- Unit Kerja --</option>
                        <?php foreach ($unitList as $key): ?>
                            <option value="<?= $key['unit_id'] ?>"
                                <?= ($key['unit_id'] == @$_GET['unit_kerja']) ? 'selected' : '' ?>>
                                <?= $key['unit_kerja'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Masuk</label>
                    <input type="text"
                        name="tanggal_masuk"
                        class="form-control monthpicker"
                        placeholder="Pilih Bulan & Tahun"
                        value="<?= esc(@$_GET['tanggal_masuk']) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Keluar</label>
                    <input type="text"
                        name="tanggal_keluar"
                        class="form-control monthpicker"
                        placeholder="Pilih Bulan & Tahun"
                        value="<?= esc(@$_GET['tanggal_keluar']) ?>">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>

            </form>

        </div>
    </div>

    <div class="row mt-4">
        
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 text-secondary fw-bold font-weight-bold text-center text-uppercase">Penelitian</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartPenelitian" class="chart-small"  style="height:260px;"></canvas>
                </div>
            </div>
        </div>
    </div>

            <a href="<?= base_url('admin/export-peserta?bulan_masuk='.service('request')->getGet('bulan_masuk').'&bulan_keluar='.service('request')->getGet('bulan_keluar').'&tahun='.service('request')->getGet('tahun')) ?>" 
                class="btn btn-success mb-3">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>

            <table class="table table-bordered table-striped" width="100%" cellspacing="0" id="dataTable" >
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Unit Kerja</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>No. RFID</th>
                        <th>Validasi Berkas</th>
                        <th>Setuju Pernyataan</th>
                        <th>Nilai Tes</th>
                        <th>Pembimbing</th>
                        <th>Form Ambil Data</th>
                        <th>Absensi</th>
                        <th>Status RFID</th>
                        <th>Feedback</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php $no = 1; foreach ($data as $item): ?>
                            <td><?= $no++; ?></td>
                            <td><?= esc($item['fullname']) ?></td>
                            <td><?= esc($item['nisn_nim']) ?></td>
                            <td><?= esc($item['unit_kerja']) ?></td>
                            <td><?= format_tanggal_indonesia($item['tanggal_masuk']) ?></td>
                            <td><?= format_tanggal_indonesia($item['tanggal_selesai']) ?></td>    
                            <td><?= esc($item['rfid_no']) ?? '-' ?></td>
                            <td><?php if(!empty($item['status_berkas_lengkap']) && $item['status_berkas_lengkap'] === 'Y'): ?>
                                    <span class="badge bg-success text-light">Valid</span>
                                                                
                                <?php else: ?>
                                <a href="<?= base_url('admin/penelitian/kelengkapan-berkas/' . $item['penelitian_id']) ?>">
                                <span class="badge bg-danger text-light">Tidak Valid</span>
                                </a>
                                <?php endif; ?>
                                
                            </td>
                            <td><?php if(!empty($item['tanggal_setujui_pernyataan'])): ?>
                                <span class="badge bg-success text-light">Disetujui</span>
                                <?php else: ?>
                                <span class="badge bg-danger text-light">Belum Setuju</span>
                                <?php endif; ?>
                            </td>  
                            <td>
                                <?php
                                    $badgeClass = 'bg-danger text-light'; 
                                    if ($item['status_tes'] === 'Lulus') {
                                        $badgeClass = 'bg-success text-light'; 
                                    } elseif ($item['status_tes'] === 'Belum Lulus') {
                                        $badgeClass = 'bg-warning text-light'; 
                                    }
                                ?>

                                <?php if(!empty($item['nilai_maksimal'])): ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $item['nilai_maksimal'] ?></span>
                                <?php else : ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= $item['status_tes'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($item['nama_pembimbing'])): ?>
                                    Belum Ada
                                    <button 
                                        class="btn btn-sm btn-success ml-2 btn-set-pembimbing-penelitian"
                                        data-id="<?= $item['penelitian_id'] ?>"
                                        data-unit="<?= $item['unit_id'] ?>"
                                        data-pembimbing=""
                                        data-nama="<?= esc($item['fullname']) ?>"
                                        data-mode="tambah"
                                    >
                                        <i class="fas fa-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <?= esc($item['nama_pembimbing']) ?>
                                    <button 
                                        class="btn btn-sm btn-primary ml-2 btn-set-pembimbing-penelitian"
                                        data-id="<?= $item['penelitian_id'] ?>"
                                        data-unit="<?= $item['unit_id'] ?>"
                                        data-pembimbing="<?= $item['pembimbing_id'] ?>"
                                        data-nama="<?= esc($item['fullname']) ?>"
                                        data-mode="edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php endif; ?>
                            </td>                       
                            <td>
                                <?php if (empty($item['formulir_penelitian'])): ?>
                                    
                                    <span class="text-muted">Belum ada</span>

                                <?php else: ?>
                                    <?php if ($item['status_pembimbing'] === 'Disetujui'): ?>
                                        
                                        <a href="<?= base_url('uploads/formpenelitian/' . $item['formulir_penelitian']) ?>" 
                                        target="_blank" 
                                        class="btn btn-primary btn-sm" 
                                        title="Lihat Formulir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm btn-tolak-formulir" 
                                                title="Tolak Formulir"
                                                data-id="<?= $item['penelitian_id'] ?>" 
                                                data-nama="<?= esc($item['fullname']) ?>">
                                            <i class="bi bi-x-circle"></i>
                                        </button>

                                    <?php elseif ($item['status_pembimbing'] === 'Ditolak'): ?>
                                        
                                        <span class="badge bg-danger text-light">Ditolak Pembimbing</span>

                                    <?php else: ?>
                                        
                                        <span class="badge bg-warning text-dark">Belum Diterima</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($item['absensi'])): ?>
                                    <a href="<?= base_url('uploads/absensi-penelitian/' . $item['absensi']) ?>" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-tolak-absensi-penelitian" 
                                        title="Tolak Absensi"
                                        data-id="<?= $item['penelitian_id'] ?>" 
                                        data-nama="<?= $item['fullname'] ?>">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td><?php if($item['status_rfid'] === 'returned'): ?>
                                <span class="badge bg-success text-light">Dikembalikan</span>
                                <?php elseif($item['status_rfid'] === 'lost') : ?>
                                <span class="badge bg-danger text-light">Denda</span>
                                <?php elseif($item['status_rfid'] === 'aktif') :?>
                                <span class="badge bg-primary text-light">Digunakan</span>
                                <?php else : ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td>           
                                <?php if (!empty($item['feedbackp_id'])): ?>
                                    <i class="bi bi-check-circle-fill text-success" title="Sudah isi feedback"></i>
                                <?php else: ?>
                                    <i class="bi bi-x-circle-fill text-danger" title="Belum isi feedback"></i>
                                <?php endif; ?>
                            </td>                                   
                            <td>                
                                <?php
                                    $hariTerakhir = date('Y-m-d') >= $item['tanggal_selesai']; 
                                    $formulirAda   = !empty($item['formulir_penelitian']);
                                    $rfidOk       = empty($item['status_rfid']) || in_array($item['status_rfid'], ['returned','lost']);
                                    $feedbackAda  = !empty($item['feedbackp_id']);
                                ?>
                                

                                <?php if ($hariTerakhir && $formulirAda && $rfidOk && $feedbackAda ): ?>
                                    <?php if ($item['finalisasi'] == null): ?>
                                        <form action="<?= base_url('admin/finalisasi-penelitian/'.$item['penelitian_id']) ?>" 
                                            method="post" class="finalisasi-form" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success btn-sm finalisasi-btn">
                                                <i class="fas fa-check-circle"></i> Finalisasi
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled title="Menunggu Approve Pusdiklat">
                                            <i class="fas fa-hourglass-half"></i> Menunggu Approve
                                        </button>
                                    <?php endif; ?>

                                <?php endif; ?>      
                            <button class="btn btn-sm btn-primary btn-detail-penelitian" data-id="<?= $item['penelitian_id'] ?>" >Detail</button>
                                  <?php if (!empty($item['rfid_no']) && $item['status_rfid'] != 'returned'): ?>
                                      
                                      <button 
                                          class="btn btn-sm btn-danger btn-rfidp"
                                          data-type="return"
                                          data-penelitian-id="<?= $item['penelitian_id'] ?>"
                                          data-assignment-id="<?= $item['assignment_id'] ?>"
                                          data-id-rfid="<?= $item['id_rfid'] ?>"
                                          data-rfid-no="<?= esc($item['rfid_no']) ?>"
                                      >
                                          Kembalikan RFID
                                      </button>
                                  <?php else: ?>
                                      
                                      <button 
                                          class="btn btn-sm btn-success btn-rfidp"
                                          data-type="add"
                                          data-penelitian-id="<?= $item['penelitian_id'] ?>"
                                      >
                                          Tambah RFID
                                      </button>
                                  <?php endif; ?>
                            </td>
                                                    
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> -->

<!-- Modal Pembimbing -->
<div class="modal fade" id="pembimbingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form id="formPembimbing" method="post" action="">
        <?= csrf_field() ?>

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalPembimbingTitle">Pilih Pembimbing</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <p class="font-weight-bold text-primary" id="namaPeserta"></p>

          <div class="form-group">
            <label>Pembimbing</label>
            <select name="pembimbing_id" id="selectPembimbing" class="form-control select2" required>
              <option value="" disabled selected>Memuat data...</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSubmitPembimbing">Simpan</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Modal Tolak Laporan -->
<div class="modal fade" id="tolakLaporanModal" tabindex="-1" aria-labelledby="tolakLaporanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formTolakLaporan" method="post" action="">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="tolakLaporanLabel">Tolak Formulir Penelitian</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p id="namaPesertaLaporan" class="font-weight-bold text-primary"></p>
          <div class="form-group">
            <label>Alasan Penolakan</label>
            <textarea name="alasan" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tolak Absensi -->
<div class="modal fade" id="tolakAbsensiModal" tabindex="-1" aria-labelledby="tolakAbsensiLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formTolakAbsensi" method="post" action="">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="tolakAbsensiLabel">Tolak Absensi</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p id="namaPesertaAbsensi" class="font-weight-bold text-primary"></p>
          <div class="form-group">
            <label>Alasan Penolakan</label>
            <textarea name="alasan" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Nilai -->
<div class="modal fade" id="modalDetailNilai" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Detail Nilai Penelitian</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="detailContent">Memuat data...</div>
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
                <button id="btnEditPenelitian" class="btn btn-sm btn-warning d-none" title="Edit"><i class="fas fa-edit"></i></button>
                <button id="btnBatalkanPenelitian" class="btn btn-sm btn-danger d-none"  title="Batalkan"><i class="fas fa-ban"></i></button>
                <button class="btn btn-secondary" data-dismiss="modal"  title="Tutup"><i class="fas fa-times"></i></button>
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
          <h5 class="modal-title" id="modalEditPenelitianLabel">Edit Data Penelitian</h5>
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

<!-- 🔹 Modal Tunggal untuk Tambah / Kembalikan RFID -->
<div class="modal fade" id="rfidModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="rfidForm" method="post">
      <?= csrf_field(); ?>
      <input type="hidden" name="penelitian_id" id="penelitian_id">
      <input type="hidden" name="assignment_id" id="assignment_id">
      <input type="hidden" name="id_rfid" id="id_rfid">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rfidModalTitle"></h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>

        <div class="modal-body" id="rfidModalBody">
          <!-- Konten akan diganti dinamis via JS -->
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" id="rfidSubmitBtn" class="btn">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
flatpickr(".monthpicker", {
    plugins: [
        new monthSelectPlugin({
            shorthand: false,
            dateFormat: "Y-m",
            altFormat: "F Y",
            altInput: true
        })
    ]
});
</script>
<script>
const makeRedirect = (filter) => {
    const unit = "<?= $unitGet ?>";
    let url = "<?= base_url('admin/manage-penelitian') ?>";
    const params = [];

    if (filter) params.push("filter=" + filter);
    if (unit) params.push("unit_kerja=" + unit);

    window.location.href = url + "?" + params.join("&");
};

// DATA PENELITIAN
const dataPenelitian = {
    labels: ['Aktif Penelitian', 'Akan Penelitian', 'Belum Selesai'],
    values: [
        <?= $chartData['aktif'] ?>,
        <?= $chartData['akan_masuk'] ?>,
        <?= $chartData['belum_selesai'] ?>
    ],
    filters: ['aktif', 'akan_penelitian', 'belum_selesai']
};

// COLORS
const colors = ['#1cc88a', '#36b9cc', '#e74a3b'];

function makePie(canvasId, data) {

    const total = data.values.reduce((a, b) => a + b, 0);
    if (total === 0) {

        const canvas = document.getElementById(canvasId);

        canvas.outerHTML = `
            <div class="d-flex justify-content-center align-items-center"
                 style="height: 230px; color:#6c757d; font-size:18px;">
                Data tidak ada
            </div>
        `;
        return;
    }

    const ctx = document.getElementById(canvasId).getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: colors,
                hoverOffset: 8,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            cutout: "55%",
            maintainAspectRatio: false,
            aspectRatio: 1.6,
            plugins: {
                legend: { position: 'right' }
            },
            onClick: (e, item) => {
                if (item.length > 0) {
                    const index = item[0].index;
                    makeRedirect(data.filters[index]);
                }
            }
        }
    });
}

// RENDER CHART
makePie('chartPenelitian', dataPenelitian);
</script>


<script>
function batalkanPenelitian(id, modalId) {
    // Tutup modal Bootstrap dulu
    $('#' + modalId).modal('hide');

    // Delay agar modal benar-benar tertutup sebelum SweetAlert muncul
    setTimeout(function() {
        Swal.fire({
            title: 'Batalkan Penelitian?',
            input: 'textarea',
            inputLabel: 'Alasan Pembatalan',
            inputPlaceholder: 'Tulis alasan pembatalan di sini...',
            inputAttributes: {
                'aria-label': 'Tulis alasan pembatalan'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            preConfirm: (alasan) => {
                if (!alasan) {
                    Swal.showValidationMessage('Alasan pembatalan wajib diisi.');
                }
                return alasan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('admin/batalkanPenelitian') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'id': id,
                        'alasan': result.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Berhasil', 'Peserta penelitian telah dibatalkan.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }, 300); 
}

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.finalisasi-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // stop dulu submitnya

        Swal.fire({
            title: 'Finalisasi Penelitian?',
            text: "Setelah difinalisasi, data tidak bisa diubah lagi. Yakin ingin melanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Finalisasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // kirim form kalau setuju
            }
        });
    });
});
</script>



</div>

<?= $this->endSection() ?>
