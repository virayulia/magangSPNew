<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Penolakan Magang</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <p>Yth. <?= esc($nama) ?>,</p>

    <p>Terima kasih atas minat Anda untuk mengikuti program magang di PT Semen Padang, khususnya di <strong><?= esc($unit) ?></strong>.</p>

    <p>Namun, kami sampaikan bahwa Anda belum memenuhi kriteria pada tahap seleksi kali ini. Kami menghargai waktu dan usaha Anda dalam mengikuti proses pendaftaran.</p>

    <?php if (!empty($alasan_batal)): ?>
        <p><strong>Catatan dari tim seleksi:</strong><br>
        <?= nl2br(esc($alasan_batal)) ?></p>
    <?php endif; ?>

    <p>Semoga sukses dalam kesempatan berikutnya. Terima kasih telah mendaftar!</p>

    <br>
    <p>Hormat kami,</p>
    <p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
    <p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>
</body>
</html>
