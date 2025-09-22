<?php
$tanggal_masuk = date('d M Y', strtotime($pendaftaran['tanggal_masuk']));
$tanggal_selesai = date('d M Y', strtotime($pendaftaran['tanggal_selesai']));
$tanggal_surat = ($user_data->tanggal_surat)
    ? \CodeIgniter\I18n\Time::parse($user_data->tanggal_surat)->toLocalizedString('d MMMM yyyy')
    : '-';
?>

<style>
    body {
        font-family: "Times", serif;
        font-size: 12pt;
    }
    .footer {
        font-size: 12pt;
        text-align: center;
        margin-top: 30px;
    }
    .content {
        margin-top: 5px;
    }
</style>

<!-- Nomor Surat dan Tanggal -->
<table width="100%">
    <tr>
        <td width="60%" style="text-align: left;">
            Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($user_data->no_surat ?? '-') ?><br>
            Hal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>Kerja Praktek</strong><br>
            Lamp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: -
        </td>
        <td width="40%" style="text-align: right;">
            Padang, <?= format_tanggal_indonesia(date('d M Y')) ?> 
        </td>
    </tr>
</table>

<br>

<!-- Alamat Tujuan -->
<p>
    Kepada Yth:<br>
    <strong>
    <?= $user_data->jabatan ?? '-' ?> <?= htmlspecialchars($user_data->nama_jurusan ?? '-') ?><br>
    <?= $user_data->nama_instansi ?? '-' ?><br>
    di Tempat </strong>
</p>

<br><br>

<!-- Dengan Hormat -->
<p>Dengan hormat,</p>

<br>

<!-- Isi Surat -->
<p style="text-align: justify;">
Sehubungan dengan surat permohonan Bapak/Ibu No:<?= htmlspecialchars($user_data->no_surat ?? '-') ?> Tanggal <?=format_tanggal_indonesia($tanggal_surat) ?> diberitahukan, bahwa kami dapat menerima mahasiswa/siswa tersebut di bawah ini untuk melakukan Kerja Praktek di PT Semen Padang :
</p>

<br>

<!-- Data Mahasiswa -->
<p>
Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($user_data->fullname ?? '-') ?><br>
NIM&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($user_data->nisn_nim ?? '-') ?><br>
Jurusan/Universitas: <?= htmlspecialchars($user_data->nama_jurusan ?? '-') ?> / <?= htmlspecialchars($user_data->nama_instansi ?? '-') ?>
</p>

<br>

<!-- Waktu Pelaksanaan -->
<p style="text-align: justify;">
Kerja praktek akan dilaksanakan pada tanggal <strong><?= format_tanggal_indonesia($tanggal_masuk) ?></strong> sampai dengan <strong><?= format_tanggal_indonesia($tanggal_selesai) ?></strong>.
</p>

<br>

<!-- Persyaratan -->
<p style="text-align: justify;">
Persyaratan yang harus dipenuhi:</p>

<ol>
    <li>Peserta magang diwajibkan hadir pada tanggal <strong><?= format_tanggal_indonesia($tanggal_masuk) ?> jam 08.00 WIB</strong> di <strong>Unit Operasional SDM (Pusdiklat)</strong> PT Semen Padang untuk mengikuti pengarahan sebelum melaksanakan Kerja Praktek.</li>
    <li>Mematuhi segala ketentuan dan disiplin yang berlaku di PT Semen Padang serta selalu mematuhi protokol kesehatan selama kerja praktek berlangsung, peserta magang dinyatakan gagal dalam melaksanakan kerja praktek jika melanggar peraturan di PT Semen Padang.</li>
    <li>Membuat laporan kerja praktek dan menyerahkan ke Unit Operasional SDM (Pusdiklat) 15 (lima belas) hari paling lambat setelah tanggal kerja praktek berakhir.</li>
    <li><strong>Perlengkapan Safety yaitu Rompi, Helm (warna biru) dan Sepatu Safety disediakan sendiri.</strong></li>
    <li><strong>Bukti asli keikutsertaan asuransi kecelakaan kerja dibawa pada hari pertama ke Unit Operasional SDM (Pusdiklat).</strong></li>
</ol>

<br>

<!-- Penutup -->
<p style="text-align: justify;">
Demikian surat ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.
</p>

<br><br><br>

<!-- Tanda Tangan -->
<table width="100%">
    <tr>
        <td width="40%">
            Hormat kami,<br>
            <strong>Training & Knowledge Management</strong><br>
            Ika Nopikasari<br><br>
            Kepala <br>
            Zamriz
        </td>
    </tr>
</table>

