<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reminder Penetapan Pembimbing Magang</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; line-height: 1.6;">

    <p>Yth. <strong>Kepala <?= esc($unit); ?></strong>,</p>

    <p>
        Bersama email ini kami sampaikan <strong>pengingat penetapan Pembimbing Magang</strong>
        untuk peserta magang yang telah diterima dan sedang melaksanakan kegiatan magang.
    </p>

    <p>Adapun daftar peserta magang pada unit kerja Bapak/Ibu sebagai berikut:</p>

    <table width="100%" border="1" cellspacing="0" cellpadding="8"
           style="border-collapse: collapse; margin-top: 10px; margin-bottom: 15px;">
        <thead style="background-color: #f2f2f2;">
            <tr>
                <th style="text-align:center; width:40px;">No</th>
                <th>Nama Lengkap</th>
                <th>Jurusan</th>
                <th>Asal Perguruan Tinggi / Sekolah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $i => $mhs): ?>
                <tr>
                    <td style="text-align:center;"><?= $i + 1; ?></td>
                    <td><?= esc($mhs['nama']); ?></td>
                    <td><?= esc($mhs['jurusan']); ?></td>
                    <td><?= esc($mhs['instansi']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p>
        Untuk mendukung kelancaran pelaksanaan magang, kami mohon kiranya
        <strong>Pembimbing Magang</strong> dapat segera ditetapkan melalui sistem.
        Pembimbing tersebut akan berperan sebagai pendamping serta penghubung utama
        selama proses magang berlangsung.
    </p>

    <p style="text-align: center; margin: 25px 0;">
        <a href="<?= base_url('pembimbing/penilaian'); ?>"
           style="
                background-color: #0d6efd;
                color: #ffffff;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
                font-weight: bold;
           ">
            Tetapkan Pembimbing Magang
        </a>
    </p>

    <p>
        Demikian kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu,
        kami ucapkan terima kasih.
    </p>

    <br>

    <p>Hormat kami,</p>

    <p style="margin-bottom: 5px;">
        <strong><?= esc($signature['unit_kerja']); ?><br>
        PT Semen Padang</strong>
    </p>

    <p style="margin-top: 0;">
        <?= esc($signature['fullname']); ?><br>
        <strong>Kepala</strong>
    </p>

</body>
</html>
