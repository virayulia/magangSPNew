<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penilaian Magang Menunggu Approval</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size:14px; color:#333; line-height:1.6;">

<p>Yth. <strong><?= esc($kaUnit['fullname']); ?></strong>,</p>

<p>
    Dengan ini kami informasikan bahwa <strong>Pembimbing Magang</strong>
    telah melakukan penilaian terhadap peserta magang berikut:
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
        <td><strong>Status</strong></td>
        <td>: Menunggu Approval</td>
    </tr>
</table>

<p style="margin-top:15px;">
    Mohon kiranya Bapak/Ibu dapat melakukan <strong>approval penilaian</strong>
    melalui sistem.
</p>

<p style="text-align:center; margin:25px 0;">
    <a href="<?= base_url('pembimbing/approve'); ?>"
       style="
            background-color:#28a745;
            color:#ffffff;
            padding:12px 24px;
            text-decoration:none;
            border-radius:5px;
            font-weight:bold;
            display:inline-block;
       ">
        Approve Penilaian Magang
    </a>
</p>

<p>
    Atau salin tautan berikut ke browser Anda:<br>
    <a href="<?= base_url('kaunit/approve-penilaian'); ?>">
        <?= base_url('kaunit/approve-penilaian'); ?>
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
