<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reminder Upload Laporan dan Absensi Magang</title>
</head>
<body>
    <p>Yth. <?= esc($nama) ?>,</p>

    <p>
    Kami mengingatkan bahwa masa upload <strong>Laporan dan/atau Absensi Magang</strong>
    telah dibuka hingga <strong>15 hari setelah magang berakhir</strong>.
    </p>

    <p>
    📅 Tanggal selesai magang: <strong><?= $tanggalSelesai ?></strong><br>
    ⏳ Sisa waktu upload: <strong><?= $sisaHari ?> hari lagi</strong>
    </p>

    <?php if (!empty($dokumen)): ?>
    <p>Dokumen yang belum diunggah:</p>
    <ul>
        <?php foreach ($dokumen as $d): ?>
            <li><?= esc($d) ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <p>
    Silakan segera mengunggah dokumen melalui sistem sebelum batas waktu berakhir.
    Apabila melebihi batas waktu, akses upload akan ditutup otomatis.
    </p>

    <br>
    <p>Hormat kami,</p>
    <p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
    <p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>
</body>
</html>
