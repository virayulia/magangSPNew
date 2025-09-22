<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Validasi Magang</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">

<p>Dengan hormat,</p>

<p>
Sehubungan dengan surat permohonan Bapak/Ibu No: <?= htmlspecialchars($user_data->no_surat ?? '-') ?> 
Tanggal <?= format_tanggal_indonesia($tanggal_surat) ?>, diberitahukan bahwa kami dapat menerima mahasiswa/siswa berikut untuk melakukan Kerja Praktek di PT Semen Padang:
</p>

<p>
<strong>Nama</strong>: <?= htmlspecialchars($user_data->fullname ?? '-') ?><br>
<strong>NIM</strong>: <?= htmlspecialchars($user_data->nisn_nim ?? '-') ?><br>
<strong>Jurusan/Universitas</strong>: <?= htmlspecialchars($user_data->nama_jurusan ?? '-') ?> / <?= htmlspecialchars($user_data->nama_instansi ?? '-') ?>
</p>

<p>
Kerja praktek akan dilaksanakan pada tanggal <strong><?= format_tanggal_indonesia($tanggal_masuk) ?></strong> 
sampai dengan <strong><?= format_tanggal_indonesia($tanggal_selesai) ?></strong>.
</p>

<p><strong>Persyaratan yang harus dipenuhi:</strong></p>
<ol>
    <li>Hadir pada tanggal <strong><?= format_tanggal_indonesia($tanggal_masuk) ?> jam 08.00 WIB</strong> 
        di <strong>Unit Operasional SDM (Pusdiklat)</strong> PT Semen Padang.</li>
    <li>Mematuhi semua ketentuan dan disiplin di PT Semen Padang, serta protokol kesehatan yang berlaku.</li>
    <li>Menyerahkan laporan kerja praktek maksimal 15 hari setelah magang selesai ke Pusdiklat.</li>
    <li><strong>Rompi, Helm biru, dan Sepatu Safety disediakan sendiri oleh peserta.</strong></li>
    <li><strong>Mengupload kartu asurasni kecelakaan kerja dan buktinya paling lambat 3 hari sebelum pelaksanaan magang.</strong></li>
    <li><strong>Bukti asli asuransi kecelakaan kerja dibawa pada hari pertama.</strong></li>
</ol>

<p>
Untuk detail pelaksanaan magang, silakan kunjungi halaman berikut:
</p>

<p style="text-align: center;">
    <a href="<?= base_url('/status-lamaran') ?>" style="background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
        Lihat Pendaftaran Magang
    </a>
</p>

<p>
Demikian kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.
</p>

<br>
<p>Hormat kami,</p>
<p><strong>Training & Knowledge Management<br>PT Semen Padang</strong></p>
<p>Siska Ayu Soraya<br><strong>Kepala</strong><br>Zamriz</p>

</body>
</html>
