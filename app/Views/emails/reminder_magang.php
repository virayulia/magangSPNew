<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pengingat Magang</title>
</head>
<body>
    <p>Yth. Kepala <?= esc($unit); ?>,</p>

    <p>Ini adalah pengingat bahwa peserta magang akan masuk pada tanggal <strong><?= esc(format_tanggal_indonesia($tanggal_masuk)); ?></strong>.</p>

    <p>Berikut detail peserta:</p>
    <ul>
        <li>Nama Lengkap: <?= esc($nama); ?></li>
        <li>Perguruan Tinggi/Sekolah: <?= esc($instansi); ?></li>
    </ul>

    <p>Terima kasih atas perhatian dan kerjasamanya.</p>

    <p>Hormat kami,</p>
    <p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
    <p>Siska Ayu Soraya<br><strong>Kepala</strong><br>Zamriz</p>
</body>
</html>
