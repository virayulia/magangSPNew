<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Penelitian</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <p>Yth. <?= esc($nama); ?>,</p>

    <p>Dengan hormat,</p>

    <p>
        Kami sampaikan ucapan terima kasih atas partisipasi Anda dalam program <strong>Penelitian di PT Semen Padang</strong>.
        Setelah melalui seluruh rangkaian kegiatan, dengan ini kami informasikan bahwa <strong>Surat Keterangan Penelitian</strong> Anda telah tersedia.
    </p>

    <p>
        Untuk mengunduh surat keterangan tersebut, silakan klik tombol di bawah ini:
    </p>

    <p style="text-align: center; margin: 20px 0;">
        <a href="<?= base_url('/surat-keterangan') ?>" 
           style="background-color: #0d6efd; color: #fff; padding: 12px 24px; text-decoration: none; 
                  border-radius: 5px; font-weight: bold; display: inline-block;">
            Unduh Surat Keterangan Penelitian
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
    <p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
    <p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>
</body>
</html>
