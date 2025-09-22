<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pembatalan Magang</title>
</head>
<body>
    <p>Yth. <?= esc($nama) ?>,</p>

    <p>Terima kasih atas minat Anda untuk mengikuti program magang di PT Semen Padang, khususnya di <strong><?= esc($unit) ?></strong>.</p>

    <p>Namun, dengan mempertimbangkan beberapa hal, kami sampaikan bahwa status pendaftaran magang Anda telah <strong>dibatalkan</strong>. Berikut catatan dari tim kami:</p>

    <blockquote style="background-color: #f9f9f9; padding: 10px; border-left: 4px solid #ccc;">
        <?= nl2br(esc($alasan)) ?>
    </blockquote>

    <p>Kami menghargai antusiasme Anda dan mengucapkan semoga sukses dalam proses pengembangan karier ke depannya.</p>

    <p>Hormat kami,</p>
    <p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
    <p>Siska Ayu Soraya<br><strong>Kepala</strong><br>Zamriz</p>
</body>
</html>
