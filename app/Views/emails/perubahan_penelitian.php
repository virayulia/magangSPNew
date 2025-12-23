<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Perubahan Jadwal Penelitian</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
<p>Kepada Yth,</p>

<p>Dengan hormat,</p>

<p>
Kami informasikan bahwa terdapat <strong>perubahan data pelaksanaan penelitian</strong> untuk mahasiswa/siswa berikut:
</p>

<p>
<strong>Nama</strong>: <?= htmlspecialchars($nama) ?><br>
<strong>Perguruan Tinggi</strong>: <?= htmlspecialchars($instansi) ?><br>
<strong>Unit Kerja</strong>: <?= htmlspecialchars($unit) ?? 'Unit Belum Ditetapkan' ?><br>
<strong>Tanggal Penelitian</strong>: <?= format_tanggal_indonesia($tanggal_masuk) ?> 
s.d. <?= format_tanggal_indonesia($tanggal_selesai) ?>
</p>

<p>
Harap menyesuaikan dengan jadwal/unit terbaru ini. Informasi lengkap dapat dilihat melalui sistem pendaftaran penelitian.
</p>

<p>Terima kasih atas perhatian dan kerja samanya.</p>

<br>
    <p>Hormat kami,</p>
    <p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
    <p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>
</body>
</html>
