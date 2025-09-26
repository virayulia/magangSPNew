<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <p>Yth. <?= esc($nama); ?>,</p>

    <p>Dengan hormat,</p>

    <p>
        Kami sampaikan ucapan terima kasih atas partisipasi Anda dalam program <strong>Magang di PT Semen Padang</strong>.
        Setelah melalui seluruh rangkaian kegiatan, dengan ini kami informasikan bahwa <strong>Sertifikat Magang</strong> Anda telah tersedia.
    </p>

    <p>
        Untuk mengunduh sertifikat, silakan klik tombol di bawah ini:
    </p>

    <p style="text-align: center; margin: 20px 0;">
        <a href="<?= base_url('/sertifikat-magang') ?>" 
           style="background-color: #0d6efd; color: #fff; padding: 12px 24px; text-decoration: none; 
                  border-radius: 5px; font-weight: bold; display: inline-block;">
            Unduh Sertifikat Magang
        </a>
    </p>

    <p>
        Apabila terdapat pertanyaan lebih lanjut, silakan menghubungi kami melalui kontak resmi yang tersedia
        pada laman <a href="<?= base_url() ?>" style="color:#0d6efd;">website PT Semen Padang</a>.
    </p>

    <br>
    <p>Atas perhatian dan kerja sama Anda, kami ucapkan terima kasih.</p>

    <br>
    <p>Hormat kami,</p>
    <p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
    <p>Siska Ayu Soraya<br><strong>Kepala</strong></p>
</body>
</html>
