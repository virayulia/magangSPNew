<?php if (empty($pendaftar)): ?>
    <p class="text-center text-muted">Belum ada pendaftar untuk unit ini pada periode ini.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NISN/NIM</th>
                    <th>Jurusan</th>
                    <th>Instansi</th>
                    <th>Durasi</th>
                    <th>CV</th>
                    <th>Proposal</th>
                    <th>Surat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendaftar as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1; ?></td>
                        <td><?= esc($p->fullname); ?></td>
                        <td><?= esc($p->nisn_nim); ?></td>
                        <td><?= esc($p->jurusan); ?></td>
                        <td><?= esc($p->nama_instansi); ?></td>
                        <td><?= esc($p->durasi); ?> bulan</td>
                        <td>
                            <?php if ($p->cv): ?>
                                <a href="<?= base_url('uploads/cv/' . $p->cv); ?>" target="_blank">Lihat</a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p->proposal): ?>
                                <a href="<?= base_url('uploads/proposal/' . $p->proposal); ?>" target="_blank">Lihat</a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p->surat_permohonan): ?>
                                <a href="<?= base_url('uploads/surat/' . $p->surat_permohonan); ?>" target="_blank">Lihat</a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="konfirmasiTerima(<?= $p->magang_id ?>)">Terima</button>
                            <button class="btn btn-danger btn-sm" onclick="konfirmasiTolak(<?= $p->magang_id ?>)">Tolak</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
function konfirmasiTerima(id) {
    if (confirm('Yakin ingin menerima pendaftar ini?')) {
        fetch(`/manage-seleksi/terima/${id}`, { method: 'POST' })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                $('#modalPendaftar').modal('hide');
                location.reload();
            });
    }
}

function konfirmasiTolak(id) {
    if (confirm('Yakin ingin menolak pendaftar ini?')) {
        fetch(`/manage-seleksi/tolak/${id}`, { method: 'POST' })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                $('#modalPendaftar').modal('hide');
                location.reload();
            });
    }
}
</script>
