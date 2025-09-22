<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?php $session = \Config\Services::session(); ?>
    <?php if ($session->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $session->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <h1 class="h3 mb-2 text-gray-800">Data Pendaftaran</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Unit Kerja</th>
                            <th>Tanggal Daftar</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pendaftaran) && is_array($pendaftaran)) : ?>
                            <?php $no = 1; foreach ($pendaftaran as $data) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($data['fullname']); ?></td>
                                    <td><?= esc($data['unit_kerja']); ?></td>
                                    <td><?= date('d-m-Y, H:i', strtotime($data['tanggal_daftar'])); ?></td>
                                    <td><?= !empty($data['tanggal_masuk']) ? date('d-m-Y', strtotime($data['tanggal_masuk'])) : '-' ?></td>
                                    <td><?= !empty($data['tanggal_selesai']) ? date('d-m-Y', strtotime($data['tanggal_selesai'])) : '-' ?></td>
                                    <td>
                                        <?php 
                                        if (!is_null($data['tanggal_validasi_berkas'])) {
                                            if ($data['status_validasi_berkas'] === 'Y') {
                                                echo "Proses Validasi";
                                            } else {
                                                echo "Berkas Tidak Valid";
                                            }
                                        } elseif(!is_null($data['status_konfirmasi'])){
                                            if($data['status_konfirmasi'] === 'Y'){
                                                echo "Terkonfirmasi";
                                            }else{
                                                echo "Tidak Konfirmasi";
                                            }
                                        } elseif(!is_null($data['status_seleksi'])) {
                                            echo $data['status_seleksi'];
                                        }else{
                                            echo "Pendaftaran";
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- <a href="<?= base_url('admin/manage-pendaftaran/detail/' . $data['magang_id']); ?>" class="btn btn-info btn-sm">Detail</a> -->
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal<?= $data['magang_id'] ?>">Detail</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <?php foreach ($pendaftaran as $data): ?>
    <div class="modal fade" id="detailModal<?= $data['magang_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel<?= $data['magang_id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="detailModalLabel<?= $data['magang_id'] ?>">Detail Pendaftaran - <?= esc($data['fullname']) ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
        </div>
        <div class="modal-body">
            <!-- Informasi Pendaftar -->
            <h6 class="text-primary">📌 Informasi Pendaftar</h6>
            <table class="table table-sm table-bordered">
                <tr>
                    <th>Nama Lengkap</th>
                    <td><?= esc($data['fullname']) ?></td>
                    <!-- gambar ditaruh di sini dengan rowspan -->
                    <td rowspan="4" class="text-center align-middle" style="width: 180px;">
                        <?php if (!empty($data['user_image'])): ?>
                        <img src="<?= base_url('uploads/user-image/' . ($data['user_image'] ?? 'default.png')) ?>" 
                            alt="Foto <?= esc($data['fullname']) ?>" 
                            class="img-thumbnail" style="max-width: 150px;">
                        <?php else: ?>
                            <span class="text-muted">Tidak ada foto</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>NIM/NISN</th>
                    <td><?= esc($data['nisn_nim']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= esc($data['email']) ?></td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <?php if ($data['jenis_kelamin'] === 'L'): ?>
                        <td>Laki-Laki</td>
                    <?php elseif ($data['jenis_kelamin'] === 'P'): ?>
                        <td>Perempuan</td>
                    <?php else: ?>
                        <td>-</td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td colspan="2">
                        <?php
                            $alamat = $data['alamat'] ?? '';
                            $kota = trim(($data['tipe_kota_ktp'] ?? '') . ' ' . ($data['kota_ktp'] ?? ''));
                            $prov = $data['provinsi_ktp'] ?? '';
                            $parts = array_filter([$alamat, $kota, $prov]);
                            echo esc(implode(', ', $parts)) ?: 'Data belum diisi';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Domisili</th>
                    <td colspan="2">
                        <?php
                            $alamat = $data['domisili'] ?? '';
                            $kota = trim(($data['tipe_kota_domisili'] ?? '') . ' ' . ($data['kota_domisili'] ?? ''));
                            $prov = $data['provinsi_domisili'] ?? '';
                            $parts = array_filter([$alamat, $kota, $prov]);
                            echo esc(implode(', ', $parts)) ?: 'Data belum diisi';
                        ?>
                    </td>
                </tr>
            </table>

            <!-- Pendidikan -->
            <h6 class="text-primary mt-4">🎓 Pendidikan</h6>
            <table class="table table-sm table-bordered">
                <tr><th>Tingkat</th><td><?= esc($data['tingkat_pendidikan'] ?? '-') ?></td></tr>
                <?php if($data['tingkat_pendidikan'] === 'SMK'): ?>
                <tr><th>Sekolah</th><td><?= esc($data['nama_instansi'] ?? '-') ?></td></tr>
                <?php else: ?>
                <tr><th>Perguruan Tinggi</th><td><?= esc($data['nama_instansi'] ?? '-') ?></td></tr>
                <?php endif; ?>
                <tr><th>Jurusan</th><td><?= esc($data['nama_jurusan'] ?? '-') ?></td></tr>
                <?php if ($data['tingkat_pendidikan'] === 'SMK'): ?>
                    <tr><th>Kelas</th><td>Kelas <?= esc($data['semester']) ?></td></tr>
                <?php else: ?>
                    <tr><th>Semester</th><td>Semester <?= esc($data['semester']) ?></td></tr>
                    <tr><th>IPK</th><td><?= esc($data['nilai_ipk'] ?? '-') ?></td></tr>
                <?php endif; ?>
            </table>

            <!-- Dokumen -->
            <h6 class="text-primary mt-4">🗂️ Dokumen</h6>
            <table class="table table-sm table-bordered">
                <?php if($data['tingkat_pendidikan'] === 'SMK'):?>
                    <tr><th>Surat Permohonan</th><td><?= $data['surat_permohonan'] ? '<a href="' . base_url('uploads/surat-permohonan/' . $data['surat_permohonan']) . '" target="_blank">Lihat Surat</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>Tanggal Surat</th><td><?= !empty($data['tanggal_surat']) ? date('d-m-Y', strtotime($data['tanggal_surat'])) : '-' ?></td></tr>
                    <tr><th>Nomor Surat</th><td><?= !empty($data['no_surat']) ? esc($data['no_surat']) : '-' ?></td></tr>
                    <tr><th>Nama Pimpinan</th><td><?= !empty($data['nama_pimpinan']) ? esc($data['nama_pimpinan']) : '-' ?></td></tr>
                    <tr><th>Jabatan Pimpinan</th><td><?= !empty($data['jabatan']) ? esc($data['jabatan']) : '-' ?></td></tr>
                    <tr><th>Email Instansi</th><td><?= !empty($data['email_instansi']) ? esc($data['email_instansi']) : '-' ?></td></tr>
                    <tr><th>KTP/KK</th><td><?= $data['ktp_kk'] ? '<a href="'.base_url('uploads/ktp-kk/'.$data['ktp_kk']).'" target="_blank">Lihat KTP/KK</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>BPJS Kes</th><td><?= $data['bpjs_kes'] ? '<a href="'.base_url('uploads/bpjs-kes/'.$data['bpjs_kes']).'" target="_blank">Lihat BPJS Kes</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>BPJS TK</th><td><?= $data['bpjs_tk'] ? '<a href="'.base_url('uploads/bpjs-tk/'.$data['bpjs_tk']).'" target="_blank">Lihat BPJS TK</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>Bukti BPJS TK</th><td><?= $data['buktibpjs_tk'] ? '<a href="'.base_url('uploads/buktibpjs-tk/'.$data['buktibpjs_tk']).'" target="_blank">Lihat Bukti BPJS TK</a>' : 'Belum Ada' ?></td></tr>
                <?php else:?>
                    <tr><th>CV</th><td><?= $data['cv'] ? '<a href="'.base_url('uploads/cv/'.$data['cv']).'" target="_blank">Lihat CV</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>Proposal</th><td><?= $data['proposal'] ? '<a href="'.base_url('uploads/proposal/'.$data['proposal']).'" target="_blank">Lihat Proposal</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>Surat Permohonan</th><td><?= $data['surat_permohonan'] ? '<a href="' . base_url('uploads/surat-permohonan/' . $data['surat_permohonan']) . '" target="_blank">Lihat Surat</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>Tanggal Surat</th><td><?= !empty($data['tanggal_surat']) ? date('d-m-Y', strtotime($data['tanggal_surat'])) : '-' ?></td></tr>
                    <tr><th>Nomor Surat</th><td><?= !empty($data['no_surat']) ? esc($data['no_surat']) : '-' ?></td></tr>
                    <tr><th>Nama Pimpinan</th><td><?= !empty($data['nama_pimpinan']) ? esc($data['nama_pimpinan']) : '-' ?></td></tr>
                    <tr><th>Jabatan Pimpinan</th><td><?= !empty($data['jabatan']) ? esc($data['jabatan']) : '-' ?></td></tr>
                    <tr><th>Email Instansi</th><td><?= !empty($data['email_instansi']) ? esc($data['email_instansi']) : '-' ?></td></tr>
                    <tr><th>KTP/KK</th><td><?= $data['ktp_kk'] ? '<a href="'.base_url('uploads/ktp-kk/'.$data['ktp_kk']).'" target="_blank">Lihat KTP/KK</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>BPJS Kes</th><td><?= $data['bpjs_kes'] ? '<a href="'.base_url('uploads/bpjs-kes/'.$data['bpjs_kes']).'" target="_blank">Lihat BPJS Kes</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>BPJS TK</th><td><?= $data['bpjs_tk'] ? '<a href="'.base_url('uploads/bpjs-tk/'.$data['bpjs_tk']).'" target="_blank">Lihat BPJS TK</a>' : 'Belum Ada' ?></td></tr>
                    <tr><th>Bukti BPJS TK</th><td><?= $data['buktibpjs_tk'] ? '<a href="'.base_url('uploads/buktibpjs-tk/'.$data['buktibpjs_tk']).'" target="_blank">Lihat Bukti BPJS TK</a>' : 'Belum Ada' ?></td></tr>
                <?php endif;?>
                
            </table>

            <!-- Status Pendaftaran -->
            <h6 class="text-primary mt-4">📆 Status Pendaftaran</h6>
            <table class="table table-sm table-bordered">
                <tr><th>Tanggal Daftar</th><td><?= format_tanggal_indonesia_dengan_jam($data['tanggal_daftar']) ?></td></tr>
                <tr><th>Unit Kerja</th><td><?= esc($data['unit_kerja']) ?></td></tr>
                <tr><th>Durasi</th><td><?= esc($data['durasi']) ?> bulan</td></tr>
                <tr><th>Status Seleksi</th><td><?= esc($data['status_seleksi'] ?? 'Belum Diseleksi') ?></td></tr>
                <tr><th>Tanggal Seleksi</th><td><?= esc($data['tanggal_seleksi'] ?? '-') ?></td></tr>
                <tr><th>Status Konfirmasi</th><td><?= esc($data['status_konfirmasi'] === 'Y' ? 'Dikonfirmasi' : ($data['status_konfirmasi'] === 'N' ? 'Tidak Konfirmasi' : 'Belum Dikonfirmasi')) ?></td></tr>
                <tr><th>Tanggal Konfirmasi</th><td><?= esc($data['tanggal_konfirmasi'] ?? '-') ?></td></tr>
                <tr><th>Status Approval Konfirmasi</th>
                    <td><?= $data['status_validasi_berkas'] === 'Y' ? 'Approved' : ($data['status_validasi_berkas'] === 'N' ? 'Tidak Approved' : 'Belum Approved') ?></td>
                </tr>
                <tr><th>Tanggal Approval Konfirmasi</th><td><?= $data['tanggal_validasi_berkas'] ? format_tanggal_indonesia_dengan_jam($data['tanggal_validasi_berkas']) : '-' ?></td></tr>
                <tr><th>Status Berkas Lengkap</th>
                    <td><?= $data['status_berkas_lengkap'] === 'Y' ? 'Valid' : ($data['status_berkas_lengkap'] === 'N' ? 'Tidak Valid' : 'Belum Divalidasi') ?></td>
                </tr>
                <tr><th>Tanggal Berkas Lengkap</th><td><?= $data['tanggal_berkas_lengkap'] ? format_tanggal_indonesia_dengan_jam($data['tanggal_berkas_lengkap']) : '-' ?></td></tr>
                <tr><th>Catatan Validasi Berkas</th><td><?= esc($data['cttn_berkas_lengkap'] ?? '-') ?></td></tr>
                <tr><th>Tanggal Setujui Pernyataan</th><td><?= $data['tanggal_setujui_pernyataan'] ? format_tanggal_indonesia($data['tanggal_setujui_pernyataan']) : '-' ?></td></tr>
                <tr><th>Catatan Validasi Berkas</th><td><?= esc($data['cttn_berkas_lengkap'] ?? '-') ?></td></tr>

            </table>
        </div>
        <div class="modal-footer">
            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?= $data['magang_id'] ?>">Edit</button>
            <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
        </div>
    </div>
    </div>
    <?php endforeach; ?>

    <!-- Modal Edit -->
    <?php foreach ($pendaftaran as $item): ?>
    <div class="modal fade" id="editModal<?= $item['magang_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $item['magang_id'] ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('admin/updateMagang/'.$item['magang_id']) ?>" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Magang - <?= esc($item['fullname']) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="<?= $item['tanggal_masuk'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="<?= $item['tanggal_selesai'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Unit Kerja</label>
                        <select name="unit_id" class="form-control select2">
                            <?php foreach ($unitList as $unit): ?>
                                <option value="<?= $unit['unit_id'] ?>" <?= $unit['unit_id'] == $item['unit_id'] ? 'selected' : '' ?>>
                                    <?= esc($unit['unit_kerja']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
    </div>
    <?php endforeach; ?>

</div>
<?= $this->endSection(); ?>
