<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pengingat Magang</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <p>Yth. Kepala <?= esc($unit); ?>,</p>

    <p>
        Ini adalah pengingat bahwa peserta magang akan masuk pada tanggal 
        <strong><?= esc(format_tanggal_indonesia($list[0]['tanggal'])); ?></strong>.
    </p>

    <p>Berikut daftar peserta yang akan masuk:</p>
    <table border="1" cellspacing="0" cellpadding="6" style="border-collapse: collapse; width: 100%;">
        <thead style="background: #f2f2f2;">
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Perguruan Tinggi/Sekolah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $i => $mhs): ?>
            <tr>
                <td style="text-align:center;"><?= $i+1; ?></td>
                <td><?= esc($mhs['nama']); ?></td>
                <td><?= esc($mhs['instansi']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <p>
        Untuk kelancaran pelaksanaan magang, kami mohon Bapak/Ibu segera menambahkan 
        <strong>Pembimbing Magang</strong> pada sistem. Pembimbing ini akan menjadi 
        penghubung utama dalam kegiatan magang mahasiswa.
    </p>

    <p style="text-align: center;">
        <a href="<?= base_url('pembimbing/penilaian') ?>" 
           style="background-color: #0d6efd; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Tambah Pembimbing
        </a>
    </p>

    <br>
    <p>Terima kasih atas perhatian dan kerjasamanya.</p>

    <p>Hormat kami,</p>
    <p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
    <p>Siska Ayu Soraya<br><strong>Kepala</strong></p>
</body>
</html>
