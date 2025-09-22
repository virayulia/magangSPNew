<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pengumuman Penerimaan Program Magang – PT Semen Padang</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <p>Yth. <?= esc($nama) ?>,</p>

    <p>Dengan hormat,</p>

    <p>Melalui email ini, kami sampaikan bahwa Anda telah <strong>LOLOS SELEKSI</strong> sebagai peserta <strong>Program Magang</strong> di <strong>PT Semen Padang</strong>, dan akan ditempatkan di <strong><?= esc($unit) ?></strong>.</p>

    <p>Adapun periode magang Anda dijadwalkan berlangsung dari tanggal <strong><?= format_tanggal_indonesia(esc($tanggal_masuk)) ?></strong> hingga <strong><?= format_tanggal_indonesia(esc($tanggal_selesai)) ?></strong>.</p>

    <p>Untuk menyelesaikan proses administrasi, kami mohon Anda melakukan <strong>konfirmasi penerimaan</strong> melalui tautan berikut:</p>

    <p style="text-align: center; margin: 20px 0;">
        <a href="<?= base_url('/status-lamaran') ?>" 
           style="background-color: #28a745; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
           Konfirmasi Sekarang
        </a>
    </p>

    <p>Konfirmasi tersebut wajib dilakukan <strong>maksimal 3 (tiga) hari kalender sejak email ini diterima</strong>. Apabila tidak ada tanggapan hingga batas waktu tersebut, maka kesempatan ini akan kami anggap <strong>gugur</strong>.</p>

    <p>Terima kasih atas partisipasi Anda dalam proses seleksi. Kami mengucapkan selamat bergabung dan semoga program ini memberikan manfaat serta pengalaman berharga bagi Anda.</p>

    <br>
    <p>Hormat kami,</p>
    <p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
    <p>Siska Ayu Soraya<br><strong>Kepala</strong><br>Zamriz</p>
</body>
</html>
