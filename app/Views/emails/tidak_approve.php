<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Validasi Magang</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <p>Yth. <?= esc($nama); ?>,</p>

    <p>Dengan hormat,</p>

    <p>
    Bersama email ini kami informasikan bahwa pendaftaran magang Anda <strong>belum memenuhi kelengkapan atau validitas yang disyaratkan</strong>.
    </p>

    <p><strong>Catatan dari tim validasi:</strong><br>
    <?= nl2br(esc($catatan)); ?></p>

    <p>
    Mohon untuk segera memperbaiki dan melengkapi data Anda agar dapat melanjutkan ke tahap selanjutnya dalam proses pendaftaran magang.
    </p>

    <p>
    Untuk memperbarui dokumen, silakan klik tombol di bawah ini:
    </p>

    <p style="text-align: center;">
        <a href="<?= base_url('/profile?tab=dokumen') ?>" style="background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
            Perbarui Dokumen
        </a>
    </p>

    <p>
    Apabila terdapat pertanyaan lebih lanjut, silakan menghubungi kami melalui kontak resmi yang tersedia.
    </p>

    <br>
    <p>Terima kasih atas perhatian dan kerja sama Anda.</p>

    <p>Hormat kami,</p>
    <p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
    <p>Siska Ayu Soraya<br><strong>Kepala</strong><br>Zamriz</p>

</body>
</html>
