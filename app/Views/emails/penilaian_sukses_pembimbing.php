<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penilaian Magang Berhasil</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size:14px; color:#333; line-height:1.6;">

<p>Yth. <strong><?= esc($pembimbing['fullname']); ?></strong>,</p>

<p>
    Penilaian magang telah <strong>berhasil Anda simpan</strong> melalui sistem.
</p>

<table cellpadding="6" cellspacing="0" style="margin-top:10px;">
    <tr>
        <td><strong>Nama Peserta</strong></td>
        <td>: <?= esc($magang['nama_mhs']); ?></td>
    </tr>
    <tr>
        <td><strong>Jurusan</strong></td>
        <td>: <?= esc($magang['nama_jurusan']); ?></td>
    </tr>
    <tr>
        <td><strong>Sekolah/Perguruan Tinggi</strong></td>
        <td>: <?= esc($magang['nama_instansi']); ?></td>
    </tr>
    <tr>
        <td><strong>Tanggal Penilaian</strong></td>
        <td>: <?= date('d M Y'); ?></td>
    </tr>
</table>

<p style="margin-top:15px;">
    Penilaian ini akan diteruskan ke <strong>Kepala Unit</strong> untuk proses
    persetujuan (approval).
</p>

<p style="text-align:center; margin:25px 0;">
    <a href="<?= base_url('pembimbing/penilaian'); ?>"
       style="
            background-color:#0d6efd;
            color:#ffffff;
            padding:12px 24px;
            text-decoration:none;
            border-radius:5px;
            font-weight:bold;
            display:inline-block;
       ">
        Lihat Data Penilaian
    </a>
</p>

<br>

<p>
    Hormat kami,<br>
    <strong><?= esc($signature['unit_kerja']); ?><br>
    PT Semen Padang</strong><br>
    <?= esc($signature['fullname']); ?>
</p>

</body>
</html>
