<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Permintaan Approval Unit Penelitian</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <p>Yth. Bapak Zamris,</p>

    <p>
        Bersama ini kami sampaikan bahwa terdapat pengajuan kegiatan 
        <strong>Penelitian</strong> yang telah diverifikasi oleh admin dan 
        memerlukan <strong>persetujuan (approval)</strong> dari Bapak 
        selaku penanggung jawab kegiatan Magang dan Penelitian di PT Semen Padang.
    </p>

    <p>Berikut rincian peserta yang mengajukan penelitian:</p>
    <table border="1" cellspacing="0" cellpadding="6" style="border-collapse: collapse; width: 100%;">
        <thead style="background: #f2f2f2;">
            <tr>
                <th>Nama Lengkap</th>
                <th>Jurusan</th>
                <th>Perguruan Tinggi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= esc($nama); ?></td>
                <td><?= esc($jurusan); ?></td>
                <td><?= esc($instansi); ?></td>
            </tr>
        </tbody>
    </table>

    <p>
        Mohon kiranya Bapak dapat melakukan <strong>approval</strong> terhadap pengajuan ini 
        melalui sistem agar proses penelitian dapat dilanjutkan.
    </p>

    <p style="text-align: center;">
        <a href="<?= base_url('pembimbing/approve-unit-penelitian') ?>" 
           style="background-color: #0d6efd; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Lihat dan Beri Approval
        </a>
    </p>

    <br>
    <p>Atas perhatian dan kerjasama Bapak, kami ucapkan terima kasih.</p>

    <p>Hormat kami,</p>
    <p><strong><?= esc($signature['unit_kerja']) ?><br>PT Semen Padang</strong></p>
    <p><?= esc($signature['fullname']) ?><br><strong>Kepala</strong></p>
</body>
</html>
