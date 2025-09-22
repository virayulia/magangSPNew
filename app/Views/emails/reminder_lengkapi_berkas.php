<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reminder Kelengkapan Berkas Magang</title>
</head>
<body>
    <p>Yth. <?= esc($nama) ?>,</p>

    <p>Anda telah diterima untuk melaksanakan program magang di PT Semen Padang, khususnya di 
    <strong><?= esc($unit) ?></strong>. Rencana tanggal mulai magang Anda adalah <strong><?= esc($tanggal_masuk) ?></strong>.</p>

    <p>Sehubungan dengan persiapan magang, kami mencatat bahwa masih terdapat dokumen yang belum lengkap pada akun Anda, yaitu:</p>

    <ul>
        <?php if (!empty($dokumenKosong)): ?>
            <?php foreach ($dokumenKosong as $d): ?>
                <li><?= esc($d) ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>-</li>
        <?php endif; ?>
    </ul>

    <p>Mohon segera melengkapi dokumen di atas agar proses administrasi magang Anda tidak terhambat. 
    Pengingat ini dikirimkan sejak 7 hari hingga 4 hari sebelum pelaksanaan magang.</p>

    <br>
    <p>Hormat kami,</p>
    <p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
    <p>Siska Ayu Soraya<br><strong>Kepala</strong><br>Zamriz</p>
</body>
</html>
