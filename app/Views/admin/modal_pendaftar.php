<?php if (isset($error) && $error): ?>
    <p class="text-danger"><?= $error ?></p>
<?php elseif (empty($pendaftar)): ?>
    <p class="text-center text-muted">Belum ada pendaftar untuk unit ini pada periode ini.</p>
<?php else: ?>
    <div>
        <p><strong>Sisa Kuota:</strong> <span id="sisaKuota"><?= $kuota_tersedia ?? 0 ?></span></p>
    </div>

    <form id="formTerimaPendaftar">
        <div class="table-responsive">
           <table class="table table-bordered table-sm w-100" id="tablePendaftar">
                <thead>
                    <tr>
                        <th colspan="11" style="text-align: left;">
                            <label>
                                <input type="checkbox" id="selectAll"> Pilih Semua
                            </label>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Pilih</th>
                        <th>Nama</th>
                        <th>NISN/NIM</th>
                        <th>Jurusan</th>
                        <th>Perguruan Tinggi/Sekolah</th>
                        <th>Tgl Daftar</th>
                        <th>Durasi</th>
                        <th>CV</th>
                        <th>Proposal</th>
                        <th>Surat</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($pendaftar as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1; ?></td>
                            <td>
                                <input type="checkbox" name="pendaftar_ids[]" value="<?= $p->magang_id ?>" class="checkbox-pendaftar">
                            </td>
                            <td><?= esc($p->fullname); ?></td>
                            <td><?= esc($p->nisn_nim); ?></td>
                            <td><?= esc($p->jurusan); ?></td>
                            <td><?= esc($p->nama_instansi); ?></td>
                            <td><?= format_tanggal_indonesia_dengan_jam(esc($p->tanggal_daftar)); ?></td>
                            <td><?= esc($p->durasi); ?> bulan</td>
                            <td><?= $p->cv ? '<a href="'.base_url('uploads/cv/'.$p->cv).'" target="_blank">Lihat</a>' : '<span class="text-muted">-</span>'; ?></td>
                            <td><?= $p->proposal ? '<a href="'.base_url('uploads/proposal/'.$p->proposal).'" target="_blank">Lihat</a>' : '<span class="text-muted">-</span>'; ?></td>
                            <td><?= $p->surat_permohonan ? '<a href="'.base_url('uploads/surat-permohonan/'.$p->surat_permohonan).'" target="_blank">Lihat</a>' : '<span class="text-muted">-</span>'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" onclick="terimaBeberapa()">Terima yang Dipilih</button>
        <button type="button" class="btn btn-danger mt-2 ml-2" onclick="tolakBeberapa()">Tolak yang Dipilih</button>
    </form>
<?php endif; ?>
