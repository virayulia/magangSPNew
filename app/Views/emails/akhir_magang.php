<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Akhir Masa Magang</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333; line-height:1.6;">
    <h2>Halo <?= esc($nama) ?>,</h2>

    <p>
        Kami ingin menginformasikan bahwa <strong>hari ini (<?= esc(format_tanggal_indonesia($tanggal_selesai)) ?>)</strong> 
        merupakan hari terakhir masa magang Anda di <strong><?= esc($unit) ?></strong>, PT Semen Padang.
    </p>

    <p>
        Terima kasih atas dedikasi dan kontribusi Anda selama menjalani program magang bersama kami. 
        Untuk membantu kami meningkatkan kualitas program ke depannya, kami mohon kesediaan Anda untuk mengisi form feedback berikut:
    </p>

    <p style="text-align: center; margin: 20px 0;">
        <a href="<?= base_url('/sertifikat-magang') ?>" 
           style="background-color: #0d6efd; color: #fff; padding: 12px 24px; text-decoration: none; 
                  border-radius: 5px; font-weight: bold; display: inline-block;">
            Isi Feedback Magang
        </a>
    </p>

    <p>
        Semoga pengalaman magang ini bermanfaat bagi perjalanan akademik dan karier Anda. 
        Kami doakan kesuksesan selalu menyertai Anda.
    </p>

    <br>
    <p>Hormat kami,</p>
    <p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
    <p>Siska Ayu Soraya<br><strong>Kepala</strong></p>
</body>
</html>
