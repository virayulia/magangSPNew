<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reminder Kelengkapan Berkas Magang/Penelitian</title>
</head>
<body>
    <p>Yth. <?= esc($nama) ?>,</p>

    <p>Anda telah diterima untuk melaksanakan program magang/penelitian di PT Semen Padang, khususnya di 
    <strong><?= esc($unit) ?></strong>. Rencana tanggal mulai magang/penelitian Anda adalah <strong><?= esc(format_tanggal_indonesia($tanggal_masuk)) ?></strong>.</p>

    <p>Sehubungan dengan persiapan magang/penelitian, kami mencatat bahwa masih terdapat dokumen yang belum lengkap pada akun Anda, yaitu:</p>

    <ul>
        <?php if (!empty($dokumenKosong)): ?>
            <?php foreach ($dokumenKosong as $d): ?>
                <li><?= esc($d) ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>-</li>
        <?php endif; ?>
    </ul>

    <p>Mohon segera melengkapi dokumen di atas agar proses administrasi magang/penelitian Anda tidak terhambat. 
    Pengingat ini dikirimkan sejak 7 hari hingga 4 hari sebelum pelaksanaan magang/penelitian.</p>

    <br>
    <p>Hormat kami,</p>
    <p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
    <p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>
</body>
</html>
