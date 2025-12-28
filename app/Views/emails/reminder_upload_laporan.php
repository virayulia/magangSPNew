<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reminder Upload Dokumen <?= ucfirst($kegiatan) ?></title>
</head>
<body>

<p>Yth. <?= esc($nama) ?>,</p>

<p>
Kami mengingatkan bahwa masa unggah dokumen
<strong><?= ucfirst($kegiatan) ?></strong>
telah dibuka hingga
<strong>15 hari setelah <?= $kegiatan === 'magang' ? 'magang' : 'penelitian' ?> berakhir</strong>.
</p>

<p>
📅 Tanggal selesai <?= esc($kegiatan) ?>:
<strong><?= esc(format_tanggal_indonesia($tanggalSelesai)) ?></strong><br>
⏳ Sisa waktu unggah:
<strong><?= esc($sisaHari) ?> hari lagi</strong>
</p>

<?php if (!empty($dokumen)): ?>
    <p>Dokumen yang belum diunggah:</p>
    <ul>
        <?php foreach ($dokumen as $d): ?>
            <li><?= esc($d) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>
    Seluruh dokumen utama telah diunggah.
    Silakan pastikan kembali kelengkapannya di sistem.
    </p>
<?php endif; ?>

<p>
Silakan segera mengunggah dokumen melalui sistem sebelum batas waktu berakhir.
Apabila melewati batas waktu tersebut, akses unggah akan ditutup secara otomatis.
</p>

<br>

<p>Hormat kami,</p>

<p>
<strong><?= esc($signature['unit_kerja']) ?><br>
PT Semen Padang</strong><br>
<?= esc($signature['fullname']) ?><br>
<strong>Kepala</strong>
</p>

</body>
</html>
