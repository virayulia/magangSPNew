<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Perubahan Jadwal Magang</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
<p>Kepada Yth,</p>

<p>Dengan hormat,</p>

<p>
Kami informasikan bahwa terdapat <strong>perubahan data pelaksanaan magang</strong> untuk mahasiswa/siswa berikut:
</p>

<p>
<strong>Nama</strong>: <?= htmlspecialchars($nama) ?><br>
<strong>Instansi</strong>: <?= htmlspecialchars($instansi) ?><br>
<strong>Unit Kerja</strong>: <?= htmlspecialchars($unit) ?><br>
<strong>Tanggal Magang</strong>: <?= format_tanggal_indonesia($tanggal_masuk) ?> 
s.d. <?= format_tanggal_indonesia($tanggal_selesai) ?>
</p>

<p>
Harap menyesuaikan dengan jadwal/unit terbaru ini. Informasi lengkap dapat dilihat melalui sistem pendaftaran magang.
</p>

<p>Terima kasih atas perhatian dan kerja samanya.</p>

<br>
<p>Hormat kami,</p>
<p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
</body>
</html>
