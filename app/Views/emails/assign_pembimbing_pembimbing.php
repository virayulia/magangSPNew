<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penetapan Pembimbing Magang</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#333;">

<p>Yth. <strong><?= esc($pembimbing['fullname']); ?></strong>,</p>

<p>
    Dengan ini kami informasikan bahwa Bapak/Ibu telah
    <strong>ditetapkan sebagai Pembimbing Magang</strong>.
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
</table>

<p>
    Silakan mengakses sistem untuk melakukan pendampingan dan penilaian magang.
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
        Akses Sistem Pembimbing
    </a>
</p>

<br>
<p>Hormat kami,</p>
<p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
<p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>

</body>
</html>
