<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Status Seleksi Magang/Penelitian - Tidak Ada Konfirmasi</title>
</head>
<body>
    <p>Yth. <?= esc($nama) ?>,</p>

    <p>Terima kasih atas minat Anda untuk mengikuti program magang/penelitian di PT Semen Padang, khususnya di <strong><?= esc($unit) ?></strong>.</p>

    <p>Berdasarkan hasil seleksi, Anda dinyatakan <strong>diterima</strong> untuk mengikuti program magang/penelitian. Namun, hingga batas waktu yang ditentukan (3 hari sejak pengumuman seleksi), kami tidak menerima konfirmasi dari Anda.</p>

    <p>Dengan demikian, sesuai ketentuan yang berlaku, kesempatan magang/penelitian tersebut dinyatakan <strong>gugur</strong> secara otomatis karena tidak adanya konfirmasi dari pihak Anda.</p>

    <p>Kami menghargai ketertarikan dan usaha Anda dalam mengikuti proses ini. Semoga sukses dalam kesempatan selanjutnya.</p>

    <br>
    <p>Hormat kami,</p>
    <p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
    <p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>
</body>
</html>
