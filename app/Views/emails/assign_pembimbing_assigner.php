<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembimbing Magang Ditetapkan</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#333;">

<p>Yth. <strong><?= esc($assigner['fullname']); ?></strong>,</p>

<p>
    Pembimbing magang telah <strong>berhasil ditetapkan</strong> melalui sistem.
</p>

<table cellpadding="6" cellspacing="0">
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
        <td><strong>Pembimbing</strong></td>
        <td>: <?= esc($pembimbing['fullname']); ?></td>
    </tr>
</table>

<p>
    Terima kasih telah melakukan penetapan pembimbing melalui sistem.
</p>

<br>
<p>Hormat kami,</p>
<p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
<p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>


</body>
</html>
