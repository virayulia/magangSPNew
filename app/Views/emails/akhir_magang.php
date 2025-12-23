<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wajib Isi Feedback Magang</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333; line-height:1.6;">
    <h2>Halo <?= esc($nama) ?>,</h2>

    <p>
        Kami ingin menginformasikan bahwa <strong>hari ini (<?= esc(format_tanggal_indonesia($tanggal_selesai)) ?>)</strong> 
        merupakan hari terakhir masa magang Anda di <strong><?= esc($unit) ?></strong>, PT Semen Padang.
    </p>

    <p>
        Terima kasih atas dedikasi dan kontribusi Anda selama menjalani program magang bersama kami. 
        <br><br>
        Sebagai bagian dari penyelesaian administrasi magang, Anda <strong>wajib</strong> mengisi form feedback berikut 
        agar kami dapat terus meningkatkan kualitas program magang ke depannya:
    </p>

    <p style="text-align: center; margin: 20px 0;">
        <a href="<?= base_url('/sertifikat-magang') ?>" 
           style="background-color: #0d6efd; color: #fff; padding: 12px 24px; text-decoration: none; 
                  border-radius: 5px; font-weight: bold; display: inline-block;">
            Isi Feedback Sekarang
        </a>
    </p>

    <p>
        Semoga pengalaman magang ini bermanfaat bagi perjalanan akademik dan karier Anda. 
        Kami doakan kesuksesan selalu menyertai Anda.
    </p>

    <br>
    <p>Hormat kami,</p>
    <p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
    <p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>
</body>
</html>
